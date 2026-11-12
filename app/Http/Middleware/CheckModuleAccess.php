<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleAccess
{
    /**
     * Handle an incoming request.
     * $params: moduleSlug, [submoduleSlug], [action]
     */
    public function handle(Request $request, Closure $next, string $moduleSlug, ?string $submoduleSlug = null, string $action = 'can_view'): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if ($user->hasModuleAccess($moduleSlug, $submoduleSlug, $action)) {
            return $next($request);
        }

        abort(403, "Access Denied: You do not have dynamic permission for module [{$moduleSlug}] / submodule [{$submoduleSlug}].");
    }
}
