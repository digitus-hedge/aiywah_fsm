<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function create()
    {
        $recentClients  = Client::latest()->take(5)->get();
        $existingTokens = Client::pluck('unique_code');
        $suggestedCode  = $this->nextCode();

        return view('client_accounts', [
            'recentClients'  => $recentClients,
            'existingTokens' => $existingTokens,
            'suggestedCode'  => $suggestedCode,
            'client'         => null,
        ]);
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
            $client->mobiles()->delete();
            $client->projects()->delete();

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
            'projects.*.completion_date' => ['nullable', 'date'],
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
            $client->projects()->create([
                'project_name'    => $project['project_name'],
                'project_code'    => $project['project_code'],
                'site_name'       => $project['site_name'] ?? null,
                'site_address'    => $project['site_address'] ?? null,
                'completion_date' => $project['completion_date'] ?? null,
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
        'mobiles'         => $client->mobiles->map(fn ($m) => [
            'name'    => $m->name,
            'country' => $m->country,
            'mobile'  => $m->mobile,
        ])->values(),
        'projects' => $client->projects->map(fn ($p) => [
            'project_name'    => $p->project_name,
            'project_code'    => $p->project_code,
            'site_name'       => $p->site_name,
            'site_address'    => $p->site_address,
            'completion_date' => optional($p->completion_date)->format('Y-m-d'),
        ])->values(),
    ]);
}
}