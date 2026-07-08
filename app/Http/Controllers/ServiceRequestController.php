<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\Client;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class ServiceRequestController extends Controller
{
    public function create()
    {
        // Service types are hardcoded in the blade (ids 1 & 2), no DB needed

        $categories = ServiceCategory::get();

        return view('sr_registration', compact('categories'));
    }

    public function sr_explorer(Request $request)
    {
        // Service types are hardcoded in the blade (ids 1 & 2), no DB needed

        // $sr_explorer = ServiceRequest::with([
        //     'client',
        //     'project',
        //     'category',
        //     'assignedUser'
        // ])
        //     ->latest()
        //     ->get();


        $statusMap = [
            'Pending'   => 'sb-pending',
            'Approved'  => 'sb-approved',
            'Forwarded' => 'sb-forwarded',
            'Rejected'  => 'sb-cancelled',
            'Assigned'  => 'sb-assigned',
        ];
        $statuses = array_keys($statusMap);

        $query = ServiceRequest::with(['client', 'project', 'assignedUser']);

        // ---- Filters ----
        if ($request->filled('search')) {
            $s = trim($request->search);

            // If they typed a formatted code like "SR-2026-00002", pull out the numeric id
            $numericId = null;
            if (preg_match('/(\d+)\s*$/', $s, $m)) {
                $numericId = (int) ltrim($m[1], '0');   // "00002" → 2
            }

            $query->where(function ($q) use ($s, $numericId) {
                $q->orWhereHas('client', fn($c) => $c->where('company_name', 'like', "%{$s}%"))
                    ->orWhere('project_site', 'like', "%{$s}%")
                    ->orWhereHas('project', fn($p) => $p->where('site_name', 'like', "%{$s}%"));

                if ($numericId !== null) {
                    $q->orWhere('id', $numericId);
                }
            });
        }


        if ($request->filled('status'))    $query->where('status', $request->status);
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('created_at', '<=', $request->date_to);

        // ---- Export current filtered view ----
        if ($request->export === 'csv') {
            $rows = (clone $query)->latest()->get();
            $headers = [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename="service_requests.csv"',
            ];
            return response()->stream(function () use ($rows) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['SR ID', 'Client', 'Site', 'Assigned To', 'Status', 'Created']);
                foreach ($rows as $sr) {
                    fputcsv($out, [
                        'SR-' . \Carbon\Carbon::parse($sr->created_at)->format('Y') . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT),
                        optional($sr->client)->company_name,
                        $sr->project_site ?? optional($sr->project)->project_name,
                        optional($sr->assignedUser)->name ?? 'Unassigned',
                        $sr->status,
                        \Carbon\Carbon::parse($sr->created_at)->format('Y-m-d H:i'),
                    ]);
                }
                fclose($out);
            }, 200, $headers);
        }

        // ---- Paginated results ----
        // $sr_explorer = $query->latest()->paginate(10)->withQueryString();

        $sortCol = $request->query('sort', 'id');
        $sortDir = $request->query('dir', 'desc');
        $allowed = ['id', 'created_at', 'status'];
        
        if(!in_array($sortCol, $allowed)) $sortCol = 'id';

        $sr_explorer = $query->orderBy($sortCol, $sortDir === 'asc' ? 'asc' : 'desc')
            ->paginate(10)->withQueryString();

        // ---- Stats (needed by the view for both full page and fragments) ----
        $stats = [
            'total'       => ServiceRequest::count(),
            'pendingRev'  => ServiceRequest::where('status', 'Pending')->count(),
            'inProgress'  => ServiceRequest::where('status', 'Assigned')->count(),
            'slaBreached' => ServiceRequest::whereDate('created_at', today())->count(),
        ];

        // ---- AJAX: return only the requested fragment from the SAME blade file ----
        if ($request->ajax() && $request->filled('frag')) {
            return view('sr_explorer', compact('sr_explorer', 'statuses', 'statusMap', 'stats'))
                ->fragment($request->frag);   // 'rows' or 'pager'
        }

        // ---- Full page load ----
        return view('sr_explorer', compact('sr_explorer', 'statuses', 'statusMap', 'stats'));

        // return view('sr_explorer', compact('sr_explorer'));
    }



    // Lookup endpoint — searches by company name, unique_code, or primary_mobile
    public function lookup(string $code)
    {
        $term = trim($code);

        $client = Client::with('projects')
            ->where(function ($q) use ($term) {
                $q->where('unique_code', $term)
                    ->orWhere('primary_mobile', $term)
                    ->orWhere('company_name', 'like', "%{$term}%");
            })
            ->first();

        if (! $client) {
            return response()->json(['found' => false], 404);
        }

        // Array of { id, name, sites } so the frontend posts a real project_id
        $projects = $client->projects->map(fn($p) => [
            'id'    => $p->id,
            'name'  => $p->project_name,
            'sites' => array_values(array_filter([$p->site_name])),
        ])->values();

        return response()->json([
            'found'  => true,
            'client' => [
                'id'       => $client->id,
                'name'     => $client->company_name,
                'status'   => 'Active',
                'flag'     => $client->contact_name ?? '',
                'contact'  => $client->primary_mobile ?? '',
                'projects' => $projects,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'         => ['required', 'exists:clients,id'],
            'project_id'        => ['required', 'exists:projects,id'],
            'service_type_id'   => ['required', 'exists:service_categories,id'],
            'reported_by'       => ['required', 'string', 'max:255'],
            'priority_level'    => ['required', 'in:Low,Medium,High,Critical'],
            'issue_description' => ['required', 'string', 'min:20'],
            'internal_remark'   => ['nullable', 'string'],
            'attachments.*'     => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
        ]);
        $sr = DB::transaction(function () use ($request, $data) {
            $paths = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $paths[] = $file->store('service-requests', 'public');
                }
            }

            return ServiceRequest::create([
                'client_id'         => $data['client_id'],
                'project_id'        => $data['project_id'],       // Stores your project primary ID key
                'service_type_id'   => $data['service_type_id'],
                'reported_by'       => $data['reported_by'],
                'priority_level'    => $data['priority_level'],
                'issue_description' => $data['issue_description'],
                'internal_remark'   => $data['internal_remark'] ?? null,
                'status'            => 'Pending',
                'attachments'       => $paths ?: null,
            ]);
        });

        return response()->json([
            'success'      => true,
            'id'           => $sr->id,
            'sr_reference' => 'SR-' . now()->year . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT),
        ]);
    }


    public function approvalIndex()
    {
        $inquiries = ServiceRequest::with('client', 'project', 'creator', 'category')
            ->where('status', 'Pending')
            ->latest()
            ->get();

        $stats = [
            'pending'   => $inquiries->count(),
            'approved'  => ServiceRequest::where('status', 'Approved')->whereDate('updated_at', today())->count(),
            'forwarded' => ServiceRequest::where('status', 'Forwarded')->whereDate('updated_at', today())->count(),
            'rejected'  => ServiceRequest::where('status', 'Rejected')->whereDate('updated_at', today())->count(),
        ];

        return view('inquiry_approval', compact('inquiries', 'stats'));
    }


    public function dispatch_engine()
    {
        $inquiries = ServiceRequest::with(['client', 'project', 'creator', 'category.domains'])
            ->where('status', 'Approved')
            ->latest()
            ->get();

        $tickets = $inquiries->map(function ($sr) {
            return [
                'id' => 'SR-' . ($sr->created_at?->year ?? now()->year) . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT),
                'dbId' => $sr->id,                    // ← ADD THIS LINE

                'client' => optional($sr->client)->company_name ?? '-',
                'contract' => optional($sr->project)->project_name ?? '-',
                'domain' => optional($sr->category)->category_name ?? '-',
                'site' => optional($sr->project)->site_name ?? '-',
                'priority' => $sr->priority_level,
                'hrsAgo' => abs((int) now()->diffInHours($sr->updated_at, false)),
                'approvedStr' => $sr->updated_at?->format('d M Y h:i A'),
                'status' => $sr->status,
                'client_id' => $sr->client_id,
                'project_id' => $sr->project_id,
                'service_type_id' => $sr->service_type_id,
                'reported_by' => $sr->reported_by,
                'issue_description' => $sr->issue_description,
                'internal_remark' => $sr->internal_remark,
            ];
        });

        $categories = ServiceCategory::where('status', 1)
            ->with(['domains' => fn($q) => $q->where('status', 1)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        // Real technicians from user_service_domain joined to users
        $technicians = DB::table('user_service_domain as usd')
            ->join('users as u', 'u.id', '=', 'usd.user_id')
            ->select(
                'u.id',
                'u.name',
                'usd.service_category_id',
                'usd.service_domain_id'
            )
            ->get()
            ->map(fn($r) => [
                'id'          => $r->id,
                'name'        => $r->name,
                'category_id' => $r->service_category_id,
                'domain_id'   => $r->service_domain_id,
            ])
            ->values();

        return view('dispatch_engine', compact('inquiries', 'tickets', 'categories', 'technicians'));
    }


    public function dispatch(Request $request, ServiceRequest $serviceRequest)
    {
        $data = $request->validate([
            'assigned_user_id'  => ['required', 'exists:users,id'],
            'service_domain_id' => ['nullable', 'exists:service_domains,id'],
        ]);

        $serviceRequest->update([
            'status'            => 'Assigned',
            'assigned_user_id'  => $data['assigned_user_id'],
            'service_domain_id' => $data['service_domain_id'] ?? null,
            'dispatched_at'     => now(),
        ]);

        $ref = 'SR-' . ($serviceRequest->created_at?->year ?? now()->year)
            . '-' . str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT);

        $tech = \App\Models\User::find($data['assigned_user_id']);

        return response()->json([
            'ok'      => true,
            'success' => true,
            'message' => 'Ticket ' . $ref . ' dispatched to ' . ($tech->name ?? 'technician') . '. Status: Assigned.',
        ]);
    }


    public function approve(ServiceRequest $serviceRequest)
    {
        $serviceRequest->update(['status' => 'Approved']);

        $ref = 'SR-' . ($serviceRequest->created_at?->year ?? now()->year)
            . '-' . str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT);

        return response()->json([
            'ok'      => true,
            'success' => true,
            'message' => 'Ticket ' . $ref . ' approved — Dispatch Engine. In-Warranty approval stamped.',
        ]);
    }

    public function forward(ServiceRequest $serviceRequest)
    {
        $serviceRequest->update(['status' => 'Forwarded']);

        $ref = 'SR-' . ($serviceRequest->created_at?->year ?? now()->year)
            . '-' . str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT);

        return response()->json([
            'ok'      => true,
            'success' => true,
            'message' => 'Ticket ' . $ref . ' forwarded — Quotation Desk. Scope set as Out-of-Warranty.',
        ]);
    }

    public function reject(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'reason' => 'required|string|min:10|max:500'
        ]);

        $serviceRequest->update([
            'status'          => 'Rejected',
            'internal_remark' => trim(($serviceRequest->internal_remark ?? '') . "\nRejection reason: " . $request->input('reason', 'Not specified')),
        ]);

        $ref = 'SR-' . ($serviceRequest->created_at?->year ?? now()->year)
            . '-' . str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT);

        return response()->json([
            'ok'      => true,
            'success' => true,
            'message' => 'Ticket ' . $ref . ' successfully rejected and archived.',
        ]);
    }

    public function ticketSummary()
{
    // Map DB statuses → Kanban lanes
    $statusMap = [
        'Pending'   => 'Pending',
        'Approved'  => 'Approved',
        'Assigned'  => 'Assigned',
        'Forwarded' => 'Forwarded',
        'Rejected'  => 'Rejected',
    ];

    $requests = ServiceRequest::with(['client', 'project', 'assignedUser', 'category'])
        ->latest()
        ->get();

    $tickets = $requests->map(function ($sr) {
        return [
            'id'        => 'SR-' . ($sr->created_at?->year ?? now()->year)
                            . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT),
            'client'    => optional($sr->client)->company_name ?? '—',
            'contract'  => optional($sr->project)->project_name ?? '—',
            'site'      => optional($sr->project)->site_name ?? '—',
            'category'  => optional($sr->category)->category_name ?? '—',
            'status'    => $sr->status,
            'priority'  => $sr->priority_level,
            'tech'      => optional($sr->assignedUser)->name ?? 'Unassigned',
            'techInitials' => $this->initials(optional($sr->assignedUser)->name),
            'createdAt' => $sr->created_at?->format('d M Y h:i A') ?? '—',
            'createdRaw'=> $sr->created_at?->toIso8601String(),
        ];
    })->values();

    $statuses = array_values(array_unique($statusMap));

return view('kanban_view', compact('tickets', 'statuses'));
}

private function initials(?string $name): string
{
    if (!$name) return '??';
    $parts = preg_split('/\s+/', trim($name));
    return strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
}

/**
     * QC Review Terminal — lists SRs that have been punched out
     * and are awaiting supervisor quality control.
     */
  public function qcReview()
{
    $requests = ServiceRequest::with([
            'client', 'project', 'assignedUser',
            // load the latest punch regardless of its status,
            // so an SR in qc_review always shows even if the punch
            // status wasn't updated to 'submitted'
            'punches' => fn($q) => $q->latest('punch_out_at')
                                     ->latest('id')
                                     ->with('items'),
        ])
        ->where('status', 'qc_review')
        ->latest('updated_at')
        ->get();

    $queue = $requests->map(function ($sr) {
        $punch = $sr->punches->first();   // may be null — that's OK now

        $scope = $this->srScope($sr);
        $sla   = $punch ? $this->srSla($sr, $punch)
                        : ['label' => '—', 'cls' => '', 'fill' => 0, 'color' => '#9ca3af'];
        $exp   = $punch ? $this->srExpenses($punch)
                        : ['rows' => [], 'total' => 0];

        return [
            'id'           => 'SR-' . ($sr->created_at?->year ?? now()->year)
                                . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT),
            'dbId'         => $sr->id,
            'client'       => optional($sr->client)->company_name ?? '—',
            'site'         => $punch?->site_location
                                ?? optional($sr->project)->site_name ?? '—',
            'tech'         => optional($sr->assignedUser)->name ?? 'Unassigned',
            'scope'        => $scope,
            'scopeLabel'   => $scope === 'iw' ? 'In Warranty' : 'Out of Warranty',
            'punchIn'      => $punch?->punch_in_at?->format('d M · h:i A') ?? '—',
            'punchOut'     => $punch?->punch_out_at?->format('d M · h:i A') ?? '—',
            'sla'          => $sla,
            'slaFill'      => $sla['fill'],
            'slaColor'     => $sla['color'],
            'expenses'     => $exp['rows'],
            'totalExpense' => 'AED ' . number_format($exp['total'], 0),
            'proof' => [
                'before' => $punch?->start_photo_path
                                ? asset('storage/' . $punch->start_photo_path) : null,
                'after'  => $punch?->finish_photo_path
                                ? asset('storage/' . $punch->finish_photo_path) : null,
            ],
            'completionSummary' => $punch?->completion_summary ?? '',
            'customerName'      => $punch?->customer_name ?? '',
        ];
    })->values();   

        $today = today();

        $passedToday = ServiceRequest::whereIn('status', ['Completed', 'Pending Invoice'])
            ->whereDate('qc_reviewed_at', $today)->count();

        $returnedRework = ServiceRequest::where('status', 'Rework')
            ->whereDate('qc_reviewed_at', $today)->count();

        $avgReviewTime = (int) round(
            ServiceRequest::whereDate('qc_reviewed_at', $today)
                ->whereNotNull('qc_reviewed_at')
                ->with(['punches' => fn($q) => $q->whereNotNull('punch_out_at')->latest('punch_out_at')])
                ->get()
                ->map(function ($sr) {
                    $p = $sr->punches->first();
                    return ($p && $p->punch_out_at && $sr->qc_reviewed_at)
                        ? abs($p->punch_out_at->diffInMinutes($sr->qc_reviewed_at))
                        : null;
                })
                ->filter()
                ->avg() ?? 0
        );

        return view('qc_review', compact(
            'queue', 'passedToday', 'returnedRework', 'avgReviewTime'
        ));
    }

    public function qcPass(ServiceRequest $serviceRequest)
    {
        $scope     = $this->srScope($serviceRequest);
        $newStatus = $scope === 'iw' ? 'Completed' : 'Pending Invoice';

        DB::transaction(function () use ($serviceRequest, $newStatus) {
            $serviceRequest->update([
                'status'         => $newStatus,
                'qc_reviewed_at' => now(),
                'qc_reviewed_by' => Auth::id(),
            ]);
            // close the punch record too
            $serviceRequest->punches()
                ->whereIn('status', ['submitted', 'qc_review'])
                ->update(['status' => 'qc_passed']);
        });

        $ref = 'SR-' . ($serviceRequest->created_at?->year ?? now()->year)
            . '-' . str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT);

        return response()->json([
            'ok'      => true,
            'success' => true,
            'scope'   => $scope,
            'status'  => $newStatus,
            'message' => $scope === 'iw'
                ? "Ticket {$ref} passed QC — marked Completed. Client WhatsApp summary queued."
                : "Ticket {$ref} passed QC — forwarded to Invoice Panel.",
        ]);
    }

    public function qcFail(Request $request, ServiceRequest $serviceRequest)
    {
        $data = $request->validate([
            'rework_notes' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        DB::transaction(function () use ($serviceRequest, $data) {
            $serviceRequest->update([
                'status'          => 'Rework',
                'qc_reviewed_at'  => now(),
                'qc_reviewed_by'  => Auth::id(),
                'rework_notes'    => $data['rework_notes'],
                'internal_remark' => trim(($serviceRequest->internal_remark ?? '')
                    . "\nQC Rework: " . $data['rework_notes']),
            ]);
            // reopen the punch so the technician can act on it
            $serviceRequest->punches()
                ->whereIn('status', ['submitted', 'qc_review'])
                ->update(['status' => 'rework']);
        });

        $ref = 'SR-' . ($serviceRequest->created_at?->year ?? now()->year)
            . '-' . str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT);

        return response()->json([
            'ok'      => true,
            'success' => true,
            'message' => "Ticket {$ref} returned to rework. Technician notified.",
        ]);
    }

    // ---------- helpers ----------

    private function srScope(ServiceRequest $sr): string
    {
        // Approval flow: 'Forwarded' → quotation desk = out-of-warranty.
        if (!empty($sr->warranty_scope)) {
            return $sr->warranty_scope === 'oow' ? 'oow' : 'iw';
        }
        return $sr->status === 'Forwarded' ? 'oow' : 'iw';
    }

    private function srSla(ServiceRequest $sr, \App\Models\Punch $punch): array
    {
        $target  = 48 * 60; // 48h in minutes — tune to your SLA rule
        $elapsed = $punch->punch_out_at
            ? abs($sr->created_at->diffInMinutes($punch->punch_out_at))
            : 0;

        $pct = $target > 0 ? min(100, (int) round($elapsed / $target * 100)) : 0;

        [$cls, $color] = match (true) {
            $pct >= 100 => ['breach', '#ef4444'],
            $pct >= 75  => ['warn',   '#d97706'],
            default     => ['',       '#15803d'],
        };

        $h = intdiv($elapsed, 60);
        $m = $elapsed % 60;

        return [
            'label' => $h > 0 ? "{$h}h {$m}m" : "{$m}m",
            'cls'   => $cls,
            'fill'  => $pct,
            'color' => $color,
        ];
    }

    /**
     * Build the expense summary from the punch's line items + labour.
     * Your billing model is PunchItem rows (materials) + a labour_charge.
     */
    private function srExpenses(\App\Models\Punch $punch): array
    {
        $rows  = [];
        $total = 0;

        foreach (($punch->items ?? []) as $it) {
            $line   = (float) ($it->line_total ?? ($it->qty * $it->rate));
            $total += $line;
            $rows[] = [
                'cat'     => $it->name . ' × ' . rtrim(rtrim(number_format((float)$it->qty, 2), '0'), '.'),
                'icon'    => 'bi-box-seam',
                'amt'     => 'AED ' . number_format($line, 0),
                'receipt' => !empty($punch->receipt_number),
            ];
        }

        $labour = (float) $punch->labour_charge;
        if ($labour > 0) {
            $total += $labour;
            $rows[] = [
                'cat'     => 'Labour Charge',
                'icon'    => 'bi-people',
                'amt'     => 'AED ' . number_format($labour, 0),
                'receipt' => false,
            ];
        }

        // Prefer the punch's own grand_total if present (already materials + labour).
        if ((float) $punch->grand_total > 0) {
            $total = (float) $punch->grand_total;
        }

        return ['rows' => $rows, 'total' => $total];
    }
}
