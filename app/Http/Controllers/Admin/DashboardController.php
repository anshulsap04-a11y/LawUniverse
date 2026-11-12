<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $pendingApprovals = User::where('status', 'pending')->count();
        $deactivatedUsers = User::where('status', 'deactivated')->count();

        $roleCounts = [
            'judge' => User::where('role', 'judge')->count(),
            'advocate' => User::where('role', 'advocate')->count(),
            'student' => User::where('role', 'student')->count(),
            'public' => User::where('role', 'public')->count(),
            'admin' => User::whereIn('role', ['admin', 'super_admin'])->count(),
        ];

        $pendingUsers = User::where('status', 'pending')
            ->latest()
            ->take(10)
            ->get();

        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->take(10)
            ->get();

        $modules = Module::with('submodules')->orderBy('order')->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'pendingApprovals',
            'deactivatedUsers',
            'roleCounts',
            'pendingUsers',
            'recentActivities',
            'modules'
        ));
    }
}
