<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;

class Userdirectorycontroller extends Controller
{
    /**
     * Render the user directory listing (dynamic, filterable, paginated).
     */
    public function index(Request $request)
    {
        abort_unless(auth()->user()?->canAccessUserDirectory(), 403);
        $roles = Role::orderBy('sort_order')->get();

        $counts = User::selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $stats = [
            'total'    => (int) $counts->sum(),
            'active'   => (int) ($counts['active']   ?? 0),
            'inactive' => (int) ($counts['inactive'] ?? 0) + (int) ($counts['pending'] ?? 0),
        ];

        $users = User::with(['role', 'serviceDomains', 'serviceCategories'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = $request->string('q');
                $query->where(function ($w) use ($term) {
                    $w->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('role'), function ($query) use ($request) {
                $query->whereHas('role', fn($r) => $r->where('code', $request->input('role')));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $canProvisionUsers = auth()->user()?->hasAnyAccess('user_provisioning') ?? false;
        $canDeleteUsers    = auth()->user()?->hasAnyAccess('user_delete') ?? false;

        return view('user_directory', compact('roles', 'stats', 'users', 'canProvisionUsers', 'canDeleteUsers'));
    }

    /**
     * Toggle a user's active/inactive status.
     */
    public function toggleStatus(User $user)
    {
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        return response()->json(['status' => $user->status]);
    }

    /**
     * Trigger a password-reset email (stub - wire to your notification).
     */
    public function resetPassword(User $user)
    {
        return response()->json(['ok' => true]);
    }

    public function show(User $user)
    {
        $user->load(['role', 'serviceDomains', 'serviceCategories']);

        return response()->json([
            'user' => [
                'id'      => $user->id,
                'name'    => $user->name,
                'email'   => $user->email,
                'roleId'  => optional($user->role)->code ?? '',
                'domains' => $user->serviceDomains->pluck('id')->all(),
                'categories' => $user->serviceCategories->pluck('id')->all(),

            ],

            'roles' => Role::orderBy('sort_order')->get(['name', 'code']),

            'domainCats' => ServiceCategory::with(['domains' => fn($q) => $q->where('status', true)->orderBy('sort_order')])
                ->where('status', true)->orderBy('sort_order')->get()
                ->map(fn($c) => [
                    'label'  => $c->category_name,
                    'skills' => $c->domains->map(fn($d) => ['id' => $d->id, 'label' => $d->domain_name])->values(),
                ])->values(),

            'categories' => ServiceCategory::where('status', true)
                ->orderBy('sort_order')
                ->get(['id', 'category_name'])
                ->map(fn($c) => ['id' => $c->id, 'name' => $c->category_name])
                ->values(),

        ]);
    }
    public function destroy(User $user)
    {
        abort_unless(auth()->user()?->hasAnyAccess('user_delete'), 403);

        // Don't allow deleting yourself
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'You cannot delete your own account.'], 422);
        }

        $user->delete(); // soft delete - sets deleted_at

        return response()->json(['ok' => true]);
    }
}