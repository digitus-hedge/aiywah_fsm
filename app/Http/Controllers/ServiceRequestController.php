<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\Client;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceRequestController extends Controller
{
    public function create()
    {
        // Service types are hardcoded in the blade (ids 1 & 2), no DB needed

        $categories = ServiceCategory::get();

        return view('sr_registration', compact('categories'));
    }

    public function sr_explorer()
    {
        // Service types are hardcoded in the blade (ids 1 & 2), no DB needed

     $sr_explorer = ServiceRequest::with([
        'client',
        'project',
        'category',
    'assignedUser'
    ])
    ->latest()
    ->get();

        return view('sr_explorer', compact('sr_explorer'));
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
}
