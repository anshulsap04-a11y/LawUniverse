<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BareAct;
use App\Models\Blog;
use App\Models\LandmarkJudgment;
use App\Models\LegalMaxim;
use App\Models\Notification;
use App\Models\Package;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $stats = [
            'bare_acts' => BareAct::count(),
            'legal_maxims' => LegalMaxim::count(),
            'landmark_judgments' => LandmarkJudgment::count(),
            'blogs' => Blog::where('status', 'published')->count(),
            'ai_queries' => $user ? $user->aiConversations()->count() : 0,
        ];

        $recentBlogs = Blog::where('status', 'published')->latest()->take(3)->get();

        $notifications = Notification::where(function ($q) {
            $q->where('recipient_type', 'all')
              ->orWhere('recipient_type', 'public');
        })->latest()->take(5)->get();

        $packages = Package::where('is_active', true)->with('features')->take(3)->get();

        $activeSubscription = $user ? $user->subscriptions()
            ->where('status', 'active')
            ->where('expiry_date', '>=', now())
            ->with('package')
            ->latest()
            ->first() : null;

        return view('public.dashboard', compact('stats', 'recentBlogs', 'notifications', 'packages', 'activeSubscription', 'user'));
    }
}
