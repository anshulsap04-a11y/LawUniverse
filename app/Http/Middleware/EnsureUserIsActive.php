<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->isPending()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('warning', 'Your account is pending verification and approval by an administrator. Please wait for Super Admin approval before logging in.');
            }

            if ($user->isDeactivated() || $user->status === 'rejected') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'Your account has been deactivated or rejected. Please contact the portal administrator.');
            }
        }

        return $next($request);
    }
}

