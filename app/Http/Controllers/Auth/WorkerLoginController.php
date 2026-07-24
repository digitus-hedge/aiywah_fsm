<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class WorkerLoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('worker')->check()) {
            return redirect()->route('worker.pipeline');
        }

        return view('auth.worker_login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::guard('worker')->attempt($data, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $user = Auth::guard('worker')->user();

        // Only workers may use this portal.
        if (!$this->isWorker($user)) {
            Auth::guard('worker')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'This login is for field technicians only.',
            ]);
        }

        // Block disabled accounts.
        if (property_exists($user, 'status') || isset($user->status)) {
            if (!$user->status) {
                Auth::guard('worker')->logout();
                throw ValidationException::withMessages([
                    'email' => 'Your account is inactive. Please contact your supervisor.',
                ]);
            }
        }

        $request->session()->regenerate();

        return redirect()->intended(route('worker.pipeline'));
    }


    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'confirmed', Password::min(8)],
        ]);

        /** @var \App\Models\Worker $user */
        $user = Auth::guard('worker')->user();

        if (!$user) {
            return response()->json([
                'message' => 'Not authenticated.',
            ], 401);
        }

        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Your current password is incorrect.',
            ]);
        }

        if (Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'The new password must be different from your current one.',
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($data['password']),
        ])->save();

        $request->session()->regenerate();

        return response()->json([
            'ok'      => true,
            'message' => 'Password updated successfully.',
        ]);
    }


    public function logout(Request $request)
    {
        Auth::guard('worker')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('worker.login');
    }

    /**
     * ADJUST THIS to match your schema — see notes below.
     */
    private function isWorker($user): bool
    {
        return optional($user->role)->code === 'ML';
    }
}
