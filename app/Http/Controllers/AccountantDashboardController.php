<?php

namespace App\Http\Controllers;

use App\Models\Punchitem;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Accountant Dashboard — scoped to what the Accountant role can actually see:
 * Invoice Panel and Expense Ledger. Deliberately NOT a trimmed copy of
 * DashboardController — no SR status/client/service filters, no KPI cards,
 * no charts. Just the two finance tables and a date range.
 */
class AccountantDashboardController extends Controller
{
    private const CURRENCY = 'AED';

    /** How many rows each card's table shows, most-recent first. */
    private const RECENT_LIMIT = 3;

    public function index(Request $request)
    {
        $filters = $this->normaliseRange($request, [
            'range' => $request->input('range', 'month'),
            'from'  => $request->input('from'),
            'to'    => $request->input('to'),
        ]);

        [$start, $end] = $this->resolveRange($filters);

        $invoices   = $this->invoicePanel($start, $end);
        $expenses   = $this->expenseLedger($start, $end);
        $breakdown  = $this->warrantyQuoteBreakdown($start, $end);
        $quotation  = $this->quotationDesk($start, $end);

        return view('accountant.dashboard', [
            'greeting'    => $this->greeting(),
            'greetingSub' => "Here's your finance overview",
            'today'       => now()->format('l, d F Y'),

            'filters'    => $filters,
            'rangeLabel' => $this->rangeLabel($filters),

            'invoiceItems'          => $invoices['items'],
            'invoiceCount'          => $invoices['count'],
            'invoiceTotalFormatted' => $this->money($invoices['total']),

            'expenseItems'          => $expenses['items'],
            'expenseCount'          => $expenses['count'],
            'expenseTotalFormatted' => $this->money($expenses['total']),

            'srBreakdown' => $breakdown,

            'quotationItems' => $quotation['items'],
            'quotationCount' => $quotation['count'],
        ]);
    }

    /* =====================================================================
     | Invoice Panel — every SR invoiced within the selected window.
     | Table shows only the latest RECENT_LIMIT rows; count/total still
     | reflect the full period.
     ===================================================================== */
    private function invoicePanel(Carbon $start, Carbon $end): array
    {
        $rows = ServiceRequest::query()
            ->select('service_requests.*', 'sc.category_name')
            ->leftJoin('service_categories as sc', 'sc.id', '=', 'service_requests.service_type_id')
            ->with('client:id,company_name')
            ->whereNotNull('service_requests.invoice_submitted_at')
            ->whereBetween('service_requests.invoice_submitted_at', [$start, $end])
            ->orderByDesc('service_requests.invoice_submitted_at')
            ->get();

        $items = $rows->take(self::RECENT_LIMIT)->map(fn ($sr) => [
            'ref'      => $this->srRef($sr),
            'client'   => $sr->client->company_name ?? '—',
            'category' => $sr->category_name ?: '—',
            'amount'   => self::CURRENCY . ' ' . number_format((float) $sr->invoice_total, 2),
            'status'   => $sr->status ?: 'Unknown',
            'date'     => $sr->invoice_submitted_at?->format('d M Y') ?: '—',
        ])->values()->all();

        return [
            'items' => $items,
            'count' => $rows->count(),
            'total' => (float) $rows->sum('invoice_total'),
        ];
    }

    /* =====================================================================
     | Expense Ledger — mirrors ServiceRequestController::expenseLedger():
     | line items (Punchitem), tech via serviceRequest.assignedUser, amount
     | is line_total falling back to qty * rate. Windowed here to the
     | dashboard's selected period via the parent punch's punch_out_at.
     | Table shows only the latest RECENT_LIMIT rows; count/total still
     | reflect the full period.
     ===================================================================== */
    private function expenseLedger(Carbon $start, Carbon $end): array
    {
        $lineItems = Punchitem::with(['punch.serviceRequest.assignedUser', 'punch.serviceRequest.client'])
            ->whereHas('punch', fn ($q) => $q->whereBetween('punch_out_at', [$start, $end]))
            ->latest('id')
            ->get();

        $amount = fn ($it) => (float) ($it->line_total ?? ($it->qty * $it->rate));

        $items = $lineItems->take(self::RECENT_LIMIT)->map(function ($it) use ($amount) {
            $sr = $it->punch?->serviceRequest;

            return [
                'ref'    => $sr ? $this->srRef($sr) : '—',
                'client' => $sr?->client?->company_name ?? '—',
                'tech'   => optional($sr?->assignedUser)->name ?? 'Unassigned',
                'amount' => self::CURRENCY . ' ' . number_format($amount($it), 2),
                'date'   => $it->created_at?->format('d M Y') ?: '—',
            ];
        })->values()->all();

        return [
            'items' => $items,
            'count' => $lineItems->count(),
            'total' => $lineItems->sum($amount),
        ];
    }

    /* =====================================================================
     | SR breakdown pie — in warranty / out of warranty / quoted / pending
     | to quote. Four independent counts over the same created_at window as
     | the rest of the page — not a partition of one total, just four quick
     | reads on where the period's SRs stand.
     ===================================================================== */
    private const PENDING_TO_QUOTE_STATUSES = ['Pending', 'Approved', 'Forwarded', 'Additional'];

    private function warrantyQuoteBreakdown(Carbon $start, Carbon $end): array
    {
        $rows = ServiceRequest::query()
            ->select('service_requests.id', 'service_requests.status', 'service_requests.warranty_scope', 'service_requests.project_id')
            ->with('project:id,warranty_end_date')
            ->whereBetween('service_requests.created_at', [$start, $end])
            ->get();

        $inWarranty  = $rows->filter(fn ($sr) => $this->isInWarranty($sr))->count();
        $outWarranty = $rows->count() - $inWarranty;
        $quoted      = $rows->where('status', 'Quoted')->count();
        $pending     = $rows->whereIn('status', self::PENDING_TO_QUOTE_STATUSES)->count();

        return [
            'labels' => ['In Warranty', 'Out of Warranty', 'Quoted', 'Pending to Quote'],
            'values' => [$inWarranty, $outWarranty, $quoted, $pending],
            'colors' => ['#9a8053', '#393837', '#15803d', '#d97706'],
            'rows'   => [
                ['label' => 'In Warranty',       'count' => $inWarranty,  'color' => '#9a8053'],
                ['label' => 'Out of Warranty',   'count' => $outWarranty, 'color' => '#393837'],
                ['label' => 'Quoted',            'count' => $quoted,      'color' => '#15803d'],
                ['label' => 'Pending to Quote',  'count' => $pending,     'color' => '#d97706'],
            ],
            'total' => $rows->count(),
        ];
    }

    /* =====================================================================
     | Quotation Desk — the actual SRs behind the "Pending to Quote" slice
     | of the breakdown pie above. Same statuses, same created_at window,
     | just the row-level detail instead of a count. Table shows only the
     | latest RECENT_LIMIT rows; count still reflects the full period.
     ===================================================================== */
    private function quotationDesk(Carbon $start, Carbon $end): array
    {
        $rows = ServiceRequest::query()
            ->select('service_requests.*', 'sc.category_name')
            ->leftJoin('service_categories as sc', 'sc.id', '=', 'service_requests.service_type_id')
            ->with('client:id,company_name')
            ->whereIn('service_requests.status', self::PENDING_TO_QUOTE_STATUSES)
            ->whereBetween('service_requests.created_at', [$start, $end])
            ->orderByDesc('service_requests.created_at')
            ->get();

        $items = $rows->take(self::RECENT_LIMIT)->map(fn ($sr) => [
            'ref'      => $this->srRef($sr),
            'client'   => $sr->client->company_name ?? '—',
            'category' => $sr->category_name ?: '—',
            'status'   => $sr->status ?: 'Unknown',
            'date'     => $sr->created_at?->format('d M Y') ?: '—',
        ])->values()->all();

        return [
            'items' => $items,
            'count' => $rows->count(),
        ];
    }

    /**
     * Mirrors DashboardController: an explicit warranty_scope wins,
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


    /**
     * Mirrors ServiceRequestController::buildSrRef(). There's no stored
     * 'code' column — the reference is derived from created_at's year plus
     * the zero-padded id, so it never changes once created.
     */
    private function srRef(ServiceRequest $sr): string
    {
        return 'SR-' . ($sr->created_at?->year ?? now()->year)
            . '-' . str_pad((string) $sr->id, 5, '0', STR_PAD_LEFT);
    }

    /* =====================================================================
     | Range handling — same shape as DashboardController, date-only.
     ===================================================================== */
    private function normaliseRange(Request $request, array $filters): array
    {
        if ($request->filled('from') && $request->filled('to')
            && ! in_array($request->input('range'), ['today', 'month', 'quarter'], true)
        ) {
            $filters['range'] = 'custom';
        }

        if (($filters['range'] ?? null) === 'custom'
            && (empty($filters['from']) || empty($filters['to']))
        ) {
            $filters['range'] = 'month';
            $filters['from']  = null;
            $filters['to']    = null;
        }

        return $filters;
    }

    private function resolveRange(array $filters): array
    {
        $range = $filters['range'] ?? 'month';

        if ($range === 'custom' && !empty($filters['from']) && !empty($filters['to'])) {
            try {
                $from = Carbon::parse($filters['from'])->startOfDay();
                $to   = Carbon::parse($filters['to'])->endOfDay();
                if ($from->gt($to)) {
                    [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
                }
                return [$from, $to];
            } catch (\Exception $e) {
                // fall through to default
            }
        }

        return match ($range) {
            'today'   => [today()->startOfDay(), today()->endOfDay()],
            'quarter' => [now()->startOfQuarter(), now()->endOfQuarter()],
            default   => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }

    private function rangeLabel(array $filters): string
    {
        if (($filters['range'] ?? null) === 'custom'
            && !empty($filters['from']) && !empty($filters['to'])
        ) {
            return Carbon::parse($filters['from'])->format('d M')
                . ' – ' . Carbon::parse($filters['to'])->format('d M Y');
        }

        return match ($filters['range'] ?? 'month') {
            'today'   => 'today',
            'quarter' => 'this quarter',
            default   => 'this month',
        };
    }

    private function greeting(): string
    {
        $hour = now()->hour;
        $part = $hour < 12 ? 'morning' : ($hour < 17 ? 'afternoon' : 'evening');
        $name = auth()->user()?->name;

        return $name ? "Good {$part}, " . Str::before($name, ' ') . '!' : "Good {$part}!";
    }

    private function money(float $amount): string
    {
        if (abs($amount) >= 1000) {
            return self::CURRENCY . ' ' . round($amount / 1000, 1) . 'k';
        }

        return self::CURRENCY . ' ' . number_format($amount, 2);
    }
}