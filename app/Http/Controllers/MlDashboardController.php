<?php

namespace App\Http\Controllers;

use App\Models\Punch;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MlDashboardController extends Controller
{
    /** Same open set the pipeline uses, so the two pages never disagree on counts. */
    private const OPEN_STATUSES = [
        'Assigned', 'Accepted', 'In Progress', 'Reschedule',
        'Rework', 'On Hold', 'Qc Review', 'Completed',
        'Pending Invoice', 'Invoice Submitted',
    ];

    /** Statuses that still need the technician to act. */
    private const ACTIONABLE = [
        'Assigned', 'Accepted', 'In Progress', 'Reschedule', 'Rework', 'On Hold',
    ];

    /** Statuses that mean the technician is done with it. */
    private const CLOSED = ['Completed', 'Pending Invoice', 'Invoice Submitted'];

    /** Chart palette - matches the swatches used in the dashboard CSS. */
    private const PALETTE = [
        '#9a8053', '#2563eb', '#0891b2', '#15803d', '#d97706', '#7c3aed', '#dc2626', '#64748b',
    ];

    /** How many months of history the trend charts cover, including the current one. */
    private const TREND_MONTHS = 6;

    public function index(Request $request)
    {
        $user = $this->worker($request);
        $user->loadMissing('role');

        // One pass over the worker's jobs - every block below reads from this.
        $requests = ServiceRequest::with(['client', 'project', 'domain', 'category'])
            ->where('assigned_user_id', $user->id)
            ->orderBy('eta_at')
            ->get();

        $open = $requests->whereIn('status', self::OPEN_STATUSES);

        // The job the worker is physically on right now, if any.
        $activePunch = Punch::with([
                'serviceRequest.client', 'serviceRequest.project',
                'serviceRequest.domain', 'serviceRequest.category',
            ])
            ->where('user_id', $user->id)
            ->whereIn('status', ['draft', 'punched_in'])
            ->whereNotNull('punch_in_at')
            ->latest('id')
            ->first();

        // Finished punches back to the start of the trend window.
        $punches = Punch::with(['items', 'serviceRequest.client'])
            ->where('user_id', $user->id)
            ->whereNotNull('punch_out_at')
            ->where('punch_out_at', '>=', now()->startOfMonth()->subMonths(self::TREND_MONTHS - 1))
            ->get();

        return view('worker.dashboard', [
            'live'      => $this->live($activePunch, $open),
            'today'     => $this->today($activePunch, $open),
            'pipeline'  => $this->pipelineBlock($requests, $open, $punches),
            'scorecard' => $this->scorecard($requests, $punches),
            'qc'        => $this->qc($requests),
            'expenses'  => $this->expenses($punches),
            'routes'    => $this->routes(),

            'userName'     => $user->name,
            'userRole'     => optional($user->role)->name ?? 'Maintenance Lead',
            'userCode'     => optional($user->role)->code,
            'userInitials' => $this->initials($user->name),
            'userShort'    => $this->shortName($user->name),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Live strip
    |--------------------------------------------------------------------------
    */

    /** The banner under the header: where the worker is, and what is closest to breaching. */
    private function live(?Punch $punch, Collection $open): array
    {
        $sr = $punch?->serviceRequest;

        $active = $sr ? [
            'ref'    => $sr->ref,
            'site'   => $this->shortSite(optional($sr->project)->site_name ?: optional($sr->project)->site_address),
            'since'  => optional($punch->punch_in_at)->format('h:i A'),
            'sr_id'  => $sr->id,
        ] : null;

        // Nearest ETA among jobs the worker still owes work on.
        $next = $open->whereIn('status', self::ACTIONABLE)
            ->filter(fn (ServiceRequest $s) => $s->eta_at)
            ->sortBy(fn (ServiceRequest $s) => $s->eta_at->timestamp)
            ->first();

        $sla = null;

        if ($next) {
            $minutes = (int) round(now()->diffInMinutes($next->eta_at, false));

            $sla = [
                'ref'   => $next->ref,
                'label' => $minutes < 0
                    ? 'Past ETA by ' . $this->humanGap(abs($minutes))
                    : 'ETA in ' . $this->humanGap($minutes),
                'state' => $minutes < 0 ? 'crit' : ($minutes <= 120 ? 'warn' : 'ok'),
            ];
        }

        return ['active' => $active, 'sla' => $sla];
    }

    /*
    |--------------------------------------------------------------------------
    | Tab: Today
    |--------------------------------------------------------------------------
    */

    private function today(?Punch $punch, Collection $open): array
    {
        $activeSr = $punch?->serviceRequest;

        $active = $activeSr ? array_merge($this->card($activeSr), [
            'punchInAt' => optional($punch->punch_in_at)->toIso8601String(),
            'workDesc'  => $punch->work_description,
        ]) : null;

        $queue = $open->whereIn('status', self::ACTIONABLE)
            ->reject(fn (ServiceRequest $sr) => $activeSr && $sr->id === $activeSr->id)
            ->sortBy(fn (ServiceRequest $sr) => $sr->eta_at?->timestamp ?? PHP_INT_MAX)
            ->values();

        return [
            'active' => $active,
            'next'   => $queue->first() ? $this->card($queue->first()) : null,
            'others' => $queue->slice(1)->take(6)->map(fn (ServiceRequest $sr) => $this->card($sr))->values()->all(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Tab: Pipeline
    |--------------------------------------------------------------------------
    */

    private function pipelineBlock(Collection $requests, Collection $open, Collection $punches): array
    {
        $monthStart = now()->startOfMonth();

        $assignedThisMonth = $requests->filter(function (ServiceRequest $sr) use ($monthStart) {
            $at = $sr->dispatched_at ?? $sr->created_at;

            return $at && $at->greaterThanOrEqualTo($monthStart);
        });

        $completedThisMonth = $punches->filter(
            fn (Punch $p) => $p->punch_out_at->greaterThanOrEqualTo($monthStart)
        );

        $byStatus = fn (array $statuses) => $open->whereIn('status', $statuses)->count();

        // 'f' is the pipeline filter tab this row links to.
        $rows = [
            ['lbl' => 'Assigned to me', 'n' => $assignedThisMonth->count(),           'c' => '#2563eb', 'f' => 'Pending'],
            ['lbl' => 'ETA confirmed',  'n' => $byStatus(['Accepted', 'Reschedule']), 'c' => '#0891b2', 'f' => 'Accepted'],
            ['lbl' => 'In progress',    'n' => $byStatus(['In Progress']),            'c' => '#d97706', 'f' => 'Accepted'],
            ['lbl' => 'On hold',        'n' => $byStatus(['On Hold']),                'c' => '#a16207', 'f' => 'On Hold'],
            ['lbl' => 'Pending review', 'n' => $byStatus(['Qc Review']),              'c' => '#ea580c', 'f' => 'Review'],
            ['lbl' => 'Rework',         'n' => $byStatus(['Rework']),                 'c' => '#dc2626', 'f' => 'Rework'],
            ['lbl' => 'Completed',      'n' => $completedThisMonth->count(),          'c' => '#059669', 'f' => 'Completed'],
        ];

        $peak = max(1, collect($rows)->max('n'));

        $funnel = collect($rows)->map(function (array $r) use ($peak) {
            $r['w'] = (int) round(($r['n'] / $peak) * 100);

            return $r;
        })->all();

        $action = $open->whereIn('status', ['Rework', 'On Hold'])
            ->sortByDesc('id')
            ->take(5)
            ->map(fn (ServiceRequest $sr) => [
                'ref'    => $sr->ref,
                'sr_id'  => $sr->id,
                'client' => optional($sr->client)->company_name ?? '-',
                'site'   => $this->shortSite(optional($sr->project)->site_address),
                'note'   => strtolower((string) $sr->status) === 'rework'
                    ? ($sr->rework_notes ?: 'Returned by QC. Open the job for the full instruction.')
                    : ($sr->hold_reason ?: 'Held. Resume it from the pipeline when you can attend.'),
                'kind'   => strtolower((string) $sr->status) === 'rework' ? 'rework' : 'hold',
                'label'  => strtolower((string) $sr->status) === 'rework' ? 'Rework' : 'On hold',
                'pill'   => strtolower((string) $sr->status) === 'rework' ? 'pill-red' : 'pill-amber',
            ])->values()->all();

        return [
            'stats' => [
                'assigned'  => $assignedThisMonth->count(),
                'completed' => $completedThisMonth->count(),
                'activeNow' => $open->whereIn('status', self::ACTIONABLE)->count(),
            ],
            'funnel' => $funnel,
            'scope'  => $this->scopeSplit($requests),
            'action' => $action,
        ];
    }

    /**
     * In-warranty vs out-of-warranty split.
     *
     * The column name differs between installs, so probe for it and return null
     * when there is nothing to read - the view hides the card in that case.
     */
    private function scopeSplit(Collection $requests): ?array
    {
        $column = collect(['warranty_category_id', 'warranty_status', 'is_warranty', 'under_warranty', 'warranty_type'])
            ->first(fn (string $c) => Schema::hasColumn('service_requests', $c));

        if (!$column) {
            return null;
        }

        $in = $requests->filter(fn (ServiceRequest $sr) => $this->isInWarranty($sr->{$column}))->count();
        $out = $requests->count() - $in;

        if ($in + $out === 0) {
            return null;
        }

        return [
            'labels' => ['In-warranty', 'Out-of-warranty'],
            'data'   => [$in, $out],
            'colors' => ['#9a8053', '#393837'],
            'total'  => $in + $out,
        ];
    }

    private function isInWarranty($value): bool
    {
        if (is_null($value) || $value === '') {
            return false;
        }
        if (is_bool($value) || is_numeric($value)) {
            return (bool) $value;
        }

        $v = strtolower((string) $value);

        return str_contains($v, 'in') && !str_contains($v, 'out') && !str_contains($v, 'non');
    }

    /*
    |--------------------------------------------------------------------------
    | Tab: Scorecard
    |--------------------------------------------------------------------------
    */

    private function scorecard(Collection $requests, Collection $punches): array
    {
        $monthStart = now()->startOfMonth();
        $prevStart  = now()->startOfMonth()->subMonth();

        $thisMonth = $punches->filter(fn (Punch $p) => $p->punch_out_at->greaterThanOrEqualTo($monthStart));
        $lastMonth = $punches->filter(fn (Punch $p) => $p->punch_out_at->between($prevStart, $monthStart));

        // Rolling months, oldest first.
        $labels = [];
        $series = [];

        for ($i = self::TREND_MONTHS - 1; $i >= 0; $i--) {
            $start = now()->startOfMonth()->subMonths($i);
            $end   = $start->copy()->endOfMonth();

            $labels[] = $start->format('M');
            $series[] = $punches->filter(fn (Punch $p) => $p->punch_out_at->between($start, $end))->count();
        }

        $categories = $requests
            ->whereIn('status', self::CLOSED)
            ->groupBy(fn (ServiceRequest $sr) => optional($sr->domain)->domain_name
                ?? optional($sr->category)->name
                ?? 'Unclassified')
            ->map->count()
            ->sortDesc()
            ->take(5);

        $hours = round($thisMonth->sum(
            fn (Punch $p) => $p->punch_in_at ? $p->punch_in_at->floatDiffInHours($p->punch_out_at) : 0
        ), 1);

        return [
            'jobsDone'    => $thisMonth->count(),
            'jobsDelta'   => $thisMonth->count() - $lastMonth->count(),
            'reworkCount' => $requests->where('status', 'Rework')->count(),
            'hours'       => $hours,
            'avgDuration' => $thisMonth->count()
                ? $this->humanGap((int) round(($hours * 60) / $thisMonth->count()))
                : '-',
            'trend'      => ['labels' => $labels, 'data' => $series],
            'categories' => [
                'labels' => $categories->keys()->all(),
                'data'   => $categories->values()->all(),
                'colors' => array_slice(self::PALETTE, 0, max(1, $categories->count())),
            ],
            'rating' => $this->ratings($requests),
        ];
    }

    /**
     * Client feedback scores for this worker's jobs.
     *
     * The feedback table is optional, so everything is probed first and the
     * card falls back to an empty state when the schema is not there.
     */
    private function ratings(Collection $requests): ?array
    {
        $table = collect(['client_feedback', 'client_feedbacks', 'feedbacks', 'feedback'])
            ->first(fn (string $t) => Schema::hasTable($t));

        if (!$table || !Schema::hasColumn($table, 'service_request_id')) {
            return null;
        }

        $column = collect(['rating', 'overall_rating', 'stars', 'score'])
            ->first(fn (string $c) => Schema::hasColumn($table, $c));

        if (!$column) {
            return null;
        }

        $srIds = $requests->pluck('id');

        if ($srIds->isEmpty()) {
            return null;
        }

        $rows = DB::table($table)
            ->whereIn('service_request_id', $srIds)
            ->whereNotNull($column)
            ->pluck($column)
            ->map(fn ($v) => (int) round((float) $v))
            ->filter(fn (int $v) => $v >= 1 && $v <= 5);

        if ($rows->isEmpty()) {
            return null;
        }

        $counts = $rows->countBy();
        $closed = $requests->whereIn('status', self::CLOSED)->count();

        return [
            'avg'      => round($rows->avg(), 1),
            'count'    => $rows->count(),
            'hist'     => collect(range(5, 1))->map(fn (int $s) => ['s' => $s, 'c' => (int) $counts->get($s, 0)])->all(),
            'closed'   => $closed,
            'response' => $closed ? (int) round(($rows->count() / $closed) * 100) : 0,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Tab: QC & Rework
    |--------------------------------------------------------------------------
    */

    private function qc(Collection $requests): array
    {
        $pending = $requests->where('status', 'Qc Review')
            ->sortByDesc('id')
            ->map(fn (ServiceRequest $sr) => [
                'ref'    => $sr->ref,
                'sr_id'  => $sr->id,
                'client' => optional($sr->client)->company_name ?? '-',
                'site'   => $this->shortSite(optional($sr->project)->site_address),
                'note'   => $sr->issue_description ?: 'Submitted with proof documents. Waiting on the QC reviewer.',
                'since'  => optional($sr->updated_at)->diffForHumans() ?? '-',
            ])->values()->all();

        $rework = $requests->where('status', 'Rework')
            ->sortByDesc('id')
            ->map(fn (ServiceRequest $sr) => [
                'ref'    => $sr->ref,
                'sr_id'  => $sr->id,
                'client' => optional($sr->client)->company_name ?? '-',
                'site'   => $this->shortSite(optional($sr->project)->site_address),
                'note'   => $sr->issue_description ?: '-',
                'reason' => $sr->rework_notes ?: 'No instruction was recorded. Check with the reviewer before revisiting.',
                'since'  => optional($sr->updated_at)->diffForHumans() ?? '-',
            ])->values()->all();

        return [
            'pending' => $pending,
            'rework'  => $rework,
            'badge'   => count($pending) + count($rework),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Tab: Expenses
    |--------------------------------------------------------------------------
    */

    private function expenses(Collection $punches): array
    {
        $monthStart = now()->startOfMonth();
        $thisMonth  = $punches->filter(fn (Punch $p) => $p->punch_out_at->greaterThanOrEqualTo($monthStart));

        $items = $thisMonth->flatMap(fn (Punch $p) => $p->items->map(function ($item) use ($p) {
            return (object) [
                'category' => $item->category ?: 'Uncategorised',
                'name'     => $item->name,
                'amount'   => (float) $item->line_total,
                'receipt'  => (bool) $item->receipt_url,
                'ref'      => optional($p->serviceRequest)->ref ?? '-',
                'client'   => optional(optional($p->serviceRequest)->client)->company_name ?? '-',
                'sr_id'    => $p->service_request_id,
            ];
        }));

        // Per-job roll-up, biggest spend first.
        $byJob = $items->groupBy('ref')->map(function (Collection $group, string $ref) {
            $first = $group->first();

            return [
                'ref'      => $ref,
                'sr_id'    => $first->sr_id,
                'client'   => $first->client,
                'category' => $group->countBy('category')->sortDesc()->keys()->first() ?? 'Materials',
                'lines'    => $group->count(),
                'amount'   => round($group->sum('amount'), 2),
                'receipt'  => $group->every(fn ($i) => $i->receipt),
            ];
        })->sortByDesc('amount')->take(8)->values()->all();

        $byCategory = $items->groupBy('category')
            ->map(fn (Collection $g) => round($g->sum('amount'), 2))
            ->sortDesc()
            ->take(5);

        // Rolling months, oldest first - same window as the scorecard trend.
        $labels = [];
        $series = [];

        for ($i = self::TREND_MONTHS - 1; $i >= 0; $i--) {
            $start = now()->startOfMonth()->subMonths($i);
            $end   = $start->copy()->endOfMonth();

            $labels[] = $start->format('M');
            $series[] = round(
                $punches->filter(fn (Punch $p) => $p->punch_out_at->between($start, $end))
                    ->sum('materials_subtotal'),
                2
            );
        }

        $total = round($items->sum('amount'), 2);

        return [
            'total'           => $total,
            'totalShort'      => $this->shortMoney($total),
            'pendingReceipts' => $items->where('receipt', false)->count(),
            'lines'           => $items->count(),
            'byJob'           => $byJob,
            'byCategory'      => [
                'labels' => $byCategory->keys()->all(),
                'data'   => $byCategory->values()->all(),
                'colors' => array_slice(self::PALETTE, 0, max(1, $byCategory->count())),
            ],
            'trend' => ['labels' => $labels, 'data' => $series],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Presentation helpers
    |--------------------------------------------------------------------------
    */

    /** One job, shaped for every card in the Today and Pipeline tabs. */
    private function card(ServiceRequest $sr): array
    {
        $ui = $this->uiStatus($sr);

        return [
            'ref'      => $sr->ref,
            'sr_id'    => $sr->id,
            'client'   => optional($sr->client)->company_name ?? '-',
            'site'     => optional($sr->project)->site_address ?? '-',
            'siteName' => optional($sr->project)->site_name ?? '-',
            'issue'    => $sr->issue_description ?: 'No description was recorded on this job.',
            'domain'   => optional($sr->domain)->domain_name ?? optional($sr->category)->name ?? '-',
            'priority' => ucfirst($sr->priority_level ?? 'Normal'),
            'status'   => $ui,
            'eta'      => optional($sr->eta_at)->format('d M, h:i A'),
            'etaShort' => optional($sr->eta_at)->format('h:i A'),
            'hrsAgo'   => $this->hoursAgo($sr),
            'pill'     => $this->pillFor($ui),
            'accent'   => $this->accentFor($ui),
            'chip'     => $this->chipFor($sr, $ui),
        ];
    }

    private function pillFor(string $ui): string
    {
        return [
            'Pending'     => 'pill-amber',
            'Accepted'    => 'pill-blue',
            'Live'        => 'pill-green',
            'Rescheduled' => 'pill-blue',
            'On Hold'     => 'pill-amber',
            'Rework'      => 'pill-red',
            'Review'      => 'pill-purple',
            'Completed'   => 'pill-green',
        ][$ui] ?? 'pill-gold';
    }

    private function accentFor(string $ui): string
    {
        return [
            'Pending'     => '#d97706',
            'Accepted'    => '#0891b2',
            'Live'        => '#15803d',
            'Rescheduled' => '#2563eb',
            'On Hold'     => '#a16207',
            'Rework'      => '#dc2626',
            'Review'      => '#7c3aed',
            'Completed'   => '#059669',
        ][$ui] ?? '#9a8053';
    }

    /** Short label on the card's right-hand pill. */
    private function chipFor(ServiceRequest $sr, string $ui): string
    {
        if ($ui === 'Pending') {
            return 'Set your ETA';
        }
        if ($sr->eta_at) {
            return 'ETA ' . $sr->eta_at->format('h:i A');
        }

        return $ui;
    }

    /** Same mapping the pipeline uses, so filter links land on the right tab. */
    private function uiStatus(ServiceRequest $sr): string
    {
        $status = strtolower((string) $sr->status);

        $ui = match ($status) {
            'rework'            => 'Rework',
            'on hold'           => 'On Hold',
            'reschedule'        => 'Rescheduled',
            'qc review'         => 'Review',
            'in progress'       => 'Live',
            'completed',
            'pending invoice',
            'invoice submitted' => 'Completed',
            default             => 'Pending',
        };

        if ($ui === 'Pending' && ($sr->accepted_at || $status === 'accepted')) {
            $ui = 'Accepted';
        }

        return $ui;
    }

    private function hoursAgo(ServiceRequest $sr): int
    {
        $created = $sr->dispatched_at ?? $sr->created_at;

        return $created ? (int) $created->diffInHours(now()) : 0;
    }

    private function humanGap(int $minutes): string
    {
        if ($minutes < 60) {
            return $minutes . 'm';
        }

        $h = intdiv($minutes, 60);
        $m = $minutes % 60;

        return $m ? "{$h}h {$m}m" : "{$h}h";
    }

    private function shortMoney(float $value): string
    {
        return $value >= 1000
            ? rtrim(rtrim(number_format($value / 1000, 1, '.', ''), '0'), '.') . 'k'
            : number_format($value, 0, '.', '');
    }

    private function shortSite(?string $site): string
    {
        return trim(explode(',', (string) $site)[0]) ?: '-';
    }

    private function shortName(?string $name): string
    {
        $parts = preg_split('/\s+/', trim((string) $name));

        if (count($parts) < 2) {
            return (string) $name;
        }

        return $parts[0] . ' ' . mb_strtoupper(mb_substr(end($parts), 0, 1)) . '.';
    }

    private function initials(?string $name): string
    {
        return collect(preg_split('/\s+/', trim((string) $name)))
            ->filter()
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->take(2)
            ->implode('');
    }

    private function worker(Request $request): User
    {
        $user = Auth::guard('worker')->user();

        abort_unless($user, 401, 'Not signed in.');

        return $user;
    }

    private function routes(): array
    {
        return [
            'dashboard' => route('worker.dashboard'),
            'pipeline'  => route('worker.pipeline'),
            'logout'    => route('worker.logout'),
        ];
    }
}