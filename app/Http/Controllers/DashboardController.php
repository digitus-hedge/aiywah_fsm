<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Punch;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    /** Field-completion target, matching srSla() in ServiceRequestController. */
    private const SLA_TARGET_HOURS = 48;

    private const CURRENCY = 'AED';

    /**
     * Set this once you tell me the WhatsApp log table. Left null, the WhatsApp
     * card renders its empty state instead of breaking.
     * Expected columns: status ('delivered'|'failed'), trigger, created_at.
     */
    private const WHATSAPP_TABLE = null;

    /** Statuses that mean the request is finished, one way or another. */
    private const CLOSED_STATUSES = ['Completed', 'Rejected', 'Quote Rejected'];

    /** Statuses sitting in someone's queue waiting on a decision. */
    private const AWAITING_ACTION = [
        'Pending', 'Forwarded', 'Quoted', 'Qc Review', 'Pending Invoice', 'Invoice Submitted',
    ];

    /** Colour per status for the distribution bars. */
    private const STATUS_COLORS = [
        'Pending'           => '#2563eb',
        'Approved'          => '#15803d',
        'Forwarded'         => '#7c3aed',
        'Additional'        => '#7c3aed',
        'Rejected'          => '#9ca3af',
        'Assigned'          => '#0891b2',
        'Quoted'            => '#0891b2',
        'In Progress'       => '#9a8053',
        'Quote Rejected'    => '#9ca3af',
        'Qc Review'         => '#b8975e',
        'Rework'            => '#dc2626',
        'Reschedule'        => '#dc2626',
        'Accepted'          => '#15803d',
        'Pending Invoice'   => '#d97706',
        'Invoice Submitted' => '#d97706',
        'Completed'         => '#15803d',
        'On Hold'           => '#64748b',
    ];

    /** Cached once per request so the four sparklines share a single query. */
    private ?Collection $sparkWeek = null;

    /* =====================================================================
     | Page
     ===================================================================== */



private function dashboardView(): string
{
    $role = optional(Auth::user()?->role)->name;

    $map = [
        'Head of Projects' => 'head_projects_dashboard',
        'Front Desk'       => 'front_desk_dashboard',
        'Accounts'         => 'accounts_dashboard',
        'QC'               => 'qc_dashboard',
    ];

    $view = $map[$role] ?? 'dashboard';

    return view()->exists($view) ? $view : 'dashboard';
}
     
    public function index(Request $request)
    {
        [$start, $end]         = $this->resolveRange($request->input('range', 'month'));
        [$prevStart, $prevEnd] = $this->previousRange($start, $end);

        $filters = [
            'range'   => $request->input('range', 'month'),
            'status'  => $request->input('status'),
            'client'  => $request->input('client'),
            'service' => $request->input('service'),
            'period'  => $request->input('period', '6M'),
        ];

        // One load per window; every card below reads from these collections.
        $current  = $this->load($filters, $start, $end);
        $previous = $this->load($filters, $prevStart, $prevEnd);

         return view($this->dashboardView(), [
        // return view('dashboard', [
            'greeting'    => $this->greeting(),
            'greetingSub' => "Here's your complete operations overview",
            'today'       => now()->format('l, d F Y'),
            'panelUrl'    => Route::has('dashboard.panel') ? route('dashboard.panel') : null,

            'filters'        => $filters,
            'rangeOptions'   => ['today' => 'Today', 'month' => 'This month', 'quarter' => 'This quarter'],
            'trendPeriods'   => ['1M' => '1M', '6M' => '6M', '1Y' => '1Y'],
            'statusOptions'  => collect(array_keys(self::STATUS_COLORS))->mapWithKeys(fn ($s) => [$s => $s]),
            'clientOptions'  => Client::orderBy('company_name')->pluck('company_name', 'id'),
            'serviceOptions' => ServiceCategory::orderBy('category_name')->pluck('category_name', 'id'),

            'kpis'            => $this->kpis($current, $previous, $filters, $start, $end, $prevStart, $prevEnd),
            'alertCounts'     => $this->alertCounts($current, $start, $end),
            'statusBreakdown' => $this->statusBreakdown($current),
            'srTrend'         => $this->srTrend($filters),
            'finance'         => $this->finance($current, $filters, $start, $end),
            'qc'              => $this->qc($current),
            'whatsapp'        => $this->whatsapp($start, $end),
            'waTriggers'      => $this->waTriggers($start, $end),
            'technicians'     => $this->technicians($current),
            'clients'         => $this->clients($current),
            'frontDesk'       => $this->frontDesk($current),
            'satisfaction'    => $this->satisfaction($current),
            'ratingBuckets'   => $this->ratingBuckets($current),
        ]);
    }

    /* =====================================================================
     | Loading + filters
     ===================================================================== */

    /** Every service request in the window, with the relations the cards need. */
    private function load(array $filters, Carbon $start, Carbon $end): Collection
    {
        return $this->query($filters)
            ->whereBetween('service_requests.created_at', [$start, $end])
            ->with([
                'client:id,company_name',
                'project:id,project_name,site_name,warranty_end_date',
                'assignedUser:id,name',
                'creator:id,name',
                'category:id,category_name',
                'punches.items',
            ])
            ->get();
    }

    private function query(array $filters): Builder
    {
        return ServiceRequest::query()
            ->when($filters['status']  ?? null, fn ($q, $v) => $q->where('service_requests.status', $v))
            ->when($filters['client']  ?? null, fn ($q, $v) => $q->where('service_requests.client_id', $v))
            ->when($filters['service'] ?? null, fn ($q, $v) => $q->where('service_requests.service_type_id', $v));
    }

    private function resolveRange(string $range): array
    {
        return match ($range) {
            'today'   => [today()->startOfDay(), today()->endOfDay()],
            'quarter' => [now()->startOfQuarter(), now()->endOfQuarter()],
            default   => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }

    /** The equally sized window immediately before the current one. */
    private function previousRange(Carbon $start, Carbon $end): array
    {
        $seconds = $start->diffInSeconds($end);

        return [$start->copy()->subSeconds($seconds + 1), $start->copy()->subSecond()];
    }

    private function greeting(): string
    {
        $hour = now()->hour;
        $part = $hour < 12 ? 'morning' : ($hour < 17 ? 'afternoon' : 'evening');
        $name = auth()->user()?->name;

        return $name ? "Good {$part}, ".Str::before($name, ' ').'!' : "Good {$part}!";
    }

    /* =====================================================================
     | Domain rules
     ===================================================================== */

    /**
     * Mirrors ServiceRequestController: an explicit warranty_scope wins,
     * otherwise fall back to the project's warranty end date.
     */
    private function isInWarranty(ServiceRequest $sr): bool
    {
        if (! empty($sr->warranty_scope)) {
            return $sr->warranty_scope !== 'oow';
        }

        $end = $sr->project?->warranty_end_date;

        return $end && $end->copy()->endOfDay()->isFuture();
    }

    private function isOpen(ServiceRequest $sr): bool
    {
        return ! in_array($sr->status, self::CLOSED_STATUSES, true);
    }

    /** When the job actually finished: last punch-out, else the QC stamp. */
    private function completedAt(ServiceRequest $sr): ?Carbon
    {
        $punchOut = $sr->punches
            ->filter(fn ($p) => $p->punch_out_at)
            ->sortByDesc('punch_out_at')
            ->first()?->punch_out_at;

        return $punchOut ?? $sr->qc_reviewed_at;
    }

    private function turnaroundHours(ServiceRequest $sr): ?float
    {
        $done = $this->completedAt($sr);

        if (! $done || ! $sr->created_at) {
            return null;
        }

        return round(abs($sr->created_at->diffInMinutes($done)) / 60, 1);
    }

    private function metSla(ServiceRequest $sr): ?bool
    {
        $hours = $this->turnaroundHours($sr);

        return is_null($hours) ? null : $hours <= self::SLA_TARGET_HOURS;
    }

    /** Field spend on one punch: grand total if set, else items plus labour. */
    private function punchExpense(Punch $punch): float
    {
        if ((float) $punch->grand_total > 0) {
            return (float) $punch->grand_total;
        }

        $items = collect($punch->items)
            ->sum(fn ($item) => (float) ($item->line_total ?? ($item->qty * $item->rate)));

        return $items + (float) $punch->labour_charge;
    }

    private function expenseFor(ServiceRequest $sr): float
    {
        return $sr->punches->sum(fn ($punch) => $this->punchExpense($punch));
    }

    private function slaCompliance(Collection $srs): ?float
    {
        $judged = $srs->map(fn ($sr) => $this->metSla($sr))->filter(fn ($v) => ! is_null($v));

        if ($judged->isEmpty()) {
            return null;
        }

        return round($judged->filter()->count() / $judged->count() * 100, 1);
    }

    /* =====================================================================
     | KPI row
     ===================================================================== */

    private function kpis(
        Collection $current,
        Collection $previous,
        array $filters,
        Carbon $start,
        Carbon $end,
        Carbon $prevStart,
        Carbon $prevEnd
    ): array {
        $total     = $current->count();
        $prevTotal = $previous->count();

        $open       = $current->filter(fn ($sr) => $this->isOpen($sr));
        $inProgress = $current->where('status', 'In Progress')->count();

        $sla     = $this->slaCompliance($current)  ?? 0;
        $prevSla = $this->slaCompliance($previous) ?? 0;

        $invoiced     = $this->invoicedTotal($filters, $start, $end);
        $prevInvoiced = $this->invoicedTotal($filters, $prevStart, $prevEnd);

        $turnaround     = $this->avgTurnaround($current);
        $prevTurnaround = $this->avgTurnaround($previous);

        $breaches  = $current->filter(fn ($sr) => $this->metSla($sr) === false)->count();
        $oowClosed = $current->where('status', 'Completed')
            ->reject(fn ($sr) => $this->isInWarranty($sr))
            ->count();

        return [
            'total' => [
                'value'      => $total,
                'sub'        => "vs {$prevTotal} last period",
                'delta'      => $this->percentLabel($total, $prevTotal),
                'delta_tone' => $this->tone($total, $prevTotal),
                'spark'      => $this->spark($filters, fn (Collection $srs) => $srs->count()),
            ],
            'open' => [
                'value'      => $open->count(),
                'sub'        => $inProgress.' in progress · '.max(0, $open->count() - $inProgress).' awaiting action',
                'delta'      => 'Live',
                'delta_tone' => 'dn',
                'spark'      => $this->spark($filters, fn (Collection $srs) => $srs->filter(fn ($sr) => $this->isOpen($sr))->count()),
            ],
            'sla' => [
                'value'      => $sla ? $sla.'%' : '—',
                'sub'        => $breaches.' '.Str::plural('breach', $breaches).' over '.self::SLA_TARGET_HOURS.'h',
                'delta'      => $prevSla ? $this->pointLabel($sla, $prevSla) : null,
                'delta_tone' => $this->tone($sla, $prevSla),
                'spark'      => $this->spark($filters, fn (Collection $srs) => $this->slaCompliance($srs) ?? 0),
            ],
            'invoiced' => [
                'value'      => $this->money($invoiced),
                'sub'        => $oowClosed.' out-of-warranty SRs closed',
                'delta'      => $this->percentLabel($invoiced, $prevInvoiced),
                'delta_tone' => $this->tone($invoiced, $prevInvoiced),
                'spark'      => [],   // invoicing is not a per-day series
            ],
            'turnaround' => [
                'value'      => $turnaround ? $turnaround.'h' : '—',
                'sub'        => 'Logged to punch-out',
                'delta'      => $turnaround && $prevTurnaround ? round($turnaround - $prevTurnaround, 1).'h' : null,
                // faster is better, so the comparison is deliberately inverted
                'delta_tone' => $this->tone($prevTurnaround ?? 0, $turnaround ?? 0),
                'spark'      => $this->spark($filters, fn (Collection $srs) => $this->avgTurnaround($srs) ?? 0),
            ],
        ];
    }

    private function avgTurnaround(Collection $srs): ?float
    {
        $hours = $srs->map(fn ($sr) => $this->turnaroundHours($sr))->filter();

        return $hours->isEmpty() ? null : round($hours->avg(), 1);
    }

    /**
     * Seven daily points ending today. The week is loaded once and reused
     * across all four sparklines.
     */
    private function spark(array $filters, callable $measure): array
    {
        $this->sparkWeek ??= $this->load($filters, now()->subDays(6)->startOfDay(), now()->endOfDay());

        $week = $this->sparkWeek;

        return collect(range(6, 0))
            ->map(function ($daysAgo) use ($week, $measure) {
                $day = now()->subDays($daysAgo)->toDateString();

                return (float) $measure(
                    $week->filter(fn ($sr) => $sr->created_at?->toDateString() === $day)
                );
            })
            ->all();
    }

    /* =====================================================================
     | Alerts
     ===================================================================== */

    /**
     * Counts behind the five alert chips. The blade renders all five, so this
     * returns raw numbers rather than a filtered list.
     */
    private function alertCounts(Collection $srs, Carbon $start, Carbon $end): array
    {
        return [
            'breaches' => $srs->filter(fn ($sr) => $this->metSla($sr) === false)->count(),

            'pending'  => $srs->whereIn('status', self::AWAITING_ACTION)->count(),

            'stalled'  => $srs
                ->filter(fn ($sr) => $this->isOpen($sr) && $sr->updated_at?->lt(now()->subDay()))
                ->count(),

            'wa_failures' => $this->whatsapp($start, $end)['failed'] ?? 0,

            'on_site' => $srs->where('status', 'In Progress')
                ->filter(fn ($sr) => $sr->punches->contains(fn ($p) => $p->punch_in_at && ! $p->punch_out_at))
                ->count(),
        ];
    }

    /* =====================================================================
     | Cards
     ===================================================================== */

    private function statusBreakdown(Collection $srs): array
    {
        return collect(self::STATUS_COLORS)
            ->map(fn ($color, $status) => [
                'label' => $status,
                'count' => $srs->where('status', $status)->count(),
                'color' => $color,
            ])
            ->filter(fn ($row) => $row['count'] > 0)
            ->sortByDesc('count')
            ->values()
            ->all();
    }

    private function srTrend(array $filters): array
    {
        $buckets = $this->buckets($filters['period'] ?? '6M');
        $from    = $buckets[0]['start'];
        $to      = end($buckets)['end'];

        $srs = $this->query($filters)
            ->whereBetween('service_requests.created_at', [$from, $to])
            ->with('project:id,warranty_end_date')
            ->get(['id', 'created_at', 'warranty_scope', 'project_id']);

        $labels = $inWarranty = $outWarranty = [];

        foreach ($buckets as $bucket) {
            $slice = $srs->filter(fn ($sr) => $sr->created_at?->between($bucket['start'], $bucket['end']));

            $labels[]      = $bucket['label'];
            $inWarranty[]  = $slice->filter(fn ($sr) => $this->isInWarranty($sr))->count();
            $outWarranty[] = $slice->reject(fn ($sr) => $this->isInWarranty($sr))->count();
        }

        $iwNow  = end($inWarranty)  ?: 0;
        $owNow  = end($outWarranty) ?: 0;
        $iwPrev = $inWarranty[count($inWarranty) - 2]   ?? 0;
        $owPrev = $outWarranty[count($outWarranty) - 2] ?? 0;

        return [
            'labels'              => $labels,
            'in_warranty'         => $inWarranty,
            'out_warranty'        => $outWarranty,
            'in_warranty_total'   => $iwNow,
            'out_warranty_total'  => $owNow,
            'in_warranty_change'  => $this->percentChange($iwNow, $iwPrev),
            'out_warranty_change' => $this->percentChange($owNow, $owPrev),
        ];
    }

    private function finance(Collection $srs, array $filters, Carbon $start, Carbon $end): array
    {
        $invoiced = $this->invoicedTotal($filters, $start, $end);
        $expense  = $srs->sum(fn ($sr) => $this->expenseFor($sr));

        $buckets = $this->buckets($filters['period'] ?? '6M');
        $from    = $buckets[0]['start'];
        $to      = end($buckets)['end'];

        $invoices = $this->query($filters)
            ->whereBetween('service_requests.invoice_submitted_at', [$from, $to])
            ->get(['id', 'invoice_submitted_at', 'invoice_total']);

        $punches = Punch::query()
            ->whereBetween('punch_out_at', [$from, $to])
            ->when(
                ($filters['client'] ?? null) || ($filters['service'] ?? null),
                fn ($q) => $q->whereHas('serviceRequest', fn ($sr) => $sr
                    ->when($filters['client']  ?? null, fn ($s, $v) => $s->where('client_id', $v))
                    ->when($filters['service'] ?? null, fn ($s, $v) => $s->where('service_type_id', $v)))
            )
            ->with('items')
            ->get();

        $labels = $invoicedSeries = $expenseSeries = [];

        foreach ($buckets as $bucket) {
            $labels[] = $bucket['label'];

            $invoicedSeries[] = round($invoices
                ->filter(fn ($sr) => $sr->invoice_submitted_at?->between($bucket['start'], $bucket['end']))
                ->sum('invoice_total'), 2);

            $expenseSeries[] = round($punches
                ->filter(fn ($p) => $p->punch_out_at?->between($bucket['start'], $bucket['end']))
                ->sum(fn ($p) => $this->punchExpense($p)), 2);
        }

        return [
            'currency'           => self::CURRENCY,
            'invoiced_formatted' => $this->money($invoiced),
            'expense_formatted'  => $this->money($expense),
            'net_formatted'      => $this->money($invoiced - $expense),
            'recovery_rate'      => $invoiced > 0 ? round(($invoiced - $expense) / $invoiced * 100, 1) : null,
            'labels'             => $labels,
            'invoiced_series'    => $invoicedSeries,
            'expense_series'     => $expenseSeries,
        ];
    }

    private function qc(Collection $srs): array
    {
        $reviewed  = $srs->filter(fn ($sr) => $sr->qc_reviewed_at);
        $firstPass = $reviewed->filter(fn ($sr) => empty($sr->rework_notes));

        $rework     = $srs->filter(fn ($sr) => ! empty($sr->rework_notes))->count();
        $reworkOpen = $srs->where('status', 'Rework')->count();

        $breaches = $srs->filter(fn ($sr) => $this->metSla($sr) === false);

        return [
            'sla_compliance'  => $this->slaCompliance($srs),
            'first_pass_rate' => $reviewed->isEmpty() ? null : round($firstPass->count() / $reviewed->count() * 100),
            'first_pass_sub'  => $firstPass->count().' of '.$reviewed->count().' reviewed',
            'rework_count'    => $rework,
            'rework_sub'      => $reworkOpen.' currently open',
            'sla_breaches'    => $breaches->count(),
            'sla_breach_sub'  => $breaches->isEmpty()
                ? 'All within target'
                : $breaches->pluck('priority_level')->filter()->unique()->take(2)->implode(', ').' priority',
        ];
    }

    private function technicians(Collection $srs): array
    {
        return $srs
            ->filter(fn ($sr) => $sr->assigned_user_id && $sr->assignedUser)
            ->groupBy('assigned_user_id')
            ->map(function (Collection $jobs) {
                $tech = $jobs->first()->assignedUser;

                $minutes = $jobs->sum(fn ($sr) => $sr->punches->sum(
                    fn ($p) => $p->punch_in_at && $p->punch_out_at
                        ? abs($p->punch_in_at->diffInMinutes($p->punch_out_at))
                        : 0
                ));

                $rated   = $jobs->filter(fn ($sr) => $sr->performance_score);
                $punched = $jobs->filter(fn ($sr) => $sr->punches->contains(fn ($p) => $p->punch_in_at))->count();

                return [
                    'id'                 => $tech->id,
                    'name'               => $tech->name,
                    'initials'           => $this->initials($tech->name),
                    'department'         => $jobs->first()->category?->category_name ?? '—',
                    'jobs'               => $jobs->count(),
                    'hours'              => round($minutes / 60),
                    'rating'             => $rated->isEmpty() ? 0 : round($rated->avg('performance_score'), 1),
                    'rework'             => $jobs->filter(fn ($sr) => ! empty($sr->rework_notes))->count(),
                    'punch_rate'         => $jobs->count() ? round($punched / $jobs->count() * 100) : 0,
                    'expenses_formatted' => self::CURRENCY.' '.number_format($jobs->sum(fn ($sr) => $this->expenseFor($sr))),
                ];
            })
            ->sortByDesc('jobs')
            ->values()
            ->all();
    }

    private function clients(Collection $srs): array
    {
        return $srs
            ->filter(fn ($sr) => $sr->client)
            ->groupBy('client_id')
            ->map(fn (Collection $group) => [
                'id'           => $group->first()->client->id,
                'name'         => $group->first()->client->company_name,
                'short_name'   => Str::before($group->first()->client->company_name, ' '),
                'srs'          => $group->count(),
                'in_warranty'  => $group->filter(fn ($sr) => $this->isInWarranty($sr))->count(),
                'out_warranty' => $group->reject(fn ($sr) => $this->isInWarranty($sr))->count(),
            ])
            ->sortByDesc('srs')
            ->take(6)
            ->values()
            ->all();
    }

    private function frontDesk(Collection $srs): array
    {
        return $srs
            ->filter(fn ($sr) => $sr->creator)
            ->groupBy('created_by')
            ->map(function (Collection $group) {
                $user      = $group->first()->creator;
                $completed = $group->where('status', 'Completed')->count();

                return [
                    'id'        => $user->id,
                    'name'      => $user->name,
                    'initials'  => $this->initials($user->name),
                    'srs'       => $group->count(),
                    'completed' => $completed,
                    'pending'   => $group->count() - $completed,
                ];
            })
            ->sortByDesc('srs')
            ->values()
            ->all();
    }

    private function satisfaction(Collection $srs): array
    {
        $rated     = $srs->filter(fn ($sr) => $sr->performance_score);
        $completed = $srs->where('status', 'Completed')->count();
        $lowest    = $rated->where('performance_score', '<=', 2)->sortBy('performance_score')->first();

        return [
            'average'       => $rated->isEmpty() ? null : round($rated->avg('performance_score'), 1),
            'responses'     => $rated->count(),
            'response_rate' => $completed > 0 ? round($rated->count() / $completed * 100) : null,
            'completed_srs' => $completed,
            'flagged'       => $lowest
                ? $lowest->code." rated {$lowest->performance_score} stars — flagged for review"
                : null,
        ];
    }

    private function ratingBuckets(Collection $srs): array
    {
        $rated = $srs->filter(fn ($sr) => $sr->performance_score);

        if ($rated->isEmpty()) {
            return [];
        }

        return collect([5, 4, 3, 2, 1])
            ->map(fn ($stars) => [
                'stars' => $stars,
                'count' => $rated->where('performance_score', $stars)->count(),
            ])
            ->all();
    }

    /* =====================================================================
     | WhatsApp — activates once WHATSAPP_TABLE is set
     ===================================================================== */

    private function whatsapp(Carbon $start, Carbon $end): array
    {
        if (! self::WHATSAPP_TABLE || ! Schema::hasTable(self::WHATSAPP_TABLE)) {
            return ['delivered' => 0, 'failed' => 0, 'total' => 0, 'delivery_rate' => null];
        }

        $counts = DB::table(self::WHATSAPP_TABLE)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $failed    = (int) ($counts['failed'] ?? 0);
        $delivered = (int) $counts->sum() - $failed;
        $total     = $delivered + $failed;

        return [
            'delivered'     => $delivered,
            'failed'        => $failed,
            'total'         => $total,
            'delivery_rate' => $total > 0 ? round($delivered / $total * 100, 1) : null,
        ];
    }

    private function waTriggers(Carbon $start, Carbon $end): array
    {
        if (! self::WHATSAPP_TABLE || ! Schema::hasTable(self::WHATSAPP_TABLE)) {
            return [];
        }

        // Rename `trigger` below to whatever your log calls the template/event.
        return DB::table(self::WHATSAPP_TABLE)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw("`trigger` as label, COUNT(*) as sent, SUM(status = 'failed') as failed")
            ->groupBy('trigger')
            ->orderByDesc('sent')
            ->get()
            ->map(fn ($row) => [
                'label'  => $row->label,
                'sent'   => (int) $row->sent,
                'failed' => (int) $row->failed,
            ])
            ->all();
    }

    /* =====================================================================
     | Slide-in detail panel
     ===================================================================== */

    public function panel(Request $request): JsonResponse
    {
        [$start, $end] = $this->resolveRange($request->input('range', 'month'));

        $filters = [
            'status'  => $request->input('status'),
            'client'  => $request->input('client'),
            'service' => $request->input('service'),
        ];

        $type = $request->input('type');
        $id   = $request->input('id');

        [$title, $icon, $section] = match ($type) {
            'active-srs'      => ['Active open SRs', 'bi-activity', 'Still in the workflow'],
            'sla-breach'      => ['SLA breaches', 'bi-exclamation-triangle', 'Over '.self::SLA_TARGET_HOURS.' hours'],
            'pending-actions' => ['Pending actions', 'bi-hourglass-split', 'Waiting on a decision'],
            'slow-srs'        => ['Stalled requests', 'bi-clock-history', 'No movement in 24h'],
            'invoices'        => ['Invoiced requests', 'bi-receipt', 'Billed this period'],
            'month-srs'       => ['All SRs this period', 'bi-ticket-detailed', 'Every request logged'],
            'technician'      => ['Technician detail', 'bi-person-badge', 'Recent assignments'],
            'client'          => ['Client detail', 'bi-buildings', 'Service request history'],
            'wa-failures'     => ['WhatsApp failures', 'bi-whatsapp', 'Undelivered messages'],
            default           => ['Details', 'bi-list', null],
        };

        $all = $this->load($filters, $start, $end);

        $srs = match ($type) {
            'active-srs'      => $all->filter(fn ($sr) => $this->isOpen($sr)),
            'sla-breach'      => $all->filter(fn ($sr) => $this->metSla($sr) === false),
            'pending-actions' => $all->whereIn('status', self::AWAITING_ACTION),
            'slow-srs'        => $all->filter(fn ($sr) => $this->isOpen($sr) && $sr->updated_at?->lt(now()->subDay())),
            'invoices'        => $all->filter(fn ($sr) => $sr->invoice_submitted_at),
            'technician'      => $all->where('assigned_user_id', $id),
            'client'          => $all->where('client_id', $id),
            default           => $all,
        };

        if ($type === 'technician' && $id) {
            $title = $srs->first()?->assignedUser?->name ?? $title;
        }

        if ($type === 'client' && $id) {
            $title = $srs->first()?->client?->company_name ?? $title;
        }

        $items = $srs
            ->sortByDesc('created_at')
            ->take(25)
            ->map(fn ($sr) => [
                'reference' => $sr->code,
                'badge'     => $sr->status,
                'color'     => self::STATUS_COLORS[$sr->status] ?? '#9a8053',
                'title'     => $sr->client?->company_name ?? '—',
                'meta'      => collect([
                    $sr->category?->category_name,
                    $this->isInWarranty($sr) ? 'In warranty' : 'Out of warranty',
                    $sr->project?->site_name,
                    $sr->assignedUser?->name,
                ])->filter()->implode(' · '),
            ])
            ->values();

        return response()->json([
            'title'    => $title,
            'subtitle' => $items->count().' '.Str::plural('record', $items->count()),
            'icon'     => $icon,
            'section'  => $section,
            'items'    => $items->all(),
        ]);
    }

    /* =====================================================================
     | Helpers
     ===================================================================== */

    private function invoicedTotal(array $filters, Carbon $start, Carbon $end): float
    {
        return (float) $this->query($filters)
            ->whereBetween('service_requests.invoice_submitted_at', [$start, $end])
            ->sum('invoice_total');
    }

    /** Trend buckets: weeks for 1M, months otherwise. */
    private function buckets(string $period): array
    {
        if ($period === '1M') {
            return collect(range(3, 0))
                ->map(function ($weeksAgo) {
                    $start = now()->subWeeks($weeksAgo)->startOfWeek();

                    return ['label' => $start->format('d M'), 'start' => $start, 'end' => $start->copy()->endOfWeek()];
                })
                ->all();
        }

        $months = $period === '1Y' ? 12 : 6;

        return collect(range($months - 1, 0))
            ->map(function ($monthsAgo) {
                $start = now()->subMonths($monthsAgo)->startOfMonth();

                return ['label' => $start->format('M'), 'start' => $start, 'end' => $start->copy()->endOfMonth()];
            })
            ->all();
    }

    private function percentChange(float $current, float $previous): ?float
    {
        return $previous == 0 ? null : round(($current - $previous) / $previous * 100, 1);
    }

    private function percentLabel(float $current, float $previous): ?string
    {
        $change = $this->percentChange($current, $previous);

        return is_null($change) ? null : ($change >= 0 ? '+' : '').$change.'%';
    }

    /** For values already expressed as percentages, the delta is in points. */
    private function pointLabel(float $current, float $previous): string
    {
        $diff = round($current - $previous, 1);

        return ($diff >= 0 ? '+' : '').$diff.'%';
    }

    private function tone(float $current, float $previous): string
    {
        if ($current == $previous) {
            return 'dn';
        }

        return $current > $previous ? 'du' : 'dd';
    }

    private function money(float $amount): string
    {
        if (abs($amount) >= 1000) {
            return self::CURRENCY.' '.round($amount / 1000, 1).'k';
        }

        return self::CURRENCY.' '.number_format($amount);
    }

    private function initials(?string $name): string
    {
        if (! $name) {
            return '—';
        }

        $parts = preg_split('/\s+/', trim($name)) ?: [];

        return mb_strtoupper(
            mb_substr($parts[0] ?? '', 0, 1).(isset($parts[1]) ? mb_substr($parts[1], 0, 1) : '')
        );
    }
}