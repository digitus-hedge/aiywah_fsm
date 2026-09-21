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
    $credentials = $request->validate([
        'email'    => ['required', 'email'],
        'password' => ['required', 'string', 'min:6'],
    ]);

    $remember = $request->boolean('remember');

    if (!Auth::attempt($credentials, $remember)) {
        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    $user = Auth::user();

        // Only active accounts may sign in - pending, inactive, or any other
    // status is blocked by default rather than individually listed.
    if ($user->status !== 'active') {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $message = $user->status === 'pending'
            ? 'Your account is pending activation. Please contact your administrator.'
            : 'Your account has been deactivated. Please contact your administrator.';

        throw ValidationException::withMessages([
            'email' => $message,
        ]);
    }

    // Maintenance Leads belong on the technician portal, not here.
    if (optional($user->role)->code === 'ML') {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('worker.login')
            ->withInput($request->only('email'))
            ->with('status', 'Field technicians sign in through the Technician Portal.');
    }

    $request->session()->regenerate();

    return redirect()->intended(route('dashboard'))
        ->with('success', 'Welcome back, ' . $user->name . '!');
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
