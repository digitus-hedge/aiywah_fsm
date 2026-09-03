<?php

namespace App\Http\Controllers;

use App\Models\Warranty;
use App\Models\Client;
use App\Models\Project;
use App\Models\ServiceRequest;
use App\Models\ClientMobile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Jobs\SendClientWelcomeNotifications;
use App\Services\SrTrackingService;
class ClientController extends Controller
{
    /**
     * Client Directory - searchable, filterable, paginated listing.
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
            'activeClients' => $activeClients,
            'totalProjects' => $totalProjects,
            'totalContacts' => $totalContacts,
            'recentCount'   => $recentCount,
            'countries'     => $countries,
            'currentStatus' => $status,
            'warranties'    => $warranties,
            'canDeleteClients' => auth()->user()?->hasAnyAccess('client_delete') ?? false,
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

    public function __construct(private SrTrackingService $tracking) {}

    public function job_tracking($id)
    {
        $sr = ServiceRequest::with([
            'client:id,company_name,unique_code',
            'category:id,category_name,icon,color_code',
            'domain:id,service_category_id,domain_name',
            'assignedUser:id,name',
            'project:id,project_name,project_code,site_name,site_address',
            'punch',
        ])->findOrFail($id);

        return view('job_tracking', [
            'sr'         => $sr,
            'milestones' => $this->buildMilestones($sr),
            'history'    => $this->buildHistory($sr),
            'statusMeta' => $this->statusMeta($sr->status),
        ]);
    }

    public function job_tracking_data($id)
    {
        $sr = ServiceRequest::with(['client', 'category', 'assignedUser', 'punch'])->findOrFail($id);

        return response()->json([
            'milestones' => $this->buildMilestones($sr),
            'history'    => $this->buildHistory($sr),
            'status'     => $this->statusMeta($sr->status),
            'punch_in'   => optional(optional($sr->punch)->punch_in_at)->toIso8601String(),
        ]);
    }

    public function show($id)
    {
        $client = Client::findOrFail($id);

        $projects = Project::where('client_id', $client->id)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->get();

        // Count service requests per project for this client
        $srCounts = ServiceRequest::where('client_id', $client->id)
            ->selectRaw('project_id, COUNT(*) as total')
            ->groupBy('project_id')
            ->pluck('total', 'project_id');   // [project_id => count]

        $mobiles = ClientMobile::where('client_id', $client->id)
            ->orderBy('id')
            ->get();

        $projectsJs = $projects->map(function ($p) use ($srCounts) {
            return [
                'id'             => $p->id,
                'code'           => $p->project_code,
                'name'           => $p->project_name,
                'siteName'       => $p->site_name,
                'siteAddress'    => $p->site_address,
                'project_engineer'  => $p->project_engineer,
                'engineer_contact' => $p->engineer_contact,
                'engineer_country'  => $p->engineer_country,
                'startDate'      => optional($p->created_at)->format('d M Y'),
                'completionDate' => optional($p->completion_date)->format('Y-m-d'),  // <-- add
                'warrantyId'     => $p->warranty_id,                                  // <-- add
                'srCount'        => $srCounts[$p->id] ?? 0,
                'active'         => in_array(strtolower($p->status ?? ''), ['active', '1']),
            ];
        })->values();

        // Project-created events - one per project_code, newest kept
        $projectActivity = $projects->sortByDesc('created_at')
            ->unique('project_code')
            ->map(fn($p) => [
                'type'   => 'project',
                'status' => 'created',
                'title'  => "Project {$p->project_code} - Created",
                'sub'    => $p->project_name,
                'time'   => $p->created_at,
            ]);

        // Recent service requests for this client
        $recentSrs = ServiceRequest::where('client_id', $client->id)
            ->orderByDesc('created_at')
            ->limit(6)
            ->get()
            ->map(fn($sr) => [
                'type'   => 'sr',
                'sr_id'  => $sr->id,
                'status' => strtolower($sr->status ?? 'pending'),
                'title'  => 'SR-' . optional($sr->created_at)->format('Y') . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT)
                    . ' - ' . ucfirst($sr->status ?? 'Pending'),
                'sub'    => $sr->project_site ?? $sr->reported_by ?? '-',
                'time'   => $sr->created_at,
            ]);

        // Only the single most-recent project as an activity item
        $latestProject = $projects->sortByDesc('created_at')->first();

        $projectActivity = collect();
        if ($latestProject) {
            $projectActivity->push([
                'type'   => 'project',
                'status' => 'created',
                'title'  => "Project {$latestProject->project_code} - Created",
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
        $avgRating   = '-';

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
        $warranties     = Warranty::where('status', 1)->orderBy('name')->get();

        return view('client_accounts', [
            'recentClients'  => $recentClients,
            'existingTokens' => $existingTokens,
            'warranties'     =>  $warranties,
            'suggestedCode'  => $client->unique_code,
            'client'         => $client,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);
        $client = Client::findOrFail($validated['client_id']);

        if ($client->status !== 'Active') {
            return back()
                ->withInput()
                ->with('error', "Cannot create a service request - {$client->company_name} is currently marked Inactive. Please activate the customer account first.");
        }

        $client = DB::transaction(function () use ($request, $validated) {
        
            $client = Client::create([
                'company_name'    => $validated['company_name'],
                'status'          => 'Active',
                'unique_code'     => $validated['unique_code'],
                'contact_name'    => $validated['contact_name'],
                'designation'     => $validated['designation'] ?? null,
                'primary_country' => $validated['primary_country'] ?? null,
                'primary_mobile'  => $validated['primary_mobile'],
                'email'           => $validated['email'],
            ]);
            $this->syncMobiles($client, $request, $validated);
            $this->syncProjects($client, $validated);

            return $client;
        });

        // Send welcome notifications (outside transaction)
        $firstProject = $client->projects()->oldest('id')->first();

        $portalUrl = route('portal.client', ['code' => $client->unique_code]);

       // Welcome email + WhatsApp are slow network calls - queue them so the
        // redirect fires as soon as the client and projects are written.
        $firstProject = $client->projects()->oldest('id')->first();

        SendClientWelcomeNotifications::dispatch(
            $client->id,
            $firstProject?->id,
            route('portal.client', ['code' => $client->unique_code]),
        );
        return redirect()
            ->route('clients.create')
            ->with('success', true)
            ->with('saved_token', $client->unique_code);
    }

    public function update(Request $request, Client $client)
    {
        // Firm name + token are locked - ignore any posted changes, keep stored values
        $validated = $this->validateData($request, $client->id, locked: true);

        $newProjectIds = [];

        DB::transaction(function () use ($request, $validated, $client, &$newProjectIds) {

            $client->update([
                'company_name'    => $validated['company_name'],
                'contact_name'    => $validated['contact_name'],
                'designation'     => $validated['designation'] ?? null,
                'primary_country' => $validated['primary_country'] ?? null,
                'primary_mobile'  => $validated['primary_mobile'],
                'email'           => $validated['email'],
            ]);

            // Rebuild stakeholder mobiles + projects from the submitted form
            // (primary mobile now lives on the clients table itself)
            $client->mobiles()->forceDelete();

            $this->syncMobiles($client, $request, $validated);
            $newProjectIds = $this->syncProjects($client, $validated);
        });

        // Notify only about projects created in this save (outside transaction)
        if ($newProjectIds) {
            $wa    = app(\App\Services\WhatsAppService::class);
            $fresh = $client->fresh();

           // Notify only about projects created in this save.
            foreach ($newProjectIds as $projectId) {
                SendClientWelcomeNotifications::dispatch(
                    $client->id,
                    $projectId,
                    null,
                    SendClientWelcomeNotifications::EVENT_PROJECT_ADDED,
                );
            }
        }

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
            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                'unique:clients,email' . ($locked && $clientId ? ",{$clientId}" : ''),
            ],

            'stakeholders'             => ['nullable', 'array'],
            'stakeholders.*.name'      => ['nullable', 'string', 'max:255'],
            'stakeholders.*.country'   => ['nullable', 'string', 'max:6'],
            'stakeholders.*.mobile'    => ['nullable', 'string', 'max:15'],

            'projects'                 => ['required', 'array', 'min:1'],
            'projects.*.id'            => ['nullable', 'integer', 'exists:projects,id'],
            'projects.*.project_name'  => ['required', 'string', 'max:255'],
            'projects.*.project_code' => ['nullable', 'string', 'max:50'],
            'projects.*.site_name'     => ['required', 'string', 'max:255'],
            'projects.*.site_address'  => ['required', 'string', 'max:1000'],
            'projects.*.completion_date'    => ['required', 'date'],
            'projects.*.warranty_id' => 'required|exists:warranties,id',
            'projects.*.warranty_end_date'  => ['nullable', 'date'],
            'projects.*.project_engineer'   => ['nullable', 'string', 'max:255'],
            'projects.*.engineer_contact'   => ['nullable', 'digits_between:7,15'],
            'projects.*.engineer_country'  => ['nullable', 'string', 'max:6'],

        ];

        // Firm name is always editable
        $rules['company_name'] = ['required', 'string', 'max:255'];

        // Unique code is only validated/editable on create; locked on update
        if (! $locked) {
            $rules['unique_code'] = ['required', 'string', 'max:20', 'unique:clients,unique_code'];
        }

        $validated = $request->validate($rules, [
            'primary_mobile.unique' => 'This primary mobile number is already registered with another client.',
            'email.unique'  => 'This email address is already registered with another client.',
            'email.email'   => 'Please enter a valid email address.',

            'projects.*.project_name.required'    => 'Project name is required for project',
            'projects.*.site_name.required'       => 'Site name is required for project',
            'projects.*.site_address.required'       => 'Site Address is required for project',
            'projects.*.completion_date.required' => 'Completion date is required for project',
            'projects.*.completion_date.date'     => 'Enter a valid completion date for project',
            'projects.*.warranty_id.required'     => 'Select a warranty period for project',
            'projects.*.warranty_id.exists'       => 'The selected warranty is not valid for project',
            'projects.*.engineer_contact.digits_between' => 'Engineer contact for project must be 7–15 digits.',
            'projects.min'                        => 'Add at least one project.',
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
        foreach ($request->input('stakeholders', []) as $sh) {
            if (empty($sh['mobile'])) {
                continue;
            }
            $client->mobiles()->create([
                'name'    => $sh['name'] ?? null,
                'country' => $sh['country'] ?? null,
                'mobile'  => $sh['mobile'],
                'notify'  => (int) ($sh['notify'] ?? 0),
            ]);
        }
    }

    private function syncProjects(Client $client, array $validated): array
    {
        $incomingIds = [];
        $createdIds  = [];

        foreach ($validated['projects'] as $project) {

            $warrantyDays = 0;
            if (!empty($project['warranty_id'])) {
                $warranty = Warranty::find($project['warranty_id']);
                $warrantyDays = (int) ($warranty->value ?? 0);
            }

            $warrantyEndDate = !empty($project['completion_date'])
                ? Carbon::parse($project['completion_date'])->addDays($warrantyDays)->toDateString()
                : null;

            $data = [
                'project_name'      => $project['project_name'],
                'site_name'         => $project['site_name'] ?? null,
                'site_address'      => $project['site_address'] ?? null,
                'completion_date'   => $project['completion_date'] ?? null,
                'warranty_id'       => $project['warranty_id'] ?? null,
                'warranty_end_date' => $warrantyEndDate,
                'project_engineer'  => $project['project_engineer'] ?? null,
                'engineer_contact'  => $project['engineer_contact'] ?? null,
                'engineer_country'  => $project['engineer_country'] ?? null,

            ];
            // note: project_code deliberately NOT in $data

            if (!empty($project['id'])) {
                $model = $client->projects()->find($project['id']);
                if ($model) {
                    $model->update($data);
                    $incomingIds[] = $model->id;
                    continue;
                }
            }

            $data['project_code'] = $this->generateProjectCode();
            $new = $client->projects()->create($data);
            $incomingIds[] = $new->id;
            $createdIds[]  = $new->id;
        }

        $client->projects()->whereNotIn('id', $incomingIds)->forceDelete();
        return $createdIds;
    }

    public function showFeedback($id)
    {
        $serviceRequest = ServiceRequest::with('category')
            ->where('id', $id)
            ->where('status', 'Completed')
            ->firstOrFail();

        return view('client_feedback', compact('serviceRequest'));
    }

    private function generateProjectCode(): string
    {
        do {
            $code = 'PRJ-' . now()->year . '-' . strtoupper(Str::random(6));
        } while (Project::withTrashed()->where('project_code', $code)->exists());

        return $code;
    }

    public function storeFeedback(Request $request, $id)
    {
        $validated = $request->validate([
            'performance_score' => 'required|integer|min:1|max:5',
            'evaluation_comment' => 'nullable|string|max:500',
        ]);

        \Log::info('Incoming', [
            'raw' => $request->all(),
            'validated' => $validated,
        ]);

        $serviceRequest = ServiceRequest::where('id', $id)
            ->where('status', 'Completed')
            ->firstOrFail();

        // Prevent double submission
        if ($serviceRequest->feedback_submitted_at) {
            return response()->json(['message' => 'Feedback already submitted.'], 409);
        }

        $serviceRequest->update([
            'performance_score'     => $validated['performance_score'],
            'evaluation_comment'    => $validated['evaluation_comment'] ?? null,
            'feedback_submitted_at' => now(),
        ]);

        \Log::info('Feedback saved', [
            'id' => $serviceRequest->id,
            'score' => $serviceRequest->performance_score,
            'fresh' => $serviceRequest->fresh()->only(['performance_score', 'evaluation_comment', 'feedback_submitted_at']),
        ]);

        return response()->json(['message' => 'Feedback recorded successfully.']);
    }


    public function preview($id)
    {
        $sr = ServiceRequest::with([
            'client',
            'project',
            'category',
            'assignedUser',
            'punches.user',
            'punches.items',
        ])
            ->where('id', $id)
            ->where('status', 'Completed')
            ->firstOrFail();

        $c = $sr->client;
        $p = $sr->project;

        $punches = $sr->punches->map(function ($pn) {
            return [
                'id'          => $pn->id,
                'technician'  => optional($pn->user)->name ?? '-',
                'punch_in'    => $pn->punch_in_at  ? \Carbon\Carbon::parse($pn->punch_in_at)->format('d M Y, h:i A')  : '-',
                'punch_out'   => $pn->punch_out_at ? \Carbon\Carbon::parse($pn->punch_out_at)->format('d M Y, h:i A') : '-',
                'location'    => $pn->site_location ?: '-',
                'work'        => $pn->work_description ?: '-',
                'summary'     => $pn->completion_summary ?: '-',
                'receipt_no'  => $pn->receipt_number ?: '-',
                'cust_name'   => $pn->customer_name ?: '-',
                'cust_phone'  => $pn->customer_phone ?: '-',
                'status'      => $pn->status,
                'notes'       => $pn->notes ?: '-',
                'materials'   => number_format((float) $pn->materials_subtotal, 2),
                'labour'      => number_format((float) $pn->labour_charge, 2),
                'grand_total' => number_format((float) $pn->grand_total, 2),
                'photos' => [
                    'start'     => $pn->start_photo_path        ? asset('storage/' . $pn->start_photo_path)        : null,
                    'finish'    => $pn->finish_photo_path       ? asset('storage/' . $pn->finish_photo_path)       : null,
                    'signature' => $pn->customer_signature_path ? asset('storage/' . $pn->customer_signature_path) : null,
                ],
                'items' => $pn->items->map(fn($i) => [
                    'name'     => $i->name,
                    'category' => $i->category,
                    'qty'      => number_format((float) $i->qty, 2),
                    'rate'     => number_format((float) $i->rate, 2),
                    'total'    => number_format((float) $i->line_total, 2),
                    'recon'    => $i->recon_status ?? 'pending',
                    'receipt'  => $i->receipt_path ? asset('storage/' . $i->receipt_path) : null,
                ])->values(),
            ];
        })->values();

        return response()->json([
            'ref' => 'SR-' . $sr->created_at->format('Y') . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT),

            // clients table
            'client_company'  => $c->company_name    ?? '-',
            'client_code'     => $c->unique_code     ?? '-',
            'client_contact'  => $c->contact_name    ?? '-',
            'client_phone'    => $c ? trim(($c->primary_country ?? '') . ' ' . ($c->primary_mobile ?? '')) : '-',
            'client_desig'    => $c->designation     ?? '-',
            'client_status'   => $c->status          ?? '-',

            // projects table
            'proj_name'       => $p->project_name        ?? '-',
            'proj_code'       => $p->project_code        ?? '-',
            'site_name'       => $p->site_name           ?? ($sr->project_site ?: '-'),
            'site_address'    => $p->site_address        ?? '-',
            'proj_status'     => $p->status              ?? '-',
            'proj_completion' => $p && $p->completion_date   ? \Carbon\Carbon::parse($p->completion_date)->format('d M Y')   : '-',
            'warranty_end'    => $p && $p->warranty_end_date ? \Carbon\Carbon::parse($p->warranty_end_date)->format('d M Y') : '-',

            'warranty'          => ($sr->project
                && $sr->project->warranty_end_date
                && \Carbon\Carbon::parse($sr->project->warranty_end_date)->endOfDay()->isFuture())
                ? 'In Warranty'
                : 'Out of Warranty',
            // service_requests table
            'category'   => optional($sr->category)->category_name ?? '-',
            'priority'   => $sr->priority_level,
            'issue'      => $sr->issue_description,
            'reported'   => $sr->reported_by,
            'sr_status'  => $sr->status,
            'dispatched' => $sr->dispatched_at ? \Carbon\Carbon::parse($sr->dispatched_at)->format('d M Y, h:i A') : '-',
            'accepted'   => $sr->accepted_at   ? \Carbon\Carbon::parse($sr->accepted_at)->format('d M Y, h:i A')   : '-',
            'tech'       => optional($sr->assignedUser)->name ?? '-',

            'punches' => $punches,
            'total' => number_format(
                (float) $sr->punches->sum(fn($pn) => (float) $pn->items->sum('line_total')),
                2
            ),
        ]);
    }


    public function lookupByName(Request $request)
    {
        $name = trim((string) $request->query('name', ''));

        if ($name === '') {
            return response()->json(['found' => false]);
        }

        $client = Client::where('company_name', $name)
            ->where('status', 'Active')
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
            'email'           => $client->email,          // ← was missing

            'primary_country' => $client->primary_country,
            'primary_mobile'  => $client->primary_mobile,
            'mobiles'         => $client->mobiles->map(fn($m) => [
                'name'    => $m->name,
                'country' => $m->country,
                'mobile'  => $m->mobile,
            ])->values(),
            'projects' => $client->projects->map(fn($p) => [
                'id'               => $p->id,
                'project_name'     => $p->project_name,
                'project_code'     => $p->project_code,
                'site_name'        => $p->site_name,
                'site_address'     => $p->site_address,
                'project_engineer' => $p->project_engineer,
                'engineer_contact' => $p->engineer_contact,
                'engineer_country' => $p->engineer_country,
                'warranty_id'      => $p->warranty_id ? (int) $p->warranty_id : null,
                'completion_date' => optional($p->completion_date)->format('Y-m-d'),
                'warranty_end_date' => optional($p->warranty_end_date)->format('Y-m-d'),
            ])->values(),
        ]);
    }

    /** Public read-only project page for the customer. */
    public function portal(Request $request, string $code)
    {
        $project = Project::with('client')
            ->where('project_code', $code)
            ->whereNull('deleted_at')
            ->firstOrFail();

        $cards = ServiceRequest::with([
            'category:id,category_name',
            'assignedUser:id,name',
            'punches.user:id,name',
            'punches.items',
        ])
            ->where('project_id', $project->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($sr) => $this->portalCard($sr));

        $warrantyEnd = $project->warranty_end_date
            ? Carbon::parse($project->warranty_end_date)->endOfDay()
            : null;

        $inWarranty = $warrantyEnd && $warrantyEnd->isFuture();

        // only honour ?sr= if that request belongs to this project
        $focusSr = (int) $request->query('sr', 0);
        if ($focusSr && ! $cards->firstWhere('id', $focusSr)) {
            $focusSr = 0;
        }

        return view('portal.project', [
            'project'     => $project,
            'client'      => $project->client,
            'cards'       => $cards,
            'focusSr'     => $focusSr,
            'openCount'   => $cards->where('is_open', true)->count(),
            'closedCount' => $cards->where('is_open', false)->count(),
            'photoCount'  => $cards->sum(fn($c) => count($c['photos'])),
            'inWarranty'  => $inWarranty,
            'warrantyEnd' => $warrantyEnd,
            'daysLeft'    => $inWarranty ? (int) now()->startOfDay()->diffInDays($warrantyEnd) : 0,
        ]);
    }

    /** Flatten one service request into everything the customer card renders. */
    private function portalCard(ServiceRequest $sr): array
    {
        $meta = $this->tracking->statusMeta($sr->status);

        // staff chip classes -> the tone classes the portal blade uses
        $tone = [
            'chip-ok'      => 'ok',
            'chip-warn'    => 'warn',
            'chip-bad'     => 'bad',
            'chip-info'    => 'info',
            'chip-inprog'  => 'live',
            'chip-pending' => 'wait',
        ][$meta['chip']] ?? 'wait';

        $punches = $sr->punches->map(fn($pn) => [
            'technician' => optional($pn->user)->name ?: '-',
            'duration'   => ($pn->punch_in_at && $pn->punch_out_at)
                ? Carbon::parse($pn->punch_in_at)->diff(Carbon::parse($pn->punch_out_at))->format('%hh %im')
                : null,
            'work'    => $pn->work_description ?: null,
            'summary' => $pn->completion_summary ?: null,
            'before'  => $pn->start_photo_path  ? asset('storage/' . ltrim($pn->start_photo_path, '/'))  : null,
            'after'   => $pn->finish_photo_path ? asset('storage/' . ltrim($pn->finish_photo_path, '/')) : null,
            'when'    => ($pn->punch_out_at ?: $pn->punch_in_at)
                ? Carbon::parse($pn->punch_out_at ?: $pn->punch_in_at)->format('d M Y · h:i A')
                : null,
            'items'   => $pn->items->map(fn($i) => [
                'name' => $i->name,
                'qty'  => rtrim(rtrim(number_format((float) $i->qty, 2), '0'), '.'),
            ])->values()->all(),
        ])->values();

        // before / after pairs, one entry per site visit
        $photos = [];
        foreach ($punches as $i => $pn) {
            if ($pn['before'] || $pn['after']) {
                $photos[] = [
                    'visit'  => 'Visit ' . ($i + 1),
                    'before' => $pn['before'],
                    'after'  => $pn['after'],
                    'when'   => $pn['when'],
                ];
            }
        }

        // stored paperwork the customer can download
        $docs = [];

    if ($sr->quote_path) {
        $docs[] = [
            'kind'  => 'Quotation',
            'label' => $sr->erp_quote_ref ?: 'Quotation',
            'icon'  => 'bi-receipt',
            'url'   => asset('storage/' . ltrim($sr->quote_path, '/')),
        ];
    }

    if ($sr->invoice_path) {
        $docs[] = [
            'kind'  => 'Invoice',
            'label' => $sr->invoice_code ?: 'Invoice',
            'icon'  => 'bi-file-earmark-text',
            'url'   => asset('storage/' . ltrim($sr->invoice_path, '/')),
        ];
    }

        foreach ($sr->punches as $i => $pn) {
            if ($pn->customer_signature_path) {
                $docs[] = [
                    'kind'  => 'Signed job sheet',
                    'label' => 'Visit ' . ($i + 1) . ' - signed',
                    'icon'  => 'bi-pen',
                    'url'   => asset('storage/' . ltrim($pn->customer_signature_path, '/')),
                ];
            }
        }

        $completed = $sr->status === 'Completed';

        return [
            'id'         => $sr->id,
            'ref'        => 'SR-' . optional($sr->created_at)->format('Y') . '-' . str_pad($sr->id, 5, '0', STR_PAD_LEFT),
            'label'      => $meta['label'],
            'tone'       => $tone,
            'icon'       => $meta['icon'],
            'color'      => $meta['color'],
            'is_open'    => ! in_array($sr->status, ['Completed', 'Rejected', 'Quote Rejected'], true),
            'category'   => optional($sr->category)->category_name ?: 'General service',
            'priority'   => $sr->priority_level,
            'issue'      => $sr->issue_description,
            'technician' => optional($sr->assignedUser)->name,
            'coverage'   => strtolower((string) $sr->warranty_scope) === 'oow' ? 'Chargeable' : 'Covered by warranty',
            'logged'     => optional($sr->created_at)->format('d M Y'),
            'timeline'   => $this->portalTimeline($sr),
            'punches'    => $punches->all(),
            'photos'     => $photos,
            'docs'       => $docs,
            'rated'      => (bool) $sr->feedback_submitted_at,
            'score'      => $sr->performance_score,
            'can_rate'   => $completed && ! $sr->feedback_submitted_at,
            'feedback_url' => $completed
                ? \Illuminate\Support\Facades\URL::signedRoute('clients.feedback.show', ['id' => $sr->id])
                : null,
        ];
    }

    private function portalTimeline(ServiceRequest $sr): array
    {
        $first = $sr->punches->first();

        $steps = [
            ['Request received',    $sr->created_at,    'We logged your request'],
            ['Request approved',    $sr->accepted_at,   'Coverage confirmed'],
            ['Technician assigned', $sr->dispatched_at, optional($sr->assignedUser)->name],
        ];

        if (strtolower((string) $sr->warranty_scope) === 'oow') {
            $steps[] = ['Quote sent',     $sr->quote_submitted_at, $sr->erp_quote_ref];
            $steps[] = ['Quote approved', $sr->client_approved_at, 'Work authorised'];
        }

        $steps[] = ['Work started',  optional($first)->punch_in_at, 'Technician on site'];
        $steps[] = ['Work finished', optional($sr->punches->last())->punch_out_at, 'Site handed back'];
        $steps[] = ['Quality check', $sr->qc_reviewed_at, 'Reviewed by supervisor'];
        $steps[] = ['Closed', $sr->status === 'Completed' ? $sr->updated_at : null, 'Job complete'];

        return collect($steps)->map(fn($s) => [
            'label' => $s[0],
            'note'  => $s[2] ?: '',
            'time'  => $s[1] ? Carbon::parse($s[1])->format('d M Y · h:i A') : null,
            'done'  => (bool) $s[1],
        ])->all();
    }

    /** Public landing page - all projects belonging to one client. */
    public function portalClient(string $code)
    {
        $client = Client::where('unique_code', $code)
            ->where('status', 'Active')
            ->firstOrFail();

        $projects = Project::where('client_id', $client->id)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->get();

        // open / total SR counts per project, one query
        $counts = ServiceRequest::whereIn('project_id', $projects->pluck('id'))
            ->selectRaw('project_id,
                     COUNT(*) as total,
                     SUM(status NOT IN ("Completed","Rejected","Quote Rejected")) as open')
            ->groupBy('project_id')
            ->get()
            ->keyBy('project_id');

        $rows = $projects->map(function ($p) use ($counts) {
            $end = $p->warranty_end_date ? Carbon::parse($p->warranty_end_date)->endOfDay() : null;
            $c   = $counts[$p->id] ?? null;

            return [
                'code'        => $p->project_code,
                'name'        => $p->project_name,
                'site'        => trim($p->site_name . ($p->site_address ? ', ' . $p->site_address : ''), ' ,'),
                'handover'    => $p->completion_date ? Carbon::parse($p->completion_date)->format('d M Y') : null,
                'engineer'    => $p->project_engineer,
                'engineerTel' => $p->project_engineer ? ($p->engineer_country . $p->engineer_contact) : null,
                'total'       => (int) ($c->total ?? 0),
                'open'        => (int) ($c->open  ?? 0),
                'inWarranty'  => $end && $end->isFuture(),
                'warrantyEnd' => $end,
                'url'         => route('portal.project', ['code' => $p->project_code]),
            ];
        });

        return view('portal.client', [
            'client'      => $client,
            'rows'        => $rows,
            'openTotal'   => $rows->sum('open'),
            'srTotal'     => $rows->sum('total'),
            'coveredCount' => $rows->where('inWarranty', true)->count(),
        ]);
    }
    public function destroy(Client $client)
    {
        abort_unless(auth()->user()?->hasAnyAccess('client_delete'), 403);

        // Optional: block deleting a client that still has open service requests
        $openSrCount = ServiceRequest::where('client_id', $client->id)
            ->whereNotIn('status', ['Completed', 'Rejected', 'Quote Rejected'])
            ->count();

        if ($openSrCount > 0) {
            return response()->json([
                'message' => "This customer has {$openSrCount} open service request(s). Resolve or close them before deleting."
            ], 422);
        }

        $client->delete(); // soft delete

        return response()->json(['ok' => true]);
    }
}
