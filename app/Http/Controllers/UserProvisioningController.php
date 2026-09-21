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
use App\Jobs\SendUserWelcomeMail;

class UserProvisioningController extends Controller
{
    /** Roles that are never shown as "source" blocks - they're superset roles. */
    private const SUPERSET_ROLES = ['SA', 'AD'];

    public function index(Request $request)
    {
        abort_unless(auth()->user()?->hasAccess('user_provisioning'), 403);
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
            ->map(fn($cat) => [
                'id'     => $cat->id,
                'label'  => $cat->category_name,
                'icon'   => $cat->icon,
                'color'  => $cat->color_code,
                'skills' => $cat->domains->map(fn($d) => [
                    'id'       => $d->id,
                    'label'    => $d->domain_name,
                    'catId'    => $cat->id,
                    'catLabel' => $cat->category_name,
                ])->values(),
            ]);

        $editUserId = $request->integer('edit') ?: null;

        return view('user_provisioning', [
            'roles'          => $roles,
            'permSections'   => $sections,
            'domainCats'     => $domainCats,
            'editUserId'     => $editUserId,
            'users'          => User::with(['role', 'serviceDomains', 'serviceCategories'])->latest()->get()->map(fn($u) => [
                'id'           => $u->id,
                'name'         => $u->name,
                'email'        => $u->email,
                'phone'        => $u->phone ?? '',
                'country_code' => $u->country_code ?? '',
                'role'         => optional($u->role)->name ?? '',
                'roleId'       => optional($u->role)->code ?? '',
                'domains'      => $u->serviceDomains->pluck('id')->values()->all(),
                'fdGrants'     => $u->fd_grants ?? [],
                'acGrants'     => $u->ac_grants ?? [],
                'extGrants'    => $u->ext_grants ?? [],   // ← add: {key: access}
                'categories'   => $u->serviceCategories->pluck('id')->values()->all(),
                'qcReview'     => (bool) $u->can_qc_review,
                'isSeEnabled'  => (bool) $u->is_se_enabled,
                'created'      => $u->created_at?->format('d M Y'),
                'status'       => $u->status ?? 'active',
            ]),
            'existingEmails' => User::pluck('email'),
            'saveUrl'        => route('user_provisioning.store'),
            'updateUrlBase'  => url('/user-provisioning'),
        ]);
    }

    /**
     * Mirrors the client-side computeExtensionBlocks() logic: a permission is
     * "extendable" for $roleCode if that role's own access is 'no' AND at
     * least one other, non-superset role has 'yes'/'rls' on it. Returns
     * [permission_key => allowed max access ('yes' beats 'rls')].
     */
    private function validExtensionsForRole(string $roleCode): array
    {
         // ML never gets extensions - same rule as the client-side matrix.
        if ($roleCode === 'ML') {
            return [];
        }
        $permissions = Permission::with('roles')->get();
        $valid = [];

        foreach ($permissions as $p) {
            $ownPivot  = $p->roles->firstWhere('code', $roleCode);
            $ownAccess = $ownPivot->pivot->access ?? 'no';
            if ($ownAccess !== 'no') {
                continue; // role already has some access to this permission
            }

            $bestAccess = null;
            foreach ($p->roles as $role) {
                if ($role->code === $roleCode || in_array($role->code, self::SUPERSET_ROLES, true)) {
                    continue;
                }
                $tok = $role->pivot->access;
                if ($tok === 'yes') { $bestAccess = 'yes'; break; }
                if ($tok === 'rls' && $bestAccess !== 'yes') { $bestAccess = 'rls'; }
            }

            if ($bestAccess) {
                $valid[$p->key] = $bestAccess;
            }
        }

        return $valid;
    }

    /** Strip anything the client sent that isn't a legitimate extension, and clamp its access to what's allowed. */
    private function sanitizeExtGrants(string $roleCode, array $submitted): array
    {
        $allowed = $this->validExtensionsForRole($roleCode);
        $clean = [];
        foreach ($submitted as $key => $access) {
            if (isset($allowed[$key])) {
                $clean[$key] = $allowed[$key]; // always store the server-computed access, never trust the client's
            }
        }
        return $clean;
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()?->hasAccess('user_provisioning'), 403);
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email:rfc,dns|max:255|unique:users,email',
            'country_code' => 'required|string|max:8',
            'phone'      => 'required|string|max:20|unique:users,phone',
            'password'   => 'required|string|min:8',
            'role'       => 'required|string',
            'roleId'     => 'required|string|exists:roles,code',
            'domains'    => 'array',
            'domains.*'  => 'integer|exists:service_domains,id',
            'categories'   => 'array',
            'categories.*' => 'integer|exists:service_categories,id',
            'qcReview'     => 'boolean',
            'isSeEnabled'  => 'boolean',
            'fdGrants'   => 'array',
            'fdGrants.*' => 'string',
            'acGrants'   => 'array',
            'acGrants.*' => 'string',
            'extGrants'  => 'array',    // ← add: { key: access } - access value is a hint, server recomputes it
        ] , [
            'phone.unique' => 'This phone number is already registered with another user.',
            'email.unique' => 'This email address is already registered with another user.',
        ]);

        $isSE = $data['roleId'] === 'SE';
        $isFD = $data['roleId'] === 'FD';
        $isAC = $data['roleId'] === 'AC';
        $isHP = $data['roleId'] === 'HP';
        $hopActsAsSe = $isHP && ($data['isSeEnabled'] ?? false);
        $grantsSeCategories = $isSE || $hopActsAsSe;

        $extGrants = $this->sanitizeExtGrants($data['roleId'], $data['extGrants'] ?? []);

        $user = User::create([
            'name'              => $data['name'],
            'email'             => strtolower($data['email']),
            'country_code'      => $data['country_code'] ?? null,
            'phone'             => $data['phone'] ?? null,
            'password'          => Hash::make($data['password']),
            'role_id'           => Role::where('code', $data['roleId'])->value('id'),
            'fd_grants'         => $isFD ? ($data['fdGrants'] ?? []) : [],
            'ac_grants'         => $isAC ? ($data['acGrants'] ?? []) : [],
            'ext_grants'        => $extGrants,   // ← add
            'can_qc_review'     => $isSE && ($data['qcReview'] ?? false),
            'is_se_enabled'     => $hopActsAsSe,
            'status'            => 'active',
            'email_verified_at' => now(),
        ]);

        $this->syncDomains($user, $data['domains'] ?? []);
        $user->serviceCategories()->sync($grantsSeCategories ? ($data['categories'] ?? []) : []);

        SendUserWelcomeMail::dispatch($user->id, $data['password'], $data['role']);

        return response()->json([
            'user' => [
                'id'           => $user->id,
                'name'         => $user->name,
                'email'        => $user->email,
                'phone'        => $user->phone,
                'country_code' => $user->country_code,
                'role'         => $data['role'],
                'roleId'       => $data['roleId'],
                'domains'      => $user->serviceDomains()->pluck('service_domain_id')->values()->all(),
                'categories'   => $user->serviceCategories()->pluck('service_categories.id')->values()->all(),
                'qcReview'     => (bool) $user->can_qc_review,
                'isSeEnabled'  => (bool) $user->is_se_enabled,
                'fdGrants'     => $user->fd_grants ?? [],
                'acGrants'     => $user->ac_grants ?? [],
                'extGrants'    => $user->ext_grants ?? [],   // ← add
                'created'      => $user->created_at->format('d M Y'),
                'status'       => $user->status ?? 'active',
            ],
        ]);
    }

    public function update(Request $request, User $user)
    {
        abort_unless(auth()->user()?->hasAccess('user_provisioning'), 403);
        $pw = $request->input('password');
        if ($pw === null || trim((string) $pw) === '') {
            $request->offsetUnset('password');
        }

        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => ['required', 'email:rfc,dns', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'country_code' => 'required|string|max:8',
            'phone'        => ['required', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($user->id)],
            'password'   => 'sometimes|nullable|string|min:8',
            'role'       => 'required|string',
            'roleId'     => 'required|string|exists:roles,code',
            'domains'    => 'array',
            'domains.*'  => 'integer|exists:service_domains,id',
            'categories'   => 'array',
            'categories.*' => 'integer|exists:service_categories,id',
            'qcReview'     => 'boolean',
            'isSeEnabled'  => 'boolean',
            'fdGrants'   => 'array',
            'fdGrants.*' => 'string',
            'acGrants'   => 'array',
            'acGrants.*' => 'string',
            'extGrants'  => 'array',   // ← add
        ] , [
            'phone.unique' => 'This phone number is already registered with another user.',
            'email.unique' => 'This email address is already registered with another user.',
        ]);

        $isSE = $data['roleId'] === 'SE';
        $isML = $data['roleId'] === 'ML';
        $isFD = $data['roleId'] === 'FD';
        $isAC = $data['roleId'] === 'AC';
        $isHP = $data['roleId'] === 'HP';

        $actingUser = auth()->user();
        $actingIsAdmin = in_array(optional($actingUser->role)->code, ['AD', 'SA'], true);

        $hopActsAsSe = $isHP && $actingIsAdmin
            ? ($data['isSeEnabled'] ?? false)
            : ($isHP ? $user->is_se_enabled : false);

        $grantsSeCategories = $isSE || $hopActsAsSe;
        $emailChanged = strtolower($data['email']) !== strtolower($user->email);

        $extGrants = $this->sanitizeExtGrants($data['roleId'], $data['extGrants'] ?? []);

        $user->update([
            'name'              => $data['name'],
            'email'             => strtolower($data['email']),
            'country_code'      => $data['country_code'] ?? null,
            'phone'             => $data['phone'] ?? null,
            'role_id'           => Role::where('code', $data['roleId'])->value('id'),
            'fd_grants'         => $data['fdGrants'] ?? [],
            'ac_grants'         => $data['acGrants'] ?? [],
            'ext_grants'        => $extGrants,   // ← add
            'can_qc_review'     => $isSE && ($data['qcReview'] ?? false),
            'is_se_enabled'     => $hopActsAsSe,
            'email_verified_at' => $emailChanged ? now() : $user->email_verified_at,
        ]);

        if (!empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        $this->syncDomains($user, $isML ? ($data['domains'] ?? []) : []);
        $user->serviceCategories()->sync($grantsSeCategories ? ($data['categories'] ?? []) : []);

        return response()->json([
            'user' => [
                'id'          => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'role'        => $data['role'],
                'roleId'      => $data['roleId'],
                'domains'     => $user->serviceDomains()->pluck('service_domain_id')->values()->all(),
                'categories'  => $user->serviceCategories()->pluck('service_categories.id')->values()->all(),
                'qcReview'    => (bool) $user->can_qc_review,
                'fdGrants'    => $user->fd_grants ?? [],
                'acGrants'    => $user->ac_grants ?? [],
                'extGrants'   => $user->ext_grants ?? [],   // ← add
                'isSeEnabled' => (bool) $user->is_se_enabled,
                'created'     => $user->created_at->format('d M Y'),
                'status'      => $user->status,
            ],
        ]);
    }

    private function syncDomains(User $user, array $domainIds): void
    {
        $rows = ServiceDomain::whereIn('id', $domainIds)
            ->get(['id', 'service_category_id'])
            ->mapWithKeys(fn($d) => [
                $d->id => ['service_category_id' => $d->service_category_id],
            ]);

        $user->serviceDomains()->sync($rows);
    }
}