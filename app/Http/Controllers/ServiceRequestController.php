<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\Client;
use App\Models\Punchitem;
use App\Models\ClientMobile;
use App\Models\Punch;
use App\Models\User;
use App\Models\ServiceCategory;
use App\Services\WhatsAppService;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ServiceRequestController extends Controller
{
    /* ============================================================
     |  CREATE / LOOKUP / STORE
     * ============================================================ */

    public function create()
    {
        $categories = ServiceCategory::get();

        return view('sr_registration', compact('categories'));
    }

    // Lookup endpoint — searches by company name, unique_code, or primary_mobile
    public function lookup(string $code)
    {
        $term = trim($code);

        // Resolve a picked client: exact code match → full detail + projects
        $client = Client::with(['projects', 'mobiles'])
            ->where('unique_code', $term)
            ->first();

        if ($client) {
            $projects = $client->projects->map(fn($p) => [
                'id'    => $p->id,
                'name'  => $p->project_name,
                'sites' => array_values(array_filter([$p->site_name])),
            ])->values();

            $contacts = $client->mobiles->map(fn($m) => [
                'id'     => $m->id,
                'name'   => $m->name,
                'mobile' => trim(($m->country ?? '') . ' ' . $m->mobile),
                'notify' => (bool) $m->notify,
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
                    'contacts' => $contacts,
                ],
            ]);
        }

        // Otherwise → always return a list, even for a single hit
        $matches = Client::where('company_name', 'like', "%{$term}%")
            ->orWhere('primary_mobile', 'like', "%{$term}%")
            ->orderBy('company_name')
            ->limit(10)
            ->get();

        if ($matches->isEmpty()) {
            return response()->json(['found' => false], 404);
        }

        return response()->json([
            'clients' => $matches->map(fn($c) => [
                'id'      => $c->id,
                'name'    => $c->company_name,
                'code'    => $c->unique_code,
                'contact' => $c->primary_mobile ?? '',
            ])->values(),
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
                'project_id'        => $data['project_id'],
                'service_type_id'   => $data['service_type_id'],
                'reported_by'       => $data['reported_by'],
                'priority_level'    => $data['priority_level'],
                'issue_description' => $data['issue_description'],
                'internal_remark'   => $data['internal_remark'] ?? null,
                'status'            => 'Pending',
                'attachments'       => $paths ?: null,
            ]);
        });

        // Send WhatsApp notification (outside transaction)
        $this->sendServiceRequestMessage($sr, $this->buildSrRef($sr), 'Pending');

        return response()->json([
            'success'      => true,
            'id'           => $sr->id,
            'sr_reference' => $this->buildSrRef($sr),
        ]);
    }

    /* ============================================================
     |  SR EXPLORER (list + filter + export)
     * ============================================================ */

    public function sr_explorer(Request $request)
    {
        $statusMap = [
            'Pending'           => 'sb-pending',
            'Approved'          => 'sb-approved',
            'Forwarded'         => 'sb-forwarded',
            'Rejected'          => 'sb-cancelled',
            'Assigned'          => 'sb-assigned',
            'Quoted'            => 'sb-quoted',
            'In Progress'       => 'sb-progress',
            'Quote Rejected'    => 'sb-cancelled',
            'Qc Review'         => 'sb-review',
            'Rework'            => 'sb-rework',
            'Reschedule'        => 'sb-rework',
            'Accepted'          => 'sb-approved',
            'Pending Invoice'   => 'sb-pending',
            'Invoice Submitted' => 'sb-forwarded',
            'Completed'         => 'sb-approved',
            'On Hold'           => 'sb-pending',
        ];
        $statuses = array_keys($statusMap);

        $query = ServiceRequest::with(['client', 'project', 'assignedUser']);

        if ($request->filled('search')) {
            $s = trim($request->search);

            $numericId = null;
            if (preg_match('/(\d+)\s*$/', $s, $m)) {
                $numericId = (int) ltrim($m[1], '0');
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
                        $this->buildSrRef($sr),
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

        $sortCol = $request->query('sort', 'id');
        $sortDir = $request->query('dir', 'desc');
        $allowed = ['id', 'created_at', 'status'];

        if (!in_array($sortCol, $allowed)) $sortCol = 'id';

        $sr_explorer = $query->orderBy($sortCol, $sortDir === 'asc' ? 'asc' : 'desc')
            ->paginate(10)->withQueryString();

        $stats = [
            'total'       => ServiceRequest::count(),
            'pendingRev'  => ServiceRequest::where('status', 'Pending')->count(),
            'inProgress'  => ServiceRequest::where('status', 'Assigned')->count(),
            'slaBreached' => ServiceRequest::whereDate('created_at', today())->count(),
        ];

        if ($request->ajax() && $request->filled('frag')) {
            return view('sr_explorer', compact('sr_explorer', 'statuses', 'statusMap', 'stats'))
                ->fragment($request->frag);
        }

        return view('sr_explorer', compact('sr_explorer', 'statuses', 'statusMap', 'stats'));
    }

    /* ============================================================
     |  INQUIRY APPROVAL (approve / forward / reject) + WhatsApp
     * ============================================================ */

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

    public function approve(ServiceRequest $serviceRequest)
    {
        $serviceRequest->update(['status' => 'Approved']);

        $ref = $this->buildSrRef($serviceRequest);
        $this->sendServiceRequestMessage($serviceRequest, $ref, 'Approved');

        return response()->json([
            'ok'      => true,
            'success' => true,
            'message' => "Ticket {$ref} approved — Dispatch Engine. In-Warranty approval stamped.",
        ]);
    }

    public function forward(ServiceRequest $serviceRequest)
    {
        $serviceRequest->update(['status' => 'Forwarded']);

        $ref = $this->buildSrRef($serviceRequest);
        $this->sendServiceRequestMessage($serviceRequest, $ref, 'Forwarded to Accounts');

        return response()->json([
            'ok'      => true,
            'success' => true,
            'message' => "Ticket {$ref} forwarded — Quotation Desk. Scope set as Out-of-Warranty.",
        ]);
    }

    public function reject(Request $request, ServiceRequest $serviceRequest)
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        $serviceRequest->update([
            'status'          => 'Rejected',
            'internal_remark' => trim(($serviceRequest->internal_remark ?? '')
                . "\nRejection reason: " . $data['reason']),
        ]);

        $ref = $this->buildSrRef($serviceRequest);
        $this->sendServiceRequestMessage($serviceRequest, $ref, 'Rejected');

        return response()->json([
            'ok'      => true,
            'success' => true,
            'message' => "Ticket {$ref} successfully rejected and archived.",
        ]);
    }

    /* ============================================================
     |  DISPATCH ENGINE
     * ============================================================ */

    public function dispatch_engine()
    {


        $inquiries = ServiceRequest::with(['client', 'project', 'creator', 'category.domains'])
            ->where('status', 'Approved')
            ->latest()
            ->get();




        $tickets = $inquiries->map(function ($sr) {
            return [
                'id'                => $this->buildSrRef($sr),
                'dbId'              => $sr->id,
                'client'            => optional($sr->client)->company_name ?? '-',
                'contract'          => optional($sr->project)->project_name ?? '-',
                'domain'            => optional($sr->category)->category_name ?? '-',
                'site'              => optional($sr->project)->site_name ?? '-',
                'priority'          => $sr->priority_level,
                'hrsAgo'            => abs((int) now()->diffInHours($sr->updated_at, false)),
                'approvedStr'       => $sr->updated_at?->format('d M Y h:i A'),
                'status'            => $sr->status,
                'client_id'         => $sr->client_id,
                'project_id'        => $sr->project_id,
                'service_type_id'   => $sr->service_type_id,
                'reported_by'       => $sr->reported_by,
                'issue_description' => $sr->issue_description,
                'internal_remark'   => $sr->internal_remark,
            ];
        });

          $categories = ServiceCategory::where('status', 1)
        ->with(['domains' => fn($q) => $q->where('status', 1)->orderBy('sort_order')])
        ->orderBy('sort_order')
        ->get();

        // --- Today's ETA workload count per technician (assigned_user_id) ---
    $loadCounts = DB::table('service_requests')
        ->select('assigned_user_id', DB::raw('COUNT(*) as cnt'))
        ->whereNotNull('assigned_user_id')
        ->whereDate('eta_at', now()->toDateString())
        ->groupBy('assigned_user_id')
        ->pluck('cnt', 'assigned_user_id');   // [3 => 5, 2 => 2, ...]

    $technicians = DB::table('user_service_domain as usd')
        ->join('users as u', 'u.id', '=', 'usd.user_id')
        ->select('u.id', 'u.name', 'usd.service_category_id', 'usd.service_domain_id')
        ->get()
        ->map(fn($r) => [
            'id'          => $r->id,
            'name'        => $r->name,
            'category_id' => $r->service_category_id,
            'domain_id'   => $r->service_domain_id,
            'count'       => (int) ($loadCounts[$r->id] ?? 0),   // today's ETA load
        ])
        ->values();

    return view('dispatch_engine', compact('inquiries', 'tickets', 'categories', 'technicians'));
    }


    public function storeContact(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'name'      => 'required|string|max:100',
            'country'   => 'required|string|max:6',
            'mobile'    => 'required|string|max:15',
            'notify'    => 'nullable|boolean',
        ]);

        $m = ClientMobile::create([
            'client_id' => $data['client_id'],
            'name'      => $data['name'],
            'country'   => $data['country'],
            'mobile'    => $data['mobile'],
            'notify'    => (int) ($data['notify'] ?? 0),
        ]);

        return response()->json([
            'contact' => [
                'id'     => $m->id,
                'name'   => $m->name,
                'mobile' => trim($m->country . ' ' . $m->mobile),
                'notify' => (bool) $m->notify,
            ],
        ]);
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

        $ref  = $this->buildSrRef($serviceRequest);
        $tech = \App\Models\User::find($data['assigned_user_id']);

        $this->sendServiceRequestMessage(
            $serviceRequest,
            $ref,
            'Assigned to ' . ($tech->name ?? 'a technician')
        );
        return response()->json([
            'ok'      => true,
            'success' => true,
            'message' => 'Ticket ' . $ref . ' dispatched to ' . ($tech->name ?? 'technician') . '. Status: Assigned.',
        ]);
    }

    /* ============================================================
     |  KANBAN / TICKET SUMMARY
     * ============================================================ */

    public function ticketSummary()
    {
        $statuses = [
            'Pending',
            'Approved',
            'Forwarded',
            'Rejected',
            'Assigned',
            'Quoted',
            'In Progress',
            'Quote Rejected',
            'Qc Review',
            'Rework',
            'Reschedule',
            'Accepted',
            'Pending Invoice',
            'Invoice Submitted',
            'Completed',
            'On Hold',
        ];

        $labels = [
            'Pending'           => 'Pending',
            'Approved'          => 'Approved',
            'Forwarded'         => 'Forwarded',
            'Rejected'          => 'Rejected',
            'Assigned'          => 'Assigned',
            'Quoted'            => 'Quoted',
            'In Progress'       => 'In Progress',
            'Quote Rejected'    => 'Quote Rejected',
            'Qc Review'         => 'QC Review',
            'Rework'            => 'Rework',
            'Reschedule'        => 'Reschedule',
            'Accepted'          => 'Accepted',
            'Pending Invoice'   => 'Pending Invoice',
            'Invoice Submitted' => 'Invoice Submitted',
            'Completed'         => 'Completed',
            'On Hold'           => 'On Hold',
        ];

        $tickets = ServiceRequest::with(['client', 'project', 'assignedUser', 'category'])
            ->latest()
            ->get()
            ->map(function ($sr) {
                return [
                    'id'           => $this->buildSrRef($sr),
                    'dbId'         => $sr->id,
                    'client'       => optional($sr->client)->company_name ?? '—',
                    'contract'     => optional($sr->project)->project_name ?? '—',
                    'site'         => optional($sr->project)->site_name ?? '—',
                    'category'     => optional($sr->category)->category_name ?? '—',
                    'status'       => $sr->status,
                    'priority'     => $sr->priority_level,
                    'tech'         => optional($sr->assignedUser)->name ?? 'Unassigned',
                    'techInitials' => $this->initials(optional($sr->assignedUser)->name),
                    'createdAt'    => $sr->created_at?->format('d M Y h:i A') ?? '—',
                    'createdRaw'   => $sr->created_at?->toIso8601String(),
                ];
            })->values();

        return view('kanban_view', compact('tickets', 'statuses', 'labels'));
    }

    /* ============================================================
     |  QC REVIEW
     * ============================================================ */

    public function qcReview()
    {
        $requests = ServiceRequest::with([
            'client',
            'project',
            'assignedUser',
            'punches' => fn($q) => $q->latest('punch_out_at')
                ->latest('id')
                ->with('items'),
        ])
            ->where('status', 'Qc Review')
            ->latest('updated_at')
            ->get();

        $queue = $requests->map(function ($sr) {
            $punch = $sr->punches->first();

            $scope = $this->srScope($sr);
            $sla   = $punch ? $this->srSla($sr, $punch)
                : ['label' => '—', 'cls' => '', 'fill' => 0, 'color' => '#9ca3af'];
            $exp   = $punch ? $this->srExpenses($punch)
                : ['rows' => [], 'total' => 0];

            return [
                'id'           => $this->buildSrRef($sr),
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
                    'before'    => $punch?->start_photo_path
                        ? asset('storage/' . $punch->start_photo_path) : null,
                    'after'     => $punch?->finish_photo_path
                        ? asset('storage/' . $punch->finish_photo_path) : null,
                    'signature' => $punch?->customer_signature_path
                        ? asset('storage/' . $punch->customer_signature_path) : null,
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

        return view('qc_review', compact('queue', 'passedToday', 'returnedRework', 'avgReviewTime'));
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
            $serviceRequest->punches()
                ->whereIn('status', ['submitted', 'qc_review'])
                ->update(['status' => 'qc_passed']);
        });
        app(\App\Services\WhatsAppService::class)->notifyServiceStatus($serviceRequest, $newStatus);
        $ref = $this->buildSrRef($serviceRequest);

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
            $serviceRequest->punches()
                ->whereIn('status', ['submitted', 'qc_review'])
                ->update(['status' => 'rework']);
        });
        app(\App\Services\WhatsAppService::class)->notifyServiceStatus($serviceRequest, 'Rework');
        $ref = $this->buildSrRef($serviceRequest);

        return response()->json([
            'ok'      => true,
            'success' => true,
            'message' => "Ticket {$ref} returned to rework. Technician notified.",
        ]);
    }

    /* ============================================================
     |  QUOTATION DESK
     * ============================================================ */

    public function quotationDesk()
    {
        $forwarded = ServiceRequest::with(['client', 'project'])
            ->where('status', 'Forwarded')
            ->latest('updated_at')
            ->get();

        $qQueue = $forwarded->map(function ($sr) {
            return [
                'id'     => $this->buildSrRef($sr),
                'dbId'   => $sr->id,
                'client' => optional($sr->client)->company_name ?? '—',
                'site'   => optional($sr->project)->site_name ?? '—',
                'logged' => $sr->updated_at?->diffForHumans() ?? '—',
                'issue'  => $sr->issue_description ?? '—',
            ];
        })->values();

        $quoted = ServiceRequest::with(['client', 'project'])
            ->where('status', 'Quoted')
            ->latest('updated_at')
            ->get();

        $pendingApproval = $quoted->map(function ($sr) {
            return [
                'id'        => 'PA-' . $sr->id,
                'sr'        => $this->buildSrRef($sr),
                'dbId'      => $sr->id,
                'client'    => optional($sr->client)->company_name ?? '—',
                'site'      => optional($sr->project)->site_name ?? '—',
                'ref'       => $sr->erp_quote_ref ?? '—',
                'submitted' => $sr->updated_at?->format('d M · h:i A') ?? '—',
                'waiting'   => $sr->updated_at?->diffForHumans(null, true) ?? '—',
            ];
        })->values();

        $clientApproved = ServiceRequest::where('status', 'Approved')
            ->where('warranty_scope', 'oow')->count();
        $quoteRejected  = ServiceRequest::where('status', 'Quote Rejected')->count();

        return view('quotation_desk', compact('qQueue', 'pendingApproval', 'clientApproved', 'quoteRejected'));
    }

    public function quoteSubmit(Request $request, ServiceRequest $serviceRequest)
    {
        $data = $request->validate([
            'erp_quote_ref' => ['required', 'string', 'max:100'],
            'quote_pdf'     => ['required', 'file', 'mimes:pdf', 'max:25600'],
        ]);

        $serviceRequest->update([
            'status'             => 'Quoted',
            'warranty_scope'     => 'oow',
            'erp_quote_ref'      => strtoupper($data['erp_quote_ref']),
            'quote_path'         => $request->file('quote_pdf')->store('quotations', 'public'),
            'quote_submitted_at' => now(),
        ]);
        app(\App\Services\WhatsAppService::class)->sendQuotation(
            $serviceRequest,
            asset('storage/' . $serviceRequest->quote_path)
        );
        return response()->json(['ok' => true, 'message' => 'Quote committed.']);
    }

    public function quoteApprove(ServiceRequest $serviceRequest)
    {
        $serviceRequest->update([
            'status'             => 'Approved',
            'warranty_scope'     => 'oow',
            'client_approved_at' => now(),
        ]);
        app(\App\Services\WhatsAppService::class)
            ->notifyServiceStatus($serviceRequest, 'Quotation approved');
        return response()->json(['ok' => true, 'message' => 'Client approved.']);
    }

    /* ============================================================
     |  INVOICE PANEL
     * ============================================================ */

    public function invoicePanel()
    {
        $pending = ServiceRequest::with([
            'client',
            'project',
            'assignedUser',
            'punches' => fn($q) => $q->whereNotNull('punch_out_at')
                ->latest('punch_out_at')->with('items'),
        ])
            ->where('status', 'Pending Invoice')
            ->latest('updated_at')
            ->get();

        $invQueue = $pending->map(function ($sr) {
            $punch = $sr->punches->first();
            $exp   = $punch ? $this->srExpenses($punch) : ['rows' => [], 'total' => 0];

            $duration = '—';
            if ($punch && $punch->punch_in_at && $punch->punch_out_at) {
                $mins = abs($punch->punch_in_at->diffInMinutes($punch->punch_out_at));
                $duration = intdiv($mins, 60) . 'h ' . ($mins % 60) . 'm';
            }

            return [
                'id'         => $this->buildSrRef($sr),
                'dbId'       => $sr->id,
                'client'     => optional($sr->client)->company_name ?? '—',
                'site'       => $punch?->site_location ?? optional($sr->project)->site_name ?? '—',
                'technician' => optional($sr->assignedUser)->name ?? 'Unassigned',
                'logged'     => $sr->updated_at?->diffForHumans() ?? '—',
                'punchIn'    => $punch?->punch_in_at?->format('d M · h:i A') ?? '—',
                'punchOut'   => $punch?->punch_out_at?->format('d M · h:i A') ?? '—',
                'duration'   => $duration,
                'expenses'   => collect($exp['rows'])->map(fn($r) => [
                    'cat' => $r['cat'],
                    'amt' => (float) preg_replace('/[^0-9.]/', '', $r['amt']),
                ])->values(),
                'totalExp'   => $exp['total'],
            ];
        })->values();

        $submitted = ServiceRequest::with(['client', 'project'])
            ->where('status', 'Invoice Submitted')
            ->latest('updated_at')
            ->get();

        $pendingHop = $submitted->map(function ($sr) {
            return [
                'id'        => 'IA-' . $sr->id,
                'sr'        => $this->buildSrRef($sr),
                'dbId'      => $sr->id,
                'client'    => optional($sr->client)->company_name ?? '—',
                'site'      => optional($sr->project)->site_name ?? '—',
                'code'      => $sr->invoice_code ?? '—',
                'submitted' => $sr->updated_at?->format('d M · h:i A') ?? '—',
                'waiting'   => $sr->updated_at?->diffForHumans(null, true) ?? '—',
            ];
        })->values();

        $completedThisMonth = ServiceRequest::where('status', 'Completed')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)->count();

        $invoicedThisMonth = number_format(
            ServiceRequest::whereIn('status', ['Invoice Submitted', 'Completed'])
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)
                ->sum('invoice_total') ?? 0,
            0
        );

        return view('invoice_panel', compact('invQueue', 'pendingHop', 'completedThisMonth', 'invoicedThisMonth'));
    }

    public function invoiceSubmit(Request $request, ServiceRequest $serviceRequest)
    {
        $data = $request->validate([
            'invoice_code'  => ['required', 'string', 'max:100'],
            'invoice_total' => ['nullable', 'numeric'],
            'invoice_pdf'   => ['required', 'file', 'mimes:pdf', 'max:25600'],
        ]);

        $path = $request->file('invoice_pdf')->store('invoices', 'public');

        $serviceRequest->update([
            'status'               => 'Invoice Submitted',
            'invoice_code'         => strtoupper($data['invoice_code']),
            'invoice_total'        => $data['invoice_total'] ?? 0,
            'invoice_path'         => $path,
            'invoice_submitted_at' => now(),
            'invoice_uploaded_by'  => Auth::id(),
        ]);
        app(\App\Services\WhatsAppService::class)
            ->notifyServiceStatus($serviceRequest, 'Invoice Submitted');
        return response()->json(['ok' => true, 'message' => 'Invoice committed. HoP notified.']);
    }

    public function hopApprove(ServiceRequest $serviceRequest)
    {
        $serviceRequest->update([
            'status'          => 'Completed',
            'hop_approved_at' => now(),
            'hop_approved_by' => Auth::id(),
        ]);
        app(\App\Services\WhatsAppService::class)
            ->notifyServiceStatus($serviceRequest, 'Completed');
        return response()->json(['ok' => true, 'message' => 'SR closed — Completed.']);
    }

    /* ============================================================
     |  EXPENSE LEDGER
     * ============================================================ */

    public function expenseLedger()
    {
        $items = Punchitem::with('punch.serviceRequest.assignedUser')
            ->latest('id')
            ->get();

        $ledger = $items->map(function ($it) {
            $sr = $it->punch?->serviceRequest;

            return [
                'id'         => $it->id,
                'sr'         => $sr ? $this->buildSrRef($sr) : '—',
                'tech'       => optional($sr?->assignedUser)->name ?? 'Unassigned',
                'name'       => $it->name,
                'cat'        => $it->category ?? '—',
                'amt'        => (float) ($it->line_total ?? ($it->qty * $it->rate)),
                'receiptUrl' => $it->receipt_path
                    ? asset('storage/' . $it->receipt_path)
                    : null,
                'date'       => $it->created_at?->format('d M Y') ?? '—',
            ];
        })->values();

        $categories = ExpenseCategory::where('status', true)
            ->orderBy('name')
            ->pluck('name');

        $totalExpenses = $ledger->sum('amt');

        $totals = [
            'total'    => $totalExpenses,
            'pending'  => (float) $items->where('recon_status', 'pending')->sum('line_total'),
            'approved' => (float) $items->where('recon_status', 'approved')->sum('line_total'),
            'disputed' => (float) $items->where('recon_status', 'disputed')->sum('line_total'),
        ];

        $lastSaved = $items->max('updated_at')?->format('d M Y · h:i A') ?? '—';

        return view('expense_ledger', compact(
            'ledger',
            'categories',
            'totalExpenses',
            'totals',
            'lastSaved'
        ));
    }

    /* ============================================================
     |  PRIVATE HELPERS
     * ============================================================ */

    /**
     * Build the human-facing SR reference, e.g. SR-2026-00011.
     * Year is taken from created_at so the reference never changes.
     */
    private function buildSrRef(ServiceRequest $sr): string
    {
        return 'SR-' . ($sr->created_at?->year ?? now()->year)
            . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Send a WhatsApp status notification to the client. All failures are
     * swallowed and logged so a WhatsApp problem never breaks the request.
     */
    private function sendServiceRequestMessage(ServiceRequest $sr, string $srReference, string $status): void
    {
        try {
            $client = $sr->client;

            if (!$client) {
                Log::warning('WhatsApp skipped — no client on SR', ['sr_id' => $sr->id]);
                return;
            }

            $whatsapp = new WhatsAppService();

            $phone = $whatsapp->formatWhatsAppNumber(
                $client->primary_country,
                $client->primary_mobile
            );

            if (!$phone) {
                Log::warning('WhatsApp skipped — no phone for client', [
                    'sr_id'     => $sr->id,
                    'client_id' => $client->id,
                ]);
                return;
            }

            $result = $whatsapp->sendServiceRequest(
                $phone,
                $client->contact_name,  // {{1}}
                $srReference,           // {{2}}
                $status                 // {{3}}
            );

            Log::info('WhatsApp service status sent', [
                'sr_id'  => $sr->id,
                'status' => $status,
                'phone'  => $phone,
                'result' => $result,
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatsApp service status message failed', [
                'sr_id' => $sr->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function initials(?string $name): string
    {
        if (!$name) return '??';
        $parts = preg_split('/\s+/', trim($name));
        return strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
    }

    private function srScope(ServiceRequest $sr): string
    {
        if (!empty($sr->warranty_scope)) {
            return $sr->warranty_scope === 'oow' ? 'oow' : 'iw';
        }
        return $sr->status === 'Forwarded' ? 'oow' : 'iw';
    }

    private function srSla(ServiceRequest $sr, Punch $punch): array
    {
        $target  = 48 * 60;
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

    private function srExpenses(Punch $punch): array
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

        if ((float) $punch->grand_total > 0) {
            $total = (float) $punch->grand_total;
        }

        return ['rows' => $rows, 'total' => $total];
    }
}
