<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use App\Models\Punch;
use App\Models\Punchitem;
use App\Models\Role;
use App\Models\NotificationLog;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ServiceRequestReschedule;


use Illuminate\Support\Facades\Auth;
class WorkerPipelineController extends Controller
{
    /** Statuses a worker can act on from the pipeline. */
    private const OPEN_STATUSES = [
    'Assigned', 'Accepted', 'In Progress', 'Reschedule',
    'Rework', 'On Hold', 'Qc Review', 'Completed', 'Pending Invoice', 'Invoice Submitted',
];

    /*
    |--------------------------------------------------------------------------
    | Read
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $user = $this->worker($request);
        $user->loadMissing('role');

            $requests = ServiceRequest::with([
                'client', 'project', 'category', 'domain', 'punches.user.role',
                'createdBy.role', 'qcReviewedBy.role', 'assignedUser',
            ])
            ->where('assigned_user_id', $user->id)
            ->whereIn('status', self::OPEN_STATUSES)
            ->orderByDesc('dispatched_at')
            ->orderByDesc('id')
            ->get();

        return view('worker.pipeline', [
            'jobs'              => $requests->map(fn (ServiceRequest $sr) => $this->transform($sr))->values(),
            'activeJob'         => $this->activeJob($user),
            'routes'            => $this->routes(),
            'expenseCategories' => $this->expenseCategories(),
            'userName'          => $user->name,
            'userRole'          => optional($user->role)->name ?? 'Maintenance Lead',
            'userCode'          => optional($user->role)->code,
            'userInitials'      => $this->initials($user->name),
            'letterhead'        => $this->letterhead(), 
        ]);
    }



private function letterhead(): array
{
    $dir = storage_path('app/public/letterhead');

    return [
        // swapped: the tall contact image goes on top, the thin strip on the bottom
        'header'    => $this->imageToBase64($dir . DIRECTORY_SEPARATOR . 'footer.jpg'),
        'footer'    => $this->imageToBase64($dir . DIRECTORY_SEPARATOR . 'header.jpg'),
        'watermark' => $this->imageToBase64($dir . DIRECTORY_SEPARATOR . 'watermark.jpg'),
    ];
}

private function imageToBase64(string $path): ?string
{
    if (!is_file($path)) {
        return null;
    }

    return 'data:image/png;base64,' . base64_encode(file_get_contents($path));
}

    public function history(Request $request)
    {
        $user = $this->worker($request);

        $punches = Punch::with([
                'serviceRequest.client',
                'serviceRequest.project',
                'serviceRequest.domain',
                'items',
            ])
            ->where('user_id', $user->id)
            ->whereIn('status', ['submitted', 'approved', 'rejected'])
            ->whereNotNull('punch_out_at')
            ->orderByDesc('punch_out_at')
            ->limit(50)
            ->get();

        return response()->json([
            'ok'    => true,
            'stats' => [
                'jobs'  => $punches->count(),
                'hours' => round(
                    $punches->sum(fn (Punch $p) => $p->punch_in_at->floatDiffInHours($p->punch_out_at)),
                    1
                ),
                // Scoped to the 50 punches above, not lifetime.
                'expenses' => number_format((float) $punches->sum('materials_subtotal'), 2, '.', ''),
            ],
            'items' => $punches->map(fn (Punch $p) => [
                'ref'       => optional($p->serviceRequest)->ref ?? '—',
                'client'    => optional(optional($p->serviceRequest)->client)->company_name ?? '—',
                'domain'    => optional(optional($p->serviceRequest)->domain)->domain_name ?? '—',
                'site'      => optional(optional($p->serviceRequest)->project)->site_address ?? '—',
                'date'      => $p->punch_out_at->format('d M Y'),
                'in'        => $p->punch_in_at->format('H:i'),
                'out'       => $p->punch_out_at->format('H:i'),
                'duration'  => $p->duration_label ?? '—',
                'materials' => number_format((float) $p->materials_subtotal, 2, '.', ''),
                'total'     => number_format((float) $p->grand_total, 2, '.', ''),
                'itemCount' => $p->items->count(),
                'status'    => $p->status,
            ])->values(),
        ]);
    }


    private function buildSrRef(ServiceRequest $sr): string
    {
        return 'SR-' . ($sr->created_at?->year ?? now()->year)
            . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT);
    }

    public function profile(Request $request)
    {
        $user = $this->worker($request);
        $user->loadMissing('role');

        $open = ServiceRequest::where('assigned_user_id', $user->id)
            ->whereIn('status', self::OPEN_STATUSES)
            ->count();

        $finished = Punch::where('user_id', $user->id)
            ->whereNotNull('punch_out_at')
            ->get();

        return response()->json([
            'ok'   => true,
            'user' => [
                'name'     => $user->name,
                'initials' => $this->initials($user->name),
                'role'     => optional($user->role)->name ?? 'Maintenance Lead',
                'code'     => optional($user->role)->code,
                'email'    => $user->email,
                'phone'    => $user->phone ?? '—',
                'joined'   => optional($user->created_at)->format('M Y') ?? '—',
            ],
            'stats' => [
                'open'      => $open,
                'completed' => $finished->count(),
                'hours'     => round(
                    $finished->sum(fn (Punch $p) => $p->punch_in_at->floatDiffInHours($p->punch_out_at)),
                    1
                ),
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Write
    |--------------------------------------------------------------------------
    */

  public function accept(Request $request)
{
    $data = $request->validate([
        'sr_id'    => ['required', 'integer'],
        'eta_date' => ['required', 'date_format:Y-m-d'],
        'eta_time' => ['required', 'date_format:H:i'],
    ]);

    $sr     = $this->ownedRequest($request, $data['sr_id']);
    $worker = $this->worker($request);

    // Rework re-enters with accepted_at still set from the first pass,
    // so the "already accepted" gate keys on status, not the timestamp.
    $isRework = strtolower((string) $sr->status) === 'rework';
    abort_if($sr->accepted_at && !$isRework, 409, 'This job has already been accepted.');

    $oldStatus = $sr->status;          // capture BEFORE update


    $sr->update([
        'eta_at'      => $data['eta_date'] . ' ' . $data['eta_time'] . ':00',
        'accepted_at' => now(),
        'status'      => 'Accepted',
        'hold_reason' => null,
        'held_at'     => null,
    ]);

    NotificationLog::create([
    'service_request_id' => $sr->id,
    'event'       => 'status_updated',
    'title'       => 'Status Updated',
    // 'message'     => $this->buildSrRef($sr) . ' accepted — ETA '
    //                  . $sr->eta_at->format('d M Y, h:i A')
    //                  . ($worker->name ?? '' ? ' by ' . $worker->name : ''),

    'message' => $this->buildSrRef($sr) . ' accepted'
        . (optional($worker)->name ? ' by ' . $worker->name : ''),        
    'from_status' => $oldStatus,   // e.g. 'Assigned' or 'Rework'
    'to_status'   => 'Accepted',
    'caused_by'   => auth()->id(),
    ]);

    app(\App\Services\WhatsAppService::class)->notifyServiceStatus($sr, 'Accepted');

    return response()->json([
        'ok'     => true,
        'sr_id'  => $sr->id,
        'eta_at' => $sr->eta_at->format('Y-m-d H:i'),
    ]);
}
    public function reschedule(Request $request)
{
    $data = $request->validate([
        'sr_id'    => ['required', 'integer'],
        'eta_date' => ['required', 'date_format:Y-m-d'],
        'eta_time' => ['required', 'date_format:H:i'],
        'remark'   => ['required', 'string', 'max:1000'],
    ]);

    $sr     = $this->ownedRequest($request, $data['sr_id']);
    $worker = $this->worker($request);

    $newEta = \Carbon\Carbon::createFromFormat(
        'Y-m-d H:i', $data['eta_date'] . ' ' . $data['eta_time']
    );

    abort_if($newEta->isPast(), 422, 'The new ETA must be in the future.');

    $oldStatus = $sr->status;          // capture BEFORE the transaction updates it

    DB::transaction(function () use ($sr, $worker, $data, $newEta,$oldStatus) {
        // Log before mutating — previous_eta_at must capture the old value.
        ServiceRequestReschedule::create([
            'service_request_id' => $sr->id,
            'user_id'            => $worker->id,
            'previous_eta_at'    => $sr->eta_at,
            'new_eta_at'         => $newEta,
            'reason'             => $data['remark'],
            'from_status'        => $sr->status,
        ]);

        $this->pauseOpenPunch($sr);

        $sr->update([
            'eta_at'          => $newEta,
            'status'          => 'Reschedule',
            'rescheduled_at'  => now(),
            'sla_started_at'  => now(),
            'paused_seconds'  => 0,
            'held_at'         => null,
            'hold_reason'     => null,
            'internal_remark' => $this->appendRemark($sr->internal_remark, 'Rescheduled', $data['remark']),
        ]);

          NotificationLog::create([
            'service_request_id' => $sr->id,
            'event'       => 'status_updated',
            'title'       => 'Status Updated',
            'message'     => $this->buildSrRef($sr) . ' Rescheduled to '
                             . $newEta->format('d M Y, h:i A')
                             . (optional($worker)->name ? ' by ' . $worker->name : ''),
            'from_status' => $oldStatus,   // e.g. 'In Progress' or 'On Hold'
            'to_status'   => 'Reschedule',
            'caused_by'   => $worker->id ?? auth()->id(),
        ]);
    });

    $sr->refresh();

    return response()->json([
        'ok'         => true,
        'eta_at'     => $sr->eta_at->format('Y-m-d H:i'),
        'reason'     => $data['remark'],
        'count'      => $sr->reschedules()->count(),
    ]);
}

    public function hold(Request $request)
    {
        $data = $request->validate([
            'sr_id'  => ['required', 'integer'],
            'remark' => ['required', 'string', 'max:1000'],
        ]);

        $sr = $this->ownedRequest($request, $data['sr_id']);
       $oldStatus = $sr->status;          // capture BEFORE the transaction updates it

        DB::transaction(function () use ($sr, $data,$oldStatus) {
            $sr->update([
                'status'      => 'On Hold',
                'hold_reason' => $data['remark'],
                'held_at'     => now(),
                'internal_remark' => $this->appendRemark($sr->internal_remark, 'On Hold', $data['remark']),
            ]);

            // An in-progress punch is abandoned when the job goes on hold.
            Punch::where('service_request_id', $sr->id)
                ->whereIn('status', ['draft', 'punched_in'])
                ->update(['status' => 'cancelled']);

            NotificationLog::create([
            'service_request_id' => $sr->id,
            'event'       => 'status_updated',
            'title'       => 'Status Updated',
            'message'     => $this->buildSrRef($sr) . ' put on hold — ' . $data['remark'],
            'from_status' => $oldStatus,   // e.g. 'In Progress'
            'to_status'   => 'On Hold',
            'caused_by'   => auth()->id(),
            ]);

        });

        return response()->json(['ok' => true]);
    }

    private function pauseOpenPunch(ServiceRequest $sr): void
{
    Punch::where('service_request_id', $sr->id)
        ->whereIn('status', ['draft', 'punched_in'])
        ->update(['status' => 'cancelled']);
}

   public function resume(Request $request)
{
    $data = $request->validate(['sr_id' => ['required', 'integer']]);
    $sr = $this->ownedRequest($request, $data['sr_id']);

    abort_unless(strtolower((string) $sr->status) === 'on hold', 422, 'Job is not on hold.');

    $oldStatus = $sr->status;          // capture BEFORE update ('On Hold')

    $sr->update([
        'status'      => 'Accepted',
        'hold_reason' => null,
        'held_at'     => null,
    ]);

     NotificationLog::create([
        'service_request_id' => $sr->id,
        'event'       => 'status_updated',
        'title'       => 'Status Updated',
        'message'     => $this->buildSrRef($sr) . ' resumed from hold',
        'from_status' => $oldStatus,   // 'On Hold'
        'to_status'   => 'Accepted',
        'caused_by'   => auth()->id(),
    ]);

    return response()->json(['ok' => true, 'sr_id' => $sr->id]);
}

    /*
    |--------------------------------------------------------------------------
    | Resolution + ownership
    |--------------------------------------------------------------------------
    */

    /**
     * The worker whose pipeline we are acting on.
     *
     * TODO: restore auth. Until then, resolution order is:
     *   1. ?worker=N on the current request
     *   2. the worker whose pipeline was last rendered (session)
     *   3. the first Maintenance Lead in the table
     *
     * index() and the POST handlers MUST agree on this, or ownedRequest()
     * fails its firstOrFail() with "No query results".
     */
   private function worker(Request $request): User
    {
        $user = Auth::guard('worker')->user();

        abort_unless($user, 401, 'Not signed in.');

        return $user;
    }

    /** Fetch an SR, 404ing unless this worker is the assignee. */
    private function ownedRequest(Request $request, int $srId): ServiceRequest
    {
        return ServiceRequest::where('id', $srId)
            ->where('assigned_user_id', $this->worker($request)->id)
            ->firstOrFail();
    }

    /*
    |--------------------------------------------------------------------------
    | Presentation
    |--------------------------------------------------------------------------
    */

    /**
     * The job the worker is mid-punch on, if any. Rehydrates the terminal
     * across a page reload — otherwise an open punch is invisible to the UI.
     */
   private function activeJob(User $user): ?array
{
    $punch = Punch::with([
            'serviceRequest.client',
            'serviceRequest.project',
            'serviceRequest.category',
            'serviceRequest.domain',
            'serviceRequest.punches.user.role',
            'serviceRequest.createdBy.role',
            'serviceRequest.qcReviewedBy.role',
            'serviceRequest.assignedUser',
            'items',
            'photos',
        ])
        ->where('user_id', $user->id)
        ->whereIn('status', ['draft', 'punched_in'])
        ->latest('id')
        ->first();

    return ($punch && $punch->serviceRequest)
        ? $this->buildActive($punch->serviceRequest, $punch)
        : null;
}

private function buildActive(ServiceRequest $sr, ?Punch $punch): array
{
    return [
        'job'       => $this->transform($sr),
        'etaDate'   => optional($sr->eta_at)->format('Y-m-d'),
        'etaTime'   => optional($sr->eta_at)->format('H:i'),
        'punchInAt' => $punch ? optional($punch->punch_in_at)->toIso8601String() : null,
        'workDesc'  => $punch->work_description ?? null,
        'uploads'   => [
            'before' => $punch ? $punch->photos->where('type', 'before')->map(fn ($p) => [
                'id' => $p->id, 'url' => $p->url,
            ])->values() : collect(),
            'after'  => $punch ? $punch->photos->where('type', 'after')->map(fn ($p) => [
                'id' => $p->id, 'url' => $p->url,
            ])->values() : collect(),
        ],
        'punchInLocation' => ($punch && $punch->punch_in_lat) ? [
            'lat'      => (float) $punch->punch_in_lat,
            'lng'      => (float) $punch->punch_in_lng,
            'accuracy' => $punch->punch_in_accuracy !== null ? (float) $punch->punch_in_accuracy : null,
            'address'  => $punch->punch_in_address,
        ] : null,
        'expenses' => $punch
            ? $punch->items->map(fn (Punchitem $i) => [
                'category'   => $i->category,
                'name'       => $i->name,
                'amount'     => number_format((float) $i->line_total, 2, '.', ''),
                'time'       => optional($i->created_at)->format('H:i') ?? '—',
                'receiptUrl' => $i->receipt_url,
              ])->values()
            : collect(),
        'workDesc'  => $punch ? $punch->work_description : null,
    ];
}

   private function transform(ServiceRequest $sr): array
{
    $created = $sr->dispatched_at ?? $sr->created_at;

    // 1. Map real DB statuses into explicit UI Filter states
   $uiStatus = match (strtolower((string) $sr->status)) {
    'rework'            => 'Rework',
    'on hold'           => 'On Hold',
    'reschedule'        => 'Rescheduled',
    'qc review'         => 'Review',
    'completed',
    'pending invoice',
    'invoice submitted' => 'Completed',
    default             => 'Pending',
};

if ($uiStatus === 'Pending' && ($sr->accepted_at
    || strtolower((string) $sr->status) === 'accepted'
    || strtolower((string) $sr->status) === 'in progress')) {
    $uiStatus = 'Accepted';
}

    $lastReschedule = $sr->reschedules->first();

    return [
        'id'          => $sr->ref,
        'sr_id'       => $sr->id,
        'status'      => $uiStatus, 
        'client'      => optional($sr->client)->company_name ?? '—',
        'contract'    => optional($sr->project)->project_name ?? ($sr->invoice_code ?? '—'),
        'domain'      => optional($sr->domain)->domain_name
                         ?? optional($sr->category)->name
                         ?? '—',
        'site'        => optional($sr->project)->site_address ?? '—',
        'siteName'    => optional($sr->project)->site_name ?? '—',
        'description' => $sr->issue_description ?? '—',
        'priority'    => ucfirst($sr->priority_level ?? 'Normal'),
        'hrsAgo'      => $created ? (int) $created->diffInHours(now()) : 0,
        'reworkNote'  => $sr->rework_notes,

        'eta'             => optional($sr->eta_at)->format('Y-m-d H:i'),
        'rescheduleCount' => $sr->reschedules->count(),
        'rescheduleReason'=> $lastReschedule?->reason,
        'rescheduledAt'   => optional($lastReschedule?->created_at)->format('d M Y, H:i'),
        'previousEta'     => optional($lastReschedule?->previous_eta_at)->format('d M Y, H:i'),
        'accepted'    => (bool) $sr->accepted_at,
        'attachments' => collect($sr->attachments ?? [])->values()->all(),
        'history'     => $this->srHistory($sr),
    ];
}

private function srHistory(ServiceRequest $sr): array
{
    $log = [];
    $fmt = fn ($d) => $d->format('d M Y, H:i');

    $raiserRole = optional(optional($sr->createdBy)->role)->name ?? 'System';
    if ($sr->created_at) {
        $log[] = "Ticket raised by {$raiserRole} on " . $fmt($sr->created_at);
    }

    if ($sr->qc_reviewed_at) {
        $qcRole = optional(optional($sr->qcReviewedBy)->role)->name ?? 'QC';
        $log[] = "Approved by {$qcRole} on " . $fmt($sr->qc_reviewed_at);
    }

    if ($sr->dispatched_at && $sr->assignedUser) {
        $log[] = "Assigned to {$sr->assignedUser->name} on " . $fmt($sr->dispatched_at);
    }

    foreach ($sr->reschedules->sortBy('id') as $r) {
    $who  = optional($r->user)->name ?? 'Worker';
    $from = $r->previous_eta_at ? $r->previous_eta_at->format('d M Y, H:i') : 'unset';
    $log[] = "Rescheduled by {$who} on " . $fmt($r->created_at)
           . " — ETA {$from} → " . $r->new_eta_at->format('d M Y, H:i')
           . ' — ' . $r->reason;
}
    if ($sr->held_at) {
        $log[] = 'On hold on ' . $fmt($sr->held_at)
               . ($sr->hold_reason ? ' — ' . $sr->hold_reason : '');
    }

    foreach ($sr->punches->sortBy('id') as $p) {
        $who = optional($p->user)->name ?? 'Worker';
        if ($p->punch_in_at) {
            $log[] = "Punched in by {$who} on " . $fmt($p->punch_in_at);
        }
        if ($p->punch_out_at) {
            $log[] = "Punched out by {$who} on " . $fmt($p->punch_out_at)
                   . ($p->duration_label ? " ({$p->duration_label})" : '');
        }
    }

    return $log;
}
    private function routes(): array
    {
        return [
            'accept'        => route('worker.job.accept'),
            'punchIn'       => route('worker.punch.in'),
            'punchOut'      => route('worker.punch.out'),
            'upload'        => route('worker.punch.upload'),
            'expense'       => route('worker.punch.expense'),
            'reschedule'    => route('worker.job.reschedule'),
            'hold'          => route('worker.job.hold'),
            'resume'        => route('worker.job.resume'),
            'history'       => route('worker.history'),
            'profile'       => route('worker.profile'),
            'signature'     => route('worker.punch.signature'),
            'photoDelete'   => route('worker.punch.photo.delete'),
            'changePassword'=> route('worker.password.change'),

        ];
    }

    private function expenseCategories(): array
    {
        return ExpenseCategory::where('status', true)
            ->orderBy('name')
            ->pluck('name')
            ->all();
    }

    private function initials(?string $name): string
    {
        return collect(preg_split('/\s+/', trim((string) $name)))
            ->filter()
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->take(2)
            ->implode('');
    }

    private function appendRemark(?string $existing, string $label, string $remark): string
    {
        $entry = '[' . now()->format('d M Y H:i') . "] {$label}: {$remark}";

        return trim(($existing ? $existing . "\n" : '') . $entry);
    }
}