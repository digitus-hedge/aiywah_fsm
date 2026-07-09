<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use App\Models\Punch;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkerPipelineController extends Controller
{
    /** Statuses a worker can act on from the pipeline. */
    private const OPEN_STATUSES = ['assigned', 'dispatched', 'rework', 'on_hold', 'accepted'];

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

        $requests = ServiceRequest::with(['client', 'project', 'category', 'domain', 'punches'])
            ->where('assigned_user_id', $user->id)
            ->whereIn('status', self::OPEN_STATUSES)
            ->orderByDesc('dispatched_at')
            ->orderByDesc('id')
            ->get();

        return view('worker.pipeline', [
            'jobs'              => $requests->map(fn (ServiceRequest $sr) => $this->transform($sr))->values(),
            'routes'            => $this->routes(),
            'expenseCategories' => $this->expenseCategories(),
            'userName'          => $user->name,
            'userRole'          => optional($user->role)->name ?? 'Maintenance Lead',
            'userCode'          => optional($user->role)->code,
            'userInitials'      => $this->initials($user->name),
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

        abort_if($sr->accepted_at, 409, 'This job has already been accepted.');

        $sr->update([
            'eta_at'      => $data['eta_date'] . ' ' . $data['eta_time'] . ':00',
            'accepted_at' => now(),
            'status'      => 'accepted',
            'hold_reason' => null,
            'held_at'     => null,
        ]);

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
                'status'      => 'on_hold',
                'hold_reason' => $data['remark'],
                'held_at'     => now(),
            ]);

            // An in-progress punch is abandoned when the job goes on hold.
            Punch::where('service_request_id', $sr->id)
                ->whereIn('status', ['draft', 'punched_in'])
                ->update(['status' => 'cancelled']);
        });

        return response()->json(['ok' => true]);
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
     *   1. authenticated user, if any
     *   2. ?worker=N on the current request
     *   3. the worker whose pipeline was last rendered (session)
     *   4. the first Maintenance Lead in the table
     *
     * index() and the POST handlers MUST agree on this, or ownedRequest()
     * fails its firstOrFail() with "No query results".
     */
 private function worker(Request $request): User
{
    // TODO: restore auth. Explicit override wins, then session, then first ML.
    $id = $request->input('worker') ?? session('dev_worker_id');
    if ($id) {
        return User::findOrFail((int) $id);
    }

    $mlRoleId = \App\Models\Role::where('code', 'ML')->value('id');
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

    private function transform(ServiceRequest $sr): array
    {
        $created = $sr->dispatched_at ?? $sr->created_at;

        return [
            'id'          => $sr->ref,
            'sr_id'       => $sr->id,
            'status'      => strtolower((string) $sr->status) === 'rework' ? 'Rework' : 'Assigned',
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
            'history'     => $this->history($sr),
        ];
    }

    private function history(ServiceRequest $sr): array
    {
        $log = [];

        if ($sr->created_at) {
            $log[] = 'Created · ' . $sr->created_at->format('d M Y, H:i');
        }
        if ($sr->dispatched_at) {
            $log[] = 'Dispatched to you · ' . $sr->dispatched_at->format('d M Y, H:i');
        }
        if ($sr->accepted_at) {
            $log[] = 'Accepted · ' . $sr->accepted_at->format('d M Y, H:i');
        }
        if ($sr->eta_at) {
            $log[] = 'ETA set · ' . $sr->eta_at->format('d M Y, H:i');
        }
        if ($sr->held_at) {
            $log[] = 'On hold · ' . $sr->held_at->format('d M Y, H:i')
                   . ($sr->hold_reason ? ' — ' . $sr->hold_reason : '');
        }
        if ($sr->qc_reviewed_at) {
            $log[] = 'QC reviewed · ' . $sr->qc_reviewed_at->format('d M Y, H:i');
        }

        foreach ($sr->punches->sortBy('id') as $punch) {
            if ($punch->punch_in_at) {
                $log[] = 'Punched in · ' . $punch->punch_in_at->format('d M Y, H:i');
            }
            if ($punch->punch_out_at) {
                $log[] = 'Punched out · ' . $punch->punch_out_at->format('d M Y, H:i')
                       . ($punch->duration_label ? ' (' . $punch->duration_label . ')' : '');
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