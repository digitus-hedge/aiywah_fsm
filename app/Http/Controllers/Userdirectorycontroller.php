<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class Userdirectorycontroller extends Controller
{
    /**
     * Render the user directory listing (dynamic, filterable, paginated).
     */
    public function index(Request $request)
    {
        $roles = Role::orderBy('sort_order')->get();
        // Aggregate stats (single grouped query)
        $counts = User::selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');
        $stats = [
            'total'    => (int) $counts->sum(),
            'active'   => (int) ($counts['active']   ?? 0),
            'pending'  => (int) ($counts['pending']  ?? 0),
            'inactive' => (int) ($counts['inactive'] ?? 0),
        ];

        // Filtered, eager-loaded, paginated listing
        $users = User::with(['role', 'serviceDomains'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = $request->string('q');
                $query->where(function ($w) use ($term) {
                    $w->where('name', 'like', "%{$term}%")
                      ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('role'), function ($query) use ($request) {
                $query->whereHas('role', fn ($r) => $r->where('code', $request->input('role')));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('user_directory', compact('roles', 'stats', 'users'));
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
     * Trigger a password-reset email (stub — wire to your notification).
     */
    public function resetPassword(User $user)
    {
        // Password::sendResetLink(['email' => $user->email]);  // when ready
        return response()->json(['ok' => true]);
    }
}