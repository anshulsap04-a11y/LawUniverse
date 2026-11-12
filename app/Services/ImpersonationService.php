<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ImpersonationService
{
    const SESSION_KEY = 'impersonating_admin_id';

    /**
     * Start impersonating another user. Only Super Admin can call this.
     */
    public function impersonate(User $targetUser): bool
    {
        $currentAdmin = Auth::user();

        if (!$currentAdmin || !$currentAdmin->isSuperAdmin()) {
            return false;
        }

        // Prevent impersonating self
        if ($currentAdmin->id === $targetUser->id) {
            return false;
        }

        // Store original admin ID in session
        session()->put(self::SESSION_KEY, $currentAdmin->id);

        // Login as the target user
        Auth::login($targetUser);

        return true;
    }

    /**
     * Stop impersonating and return to original Super Admin.
     */
    public function stopImpersonating(): ?User
    {
        $originalAdminId = session()->get(self::SESSION_KEY);

        if (!$originalAdminId) {
            return null;
        }

        $admin = User::find($originalAdminId);

        if ($admin && $admin->isSuperAdmin()) {
            session()->forget(self::SESSION_KEY);
            Auth::login($admin);
            return $admin;
        }

        return null;
    }

    /**
     * Check if current session is an active impersonation.
     */
    public function isImpersonating(): bool
    {
        return session()->has(self::SESSION_KEY);
    }

    /**
     * Get the original admin ID if currently impersonating.
     */
    public function getOriginalAdminId(): ?int
    {
        return session()->get(self::SESSION_KEY);
    }
}

