<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class WorkerPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('worker_forgot_password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        // Only Maintenance Leads may reset through this portal.
        $user = User::where('email', $request->email)->first();

        if (!$user || optional($user->role)->code !== 'ML') {
            // Deliberately identical to the success path — don't leak
            // which addresses exist or which role they hold.
            return back()->with('status', 'If that email is registered, a reset link is on its way.');
        }

        Password::broker('workers')->sendResetLink($request->only('email'));

        return back()->with('status', 'If that email is registered, a reset link is on its way.');
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('worker_reset_password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::broker('workers')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('worker.login')->with('status', 'Password updated. You can sign in now.')
            : back()->withInput($request->only('email'))
                    ->withErrors(['email' => __($status)]);
    }
}