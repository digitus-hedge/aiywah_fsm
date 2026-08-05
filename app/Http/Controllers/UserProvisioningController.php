<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\ServiceCategory;
use App\Models\ServiceDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Mail\UserWelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
class UserProvisioningController extends Controller
{
    /**
     * Render the provisioning studio.
     */
    public function index()
{
    $roles       = Role::orderBy('sort_order')->get();
    $permissions = Permission::with('roles')->orderBy('sort_order')->get();

    $sections = $permissions->groupBy('section')->map(function ($perms, $section) {
        return [
            'label' => $section,
            'items' => $perms->map(function ($p) {
                $access = [];
                $grant  = [];
                foreach ($p->roles as $role) {
                    $access[$role->code] = $role->pivot->access;
                    $grant[$role->code]  = (bool) $role->pivot->can_grant;
                }
                return [
                    'key'    => $p->key,
                    'name'   => $p->name,
                    'icon'   => $p->icon,
                    'access' => $access,
                    'grant'  => $grant,
                ];
            })->values(),
        ];
    })->values();

   $domainCats = ServiceCategory::with(['domains' => function ($q) {
        $q->where('status', true)->orderBy('sort_order');
    }])
    ->where('status', true)
    ->orderBy('sort_order')
    ->get()
    ->map(fn ($cat) => [
        'id'     => $cat->id,
        'label'  => $cat->category_name,
        'icon'   => $cat->icon,
        'color'  => $cat->color_code,
        'skills' => $cat->domains->map(fn ($d) => [
            'id'       => $d->id,
            'label'    => $d->domain_name,
            'catId'    => $cat->id,
            'catLabel' => $cat->category_name,
        ])->values(),
    ]);
    return view('user_provisioning', [
        'roles'          => $roles,
        'permSections'   => $sections,
        'domainCats'     => $domainCats,
        'users'          => User::with(['role', 'serviceDomains'])->latest()->get()->map(fn ($u) => [
            'id'       => $u->id,
            'name'     => $u->name,
            'email'    => $u->email,
             'phone'    => $u->phone ?? '',
            'country_code' => $u->country_code ?? '',
            'role'     => optional($u->role)->name ?? '',
            'roleId'   => optional($u->role)->code ?? '',
            'domains'  => $u->serviceDomains->pluck('id')->values()->all(),
            'fdGrants' => $u->fd_grants ?? [],
            'created'  => $u->created_at?->format('d M Y'),
            'status'   => $u->status ?? 'active',
        ]),
        'existingEmails' => User::pluck('email'),
        'saveUrl'        => route('user_provisioning.store'),
        'updateUrlBase'  => url('/user-provisioning'),
    ]);
}
    /**
     * Create a new user.
     */
   public function store(Request $request)
{
    $data = $request->validate([
        'name'       => 'required|string|max:255',
        'email'      => 'required|email:rfc,dns|max:255|unique:users,email',
        'country_code' => 'nullable|string|max:8',
         'phone'      => 'nullable|string|max:20',
        'password'   => 'required|string|min:8',
        'role'       => 'required|string',
        'roleId'     => 'required|string|exists:roles,code',
        'domains'    => 'array',
        'domains.*'  => 'integer|exists:service_domains,id',
        'fdGrants'   => 'array',
        'fdGrants.*' => 'string',
    ]);

    $user = User::create([
        'name'              => $data['name'],
        'email'             => strtolower($data['email']),
        'country_code'      => $data['country_code'] ?? null,
        'phone'             => $data['phone'] ?? null,
        'password'          => Hash::make($data['password']),
        'role_id'           => Role::where('code', $data['roleId'])->value('id'),
        'fd_grants'         => $data['fdGrants'] ?? [],
        'status'            => 'pending',
        'email_verified_at' => now(),
    ]);

    $this->syncDomains($user, $data['domains'] ?? []);
    try {
    $user->load('role');

    Mail::to($user->email)->send(
        new UserWelcomeMail($user, $data['password'], $data['role'])
    );
} catch (\Throwable $e) {
    Log::error('User welcome mail failed', [
        'user_id' => $user->id,
        'error'   => $e->getMessage(),
    ]);
}
    return response()->json([
        'user' => [
            'id'       => $user->id,
            'name'     => $user->name,
            'email'    => $user->email,
            'phone'        => $user->phone,
            'country_code' => $user->country_code,
            'role'     => $data['role'],
            'roleId'   => $data['roleId'],
            'domains'  => $user->serviceDomains()->pluck('service_domain_id')->values()->all(),
            'fdGrants' => $user->fd_grants ?? [],
            'created'  => $user->created_at->format('d M Y'),
            'status'   => $user->status ?? 'pending',
        ],
    ]);
}

public function update(Request $request, User $user)
{
    $pw = $request->input('password');
    if ($pw === null || trim((string) $pw) === '') {
        $request->offsetUnset('password');
    }

    $data = $request->validate([
        'name'       => 'required|string|max:255',
        'email'      => ['required', 'email:rfc,dns', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
         'country_code' => 'nullable|string|max:8',
        'phone'        => 'nullable|string|max:20',
        'password'   => 'sometimes|nullable|string|min:8',
        'role'       => 'required|string',
        'roleId'     => 'required|string|exists:roles,code',
        'domains'    => 'array',
        'domains.*'  => 'integer|exists:service_domains,id',
        'fdGrants'   => 'array',
        'fdGrants.*' => 'string',
    ]);

    $emailChanged = strtolower($data['email']) !== strtolower($user->email);
    
    $user->update([
        'name'              => $data['name'],
        'email'             => strtolower($data['email']),
        'country_code' => $data['country_code'] ?? null,
        'phone'        => $data['phone'] ?? null,
        'role_id'           => Role::where('code', $data['roleId'])->value('id'),
        'fd_grants'         => $data['fdGrants'] ?? [],
        'email_verified_at' => $emailChanged ? now() : $user->email_verified_at,
    ]);

    if (!empty($data['password'])) {
        $user->update(['password' => Hash::make($data['password'])]);
    }

    $this->syncDomains($user, $data['domains'] ?? []);

    return response()->json([
        'user' => [
            'id'       => $user->id,
            'name'     => $user->name,
            'email'    => $user->email,
            'role'     => $data['role'],
            'roleId'   => $data['roleId'],
            'domains'  => $user->serviceDomains()->pluck('service_domain_id')->values()->all(),
            'fdGrants' => $user->fd_grants ?? [],
            'created'  => $user->created_at->format('d M Y'),
            'status'   => $user->status,
        ],
    ]);
}

/**
 * Sync a user's selected service domains, storing both the
 * category_id and domain_id on each pivot row.
 */
private function syncDomains(User $user, array $domainIds): void
{
    $rows = ServiceDomain::whereIn('id', $domainIds)
        ->get(['id', 'service_category_id'])
        ->mapWithKeys(fn ($d) => [
            $d->id => ['service_category_id' => $d->service_category_id],
        ]);

    $user->serviceDomains()->sync($rows);
}
}