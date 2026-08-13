<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\Priority;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReworkServiceRequestController extends Controller
{
    /**
     * Statuses that count as "rework".
     */
    private array $assignedStatuses = ['rework'];

    /**
     * Listing page + AJAX fragments (rows / pager) + CSV export.
     */
    public function index(Request $request)
    {
        // CSV export short-circuits before pagination.
        if ($request->query('export') === 'csv') {
            return $this->export($request);
        }

        $assigned = $this->baseQuery($request)
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        // AJAX fragment responses for live filtering.
        if ($request->query('frag') === 'rows') {
            return view('rework_sr', compact('assigned'))->fragment('rows');
        }
        if ($request->query('frag') === 'pager') {
            return view('rework_sr', compact('assigned'))->fragment('pager');
        }

        if ($request->query('frag') === 'hdr') {
            return view('rework_sr', compact('assigned'))->fragment('hdr');
        }

        $stats = $this->stats($request);

        $priorities = Priority::where('status', 1)
            ->orderBy('display_order')
            ->get();

        return view('rework_sr', compact('assigned', 'stats', 'priorities'));
    }

    /**
     * JSON detail for the popup.
     */
    public function show(Request $request, int $id)
    {
        $sr = $this->baseQuery($request, applyFilters: false)
            ->whereKey($id)
            ->firstOrFail();

        return response()->json($this->payload($sr));
    }

    /* ─────────────────────────────────────────────────────────
       QUERY
    ───────────────────────────────────────────────────────── */

    private function baseQuery(Request $request, bool $applyFilters = true)
    {
        $query = ServiceRequest::query()
            ->whereIn('status', $this->assignedStatuses)
            ->with([
                'client',
                'project',
                'assignedUser',
            ]);

        // Tab split: All = reallocate 0, Reallocated = reallocate 1
        $tab = $request->query('tab', 'all');
        $query->where('reallocate', $tab === 'realloc' ? 1 : 0);

        if (! $applyFilters) {
            return $query;
        }

        // Search across SR id, client company, and worker name.
        // $query->when($request->filled('search'), function ($q) use ($request) {
        //     $s = trim($request->query('search'));
        //     $q->where(function ($w) use ($s) {
        //         $w->where('id', 'like', "%{$s}%")
        //             ->orWhereHas('client', fn($c) => $c->where('company_name', 'like', "%{$s}%"))
        //             ->orWhereHas('assignedUser', fn($u) => $u->where('name', 'like', "%{$s}%"));
        //     });
        // });


        $query->when($request->filled('search'), function ($q) use ($request) {
            $s = trim($request->query('search'));

            // Pull the numeric id out of anything shaped like SR-2026-00080 / 00080 / 80
            $idTerm = null;
            if (preg_match('/(\d+)\s*$/', $s, $m)) {
                $idTerm = ltrim($m[1], '0');   // "00080" → "80"
                $idTerm = $idTerm === '' ? '0' : $idTerm;
            }

            $q->where(function ($w) use ($s, $idTerm) {
                if ($idTerm !== null) {
                    $w->where('id', $idTerm);
                }
                $w->orWhereHas('client', fn($c) => $c->where('company_name', 'like', "%{$s}%"))
                    ->orWhereHas('assignedUser', fn($u) => $u->where('name', 'like', "%{$s}%"))
                    ->orWhereHas('reallocateUser', fn($u) => $u->where('name', 'like', "%{$s}%"));
            });
        });

        // Status filter.
        $query->when($request->filled('status'), fn($q) =>
        $q->where('status', $request->query('status')));

        // Priority filter.
        $query->when($request->filled('priority'), fn($q) =>
        $q->where('priority_level', $request->query('priority')));

        // Assigned date range (dispatched_at as the assignment timestamp).
        $query->when($request->filled('date_from'), fn($q) =>
        $q->whereDate('dispatched_at', '>=', $request->query('date_from')));
        $query->when($request->filled('date_to'), fn($q) =>
        $q->whereDate('dispatched_at', '<=', $request->query('date_to')));

        return $query;
    }

    /* ─────────────────────────────────────────────────────────
       STATS
    ───────────────────────────────────────────────────────── */

    private function stats(Request $request): array
    {
        $base = fn() => ServiceRequest::whereIn('status', $this->assignedStatuses);

        return [
            'rework'     => $base()->where('reallocate', 0)->count(),
            'reallocated' => $base()->where('reallocate', 1)->count(),
            'today'      => $base()->whereDate('updated_at', today())->count(),
            'overdue'    => $base()->whereNotNull('eta_at')
                ->where('eta_at', '<', now())
                ->count(),
        ];
    }

    /* ─────────────────────────────────────────────────────────
       PAYLOAD (shared by show() and export())
    ───────────────────────────────────────────────────────── */

    private function payload(ServiceRequest $sr): array
    {
        $srCode = 'SR-' . Carbon::parse($sr->created_at)->format('Y')
            . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT);

        $assignedAt = $sr->updated_at;

        $eta = $sr->eta_at ? Carbon::parse($sr->eta_at) : null;
        $isOverdue = $eta && $eta->isPast();

        $isOow = ($sr->warranty_scope ?? '') === 'Out of Warranty';

        return [
            'id'          => $sr->id,
            'code'        => $srCode,
            'client'      => optional($sr->client)->company_name ?? '—',
            'site'        => optional($sr->project)->site_name ?? '—',
            'worker'      => optional($sr->assignedUser)->name ?? 'Unassigned',
            'issue'       => $sr->issue_description ?? '—',
            'status'      => Str::headline($sr->status ?? '—'),
            'priority'    => $sr->priority_level ?? '—',
            'warranty'    => $isOow ? 'Out of Warranty' : 'In Warranty',
            'contact'     => optional($sr->client)->primary_mobile
                ?? optional($sr->client)->contact_number ?? '—',
            'scheduled'   => $assignedAt->format('d M Y · h:i A'),
            'assigned'    => $assignedAt->format('d M Y · h:i A'),
            'assigned_h'  => $assignedAt->diffForHumans(),
            'sla_due'     => $eta ? $eta->format('d M Y · h:i A') : '—',
            'sla_due_h'   => $eta ? $eta->diffForHumans() : '—',
            'is_overdue'  => $isOverdue,
        ];
    }

    /* ─────────────────────────────────────────────────────────
       CSV EXPORT
    ───────────────────────────────────────────────────────── */

    private function export(Request $request): StreamedResponse
    {
        $rows = $this->baseQuery($request)->latest('updated_at')->get();

        $tabLabel = $request->query('tab', 'all') === 'realloc' ? 'reallocated' : 'all';
        $filename = "rework-srs-{$tabLabel}-" . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'SR Code',
                'Client',
                'Site',
                'Worker',
                'Status',
                'Priority',
                'Warranty',
                'Scheduled (ETA)',
                'Assigned',
                'SLA Due (ETA)',
            ]);

            foreach ($rows as $sr) {
                $assignedAt = $sr->dispatched_at
                    ? Carbon::parse($sr->dispatched_at)
                    : Carbon::parse($sr->updated_at);
                $eta = $sr->eta_at ? Carbon::parse($sr->eta_at) : null;

                fputcsv($out, [
                    'SR-' . Carbon::parse($sr->created_at)->format('Y') . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT),
                    optional($sr->client)->company_name ?? '',
                    optional($sr->project)->site_name ?? '',
                    optional($sr->assignedUser)->name ?? 'Unassigned',
                    Str::headline($sr->status ?? ''),
                    $sr->priority_level ?? '',
                    (($sr->warranty_scope ?? '') === 'Out of Warranty') ? 'Out of Warranty' : 'In Warranty',
                    $eta ? $eta->format('Y-m-d H:i') : '',
                    $assignedAt->format('Y-m-d H:i'),
                    $eta ? $eta->format('Y-m-d H:i') : '',
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
