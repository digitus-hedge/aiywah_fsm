<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('client')->latest()->get();
        $clients  = Client::orderBy('company_name')->get();

        $stats = [
            'total'    => $projects->count(),
            'active'   => $projects->where('status','Active')->count(),
            'inactive' => $projects->where('status','Inactive')->count(),
            'clients'  => $projects->pluck('client_id')->unique()->count(),
        ];

        return view('project_site', compact('projects','clients','stats'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Project::create($data);
        return response()->json(['ok'=>true,'message'=>'Project created.']);
    }

    public function update(Request $request, Project $project)
    {
        $data = $this->validated($request);
        $project->update($data);
        return response()->json(['ok'=>true,'message'=>'Project updated.']);
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json(['ok'=>true,'message'=>'Project deleted.']);
    }

    

    private function validated(Request $request): array
    {
        return $request->validate([
            'client_id'    => 'required|exists:clients,id',
            'project_name' => 'required|string|max:255',
            'project_code' => 'nullable|string|max:50',
            'site_name'    => 'required|string|max:255',
            'completion_date' => 'nullable|date', // <-- CRITICAL: Must be registered here
            'site_address' => 'required|string',
            'status'       => 'required|in:Active,Inactive',
        ]);
    }
}