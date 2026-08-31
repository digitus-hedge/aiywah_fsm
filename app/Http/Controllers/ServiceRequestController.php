<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\Client;
use App\Models\Punchitem;
use App\Models\ClientMobile;
use App\Models\NotificationLog;
use App\Models\Punch;
use App\Models\User;
use App\Models\ServiceCategory;
use App\Models\Priority;
use App\Services\WhatsAppService;
use App\Models\ExpenseCategory;
use App\Models\SlaMatrix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Support\PortalLink;
use App\Mail\ServiceRequestReceivedMail;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendSrNotifications;
class ServiceRequestController extends Controller
{
    /* ============================================================
     |  CREATE / LOOKUP / STORE
     * ============================================================ */

    public function create()
    {
        $categories = ServiceCategory::get();
        $priorities = Priority::where('status', true)
            ->orderBy('display_order')
            ->get();
        $canViewTriage = auth()->user()->hasAnyAccess('inquiry_approval');  
        return view('sr_registration', compact('categories', 'priorities'));
    }
    // Lookup endpoint — searches by company name, unique_code, or primary_mobile
    public function lookup(string $code)
    {
        $term = trim($code);

        // Resolve a picked client: exact code match → full detail + projects
        $client = Client::with(['projects', 'mobiles'])
            ->where('unique_code', $term)
            ->where('status', 'Active')
            ->first();

        if ($client) {
            $projects = $client->projects->where('status', 'Active')->map(fn($p) => [
                'id'    => $p->id,
                'name'  => $p->project_name,
                'sites' => array_values(array_filter([$p->site_name])),
            ])->values();

            $contacts = $client->mobiles->map(fn($m) => [
                'id'     => $m->id,
                'name'   => $m->name,
                'mobile' => trim(($m->country ?? '') . ' ' . $m->mobile),
                'notify' => (bool) $m->notify,
            ])->values()->toArray();

            // prepend the client's primary contact_name if present and not already in the list
            if (!empty($client->contact_name)) {
                $exists = collect($contacts)->contains(
                    fn($c) => strcasecmp($c['name'] ?? '', $client->contact_name) === 0
                );
                if (!$exists) {
                    array_unshift($contacts, [
                        'id'      => null,
                        'name'    => $client->contact_name,
                        'mobile'  => $client->primary_mobile ?? '',
                        'notify'  => false,
                        'primary' => true,
                    ]);
                }
            }


            // prepend the client's primary contact_name if present and not already in the list


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

        // $matches = Client::where('company_name', 'like', "%{$term}%")
        //     ->orWhere('primary_mobile', 'like', "%{$term}%")
        //     ->orderBy('company_name')
        //     ->limit(10)
        //     ->get();

        $matches = Client::where('status', 'Active')
            ->where(function ($q) use ($term) {
                $q->where('company_name', 'like', "%{$term}%")
                    ->orWhere('primary_mobile', 'like', "%{$term}%");
            })
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




    public function mlsByCategory($categoryId)
    {
        $openStatuses = ['Accepted', 'In Progress', 'Reschedule', 'On Hold'];

        $mls = DB::table('user_service_domain as usd')
            ->join('users as u', 'u.id', '=', 'usd.user_id')
            ->leftJoin('service_requests as sr', function ($j) use ($openStatuses) {
                $j->on('sr.assigned_user_id', '=', 'u.id')
                    ->whereIn('sr.status', $openStatuses);
            })
            ->where('usd.service_category_id', $categoryId)
            ->groupBy('u.id', 'u.name')
            ->select('u.id', 'u.name', DB::raw('COUNT(DISTINCT sr.id) as active_count'))
            ->orderBy('active_count')
            ->orderBy('u.name')
            ->get();

        return response()->json($mls);
    }
    public function reallocate(Request $request)
{
    $data = $request->validate([
        'sr_id'       => 'required|exists:service_requests,id',
        'ml_id'       => 'required|exists:users,id',
        'remark'      => 'nullable|string|max:1000',
    ]);

    $sr = ServiceRequest::findOrFail($data['sr_id']);

    $oldStatus = $sr->status;   // capture BEFORE update

    $sr->update([
        'reallocate_user_id'    => $data['ml_id'],
        'reallocate'            => true,
        'reallocated_submit_at' => now(),
        'relocation_remarks'    => $data['remark'],
        'status'                => 'Rework',
    ]);

    $ref = $this->buildSrRef($sr);

    NotificationLog::create([
        'service_request_id' => $sr->id,
        'event'       => 'status_updated',
        'title'       => 'Status Updated',
        'message'     => "{$ref} reallocated — returned to Rework"
            . (!empty($data['remark']) ? ': ' . $data['remark'] : ''),
        'from_status' => $oldStatus,
        'to_status'   => 'Rework',
        'caused_by'   => auth()->id(),
    ]);

    // Same customer/internal message as a QC-failed rework — queued.
    SendSrNotifications::dispatch(
        $sr->id,
        SendSrNotifications::REALLOCATED,
        $ref,
        auth()->user()?->name
    );

    return response()->json(['ok' => true]);
}


    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'         => ['required', 'exists:clients,id'],
            'project_id'        => ['required', 'exists:projects,id'],
            'service_type_id'   => ['required', 'exists:service_categories,id'],
            'reported_by'       => ['required', 'string', 'max:255'],
            'priority_level'    => ['required', 'exists:priorities,name'],
            'issue_description' => ['required', 'string', 'min:20'],
            'internal_remark'   => ['nullable', 'string'],
            'attachments.*'     => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
        ]);

        // Store files OUTSIDE the transaction — disk writes shouldn't hold a DB lock.
        $paths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $paths[] = $file->store('service-requests', 'public');
            }
        }

        $sr = DB::transaction(function () use ($data, $paths) {
            $sr = ServiceRequest::create([
                'client_id'         => $data['client_id'],
                'project_id'        => $data['project_id'],
                'service_type_id'   => $data['service_type_id'],
                'reported_by'       => $data['reported_by'],
                'priority_level'    => $data['priority_level'],
                'issue_description' => $data['issue_description'],
                'internal_remark'   => $data['internal_remark'] ?? null,
                'status'            => 'Pending',
                'created_by'        => auth()->id(),
                 'logged_by_role'    => optional(auth()->user()->role)->code,
                'attachments'       => $paths ?: null,
            ]);

            NotificationLog::create([
                'service_request_id' => $sr->id,
                'event'     => 'sr_created',
                'title'     => 'New Service Request',
                'message'   => 'SR-' . ($sr->created_at?->year ?? now()->year)
                . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT) . ' created'
                . ' by ' . (auth()->user()->name ?? 'Unknown')
                . ' (' . (optional(auth()->user()->role)->code ?? '—') . ')',   // ← NEW: role visible in the log line
                'to_status' => 'Pending',
                'caused_by' => auth()->id(),
            ]);

            return $sr;
        });

    // WhatsApp + email are slow network calls — hand them to the queue.
    SendSrNotifications::dispatch($sr->id, SendSrNotifications::CREATED, $this->buildSrRef($sr));
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
            'Additional'        => 'sb-forwarded',
            'Rejected'          => 'sb-cancelled',
            'Assigned'          => 'sb-assigned',
            'Quoted'            => 'sb-quoted',
            'Quote Approved'    => 'sb-approved',
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

        $categories = ServiceCategory::orderBy('category_name')->get(['id', 'category_name']);

        $query = ServiceRequest::with(['client', 'project', 'assignedUser', 'category']);

        /* ── Row-level scope: only applies when this role's access level
        on sr_explorer is 'view_rls'. SE keeps its assigned_se meaning
        (their dispatched tickets); every other role scopes to the SRs
        they personally logged (created_by). ── */
        $user = auth()->user();
        $roleCode = optional($user->role)->code;
        $level = \App\Support\Permissions::for($roleCode, 'sr_explorer');   // 'yes' | 'rls' | 'view_rls' | ...

        if ($level === 'view_rls' || $level === 'rls') {
            $ownerColumn = $roleCode === 'SE' ? 'assigned_se' : 'created_by';
            $query->where($ownerColumn, $user->id);
        }

        // SE sees only their own assigned SRs
        $user = auth()->user();
        $isSe = $user->role?->code === 'SE';

        if ($isSe) {
            $query->where('assigned_se', $user->id);
        }

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
        if ($request->filled('category_id')) $query->where('service_type_id', $request->category_id);
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('created_at', '<=', $request->date_to);

        if ($request->query('export') === 'csv') {
            $rows = (clone $query)->latest()->get();

            return response()->streamDownload(function () use ($rows) {
                $out = fopen('php://output', 'w');
                fwrite($out, "\xEF\xBB\xBF");
                fputcsv($out, ['SR ID', 'Client', 'Site', 'Assigned To', 'Category', 'Status', 'Created']);
                foreach ($rows as $sr) {
                    fputcsv($out, [
                        $this->buildSrRef($sr),
                        optional($sr->client)->company_name,
                        optional($sr->project)->site_name ?? $sr->project_site,
                        optional($sr->assignedUser)->name ?? 'Unassigned',
                        optional($sr->category)->category_name ?? '-',
                        $sr->status,
                        $sr->created_at?->format('Y-m-d H:i'),
                    ]);
                }
                fclose($out);
            }, 'service_requests_' . now()->format('Ymd_His') . '.csv', [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        $sortCol = $request->query('sort', 'id');
        $sortDir = $request->query('dir', 'desc');
        $allowed = ['id', 'created_at', 'status'];

        if (!in_array($sortCol, $allowed)) $sortCol = 'id';

        $sr_explorer = $query->orderBy($sortCol, $sortDir === 'asc' ? 'asc' : 'desc')
            ->paginate(10)->withQueryString();

        $statBase = fn() => ServiceRequest::when($isSe, fn($q) => $q->where('assigned_se', $user->id));

        // $stats = [
        //     'total'       => ServiceRequest::count(),
        //     'pendingRev'  => ServiceRequest::where('status', 'Pending')->count(),
        //     'inProgress'  => ServiceRequest::where('status', 'Assigned')->count(),
        //     'slaBreached' => ServiceRequest::whereDate('created_at', today())->count(),
        // ];


        $stats = [
            'total'       => $statBase()->count(),
            'pendingRev'  => $statBase()->where('status', 'Pending')->count(),
            'inProgress'  => $statBase()->where('status', 'Assigned')->count(),
            'slaBreached' => $statBase()->whereDate('created_at', today())->count(),
        ];

        if ($request->ajax() && $request->filled('frag')) {
            return view('sr_explorer', compact('sr_explorer', 'statuses', 'statusMap', 'stats', 'categories'))
                ->fragment($request->frag);
        }

        return view('sr_explorer', compact('sr_explorer', 'statuses', 'statusMap', 'stats', 'categories'));
    }

    /* ============================================================
     |  INQUIRY APPROVAL (approve / forward / reject) + WhatsApp
     * ============================================================ */
    public function approvalIndex(Request $request)
    {
        $filters = [
            'range'   => $request->input('range'),
            'from'    => $request->input('from'),
            'to'      => $request->input('to'),
            'client'  => $request->input('client'),
            'service' => $request->input('service'),
        ];

        $query = ServiceRequest::with('client', 'project', 'creator', 'category')
            ->where('status', 'Pending');

        // Only window when the dashboard sent a range — a direct visit shows everything pending.
        if (!empty($filters['range'])) {
            [$start, $end] = $this->resolveRange($filters);
            $query->whereBetween('created_at', [$start, $end]);
        }

        if (!empty($filters['client'])) {
            $query->where('client_id', $filters['client']);
        }

        if (!empty($filters['service'])) {
            $query->where('service_type_id', $filters['service']);
        }

        $inquiries = $query->latest()->get();

        /* ── OOW tickets whose quotation the client has approved ── */
        $oowQuery = ServiceRequest::with('client', 'project', 'creator', 'category')
            ->where('status', 'Quote Approved');

        if (!empty($filters['range'])) {
            [$start, $end] = $this->resolveRange($filters);
            $oowQuery->whereBetween('created_at', [$start, $end]);
        }
        if (!empty($filters['client']))  $oowQuery->where('client_id', $filters['client']);
        if (!empty($filters['service'])) $oowQuery->where('service_type_id', $filters['service']);

        $oowInquiries = $oowQuery->latest('client_approved_at')->get();

        $priorities = Priority::where('status', 1)
            ->orderBy('display_order')
            ->get();

        // Today's throughput — deliberately NOT windowed by the dashboard filter.
        // These read "what happened today", so a past date range shouldn't zero them.
        $stats = [
            'pending'   => $inquiries->count(),
            'oow'       => $oowInquiries->count(),
            'approved'  => ServiceRequest::where('status', 'Approved')->whereDate('updated_at', today())->count(),
            'forwarded' => ServiceRequest::where('status', 'Forwarded')->whereDate('updated_at', today())->count(),
            'rejected'  => ServiceRequest::where('status', 'Rejected')->whereDate('updated_at', today())->count(),
        ];

        $engineers = User::whereHas('role', fn($q) => $q->where('code', 'SE'))
            ->with('serviceCategories:id')
            ->orderBy('name')
            ->get()
            ->map(fn($u) => [
                'id'         => $u->id,
                'name'       => $u->name,
                'categories' => $u->serviceCategories->pluck('id')->map(fn($i) => (int) $i)->all(),
                'qcReview'   => (bool) $u->can_qc_review,
            ])
            ->values();

        return view('inquiry_approval', compact(
            'inquiries',
            'oowInquiries',
            'stats',
            'priorities',
            'filters',
            'engineers'
        ));
    }

    public function approve(Request $request, ServiceRequest $serviceRequest)
    {
        $data = $request->validate([
            'assigned_se' => ['required', 'exists:users,id'],
        ], [], ['assigned_se' => 'service engineer']);

        if (!$this->isServiceEngineerId($data['assigned_se'])) {
            return response()->json([
                'ok' => false,
                'success' => false,
                'message' => 'The selected user is not a Service Engineer.',
            ], 422);
        }

        $oldStatus = $serviceRequest->status;          // capture BEFORE update

        $serviceRequest->update([
            'status'      => 'Approved',
            'approved_at' => now(),
            'assigned_se' => $data['assigned_se'],
        ]);

        $ref = $this->buildSrRef($serviceRequest);

        NotificationLog::create([
            'service_request_id' => $serviceRequest->id,
            'event'       => 'status_updated',
            'title'       => 'Status Updated',
            'message'     => "{$ref} moved to Approved",
            'from_status' => $oldStatus,               // e.g. 'Pending'
            'to_status'   => 'Approved',
            'caused_by'   => auth()->id(),
        ]);

        $serviceRequest->loadMissing(['client', 'project', 'creator']);


        $warrantyEnd = optional($serviceRequest->project)->warranty_end_date;

        $inWarranty = $warrantyEnd
            && \Carbon\Carbon::parse($warrantyEnd)->endOfDay()->isFuture();

        Log::info('Approve warranty branch', [
            'sr_id'        => $serviceRequest->id,
            'project_id'   => $serviceRequest->project_id,
            'warranty_end' => $warrantyEnd,
            'in_warranty'  => $inWarranty,
        ]);

        SendSrNotifications::dispatch($serviceRequest->id, SendSrNotifications::APPROVED, $ref);
        return response()->json([
            'ok'      => true,
            'success' => true,
            'message' => "Ticket {$ref} approved — Dispatch Engine. In-Warranty approval stamped.",
        ]);
    }

    /**
     * Second-stage approval for out-of-warranty work. The client has already
     * signed off the quotation; this allocates the engineer and releases the
     * SR into the Dispatch Engine.
     */
    public function approveOow(Request $request, ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->status !== 'Quote Approved') {
            return response()->json([
                'ok' => false,
                'success' => false,
                'message' => 'This SR is not awaiting out-of-warranty release.',
            ], 422);
        }

        $data = $request->validate([
            'assigned_se' => ['required', 'exists:users,id'],
        ], [], ['assigned_se' => 'service engineer']);

        if (!$this->isServiceEngineerId($data['assigned_se'])) {
            return response()->json([
                'ok' => false,
                'success' => false,
                'message' => 'The selected user is not a Service Engineer.',
            ], 422);
        }

        $oldStatus = $serviceRequest->status;

        $serviceRequest->update([
            'status'         => 'Approved',
            'warranty_scope' => 'oow',
            'assigned_se'    => $data['assigned_se'],
            'approved_at'    => now(),      // dispatch engine reads this for SLA
        ]);

        $ref = $this->buildSrRef($serviceRequest);

        NotificationLog::create([
            'service_request_id' => $serviceRequest->id,
            'event'       => 'status_updated',
            'title'       => 'Status Updated',
            'message'     => "{$ref} released to Dispatch Engine (out-of-warranty, quotation approved)",
            'from_status' => $oldStatus,
            'to_status'   => 'Approved',
            'caused_by'   => auth()->id(),
        ]);

        SendSrNotifications::dispatch($serviceRequest->id, SendSrNotifications::OOW_RELEASED, $ref);
        return response()->json([
            'ok'      => true,
            'success' => true,
            'message' => "Ticket {$ref} released to Dispatch Engine — Out-of-Warranty scope.",
        ]);
    }
   public function forward(ServiceRequest $serviceRequest)
{
    $oldStatus = $serviceRequest->status;

    $serviceRequest->update([
        'status'         => 'Forwarded',
        'warranty_scope' => 'oow',
    ]);

    $ref = $this->buildSrRef($serviceRequest);

    NotificationLog::create([
        'service_request_id' => $serviceRequest->id,
        'event'       => 'status_updated',
        'title'       => 'Status Updated',
        'message'     => "{$ref} Forwarded to Accounts",
        'from_status' => $oldStatus,
        'to_status'   => 'Forwarded',
        'caused_by'   => auth()->id(),
    ]);

    SendSrNotifications::dispatch($serviceRequest->id, SendSrNotifications::FORWARDED, $ref);
    SendSrNotifications::dispatch(
        $serviceRequest->id,
        SendSrNotifications::QUOTE_PENDING_ACCOUNTS,
        $ref,
        auth()->user()?->name
    );

    return response()->json([
        'ok'      => true,
        'success' => true,
        'message' => "Ticket {$ref} forwarded — Quotation Desk. Scope set as Out-of-Warranty.",
    ]);
}

public function additionalWork(ServiceRequest $serviceRequest)
{
    $oldStatus = $serviceRequest->status;          // capture BEFORE update

    $serviceRequest->update([
        'status'         => 'Additional',
        'warranty_scope' => 'oow',   // ← additional work is billed the same as OOW
    ]);

    $ref = $this->buildSrRef($serviceRequest);

    NotificationLog::create([
        'service_request_id' => $serviceRequest->id,
        'event'       => 'status_updated',
        'title'       => 'Status Updated',
        'message'     => "{$ref} accepted as Additional Work",
        'from_status' => $oldStatus,
        'to_status'   => 'Additional',
        'caused_by'   => auth()->id(),
    ]);

    SendSrNotifications::dispatch($serviceRequest->id, SendSrNotifications::ADDITIONAL, $ref);
    SendSrNotifications::dispatch(
        $serviceRequest->id,
        SendSrNotifications::QUOTE_PENDING_ACCOUNTS,
        $ref,
        auth()->user()?->name
    );

    return response()->json([
        'ok'      => true,
        'success' => true,
        'message' => "Ticket {$ref} accepted as Additional Work.",
    ]);
}

    public function reject(Request $request, ServiceRequest $serviceRequest)
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        $oldStatus = $serviceRequest->status;          // capture BEFORE update


        $serviceRequest->update([
            'status'          => 'Rejected',
            'internal_remark' => trim(($serviceRequest->internal_remark ?? '')
                . "\nRejection reason: " . $data['reason']),
        ]);

        $ref = $this->buildSrRef($serviceRequest);

        NotificationLog::create([
            'service_request_id' => $serviceRequest->id,
            'event'       => 'status_updated',
            'title'       => 'Status Updated',
            'message'     => "{$ref} Rejected — " . $data['reason'],
            'from_status' => $oldStatus,
            'to_status'   => 'Rejected',
            'caused_by'   => auth()->id(),
        ]);

        SendSrNotifications::dispatch($serviceRequest->id, SendSrNotifications::REJECTED, $ref);

        return response()->json([
            'ok'      => true,
            'success' => true,
            'message' => "Ticket {$ref} successfully rejected and archived.",
        ]);
    }

    /* ============================================================
     |  DISPATCH ENGINE
     * ============================================================ */

    public function dispatch_engine(Request $request)
    {
        $filters = [
            'range'   => $request->input('range'),
            'from'    => $request->input('from'),
            'to'      => $request->input('to'),
            'client'  => $request->input('client'),
            'service' => $request->input('service'),
        ];

        $query = ServiceRequest::with(['client', 'project', 'creator', 'category.domains'])
            ->where('status', 'Approved');

        /* ▼ ADD — Service Engineers see only their own assigned tickets */
        $user = auth()->user();

        if ($this->isServiceEngineer($user)) {
            $query->where('assigned_se', $user->id);
        }
        /* ▲ */

        // Only window when the dashboard sent a range — a direct visit shows the full queue.
        if (!empty($filters['range'])) {
            [$start, $end] = $this->resolveRange($filters);
            $query->whereBetween('created_at', [$start, $end]);
        }

        if (!empty($filters['client'])) {
            $query->where('client_id', $filters['client']);
        }

        if (!empty($filters['service'])) {
            $query->where('service_type_id', $filters['service']);
        }

        $inquiries = $query->latest()->get();


        // SLA targets keyed by priority for the view
        $slaMatrix = SlaMatrix::with('priority')->get()->map(fn($r) => [
            'prioId'   => (int) $r->priority_id,
            'name'     => optional($r->priority)->name,
            'prioKey'  => strtolower(trim((string) optional($r->priority)->name)),  // ← add
            'color'    => optional($r->priority)->color ?? '#8a8a8a',
            'approve'  => (int) $r->response_time,
            'dispatch' => (int) $r->assignment_time,
            'qc'       => (int) $r->resolution_time,
        ])->values();


        // Index the matrix both ways so it works whether priority_level holds an id or a name
        $slaById   = $slaMatrix->keyBy('prioId');
        $slaByName = $slaMatrix->keyBy('prioKey');

        $tickets = $inquiries->map(function ($sr) use ($slaById, $slaByName) {
            $stop    = $sr->approved_at ?: now();
            $elapsed = abs((int) $sr->created_at->diffInHours($stop, false));

            $rawPrio = $sr->priority_level;
            $meta    = $slaById[(int) $rawPrio]
                ?? $slaByName[strtolower(trim((string) $rawPrio))]
                ?? null;

            return [
                'id'                => $this->buildSrRef($sr),
                'dbId'              => $sr->id,
                'client'            => optional($sr->client)->company_name ?? '-',
                'contract'          => optional($sr->project)->project_name ?? '-',
                'domain'            => optional($sr->category)->category_name ?? '-',
                'site'              => optional($sr->project)->site_name ?? '-',
                'priority'          => $meta['name']  ?? ($rawPrio !== null ? (string) $rawPrio : '—'),
                'prioColor'         => $meta['color'] ?? '#8a8a8a',
                'prioId'            => $meta['prioId'] ?? null,
                'prioKey'           => $meta['prioKey'] ?? strtolower(trim((string) $rawPrio)),
                'hrsAgo'            => $elapsed,
                'approvedStr'       => $sr->approved_at?->format('d M Y h:i A') ?? '-',
                'status'            => $sr->status,
                'warranty'          => ($sr->project
                    && $sr->project->warranty_end_date
                    && \Carbon\Carbon::parse($sr->project->warranty_end_date)->endOfDay()->isFuture())
                    ? 'In Warranty'
                    : 'Out of Warranty',
                'client_id'         => $sr->client_id,
                'project_id'        => $sr->project_id,
                'service_type_id'   => $sr->service_type_id,
                'reported_by'       => $sr->reported_by,
                'issue_description' => $sr->issue_description,
                'internal_remark'   => $sr->internal_remark,

                'assignedSe' => $sr->assigned_se,
            ];
        });

        // $categories = ServiceCategory::where('status', 1)
        //     ->with(['domains' => fn($q) => $q->where('status', 1)->orderBy('sort_order')])
        //     ->orderBy('sort_order')
        //     ->get();


        $categories = ServiceCategory::where('status', 1)
            ->with(['domains' => fn($q) => $q->where('status', 1)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        // Which categories each SE covers → { seId: [catId, ...] }
        $catsBySe = DB::table('user_service_category as usc')
            ->join('users as u', 'u.id', '=', 'usc.user_id')
            ->join('roles as r', 'r.id', '=', 'u.role_id')
            ->where('r.code', 'SE')
            ->select('usc.user_id', 'usc.service_category_id')
            ->get()
            ->groupBy('user_id')
            ->map(fn($rows) => $rows->pluck('service_category_id')->map(fn($v) => (int) $v)->values());


        // --- Today's ETA workload count per technician (assigned_user_id) ---
        // Deliberately NOT filtered: this is live capacity, not a period metric.
        $loadCounts = DB::table('service_requests')
            ->select('assigned_user_id', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('assigned_user_id')
            ->whereDate('eta_at', now()->toDateString())
            ->groupBy('assigned_user_id')
            ->pluck('cnt', 'assigned_user_id');


        $technicians = DB::table('user_service_domain as usd')
            ->join('users as u', 'u.id', '=', 'usd.user_id')
            ->select('u.id', 'u.name', 'usd.service_category_id', 'usd.service_domain_id')
            ->get()
            ->map(fn($r) => [
                'id'          => $r->id,
                'name'        => $r->name,
                'category_id' => $r->service_category_id,
                'domain_id'   => $r->service_domain_id,
                'count'       => (int) ($loadCounts[$r->id] ?? 0),
            ])
            ->values();

        return view('dispatch_engine', compact(
            'inquiries',
            'tickets',
            'categories',
            'technicians',
            'filters',
            'slaMatrix',
            'catsBySe'
        ));
    }


    private function isServiceEngineer($user): bool
    {
        if (!$user) return false;

        return \DB::table('users')            // ← use your real pivot name
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->where('users.id', $user->id)
            ->where('roles.code', 'SE')
            ->exists();
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
            'eta_at'            => ['nullable', 'date'],
        ]);

        $oldStatus = $serviceRequest->status;

        $serviceRequest->update([
            'status'            => 'Assigned',
            'assigned_user_id'  => $data['assigned_user_id'],
            'service_domain_id' => $data['service_domain_id'] ?? null,
            'eta_at'            => $data['eta_at'] ?? null,
            'dispatched_at'     => now(),
        ]);

        $ref  = $this->buildSrRef($serviceRequest);
        $tech = User::find($data['assigned_user_id']);

        NotificationLog::create([
            'service_request_id' => $serviceRequest->id,
            'event'       => 'status_updated',
            'title'       => 'Status Updated',
            'message'     => "{$ref} assigned to " . ($tech->name ?? 'a technician')
                . (!empty($data['eta_at'])
                    ? ' — ETA ' . \Carbon\Carbon::parse($data['eta_at'])->format('d M Y h:i A')
                    : ''),
            'from_status' => $oldStatus,
            'to_status'   => 'Assigned',
            'caused_by'   => auth()->id(),
        ]);

        // Two mails and two WhatsApp round-trips — off the request entirely.
        \App\Jobs\SendSrNotifications::dispatch(
            $serviceRequest->id,
            \App\Jobs\SendSrNotifications::DISPATCHED,
            $ref
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
     /** A completed SR drops into the Archived lane once it is this old. */
    private const ARCHIVE_AFTER_MONTHS = 1;

    /**
     * Which column identifies "this user's own ticket" for row-level scoping,
     * per role code. SE owns via assignment; other view_rls roles (e.g. AC)
     * own via who raised/actioned the request. Adjust to match your schema.
     */
    private const OWNER_COLUMN_BY_ROLE = [
        'SE' => 'assigned_se',
        'FD' => 'created_by',
    ];
    
    /**
 * Resolves the row-level scope for a module (e.g. 'kanban_view') from the
 * role's permission map. Returns:
 *   null   → no scoping (permission is 'grant' or role isn't restricted)
 *   [col, id] → apply ->where($col, $id)
 * Deny is NOT handled here — a denied user shouldn't reach this method at all;
 * that should be enforced by your route middleware / policy.
 */
private function rlsScope(string $module): ?array
{
    $user = auth()->user();
    $code = optional($user->role)->code;

    // Swap this for however you actually resolve the permission map —
    // e.g. config('permissions.roles'), a Permission model, a Gate, etc.
    $level = \App\Support\Permissions::for($code, $module); // ← placeholder

    if ($level !== 'view_rls') {
        return null; // 'grant' or anything else → unrestricted
    }

    $column = self::OWNER_COLUMN_BY_ROLE[$code] ?? 'created_by';

    return [$column, $user->id];
}
 
   
   public function ticketSummary()
{
    /* Lane order, left → right. 'Archived' is a view-only lane: nothing in
       the database changes, a Completed ticket simply renders there once
       it passes the archive cutoff. */
    $statuses = [
        'Pending',
        'Approved',
        'Forwarded',
        'Additional',
        'Quoted',
        'Quote Approved',
        'Assigned',
        'Accepted',
        'In Progress',
        'Reschedule',
        'On Hold',
        'Qc Review',
        'Rework',
        'Pending Invoice',
        'Invoice Submitted',
        'Completed',
        'Archived',
        'Rejected',
        'Quote Rejected',
    ];

    $labels = [
        'Pending'           => 'Pending',
        'Approved'          => 'Approved',
        'Forwarded'         => 'Forwarded',
        'Additional'        => 'Additional Work',
        'Rejected'          => 'Rejected',
        'Assigned'          => 'Assigned',
        'Quoted'            => 'Quoted',
        'Quote Approved'    => 'Quote Approved',
        'In Progress'       => 'In Progress',
        'Quote Rejected'    => 'Quote Rejected',
        'Qc Review'         => 'QC Review',
        'Rework'            => 'Rework',
        'Reschedule'        => 'Reschedule',
        'Accepted'          => 'Accepted',
        'Pending Invoice'   => 'Pending Invoice',
        'Invoice Submitted' => 'Invoice Submitted',
        'Completed'         => 'Completed',
        'Archived'          => 'Archived',
        'On Hold'           => 'On Hold',
    ];

    /* The stat cards across the top. Every status maps to exactly one
       bucket below, so the counts always reconcile with the lanes. */
    $statGroups = [
        ['key' => 'pending',  'label' => 'Open / Intake',     'color' => '#f5c842'],
        ['key' => 'progress', 'label' => 'In Progress',       'color' => '#9a8053'],
        ['key' => 'review',   'label' => 'Awaiting Review',   'color' => '#b44fd4'],
        ['key' => 'rework',   'label' => 'Rework',            'color' => '#ff3366'],
        ['key' => 'done',     'label' => 'Completed',         'color' => '#05a34a'],
        ['key' => 'archive',  'label' => 'Archived',          'color' => '#4f9a8e'],
        ['key' => 'cancel',   'label' => 'Closed / Rejected', 'color' => '#aeb7c5'],
    ];

    /* Lane colour + stat bucket per status. The Blade tints this single
       hex for the lane header, its border and the count pill. */
    $statusCfg = [
        'Pending'           => ['color' => '#f5c842', 'group' => 'pending'],
        'Forwarded'         => ['color' => '#f5c842', 'group' => 'pending'],
        'Additional'        => ['color' => '#f5c842', 'group' => 'pending'],
        'Quoted'            => ['color' => '#f5c842', 'group' => 'pending'],
        'Quote Approved'    => ['color' => '#f5c842', 'group' => 'pending'],
        'On Hold'           => ['color' => '#f5c842', 'group' => 'pending'],

        'Approved'          => ['color' => '#9a8053', 'group' => 'progress'],
        'Assigned'          => ['color' => '#9a8053', 'group' => 'progress'],
        'Accepted'          => ['color' => '#9a8053', 'group' => 'progress'],
        'In Progress'       => ['color' => '#9a8053', 'group' => 'progress'],
        'Reschedule'        => ['color' => '#9a8053', 'group' => 'progress'],

        'Qc Review'         => ['color' => '#b44fd4', 'group' => 'review'],
        'Pending Invoice'   => ['color' => '#b44fd4', 'group' => 'review'],
        'Invoice Submitted' => ['color' => '#b44fd4', 'group' => 'review'],

        'Rework'            => ['color' => '#ff3366', 'group' => 'rework'],

        'Completed'         => ['color' => '#05a34a', 'group' => 'done'],

        'Archived'          => [
            'color' => '#4f9a8e',
            'group' => 'archive',
            'note'  => 'Completed more than ' . self::ARCHIVE_AFTER_MONTHS
                       . ' month ago. Read-only history.',
        ],

        'Rejected'          => ['color' => '#aeb7c5', 'group' => 'cancel'],
        'Quote Rejected'    => ['color' => '#aeb7c5', 'group' => 'cancel'],
    ];

     $user = auth()->user();
    $roleCode = optional($user->role)->code;

    $ownerColumn = self::OWNER_COLUMN_BY_ROLE[$roleCode] ?? null;

    $requests = ServiceRequest::with([
    'client',
    'project',
    'assignedUser',
    'category',
    'punches' => fn($q) => $q->latest('punch_out_at')->latest('id')->with('photos'),
])
    ->when($ownerColumn, fn($q) => $q->where($ownerColumn, $user->id))
    ->when($roleCode === 'AC', function ($q) use ($user) {
        $q->where(function ($sub) use ($user) {
            $sub->where('created_by', $user->id)      // SRs AC personally logged (CR-01)
                ->orWhere('warranty_scope', 'oow');    // the broader OOW financial pipeline
        });
    })
    ->latest()
    ->get();

    /* "Last moved by" — newest notification log per SR. Ordering ascending
       and keying by SR means the final write wins, i.e. the latest entry. */
    $logs = NotificationLog::whereIn('service_request_id', $requests->pluck('id'))
        ->orderBy('id')
        ->get(['service_request_id', 'caused_by', 'created_at'])
        ->keyBy('service_request_id');

    $moverNames = User::whereIn('id', $logs->pluck('caused_by')->filter()->unique())
        ->pluck('name', 'id');

    $archiveCutoff = now()->subMonths(self::ARCHIVE_AFTER_MONTHS);

    $tickets = $requests->map(function ($sr) use ($logs, $moverNames, $archiveCutoff) {
        $punch = $sr->punches->first();
        $log   = $logs->get($sr->id);

        /* Archived is derived at render time — the stored status stays
           'Completed', so nothing else in the system is affected. */
        $closedAt   = $this->srClosedAt($sr);
        $isArchived = $sr->status === 'Completed'
            && $closedAt
            && $closedAt->lt($archiveCutoff);

        $laneStatus = $isArchived ? 'Archived' : $sr->status;

        /* Site photos and the signed sheet are completion artefacts — they
           only exist after the job is closed, so they are only offered on
           the Completed and Archived lanes. */
        $showProof = in_array($laneStatus, ['Completed', 'Archived'], true);

        $moverName = $log && $log->caused_by
            ? ($moverNames[$log->caused_by] ?? null)
            : null;

        $site = $punch?->site_location
            ?? optional($sr->project)->site_name
            ?? '—';

        return [
            'id'           => $this->buildSrRef($sr),
            'dbId'         => $sr->id,
            'client'       => optional($sr->client)->company_name ?? '—',
            'contract'     => optional($sr->project)->project_name ?? '—',
            'site'         => $site,
            'category'     => optional($sr->category)->category_name ?? '—',
            'status'       => $laneStatus,
            'realStatus'   => $sr->status,          // untouched DB value
            'priority'     => $sr->priority_level,
            'tech'         => optional($sr->assignedUser)->name ?? 'Unassigned',
            'techInitials' => $this->initials(optional($sr->assignedUser)->name),
            'createdAt'    => $sr->created_at?->format('d M Y h:i A') ?? '—',
            'createdRaw'   => $sr->created_at?->toIso8601String(),
            'closedAt'     => $closedAt?->format('d M Y') ?? '—',

            'warranty'     => $this->srWarrantyLabel($sr),

            'showProof'    => $showProof,

            'photosBefore' => $showProof && $punch
                ? $punch->photos->where('type', 'before')->map(fn($p) => $p->url)->values()->all()
                : [],
            'photosAfter'  => $showProof && $punch
                ? $punch->photos->where('type', 'after')->map(fn($p) => $p->url)->values()->all()
                : [],

            'signedPdfUrl' => $showProof
                ? $this->srSignedSheetUrl($punch)
                : null,

            'mover' => [
                'name'     => $moverName,
                'initials' => $this->initials($moverName),
                'at'       => $log?->created_at?->format('d M Y h:i A') ?? '—',
            ],
        ];
    })->values();

    return view('kanban_view', compact(
        'tickets',
        'statuses',
        'labels',
        'statusCfg',
        'statGroups'
    ));
}
    /* ============================================================
     |  QC REVIEW
     * ============================================================ */

    public function qcReview(Request $request)
    {
        $user = auth()->user();
        $isSe = $this->isServiceEngineer($user);

        $filters = [
            'range'   => $request->input('range'),
            'from'    => $request->input('from'),
            'to'      => $request->input('to'),
            'client'  => $request->input('client'),
            'service' => $request->input('service'),
        ];

        $query = ServiceRequest::with([
            'client',
            'project',
            'assignedUser',
            'assignedSe',
            'punches' => fn($q) => $q->latest('punch_out_at')->latest('id')->with(['items', 'photos']),
        ])->where('status', 'Qc Review');

        /* QC ownership — engineers only ever see tickets they personally hold.
       The can_qc_review grant controls the Pass/Fail buttons (via canQc),
       not whether the ticket is visible. */
        if ($isSe) {
            $query->where('assigned_se', $user->id);
        }

        if (!empty($filters['range'])) {
            [$start, $end] = $this->resolveRange($filters);
            $query->whereBetween('updated_at', [$start, $end]);
        }

        if (!empty($filters['client'])) {
            $query->where('client_id', $filters['client']);
        }

        if (!empty($filters['service'])) {
            $query->where('service_type_id', $filters['service']);
        }

        $requests = $query->latest('updated_at')->get();

        $queue = $requests->map(function ($sr) use ($user) {
            $punch     = $sr->punches->first();
            $qcOwnerId = $this->qcOwnerId($sr);

            $sla = $punch ? $this->srSla($sr, $punch)
                : ['label' => '—', 'cls' => '', 'fill' => 0, 'color' => '#9ca3af'];
            $exp = $punch ? $this->srExpenses($punch)
                : ['rows' => [], 'total' => 0];

            $warrantyEnd  = optional($sr->project)->warranty_end_date;
            $isInWarranty = $warrantyEnd
                && \Carbon\Carbon::parse($warrantyEnd)->endOfDay()->isFuture();

            return [
                'id'         => $this->buildSrRef($sr),
                'dbId'       => $sr->id,
                'client'     => optional($sr->client)->company_name ?? '—',
                'site'       => $punch?->site_location
                    ?? optional($sr->project)->site_name ?? '—',
                'tech'       => optional($sr->assignedUser)->name ?? 'Unassigned',

                'scope'      => $isInWarranty ? 'iw' : 'oow',
                'scopeLabel' => $isInWarranty ? 'In Warranty' : 'Out of Warranty',
                'warranty'   => $isInWarranty ? 'In Warranty' : 'Out of Warranty',

                'categoryId'   => $sr->service_type_id,
                'categoryName' => optional($sr->category)->category_name ?? '',

                'punchIn'    => $punch?->punch_in_at?->format('d M · h:i A') ?? '—',
                'punchOut'   => $punch?->punch_out_at?->format('d M · h:i A') ?? '—',
                'sla'        => $sla,
                'slaFill'    => $sla['fill'],
                'slaColor'   => $sla['color'],

                'expenses' => collect($exp['rows'])->map(fn($r) => [
                    'cat'     => $r['cat']  ?? $r['category'] ?? '—',
                    'icon'    => $r['icon'] ?? 'bi-receipt',
                    'amt'     => $r['amt']  ?? $r['amount']   ?? 0,
                    'receipt' => (bool) ($r['receipt'] ?? $r['receipt_path'] ?? false),
                ])->values(),

                'totalExpense' => 'AED ' . number_format($exp['total'], 0),

                'proof' => [
                    'before' => $punch
                        ? $punch->photos->where('type', 'before')->map(fn($p) => $p->url)->values()->all()
                        : [],
                    'after'  => $punch
                        ? $punch->photos->where('type', 'after')->map(fn($p) => $p->url)->values()->all()
                        : [],
                    'signature' => $punch?->customer_signature_path
                        ? asset('storage/' . $punch->customer_signature_path) : null,
                ],
                'completionSummary' => $punch?->completion_summary ?? '',
                'customerName'      => $punch?->customer_name ?? '',

                'canAct'      => $this->canQc($user, $sr),
                'qcOwner'     => $qcOwnerId
                    ? (optional($sr->assignedSe)->name ?? 'Assigned Engineer')
                    : 'Head of Projects',
                'qcOwnerType' => $qcOwnerId ? 'se' : 'hop',
            ];
        })->values();

        $categories = ServiceCategory::where('status', 1)
            ->orderBy('category_name')
            ->get(['id', 'category_name']);

        $today = today();

        // fresh builder per call so conditions don't leak between the three queries
        $scoped = fn() => ServiceRequest::query()
            ->when($isSe, fn($q) => $q->where('assigned_se', $user->id));

        $passedToday = $scoped()
            ->whereIn('status', ['Completed', 'Pending Invoice'])
            ->whereDate('qc_reviewed_at', $today)
            ->count();

        $returnedRework = $scoped()
            ->where('status', 'Rework')
            ->whereDate('qc_reviewed_at', $today)
            ->count();

        $avgReviewTime = (int) round(
            $scoped()
                ->whereDate('qc_reviewed_at', $today)
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
            'queue',
            'passedToday',
            'returnedRework',
            'avgReviewTime',
            'filters',
            'categories'
        ));
    }




    private function resolveRange(array $filters): array
    {
        $range = $filters['range'] ?? 'today';

        if ($range === 'custom' && !empty($filters['from']) && !empty($filters['to'])) {
            try {
                $from = \Carbon\Carbon::parse($filters['from'])->startOfDay();
                $to   = \Carbon\Carbon::parse($filters['to'])->endOfDay();

                // user picked them backwards
                if ($from->gt($to)) {
                    [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
                }

                return [$from, $to];
            } catch (\Exception $e) {
                // unparseable date — fall through to the presets
            }
        }

        return match ($range) {
            'month'   => [now()->startOfMonth(), now()->endOfMonth()],
            'quarter' => [now()->startOfQuarter(), now()->endOfQuarter()],
            default   => [today()->startOfDay(), today()->endOfDay()],
        };
    }

    public function qcPass(ServiceRequest $serviceRequest)
    {
        if ($deny = $this->denyQc($serviceRequest)) return $deny;
        $scope     = $this->srScope($serviceRequest);
        $newStatus = $scope === 'iw' ? 'Completed' : 'Pending Invoice';

        $oldStatus = $serviceRequest->status;          // capture BEFORE update ('Qc Review')

        DB::transaction(function () use ($serviceRequest, $newStatus, $oldStatus, $scope) {
            $serviceRequest->update([
                'status'         => $newStatus,
                'qc_reviewed_at' => now(),
                'qc_reviewed_by' => Auth::id(),
            ]);

            $serviceRequest->punches()
                ->whereIn('status', ['submitted', 'qc_review'])
                ->update(['status' => 'qc_passed']);

            NotificationLog::create([
                'service_request_id' => $serviceRequest->id,
                'event'       => 'status_updated',
                'title'       => 'Status Updated',
                'message'     => $this->buildSrRef($serviceRequest) . ' passed QC — '
                    . ($scope === 'iw' ? 'marked Completed' : 'forwarded to invoicing'),
                'from_status' => $oldStatus,   // 'Qc Review'
                'to_status'   => $newStatus,   // 'Completed' or 'Pending Invoice'
                'caused_by'   => Auth::id(),
            ]);
        });

        $ref = $this->buildSrRef($serviceRequest);

        // In-warranty work ends here, so this is the moment to tell the customer.
        // Out-of-warranty still has to clear invoicing — hopApprove() notifies instead.
        if ($scope === 'iw') {
            SendSrNotifications::dispatch($serviceRequest->id, SendSrNotifications::COMPLETED, $ref);
        } else {
            SendSrNotifications::dispatch(
                $serviceRequest->id,
                SendSrNotifications::INVOICE_REQUIRED_ACCOUNTS,
                $ref,
                auth()->user()?->name   // ← actor is whoever is passing QC right now
            );
        }
        return response()->json([
            'ok'      => true,
            'success' => true,
            'scope'   => $scope,
            'status'  => $newStatus,
            'message' => $scope === 'iw'
                ? "Ticket {$ref} passed QC — marked Completed. Client notified."
                : "Ticket {$ref} passed QC — forwarded to Invoice Panel.",
        ]);
    }

    public function qcFail(Request $request, ServiceRequest $serviceRequest)
    {
        if ($deny = $this->denyQc($serviceRequest)) return $deny;
        $data = $request->validate([
            'rework_notes' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $oldStatus = $serviceRequest->status;          // capture BEFORE update ('Qc Review')

        DB::transaction(function () use ($serviceRequest, $data, $oldStatus) {
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

            NotificationLog::create([
                'service_request_id' => $serviceRequest->id,
                'event'       => 'status_updated',
                'title'       => 'Status Updated',
                'message'     => $this->buildSrRef($serviceRequest) . ' returned for rework — '
                    . \Illuminate\Support\Str::limit($data['rework_notes'], 60),
                'from_status' => $oldStatus,   // 'Qc Review'
                'to_status'   => 'Rework',
                'caused_by'   => Auth::id(),
            ]);
        });

        $ref = $this->buildSrRef($serviceRequest);

        // Rework notifications — off the request, onto the queue.
        SendSrNotifications::dispatch(
            $serviceRequest->id,
            SendSrNotifications::REWORK,
            $ref,
            Auth::user()?->name
        );

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


        $user = auth()->user();
        $isSe = $user?->role?->code === 'SE';

        // one reusable base builder — call it fresh each time
        $scoped = fn() => ServiceRequest::with(['client', 'project'])
            ->when($isSe, fn($q) => $q->where('assigned_se', $user->id));

        $forwarded = $scoped()
            ->whereIn('status', ['Forwarded', 'Additional'])
            ->latest('updated_at')
            ->get();

        $qQueue = $forwarded->map(function ($sr) {
            return [
                'id'        => $this->buildSrRef($sr),
                'dbId'      => $sr->id,
                'client'    => optional($sr->client)->company_name ?? '—',
                'site'      => optional($sr->project)->site_name ?? '—',
                'logged'    => $sr->updated_at?->diffForHumans() ?? '—',
                'createdAt' => $sr->created_at?->format('Y-m-d'),
                'issue'     => $sr->issue_description ?? '—',
            ];
        })->values();

        $quoted = $scoped()
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
                'createdAt' => $sr->created_at?->format('Y-m-d'),
            ];
        })->values();

        $rejected = $scoped()
            ->where('status', 'Quote Rejected')
            ->latest('updated_at')
            ->get();

        $rejectedQuotes = $rejected->map(function ($sr) {
            return [
                'id'        => 'QR-' . $sr->id,
                'sr'        => $this->buildSrRef($sr),
                'dbId'      => $sr->id,
                'client'    => optional($sr->client)->company_name ?? '—',
                'site'      => optional($sr->project)->site_name ?? '—',
                'ref'       => $sr->erp_quote_ref ?? '—',
                'rejected'  => $sr->updated_at?->format('d M · h:i A') ?? '—',
                'ago'       => $sr->updated_at?->diffForHumans(null, true) ?? '—',
                'createdAt' => $sr->created_at?->format('Y-m-d'),
            ];
        })->values();

        // stat counters must be scoped too, or an SE sees company-wide totals
        $clientApproved = $scoped()
            ->whereNotNull('client_approved_at')
            ->where('warranty_scope', 'oow')
            ->count();

        $quoteRejected = $scoped()
            ->where('status', 'Quote Rejected')
            ->count();

        return view('quotation_desk', compact(
            'qQueue',
            'pendingApproval',
            'rejectedQuotes',
            'clientApproved',
            'quoteRejected'
        ));
    }


    public function quoteSubmit(Request $request, ServiceRequest $serviceRequest)
    {
        $data = $request->validate([
            'erp_quote_ref' => ['required', 'string', 'max:100'],
            'quote_pdf'     => ['required', 'file', 'mimes:pdf', 'max:25600'],
        ]);

        $oldStatus = $serviceRequest->status;          // capture BEFORE update

        $serviceRequest->update([
            'status'             => 'Quoted',
            'warranty_scope'     => 'oow',
            'erp_quote_ref'      => strtoupper($data['erp_quote_ref']),
            'quote_path'         => $request->file('quote_pdf')->store('quotations', 'public'),
            'quote_submitted_at' => now(),
        ]);

        NotificationLog::create([
            'service_request_id' => $serviceRequest->id,
            'event'       => 'status_updated',
            'title'       => 'Status Updated',
            'message'     =>  $this->buildSrRef($serviceRequest) . ' quoted — ref '
                . strtoupper($data['erp_quote_ref']),
            'from_status' => $oldStatus,   // e.g. 'Forwarded'
            'to_status'   => 'Quoted',
            'caused_by'   => auth()->id(),
        ]);
        return response()->json(['ok' => true, 'message' => 'Quote committed.']);
    }

    public function quoteApprove(ServiceRequest $serviceRequest)
{
    $oldStatus = $serviceRequest->status;          // 'Quoted'

    $serviceRequest->update([
        'status'             => 'Quote Approved',
        'warranty_scope'     => 'oow',
        'client_approved_at' => now(),
        // approved_at deliberately NOT set — that stamp belongs to approveOow()
    ]);

    NotificationLog::create([
        'service_request_id' => $serviceRequest->id,
        'event'       => 'status_updated',
        'title'       => 'Status Updated',
        'message'     => $this->buildSrRef($serviceRequest)
            . ' — quotation approved by client, awaiting engineer allocation',
        'from_status' => $oldStatus,
        'to_status'   => 'Quote Approved',
        'caused_by'   => auth()->id(),
    ]);

    SendSrNotifications::dispatch(
        $serviceRequest->id,
        SendSrNotifications::QUOTE_CLIENT_APPROVED,
        $this->buildSrRef($serviceRequest),
        auth()->user()?->name
    );

    return response()->json([
        'ok'      => true,
        'success' => true,
        'message' => 'Client approved the quotation — SR returned to Inquiry Approval for engineer allocation.',
    ]);
}

    public function quoteReject(Request $request, ServiceRequest $serviceRequest)
    {
        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $oldStatus = $serviceRequest->status;   // 'Quoted'

        $serviceRequest->update([
            'status'            => 'Quote Rejected',
            'warranty_scope'    => 'oow',
            'quote_rejected_at' => now(),
            'internal_remark'   => trim(($serviceRequest->internal_remark ?? '')
                . "\nQuote rejected by client" . (!empty($data['reason']) ? ': ' . $data['reason'] : '')),
        ]);

        NotificationLog::create([
            'service_request_id' => $serviceRequest->id,
            'event'       => 'status_updated',
            'title'       => 'Status Updated',
            'message'     => $this->buildSrRef($serviceRequest)
                . ' — quotation rejected by client'
                . (!empty($data['reason']) ? ' (' . $data['reason'] . ')' : ''),
            'from_status' => $oldStatus,
            'to_status'   => 'Quote Rejected',
            'caused_by'   => auth()->id(),
        ]);

        // quoteReject()
        app(\App\Services\WhatsAppService::class)->notifyServiceStatus($serviceRequest, 'Quote Rejected');
        app(\App\Services\WhatsAppService::class)->notifyInternalStatusChange($serviceRequest, 'Quote Rejected', auth()->user()?->name);

        return response()->json([
            'ok'      => true,
            'success' => true,
            'message' => 'Quotation marked as rejected by client.',
        ]);
    }
    /* ============================================================
     |  INVOICE PANEL
     * ============================================================ */

    public function invoicePanel()
    {
        $canHopApprove = (bool) auth()->user()?->hasAnyAccess('invoice_hop_approve');
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
                'createdAt'  => $sr->created_at?->format('Y-m-d'),   // <-- add
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
                'createdAt'  => $sr->created_at?->format('Y-m-d'),   // <-- add
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

         return view('invoice_panel', compact(
        'invQueue', 'pendingHop', 'completedThisMonth', 'invoicedThisMonth', 'canHopApprove'
    ));
    }
public function invoiceSubmit(Request $request, ServiceRequest $serviceRequest)
{
    $data = $request->validate([
        'invoice_code'  => ['required', 'string', 'max:100'],
        'invoice_total' => ['required', 'numeric', 'min:0'],
        'invoice_pdf'   => ['required', 'file', 'mimes:pdf', 'max:25600'],
    ]);

    $path = $request->file('invoice_pdf')->store('invoices', 'public');

    $oldStatus = $serviceRequest->status;          // capture BEFORE update ('Pending Invoice')

    $serviceRequest->update([
        'status'               => 'Invoice Submitted',
        'invoice_code'         => strtoupper($data['invoice_code']),
        'invoice_total'        => $data['invoice_total'],
        'invoice_path'         => $path,
        'invoice_submitted_at' => now(),
        'invoice_uploaded_by'  => Auth::id(),
    ]);

    NotificationLog::create([
        'service_request_id' => $serviceRequest->id,
        'event'       => 'status_updated',
        'title'       => 'Status Updated',
        'message'     => $this->buildSrRef($serviceRequest) . ' invoice submitted — '
            . strtoupper($data['invoice_code'])
            . (isset($data['invoice_total']) ? ' (₹' . number_format($data['invoice_total'], 2) . ')' : ''),
        'from_status' => $oldStatus,   // 'Pending Invoice'
        'to_status'   => 'Invoice Submitted',
        'caused_by'   => Auth::id(),
    ]);

    SendSrNotifications::dispatch(
        $serviceRequest->id,
        SendSrNotifications::INVOICE_SUBMITTED,
        $this->buildSrRef($serviceRequest),
        Auth::user()?->name
    );

    return response()->json(['ok' => true, 'message' => 'Invoice committed. HoP notified.']);
}
    public function hopApprove(ServiceRequest $serviceRequest)
{
     if (! auth()->user()?->hasAnyAccess('invoice_hop_approve')) {
        return response()->json([
            'ok'      => false,
            'success' => false,
            'message' => 'You do not have permission to approve invoices for closure.',
        ], 403);
    }

    $oldStatus = $serviceRequest->status;

    $serviceRequest->update([
        'status'          => 'Completed',
        'hop_approved_at' => now(),
        'hop_approved_by' => Auth::id(),
    ]);

    $ref = $this->buildSrRef($serviceRequest);

    NotificationLog::create([
        'service_request_id' => $serviceRequest->id,
        'event'       => 'status_updated',
        'title'       => 'Status Updated',
        'message'     => $ref . ' — HoP approved invoice, service request completed',
        'from_status' => $oldStatus,
        'to_status'   => 'Completed',
        'caused_by'   => Auth::id(),
    ]);

    SendSrNotifications::dispatch($serviceRequest->id, SendSrNotifications::COMPLETED, $ref);

    return response()->json([
        'ok'      => true,
        'success' => true,
        'message' => 'HoP approved — service request completed. Client notified.',
    ]);
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
                $status,                // {{3}}
                'en_US',
                $sr,                    // populates service_request_id + enables fan-out
                $client                 // populates client_id + secondary contacts
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
        $target = 48 * 60; // minutes

        // Clock starts at SR creation, stops at punch-out.
        // Still open → keep counting to now, so the badge stays live.
        $start = $sr->created_at;
        $end   = $punch->punch_out_at ?? now();

        $elapsed = $start && $end
            ? max(0, (int) round($start->diffInMinutes($end, false)))
            : 0;

        $raw = $target > 0 ? $elapsed / $target * 100 : 0;
        $pct = min(100, (int) round($raw));   // bar width only — must be capped

        [$cls, $color] = match (true) {
            $raw >= 100 => ['breach', '#ef4444'],
            $raw >= 75  => ['warn',   '#d97706'],
            default     => ['',       '#15803d'],
        };

        $h = intdiv($elapsed, 60);
        $m = $elapsed % 60;

        return [
            'label'   => $h > 0 ? "{$h}h {$m}m" : "{$m}m",
            'cls'     => $cls,
            'fill'    => $pct,
            'color'   => $color,
            'running' => $punch->punch_out_at === null,
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

    /**
     * Everything the customer gets when a service request is genuinely finished:
     * completion email, WhatsApp summary, and the delayed satisfaction survey.
     * Called from qcPass() for in-warranty work, hopApprove() for out-of-warranty.
     */
    private function sendCompletionNotifications(
        ServiceRequest $sr,
        string $ref,
        string $customerLink
    ): void {
        try {
            if ($to = $sr->client?->email) {
                Mail::to($to)->send(new \App\Mail\MaintenanceCompletedMail($sr, $ref, $customerLink));
            } else {
                Log::warning('Completion mail skipped — client has no email', ['sr_id' => $sr->id]);
            }
        } catch (\Throwable $e) {
            Log::error('Completion mail failed', ['sr_id' => $sr->id, 'error' => $e->getMessage()]);
        }

        try {
            app(\App\Services\WhatsAppService::class)
                ->notifyMaintenanceCompleted($sr, null, $customerLink);
        } catch (\Throwable $e) {
            Log::error('Completion WhatsApp (customer) failed', ['sr_id' => $sr->id, 'error' => $e->getMessage()]);
        }

        try {
            app(\App\Services\WhatsAppService::class)
                ->notifyInternalMaintenanceCompleted($sr, null, $customerLink);
        } catch (\Throwable $e) {
            Log::error('Completion WhatsApp (internal) failed', ['sr_id' => $sr->id, 'error' => $e->getMessage()]);
        }

        \App\Jobs\SendSatisfactionSurvey::dispatch($sr)
            ->delay(now()->addMinutes(2));   // ← restore addDay() before go-live
    }

    /** Roles that inherit QC when the assigned engineer doesn't hold it. */
    private const QC_FALLBACK_ROLES = ['HP', 'AD', 'SA'];

    /**
     * Who is allowed to QC this SR.
     * Returns the SE's id when that engineer holds QC rights, or null when it falls to HoP.
     */
    private function qcOwnerId(ServiceRequest $sr): ?int
    {
        $se = $sr->relationLoaded('assignedSe') ? $sr->assignedSe : $sr->assignedSe()->first();

        return ($se && $se->can_qc_review) ? (int) $se->id : null;
    }

    private function canQc($user, ServiceRequest $sr): bool
    {
        if (!$user) return false;

        $ownerId = $this->qcOwnerId($sr);

        // An engineer holds it → only that engineer.
        if ($ownerId !== null) {
            return (int) $user->id === $ownerId;
        }

        // Nobody holds it (no SE, or SE has can_qc_review = 0) → HoP.
        return in_array(optional($user->role)->code, self::QC_FALLBACK_ROLES, true);
    }

    private function denyQc(ServiceRequest $sr): ?\Illuminate\Http\JsonResponse
    {
        if ($this->canQc(auth()->user(), $sr)) {
            return null;
        }

        $ownerId = $this->qcOwnerId($sr);

        return response()->json([
            'ok'      => false,
            'success' => false,
            'message' => $ownerId
                ? 'QC on this ticket is allocated to '
                . (optional($sr->assignedSe)->name ?? 'the assigned engineer') . '.'
                : 'QC on this ticket is reserved for the Head of Projects.',
        ], 403);
    }
    private function isServiceEngineerId($userId): bool
    {
        return DB::table('users')
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->where('users.id', $userId)
            ->where('roles.code', 'SE')
            ->exists();
    }

    /** When a ticket was actually closed — drives the archive cutoff. */
    private function srClosedAt(ServiceRequest $sr): ?\Carbon\Carbon
    {
        $raw = $sr->hop_approved_at
            ?? $sr->qc_reviewed_at
            ?? $sr->updated_at;
 
        if (!$raw) {
            return null;
        }
 
        return $raw instanceof \Carbon\Carbon
            ? $raw
            : \Carbon\Carbon::parse($raw);
    }
 
    /** Warranty badge text for a ticket card. */
    private function srWarrantyLabel(ServiceRequest $sr): string
    {
        // An explicit out-of-warranty stamp always wins.
        if ($sr->warranty_scope === 'oow') {
            return 'Not Covered';
        }
 
        $end = optional($sr->project)->warranty_end_date;
 
        return ($end && \Carbon\Carbon::parse($end)->endOfDay()->isFuture())
            ? 'Covered'
            : 'Not Covered';
    }
 
    /**
     * The signed acceptance sheet for a completed job.
     *
     * Falls through a few likely column names — a column that doesn't exist
     * on the punches table simply reads as null, so this is safe either way.
     * If your signed sheet lives somewhere else, point the first line at it.
     */
    private function srSignedSheetUrl($punch): ?string
    {
        if (!$punch) {
            return null;
        }
 
        $path = $punch->acceptance_pdf_path
            ?? $punch->signed_sheet_path
            ?? $punch->customer_signature_path
            ?? null;
 
        return $path ? asset('storage/' . $path) : null;
    }

    public function loggedByRoleLabel(): string
{
    return match ($this->logged_by_role) {
        'FD' => 'Front Desk',
        'HP' => 'Head of Projects',
        'SE' => 'Service Engineer',
        'AC' => 'Accounts / AR',
        'AD' => 'Admin',
        'SA' => 'Super Admin',
        default => $this->logged_by_role ?? '—',
    };
}
 
}
