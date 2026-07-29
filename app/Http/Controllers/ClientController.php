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

    /* ── Status catalogue ── */
    private function statusCatalogue(): array
    {
        return [
            'Pending'           => ['label' => 'Pending',            'chip' => 'chip-pending', 'color' => '#7c3aed', 'icon' => 'bi-hourglass-split'],
            'Approved'          => ['label' => 'Approved',           'chip' => 'chip-ok',      'color' => '#15803d', 'icon' => 'bi-check-circle'],
            'Forwarded'         => ['label' => 'Forwarded',          'chip' => 'chip-info',    'color' => '#2563eb', 'icon' => 'bi-send'],
            'Rejected'          => ['label' => 'Rejected',           'chip' => 'chip-bad',     'color' => '#dc2626', 'icon' => 'bi-x-octagon'],
            'Assigned'          => ['label' => 'Assigned',           'chip' => 'chip-info',    'color' => '#2563eb', 'icon' => 'bi-person-check'],
            'Quoted'            => ['label' => 'Quoted',             'chip' => 'chip-warn',    'color' => '#b45309', 'icon' => 'bi-receipt'],
            'In Progress'       => ['label' => 'In Progress',        'chip' => 'chip-inprog',  'color' => '#0891b2', 'icon' => 'bi-wrench-adjustable-circle'],
            'Quote Rejected'    => ['label' => 'Quote Rejected',     'chip' => 'chip-bad',     'color' => '#dc2626', 'icon' => 'bi-x-circle'],
            'Qc Review'         => ['label' => 'Qc Review',          'chip' => 'chip-warn',    'color' => '#b45309', 'icon' => 'bi-clipboard2-check'],
            'Rework'            => ['label' => 'Rework',             'chip' => 'chip-warn',    'color' => '#ea580c', 'icon' => 'bi-arrow-repeat'],
            'Reschedule'        => ['label' => 'Reschedule',         'chip' => 'chip-warn',    'color' => '#ea580c', 'icon' => 'bi-calendar-event'],
            'Accepted'          => ['label' => 'Accepted',           'chip' => 'chip-ok',      'color' => '#15803d', 'icon' => 'bi-hand-thumbs-up'],
            'Pending Invoice'   => ['label' => 'Pending Invoice',    'chip' => 'chip-warn',    'color' => '#b45309', 'icon' => 'bi-file-earmark-text'],
            'Invoice Submitted' => ['label' => 'Invoice Submitted',  'chip' => 'chip-info',    'color' => '#2563eb', 'icon' => 'bi-file-earmark-check'],
            'Completed'         => ['label' => 'Completed',          'chip' => 'chip-ok',      'color' => '#15803d', 'icon' => 'bi-patch-check'],
            'On Hold'           => ['label' => 'On Hold',            'chip' => 'chip-pending', 'color' => '#64748b', 'icon' => 'bi-pause-circle'],
        ];
    }

    private function statusMeta(?string $s): array
    {
        return $this->statusCatalogue()[$s]
            ?? ['label' => $s ?: '—', 'chip' => 'chip-pending', 'color' => '#6b7280', 'icon' => 'bi-circle'];
    }

    /* ── Milestone graph ── */
    private function buildMilestones(ServiceRequest $sr): array
    {
        $terminal = ['Rejected', 'Quote Rejected'];
        $flow = in_array($sr->status, $terminal, true)
            ? ['Pending', $sr->status]
            : $this->flowFor($sr);

        $cat  = $this->statusCatalogue();
        $cur  = array_search($sr->status, $flow, true);
        $out  = [];

        foreach ($flow as $i => $key) {
            $m = $cat[$key];
            $out[] = [
                'key'   => $key,
                'label' => $m['label'],
                'icon'  => $m['icon'],
                'state' => $cur === false ? 'pending' : ($i < $cur ? 'done' : ($i === $cur ? 'active' : 'pending')),
                'time'  => optional($this->stampFor($sr, $key))->format('d M · h:i A') ?? '',
                'desc'  => $this->descFor($sr, $key),
            ];
        }
        return $out;
    }

    private function flowFor(ServiceRequest $sr): array
    {
        // OOW jobs pass through quoting; IW jobs skip it.
        $quote = $sr->warranty_scope === 'oow' ? ['Quoted'] : [];
        return array_merge(
            ['Pending', 'Approved', 'Assigned'],
            $quote,
            ['in_progress', 'qc_review', 'Pending Invoice', 'Invoice Submitted', 'Completed']
        );
    }

    private function stampFor(ServiceRequest $sr, string $key)
    {
        return [
            'Pending'           => $sr->created_at,
            'Approved'          => $sr->accepted_at,
            'Forwarded'         => $sr->dispatched_at,
            'Rejected'          => $sr->updated_at,
            'Assigned'          => $sr->dispatched_at,
            'Quoted'            => $sr->quote_submitted_at ?: $sr->client_approved_at,
            'In Progress'       => optional($sr->punch)->punch_in_at,
            'Quote Rejected'    => $sr->updated_at,
            'Qc Review'         => $sr->qc_reviewed_at,
            'Rework'            => $sr->qc_reviewed_at,
            'Reschedule'        => $sr->rescheduled_at ?? $sr->updated_at,
            'Accepted'          => $sr->client_approved_at ?: $sr->accepted_at,
            'Pending Invoice'   => optional($sr->punch)->punch_out_at,
            'Invoice Submitted' => $sr->invoice_submitted_at,
            'Completed'         => $sr->status === 'Completed' ? $sr->updated_at : null,
            'On Hold'           => $sr->on_hold_at ?? $sr->updated_at,
        ][$key] ?? null;
    }

    private function descFor(ServiceRequest $sr, string $key): string
    {
        return match ($key) {
            'Pending'           => 'SR Logged',
            'Approved'          => trim(strtoupper($sr->warranty_scope ?: '') . ' path confirmed'),
            'Forwarded'         => 'Forwarded to service partner',
            'Rejected'          => $sr->rejection_reason ?: 'Request rejected',
            'Assigned'          => 'Technician ' . (optional($sr->assignedUser)->name ?: 'pending assignment'),
            'Quoted'            => 'Quote ' . ($sr->erp_quote_ref ?: 'submitted') . ' sent to client',
            'In Progress'       => 'Technician on-site and working',
            'Quote Rejected'    => $sr->quote_rejection_reason ?: 'Quote rejected by client',
            'Qc Review'         => 'Quality check by supervisor',
            'Rework'            => $sr->rework_notes ?: 'Rework requested by QC',
            'Reschedule'        => $sr->reschedule_reason ?: 'Visit rescheduled',
            'Accepted'          => 'Quote accepted by client',
            'Pending Invoice'   => 'Awaiting invoice generation',
            'Invoice Submitted' => trim('Invoice ' . ($sr->invoice_code ?: '') . ' submitted'),
            'Completed'         => 'Work completed and closed',
            'On Hold'           => $sr->hold_reason ?: 'Request on hold',
            default             => '',
        };
    }

    /* ── Activity log ── */
    private function buildHistory(ServiceRequest $sr): array
    {
        $cat  = $this->statusCatalogue();
        $rows = [];

        $push = function ($key, $event, $meta, $ts) use (&$rows, $cat) {
            if (!$ts) return;
            $rows[] = [
                'color' => $cat[$key]['color'] ?? '#6b7280',
                'event' => $event,
                'meta'  => $meta,
                'ts'    => $ts,
            ];
        };

        $tech = optional($sr->assignedUser)->name;
        $push('Pending', 'Service Request logged — ' . $sr->sr_code . ' created', 'Logged by ' . ($sr->reported_by ?: 'Front Desk'), $sr->created_at);
        $push('Approved', 'SR approved — ' . strtoupper($sr->warranty_scope ?: '') . ' path', 'Contract coverage verified', $sr->accepted_at);
        $push('Forwarded', 'SR forwarded to service partner', $sr->forward_remark ?: 'Routed for dispatch', $sr->dispatched_at);
        $push('Assigned', ($tech ?: 'Technician') . ' accepted job and committed attendance', 'Dispatched by Head of Projects', $sr->dispatched_at);
        $push('Assigned', 'ETA confirmed — arriving at ' . optional($sr->eta_at)->format('h:i A'), 'WhatsApp notification sent', $sr->eta_at);
        $push('Quoted', 'Quote submitted — ' . ($sr->erp_quote_ref ?: ''), 'Awaiting client approval', $sr->quote_submitted_at);
        $push('Accepted', 'Quote accepted by client', 'Approval received — work authorised', $sr->client_approved_at);
        $push('Quote Rejected', 'Quote rejected by client', $sr->quote_rejection_reason ?: 'Client declined the quotation', $sr->status === 'Quote Rejected' ? $sr->updated_at : null);
        $push('Reschedule', 'Visit rescheduled', $sr->reschedule_reason ?: 'New slot agreed with client', $sr->rescheduled_at);
        $push('In Progress', 'Technician ' . ($tech ?: '') . ' punched in on-site', optional($sr->category)->category_name . ' — ' . ($sr->project_site ?: '—'), optional($sr->punch)->punch_in_at);
        $push('Pending Invoice', 'Technician punched out', optional($sr->punch)->completion_summary, optional($sr->punch)->punch_out_at);
        $push('Qc Review', 'QC review recorded', $sr->rework_notes ?: 'Passed quality check', $sr->qc_reviewed_at);
        $push('Rework', 'Rework requested by QC', $sr->rework_notes ?: 'Returned to technician', $sr->status === 'Rework' ? $sr->qc_reviewed_at : null);
        $push('Invoice Submitted', 'Invoice ' . ($sr->invoice_code ?: '') . ' submitted', 'Total: ' . $sr->invoice_total, $sr->invoice_submitted_at);
        $push('On Hold', 'SR placed on hold', $sr->hold_reason ?: $sr->internal_remark, $sr->on_hold_at);
        $push('Completed', 'Work completed and closed', $sr->closure_remark ?: 'SR closed', $sr->status === 'Completed' ? $sr->updated_at : null);
        $push('Rejected', 'SR rejected', $sr->internal_remark, $sr->status === 'Rejected' ? $sr->updated_at : null);

        usort($rows, fn($a, $b) => $b['ts'] <=> $a['ts']);

        return array_map(fn($r) => [
            'color' => $r['color'],
            'event' => $r['event'],
            'meta'  => $r['meta'] ?: '—',
            'day'   => $r['ts']->isToday() ? 'Today' : $r['ts']->format('d M'),
            'time'  => $r['ts']->format('h:i A'),
        ], $rows);
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
                'contract'       => $p->contract_type ?? '—',
                'startDate'      => optional($p->created_at)->format('d M Y'),
                'completionDate' => optional($p->completion_date)->format('Y-m-d'),  // <-- add
                'warrantyId'     => $p->warranty_id,                                  // <-- add
                'srCount'        => $srCounts[$p->id] ?? 0,
                'active'         => in_array(strtolower($p->status ?? ''), ['active', '1']),
            ];
        })->values();

        // Project-created events — one per project_code, newest kept
        $projectActivity = $projects->sortByDesc('created_at')
            ->unique('project_code')
            ->map(fn($p) => [
                'type'   => 'project',
                'status' => 'created',
                'title'  => "Project {$p->project_code} — Created",
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
        $warranties     = Warranty::where('status', 1)->orderBy('name')->get();

        return view('client_accounts', [
            'recentClients'  => $recentClients,
            'existingTokens' => $existingTokens,
            'warranties'     =>  $warranties,
            'suggestedCode'  => $client->unique_code,
            'client'         => $client,
        ]);
    }

    private function sendRegistrationMessage(Client $client): void
    {
        try {
            $phone = $this->formatWhatsAppNumber(
                $client->primary_country,
                $client->primary_mobile
            );

            if (!$phone) {
                return;
            }

            $result = app(\App\Services\WhatsAppService::class)->sendRegistration(
                $phone,
                $client->contact_name,   // {{1}} name
                $client->unique_code,    // token (unused by template, kept for the log)
                'en_US',
                null,                    // no ServiceRequest tied to registration
                $client                  // pass the model → enables notify=1 fan-out
            );

            \Log::info('WhatsApp registration sent', [
                'client_id' => $client->id,
                'phone'     => $phone,
                'result'    => $result,
            ]);
        } catch (\Throwable $e) {
            \Log::error('WhatsApp registration message failed', [
                'client_id' => $client->id,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    private function formatWhatsAppNumber(?string $country, ?string $mobile): ?string
    {
        if (!$mobile) {
            return null;
        }

        // Strip everything except digits from both parts and join
        $country = preg_replace('/\D/', '', (string) $country); // "+91" -> "91"
        $mobile  = preg_replace('/\D/', '', $mobile);           // "80 8677 2507" -> "8086772507"

        return $country . $mobile; // "918086772507"
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

        // Send WhatsApp registration confirmation (outside transaction)
        $this->sendRegistrationMessage($client);
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
                'company_name'    => $validated['company_name'],
                'contact_name'    => $validated['contact_name'],
                'designation'     => $validated['designation'] ?? null,
                'primary_country' => $validated['primary_country'] ?? null,
                'primary_mobile'  => $validated['primary_mobile'],
            ]);

            // Rebuild stakeholder mobiles + projects from the submitted form
            // (primary mobile now lives on the clients table itself)
            // $client->mobiles()->delete();
            // $client->projects()->delete();


            $client->mobiles()->forceDelete();
            // $client->projects()->forceDelete();

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
            'projects.*.id'            => ['nullable', 'integer', 'exists:projects,id'],
            'projects.*.project_name'  => ['required', 'string', 'max:255'],
             'projects.*.project_code' => ['nullable', 'string', 'max:50'],
            'projects.*.site_name'     => ['required', 'string', 'max:255'],
            'projects.*.site_address'  => ['nullable', 'string', 'max:1000'],
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

    private function syncProjects(Client $client, array $validated): void
{
    $incomingIds = [];

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
                $model->update($data);          // existing row keeps its code
                $incomingIds[] = $model->id;
                continue;
            }
        }

        $data['project_code'] = $this->generateProjectCode();   // only on create
        $incomingIds[] = $client->projects()->create($data)->id;
    }

    $client->projects()
        ->whereNotIn('id', $incomingIds)
        ->forceDelete();
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
                'technician'  => optional($pn->user)->name ?? '—',
                'punch_in'    => $pn->punch_in_at  ? \Carbon\Carbon::parse($pn->punch_in_at)->format('d M Y, h:i A')  : '—',
                'punch_out'   => $pn->punch_out_at ? \Carbon\Carbon::parse($pn->punch_out_at)->format('d M Y, h:i A') : '—',
                'location'    => $pn->site_location ?: '—',
                'work'        => $pn->work_description ?: '—',
                'summary'     => $pn->completion_summary ?: '—',
                'receipt_no'  => $pn->receipt_number ?: '—',
                'cust_name'   => $pn->customer_name ?: '—',
                'cust_phone'  => $pn->customer_phone ?: '—',
                'status'      => $pn->status,
                'notes'       => $pn->notes ?: '—',
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
            'client_company'  => $c->company_name    ?? '—',
            'client_code'     => $c->unique_code     ?? '—',
            'client_contact'  => $c->contact_name    ?? '—',
            'client_phone'    => $c ? trim(($c->primary_country ?? '') . ' ' . ($c->primary_mobile ?? '')) : '—',
            'client_desig'    => $c->designation     ?? '—',
            'client_status'   => $c->status          ?? '—',

            // projects table
            'proj_name'       => $p->project_name        ?? '—',
            'proj_code'       => $p->project_code        ?? '—',
            'site_name'       => $p->site_name           ?? ($sr->project_site ?: '—'),
            'site_address'    => $p->site_address        ?? '—',
            'proj_status'     => $p->status              ?? '—',
            'proj_completion' => $p && $p->completion_date   ? \Carbon\Carbon::parse($p->completion_date)->format('d M Y')   : '—',
            'warranty_end'    => $p && $p->warranty_end_date ? \Carbon\Carbon::parse($p->warranty_end_date)->format('d M Y') : '—',

            'warranty'          => ($sr->project
                && $sr->project->warranty_end_date
                && \Carbon\Carbon::parse($sr->project->warranty_end_date)->endOfDay()->isFuture())
                ? 'In Warranty'
                : 'Out of Warranty',
            // service_requests table
            'category'   => optional($sr->category)->category_name ?? '—',
            'priority'   => $sr->priority_level,
            'issue'      => $sr->issue_description,
            'reported'   => $sr->reported_by,
            'sr_status'  => $sr->status,
            'dispatched' => $sr->dispatched_at ? \Carbon\Carbon::parse($sr->dispatched_at)->format('d M Y, h:i A') : '—',
            'accepted'   => $sr->accepted_at   ? \Carbon\Carbon::parse($sr->accepted_at)->format('d M Y, h:i A')   : '—',
            'tech'       => optional($sr->assignedUser)->name ?? '—',

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
                'warranty_end_date' => optional($p->warranty_end_date)->format('Y-m-d'),
            ])->values(),
        ]);
    }
}
