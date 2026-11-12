<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeLetterMail;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            $socialUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::error('Google OAuth Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Google login failed or was cancelled. Please try again.');
        }

        return $this->processSocialLogin($socialUser, 'google', $request);
    }

    public function redirectToFacebook(): RedirectResponse
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback(Request $request): RedirectResponse
    {
        try {
            $socialUser = Socialite::driver('facebook')->user();
        } catch (\Throwable $e) {
            Log::error('Facebook OAuth Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Facebook login failed or was cancelled. Please try again.');
        }

        return $this->processSocialLogin($socialUser, 'facebook', $request);
    }

    protected function processSocialLogin($socialUser, string $provider, Request $request): RedirectResponse
    {
        $email = $socialUser->getEmail();
        $providerId = $socialUser->getId();

        // 1. Look for existing user
        $user = User::where($provider . '_id', $providerId)
            ->orWhere('email', $email)
            ->first();

        if ($user) {
            // Update provider ID if not linked
            if (!$user->{$provider . '_id'}) {
                $user->update([
                    $provider . '_id' => $providerId,
                    'oauth_provider' => $provider,
                ]);
            }

            if ($user->isPending()) {
                return redirect()->route('login')->with('warning', 'Your account is pending verification and approval by an administrator.');
            }

            if ($user->isDeactivated() || $user->status === 'rejected') {
                return redirect()->route('login')->with('error', 'Your account has been deactivated or rejected.');
            }

            Auth::login($user, true);
            $request->session()->regenerate();
            ActivityLog::log('user_social_login', "User logged in via {$provider}", $user);

            return redirect()->intended($user->getDashboardRoute());
        }

        // 2. New User: store in session and redirect to role selection step (spec page 7)
        $nameParts = explode(' ', $socialUser->getName() ?? 'User', 2);
        $firstName = $nameParts[0] ?? 'User';
        $lastName = $nameParts[1] ?? '';

        $request->session()->put('oauth_pending_user', [
            'provider' => $provider,
            'provider_id' => $providerId,
            'email' => $email,
            'name' => $socialUser->getName() ?? 'User',
            'first_name' => $firstName,
            'last_name' => $lastName,
            'avatar' => $socialUser->getAvatar(),
        ]);

        return redirect()->route('auth.social.role');
    }

    public function showRoleSelection(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('oauth_pending_user')) {
            return redirect()->route('login')->with('error', 'Session expired. Please initiate social login again.');
        }

        $oauthData = $request->session()->get('oauth_pending_user');
        return view('auth.select-role', compact('oauthData'));
    }

    public function completeRegistration(Request $request): RedirectResponse
    {
        if (!$request->session()->has('oauth_pending_user')) {
            return redirect()->route('login')->with('error', 'Session expired. Please initiate social login again.');
        }

        $validated = $request->validate([
            'role' => ['required', 'string', 'in:public,student,advocate,judge'],
        ]);

        $oauthData = $request->session()->get('oauth_pending_user');
        $provider = $oauthData['provider'];
        $roleSlug = $validated['role'];

        $isAutoApproved = in_array($roleSlug, ['public', 'student'], true);
        $status = $isAutoApproved ? 'active' : 'pending';
        $approvedAt = $isAutoApproved ? now() : null;

        $userCode = sprintf('%02d%02d%02d%04d', rand(1, 28), rand(1, 12), rand(10, 99), rand(1000, 9999));

        $user = User::create([
            'user_code' => $userCode,
            'first_name' => $oauthData['first_name'],
            'last_name' => $oauthData['last_name'],
            'name' => $oauthData['name'],
            'email' => $oauthData['email'],
            'password' => null, // social user without local password
            'role' => $roleSlug,
            'status' => $status,
            'country' => 'India',
            'terms_accepted' => true,
            'receive_welcome_letter' => true,
            $provider . '_id' => $oauthData['provider_id'],
            'oauth_provider' => $provider,
            'email_verified_at' => now(),
            'approved_at' => $approvedAt,
        ]);

        $roleModel = Role::where('name', $roleSlug)->first();
        if ($roleModel) {
            $user->roles()->attach($roleModel->id);
        }

        $request->session()->forget('oauth_pending_user');

        ActivityLog::log('user_social_registered', "User registered via {$provider} with role [{$roleSlug}]", $user);

        try {
            Mail::to($user->email)->send(new WelcomeLetterMail($user));
        } catch (\Throwable $e) {
            Log::warning('Failed sending welcome email to social user: ' . $e->getMessage());
        }

        if ($status === 'pending') {
            return redirect()->route('login')->with('warning', 'Account created with ' . ucfirst($provider) . '! Because you selected the role of ' . ucfirst($roleSlug) . ', your account requires Administrator approval before you can access the portal.');
        }

        return redirect()->route('login')->with('success', 'Account created with ' . ucfirst($provider) . '! You may now log in to access your Law Universe Control Panel.');
    }
}

