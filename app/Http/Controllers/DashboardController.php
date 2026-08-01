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
    private const CURRENCY = 'AED';

    /**
     * Per-stage SLA targets in hours. A breach is one stage running long,
     * not the request as a whole — so a single SR can breach more than once.
     *
     * Tune these to your actual commitments; the numbers below are
     * placeholders that produce sensible output, nothing more.
     */
        private const SLA_TARGET_HOURS = 48;
    private const STAGE_SLA_HOURS = [
        'approval' => 24,   // created_at      → hop_approved_at
        'dispatch' => 12,   // hop_approved_at → dispatched_at
        'accept'   => 6,    // dispatched_at   → accepted_at
        'closeout' => 24,   // qc_reviewed_at  → completed
    ];

    private const STAGE_LABELS = [
        'approval' => 'Pending → Approval',
        'dispatch' => 'Approval → Dispatch',
        'accept'   => 'Dispatch → Accept',
        'closeout' => 'QC → Completed',
    ];

    /** A breach on one of these priorities is flagged critical. */
    private const CRITICAL_PRIORITIES = ['High', 'Critical', 'Urgent'];

    /** Field-time target per service request, in hours. */
    private const FIELD_HOURS_TARGET = 8;

    /**
     * Set this once you tell me the WhatsApp log table. Left null, the WhatsApp
     * card renders its empty state instead of breaking.
     * Expected columns: status ('delivered'|'failed'), trigger, created_at.
     */
    private const WHATSAPP_TABLE = null;

    /** Statuses that mean the request is finished, one way or another. */
    private const CLOSED_STATUSES = ['Completed', 'Rejected', 'Quote Rejected'];

    /** Statuses that mean the request was turned down. */
    private const REJECTED_STATUSES = ['Rejected', 'Quote Rejected'];

    /** Statuses sitting in someone's queue waiting on a decision. */
    private const AWAITING_ACTION = [
        'Pending',
        'Forwarded',
        'Quoted',
        'Qc Review',
        'Pending Invoice',
        'Invoice Submitted',
    ];

    /** Punch states that mean the work is with QC. */
    private const QC_PUNCH_STATUSES = ['submitted', 'qc_review'];

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


     private const STATUS_GROUPS = [
        'Intake'         => ['Pending', 'Approved', 'Forwarded', 'Additional', 'Quoted', 'Accepted'],
        'Field / Active' => ['Assigned', 'In Progress', 'Qc Review', 'Rework', 'Reschedule'],
        'Completed'      => ['Pending Invoice', 'Invoice Submitted', 'Completed'],
        'Issues'         => ['Rejected', 'Quote Rejected', 'On Hold'],
    ];

    private const STATUS_LEGEND = [
        'Intake'         => '#2563eb',
        'Field / Active' => '#9a8053',
        'Completed'      => '#15803d',
        'Issues'         => '#dc2626',
    ];

    /** Cached once per request so the sparklines share a single query. */
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

   
    /**
     * Base filtered query. Every card-level SR list starts here.
     */
    private function scoped(array $filters, $start, $end)
    {
        $q = ServiceRequest::query()
            ->select(
                'service_requests.*',
                'sc.category_name',
                'u.name as assigned_name'
            )
            ->leftJoin('service_categories as sc', 'sc.id', '=', 'service_requests.service_type_id')
            ->leftJoin('users as u',               'u.id',  '=', 'service_requests.assigned_user_id')
            ->leftJoin('projects as p',            'p.id',  '=', 'service_requests.project_id')
            ->with('client:id,company_name')
            ->whereBetween('service_requests.created_at', [$start, $end]);

        if (!empty($filters['status'])) {
            $q->where('service_requests.status', $filters['status']);
        }
        if (!empty($filters['client'])) {
            $q->where('service_requests.client_id', $filters['client']);
        }
        if (!empty($filters['service'])) {
            $q->where('service_requests.service_type_id', $filters['service']);
        }

        return $q->orderByDesc('service_requests.id');
    }



    /**
     * Base filtered query used by every card-level SR list.
     * Mirrors the joins in the existing helpers.
     */



    private function qcSrs(array $filters, $start, $end)
    {
        $f = $filters;
        unset($f['status']);
        return $this->scoped($f, $start, $end)
            ->where('service_requests.status', 'Qc Review')
            ->get();
    }

    private function approvedSrs(array $filters, $start, $end)
    {
        $f = $filters;
        unset($f['status']);
        return $this->scoped($f, $start, $end)
            ->where('service_requests.status', 'Approved')
            ->get();
    }


    private function reworkSrs(array $filters, $start, $end)
    {
        $f = $filters;
        unset($f['status']);
        return $this->scoped($f, $start, $end)
            ->where('service_requests.status', 'Rework')
            ->get();
    }


    private function srItems($rows): array
    {
        return $rows->map(function ($sr) {
            $meta = array_filter([
                $sr->category_name,
                $sr->site_location,
                $sr->assigned_name ?: 'Unassigned',
            ]);

            return [
                'id'     => $sr->sr_number
                    ?: 'SR-' . ($sr->created_at?->format('Y') ?? date('Y'))
                    . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT),
                'client' => $sr->client->company_name ?? '—',
                'meta'   => implode(' · ', $meta),
                'badge'  => $sr->status ?: 'Unknown',
                'bc'     => self::STATUS_COLORS[$sr->status] ?? '#64748b',
            ];
        })->values()->all();
    }


    public function index(Request $request)
    {
        $filters = [
    'range'   => $request->input('range', 'today'),   // ← changed
    'from'    => $request->input('from'),
    'to'      => $request->input('to'),
    'status'  => $request->input('status'),
    'client'  => $request->input('client'),
    'service' => $request->input('service'),
    'period'  => $request->input('period', '6M'),
];

        [$start, $end]         = $this->resolveRange($filters);
        [$prevStart, $prevEnd] = $this->previousRange($start, $end);

        // One load per window; the collection-based cards read from these.
        $current  = $this->load($filters, $start, $end);
        $previous = $this->load($filters, $prevStart, $prevEnd);

        // ---- Card-level SR lists (now filter-aware) ----
        $activeSRs   = $this->activeSrs($filters, $start, $end);
        $qcSRs       = $this->qcSrs($filters, $start, $end);
        $approvedSRs = $this->approvedSrs($filters, $start, $end);
        $reworkSRs   = $this->reworkSrs($filters, $start, $end);
        $allSRs      = $this->allSrs($filters, $start, $end);
        $inquirySRs  = $this->inquirySrs($filters, $start, $end);
        $slaBreaches = $this->slaBreachItems($filters, $start, $end);

        // ---- Client Satisfaction ----
        $satisfaction2  = $this->satisfaction2($filters, $start, $end);
        $ratedRows      = $satisfaction2['rows'];
        unset($satisfaction2['rows']);          // don't ship Eloquent models to the view

        $ratingBuckets2 = $this->ratingBuckets2($ratedRows);
        $feedbackItems2 = $this->feedbackItems2($ratedRows, $ratingBuckets2, $satisfaction2);

        $feedbackPanel = [
            'title' => 'Client Feedback',
            'icon'  => 'bi-star-half',
            'sub'   => sprintf(
                '%d responses · %s★ average · %d%% response rate · %s',
                $satisfaction2['responses'],
                number_format($satisfaction2['avg'], 1),
                $satisfaction2['response_rate'],
                $this->rangeLabel($filters)
            ),
            'items' => $feedbackItems2,
        ];

        return view($this->dashboardView(), [
            'greeting'    => $this->greeting(),
            'greetingSub' => "Here's your complete operations overview",
            'today'       => now()->format('l, d F Y'),
            'panelUrl'    => Route::has('dashboard.panel') ? route('dashboard.panel') : null,

            'filters'        => $filters,
            'rangeLabel'     => $this->rangeLabel($filters),
            'rangeOptions'   => ['today' => 'Today', 'month' => 'This month', 'quarter' => 'This quarter'],
            'trendPeriods'   => ['1M' => '1M', '6M' => '6M', '1Y' => '1Y'],
            'statusOptions'  => collect(array_keys(self::STATUS_COLORS))->mapWithKeys(fn($s) => [$s => $s]),
            'clientOptions'  => Client::orderBy('company_name')->pluck('company_name', 'id'),
            'serviceOptions' => ServiceCategory::orderBy('category_name')->pluck('category_name', 'id'),

            'kpis'            => $this->kpis($current, $previous, $filters, $start, $end, $prevStart, $prevEnd),
            'alertCounts'     => $this->alertCounts($current, $start, $end),
            'statusBreakdown' => $this->statusBreakdown($current),
            'srTrend'         => $this->srTrend($filters),
            'srTrend2'        => $this->srTrend2($filters),
            'finance'         => $this->finance($current, $filters, $start, $end),
            'qc'              => $this->qc($current),
            'whatsapp'        => $this->whatsapp($start, $end),
            'waTriggers'      => $this->waTriggers($start, $end),
            'technicians'     => $this->technicians($current),
            'clients'         => $this->clients($current),
            'frontDesk'       => $this->frontDesk($current),
            'satisfaction'    => $this->satisfaction($current),
            'ratingBuckets'   => $this->ratingBuckets($current),
            'clientReviews'   => $this->clientReviews($current),

            'satisfaction2'   => $satisfaction2,
            'ratingBuckets2'  => $ratingBuckets2,
            'feedbackItems2'  => $feedbackItems2,
            'feedbackPanel'   => $feedbackPanel,

            'activeCount'     => $activeSRs->count(),
            'activeItems'     => $this->srItems($activeSRs),

            'qcPendingCount'  => $qcSRs->count(),
            'qcItems'         => $this->srItems($qcSRs),

            'dispatchCount'   => $approvedSRs->count(),
            'dispatchItems'   => $this->srItems($approvedSRs),

            'reworkCount'     => $reworkSRs->count(),
            'reworkItems'     => $this->srItems($reworkSRs),

            'allCount'        => $allSRs->count(),
            'allItems'        => $this->srItems($allSRs),
            'statusLegend'    => self::STATUS_LEGEND,

            'inquiryCount'    => $inquirySRs->count(),
            'inquiryItems'    => $this->srItems($inquirySRs),

            'slaBreachCount'  => count($slaBreaches),
            'slaBreachItems'  => $slaBreaches,

            'workforce'       => $this->workforce($filters, $start, $end),
        ]);
    }


    private function allSrs(array $filters, $start, $end)
    {
        // This one DOES respect the status dropdown
        return $this->scoped($filters, $start, $end)->get();
    }

    /**
     * Human-readable label for the active date filter.
     * Used in card headers and panel subtitles.
     */
    private function rangeLabel(array $filters): string
    {
        if (($filters['range'] ?? null) === 'custom'
            && !empty($filters['from']) && !empty($filters['to'])
        ) {
            return Carbon::parse($filters['from'])->format('d M')
                . ' – ' . Carbon::parse($filters['to'])->format('d M Y');
        }

        return match ($filters['range'] ?? 'today') {
            'month'   => 'this month',
            'quarter' => 'this quarter',
            default   => 'today',
        };
    }
    /**
     * Client Satisfaction headline stats.
     * Reads from the already-loaded $current collection.
     */
    private function satisfaction2(array $filters, $start, $end): array
    {
        // Ratings land in the window based on when feedback was submitted,
        // not when the SR was created.
        $rated = ServiceRequest::query()
            ->with(['assignedUser', 'domain', 'category', 'client'])
            ->whereNotNull('performance_score')
            ->whereBetween('feedback_submitted_at', [$start, $end])
            ->when(!empty($filters['status']),  fn($q) => $q->where('status', $filters['status']))
            ->when(!empty($filters['client']),  fn($q) => $q->where('client_id', $filters['client']))
            ->when(!empty($filters['service']), fn($q) => $q->where('service_type_id', $filters['service']))
            ->get();

        // Denominator: completed SRs in the window (by created_at, like the rest
        // of the dashboard). Status dropdown is ignored here — "completed" is fixed.
        $completedCount = ServiceRequest::query()
            ->where('status', 'Completed')
            ->whereBetween('created_at', [$start, $end])
            ->when(!empty($filters['client']),  fn($q) => $q->where('client_id', $filters['client']))
            ->when(!empty($filters['service']), fn($q) => $q->where('service_type_id', $filters['service']))
            ->count();

        $fbCount  = $rated->count();
        $avgScore = $fbCount ? round($rated->avg('performance_score'), 1) : 0.0;
        $respRate = $completedCount ? (int) round($fbCount / $completedCount * 100) : 0;

        $flagged = $rated->filter(fn($sr) => $sr->performance_score <= 2)
            ->sortByDesc('feedback_submitted_at')
            ->first();

        return [
            'avg'           => $avgScore,
            'avg_display'   => $fbCount ? number_format($avgScore, 1) : '—',
            'stars'         => $this->starIcons($avgScore),
            'responses'     => $fbCount,
            'response_rate' => min(100, $respRate),   // clamp — feedback can outpace the window
            'completed'     => $completedCount,
            'rows'          => $rated,                // consumed by the two methods below
            'flagged'       => $flagged ? [
                'id'    => $flagged->id,
                'code'  => $this->srCode($flagged),
                'score' => (int) $flagged->performance_score,
            ] : null,
        ];
    }

    /**
     * Rating histogram, 5★ first. 'max' scales the bar widths.
     */
    private function ratingBuckets2(Collection $rated): array
    {
        $counts = collect(range(1, 5))->mapWithKeys(fn($s) => [$s => 0])->all();

        foreach ($rated as $sr) {
            $s = (int) $sr->performance_score;
            if ($s >= 1 && $s <= 5) {
                $counts[$s]++;
            }
        }

        return [
            'rows'  => collect([5, 4, 3, 2, 1])
                ->map(fn($s) => ['s' => $s, 'c' => $counts[$s]])
                ->values()->all(),
            'max'   => max(1, max($counts)),
            'total' => array_sum($counts),
            'label' => collect([5, 4, 3, 2, 1])
                ->map(fn($s) => $s . '★:' . $counts[$s])
                ->implode(' · '),
        ];
    }

    /**
     * Rows for PANEL_DATA['feedback'] — mirrors your srItems() shape.
     */
    private function feedbackItems2(Collection $rated, array $buckets, array $sat): array
    {
        $items = $rated
            ->sortBy([
                ['performance_score', 'asc'],
                ['feedback_submitted_at', 'desc'],
            ])
            ->take(20)
            ->map(function ($sr) {
                $score = (int) $sr->performance_score;

                $meta = [$this->shortName(optional($sr->assignedUser)->name)];

                // Prefer the domain (specific trade), fall back to the category
                if ($sr->domain) {
                    $meta[] = $sr->domain->name;
                } elseif ($sr->category) {
                    $meta[] = $sr->category->category_name;
                }

                if ($sr->evaluation_comment) $meta[] = '"' . $sr->evaluation_comment . '"';
                if ($score <= 2)             $meta[] = 'Flagged for QC review';

                return [
                    'id'     => $this->srCode($sr),
                    'client' => optional($sr->client)->company_name ?: ($sr->project_site ?: '—'),
                    'badge'  => $score . '★',
                    'bc'     => $this->ratingColor($score),
                    'meta'   => implode(' · ', $meta),
                ];
            })
            ->values()
            ->all();

        $items[] = $sat['responses'] > 0
            ? [
                'id'     => 'Rating breakdown',
                'client' => $buckets['label'],
                'badge'  => $sat['responses'] . ' total',
                'bc'     => '#9a8053',
                'meta'   => 'Distribution of all ratings in the selected period',
            ]
            : [
                'id'     => 'No feedback yet',
                'client' => '—',
                'badge'  => '0',
                'bc'     => '#94a3b8',
                'meta'   => 'No ratings match the current filters',
            ];

        return $items;
    }



    private function srCode($sr): string
    {
        $yr = $sr->feedback_submitted_at
            ? Carbon::parse($sr->feedback_submitted_at)->format('Y')
            : now()->format('Y');

        return 'SR-' . $yr . '-' . str_pad((string) $sr->id, 4, '0', STR_PAD_LEFT);
    }

    private function ratingColor(int $s): string
    {
        return match (true) {
            $s >= 5 => '#15803d',
            $s === 4 => '#16a34a',
            $s === 3 => '#d97706',
            default  => '#dc2626',
        };
    }

    private function shortName(?string $full): string
    {
        $full = trim((string) $full);
        if ($full === '') return 'Unassigned';

        $p = preg_split('/\s+/', $full);
        return count($p) > 1
            ? $p[0] . ' ' . Str::upper(Str::substr(end($p), 0, 1)) . '.'
            : $p[0];
    }

    /** Returns ['full' => n, 'half' => 0|1, 'empty' => n] for the star row. */
    private function starIcons(float $score): array
    {
        $full = (int) floor($score);
        $frac = $score - $full;
        $half = 0;

        if ($frac >= 0.75) {
            $full++;
        } elseif ($frac >= 0.25) {
            $half = 1;
        }

        return ['full' => $full, 'half' => $half, 'empty' => max(0, 5 - $full - $half)];
    }



    private const ACTIVE_STATUSES = ['Assigned', 'In Progress', 'Qc Review', 'Rework', 'Reschedule'];
    private const TRADE_COLORS    = ['#9a8053', '#393837', '#b8975e', '#64748b', '#7c3aed'];

    private function workforce(array $filters, $start, $end): array
{
    // All SRs in the window, with their category
    $rows = DB::table('service_requests as sr')
        ->leftJoin('service_categories as sc', 'sc.id', '=', 'sr.service_type_id')
        ->whereBetween('sr.created_at', [$start, $end])
        ->when(!empty($filters['status']),  fn($q) => $q->where('sr.status', $filters['status']))
        ->when(!empty($filters['client']),  fn($q) => $q->where('sr.client_id', $filters['client']))
        ->when(!empty($filters['service']), fn($q) => $q->where('sr.service_type_id', $filters['service']))
        ->get(['sr.assigned_user_id', 'sr.status', 'sc.category_name']);

    // Capacity per category: total SRs vs unassigned ("free")
 $capacity = $rows
    ->filter(fn($r) => $r->category_name)
    ->groupBy('category_name')
    ->map(function ($group, $category) {
        $total = $group->count();
        $free  = $group->filter(fn($r) => empty($r->assigned_user_id))->count();

        return [
            't'     => $category,
            'total' => $total,
            'avail' => $free,
            'taken' => $total - $free,
        ];
    })
    ->sortByDesc('total')
    ->values()
    ->map(fn($c, $i) => $c + ['c' => self::TRADE_COLORS[$i % count(self::TRADE_COLORS)]]);

    // Overall totals
    $totalSrs = $rows->count();
    $freeSrs  = $rows->filter(fn($r) => empty($r->assigned_user_id))->count();
    $takenSrs = $totalSrs - $freeSrs;

    // Live technician state (unfiltered — this is "right now")
    $assigned = $rows->filter(fn($r) => !empty($r->assigned_user_id));
    $onSite   = $assigned->where('status', 'In Progress')->count();
    $enRoute  = $assigned->where('status', 'Assigned')->count();

    // Job volume per trade — feeds the "Jobs by Trade" donut
    $trades = $rows->filter(fn($r) => $r->category_name)
        ->countBy('category_name')
        ->map(fn($n, $name) => ['n' => $name, 'v' => $n])
        ->values()
        ->map(fn($t, $i) => $t + ['c' => self::TRADE_COLORS[$i % count(self::TRADE_COLORS)]]);

    return [
        'total'        => $totalSrs,
        'available'    => $freeSrs,
        'taken'        => $takenSrs,
        'on_site'      => $onSite,
        'en_route'     => $enRoute,
        'utilization'  => $totalSrs > 0 ? (int) round($takenSrs / $totalSrs * 100) : 0,
        'capacity'     => $capacity->all(),
        'trades'       => $trades->all(),
        'trades_total' => $rows->filter(fn($r) => $r->category_name)->count(),
    ];
}

    private function inquirySrs(array $filters, $start, $end)
    {
        $f = $filters;
        unset($f['status']);
        return $this->scoped($f, $start, $end)
            ->where('service_requests.status', 'Pending')
            ->get();
    }

    private function slaBreachItems(array $filters, $start, $end): array
    {
        $rows = $this->scoped($filters, $start, $end)
            ->get()
            ->filter(fn($sr) => $this->metSla($sr) === false)
            ->sortByDesc('id');

        return collect($this->srItems($rows))
            ->map(fn($item) => $item + ['badge' => 'Breached', 'bc' => '#dc2626'])
            ->all();
    }

    private function srQuery()
    {
        return ServiceRequest::query()
            ->select(
                'service_requests.*',
                'sc.category_name',
                'u.name as assigned_name',

            )
            ->leftJoin('service_categories as sc', 'sc.id', '=', 'service_requests.service_type_id')
            ->leftJoin('users as u',              'u.id',  '=', 'service_requests.assigned_user_id')
            ->leftJoin('projects as p',           'p.id',  '=', 'service_requests.project_id')
            ->with('client:id,company_name')

            ->orderByDesc('service_requests.id');
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
                'punches.user:id,name',   // the ML on each punch
            ])
            ->get();
    }

    private function query(array $filters): Builder
    {
        return ServiceRequest::query()
            ->when($filters['status']  ?? null, fn($q, $v) => $q->where('service_requests.status', $v))
            ->when($filters['client']  ?? null, fn($q, $v) => $q->where('service_requests.client_id', $v))
            ->when($filters['service'] ?? null, fn($q, $v) => $q->where('service_requests.service_type_id', $v));
    }

    private function resolveRange(array $filters): array
    {
        $range = $filters['range'] ?? 'today';

        if ($range === 'custom' && !empty($filters['from']) && !empty($filters['to'])) {
            try {
                $from = Carbon::parse($filters['from'])->startOfDay();
                $to   = Carbon::parse($filters['to'])->endOfDay();
                if ($from->gt($to)) {
                    [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
                }
                return [$from, $to];
            } catch (\Exception $e) {
                // fall through
            }
        }

        return match ($range) {
            'month'   => [now()->startOfMonth(), now()->endOfMonth()],
            'quarter' => [now()->startOfQuarter(), now()->endOfQuarter()],
            default   => [today()->startOfDay(), today()->endOfDay()],   // ← today is now default
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

        return $name ? "Good {$part}, " . Str::before($name, ' ') . '!' : "Good {$part}!";
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

    /**
     * When the request was actually closed out.
     *
     * There is no completed_at column, so this approximates: an invoice
     * timestamp if one exists, otherwise the last time the row was touched.
     * Add a real completed_at and change this to return it — the fourth
     * stage SLA is only as accurate as this method.
     */
    private function closedAt(ServiceRequest $sr): ?Carbon
    {
        if ($sr->status !== 'Completed') {
            return null;
        }

        return $sr->invoice_submitted_at ?? $sr->updated_at;
    }

    /** When the field work finished: last punch-out, else the QC stamp. */
    private function completedAt(ServiceRequest $sr): ?Carbon
    {
        $punchOut = $sr->punches
            ->filter(fn($p) => $p->punch_out_at)
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

    /* ---------------------------------------------------------------------
     | Stage SLAs
     |
     | Each stage is a pair of timestamps. A stage with either end missing
     | is not judged — an SR still awaiting approval hasn't breached the
     | approval SLA, it simply hasn't finished that stage yet.
     --------------------------------------------------------------------- */

    private function stageHours(ServiceRequest $sr, string $stage): ?float
    {
        [$from, $to] = match ($stage) {
            'approval' => [$sr->created_at,      $sr->hop_approved_at],
            'dispatch' => [$sr->hop_approved_at, $sr->dispatched_at],
            'accept'   => [$sr->dispatched_at,   $sr->accepted_at],
            'closeout' => [$sr->qc_reviewed_at,  $this->closedAt($sr)],
            default    => [null, null],
        };

        if (! $from || ! $to) {
            return null;
        }

        return round(abs($from->diffInMinutes($to)) / 60, 1);
    }

    /** Breached stages for one SR, as ['stage' => hours taken]. */
 private function stageBreaches(ServiceRequest $sr): array
    {
        $breached = [];

        foreach (self::STAGE_SLA_HOURS as $stage => $target) {
            $hours = $this->stageHours($sr, $stage);

            if (! is_null($hours) && $hours > $target) {
                $breached[$stage] = $hours;
            }
        }

        return $breached;
    }
      

    private function hasBreach(ServiceRequest $sr): bool
    {
        return $this->stageBreaches($sr) !== [];
    }

    private function isCriticalBreach(ServiceRequest $sr): bool
    {
        return $this->hasBreach($sr)
            && in_array($sr->priority_level, self::CRITICAL_PRIORITIES, true);
    }

    /** Breach counts per stage across a collection. */
    private function breachesByStage(Collection $srs): array
    {
        $counts = array_fill_keys(array_keys(self::STAGE_SLA_HOURS), 0);

        foreach ($srs as $sr) {
            foreach (array_keys($this->stageBreaches($sr)) as $stage) {
                $counts[$stage]++;
            }
        }

        return collect($counts)
            ->map(fn ($count, $stage) => [
                'stage'  => $stage,
                'label'  => self::STAGE_LABELS[$stage],
                'target' => self::STAGE_SLA_HOURS[$stage],
                'count'  => $count,
            ])
            ->values()
            ->all();
    }

    /* ---------------------------------------------------------------------
     | Field hours — drives SLA compliance
     --------------------------------------------------------------------- */

    /** Total time an ML spent on site for this SR, across all punches. */
    private function fieldHours(ServiceRequest $sr): ?float
    {
        $minutes = $sr->punches
            ->filter(fn ($p) => $p->punch_in_at && $p->punch_out_at)
            ->sum(fn ($p) => abs($p->punch_in_at->diffInMinutes($p->punch_out_at)));

        return $minutes > 0 ? round($minutes / 60, 1) : null;
        return $sr->punches->sum(fn($punch) => $this->punchExpense($punch));
    }

    private function metFieldSla(ServiceRequest $sr): ?bool
    {
        $hours = $this->fieldHours($sr);

        return is_null($hours) ? null : $hours <= self::FIELD_HOURS_TARGET;
    }

    /** Share of completed field work that came in at or under target. */
    private function slaCompliance(Collection $srs): ?float
    {
        $judged = $srs->map(fn($sr) => $this->metSla($sr))->filter(fn($v) => ! is_null($v));

        if ($judged->isEmpty()) {
            return null;
        }

        return round($judged->filter()->count() / $judged->count() * 100, 1);
    }

    /* ---------------------------------------------------------------------
     | Money
     --------------------------------------------------------------------- */

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

        $open       = $current->filter(fn($sr) => $this->isOpen($sr));
        $inProgress = $current->where('status', 'In Progress')->count();

        $sla     = $this->slaCompliance($current)  ?? 0;
        $prevSla = $this->slaCompliance($previous) ?? 0;

        $invoiced     = $this->invoicedTotal($filters, $start, $end);
        $prevInvoiced = $this->invoicedTotal($filters, $prevStart, $prevEnd);

        $turnaround     = $this->avgTurnaround($current);
        $prevTurnaround = $this->avgTurnaround($previous);

        $breaches  = $current->filter(fn($sr) => $this->metSla($sr) === false)->count();
        $oowClosed = $current->where('status', 'Completed')
            ->reject(fn($sr) => $this->isInWarranty($sr))
            ->count();

        return [
            'total' => [
                'value'      => $total,
                'sub'        => "vs {$prevTotal} last period",
                'delta'      => $this->percentLabel($total, $prevTotal),
                'delta_tone' => $this->tone($total, $prevTotal),
                'spark'      => $this->spark($filters, fn(Collection $srs) => $srs->count()),
            ],
            'open' => [
                'value'      => $open->count(),
                'sub'        => $inProgress . ' in progress · ' . max(0, $open->count() - $inProgress) . ' awaiting action',
                'delta'      => 'Live',
                'delta_tone' => 'dn',
                'spark'      => $this->spark($filters, fn(Collection $srs) => $srs->filter(fn($sr) => $this->isOpen($sr))->count()),
            ],
            'sla' => [
                'value'      => $sla ? $sla . '%' : '—',
                'sub'        => $breaches . ' ' . Str::plural('breach', $breaches) . ' over ' . self::SLA_TARGET_HOURS . 'h',
                'delta'      => $prevSla ? $this->pointLabel($sla, $prevSla) : null,
                'delta_tone' => $this->tone($sla, $prevSla),
                'spark'      => $this->spark($filters, fn(Collection $srs) => $this->slaCompliance($srs) ?? 0),
            ],
            'invoiced' => [
                'value'      => $this->money($invoiced),
                'sub'        => $oowClosed . ' out-of-warranty SRs closed',
                'delta'      => $this->percentLabel($invoiced, $prevInvoiced),
                'delta_tone' => $this->tone($invoiced, $prevInvoiced),
                'spark'      => [],   // invoicing is not a per-day series
            ],
            'turnaround' => [
                'value'      => $turnaround ? $turnaround . 'h' : '—',
                'sub'        => 'Logged to punch-out',
                'delta'      => $turnaround && $prevTurnaround ? round($turnaround - $prevTurnaround, 1) . 'h' : null,
                // faster is better, so the comparison is deliberately inverted
                'delta_tone' => $this->tone($prevTurnaround ?? 0, $turnaround ?? 0),
                'spark'      => $this->spark($filters, fn(Collection $srs) => $this->avgTurnaround($srs) ?? 0),
            ],
        ];
    }

    private function avgTurnaround(Collection $srs): ?float
    {
        $hours = $srs->map(fn($sr) => $this->turnaroundHours($sr))->filter();

        return $hours->isEmpty() ? null : round($hours->avg(), 1);
    }

    /**
     * Seven daily points ending today. The week is loaded once and reused
     * across all the sparklines.
     */
    private function spark(array $filters, callable $measure): array
    {
        $this->sparkWeek ??= $this->load($filters, now()->subDays(6)->startOfDay(), now()->endOfDay());

        $week = $this->sparkWeek;

        return collect(range(6, 0))
            ->map(function ($daysAgo) use ($week, $measure) {
                $day = now()->subDays($daysAgo)->toDateString();

                return (float) $measure(
                    $week->filter(fn($sr) => $sr->created_at?->toDateString() === $day)
                );
            })
            ->all();
    }

    /* =====================================================================
     | Alerts
     ===================================================================== */

    private function alertCounts(Collection $srs, Carbon $start, Carbon $end): array
    {
        $breached = $srs->filter(fn ($sr) => $this->hasBreach($sr));

        return [
            'breaches' => $srs->filter(fn($sr) => $this->metSla($sr) === false)->count(),

            'pending' => $srs->whereIn('status', self::AWAITING_ACTION)->count(),

            'stalled'  => $srs
                ->filter(fn($sr) => $this->isOpen($sr) && $sr->updated_at?->lt(now()->subDay()))
                ->count(),

            'wa_failures' => $this->whatsapp($start, $end)['failed'] ?? 0,

            'on_site' => $srs->where('status', 'In Progress')
                ->filter(fn($sr) => $sr->punches->contains(fn($p) => $p->punch_in_at && ! $p->punch_out_at))
                ->count(),
        ];
    }

    /* =====================================================================
     | Cards
     ===================================================================== */

    // private function statusBreakdown(Collection $srs): array
    // {
    //     return collect(self::STATUS_COLORS)
    //         ->map(fn($color, $status) => [
    //             'label' => $status,
    //             'count' => $srs->where('status', $status)->count(),
    //             'color' => $color,
    //         ])
    //         ->filter(fn($row) => $row['count'] > 0)
    //         ->sortByDesc('count')
    //         ->values()
    //         ->all();
    // }


    private function statusBreakdown($current): array
    {
        $rows   = collect($current);
        $counts = $rows->countBy('status');
        $max    = max(1, $counts->max() ?? 0);

        $out = [];

        foreach (self::STATUS_GROUPS as $group => $statuses) {
            foreach ($statuses as $status) {
                $n = $counts[$status] ?? 0;

                $out[] = [
                    'lbl' => $status,
                    'n'   => $n,
                    'w'   => round($n / $max * 100, 1),
                    'c'   => self::STATUS_COLORS[$status] ?? '#64748b',
                ];
            }
        }

        return $out;
    }



    private function srTrend2(array $filters): array
    {
        $buckets = $this->buckets2($filters['period'] ?? '6M');
        $from    = $buckets[0]['start'];
        $to      = end($buckets)['end'];

        $srs = $this->query($filters)
            ->whereBetween('service_requests.created_at', [$from, $to])
            ->get(['id', 'created_at', 'status']);

        $labels = $done = $pending = [];

        foreach ($buckets as $bucket) {
            $slice = $srs->filter(fn($sr) => $sr->created_at?->between($bucket['start'], $bucket['end']));

            $labels[]  = $bucket['label'];
            $done[]    = $slice->where('status', 'Completed')->count();
            $pending[] = $slice->where('status', 'Pending')->count();
        }

        return [
            'labels'     => $labels,
            'done'       => $done,
            'inquiries'  => $pending,
            'doneTotal'  => array_sum($done),
            'inqTotal'   => array_sum($pending),
            'period'     => $filters['period'] ?? '6M',
        ];
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
            $slice = $srs->filter(fn($sr) => $sr->created_at?->between($bucket['start'], $bucket['end']));

            $labels[]      = $bucket['label'];
            $inWarranty[]  = $slice->filter(fn($sr) => $this->isInWarranty($sr))->count();
            $outWarranty[] = $slice->reject(fn($sr) => $this->isInWarranty($sr))->count();
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
        $expense  = $srs->sum(fn($sr) => $this->expenseFor($sr));

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
                fn($q) => $q->whereHas('serviceRequest', fn($sr) => $sr
                    ->when($filters['client']  ?? null, fn($s, $v) => $s->where('client_id', $v))
                    ->when($filters['service'] ?? null, fn($s, $v) => $s->where('service_type_id', $v)))
            )
            ->with('items')
            ->get();

        $labels = $invoicedSeries = $expenseSeries = [];

        foreach ($buckets as $bucket) {
            $labels[] = $bucket['label'];

            $invoicedSeries[] = round($invoices
                ->filter(fn($sr) => $sr->invoice_submitted_at?->between($bucket['start'], $bucket['end']))
                ->sum('invoice_total'), 2);

            $expenseSeries[] = round($punches
                ->filter(fn($p) => $p->punch_out_at?->between($bucket['start'], $bucket['end']))
                ->sum(fn($p) => $this->punchExpense($p)), 2);
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

    /**
     * QC & quality.
     *
     * The gauge now shows QC throughput — reviewed against everything that
     * has reached QC — instead of SLA compliance, which moved to the KPI row.
     * First-pass rate is replaced by the pending queue.
     */
    private function qc(Collection $srs): array
{
    $reviewed  = $srs->filter(fn($sr) => $sr->qc_reviewed_at);
    $firstPass = $reviewed->filter(fn($sr) => empty($sr->rework_notes));

    $rework     = $srs->filter(fn($sr) => !empty($sr->rework_notes))->count();
    $reworkOpen = $srs->where('status', 'Rework')->count();

    // SRs sitting in the QC queue
    $pending = $srs->where('status', 'Qc Review');

    // SRs that have reached QC at all (queued or already reviewed)
    $reachedQc = $pending->count() + $reviewed->count();

    $oldest = $pending
        ->sortBy(fn($sr) => $this->completedAt($sr) ?? $sr->updated_at)
        ->first();

    $breached = $srs->filter(fn($sr) => $this->metSla($sr) === false);

    return [
        // Gauge
        'qc_rate'     => $reachedQc > 0 ? round($reviewed->count() / $reachedQc * 100, 1) : null,
        'qc_reviewed' => $reviewed->count(),
        'qc_reached'  => $reachedQc,

        // Used by the KPI card and the QC card header
        'pending_review' => $pending->count(),
        'pending_qc'     => $pending->count(),
        'pending_qc_sub' => $oldest
            ? 'Oldest ' . optional($this->completedAt($oldest) ?? $oldest->updated_at)->diffForHumans()
            : 'Queue clear',

        'sla_compliance'  => $this->slaCompliance($srs),

        'first_pass_rate' => $reviewed->isEmpty()
            ? null
            : round($firstPass->count() / $reviewed->count() * 100),
        'first_pass_sub'  => $firstPass->count() . ' of ' . $reviewed->count() . ' reviewed',

        'rework_count' => $rework,
        'rework_sub'   => $reworkOpen . ' currently open',

        'sla_breaches'   => $breached->count(),
        'sla_breach_sub' => $breached->isEmpty()
            ? 'All within target'
            : $breached->pluck('priority_level')->filter()->unique()->take(2)->implode(', ') . ' priority',

        'stage_breaches' => $this->breachesByStage($srs),
    ];
}
    /**
     * Technician scorecard.
     *
     * Punch-in rate is gone. In its place: how much work each ML still has
     * open (with the SR list for the hover), and their breach count with a
     * critical flag.
     */
    private function technicians(Collection $srs): array
    {
        return $srs
            ->filter(fn($sr) => $sr->assigned_user_id && $sr->assignedUser)
            ->groupBy('assigned_user_id')
            ->map(function (Collection $jobs) {
                $tech = $jobs->first()->assignedUser;

                $minutes = $jobs->sum(fn($sr) => $sr->punches->sum(
                    fn($p) => $p->punch_in_at && $p->punch_out_at
                        ? abs($p->punch_in_at->diffInMinutes($p->punch_out_at))
                        : 0
                ));

                $rated    = $jobs->filter(fn ($sr) => $sr->performance_score);
                $open     = $jobs->filter(fn ($sr) => $this->isOpen($sr));
                $breached = $jobs->filter(fn ($sr) => $this->hasBreach($sr));
                $rated   = $jobs->filter(fn($sr) => $sr->performance_score);
                $punched = $jobs->filter(fn($sr) => $sr->punches->contains(fn($p) => $p->punch_in_at))->count();

                return [
                    'id'                 => $tech->id,
                    'name'               => $tech->name,
                    'initials'           => $this->initials($tech->name),
                    'department'         => $jobs->first()->category?->category_name ?? '—',
                    'jobs'               => $jobs->count(),
                    'hours'              => round($minutes / 60),
                    'rating'             => $rated->isEmpty() ? 0 : round($rated->avg('performance_score'), 1),
                    'rework'             => $jobs->filter(fn ($sr) => ! empty($sr->rework_notes))->count(),
                    'expenses_formatted' => self::CURRENCY.' '.number_format($jobs->sum(fn ($sr) => $this->expenseFor($sr))),

                    // Pending work — count for the cell, list for the hover
                    'pending'       => $open->count(),
                    'pending_items' => $open
                        ->sortBy('created_at')
                        ->take(6)
                        ->map(fn ($sr) => $sr->code.' · '.$sr->status
                            .' · '.($sr->client?->company_name ?? '—'))
                        ->values()
                        ->all(),

                    // Breaches, flagged critical on high-priority work
                    'sla_breaches' => $breached->count(),
                    'critical'     => $breached->contains(fn ($sr) => $this->isCriticalBreach($sr)),
                    'breach_items' => $breached
                        ->take(6)
                        ->map(function ($sr) {
                            $stages = collect($this->stageBreaches($sr))
                                ->map(fn ($hours, $stage) => self::STAGE_LABELS[$stage].' '.$hours.'h')
                                ->implode(', ');

                            return $sr->code.' · '.$stages;
                        })
                        ->values()
                        ->all(),
                    'rework'             => $jobs->filter(fn($sr) => ! empty($sr->rework_notes))->count(),
                    'punch_rate'         => $jobs->count() ? round($punched / $jobs->count() * 100) : 0,
                    'expenses_formatted' => self::CURRENCY . ' ' . number_format($jobs->sum(fn($sr) => $this->expenseFor($sr))),
                ];
            })
            ->sortByDesc('jobs')
            ->values()
            ->all();
    }

    // private function clients(Collection $srs): array
    // {
    //     return $srs
    //         ->filter(fn($sr) => $sr->client)
    //         ->groupBy('client_id')
    //         ->map(fn(Collection $group) => [
    //             'id'           => $group->first()->client->id,
    //             'name'         => $group->first()->client->company_name,
    //             'short_name'   => Str::before($group->first()->client->company_name, ' '),
    //             'srs'          => $group->count(),
    //             'in_warranty'  => $group->filter(fn($sr) => $this->isInWarranty($sr))->count(),
    //             'out_warranty' => $group->reject(fn($sr) => $this->isInWarranty($sr))->count(),
    //         ])
    //         ->sortByDesc('srs')
    //         ->take(6)
    //         ->values()
    //         ->all();
    // }


    private function clients(Collection $current): array
{
    return $current
        ->filter(fn($sr) => $sr->client_id)
        ->groupBy('client_id')
        ->map(function ($group, $clientId) {
            $first = $group->first();
            $rated = $group->whereNotNull('performance_score');

            $iw = $group->filter(fn($sr) =>
                in_array($sr->warranty_scope, ['In-warranty', 'IW', 'in_warranty'], true)
            )->count();

            return [
                'id'     => (int) $clientId,
                'n'      => optional($first->client)->company_name ?: '—',
                'srs'    => $group->count(),
                'iw'     => $iw,
                'oow'    => $group->count() - $iw,
                'rating' => $rated->count() ? round($rated->avg('performance_score'), 1) : 0,
                'exp'    => 'AED ' . number_format((float) $group->sum('invoice_total'), 0),
            ];
        })
        ->sortByDesc('srs')
        ->take(6)
        ->values()
        ->all();
}

    /** Front desk: pending, rejected and completed per person. */
    private function frontDesk(Collection $srs): array
    {
        return $srs
            ->filter(fn($sr) => $sr->creator)
            ->groupBy('created_by')
            ->map(function (Collection $group) {
                $user = $group->first()->creator;

                $completed = $group->where('status', 'Completed')->count();
                $rejected  = $group->whereIn('status', self::REJECTED_STATUSES)->count();

                return [
                    'id'        => $user->id,
                    'name'      => $user->name,
                    'initials'  => $this->initials($user->name),
                    'srs'       => $group->count(),
                    'pending'   => $group->filter(fn ($sr) => $this->isOpen($sr))->count(),
                    'rejected'  => $rejected,
                    'completed' => $completed,
                ];
            })
            ->sortByDesc('srs')
            ->values()
            ->all();
    }

    private function satisfaction(Collection $srs): array
    {
        $rated     = $srs->filter(fn($sr) => $sr->performance_score);
        $completed = $srs->where('status', 'Completed')->count();
        $lowest    = $rated->where('performance_score', '<=', 2)->sortBy('performance_score')->first();

        return [
            'average'       => $rated->isEmpty() ? null : round($rated->avg('performance_score'), 1),
            'responses'     => $rated->count(),
            'response_rate' => $completed > 0 ? round($rated->count() / $completed * 100) : null,
            'completed_srs' => $completed,
            'flagged'       => $lowest
                ? $lowest->code . " rated {$lowest->performance_score} stars — flagged for review"
                : null,
        ];
    }

    /**
     * The five most recent client reviews: client, SR code, the ML who did
     * the work, the star score and the comment.
     */
    private function clientReviews(Collection $srs): array
    {
        return $srs
            ->filter(fn ($sr) => $sr->performance_score)
            ->sortByDesc(fn ($sr) => $sr->feedback_submitted_at ?? $sr->updated_at)
            ->take(5)
            ->map(function ($sr) {
                // Prefer the ML who actually punched; fall back to the assignee.
                $ml = $sr->punches
                    ->filter(fn ($p) => $p->user)
                    ->sortByDesc('punch_out_at')
                    ->first()?->user?->name
                    ?? $sr->assignedUser?->name;

                return [
                    'id'       => $sr->id,
                    'code'     => $sr->code,
                    'client'   => $sr->client?->company_name ?? '—',
                    'ml'       => $ml ?? '—',
                    'initials' => $this->initials($ml),
                    'stars'    => (int) $sr->performance_score,
                    'comment'  => $sr->evaluation_comment,
                    'when'     => ($sr->feedback_submitted_at ?? $sr->updated_at)?->diffForHumans(),
                ];
            })
            ->values()
            ->all();
    }

    private function ratingBuckets(Collection $srs): array
    {
        $rated = $srs->filter(fn($sr) => $sr->performance_score);

        if ($rated->isEmpty()) {
            return [];
        }

        return collect([5, 4, 3, 2, 1])
            ->map(fn($stars) => [
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
            ->map(fn($row) => [
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
    $filters = [
        'range'   => $request->input('range', 'today'),
        'from'    => $request->input('from'),
        'to'      => $request->input('to'),
        'status'  => $request->input('status'),
        'client'  => $request->input('client'),
        'service' => $request->input('service'),
    ];

    [$start, $end] = $this->resolveRange($filters);

    $type = $request->input('type');
    $id   = $request->input('id');

    [$title, $icon, $section] = match ($type) {
        'active-srs'      => ['Active open SRs', 'bi-activity', 'Still in the workflow'],
        'sla-breach'      => ['SLA breaches', 'bi-exclamation-triangle', 'Over ' . self::SLA_TARGET_HOURS . ' hours'],
        'pending-actions' => ['Pending actions', 'bi-hourglass-split', 'Waiting on a decision'],
        'slow-srs'        => ['Stalled requests', 'bi-clock-history', 'No movement in 24h'],
        'pending-qc'      => ['Pending QC', 'bi-patch-check', 'Waiting on review'],
        'invoices'        => ['Invoiced requests', 'bi-receipt', 'Billed this period'],
        'month-srs'       => ['All SRs this period', 'bi-ticket-detailed', 'Every request logged'],
        'technician'      => ['Technician detail', 'bi-person-badge', 'Recent assignments'],
        'client'          => ['Client detail', 'bi-buildings', 'Service request history'],
        'wa-failures'     => ['WhatsApp failures', 'bi-whatsapp', 'Undelivered messages'],
        default           => ['Details', 'bi-list', null],
    };

    $all = $this->load($filters, $start, $end);

    $srs = match ($type) {
        'active-srs'      => $all->filter(fn($sr) => $this->isOpen($sr)),
        'sla-breach'      => $all->filter(fn($sr) => $this->metSla($sr) === false),
        'pending-actions' => $all->whereIn('status', self::AWAITING_ACTION),
        'slow-srs'        => $all->filter(fn($sr) => $this->isOpen($sr) && $sr->updated_at?->lt(now()->subDay())),
        'invoices'        => $all->filter(fn($sr) => $sr->invoice_submitted_at),
        'technician'      => $all->where('assigned_user_id', $id),
        'client'          => $all->filter(fn($sr) => optional($sr->project)->client_id == $id),
        default           => $all,
    };

    if ($type === 'technician' && $id) {
        $title = $srs->first()?->assignedUser?->name ?? $title;
    }

    if ($type === 'client' && $id) {
        $title = $srs->first()?->project?->client?->company_name ?? $title;
    }

    // On the breach panel, sort worst first.
    if ($type === 'sla-breach') {
        $srs = $srs->sortByDesc(fn($sr) => $this->isCriticalBreach($sr) ? 1 : 0);
    }

    $items = $srs
        ->sortByDesc('created_at')
        ->take(25)
        ->map(function ($sr) use ($type) {
            $isCritical = $type === 'sla-breach' && $this->isCriticalBreach($sr);

            if ($type === 'sla-breach') {
                $stages = $this->stageBreaches($sr);
                $meta = collect($stages)
                    ->map(fn($hours, $stage) => (self::STAGE_LABELS[$stage] ?? $stage) . ' — ' . $hours . 'h')
                    ->values()
                    ->push($sr->assignedUser?->name);
            } else {
                $meta = collect([
                    $sr->category?->category_name,
                    $this->isInWarranty($sr) ? 'In warranty' : 'Out of warranty',
                    $sr->project?->site_name,
                    $sr->assignedUser?->name,
                ]);
            }

            return [
                'reference' => $sr->code,
                'badge'     => $isCritical ? 'Critical' : $sr->status,
                'color'     => $isCritical ? '#dc2626' : (self::STATUS_COLORS[$sr->status] ?? '#9a8053'),
                'title'     => $sr->project?->client?->company_name ?? '—',
                'meta'      => $meta->filter()->implode(' · '),
            ];
        })
        ->values();

    return response()->json([
        'title'    => $title,
        'subtitle' => $items->count() . ' ' . Str::plural('record', $items->count()),
        'icon'     => $icon,
        'section'  => $section,
        'items'    => $items->all(),
    ]);
}

  private function activeSrs(array $filters, $start, $end)
    {
        // "Active" means "not rejected" — it ignores the status dropdown
        $f = $filters;
        unset($f['status']);

        return $this->scoped($f, $start, $end)
            ->where(function ($q) {
                $q->whereNull('service_requests.status')
                    ->orWhere('service_requests.status', '!=', 'Rejected');
            })
            ->get();
    }

      private function metSla(ServiceRequest $sr): ?bool
    {
        $hours = $this->turnaroundHours($sr);

        return is_null($hours) ? null : $hours <= self::SLA_TARGET_HOURS;
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


    private function buckets2(string $period): array
    {
        if ($period === '1M') {
            return collect(range(3, 0))
                ->map(function ($weeksAgo) {
                    $start = now()->startOfWeek()->subWeeks($weeksAgo);

                    return [
                        'label' => $start->format('d M'),
                        'start' => $start,
                        'end'   => $start->copy()->endOfWeek(),
                    ];
                })
                ->all();
        }

        $months = $period === '1Y' ? 12 : 6;

        return collect(range($months - 1, 0))
            ->map(function ($monthsAgo) use ($months) {
                $start = now()->startOfMonth()->subMonths($monthsAgo);

                return [
                    'label' => $start->format($months === 12 ? 'M y' : 'M'),
                    'start' => $start,
                    'end'   => $start->copy()->endOfMonth(),
                ];
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

        return is_null($change) ? null : ($change >= 0 ? '+' : '') . $change . '%';
    }

    /** For values already expressed as percentages, the delta is in points. */
    private function pointLabel(float $current, float $previous): string
    {
        $diff = round($current - $previous, 1);

        return ($diff >= 0 ? '+' : '') . $diff . '%';
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
            return self::CURRENCY . ' ' . round($amount / 1000, 1) . 'k';
        }

        return self::CURRENCY . ' ' . number_format($amount);
    }

    private function initials(?string $name): string
    {
        if (! $name) {
            return '—';
        }

        $parts = preg_split('/\s+/', trim($name)) ?: [];

        return mb_strtoupper(
            mb_substr($parts[0] ?? '', 0, 1) . (isset($parts[1]) ? mb_substr($parts[1], 0, 1) : '')
        );
    }
}
