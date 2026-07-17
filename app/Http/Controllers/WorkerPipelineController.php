<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use App\Models\Punch;
use App\Models\Punchitem;
use App\Models\Role;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkerPipelineController extends Controller
{
    /** Statuses a worker can act on from the pipeline. */
    private const OPEN_STATUSES = [
    'Assigned', 'Accepted', 'In Progress',
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

        // TODO: remove with the auth bypass. Lets the POST routes below know
        // which worker the pipeline was rendered for.
        session(['dev_worker_id' => $user->id]);

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
        ]);
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

        $sr = $this->ownedRequest($request, $data['sr_id']);

        // Rework re-enters the pipeline with accepted_at still set from the first
        // pass, so the gate is on status, not on the timestamp.
        $isRework = strtolower((string) $sr->status) === 'Rework';
        abort_if($sr->accepted_at && !$isRework, 409, 'This job has already been accepted.');

        $sr->update([
            'eta_at'      => $data['eta_date'] . ' ' . $data['eta_time'] . ':00',
            'accepted_at' => now(),
            'status'      => 'Accepted',
            'hold_reason' => null,
            'held_at'     => null,
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

        $sr = $this->ownedRequest($request, $data['sr_id']);

        $sr->update([
            'eta_at'          => $data['eta_date'] . ' ' . $data['eta_time'] . ':00',
            'internal_remark' => $this->appendRemark($sr->internal_remark, 'Rescheduled', $data['remark']),
        ]);

        return response()->json([
            'ok'     => true,
            'eta_at' => $sr->eta_at->format('Y-m-d H:i'),
        ]);
    }

    public function hold(Request $request)
    {
        $data = $request->validate([
            'sr_id'  => ['required', 'integer'],
            'remark' => ['required', 'string', 'max:1000'],
        ]);

        $sr = $this->ownedRequest($request, $data['sr_id']);

        DB::transaction(function () use ($sr, $data) {
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
        });

        return response()->json(['ok' => true]);
    }

    public function resume(Request $request)
{
    $data = $request->validate(['sr_id' => ['required', 'integer']]);
    $sr = $this->ownedRequest($request, $data['sr_id']);

    abort_unless(strtolower((string) $sr->status) === 'On Hold', 422, 'Job is not on hold.');

    $sr->update([
        'status'      => 'accepted',
        'hold_reason' => null,
        'held_at'     => null,
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
        $id = $request->input('worker') ?? session('dev_worker_id');

        if ($id) {
            return User::findOrFail((int) $id);
        }

        $mlRoleId = Role::where('code', 'ML')->value('id');
        abort_unless($mlRoleId, 500, 'ML role missing — run RoleSeeder.');

        $worker = User::where('role_id', $mlRoleId)->first();
        abort_unless($worker, 500, 'No Maintenance Lead user exists.');

        return $worker;
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
        ])
        ->where('user_id', $user->id)
        ->whereIn('status', ['draft', 'punched_in'])
        ->latest('id')
        ->first();

    if ($punch && $punch->serviceRequest) {
        return $this->buildActive($punch->serviceRequest, $punch);
    }

    // Accepted but not yet punched in — still the worker's active job.
        $sr = ServiceRequest::with([
                'client', 'project', 'category', 'domain',
                'punches.user.role', 'createdBy.role', 'qcReviewedBy.role', 'assignedUser',
            ])
            ->where('assigned_user_id', $user->id)
            ->whereIn('status', ['Accepted', 'In Progress'])
            ->whereNotNull('accepted_at')
            ->latest('accepted_at')
            ->first();

        return $sr ? $this->buildActive($sr, null) : null;
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
            'before' => $punch ? filled($punch->start_photo_path) : false,
            'after'  => $punch ? filled($punch->finish_photo_path) : false,
        ],
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

        return [
            'id'          => $sr->ref,
            'sr_id'       => $sr->id,
            'status' => match (strtolower((string) $sr->status)) {
                            'Rework'  => 'Rework',
                            'On Hold' => 'On Hold',
                            default   => 'Assigned',
                        },
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
            'eta'         => optional($sr->eta_at)->format('Y-m-d H:i'),
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

    if ($sr->accepted_at) {
        $log[] = 'Accepted on ' . $fmt($sr->accepted_at);
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
            'accept'     => route('worker.job.accept'),
            'punchIn'    => route('worker.punch.in'),
            'punchOut'   => route('worker.punch.out'),
            'upload'     => route('worker.punch.upload'),
            'expense'    => route('worker.punch.expense'),
            'reschedule' => route('worker.job.reschedule'),
            'hold'       => route('worker.job.hold'),
            'resume'     => route('worker.job.resume'),
            'history'    => route('worker.history'),
            'profile'    => route('worker.profile'),
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