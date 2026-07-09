<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Warranty;
use App\Models\Client;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('client')->latest()->get();
        $clients  = Client::orderBy('company_name')->get();
        $warranties = Warranty::where('status', 1)->orderBy('name')->get();

        $stats = [
            'total'    => $projects->count(),
            'active'   => $projects->where('status', 'Active')->count(),
            'inactive' => $projects->where('status', 'Inactive')->count(),
            'clients'  => $projects->pluck('client_id')->unique()->count(),
        ];

        return view('project_site', compact('projects', 'clients', 'stats', 'warranties'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Project::create($data);
        return response()->json(['ok' => true, 'message' => 'Project created.']);
    }

    public function update(Request $request, Project $project)
    {
        $data = $this->validated($request);
        $project->update($data);
        return response()->json(['ok' => true, 'message' => 'Project updated.']);
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json(['ok' => true, 'message' => 'Project deleted.']);
    }

    public function show(Project $project)
    {
        $project->load(['client', 'warranty']);

        $srs = $project->serviceRequests()
            ->with(['assignedUser', 'category', 'punch.user'])
            ->latest()
            ->get();

        // Bucket definitions — single source of truth
        $activeStatuses    = ['Approved', 'Forwarded', 'Assigned', 'in_progress', 'qc_review', 'Rework'];
        $completedStatuses = ['Completed', 'Pending Invoice'];
        $cancelledStatuses = ['Rejected'];

        $stats = [
            'total'     => $srs->count(),
            'active'    => $srs->whereIn('status', $activeStatuses)->count(),
            'completed' => $srs->whereIn('status', $completedStatuses)->count(),
            'cancelled' => $srs->whereIn('status', $cancelledStatuses)->count(),
            'pending'   => $srs->where('status', 'Pending')->count(),
            'rating'    => 0,
        ];

        $statusColors = [
            'Pending'         => '#f59e0b',
            'Approved'        => '#10b981',
            'Forwarded'       => '#2563eb',
            'Rejected'        => '#ef4444',
            'Assigned'        => '#8b5cf6',
            'in_progress'     => '#0891b2',
            'qc_review'       => '#d97706',
            'Rework'          => '#f97316',
            'Completed'       => '#059669',
            'Pending Invoice' => '#9a8053',
        ];

        $breakdown = $srs->groupBy('status')->map(fn($g, $status) => [
            'status' => $status,
            'label'  => Str::headline($status),   // in_progress → In Progress
            'count'  => $g->count(),
            'color'  => $statusColors[$status] ?? '#6b7280',
        ])->sortByDesc('count')->values();

        $srMap = $srs->keyBy('id')->map(function ($s) {
            $p = $s->punch;

            return [
                'id'          => $s->id,
                'code'        => $s->code,
                'category'    => $s->category?->category_name ?? '—',
                'cat_color'   => $s->category?->color_code ?? '#6b7280',
                'cat_icon'    => $s->category?->icon ?? 'bi-tools',
                'desc'        => $s->issue_description,
                'remark'      => $s->internal_remark,
                'date'        => $s->created_at?->format('d M Y'),
                'status'      => $s->status,
                'iw'          => $s->in_warranty,
                'priority'    => $s->priority_level,
                'reported_by' => $s->reported_by,
                'site'        => $p?->site_location ?? $s->project_site,
                'tech'        => $s->assignedUser?->name,
                'tech_id'     => $s->assigned_user_id,
                'dispatched'  => $s->dispatched_at?->format('d M Y, h:i A'),
                'qc_at'       => $s->qc_reviewed_at?->format('d M Y, h:i A'),
                'rework'      => $s->rework_notes,
                'elapsed'     => $s->created_at ? (int) $s->created_at->diffInHours(now()) : 0,

                // ---- punch data ----
                'punch_in'    => $p?->punch_in_at?->format('h:i A'),
                'punch_out'   => $p?->punch_out_at?->format('h:i A'),
                'duration'    => $p?->duration_label,
                'work_desc'   => $p?->work_description,
                'summary'     => $p?->completion_summary,
                'before'      => $p?->start_photo_path  ? asset('storage/' . $p->start_photo_path)  : null,
                'after'       => $p?->finish_photo_path ? asset('storage/' . $p->finish_photo_path) : null,
                'signature'   => $p?->customer_signature_path ? asset('storage/' . $p->customer_signature_path) : null,
                'receipt'     => $p?->receipt_number,
                'customer'    => $p?->customer_name,
                'materials'   => $p?->materials_subtotal,
                'labour'      => $p?->labour_charge,
                'total'       => $p?->grand_total,

                'attachments' => collect($s->attachments ?? [])->map(fn($a) => [
                    'url'  => asset('storage/' . (is_array($a) ? $a['path'] : $a)),
                    'name' => is_array($a) ? ($a['name'] ?? basename($a['path'])) : basename($a),
                ])->values(),
            ];
        });

        $categories = ServiceCategory::orderBy('sort_order')->get();
        $activities = collect();

        foreach ($srs as $s) {
            $activities->push([
                'title' => $s->code . ' — New Inquiry Raised',
                'by'    => $s->reported_by ?? 'System',
                'icon'  => 'bi-plus',
                'color' => '#9a8053',
                'bg'    => 'rgba(154,128,83,.12)',
                'at'    => $s->created_at,
            ]);

            if ($s->dispatched_at) {
                $activities->push([
                    'title' => $s->code . ' — Dispatched to ' . ('Technician'),
                    'by'    => $s->assignedUser?->name ?? 'Unassigned',
                    'icon'  => 'bi-send',
                    'color' => '#2563eb',
                    'bg'    => 'rgba(37,99,235,.1)',
                    'at'    => $s->dispatched_at,
                ]);
            }

            if ($s->qc_reviewed_at) {
                $isRework = ! empty($s->rework_notes);
                $activities->push([
                    'title' => $s->code . ($isRework ? ' — Returned for Rework' : ' — QC Passed & Closed'),
                    'by'    => optional(\App\Models\User::find($s->qc_reviewed_by))->name ?? 'QC Team',
                    'icon'  => $isRework ? 'bi-arrow-repeat' : 'bi-check-lg',
                    'color' => $isRework ? '#d97706' : '#10b981',
                    'bg'    => $isRework ? 'rgba(245,158,11,.1)' : 'rgba(16,185,129,.12)',
                    'at'    => $s->qc_reviewed_at,
                ]);
            }
        }

        $activities = $activities
            ->filter(fn($a) => $a['at'] !== null)
            ->sortByDesc('at')
            ->take(8)
            ->values();

        $nextId     = (ServiceRequest::max('id') ?? 0) + 1;
        $nextSrCode = 'SR-' . now()->year . '-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

        return view('project_view', compact(
            'project',
            'srs',
            'stats',
            'breakdown',
            'srMap',
            'categories',
            'activities',
            'nextSrCode'
        ));
    }


    private function validated(Request $request): array
    {
        $data = $request->validate([
            'client_id'       => 'required|exists:clients,id',
            'project_name'    => 'required|string|max:255',
            'project_code'    => 'nullable|string|max:50',
            'site_name'       => 'required|string|max:255',
            'completion_date' => 'nullable|date',
            'site_address'    => 'required|string',
            'status'          => 'required|in:Active,Inactive',
            'warranty_id' => 'nullable|exists:warranties,id',
        ]);

        // Warranty runs one year (365 days) from the completion date.
        // $data['warranty_end_date'] = !empty($data['completion_date'])
        //     ? Carbon::parse($data['completion_date'])->addYear()
        //     : null;


        $warrantyDays = 0;

        if (!empty($data['warranty_id'])) {
            $warranty = Warranty::find($data['warranty_id']);
            $warrantyDays = (int) ($warranty->value ?? 0);
        }

        $data['warranty_end_date'] = !empty($data['completion_date'])
            ? Carbon::parse($data['completion_date'])->addDays($warrantyDays)->toDateString()
            : null;

        return $data;
    }
}
