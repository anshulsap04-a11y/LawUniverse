<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ImpersonationService;
use Illuminate\Http\Request;

class ImpersonationController extends Controller
{
    protected ImpersonationService $impersonationService;

    public function __construct(ImpersonationService $impersonationService)
    {
        $this->impersonationService = $impersonationService;
    }

    /**
     * Start impersonating a user.
     */
    public function impersonate(User $user)
    {
        if ($this->impersonationService->impersonate($user)) {
            // Redirect to user's appropriate portal
            return redirect($user->dashboard_url)->with('info', "You are now impersonating {$user->full_name} ({$user->role_name}).");
        }

        return back()->with('error', 'Cannot impersonate this account.');
    }

    /**
     * Stop impersonating and return to Super Admin.
     */
    public function stop()
    {
        $admin = $this->impersonationService->stopImpersonating();

        if ($admin) {
            return redirect()->route('admin.dashboard')->with('success', 'Switched back to Super Admin account.');
        }

        return redirect()->route('login');
    }
}
