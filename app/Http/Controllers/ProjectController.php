<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Warranty;
use App\Models\Client;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('client')->latest()->get();
        $clients  = Client::orderBy('company_name')->get();
        $warranties = Warranty::where('status', 1)->orderBy('name')->get();

        $stats = [
            'total'    => $projects->count(),
            'active'   => $projects->where('status','Active')->count(),
            'inactive' => $projects->where('status','Inactive')->count(),
            'clients'  => $projects->pluck('client_id')->unique()->count(),
        ];

        return view('project_site', compact('projects','clients','stats','warranties'));
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