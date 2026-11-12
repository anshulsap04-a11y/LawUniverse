<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpLoginMail;
use App\Models\ActivityLog;
use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class OtpLoginController extends Controller
{
    public function showRequestForm(): View
    {
        return view('auth.otp-request');
    }

    public function sendOtp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return back()->withInput()->withErrors(['email' => 'No account found with this email address.']);
        }

        if ($user->isPending()) {
            return back()->withInput()->with('warning', 'Your account is pending verification and approval by an administrator.');
        }

        if ($user->isDeactivated() || $user->status === 'rejected') {
            return back()->withInput()->with('error', 'Your account has been deactivated or rejected.');
        }

        // Invalidate prior unused OTPs for this email
        OtpVerification::where('email', $user->email)
            ->where('action_type', 'instant_login')
            ->whereNull('verified_at')
            ->delete();

        // Generate 6-digit OTP code
        $otpCode = (string) mt_rand(100000, 999999);

        OtpVerification::create([
            'email' => $user->email,
            'otp_code' => $otpCode,
            'action_type' => 'instant_login',
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
        ]);

        try {
            Mail::to($user->email)->send(new OtpLoginMail($otpCode, 10));
        } catch (\Throwable $e) {
            Log::warning('Failed sending OTP email: ' . $e->getMessage());
        }

        $request->session()->put('otp_login_email', $user->email);

        // Flash message with test convenience info if mail driver is log
        $extraNotice = config('mail.default') === 'log' ? " (For testing: OTP is {$otpCode})" : '';

        return redirect()->route('auth.otp.verify.form')
            ->with('success', "A 6-digit OTP has been sent to {$user->email}. Please enter it below.{$extraNotice}");
    }

    public function showVerifyForm(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('otp_login_email')) {
            return redirect()->route('auth.otp.request.form')->with('error', 'Please enter your email to receive an OTP first.');
        }

        $email = $request->session()->get('otp_login_email');
        return view('auth.otp-verify', compact('email'));
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $otpRecord = OtpVerification::where('email', $validated['email'])
            ->where('action_type', 'instant_login')
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (!$otpRecord) {
            return back()->withErrors(['otp' => 'No active OTP request found. Please request a new OTP.']);
        }

        if ($otpRecord->isExpired()) {
            return back()->withErrors(['otp' => 'This OTP has expired. Please request a fresh OTP.']);
        }

        if ($otpRecord->otp_code !== trim($validated['otp'])) {
            $otpRecord->increment('attempts');
            return back()->withErrors(['otp' => 'Invalid OTP code. Please check and try again.']);
        }

        // Mark OTP as verified
        $otpRecord->update(['verified_at' => now()]);

        $user = User::where('email', $validated['email'])->firstOrFail();

        if ($user->isPending()) {
            return redirect()->route('login')->with('warning', 'Your account is pending verification and approval by an administrator.');
        }

        if ($user->isDeactivated() || $user->status === 'rejected') {
            return redirect()->route('login')->with('error', 'Your account has been deactivated or rejected.');
        }

        Auth::login($user, true);
        $request->session()->regenerate();
        $request->session()->forget('otp_login_email');

        ActivityLog::log('user_otp_login', 'User logged in via Instant Email OTP', $user);

        return redirect()->intended($user->getDashboardRoute());
    }
}

