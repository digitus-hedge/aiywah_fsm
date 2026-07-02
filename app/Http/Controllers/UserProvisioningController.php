<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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

        return view('user_provisioning', [
            'roles'          => $roles,
            'permSections'   => $sections,
            'users'          => User::with('role')->latest()->get()->map(fn ($u) => [
                'id'       => $u->id,
                'name'     => $u->name,
                'email'    => $u->email,
                'role'     => optional($u->role)->name ?? '',
                'roleId'   => optional($u->role)->code ?? '',
                'domains'  => $u->domains ?? [],
                'fdGrants' => $u->fd_grants ?? [],
                'created'  => $u->created_at?->format('d M Y'),
                'status'   => $u->status ?? 'active',
            ]),
            'existingEmails' => User::pluck('email'),
            'saveUrl'        => route('user_provisioning.store'),
            'updateUrlBase'  => url('/user-provisioning'), // base for PUT /user-provisioning/{id}
        ]);
    }

    /**
     * Create a new user.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email:rfc,dns|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role'     => 'required|string',
            'roleId'   => 'required|string|exists:roles,code',
            'domains'  => 'array',
            'domains.*' => 'string',
            'fdGrants'  => 'array',
            'fdGrants.*' => 'string',
        ]);

        $user = User::create([
            'name'              => $data['name'],
            'email'             => strtolower($data['email']),
            'password'          => Hash::make($data['password']),
            'role_id'           => Role::where('code', $data['roleId'])->value('id'),
            'domains'           => $data['domains']  ?? [],
            'fd_grants'         => $data['fdGrants'] ?? [],
            'status'            => 'pending',
            'email_verified_at' => now(),
        ]);

        return response()->json([
            'user' => [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'role'     => $data['role'],
                'roleId'   => $data['roleId'],
                'domains'  => $user->domains  ?? [],
                'fdGrants' => $user->fd_grants ?? [],
                'created'  => $user->created_at->format('d M Y'),
                'status'   => $user->status ?? 'pending',
            ],
        ]);
    }

    /**
     * Update an existing user.
     */
    public function update(Request $request, User $user)
    {
        // Treat a blank/absent password as "not provided" so it skips min:8
        // and is never mass-assigned as null. Works for both JSON and form bodies.
        $pw = $request->input('password');
        if ($pw === null || trim((string) $pw) === '') {
            $request->offsetUnset('password');
        }

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email:rfc,dns', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'sometimes|nullable|string|min:8',
            'role'     => 'required|string',
            'roleId'   => 'required|string|exists:roles,code',
            'domains'  => 'array',
            'domains.*' => 'string',
            'fdGrants'  => 'array',
            'fdGrants.*' => 'string',
        ]);

        $emailChanged = strtolower($data['email']) !== strtolower($user->email);

        $user->update([
            'name'      => $data['name'],
            'email'     => strtolower($data['email']),
            'role_id'   => Role::where('code', $data['roleId'])->value('id'),
            'domains'   => $data['domains']  ?? [],
            'fd_grants' => $data['fdGrants'] ?? [],
            // Re-verify silently when the email changes so the column stays populated.
            'email_verified_at' => $emailChanged ? now() : $user->email_verified_at,
        ]);

        if (!empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        return response()->json([
            'user' => [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'role'     => $data['role'],
                'roleId'   => $data['roleId'],
                'domains'  => $user->domains  ?? [],
                'fdGrants' => $user->fd_grants ?? [],
                'created'  => $user->created_at->format('d M Y'),
                'status'   => $user->status,
            ],
        ]);
    }
}