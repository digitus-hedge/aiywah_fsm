<?php

namespace App\Http\Controllers;

use App\Models\Warranty;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClientController extends Controller
{
    /**
     * Client Directory — searchable, filterable, paginated listing.
     */
    public function directory(Request $request)
    {

        $q       = trim((string) $request->query('q', ''));
        $country = trim((string) $request->query('country', ''));
        $status  = trim((string) $request->query('status', '')); // <-- 1. Capture status input

        $clients = Client::query()
            // counts for the "Projects" and "Contacts" columns (no N+1)
            ->withCount(['projects', 'mobiles'])
            ->with(['mobiles', 'projects'])
            // search across firm name, token, contact name & primary mobile
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('company_name', 'like', "%{$q}%")
                        ->orWhere('unique_code', 'like', "%{$q}%")
                        ->orWhere('contact_name', 'like', "%{$q}%")
                        ->orWhere('primary_mobile', 'like', "%{$q}%");
                });
            })

            // filter by primary country dial code
            ->when($country !== '', fn($query) => $query->where('primary_country', $country))

            // filter by status ('Active' or 'Inactive')
            ->when($status !== '', fn($query) => $query->where('status', $status)) // <-- 2. Apply status filter

            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();   // keep ?q=, ?country=, & ?status= across pages

        // ── Stat-strip figures ──
        $totalClients  = Client::count();
        $activeClients = Client::where('status', 'Active')->count();   // <-- 3. New Dynamic Stat
        $totalProjects = DB::table('projects')->whereNull('deleted_at')->count(); // Added soft-delete check safety
        $totalContacts = DB::table('client_mobiles')->count() + $totalClients;
        $recentCount   = Client::where('created_at', '>=', now()->startOfMonth())->count();


        $warranties = Warranty::where('status', 1)->orderBy('name')->get();

        // ── Distinct countries for the filter dropdown ──
        $countries = Client::query()
            ->whereNotNull('primary_country')
            ->where('primary_country', '!=', '')
            ->distinct()
            ->orderBy('primary_country')
            ->pluck('primary_country');

        return view('client_directory', [
            'clients'       => $clients,
            'totalClients'  => $totalClients,
            'activeClients' => $activeClients, // <-- Pass to view
            'totalProjects' => $totalProjects,
            'totalContacts' => $totalContacts,
            'recentCount'   => $recentCount,
            'countries'     => $countries,
            'currentStatus' => $status,        // <-- Pass to keep selection highlighted
            'warranties' => $warranties,
        ]);
    }

    public function toggleStatus(Client $client)
    {
        // Fix casing to match database Enum properties ('Active' / 'Inactive')
        $client->status = $client->status === 'Active' ? 'Inactive' : 'Active';
        $client->save();

        return response()->json([
            'ok' => true,
            'status' => $client->status,
            'message' => 'Status updated to ' . $client->status
        ]);
    }

    public function create()
    {
        $recentClients  = Client::latest()->take(5)->get();
        $existingTokens = Client::pluck('unique_code')->toArray();
        $suggestedCode  = $this->nextCode();
        $warranties = Warranty::where('status', 1)->orderBy('name')->get();

        return view('client_accounts', [
            'recentClients'  => $recentClients,
            'existingTokens' => $existingTokens,
            'suggestedCode'  => $suggestedCode,
            'warranties'    =>  $warranties,
            'client'         => null,
        ]);
    }


    public function show($id)
    {
        $client = \App\Models\Client::findOrFail($id);

        $projects = \App\Models\Project::where('client_id', $client->id)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->get();

        // Count service requests per project for this client
        $srCounts = \App\Models\ServiceRequest::where('client_id', $client->id)
            ->selectRaw('project_id, COUNT(*) as total')
            ->groupBy('project_id')
            ->pluck('total', 'project_id');   // [project_id => count]

        $mobiles = \App\Models\ClientMobile::where('client_id', $client->id)
            ->orderBy('id')
            ->get();

       $projectsJs = $projects->map(function ($p) use ($srCounts) {
    return [
        'id'             => $p->id,
        'code'           => $p->project_code,
        'name'           => $p->project_name,
        'siteName'       => $p->site_name,
        'siteAddress'    => $p->site_address,
        'contract'       => $p->contract_type ?? '—',
        'startDate'      => optional($p->created_at)->format('d M Y'),
        'completionDate' => optional($p->completion_date)->format('Y-m-d'),  // <-- add
        'warrantyId'     => $p->warranty_id,                                  // <-- add
        'srCount'        => $srCounts[$p->id] ?? 0,
        'active'         => in_array(strtolower($p->status ?? ''), ['active', '1']),
    ];
})->values();

        // Project-created events — one per project_code, newest kept
        $projectActivity = $projects
            ->sortByDesc('created_at')
            ->unique('project_code')
            ->map(fn($p) => [
                'type'   => 'project',
                'status' => 'created',
                'title'  => "Project {$p->project_code} — Created",
                'sub'    => $p->project_name,
                'time'   => $p->created_at,
            ]);

        // Recent service requests for this client
        $recentSrs = \App\Models\ServiceRequest::where('client_id', $client->id)
            ->orderByDesc('created_at')
            ->limit(6)
            ->get()
            ->map(fn($sr) => [
                'type'   => 'sr',
                'status' => strtolower($sr->status ?? 'pending'),
                'title'  => 'SR-' . optional($sr->created_at)->format('Y') . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT)
                    . ' — ' . ucfirst($sr->status ?? 'Pending'),
                'sub'    => $sr->project_site ?? $sr->reported_by ?? '—',
                'time'   => $sr->created_at,
            ]);

        // Only the single most-recent project as an activity item
        $latestProject = $projects->sortByDesc('created_at')->first();

        $projectActivity = collect();
        if ($latestProject) {
            $projectActivity->push([
                'type'   => 'project',
                'status' => 'created',
                'title'  => "Project {$latestProject->project_code} — Created",
                'sub'    => $latestProject->project_name,
                'time'   => $latestProject->created_at,
            ]);
        }

        // Merge, drop null times, newest first, cap at 6
        $activity = $recentSrs->concat($projectActivity)
            ->filter(fn($a) => $a['time'])
            ->sortByDesc(fn($a) => $a['time'])
            ->take(5)
            ->values();


     $warranties = Warranty::where('status', 1)->orderBy('name')->get();


        $lifetimeSrs = 0;
        $activeSrs   = 0;
        $avgRating   = '—';

        return view('client_view', compact(
            'client',
            'projects',
            'projectsJs',
            'mobiles',
            'activity',
            'lifetimeSrs',
            'activeSrs',
            'avgRating',
            'warranties'
        ));
    }

      public function edit(Client $client)
    {
        $client->load(['mobiles', 'projects']);

        $recentClients  = Client::latest()->take(5)->get();
        $existingTokens = Client::pluck('unique_code');

        return view('client_accounts', [
            'recentClients'  => $recentClients,
            'existingTokens' => $existingTokens,
            'suggestedCode'  => $client->unique_code,
            'client'         => $client,
        ]);
    }
    

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        $client = DB::transaction(function () use ($request, $validated) {

            $client = Client::create([
                'company_name'    => $validated['company_name'],
                'unique_code'     => $validated['unique_code'],
                'contact_name'    => $validated['contact_name'],
                'designation'     => $validated['designation'] ?? null,
                'primary_country' => $validated['primary_country'] ?? null,
                'primary_mobile'  => $validated['primary_mobile'],
            ]);

            $this->syncMobiles($client, $request, $validated);
            $this->syncProjects($client, $validated);

            return $client;
        });

        return redirect()
            ->route('clients.create')
            ->with('success', true)
            ->with('saved_token', $client->unique_code);
    }

    public function update(Request $request, Client $client)
    {
        // Firm name + token are locked — ignore any posted changes, keep stored values
        $validated = $this->validateData($request, $client->id, locked: true);

        DB::transaction(function () use ($request, $validated, $client) {

            $client->update([
                'contact_name'    => $validated['contact_name'],
                'designation'     => $validated['designation'] ?? null,
                'primary_country' => $validated['primary_country'] ?? null,
                'primary_mobile'  => $validated['primary_mobile'],
            ]);

            // Rebuild stakeholder mobiles + projects from the submitted form
            // (primary mobile now lives on the clients table itself)
            // $client->mobiles()->delete();
            // $client->projects()->delete();


            $client->mobiles()->forceDelete();   // <-- changed
            $client->projects()->forceDelete();  // <-- changed

            $this->syncMobiles($client, $request, $validated);
            $this->syncProjects($client, $validated);
        });

        return redirect()
            ->route('clients.edit', $client)
            ->with('success', true)
            ->with('saved_token', $client->unique_code);
    }

    /* ── Helpers ── */

    private function nextCode(): string
    {
        $year = now()->year;

        $last = Client::where('unique_code', 'like', "CUST-{$year}%")
            ->orderByDesc('id')
            ->value('unique_code');

        $next = 1;

        if ($last && preg_match('/CUST-' . $year . '(\d{3})$/', $last, $matches)) {
            $next = (int) $matches[1] + 1;
        }

        return sprintf('CUST-%d%03d', $year, $next);
    }

    private function validateData(Request $request, ?int $clientId = null, bool $locked = false): array
    {
        $rules = [
            'contact_name'             => ['required', 'string', 'max:255'],
            'designation'              => ['nullable', 'string', 'max:255'],
            'primary_country'          => ['nullable', 'string', 'max:6'],

            // Primary mobile must be unique across clients (ignore current client on update)
            'primary_mobile'           => [
                'required',
                'string',
                'max:15',
                'unique:clients,primary_mobile' . ($locked && $clientId ? ",{$clientId}" : ''),
            ],

            'stakeholders'             => ['nullable', 'array'],
            'stakeholders.*.name'      => ['nullable', 'string', 'max:255'],
            'stakeholders.*.country'   => ['nullable', 'string', 'max:6'],
            'stakeholders.*.mobile'    => ['nullable', 'string', 'max:15'],

            'projects'                 => ['required', 'array', 'min:1'],
            'projects.*.project_name'  => ['required', 'string', 'max:255'],
            'projects.*.project_code'  => ['required', 'string', 'max:50'],
            'projects.*.site_name'     => ['nullable', 'string', 'max:255'],
            'projects.*.site_address'  => ['nullable', 'string', 'max:1000'],
            'projects.*.completion_date'    => ['nullable', 'date'],
            'projects.*.warranty_id' => 'nullable|exists:warranties,id',
            'projects.*.warranty_end_date'  => ['nullable', 'date'],
        ];

        // Firm name is always editable
        $rules['company_name'] = ['required', 'string', 'max:255'];

        // Unique code is only validated/editable on create; locked on update
        if (! $locked) {
            $rules['unique_code'] = ['required', 'string', 'max:20', 'unique:clients,unique_code'];
        }

        $validated = $request->validate($rules, [
            'primary_mobile.unique' => 'This primary mobile number is already registered with another client.',
        ]);

        // On update, keep ONLY the unique_code from the stored record
        if ($locked && $clientId) {
            $client = Client::findOrFail($clientId);
            $validated['unique_code'] = $client->unique_code;
        }

        return $validated;
    }

    private function syncMobiles(Client $client, Request $request, array $validated): void
    {
        // Stakeholders only — primary mobile now lives on the clients table itself
        foreach ($request->input('stakeholders', []) as $sh) {
            if (empty($sh['mobile'])) {
                continue;
            }
            $client->mobiles()->create([
                'name'    => $sh['name'] ?? null,
                'country' => $sh['country'],
                'mobile'  => $sh['mobile'],
            ]);
        }
    }

    private function syncProjects(Client $client, array $validated): void
    {
        foreach ($validated['projects'] as $project) {


            $warrantyDays = 0;

            if (!empty($project['warranty_id'])) {
                $warranty = Warranty::find($project['warranty_id']);
                $warrantyDays = (int) ($warranty->value ?? 0);
            }

            $warrantyEndDate = !empty($project['completion_date'])
                ? Carbon::parse($project['completion_date'])->addDays($warrantyDays)->toDateString()
                : null;


            $client->projects()->create([
                'project_name'      => $project['project_name'],
                'project_code'      => $project['project_code'],
                'site_name'         => $project['site_name'] ?? null,
                'site_address'      => $project['site_address'] ?? null,
                'completion_date'   => $project['completion_date'] ?? null,
                'warranty_id'       => $project['warranty_id'] ?? null,
                'warranty_end_date' => $warrantyEndDate,
            ]);
        }
    }

    public function lookupByName(Request $request)
    {
        $name = trim((string) $request->query('name', ''));

        if ($name === '') {
            return response()->json(['found' => false]);
        }

        $client = Client::where('company_name', $name)
            ->with(['mobiles', 'projects'])
            ->first();

        if (! $client) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found'           => true,
            'client_id'       => $client->id,
            'unique_code'     => $client->unique_code,
            'contact_name'    => $client->contact_name,
            'designation'     => $client->designation,
            'primary_country' => $client->primary_country,
            'primary_mobile'  => $client->primary_mobile,
            'mobiles'         => $client->mobiles->map(fn($m) => [
                'name'    => $m->name,
                'country' => $m->country,
                'mobile'  => $m->mobile,
            ])->values(),
            'projects' => $client->projects->map(fn($p) => [
                'project_name'    => $p->project_name,
                'project_code'    => $p->project_code,
                'site_name'       => $p->site_name,
                'site_address'    => $p->site_address,
                'completion_date' => optional($p->completion_date)->format('Y-m-d'),
            ])->values(),
        ]);
    }
}
