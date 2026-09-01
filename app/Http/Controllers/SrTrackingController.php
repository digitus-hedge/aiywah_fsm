<?php
namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Services\SrTrackingService;

class SrTrackingController extends Controller
{
    public function __construct(private SrTrackingService $tracking) {}

    public function popup($id)
    {
       $sr = ServiceRequest::with([
            'client:id,company_name,unique_code',
            'category:id,category_name',
            'assignedUser:id,name,role_id',
            'assignedUser.role:id,code',
            'assignedSe:id,name,role_id',
            'assignedSe.role:id,code',
            'creator:id,name,role_id',
            'creator.role:id,code',
            'qcReviewedBy:id,name,role_id',
            'qcReviewedBy.role:id,code',
            'invoiceUploadedBy:id,name',
            'hopApprovedBy:id,name,role_id',
            'hopApprovedBy.role:id,code',
            'project:id,project_name,site_name,site_address',
            'punch',
        ])->findOrFail($id);

        return response()->json([
            'sr' => [
                'id'       => $sr->id,
                'ref'      => 'SR-' . optional($sr->created_at)->format('Y') . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT),
                'client'   => optional($sr->client)->company_name ?? '—',
                'site'     => optional($sr->project)->site_name ?? $sr->project_site ?? '—',
                'category' => optional($sr->category)->category_name ?? '—',
                'priority' => $sr->priority_level ?? '—',
                'tech'     => optional($sr->assignedUser)->name ?? 'Unassigned',
                'issue'    => $sr->issue_description ?? '—',
            ],
            'status'     => $this->tracking->statusMeta($sr->status),
            'ownership'  => $this->tracking->ownershipInfo($sr),
            'milestones' => $this->tracking->buildMilestones($sr),
            'history'    => $this->tracking->buildHistory($sr),
            'punch_in'   => optional(optional($sr->punch)->punch_in_at)->toIso8601String(),
        ]);
    }
    public function popupForWorker($id)
{
    $worker = auth('worker')->user();
    abort_unless($worker, 401);

    $sr = ServiceRequest::where('id', $id)
        ->where(function ($q) use ($worker) {
            $q->where('assigned_user_id', $worker->id)
              ->orWhere('reallocate_user_id', $worker->id);
        })
        ->with([
            'client:id,company_name,unique_code',
            'category:id,category_name',
            'assignedUser:id,name,role_id',
            'assignedUser.role:id,code',
            'assignedSe:id,name,role_id',
            'assignedSe.role:id,code',
            'creator:id,name,role_id',
            'creator.role:id,code',
            'qcReviewedBy:id,name,role_id',
            'qcReviewedBy.role:id,code',
            'invoiceUploadedBy:id,name',
            'hopApprovedBy:id,name,role_id',
            'hopApprovedBy.role:id,code',
            'project:id,project_name,site_name,site_address',
            'punch',
        ])
        ->firstOrFail();

    return response()->json([
        'sr' => [
            'id'       => $sr->id,
            'ref'      => 'SR-' . optional($sr->created_at)->format('Y') . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT),
            'client'   => optional($sr->client)->company_name ?? '—',
            'site'     => optional($sr->project)->site_name ?? $sr->project_site ?? '—',
            'category' => optional($sr->category)->category_name ?? '—',
            'priority' => $sr->priority_level ?? '—',
            'tech'     => optional($sr->assignedUser)->name ?? 'Unassigned',
            'issue'    => $sr->issue_description ?? '—',
        ],
        'status'     => $this->tracking->statusMeta($sr->status),
        'ownership'  => $this->tracking->ownershipInfo($sr),
        'milestones' => $this->tracking->buildMilestones($sr),
        'history'    => $this->tracking->buildHistory($sr),
        'punch_in'   => optional(optional($sr->punch)->punch_in_at)->toIso8601String(),
    ]);
}
}