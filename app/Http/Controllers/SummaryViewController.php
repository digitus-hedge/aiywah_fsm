<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\Punch;
use App\Models\ServiceRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Models\AlertType;
use App\Models\UserAlertPermission;
/**
 * Public "view page" for the Admin & HoP Daily Summaries — the pages the
 * WhatsApp button opens. No login required (signed-URL protected, see
 * routes/web.php notes), but each is gated to specific roles — anyone
 * else with a technically-valid signed link gets a 403.
 */
class SummaryViewController extends Controller
{
    /** Only these roles may view the Admin summary. */
    private const ADMIN_ALLOWED_ROLE_CODES = ['AD', 'SA'];

    /** Only these roles may view the HoP summary (HoP themselves, plus
     *  Admin/Super Admin oversight). */
    private const HOP_ALLOWED_ROLE_CODES = ['HP'];

    /** Terminal statuses — never "open/carried forward". */
    private const CLOSED_STATUSES = ['Completed', 'Rejected', 'Quote Rejected'];

    /** Only these roles may view the SE summary (SE themselves, plus HoP/Admin oversight). */
    private const SE_ALLOWED_ROLE_CODES = ['SE'];

    private const ML_ALLOWED_ROLE_CODES = ['ML'];
    /** Same bucket groupings as ServiceRequestController::ticketSummary(),
     *  restricted to the non-terminal buckets, so this page's "Open by
     *  Status" chart speaks the same language as the Kanban board. */
    private const STATUS_GROUPS = [
        'pending'  => ['label' => 'Open / Intake',   'color' => '#f5c842', 'statuses' => ['Pending', 'Forwarded', 'Additional', 'Quoted', 'Quote Approved', 'On Hold']],
        'progress' => ['label' => 'In Progress',     'color' => '#9A7B4F', 'statuses' => ['Approved', 'Assigned', 'Accepted', 'In Progress', 'Reschedule']],
        'review'   => ['label' => 'Awaiting Review', 'color' => '#b44fd4', 'statuses' => ['Qc Review', 'Pending Invoice', 'Invoice Submitted']],
        'rework'   => ['label' => 'Rework',          'color' => '#ff3366', 'statuses' => ['Rework']],
    ];

    /**
     * Build the 48-hour signed link to send in the WhatsApp message.
     * Example: SummaryViewController::adminLink($user)
     */
    public static function adminLink(User $user, int $validHours = 24): string
    {
        return URL::temporarySignedRoute(
            'summary.admin.show',
            now()->addHours($validHours),
            ['user' => $user->id]
        );
    }

    /**
     * GET /summary/admin/{user}  (name: summary.admin.show)
     *
     * The 'signed' route middleware already verified the signature +
     * expiry before this method runs. This method additionally checks
     * the ROLE of the user the link was generated for — a signed link
     * only ever proves "this URL wasn't tampered with", not "this
     * person should see an Admin summary".
     */
    public function admin(User $user)
    {
        abort_unless(
            in_array(optional($user->role)->code, self::ADMIN_ALLOWED_ROLE_CODES, true),
            403,
            'This summary is only available to Admin and Super Admin.'
        );

        $summaryDate = Carbon::yesterday();

        $data = $this->buildAdminSummaryData($summaryDate);
        $data['enabled'] = $this->enabledAlertKeys($user, AlertType::ROLE_ADMIN);

        return view('summary.admin', [
            'user'         => $user,
            'summaryDate'  => $summaryDate,
            'data'         => $data,
            'loginUrl'     => route('login'),
        ]);
    }

    /**
     * Build the 48-hour signed link for the HoP summary.
     * Example: SummaryViewController::hopLink($user)
     */
    public static function hopLink(User $user, int $validHours = 24): string
    {
        return URL::temporarySignedRoute(
            'summary.hop.show',
            now()->addHours($validHours),
            ['user' => $user->id]
        );
    }

    /**
     * GET /summary/hop/{user}  (name: summary.hop.show)
     */
    public function hop(User $user)
    {
        abort_unless(
            in_array(optional($user->role)->code, self::HOP_ALLOWED_ROLE_CODES, true),
            403,
            'This summary is only available to the Head of Projects, Admin, and Super Admin.'
        );

        $summaryDate = Carbon::yesterday();

        $data = $this->buildHopSummaryData($summaryDate);
        $data['enabled'] = $this->enabledAlertKeys($user, AlertType::ROLE_HOP);

        return view('summary.hop', [
            'user'        => $user,
            'summaryDate' => $summaryDate,
            'data'        => $data,
            'loginUrl'    => route('login'),
        ]);
    }

    public static function seLink(User $user, int $validHours = 24): string
    {
        return URL::temporarySignedRoute(
            'summary.se.show',
            now()->addHours($validHours),
            ['user' => $user->id]
        );
    }
    /**
     * GET /summary/se/{user}  (name: summary.se.show)
     */
    public function se(User $user)
    {
        abort_unless(
            in_array(optional($user->role)->code, self::SE_ALLOWED_ROLE_CODES, true),
            403,
            'This summary is only available to the Service Engineer, Head of Projects, Admin, and Super Admin.'
        );

        $summaryDate = Carbon::yesterday();

        $data = $this->buildSeSummaryData($summaryDate, $user);
        $data['enabled'] = $this->enabledAlertKeys($user, AlertType::ROLE_SE);

        return view('summary.se', [
            'user'        => $user,
            'summaryDate' => $summaryDate,
            'data'        => $data,
            'loginUrl'    => route('login'),
        ]);
    }

    public static function mlLink(User $user, int $validHours = 24): string
    {
        return URL::temporarySignedRoute(
            'summary.ml.show',
            now()->addHours($validHours),
            ['user' => $user->id]
        );
    }
    /**
 * GET /summary/ml/{user}  (name: summary.ml.show)
 */
public function ml(User $user)
{
    abort_unless(
        in_array(optional($user->role)->code, self::ML_ALLOWED_ROLE_CODES, true),
        403,
        'This summary is only available to the Maintenance Lead, Head of Projects, Admin, and Super Admin.'
    );

    $summaryDate = Carbon::yesterday();

    $data = $this->buildMlSummaryData($summaryDate, $user);
    $data['enabled'] = $this->enabledAlertKeys($user, AlertType::ROLE_ML);

    return view('summary.ml', [
        'user'        => $user,
        'summaryDate' => $summaryDate,
        'data'        => $data,
        'loginUrl'    => route('login'),
    ]);
}
    /**
     * Assembles every number/list the admin summary page needs, from real
     * data. The one exception is 'wa_failures' — I don't have your
     * WhatsApp delivery-log model (the one WhatsappLogController /
     * wa_notification_log reads), so that block is wrapped in a
     * try/catch against a guessed `App\Models\WhatsappLog` shape and
     * degrades to empty rather than ever crashing the page. Send me
     * that model and I'll wire it in properly.
     */
    private function buildAdminSummaryData(Carbon $summaryDate): array
    {
        $srsLoggedYesterday = ServiceRequest::with('project', 'client', 'category')
            ->whereDate('created_at', $summaryDate)
            ->get();

        $srsCompletedYesterday = ServiceRequest::with('client', 'project')
            ->where('status', 'Completed')
            ->whereDate('updated_at', $summaryDate)
            ->get();

        $openSrs = ServiceRequest::whereNotIn('status', self::CLOSED_STATUSES)->get(['id', 'status']);

        $reworkYesterday = ServiceRequest::with('client', 'assignedSe', 'assignedUser')
            ->where('status', 'Rework')
            ->whereDate('qc_reviewed_at', $summaryDate)
            ->get();

        $cancelledYesterday = ServiceRequest::with('client', 'category')
            ->where('status', 'Rejected')
            ->whereDate('updated_at', $summaryDate)
            ->get();

        $punchesYesterday = Punch::with(['items', 'serviceRequest.assignedUser', 'serviceRequest.client'])
            ->whereDate('punch_out_at', $summaryDate)
            ->get();

        $newClients  = Client::whereDate('created_at', $summaryDate)->count();
        $newProjects = Project::whereDate('created_at', $summaryDate)->count();

        // ── SRs Logged — warranty split (as of creation time) ──────────
        $loggedIw = 0;
        $loggedWe = 0;
        foreach ($srsLoggedYesterday as $sr) {
            $warrantyEnd = optional($sr->project)->warranty_end_date;
            $inWarranty  = $warrantyEnd && Carbon::parse($warrantyEnd)->endOfDay()->isAfter($sr->created_at);
            $inWarranty ? $loggedIw++ : $loggedWe++;
        }

        // ── Open SRs — bucketed like the Kanban board ───────────────────
        $openByGroup = collect(self::STATUS_GROUPS)->map(function ($group) use ($openSrs) {
            return $openSrs->whereIn('status', $group['statuses'])->count();
        });

        // ── Field hours + expenses, per ML, from yesterday's punches ────
        $mlAgg = []; // [name => ['hours'=>float,'jobs'=>int,'expense'=>float]]
        $totalFieldHours = 0.0;
        $expenseSubmissions = 0;
        $expensePending = 0;
        $expenseTotal = 0.0;
        $expenseRows = [];

        foreach ($punchesYesterday as $punch) {
            $mlName = optional($punch->serviceRequest?->assignedUser)->name ?? 'Unassigned';

            $hours = 0.0;
            if ($punch->punch_in_at && $punch->punch_out_at) {
                $hours = round(abs($punch->punch_in_at->diffInMinutes($punch->punch_out_at)) / 60, 1);
            }
            $totalFieldHours += $hours;

            $mlAgg[$mlName] ??= ['hours' => 0.0, 'jobs' => 0, 'expense' => 0.0];
            $mlAgg[$mlName]['hours'] += $hours;
            $mlAgg[$mlName]['jobs']  += 1;

            $lineTotal = 0.0;
            $hasMissingReceipt = false;

            foreach ($punch->items as $item) {
                $amt = (float) ($item->line_total ?? ($item->qty * $item->rate));
                $lineTotal += $amt;
                if (empty($item->receipt_path)) {
                    $hasMissingReceipt = true;
                }
                $expenseRows[] = [
                    $punch->serviceRequest ? $this->srRef($punch->serviceRequest) : '—',
                    $mlName,
                    $item->category ?? $item->name ?? '—',
                    'AED ' . number_format($amt, 0),
                    empty($item->receipt_path) ? 'Pending' : 'Submitted',
                ];
            }

            $labour = (float) ($punch->labour_charge ?? 0);
            if ($labour > 0) {
                $lineTotal += $labour;
            }
            $grand = (float) ($punch->grand_total ?? 0);
            if ($grand > 0) {
                $lineTotal = $grand;
            }

            if ($lineTotal > 0) {
                $expenseSubmissions++;
                $expenseTotal += $lineTotal;
                $mlAgg[$mlName]['expense'] += $lineTotal;
                if ($hasMissingReceipt) {
                    $expensePending++;
                }
            }
        }

        $submissionRate = $expenseSubmissions > 0
            ? (int) round((($expenseSubmissions - $expensePending) / $expenseSubmissions) * 100)
            : 0;

        // ── WA Failures — needs your WhatsApp log model to be real ──────
        $waFailures = $this->attemptWaFailures($summaryDate);

        return [
            'quick' => [
                'completed'   => $srsCompletedYesterday->count(),
                'logged'      => $srsLoggedYesterday->count(),
                'rework'      => $reworkYesterday->count(),
                'wa_failures' => count($waFailures),
            ],

            'kpis' => [
                'logged'    => ['value' => $srsLoggedYesterday->count(), 'sub' => "{$loggedIw} IW · {$loggedWe} WE"],
                'completed' => ['value' => $srsCompletedYesterday->count(), 'sub' => 'Closed yesterday'],
                'open'      => ['value' => $openSrs->count(), 'sub' => 'Across ' . $openByGroup->filter()->count() . ' groups'],
                'wa'        => ['value' => count($waFailures), 'sub' => count($waFailures) ? 'Need follow-up' : 'No failures'],
                'rework'    => ['value' => $reworkYesterday->count(), 'sub' => 'QC rejected'],
            ],

            'logged_split' => [
                'total'            => $srsLoggedYesterday->count(),
                'in_warranty'      => $loggedIw,
                'warranty_expired' => $loggedWe,
            ],

            'open_by_status' => [
                'labels' => collect(self::STATUS_GROUPS)->pluck('label')->values()->all(),
                'values' => $openByGroup->values()->all(),
                'colors' => collect(self::STATUS_GROUPS)->pluck('color')->values()->all(),
            ],

            'field_hours' => [
                'total_hours' => round($totalFieldHours, 1),
                'ml_count'    => count($mlAgg),
                'rows'        => collect($mlAgg)->map(function ($row, $name) use ($totalFieldHours) {
                    return [
                        'name'     => $name,
                        'initials' => $this->initials($name),
                        'hours'    => $row['hours'],
                        'pct'      => $totalFieldHours > 0 ? (int) round(($row['hours'] / $totalFieldHours) * 100) : 0,
                    ];
                })->sortByDesc('hours')->values()->all(),
            ],

            'expenses' => [
                'submissions'     => $expenseSubmissions,
                'submission_rate' => $submissionRate,
                'pending'         => $expensePending,
                'total_value'     => (int) round($expenseTotal),
                'by_ml_labels'    => collect($mlAgg)->keys()->map(fn ($n) => $this->initials($n))->values()->all(),
                'by_ml_values'    => collect($mlAgg)->pluck('expense')->values()->all(),
            ],

            'wa_failures' => $waFailures,

            'new_additions' => [
                'clients'  => $newClients,
                'projects' => $newProjects,
            ],

            'cancelled' => $cancelledYesterday->map(fn ($sr) => [
                'sr'     => $this->srRef($sr),
                'client' => optional($sr->client)->company_name ?? '—',
                'reason' => $this->extractCancelReason($sr->internal_remark),
            ])->values()->all(),

            'sheets' => [
                'logged' => [
                    'title' => "SRs Logged Yesterday — {$srsLoggedYesterday->count()} Total",
                    'rows'  => $srsLoggedYesterday->map(fn ($sr) => [
                        $this->srRef($sr),
                        optional($sr->client)->company_name ?? '—',
                        optional($sr->category)->category_name ?? '—',
                        $sr->status,
                        $sr->created_at?->format('d M · h:i A') ?? '—',
                    ])->values()->all(),
                ],
                'completed' => [
                    'title' => "SRs Completed Yesterday — {$srsCompletedYesterday->count()} Total",
                    'rows'  => $srsCompletedYesterday->map(fn ($sr) => [
                        $this->srRef($sr),
                        optional($sr->client)->company_name ?? '—',
                        optional($sr->project)->site_name ?? '—',
                        $sr->invoice_total ? 'AED ' . number_format($sr->invoice_total, 0) : '—',
                        $sr->updated_at?->format('d M · h:i A') ?? '—',
                    ])->values()->all(),
                ],
                'open' => [
                    'title' => "Open SRs Carried Forward — {$openSrs->count()} Total",
                    'rows'  => collect(self::STATUS_GROUPS)->map(function ($group, $key) use ($openByGroup) {
                        return [$group['label'], implode(', ', $group['statuses']), $openByGroup[$key], '', ''];
                    })->values()->all(),
                ],
                'wa' => [
                    'title' => 'WhatsApp Notification Failures — ' . count($waFailures),
                    'rows'  => collect($waFailures)->map(fn ($f) => [
                        $f['sr'], $f['client'], '—', $f['reason'], 'Retry',
                    ])->values()->all(),
                ],
                'rework' => [
                    'title' => "Return Works (QC Rejected) — {$reworkYesterday->count()} Cases",
                    'rows'  => $reworkYesterday->map(fn ($sr) => [
                        $this->srRef($sr),
                        optional($sr->client)->company_name ?? '—',
                        optional($sr->assignedUser)->name ?? optional($sr->assignedSe)->name ?? '—',
                        \Illuminate\Support\Str::limit($sr->rework_notes ?? '—', 80),
                        $sr->reallocate ? 'Reassigned' : 'Pending SE action',
                    ])->values()->all(),
                ],
                'hours' => [
                    'title' => "Technician Field Hours — {$totalFieldHours}h Total",
                    'rows'  => collect($mlAgg)->map(fn ($row, $name) => [
                        $name, $row['hours'] . 'h', $row['jobs'] . ' jobs', 'AED ' . number_format($row['expense'], 0), 'Logged',
                    ])->values()->all(),
                ],
                'expenses' => [
                    'title' => 'Expense Submissions — AED ' . number_format($expenseTotal, 0),
                    'rows'  => $expenseRows,
                ],
                'new' => [
                    'title' => 'New Additions Yesterday',
                    'rows'  => [
                        ['New Clients', (string) $newClients, '', '', ''],
                        ['New Projects', (string) $newProjects, '', '', ''],
                    ],
                ],
                'cancelled' => [
                    'title' => "Cancelled SRs Yesterday — {$cancelledYesterday->count()}",
                    'rows'  => $cancelledYesterday->map(fn ($sr) => [
                        $this->srRef($sr),
                        optional($sr->client)->company_name ?? '—',
                        optional($sr->category)->category_name ?? '—',
                        $this->extractCancelReason($sr->internal_remark),
                        '—',
                    ])->values()->all(),
                ],
            ],
        ];
    }

    /**
     * Assembles the HoP summary from real data. Two sections are
     * best-effort, both wrapped so they can never crash the page:
     *
     * - 'realloc' (category re-allocations): ServiceRequestController's
     *   reallocate() re-assigns the ML, not the category — I don't see a
     *   column or log tracking "category corrected from X to Y" anywhere
     *   in what you shared. Returns empty until you tell me where that's
     *   actually tracked.
     * - 'completed' ratings: guesses at a `client_rating` column on
     *   service_requests (or a related feedback model). Falls back to
     *   "no rating yet" — which the original design already handles
     *   gracefully — if that guess is wrong.
     */
    private function buildHopSummaryData(Carbon $summaryDate): array
    {
        $pendingReview = ServiceRequest::with('client', 'category')
            ->where('status', 'Pending')
            ->orderBy('created_at')
            ->get();

        $approvedYesterday = ServiceRequest::with('client', 'assignedSe')
            ->whereDate('approved_at', $summaryDate)
            ->get();

        $qcPending = ServiceRequest::with('client', 'category', 'assignedUser', 'assignedSe')
            ->where('status', 'Qc Review')
            ->get();

        $reworkYesterday = ServiceRequest::with('client', 'assignedUser', 'assignedSe')
            ->where('status', 'Rework')
            ->whereDate('qc_reviewed_at', $summaryDate)
            ->get();

        $completedYesterday = ServiceRequest::with('client', 'category', 'assignedUser')
            ->where('status', 'Completed')
            ->whereDate('updated_at', $summaryDate)
            ->get();

        // ── Pending Review rows — priority + waiting time ───────────────
        $pendingRows = $pendingReview->map(function ($sr) {
            $hrsAgo = abs((int) $sr->created_at->diffInHours(now(), false));
            $priority = $sr->priority_level ?? 'Medium';
            $priorityClass = match (strtolower($priority)) {
                'high' => 'ps-red',
                'low' => 'ps-grey',
                default => 'ps-amb',
            };
            $borderColor = $hrsAgo >= 14 ? '#ef4444' : ($hrsAgo >= 10 ? '#f59e0b' : '#94a3b8');
            $hrsColor = $hrsAgo >= 14 ? '#b91c1c' : ($hrsAgo >= 10 ? '#b45309' : '#64748b');

            return [
                'sr'             => $this->srRef($sr),
                'client'         => optional($sr->client)->company_name ?? '—',
                'category'       => optional($sr->category)->category_name ?? '—',
                'hrs_ago'        => $hrsAgo . 'h ago',
                'priority'       => $priority,
                'priority_class' => $priorityClass,
                'border'         => $borderColor,
                'hrs_color'      => $hrsColor,
                'logged_by'      => $this->loggedByRoleLabel($sr->logged_by_role),
                'scope'          => $this->srScope($sr),
            ];
        })->values();

        // ── Approved — forward-progress funnel ──────────────────────────
        $dispatchedStatuses = ['Assigned', 'Accepted', 'In Progress', 'Reschedule', 'On Hold', 'Qc Review', 'Rework', 'Pending Invoice', 'Invoice Submitted', 'Completed'];
        $dispatched = $approvedYesterday->whereIn('status', $dispatchedStatuses)->count();
        $awaiting   = $approvedYesterday->where('status', 'Approved')->count();
        $accounts   = $approvedYesterday->whereIn('status', ['Forwarded', 'Additional'])->count();
        $approvedTotal = $approvedYesterday->count();

        $approvedRows = $approvedYesterday->map(function ($sr) use ($dispatchedStatuses) {
            $next = in_array($sr->status, $dispatchedStatuses, true)
                ? 'SE Dispatched'
                : ($sr->status === 'Approved' ? 'Awaiting SE' : 'Routed to Accounts');

            return [
                $this->srRef($sr),
                optional($sr->client)->company_name ?? '—',
                $this->srScope($sr),
                optional($sr->assignedSe)->name ?? '—',
                $next,
            ];
        })->values()->all();

        // ── QC Pending — breakdown by whether the SE holds QC rights ────
        $awaitingHop = 0;
        $seQcOn = 0;
        foreach ($qcPending as $sr) {
            $se = $sr->assignedSe;
            ($se && $se->can_qc_review) ? $seQcOn++ : $awaitingHop++;
        }
        $qcByCategory = $qcPending->groupBy(fn ($sr) => optional($sr->category)->category_name ?? 'Uncategorized')
            ->map->count();

        $qcRows = $qcPending->map(function ($sr) {
            $punch = $sr->punches()->latest('punch_out_at')->first();
            return [
                $this->srRef($sr),
                optional($sr->client)->company_name ?? '—',
                optional($sr->category)->category_name ?? '—',
                optional($sr->assignedUser)->name ?? '—',
                $sr->updated_at?->format('d M · h:i A') ?? '—',
                $punch ? 'Submitted' : '—',
            ];
        })->values()->all();

        // ── Rework rows ───────────────────────────────────────────────
        $reworkRows = $reworkYesterday->map(function ($sr) {
            $action = $sr->reallocate ? 'Reallocated' : 'Pending SE action';
            $actionClass = $sr->reallocate ? 'ps-amb' : 'ps-red';
            return [
                'sr'            => $this->srRef($sr),
                'client'        => optional($sr->client)->company_name ?? '—',
                'ml'            => optional($sr->assignedUser)->name ?? '—',
                'reason'        => $sr->rework_notes ?? '—',
                'action'        => $action,
                'action_class'  => $actionClass,
                'time'          => $sr->qc_reviewed_at?->format('h:i A') ?? '—',
            ];
        })->values();

        // ── Category Re-allocations — no tracked source, honest empty ──
        $reallocRows = collect(); // see method docblock

        // ── Completed + ratings (best-effort) ───────────────────────────
        $ratings = $this->attemptRatings($completedYesterday);

        $completedRows = $completedYesterday->map(function ($sr) use ($ratings) {
            $rating = $ratings[$sr->id] ?? null;
            return [
                'sr'       => $this->srRef($sr),
                'client'   => optional($sr->client)->company_name ?? '—',
                'ml'       => optional($sr->assignedUser)->name ?? '—',
                'category' => optional($sr->category)->category_name ?? '—',
                'scope'    => $this->srScope($sr),
                'stars'    => $rating ? str_repeat('★', $rating) : '',
                'rework'   => 'No', // this cohort is status=Completed, so by definition not currently in rework
            ];
        })->values();

        $ratedValues = collect($ratings)->values();
        $avgRating = $ratedValues->count() ? round($ratedValues->avg(), 1) : 0;
        $ratingDistribution = [0, 0, 0, 0, 0];
        foreach ($ratedValues as $r) {
            if ($r >= 1 && $r <= 5) {
                $ratingDistribution[$r - 1]++;
            }
        }

        return [
            'quick' => [
                'pending'   => $pendingReview->count(),
                'approved'  => $approvedTotal,
                'qc'        => $qcPending->count(),
                'completed' => $completedYesterday->count(),
            ],

            'kpis' => [
                'pending'   => ['value' => $pendingReview->count(), 'sub' => 'Not yet triaged'],
                'approved'  => ['value' => $approvedTotal, 'sub' => 'Moved forward'],
                'qc'        => ['value' => $qcPending->count(), 'sub' => 'Carried forward'],
                'rework'    => ['value' => $reworkYesterday->count(), 'sub' => 'QC rejected'],
                'realloc'   => ['value' => $reallocRows->count(), 'sub' => 'Category fixed'],
                'completed' => ['value' => $completedYesterday->count(), 'sub' => 'With ratings'],
            ],

            'pending' => ['rows' => $pendingRows->all()],

            'approved' => [
                'total' => $approvedTotal,
                'funnel' => [
                    'dispatched'     => $dispatched,
                    'dispatched_pct' => $approvedTotal > 0 ? (int) round($dispatched / $approvedTotal * 100) : 0,
                    'awaiting'       => $awaiting,
                    'awaiting_pct'   => $approvedTotal > 0 ? (int) round($awaiting / $approvedTotal * 100) : 0,
                    'accounts'       => $accounts,
                    'accounts_pct'   => $approvedTotal > 0 ? (int) round($accounts / $approvedTotal * 100) : 0,
                ],
            ],

            'qc' => [
                'total'              => $qcPending->count(),
                'awaiting_hop'       => $awaitingHop,
                'se_qc_on'           => $seQcOn,
                'by_category_labels' => $qcByCategory->keys()->values()->all(),
                'by_category_values' => $qcByCategory->values()->all(),
            ],

            'rework' => [
                'total' => $reworkYesterday->count(),
                'rows'  => $reworkRows->all(),
            ],

            'realloc' => [
                'total' => $reallocRows->count(),
                'rows'  => $reallocRows->all(),
            ],

            'completed' => [
                'total'                => $completedYesterday->count(),
                'avg_rating'           => $avgRating,
                'avg_stars'            => str_repeat('★', (int) round($avgRating)),
                'rated_count'          => $ratedValues->count(),
                'rating_distribution'  => $ratingDistribution,
                'rows'                 => $completedRows->all(),
            ],

            'sheets' => [
                'pending' => [
                    'title' => "Pending Review — {$pendingReview->count()} SRs Awaiting Triage",
                    'rows'  => $pendingRows->map(fn ($r) => [
                        $r['sr'], $r['client'], $r['category'], $r['scope'], $r['priority'], $r['logged_by'], $r['hrs_ago'],
                    ])->all(),
                ],
                'approved' => [
                    'title' => "Approved SRs Yesterday — {$approvedTotal} Total",
                    'rows'  => $approvedRows,
                ],
                'qc' => [
                    'title' => "QC Reviews Pending — {$qcPending->count()} Carried Forward",
                    'rows'  => $qcRows,
                ],
                'rework' => [
                    'title' => "Rework Cases Yesterday — {$reworkYesterday->count()} Rejected",
                    'rows'  => $reworkRows->map(fn ($r) => [
                        $r['sr'], $r['client'], $r['ml'], $r['reason'], $r['action'], $r['time'],
                    ])->all(),
                ],
                'realloc' => [
                    'title' => "Category Re-allocations — {$reallocRows->count()} Corrections",
                    'rows'  => $reallocRows->all(),
                ],
                'completed' => [
                    'title' => "Completed SRs Yesterday — {$completedYesterday->count()} Total",
                    'rows'  => $completedRows->map(fn ($r) => [
                        $r['sr'], $r['client'], $r['ml'], $r['scope'], $r['stars'] ?: 'No rating yet', $r['rework'],
                    ])->all(),
                ],
            ],
        ];
    }

    /**
     * Best-effort per-SR star rating for a "Completed" cohort. Guesses at
     * either a `client_rating` column directly on service_requests, or a
     * related `feedback`/`clientFeedback` relation with a `rating` column.
     * Returns [sr_id => int(1-5)] for whichever SRs actually have one;
     * anything that doesn't resolve (wrong guess, or genuinely no rating
     * yet) is simply absent from the map — never throws.
     */
    private function attemptRatings($completedSrs): array
    {
        $ratings = [];

        foreach ($completedSrs as $sr) {
            try {
                if (isset($sr->client_rating) && $sr->client_rating) {
                    $ratings[$sr->id] = (int) $sr->client_rating;
                    continue;
                }
            } catch (\Throwable $e) {
                // column doesn't exist — fall through to relation attempt
            }

            foreach (['feedback', 'clientFeedback'] as $relation) {
                try {
                    if (method_exists($sr, $relation)) {
                        $related = $sr->{$relation};
                        if ($related && isset($related->rating) && $related->rating) {
                            $ratings[$sr->id] = (int) $related->rating;
                            break;
                        }
                    }
                } catch (\Throwable $e) {
                    // relation/column doesn't exist — leave unrated
                }
            }
        }

        return $ratings;
    }

    /** Same role-code → label mapping as ServiceRequest::loggedByRoleLabel(). */
    private function loggedByRoleLabel(?string $code): string
    {
        return match ($code) {
            'FD' => 'Front Desk',
            'HP' => 'Head of Projects',
            'SE' => 'Service Engineer',
            'AC' => 'Accounts / AR',
            'AD' => 'Admin',
            'SA' => 'Super Admin',
            default => $code ?? '—',
        };
    }

    /** Same in/out-of-warranty scope logic as ServiceRequestController::srScope(). */
    private function srScope(ServiceRequest $sr): string
    {
        if (!empty($sr->warranty_scope)) {
            return $sr->warranty_scope === 'oow' ? 'WE' : 'IW';
        }
        return $sr->status === 'Forwarded' ? 'WE' : 'IW';
    }

    /**
     * Best-effort WhatsApp failure lookup. Guesses at a WhatsappLog model
     * shaped like: service_request_id, status ('failed'), reason/error
     * message, created_at. If that model/columns don't exist, this just
     * logs a warning and returns an empty list — it will NEVER break the
     * page. Tell me the real model and I'll replace this with a proper
     * query (and drop the try/catch).
     */
    private function attemptWaFailures(Carbon $summaryDate): array
    {
        if (!class_exists(\App\Models\WhatsappLog::class)) {
            return [];
        }

        try {
            return \App\Models\WhatsappLog::with('serviceRequest.client')
                ->where('status', 'failed')
                ->whereDate('created_at', $summaryDate)
                ->get()
                ->map(function ($log) {
                    $sr = $log->serviceRequest;
                    return [
                        'sr'     => $sr ? $this->srRef($sr) : ('#' . $log->service_request_id),
                        'client' => optional(optional($sr)->client)->company_name ?? '—',
                        'reason' => $log->reason ?? $log->error_message ?? 'Delivery failed',
                    ];
                })
                ->values()
                ->all();
        } catch (\Throwable $e) {
            Log::warning('SummaryViewController: WA failures query skipped (model/columns unconfirmed)', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    private function srRef(ServiceRequest $sr): string
    {
        return 'SR-' . ($sr->created_at?->year ?? now()->year)
            . '-' . str_pad((string) $sr->id, 5, '0', STR_PAD_LEFT);
    }

    private function initials(?string $name): string
    {
        if (!$name) {
            return '??';
        }
        $parts = preg_split('/\s+/', trim($name));
        return strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
    }
    /**
 * Enabled alert-type keys for this user, keyed by AlertType 'key'
 * (e.g. 'admin_logged', 'se_rework'). A missing row in
 * user_alert_permissions defaults to enabled=true — same convention
 * UserAlertPermissionSeeder and MasterController::summaryAlertPermissions()
 * already use, so a user who's never touched Master Settings still gets
 * everything.
 */
private function enabledAlertKeys(User $user, string $roleSlug): array
{
    $alertTypes = AlertType::forRole($roleSlug)->get();

    if ($alertTypes->isEmpty()) {
        return [];
    }

    $existing = UserAlertPermission::where('user_id', $user->id)
        ->whereIn('alert_type_id', $alertTypes->pluck('id'))
        ->pluck('is_enabled', 'alert_type_id');

    return $alertTypes->mapWithKeys(function ($type) use ($existing) {
        $enabled = $existing->has($type->id) ? (bool) $existing[$type->id] : true;
        return [$type->key => $enabled];
    })->all();
}
    /**
     * reject() on ServiceRequestController appends
     * "\nRejection reason: <reason>" to internal_remark. Pull that back out
     * for display; falls back to a generic label if the pattern isn't found
     * (e.g. cancelled some other way).
     */
    private function extractCancelReason(?string $internalRemark): string
    {
        if ($internalRemark && preg_match('/Rejection reason:\s*(.+)/s', $internalRemark, $m)) {
            return trim(explode("\n", $m[1])[0]);
        }
        return 'No reason recorded';
    }

   private function buildSeSummaryData(Carbon $summaryDate, User $seUser): array
{
    $fieldStatuses = ['Accepted', 'In Progress', 'Reschedule', 'On Hold'];

    // ── Assigned yesterday by HoP → dispatched vs pending ────────────
    $assignedYesterday = ServiceRequest::with('client', 'category', 'assignedUser')
        ->where('assigned_se', $seUser->id)
        ->whereDate('approved_at', $summaryDate)
        ->get();

    $dispatchedCount = $assignedYesterday->whereNotNull('dispatched_at')->count();
    $pendingCount    = $assignedYesterday->whereNull('dispatched_at')->count();

    // ── Rework cases currently sitting in their queue ────────────────
    $reworkQueue = ServiceRequest::with('client', 'assignedUser')
        ->where('assigned_se', $seUser->id)
        ->where('status', 'Rework')
        ->orderBy('qc_reviewed_at')
        ->get();

    // ── Average dispatch time yesterday — approved_at → dispatched_at ─
    $durations = [];
    foreach ($assignedYesterday->whereNotNull('dispatched_at') as $sr) {
        if ($sr->approved_at) {
            $mins = abs(Carbon::parse($sr->approved_at)->diffInMinutes($sr->dispatched_at));
            if ($mins > 0) {
                $durations[] = $mins;
            }
        }
    }
    $avgDispatchMinutes = count($durations)
        ? (int) round(array_sum($durations) / count($durations))
        : null;

    // ── SRs currently in field — live carry-forward count ────────────
    $inField = ServiceRequest::where('assigned_se', $seUser->id)
        ->whereIn('status', $fieldStatuses)
        ->get(['id', 'status']);

    // ── QC reviews done yesterday (only if this SE has QC rights) ────
    $qcApproved = 0;
    $qcRejected = 0;

    if ($seUser->can_qc_review) {
        $qcDoneYesterday = ServiceRequest::where('qc_reviewed_by', $seUser->id)
            ->whereDate('qc_reviewed_at', $summaryDate)
            ->get(['id', 'status']);

        $qcApproved = $qcDoneYesterday->whereIn('status', ['Completed', 'Pending Invoice', 'Invoice Submitted'])->count();
        $qcRejected = $qcDoneYesterday->where('status', 'Rework')->count();
    }

    return [
        'quick' => [
            'assigned'   => $assignedYesterday->count(),
            'dispatched' => $dispatchedCount,
            'pending'    => $pendingCount,
            'rework'     => $reworkQueue->count(),
            'in_field'   => $inField->count(),
        ],

        'kpis' => [
            'assigned'   => ['value' => $assignedYesterday->count(), 'sub' => 'From HoP yesterday'],
            'dispatched' => ['value' => $dispatchedCount, 'sub' => $assignedYesterday->count() > 0
                ? round($dispatchedCount / $assignedYesterday->count() * 100) . '% of assigned'
                : 'No SRs assigned'],
            'pending'    => ['value' => $pendingCount, 'sub' => 'Not yet dispatched'],
            'rework'     => ['value' => $reworkQueue->count(), 'sub' => 'In queue'],
            'avg_time'   => ['value' => $avgDispatchMinutes !== null ? $this->formatMinutes($avgDispatchMinutes) : '—', 'sub' => 'Avg dispatch time'],
            'in_field'   => ['value' => $inField->count(), 'sub' => 'Carried forward'],
        ],

        'qc_enabled' => (bool) $seUser->can_qc_review,
        'qc' => [
            'approved' => $qcApproved,
            'rejected' => $qcRejected,
            'total'    => $qcApproved + $qcRejected,
        ],

        'sheets' => [
            'assigned' => [
                'title' => "SRs Assigned Yesterday — {$assignedYesterday->count()} Total",
                'rows'  => $assignedYesterday->map(fn ($sr) => [
                    $this->srRef($sr),
                    optional($sr->client)->company_name ?? '—',
                    optional($sr->category)->category_name ?? '—',
                    $sr->dispatched_at ? 'Dispatched — ' . optional($sr->assignedUser)->name : 'Pending Dispatch',
                    $sr->approved_at?->format('d M · h:i A') ?? '—',
                ])->values()->all(),
            ],
            'rework' => [
                'title' => "Rework Queue — {$reworkQueue->count()} Cases",
                'rows'  => $reworkQueue->map(fn ($sr) => [
                    $this->srRef($sr),
                    optional($sr->client)->company_name ?? '—',
                    optional($sr->assignedUser)->name ?? '—',
                    \Illuminate\Support\Str::limit($sr->rework_notes ?? '—', 80),
                    $sr->qc_reviewed_at?->format('d M · h:i A') ?? '—',
                ])->values()->all(),
            ],
            'in_field' => [
                'title' => "SRs Currently In Field — {$inField->count()}",
                'rows'  => $inField->map(fn ($sr) => [
                    $this->srRef($sr), $sr->status, '', '', '',
                ])->values()->all(),
            ],
        ],
    ];
}
private function formatMinutes(int $minutes): string
{
    if ($minutes < 60) {
        return $minutes . 'm';
    }
    $h = intdiv($minutes, 60);
    $m = $minutes % 60;
    return $m > 0 ? "{$h}h {$m}m" : "{$h}h";
}
private function buildMlSummaryData(Carbon $summaryDate, User $mlUser): array
{
    $waitingStatuses = ['Assigned', 'Accepted', 'In Progress', 'Reschedule', 'On Hold'];

    // ── Jobs completed yesterday ──────────────────────────────────
    $completedYesterday = ServiceRequest::with('client', 'category', 'project')
        ->where('assigned_user_id', $mlUser->id)
        ->where('status', 'Completed')
        ->whereDate('updated_at', $summaryDate)
        ->get();

    // ── Jobs punched in/out yesterday ─────────────────────────────
    $punchesYesterday = Punch::with(['items', 'serviceRequest.client'])
        ->whereHas('serviceRequest', fn ($q) => $q->where('assigned_user_id', $mlUser->id))
        ->whereDate('punch_out_at', $summaryDate)
        ->get();

    // ── Rework from previous submissions ──────────────────────────
    $reworkYesterday = ServiceRequest::with('client')
        ->where('assigned_user_id', $mlUser->id)
        ->where('status', 'Rework')
        ->whereDate('qc_reviewed_at', $summaryDate)
        ->get();

    // ── Pending jobs — live carry-forward, waiting today ──────────
    $pendingToday = ServiceRequest::with('client', 'category')
        ->where('assigned_user_id', $mlUser->id)
        ->whereIn('status', $waitingStatuses)
        ->orderBy('eta_at')
        ->get();

    // ── Expenses — submitted vs pending receipts ────────────────────
    $expenseSubmissions = 0;
    $expensePending     = 0;
    $expenseTotal       = 0.0;
    $expenseRows        = [];

    foreach ($punchesYesterday as $punch) {
        $lineTotal = 0.0;
        $hasMissingReceipt = false;

        foreach ($punch->items as $item) {
            $amt = (float) ($item->line_total ?? ($item->qty * $item->rate));
            $lineTotal += $amt;
            if (empty($item->receipt_path)) {
                $hasMissingReceipt = true;
            }
            $expenseRows[] = [
                $punch->serviceRequest ? $this->srRef($punch->serviceRequest) : '—',
                $item->category ?? $item->name ?? '—',
                'AED ' . number_format($amt, 0),
                empty($item->receipt_path) ? 'Pending' : 'Submitted',
            ];
        }

        $labour = (float) ($punch->labour_charge ?? 0);
        if ($labour > 0) {
            $lineTotal += $labour;
        }
        $grand = (float) ($punch->grand_total ?? 0);
        if ($grand > 0) {
            $lineTotal = $grand;
        }

        if ($lineTotal > 0) {
            $expenseSubmissions++;
            $expenseTotal += $lineTotal;
            if ($hasMissingReceipt) {
                $expensePending++;
            }
        }
    }

    $submissionRate = $expenseSubmissions > 0
        ? (int) round((($expenseSubmissions - $expensePending) / $expenseSubmissions) * 100)
        : 0;

    // ── Client ratings on jobs closed ────────────────────────────
    $ratings = $this->attemptRatings($completedYesterday);
    $ratedValues = collect($ratings)->values();
    $avgRating = $ratedValues->count() ? round($ratedValues->avg(), 1) : 0;

    return [
        'quick' => [
            'completed' => $completedYesterday->count(),
            'punches'   => $punchesYesterday->count(),
            'rework'    => $reworkYesterday->count(),
            'pending'   => $pendingToday->count(),
        ],

        'kpis' => [
            'completed' => ['value' => $completedYesterday->count(), 'sub' => 'Closed yesterday'],
            'punches'   => ['value' => $punchesYesterday->count(), 'sub' => 'Jobs punched'],
            'rework'    => ['value' => $reworkYesterday->count(), 'sub' => 'QC rejected'],
            'pending'   => ['value' => $pendingToday->count(), 'sub' => 'Waiting today'],
            'expenses'  => ['value' => $expenseSubmissions, 'sub' => $submissionRate . '% receipts in'],
            'rating'    => ['value' => $avgRating ?: '—', 'sub' => $ratedValues->count() . ' rated'],
        ],

        'expenses' => [
            'submissions'     => $expenseSubmissions,
            'submission_rate' => $submissionRate,
            'pending'         => $expensePending,
            'total_value'     => (int) round($expenseTotal),
        ],

        'ratings' => [
            'avg'          => $avgRating,
            'avg_stars'    => str_repeat('★', (int) round($avgRating)),
            'rated_count'  => $ratedValues->count(),
        ],

        'sheets' => [
            'completed' => [
                'title' => "Jobs Completed Yesterday — {$completedYesterday->count()} Total",
                'rows'  => $completedYesterday->map(function ($sr) use ($ratings) {
                    $rating = $ratings[$sr->id] ?? null;
                    return [
                        $this->srRef($sr),
                        optional($sr->client)->company_name ?? '—',
                        optional($sr->category)->category_name ?? '—',
                        $rating ? str_repeat('★', $rating) : 'No rating yet',
                        $sr->updated_at?->format('d M · h:i A') ?? '—',
                    ];
                })->values()->all(),
            ],
            'punches' => [
                'title' => "Jobs Punched In/Out Yesterday — {$punchesYesterday->count()} Total",
                'rows'  => $punchesYesterday->map(function ($punch) {
                    $sr = $punch->serviceRequest;
                    return [
                        $sr ? $this->srRef($sr) : '—',
                        $sr ? (optional($sr->client)->company_name ?? '—') : '—',
                        $punch->punch_in_at?->format('h:i A') ?? '—',
                        $punch->punch_out_at?->format('h:i A') ?? '—',
                    ];
                })->values()->all(),
            ],
            'rework' => [
                'title' => "Rework — Previous Submissions Rejected — {$reworkYesterday->count()}",
                'rows'  => $reworkYesterday->map(fn ($sr) => [
                    $this->srRef($sr),
                    optional($sr->client)->company_name ?? '—',
                    \Illuminate\Support\Str::limit($sr->rework_notes ?? '—', 80),
                    $sr->qc_reviewed_at?->format('d M · h:i A') ?? '—',
                ])->values()->all(),
            ],
            'pending' => [
                'title' => "Pending Jobs — {$pendingToday->count()} Waiting Today",
                'rows'  => $pendingToday->map(fn ($sr) => [
                    $this->srRef($sr),
                    optional($sr->client)->company_name ?? '—',
                    optional($sr->category)->category_name ?? '—',
                    $sr->status,
                    $sr->eta_at?->format('d M · h:i A') ?? 'Not scheduled',
                ])->values()->all(),
            ],
            'expenses' => [
                'title' => 'Expense Submissions — AED ' . number_format($expenseTotal, 0),
                'rows'  => $expenseRows,
            ],
        ],
    ];
}
}