<?php

namespace App\Http\Controllers\Advocate;

use App\Http\Controllers\Controller;
use App\Models\BareAct;
use App\Models\Blog;
use App\Models\LandmarkJudgment;
use App\Models\LegalMaxim;
use App\Models\Note;
use App\Models\Notification;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $stats = [
            'landmark_judgments' => LandmarkJudgment::count(),
            'bare_acts' => BareAct::count(),
            'legal_maxims' => LegalMaxim::count(),
            'notes' => Note::count(),
            'ai_queries' => $user->aiConversations()->count(),
            'blogs' => Blog::where('status', 'published')->count(),
        ];

        // Recent landmark judgments
        $recentJudgments = LandmarkJudgment::latest()->take(5)->get();

        // Recent legal commentary
        $recentBlogs = Blog::where('status', 'published')->latest()->take(3)->get();

        // Advocate alerts & notifications
        $notifications = Notification::where(function ($q) {
            $q->where('recipient_type', 'all')
              ->orWhere('recipient_type', 'advocate');
        })->latest()->take(5)->get();

        // Advocate active subscription
        $activeSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->where('expiry_date', '>=', now())
            ->with('package')
            ->latest()
            ->first();

        // User's private AI draft conversations
        $aiConversations = $user->aiConversations()->latest()->take(5)->get();

        return view('advocate.dashboard', compact(
            'stats',
            'recentJudgments',
            'recentBlogs',
            'notifications',
            'activeSubscription',
            'aiConversations',
            'user'
        ));
    }
}
