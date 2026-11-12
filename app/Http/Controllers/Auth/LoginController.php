<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->to(Auth::user()->getDashboardRoute());
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'The provided credentials do not match our records.']);
        }

        // Check user approval / status
        if ($user->isPending()) {
            return back()->withInput($request->only('email'))
                ->with('warning', 'Your account is pending verification and approval by an administrator. Please wait for Super Admin approval before logging in.');
        }

        if ($user->isDeactivated() || $user->status === 'rejected') {
            return back()->withInput($request->only('email'))
                ->with('error', 'Your account has been deactivated or rejected. Please contact the portal administrator.');
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();

        ActivityLog::log('user_login', 'User logged in via email/password', $user);

        $intended = $request->session()->pull('url.intended');
        $intendedPath = $intended ? parse_url($intended, PHP_URL_PATH) : null;
        if ($intended && $intendedPath !== '/' && $intendedPath !== '' && !str_contains($intended, '/login')) {
            return redirect()->to($intended);
        }

        return redirect()->to($user->getDashboardRoute());
    }

    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            ActivityLog::log('user_logout', 'User logged out', Auth::user());
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}

