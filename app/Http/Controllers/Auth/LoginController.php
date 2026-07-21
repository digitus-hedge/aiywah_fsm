<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login attempt.
     */
    public function login(Request $request)
{
    // Validate input
    $credentials = $request->validate([
        'email'    => ['required', 'email'],
        'password' => ['required', 'string', 'min:6'],
    ]);

    $remember = $request->boolean('remember');

    // Attempt login
    if (Auth::attempt($credentials, $remember)) {
        // Regenerate session to prevent fixation
        $request->session()->regenerate();

        $user = Auth::user();

        // Maintenance Leads land on their pipeline; everyone else on the explorer.
        if ((int) $user->role_id === 4) {
            return redirect()->route('worker.pipeline')
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }

        return redirect()->intended(route('sr_explorer'))
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }

    // Auth failed
    throw ValidationException::withMessages([
        'email' => 'The provided credentials do not match our records.',
    ]);
}

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'You have been logged out.');
    }
}
