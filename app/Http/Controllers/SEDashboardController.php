<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use App\Models\ServiceRequest;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SEDashboardController extends Controller
{
    public const COLUMNS = [
        'inquiry'         => ['label' => 'Inquiry Logged',   'color' => '#64748b'],
        'pending_quote'   => ['label' => 'Pending Quote',    'color' => '#7c3aed'],
        'quoted'          => ['label' => 'Quoted',           'color' => '#9a8053'],
        'approved'        => ['label' => 'Approved',         'color' => '#15803d'],
        'assigned'        => ['label' => 'Assigned',         'color' => '#2563eb'],
        'in_progress'     => ['label' => 'In Progress',      'color' => '#d97706'],
        'pending_review'  => ['label' => 'Pending Review',   'color' => '#ea580c'],
        'pending_invoice' => ['label' => 'Pending Invoice',  'color' => '#7c3aed'],
        'completed'       => ['label' => 'Completed',        'color' => '#059669'],
        'cancelled'       => ['label' => 'Cancelled',        'color' => '#ef4444'],
    ];

    /**
     * Maps whatever `service_requests.status` actually stores onto the canonical
     * keys above. Compared after normalisation (lowercased, non-alphanumerics
     * collapsed to underscores), so 'Pending', 'pending', 'In Progress' and
     * 'in-progress' all resolve without editing this list.
     *
     * Your inquiry-approval screen filters on status 'Pending', which is why
     * that alias maps to the intake stage.
     */
    public const STATUS_ALIASES = [
        'pending'          => 'inquiry',
        'inquiry'          => 'inquiry',
        'inquiry_logged'   => 'inquiry',
        'logged'           => 'inquiry',
        'new'              => 'inquiry',
        'open'             => 'inquiry',

        'pending_quote'    => 'pending_quote',
        'quote_pending'    => 'pending_quote',
        'awaiting_quote'   => 'pending_quote',
        'forwarded'        => 'pending_quote',

        'quoted'           => 'quoted',
        'quote_submitted'  => 'quoted',
        'quotation_sent'   => 'quoted',

        'approved'         => 'approved',
        'quote_approved'   => 'approved',
        'client_approved'  => 'approved',
        'hop_approved'     => 'approved',

        'assigned'         => 'assigned',
        'dispatched'       => 'assigned',
        'accepted'         => 'assigned',

        'in_progress'      => 'in_progress',
        'inprogress'       => 'in_progress',
        'ongoing'          => 'in_progress',
        'punched_in'       => 'in_progress',
        'on_hold'          => 'in_progress',
        'hold'             => 'in_progress',

        'pending_review'   => 'pending_review',
        'qc_review'        => 'pending_review',
        'under_review'     => 'pending_review',
        'submitted'        => 'pending_review',
        'qc_pending'       => 'pending_review',

        'pending_invoice'  => 'pending_invoice',
        'invoice_pending'  => 'pending_invoice',
        'awaiting_invoice' => 'pending_invoice',
        'qc_passed'        => 'pending_invoice',

        'completed'        => 'completed',
        'closed'           => 'completed',
        'done'             => 'completed',
        'invoiced'         => 'completed',

        'cancelled'        => 'cancelled',
        'canceled'         => 'cancelled',
        'rejected'         => 'cancelled',
        'withdrawn'        => 'cancelled',
        'quote_rejected'   => 'cancelled',
    ];

    public const TRIAGE_STAGES    = ['inquiry'];
    public const CANCELLED_STAGES = ['cancelled'];
    public const COMPLETED_STAGES = ['completed'];

    /** Max cards rendered per kanban column, keeping the JSON payload small. */
    private const CARDS_PER_COLUMN = 12;

    /** raw DB status => canonical stage. Resolved once per request. */
    private ?array $statusMap = null;


    public function index(Request $request)
    {
        $user   = Auth::user();
        $period = in_array($request->query('period'), ['today', 'week', 'month'], true)
            ? $request->query('period')
            : 'month';

        [$from, $to, $periodLabel] = $this->resolvePeriod($period);

        $stageCounts   = $this->stageCounts($user->id, $from, $to);
        $totalInPeriod = (int) $stageCounts->sum();
        $cancelled     = (int) $stageCounts->get('cancelled', 0);
        $completed     = (int) $stageCounts->get('completed', 0);
        $pendingTriage = $this->openTriageCount($user->id);
        $whatsapp      = $this->whatsappStats($user->id, $from, $to);
        $reworkRate    = $this->reworkRate($user->id, $from, $to);
        $dispatch      = $this->dispatchTime($user->id, $from, $to);  // ← here
        $rating        = $this->avgRating($user->id, $from, $to);

        return view('service_engineer_dashboard', [
            'greeting'    => $this->greeting($user),
            'greetingSub' => "Here's your intake overview",
            'today'       => now()->format('l, d F Y'),
            'periodLabel' => $periodLabel,
            'filters'     => ['period' => $period],

            // Order matches the mock-up's pill order.
            'periodOptions' => ['month' => 'This Month', 'week' => 'This Week', 'today' => 'Today'],

            'alertCounts' => [
                'triage'      => $pendingTriage,
                'wa_failures' => $whatsapp['failed'],
                // 'rework'  => $this->reworkCount($user->id),
            ],

            'kpis' => $this->kpis($user->id, $from, $to, $period, $totalInPeriod, $cancelled, $completed, $pendingTriage),

            'statusBreakdown' => $this->statusBreakdown($stageCounts),
            'triage'          => $this->triageQueue($user->id),

            'srTrend'     => $this->intakeTrend($user->id),
            'intakeStats' => $this->intakeStats($user->id, $from, $to, $totalInPeriod),
            'scope'       => $this->scopeSplit($user->id, $from, $to),

            'cancellation' => [
                'count' => $cancelled,
                'total' => $totalInPeriod,
                'rate'  => $totalInPeriod > 0 ? (int) round($cancelled / $totalInPeriod * 100) : 0,
                'items' => $this->cancelledList($user->id, $from, $to),
            ],

            'whatsapp' => $whatsapp,

            'clients'     => $this->topClients($user->id, $from, $to),
            'clientStats' => [
                'new_clients' => $this->newClientCount($user->id, $from, $to),
                'new_sites'   => $this->newSiteCount($from, $to),
            ],


            'categories' => $user->serviceCategories()
                ->where('service_categories.status', 1)
                ->orderBy('service_categories.sort_order')
                ->get(['service_categories.id', 'category_name', 'icon', 'color_code']),

            'reworkItems'   => $this->reworkItems($user->id, $from, $to),
            'pendingItems'  => $this->pendingItems($user->id, $from, $to),
            'qcItems'       => $this->qcItems($user->id, $from, $to),

            'activeFieldCount' => $this->activeFieldCount($user->id, $from, $to),
            
            'metrics' => [
                'reworkRate'    => $reworkRate['rate'],
                'reworkSub'     => $reworkRate['returned'] . ' of ' . $reworkRate['total'] . ' SRs returned',
                'dispatchMins'  => $dispatch['mins'],
                'dispatchLabel' => $dispatch['display'],
                'dispatchSub'   => $dispatch['count'] . ' ' . Str::plural('SR', $dispatch['count']) . ' approved',
                'slaCompliance' => 87,
                'rating'      => $rating['value'],
                'ratingLabel' => $rating['display'],
                'ratingSub'   => $rating['sub']
            ],


            'completedCount' => $completed,
            'kanban'         => $this->kanban($user->id),
        ]);
    }



    private function avgRating(int $userId, Carbon $from, Carbon $to): array
    {
        $rows = ServiceRequest::query()
            ->where('assigned_se', $userId)
            ->whereNotNull('performance_score')
            ->whereBetween('feedback_submitted_at', [$from, $to])
            ->pluck('performance_score');

        $count = $rows->count();
        $avg   = $count ? round($rows->avg(), 1) : 0;

        return [
            'value'   => $avg,
            'display' => $count ? number_format($avg, 1) : '—',
            'sub'     => $count
                ? $count . ' ' . Str::plural('response', $count) . ' · out of 5'
                : 'No feedback yet',
        ];
    }


    private function dispatchTime(int $userId, Carbon $from, Carbon $to): array
    {
        $minutes = ServiceRequest::query()
            ->where('assigned_se', $userId)
            ->whereNotNull('approved_at')
            ->whereBetween('approved_at', [$from, $to])
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, approved_at)) AS avg_mins')
            ->value('avg_mins');

        $mins = (int) round((float) $minutes);

        return [
            'mins'    => $mins,
            'display' => $mins <= 0
                ? '—'
                : ($mins < 60
                    ? $mins . 'm'
                    : intdiv($mins, 60) . 'h ' . ($mins % 60) . 'm'),
            'count'   => ServiceRequest::query()
                ->where('assigned_se', $userId)
                ->whereNotNull('approved_at')
                ->whereBetween('approved_at', [$from, $to])
                ->count(),
        ];
    }


    private function activeFieldCount(int $userId, Carbon $from, Carbon $to): int
    {
        return ServiceRequest::query()
            ->where('assigned_se', $userId)
            ->whereIn('status', ['Accepted', 'In Progress'])
            ->whereBetween('updated_at', [$from, $to])
            ->count();
    }

    private function reworkRate(int $userId, Carbon $from, Carbon $to): array
    {
        $total = ServiceRequest::query()
            ->where('assigned_se', $userId)
            ->whereBetween('updated_at', [$from, $to])
            ->count();

        $returned = ServiceRequest::query()
            ->where('assigned_se', $userId)
            ->whereBetween('updated_at', [$from, $to])
            ->whereNotNull('rework_notes')
            ->count();

        return [
            'rate'     => $total > 0 ? (int) round($returned / $total * 100) : 0,
            'returned' => $returned,
            'total'    => $total,
        ];
    }
    /* ═══════════════════════ PERIOD ═══════════════════════ */

    /** @return array{0: Carbon, 1: Carbon, 2: string} */
    private function resolvePeriod(string $period): array
    {
        return match ($period) {
            'today' => [now()->startOfDay(),   now()->endOfDay(),   'Today'],
            'week'  => [now()->startOfWeek(),  now()->endOfWeek(),  'This Week'],
            default => [now()->startOfMonth(), now()->endOfMonth(), 'This Month'],
        };
    }


    private function reworkItems(int $userId, Carbon $from, Carbon $to)
    {
        return ServiceRequest::query()
            ->where('assigned_se', $userId)
            ->where('status', 'Rework')
            ->whereBetween('updated_at', [$from, $to])
            ->with(['client:id,company_name', 'project:id,site_name', 'category:id,category_name', 'assignedUser:id,name'])
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn($sr) => [
                'id'         => $sr->code,
                'client'     => $sr->client?->company_name ?? '—',
                'site'       => $sr->project?->site_name ?? '—',
                'priority'   => ucfirst((string) $sr->priority_level),
                'scope'      => $sr->warranty_scope === 'oow' ? 'OoW' : 'IW',
                'cat'        => $sr->category?->category_name ?? '—',
                'originalML' => $sr->assignedUser?->name ?? '—',
                'mlInit'     => $sr->assignedUser?->initials ?? '—',
                'attempt'    => 1,
                'elapsed'    => $sr->updated_at?->diffForHumans(),
                'reason'     => $sr->rework_notes ?: 'No reason recorded.',
            ])
            ->values();
    }



    private function pendingItems(int $userId, Carbon $from, Carbon $to)
    {
        return ServiceRequest::query()
            ->where('assigned_se', $userId)
            ->where('status', 'Approved')
            ->whereBetween('updated_at', [$from, $to])
            ->with(['client:id,company_name', 'project:id,site_name', 'category:id,category_name'])
            ->orderBy('created_at')
            ->get()
            ->map(fn($sr) => [
                'id'       => $sr->code,
                'client'   => $sr->client?->company_name ?? '—',
                'site'     => $sr->project?->site_name ?? '—',
                'priority' => $this->priority($sr->priority_level),
                'scope'    => $this->scopeCode($sr->warranty_scope),
                'cat'      => $sr->category?->category_name ?? '—',
                'logged'   => $this->shortAge($sr->created_at) . ' ago',
                'hrs'      => (int) abs($sr->created_at?->diffInHours(now()) ?? 0),
                'issue'    => $sr->issue_description ?: 'No description recorded.',
            ])
            ->values();
    }


    private function qcItems(int $userId, Carbon $from, Carbon $to)
    {
        return ServiceRequest::query()
            ->where('assigned_se', $userId)
            ->where('status', 'Qc Review')
            ->whereBetween('updated_at', [$from, $to])
            ->with([
                'client:id,company_name',
                'project:id,site_name',
                'category:id,category_name',
                'assignedUser:id,name',
                'punches',
            ])
            ->orderByDesc('updated_at')
            ->get()
            ->map(function ($sr) {
                $punch = $sr->punches->sortByDesc('created_at')->first();

                return [
                    'id'       => $sr->code,
                    'client'   => $sr->client?->company_name ?? '—',
                    'site'     => $sr->project?->site_name ?? '—',
                    'ml'       => $sr->assignedUser?->name ?? '—',
                    'mlInit'   => $sr->assignedUser?->initials ?? '—',
                    'punchout' => $this->clockTime($punch?->punch_out_at) ?? '—',
                    'cat'      => $sr->category?->category_name ?? '—',
                    'priority' => $this->priority($sr->priority_level),
                    'scope'    => $this->scopeCode($sr->warranty_scope),
                    'proofs'   => [
                        'before' => (bool) $punch?->before_photo_path,
                        'after'  => (bool) $punch?->after_photo_path,
                        'form'   => (bool) $punch?->acceptance_form_path,
                    ],
                    'issue'    => $sr->issue_description ?: 'No description recorded.',
                ];
            })
            ->values();
    }



    /** "vs 16 last month" — the phrasing used on the first KPI card. */
    private function previousLabel(string $period): string
    {
        return match ($period) {
            'today' => 'yesterday',
            'week'  => 'last week',
            default => 'last month',
        };
    }

    private function greeting($user): string
    {
        $hour = now()->hour;
        $part = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
        $name = trim((string) preg_split('/\s+/', trim((string) $user->name))[0]);

        return $name !== '' ? "{$part}, {$name}!" : $part . '!';
    }

    /* ═══════════════════════ STATUS NORMALISATION ═══════════════════════ */

    private function normalise(?string $raw): string
    {
        return trim(preg_replace('/_+/', '_', preg_replace('/[^a-z0-9]+/', '_', strtolower((string) $raw))), '_');
    }

    /**
     * Reads the distinct statuses actually present and maps each to a canonical
     * stage. Anything unrecognised is dropped rather than guessed at, so a typo
     * in the data never silently lands in the wrong column.
     */
    private function statusMap(): array
    {
        if ($this->statusMap !== null) {
            return $this->statusMap;
        }

        $map = [];

        foreach (ServiceRequest::query()->distinct()->pluck('status') as $raw) {
            if ($raw === null || $raw === '') {
                continue;
            }

            $key = self::STATUS_ALIASES[$this->normalise($raw)] ?? null;

            if ($key !== null) {
                $map[$raw] = $key;
            }
        }

        return $this->statusMap = $map;
    }

    /** Raw status strings that belong to the given canonical stages. */
    private function rawFor(array $stages): array
    {
        return array_keys(array_filter(
            $this->statusMap(),
            fn($stage) => in_array($stage, $stages, true)
        ));
    }

    private function stageOf(?string $raw): ?string
    {
        return $this->statusMap()[$raw] ?? (self::STATUS_ALIASES[$this->normalise($raw)] ?? null);
    }

    /* ═══════════════════════ BASE QUERY ═══════════════════════ */

    private function mine(int $userId)
    {
        return ServiceRequest::query()->where('created_by', $userId);
    }

    /** canonical stage => count, for SRs created inside the window. */
    private function stageCounts(int $userId, Carbon $from, Carbon $to): Collection
    {
        $rows = $this->mine($userId)
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('status')
            ->selectRaw('status, COUNT(*) AS aggregate')
            ->pluck('aggregate', 'status');

        $counts = collect();

        foreach ($rows as $raw => $count) {
            if ($stage = $this->stageOf((string) $raw)) {
                $counts[$stage] = ($counts[$stage] ?? 0) + (int) $count;
            }
        }

        return $counts;
    }

    /** Triage backlog is deliberately NOT period-scoped — old ones still matter. */
    private function openTriageCount(int $userId): int
    {
        $raw = $this->rawFor(self::TRIAGE_STAGES);

        return $raw ? $this->mine($userId)->whereIn('status', $raw)->count() : 0;
    }

    /* ═══════════════════════ KPI CARDS ═══════════════════════ */

    private function kpis(int $userId, Carbon $from, Carbon $to, string $period, int $total, int $cancelled, int $completed, int $pendingTriage): array
    {
        // abs()+int keeps this identical under Carbon 2 (int) and Carbon 3 (float).
        $length   = (int) abs($from->diffInDays($to)) + 1;
        $prevFrom = (clone $from)->subDays($length);
        $prevTo   = (clone $from)->subSecond();
        $prevWord = $this->previousLabel($period);

        $prevTotal = $this->mine($userId)->whereBetween('created_at', [$prevFrom, $prevTo])->count();

        $completedRaw  = $this->rawFor(self::COMPLETED_STAGES);
        $prevCompleted = $completedRaw
            ? $this->mine($userId)->whereBetween('created_at', [$prevFrom, $prevTo])->whereIn('status', $completedRaw)->count()
            : 0;

        $triageRaw = $this->rawFor(self::TRIAGE_STAGES);
        $oldest    = $triageRaw
            ? $this->mine($userId)->whereIn('status', $triageRaw)->orderBy('created_at')->value('created_at')
            : null;

        $rate = $total > 0 ? (int) round($cancelled / $total * 100) : 0;

        return [
            'total' => [
                'value'      => $total,
                'sub'        => 'vs ' . $prevTotal . ' ' . $prevWord,
                'delta'      => $this->delta($total, $prevTotal),
                'delta_tone' => $this->tone($total, $prevTotal),
                'spark'      => $this->sparkline($userId),
            ],
            'pending' => [
                'value'      => $pendingTriage,
                'sub'        => 'Not yet actioned by HoP',
                'delta'      => $oldest ? 'Oldest: ' . $this->shortAge(Carbon::parse($oldest)) . ' ago' : 'Clear',
                'delta_tone' => 'dn',
                'spark'      => $this->sparkline($userId, self::TRIAGE_STAGES),
            ],
            'cancelled' => [
                'value'      => $cancelled,
                'sub'        => 'Cancellation rate',
                'delta'      => $rate . '%',
                'delta_tone' => 'dn',
                'spark'      => $this->sparkline($userId, self::CANCELLED_STAGES),
            ],
            'completed' => [
                'value'      => $completed,
                'sub'        => 'Fully closed this period',
                'delta'      => $this->delta($completed, $prevCompleted),
                'delta_tone' => $this->tone($completed, $prevCompleted),
                'spark'      => $this->sparkline($userId, self::COMPLETED_STAGES),
            ],
        ];
    }

    private function delta(int $now, int $prev): string
    {
        $d = $now - $prev;

        return $d === 0 ? '±0' : ($d > 0 ? '+' . $d : (string) $d);
    }

    private function tone(int $now, int $prev): string
    {
        return $now === $prev ? 'dn' : ($now > $prev ? 'du' : 'dd');
    }

    /** Seven-day daily counts feeding the mini sparkline on each KPI card. */
    private function sparkline(int $userId, ?array $stages = null): array
    {
        $query = $this->mine($userId)->where('created_at', '>=', now()->subDays(6)->startOfDay());

        if ($stages !== null) {
            $raw = $this->rawFor($stages);

            if (! $raw) {
                return array_fill(0, 7, 0);
            }

            $query->whereIn('status', $raw);
        }

        $rows = $query->groupBy('d')
            ->selectRaw('DATE(created_at) AS d, COUNT(*) AS aggregate')
            ->pluck('aggregate', 'd');

        return collect(range(6, 0))
            ->map(fn($i) => (int) $rows->get(now()->subDays($i)->toDateString(), 0))
            ->values()
            ->all();
    }

    /* ═══════════════════════ STATUS DISTRIBUTION ═══════════════════════ */

    private function statusBreakdown(Collection $counts): array
    {
        return collect(self::COLUMNS)->map(fn($meta, $key) => [
            'key'   => $key,
            'label' => $meta['label'],
            'count' => (int) $counts->get($key, 0),
            'color' => $meta['color'],
        ])->values()->all();
    }

    /* ═══════════════════════ TRIAGE QUEUE ═══════════════════════ */

    private function triageQueue(int $userId, int $limit = 8): array
    {
        $raw = $this->rawFor(self::TRIAGE_STAGES);

        if (! $raw) {
            return [];
        }

        return $this->mine($userId)
            ->with(['client:id,company_name', 'category:id,category_name'])
            ->whereIn('status', $raw)
            ->orderBy('created_at') // oldest first
            ->limit($limit)
            ->get()
            ->map(fn(ServiceRequest $sr) => [
                'code'     => $sr->code,
                'client'   => $sr->client?->company_name ?? '—',
                'category' => $sr->category?->category_name ?? '—',
                'priority' => $this->priority($sr->priority_level),
                'wait'     => $this->shortAge($sr->created_at),
                'stale'    => $sr->created_at && abs($sr->created_at->diffInHours(now())) >= 5,
            ])
            ->all();
    }

    /** "18h" / "3d" — the compact style used across the mock-up. */
    private function shortAge(?CarbonInterface $when): string
    {
        if (! $when) {
            return '—';
        }

        $mins = (int) abs($when->diffInMinutes(now()));

        if ($mins < 60) {
            return max(1, $mins) . 'm';
        }

        $hours = (int) abs($when->diffInHours(now()));

        return $hours < 24 ? $hours . 'h' : (int) abs($when->diffInDays(now())) . 'd';
    }

    private function priority(?string $raw): string
    {
        return match ($this->normalise($raw)) {
            'high', 'urgent', 'critical', 'p1' => 'High',
            'low', 'p3'                        => 'Low',
            default                            => 'Medium',
        };
    }

    /** Normalises whatever `warranty_scope` holds into 'IW' / 'OoW'. */
    private function scopeCode($raw): string
    {
        $v = $this->normalise((string) $raw);

        return in_array($v, ['iw', 'in_warranty', 'warranty', 'inwarranty', '1', 'yes', 'true'], true) ? 'IW' : 'OoW';
    }

    /* ═══════════════════════ INTAKE TREND ═══════════════════════ */

    /** Last 6 calendar months, split in-warranty vs out-of-warranty. */
    private function intakeTrend(int $userId): array
    {
        $rows = $this->mine($userId)
            ->where('created_at', '>=', now()->startOfMonth()->subMonths(5))
            ->groupBy('ym', 'warranty_scope')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') AS ym, warranty_scope, COUNT(*) AS aggregate")
            ->get();

        $labels = $iw = $oow = [];

        foreach (range(5, 0) as $i) {
            $month    = now()->startOfMonth()->subMonths($i);
            $bucket   = $rows->where('ym', $month->format('Y-m'));
            $labels[] = $month->format('M');

            $iw[]  = (int) $bucket->filter(fn($r) => $this->scopeCode($r->warranty_scope) === 'IW')->sum('aggregate');
            $oow[] = (int) $bucket->filter(fn($r) => $this->scopeCode($r->warranty_scope) === 'OoW')->sum('aggregate');
        }

        return [
            'labels'       => $labels,
            'in_warranty'  => $iw,
            'out_warranty' => $oow,
        ];
    }

    private function scopeSplit(int $userId, Carbon $from, Carbon $to): array
    {
        $rows = $this->mine($userId)
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('warranty_scope')
            ->selectRaw('warranty_scope, COUNT(*) AS aggregate')
            ->get();

        $iw  = (int) $rows->filter(fn($r) => $this->scopeCode($r->warranty_scope) === 'IW')->sum('aggregate');
        $oow = (int) $rows->filter(fn($r) => $this->scopeCode($r->warranty_scope) === 'OoW')->sum('aggregate');

        return ['iw' => $iw, 'oow' => $oow, 'total' => $iw + $oow];
    }

    private function intakeStats(int $userId, Carbon $from, Carbon $to, int $total): array
    {
        $end  = min($to, now());
        $days = max(1, (int) abs($from->diffInDays($end)) + 1);

        return [
            'total'  => $total,
            'perDay' => round($total / $days, 1),
            'today'  => $this->mine($userId)->whereDate('created_at', today())->count(),
        ];
    }

    /* ═══════════════════════ CLIENTS ═══════════════════════ */

    private function topClients(int $userId, Carbon $from, Carbon $to, int $limit = 6): array
    {
        $rows = $this->mine($userId)
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('client_id')
            ->groupBy('client_id')
            ->selectRaw('client_id, COUNT(*) AS aggregate')
            ->orderByDesc('aggregate')
            ->limit($limit)
            ->get();

        $clients = Client::whereIn('id', $rows->pluck('client_id'))
            ->get(['id', 'company_name', 'created_at'])
            ->keyBy('id');

        return $rows->map(function ($r) use ($clients, $from, $to) {
            $client = $clients->get($r->client_id);

            return [
                'id'     => $r->client_id,
                'name'   => $client->company_name ?? '—',
                'srs'    => (int) $r->aggregate,
                'is_new' => (bool) ($client && $client->created_at && $client->created_at->between($from, $to)),
            ];
        })->all();
    }

    private function newClientCount(int $userId, Carbon $from, Carbon $to): int
    {
        $q = Client::whereBetween('created_at', [$from, $to]);

        if (Schema::hasColumn('clients', 'created_by')) {
            $q->where('created_by', $userId);
        }

        return $q->count();
    }

    private function newSiteCount(Carbon $from, Carbon $to): int
    {
        return Project::whereBetween('created_at', [$from, $to])->count();
    }

    /* ═══════════════════════ CANCELLATIONS ═══════════════════════ */

    private function cancelledList(int $userId, Carbon $from, Carbon $to, int $limit = 4): array
    {
        $raw = $this->rawFor(self::CANCELLED_STAGES);

        if (! $raw) {
            return [];
        }

        return $this->mine($userId)
            ->with('client:id,company_name')
            ->whereIn('status', $raw)
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn(ServiceRequest $sr) => [
                'code'   => $sr->code,
                'client' => $sr->client?->company_name ?? '—',
                'reason' => $sr->hold_reason ?: ucfirst(str_replace('_', ' ', (string) $sr->status)),
            ])
            ->all();
    }

    /* ═══════════════════════ WHATSAPP ═══════════════════════ */

    /**
     * Reads the WhatsApp log table directly and degrades to zeros when it is
     * absent or shaped differently. Point TABLE at your real table and adjust
     * the column names once confirmed — WhatsappLogController owns that schema.
     */
    private function whatsappStats(int $userId, Carbon $from, Carbon $to): array
    {
        $table = 'whatsapp_logs';
        $empty = ['delivered' => 0, 'failed' => 0, 'delivery_rate' => 0, 'failedList' => [], 'available' => false];

        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'service_request_id')) {
            return $empty;
        }

        $srIds = $this->mine($userId)->whereBetween('created_at', [$from, $to])->pluck('id');

        if ($srIds->isEmpty()) {
            return array_merge($empty, ['available' => true]);
        }

        $rows = DB::table($table)
            ->whereIn('service_request_id', $srIds)
            ->groupBy('status')
            ->selectRaw('status, COUNT(*) AS aggregate')
            ->pluck('aggregate', 'status');

        // Anything not explicitly failed counts as delivered.
        $failed    = 0;
        $delivered = 0;

        foreach ($rows as $status => $count) {
            $isFailure = in_array($this->normalise((string) $status), ['failed', 'failure', 'error', 'undelivered'], true);
            $isFailure ? $failed += (int) $count : $delivered += (int) $count;
        }

        $total     = $delivered + $failed;
        $reasonCol = Schema::hasColumn($table, 'failure_reason') ? 'failure_reason'
            : (Schema::hasColumn($table, 'error_message') ? 'error_message' : null);

        $failedList = DB::table($table . ' AS w')
            ->join('service_requests AS s', 's.id', '=', 'w.service_request_id')
            ->leftJoin('clients AS c', 'c.id', '=', 's.client_id')
            ->whereIn('w.service_request_id', $srIds)
            ->whereIn(DB::raw('LOWER(w.status)'), ['failed', 'failure', 'error'])
            ->orderByDesc('w.created_at')
            ->limit(5)
            ->get(array_filter([
                's.id AS sr_id',
                's.created_at AS sr_created',
                'c.company_name',
                $reasonCol ? 'w.' . $reasonCol . ' AS reason' : null,
            ]))
            ->map(fn($r) => [
                'code'   => 'SR-' . Carbon::parse($r->sr_created)->format('Y') . '-' . str_pad((string) $r->sr_id, 5, '0', STR_PAD_LEFT),
                'client' => $r->company_name ?? '—',
                'reason' => ($r->reason ?? null) ?: 'Delivery failed',
            ])
            ->all();

        return [
            'delivered'     => $delivered,
            'failed'        => $failed,
            'delivery_rate' => $total > 0 ? (int) round($delivered / $total * 100) : 0,
            'failedList'    => $failedList,
            'available'     => true,
        ];
    }

    /* ═══════════════════════ KANBAN ═══════════════════════ */

    private function kanban(int $userId): array
    {
        $closedRaw = $this->rawFor(array_merge(self::COMPLETED_STAGES, self::CANCELLED_STAGES));

        // Open work shows in full; closed work is capped to a recent window so
        // the board does not grow unbounded as history accumulates.
        $requests = $this->mine($userId)
            ->with([
                'client:id,company_name',
                'project:id,site_name,site_address',
                'category:id,category_name',
                'assignedUser:id,name',
                'punches',
            ])
            ->where(function ($q) use ($closedRaw) {
                $q->whereNotIn('status', $closedRaw ?: ['__none__'])
                    ->orWhere(function ($q2) use ($closedRaw) {
                        if ($closedRaw) {
                            $q2->whereIn('status', $closedRaw)->where('created_at', '>=', now()->subDays(90));
                        }
                    });
            })
            ->latest()
            ->get()
            ->groupBy(fn(ServiceRequest $sr) => $this->stageOf($sr->status) ?? '__unmapped__');

        $board = [];

        foreach (self::COLUMNS as $key => $meta) {
            $bucket = $requests[$key] ?? collect();

            $board[] = [
                'key'   => $key,
                'label' => $meta['label'],
                'color' => $meta['color'],
                'total' => $bucket->count(),
                'cards' => $bucket->take(self::CARDS_PER_COLUMN)
                    ->map(fn(ServiceRequest $sr) => $this->card($sr))
                    ->values()
                    ->all(),
            ];
        }

        return $board;
    }

    /** Shapes one SR into the payload the slide-in detail panel expects. */
    private function card(ServiceRequest $sr): array
    {
        $tech  = $sr->assignedUser;
        $punch = $sr->punches->sortByDesc('created_at')->first();

        return [
            'code'     => $sr->code,
            'client'   => $sr->client?->company_name ?? '—',
            'site'     => $sr->project?->site_name ?: ($sr->project?->site_address ?: '—'),
            'priority' => $this->priority($sr->priority_level),
            'scope'    => $this->scopeCode($sr->warranty_scope),
            'category' => $sr->category?->category_name ?? '—',
            'logged'   => $this->shortAge($sr->created_at),
            'tech'     => $tech?->name,
            'initials' => $tech?->initials,
            'issue'    => $sr->issue_description ?: 'No description recorded.',
            'punched'  => $this->clockTime($punch?->punch_in_at),
            'punchout' => $this->clockTime($punch?->punch_out_at),
            'erp'      => $sr->erp_quote_ref ?: null,
            'invoice'  => $sr->invoice_code ?: null,
            'amount'   => $sr->invoice_total ? 'AED ' . number_format((float) $sr->invoice_total, 2) : null,
            'rating'   => $sr->performance_score ?: null,
        ];
    }

    /**
     * Formats a punch timestamp as "10:35 AM". The Punch model may or may not
     * cast these columns, so accept a date object or a raw string and never
     * blow up on bad data.
     */
    private function clockTime($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            return ($value instanceof CarbonInterface ? $value : Carbon::parse($value))->format('h:i A');
        } catch (\Throwable) {
            return null;
        }
    }
}
