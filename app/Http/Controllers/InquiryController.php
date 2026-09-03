<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ServiceRequest;
use App\Models\NotificationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendSrNotifications;

class InquiryController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $data = $request->validate([
            'service_type_id'   => ['required', 'exists:service_categories,id'],
            'reported_by'       => ['required', 'string', 'max:255'],   // ← was missing
            'issue_description' => ['required', 'string', 'min:20'],
            'internal_remark'   => ['nullable', 'string', 'max:1000'],
            'warranty_scope'    => ['required', 'in:iw,oow'],
            'priority_level'    => ['required', 'in:Low,Medium,High,Critical'],
            'photos'            => ['nullable', 'array', 'max:5'],
            'photos.*'          => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
        ]);

        $sr = DB::transaction(function () use ($data, $project, $request) {

            $attachments = [];
            foreach ($request->file('photos', []) as $file) {
                $attachments[] = [
                    'path' => $file->store('service-requests', 'public'),
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                ];
            }

            $sr = ServiceRequest::create([
                'client_id'         => $project->client_id,
                'project_id'        => $project->id,
                'project_site'      => $project->site_name,
                'service_type_id'   => $data['service_type_id'],
                'reported_by'       => $data['reported_by'],   // ← now uses the form value
                'priority_level'    => $data['priority_level'],
                'issue_description' => $data['issue_description'],
                'internal_remark'   => $data['internal_remark'] ?? null,
                'status'            => 'Pending',
                'warranty_scope'    => $data['warranty_scope'],
                'created_by'        => auth()->id(),
                'logged_by_role'    => optional(auth()->user()->role)->code,
                'attachments'       => $attachments,
            ]);

            NotificationLog::create([
                'service_request_id' => $sr->id,
                'event'     => 'sr_created',
                'title'     => 'New Service Request',
                'message'   => $this->buildSrRef($sr) . ' created'
                    . ' by ' . (auth()->user()->name ?? 'Unknown')
                    . ' (' . (optional(auth()->user()->role)->code ?? '-') . ')',
                'to_status' => 'Pending',
                'caused_by' => auth()->id(),
            ]);

            return $sr;
        });

        // Same WhatsApp + email flow as SR Registration.
        SendSrNotifications::dispatch(
            $sr->id,
            SendSrNotifications::CREATED,
            $this->buildSrRef($sr)
        );

        return redirect()
            ->route('projects.show', $project)
            ->with('success', $this->buildSrRef($sr) . " created. WhatsApp receipt sent to client.");
    }

    /**
     * Build the human-facing SR reference, e.g. SR-2026-00011.
     * Mirrors ServiceRequestController::buildSrRef() so refs stay identical everywhere.
     */
    private function buildSrRef(ServiceRequest $sr): string
    {
        return 'SR-' . ($sr->created_at?->year ?? now()->year)
            . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT);
    }
}