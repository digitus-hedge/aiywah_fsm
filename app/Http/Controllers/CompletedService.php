<?php

namespace App\Http\Controllers;

use App\Models\Punch;
use App\Models\ServiceRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CompletedService extends Controller
{
    /**
     * Statuses that count as "completed". Adjust to match your schema.
     * Kept as an array so a job closed as 'completed' or 'closed' both show.
     */
    private array $completedStatuses = ['completed', 'closed', 'done'];

    /**
     * Listing page + AJAX fragments (rows / pager) + CSV export.
     */
    public function index(Request $request)
    {
        // CSV export short-circuits before pagination.
        if ($request->query('export') === 'csv') {
            return $this->export($request);
        }

        $completed = $this->baseQuery($request)
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        // AJAX fragment responses for live filtering (matches the blade's fetch calls).
        if ($request->query('frag') === 'rows') {
            return view('completed_sr', compact('completed'))->fragment('rows');
        }
        if ($request->query('frag') === 'pager') {
            return view('completed_sr', compact('completed'))->fragment('pager');
        }

        $stats = $this->stats();

        return view('completed_sr', compact('completed', 'stats'));
    }

    /**
     * JSON detail for the popup. The blade currently reads the row's data-sr
     * payload directly, but this endpoint lets you fetch fresh detail on demand
     * (e.g. wire the View button to hit /completed-sr/{id} instead of inline JSON).
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
            ->whereIn('status', $this->completedStatuses)
            ->with([
                'client',
                'project',
                'punch.user',   // hasOne latest punch — see note; falls back gracefully if hasMany
            ]);

        if (! $applyFilters) {
            return $query;
        }

        // Search across SR id, client company, and worker name.
        $query->when($request->filled('search'), function ($q) use ($request) {
            $s = trim($request->query('search'));
            $q->where(function ($w) use ($s) {
                $w->where('id', 'like', "%{$s}%")
                  ->orWhereHas('client', fn ($c) => $c->where('company_name', 'like', "%{$s}%"))
                  ->orWhereHas('punch.user', fn ($u) => $u->where('name', 'like', "%{$s}%"));
            });
        });

        // Warranty scope filter.
        $query->when($request->filled('warranty'), function ($q) use ($request) {
            if ($request->query('warranty') === 'oow') {
                $q->where(fn ($w) => $w->where('is_oow', true)
                                       ->orWhere('warranty_status', 'Out of Warranty'));
            } elseif ($request->query('warranty') === 'warranty') {
                $q->where(fn ($w) => $w->where('is_oow', false)
                                       ->orWhereNull('is_oow'));
            }
        });

        // Completion date range (using updated_at as the close timestamp).
        $query->when($request->filled('date_from'), fn ($q) =>
            $q->whereDate('updated_at', '>=', $request->query('date_from')));
        $query->when($request->filled('date_to'), fn ($q) =>
            $q->whereDate('updated_at', '<=', $request->query('date_to')));

        return $query;
    }

    /* ─────────────────────────────────────────────────────────
       STATS
    ───────────────────────────────────────────────────────── */

    private function stats(): array
    {
        $completedIds = ServiceRequest::whereIn('status', $this->completedStatuses)->pluck('id');

        return [
            'total'     => $completedIds->count(),
            'thisMonth' => ServiceRequest::whereIn('status', $this->completedStatuses)
                                ->whereMonth('updated_at', now()->month)
                                ->whereYear('updated_at', now()->year)
                                ->count(),
            'revenue'   => Punch::whereIn('service_request_id', $completedIds)->sum('grand_total'),
            'signed'    => Punch::whereIn('service_request_id', $completedIds)
                                ->whereNotNull('customer_signature_path')
                                ->count(),
        ];
    }

    /* ─────────────────────────────────────────────────────────
       PAYLOAD (shared by show() and export())
    ───────────────────────────────────────────────────────── */

    /**
     * Resolve the relevant punch for an SR, tolerating either a hasOne 'punch'
     * relation or a hasMany 'punches' collection.
     */
    private function resolvePunch(ServiceRequest $sr): ?Punch
    {
        if ($sr->relationLoaded('punch') && $sr->punch) {
            return $sr->punch;
        }
        if ($sr->relationLoaded('punches')) {
            return $sr->punches->last();
        }
        // Last resort: query directly.
        return Punch::where('service_request_id', $sr->id)
            ->orderByDesc('punch_out_at')
            ->first();
    }

    private function payload(ServiceRequest $sr): array
    {
        $punch = $this->resolvePunch($sr);

        $srCode = 'SR-' . Carbon::parse($sr->created_at)->format('Y')
                . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT);

        $completedAt = $punch && $punch->punch_out_at
            ? Carbon::parse($punch->punch_out_at)
            : Carbon::parse($sr->updated_at);

        $isOow = ($sr->is_oow ?? false)
              || (($sr->warranty_status ?? '') === 'Out of Warranty');

        return [
            'id'          => $sr->id,
            'code'        => $srCode,
            'client'      => optional($sr->client)->company_name ?? '—',
            'site'        => optional($sr->project)->site_name ?? '—',
            'worker'      => optional($punch?->user)->name ?? 'Unassigned',
            'issue'       => $sr->issue_description ?? '—',
            'warranty'    => $isOow ? 'Out of Warranty' : 'In Warranty',
            'contact'     => optional($sr->client)->primary_mobile
                             ?? optional($sr->client)->contact_number ?? '—',
            'cust_name'   => $punch->customer_name ?? '—',
            'summary'     => $punch->completion_summary ?? '—',
            'punch_in'    => $punch && $punch->punch_in_at
                             ? Carbon::parse($punch->punch_in_at)->format('d M Y · h:i A') : '—',
            'punch_out'   => $punch && $punch->punch_out_at
                             ? Carbon::parse($punch->punch_out_at)->format('d M Y · h:i A') : '—',
            'duration'    => $punch->duration_label ?? '—',
            'materials'   => number_format($punch->materials_subtotal ?? 0, 2),
            'labour'      => number_format($punch->labour_charge ?? 0, 2),
            'total'       => number_format($punch->grand_total ?? 0, 2),
            'completed'   => $completedAt->format('d M Y · h:i A'),
            'completed_h' => $completedAt->diffForHumans(),
            'before'      => $punch && $punch->start_photo_path
                             ? Storage::url($punch->start_photo_path) : null,
            'after'       => $punch && $punch->finish_photo_path
                             ? Storage::url($punch->finish_photo_path) : null,
            'signature'   => $punch && $punch->customer_signature_path
                             ? Storage::url($punch->customer_signature_path) : null,
        ];
    }

    /* ─────────────────────────────────────────────────────────
       CSV EXPORT
    ───────────────────────────────────────────────────────── */

    private function export(Request $request): StreamedResponse
    {
        $rows = $this->baseQuery($request)->latest('updated_at')->get();

        $filename = 'completed-srs-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'SR Code', 'Client', 'Site', 'Worker', 'Warranty',
                'Signed By', 'Punch In', 'Punch Out', 'Duration',
                'Materials', 'Labour', 'Grand Total', 'Completed',
            ]);

            foreach ($rows as $sr) {
                $p = $this->payload($sr);
                fputcsv($out, [
                    $p['code'], $p['client'], $p['site'], $p['worker'], $p['warranty'],
                    $p['cust_name'], $p['punch_in'], $p['punch_out'], $p['duration'],
                    $p['materials'], $p['labour'], $p['total'], $p['completed'],
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}