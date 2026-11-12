<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetCustomMail;
use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'dob' => ['required', 'date'],
        ], [
            'dob.required' => 'Registered Date of Birth is required to verify identity for password recovery.',
        ]);

        $dobFormatted = Carbon::parse($validated['dob'])->format('Y-m-d');

        // Verify email and date of birth dual verification
        $user = User::where('email', $validated['email'])
            ->whereDate('dob', $dobFormatted)
            ->first();

        if (!$user) {
            return back()->withInput()->withErrors([
                'email' => 'The provided Email and Date of Birth combination does not match our records.',
            ]);
        }

        // Generate token
        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

        try {
            Mail::to($user->email)->send(new PasswordResetCustomMail($user, $resetUrl));
        } catch (\Throwable $e) {
            Log::warning('Failed sending password reset mail: ' . $e->getMessage());
        }

        ActivityLog::log('password_reset_requested', 'Password recovery requested with verified DOB', $user);

        $extraNotice = config('mail.default') === 'log' ? " (For testing, check logs or click: <a href='{$resetUrl}'>Direct Reset Link</a>)" : '';

        return back()->with('success', "We'll forward reset instruction to your email. Please check your inbox.{$extraNotice}");
    }

    public function showResetForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $validated['email'])->first();

        if (!$record || !Hash::check($validated['token'], $record->token)) {
            return back()->withErrors(['email' => 'This password reset token is invalid or has expired.']);
        }

        // Check 60-minute token expiration
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();
            return back()->withErrors(['email' => 'This password reset token has expired. Please request a new one.']);
        }

        $user = User::where('email', $validated['email'])->firstOrFail();
        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'remember_token' => Str::random(60),
        ])->save();

        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        ActivityLog::log('password_reset_completed', 'Password reset successfully', $user);

        return redirect()->route('login')->with('success', 'Your password has been reset successfully! You can now log in with your new password.');
    }
}
