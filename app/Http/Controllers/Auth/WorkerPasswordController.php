<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WorkerOtpMail;
use App\Models\User;
use App\Models\WorkerOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class WorkerPasswordController extends Controller
{
    private const OTP_TTL_MINUTES = 10;
    private const MAX_ATTEMPTS    = 5;

    /* ---------- Step 1: ask for email ---------- */

    public function showLinkRequestForm(Request $request)
    {
        return view('auth.worker_forgot_password', [
            'email' => $request->query('email', ''),
        ]);
    }

    private function dispatchOtp(string $email): void
{
    $user = User::where('email', $email)->first();

    if (!$user || optional($user->role)->code !== 'ML' || !$user->status) {
        return;
    }

    $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    WorkerOtp::where('email', $user->email)->delete();

    $record = WorkerOtp::create([
        'email'      => $user->email,
        'otp_hash'   => Hash::make($otp),
        'expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
    ]);

    try {
        Mail::to($user->email)->send(new WorkerOtpMail($otp, self::OTP_TTL_MINUTES));
    } catch (\Throwable $e) {
        Log::error('Worker OTP mail failed', ['email' => $user->email, 'error' => $e->getMessage()]);
        $record->delete();
    }
}

public function sendOtp(Request $request)
{
    $request->validate(['email' => ['required', 'email']]);

    $this->dispatchOtp($request->email);

    $request->session()->put('worker_otp_email', $request->email);

    return redirect()->route('worker.otp.form')
        ->with('status', 'If that email is registered, a code is on its way.');
}

public function resendOtp(Request $request)
{
    $email = $request->session()->get('worker_otp_email');

    if (!$email) {
        return redirect()->route('worker.password.request');
    }

    $this->dispatchOtp($email);

    return back()->with('status', 'A new code has been sent.');
}

    /* ---------- Step 2: verify OTP ---------- */

    public function showOtpForm(Request $request)
    {
        if (!$request->session()->has('worker_otp_email')) {
            return redirect()->route('worker.password.request');
        }

        return view('auth.worker_otp', [
            'email' => $request->session()->get('worker_otp_email'),
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => ['required', 'digits:6']]);

        $email = $request->session()->get('worker_otp_email');

        if (!$email) {
            return redirect()->route('worker.password.request');
        }

        $record = WorkerOtp::where('email', $email)->whereNull('verified_at')->latest()->first();

        if (!$record || $record->isExpired()) {
            optional($record)->delete();
            throw ValidationException::withMessages([
                'otp' => 'That code has expired. Please request a new one.',
            ]);
        }

        if ($record->attempts >= self::MAX_ATTEMPTS) {
            $record->delete();
            $request->session()->forget('worker_otp_email');
            throw ValidationException::withMessages([
                'otp' => 'Too many incorrect attempts. Please start again.',
            ]);
        }

        if (!$record->matches($request->otp)) {
            $record->increment('attempts');
            throw ValidationException::withMessages([
                'otp' => 'Incorrect code. ' . (self::MAX_ATTEMPTS - $record->attempts) . ' attempts left.',
            ]);
        }

        $record->update(['verified_at' => now()]);

        $user = User::where('email', $email)->firstOrFail();
        $user->forceFill(['must_reset_password' => true])->save();

        // Log them in - middleware will pin them to the reset screen.
        Auth::guard('worker')->login($user);
        $request->session()->regenerate();
        $request->session()->forget('worker_otp_email');

        return redirect()->route('worker.password.forced');
    }

    /* ---------- Step 3: forced reset ---------- */

    public function showForcedResetForm()
    {
        return view('auth.worker_force_reset');
    }

    public function forcedReset(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = Auth::guard('worker')->user();

        if (Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Please choose a password you have not used before.',
            ]);
        }

        $user->forceFill([
            'password'            => Hash::make($request->password),
            'remember_token'      => Str::random(60),
            'must_reset_password' => false,
        ])->save();

        WorkerOtp::where('email', $user->email)->delete();

        $request->session()->regenerate();

        return redirect()->route('worker.pipeline')
            ->with('status', 'Password updated. Welcome back.');
    }
}