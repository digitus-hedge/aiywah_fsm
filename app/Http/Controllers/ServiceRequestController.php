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

        // Key each project by name → [ its single site_name ]
        $projects = $client->projects->mapWithKeys(fn($p) => [
            $p->project_name => array_filter([$p->site_name]),
        ]);

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
            'service_type_id'   => ['required', 'exists:service_categories,id'], // Cleaned to match your dynamic DB category id lookup            'reported_by'       => ['required', 'string', 'max:255'],
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

    public function approve(ServiceRequest $serviceRequest)
    {
        $serviceRequest->update(['status' => 'Approved']);

        return response()->json([
            'ok'      => true, // Matches JavaScript validation check
            'success' => true,
            'message' => 'Ticket ' . $serviceRequest->unique_code . ' approved — routed to Dispatch Engine.'
        ]);
    }

    public function forward(ServiceRequest $serviceRequest)
    {
        $serviceRequest->update(['status' => 'Forwarded']);

        return response()->json([
            'ok'      => true, // Matches JavaScript validation check
            'success' => true,
            'message' => 'Ticket ' . $serviceRequest->unique_code . ' forwarded — routed to Quotation Desk.'
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

        return response()->json([
            'ok'      => true, // Matches JavaScript validation check
            'success' => true,
            'message' => 'Ticket ' . $serviceRequest->unique_code . ' successfully rejected and archived.'
        ]);
    }
}
