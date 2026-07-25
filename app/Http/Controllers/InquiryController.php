<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class InquiryController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $data = $request->validate([
            'service_type_id'   => ['required', 'exists:service_categories,id'],
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

            return ServiceRequest::create([
                'client_id'         => $project->client_id,
                'project_id'        => $project->id,
                'project_site'      => $project->site_name,
                'service_type_id'   => $data['service_type_id'],
               'reported_by' => optional($project->client)->contact_name ?? 'Unknown',
                'priority_level'    => $data['priority_level'],
                'issue_description' => $data['issue_description'],
                'internal_remark'   => $data['internal_remark'] ?? null,
                'status'            => 'Pending',
                'warranty_scope'    => $data['warranty_scope'],
                'attachments'       => $attachments,
            ]);
        });

        return redirect()
            ->route('projects.show', $project)
            ->with('success', "{$sr->code} created. WhatsApp receipt sent to client.");
    }
}