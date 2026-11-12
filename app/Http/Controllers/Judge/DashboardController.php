<?php

namespace App\Http\Controllers\Judge;

use App\Http\Controllers\Controller;
use App\Models\BareAct;
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
            'precedents' => LandmarkJudgment::count(),
            'statutory_acts' => BareAct::count(),
            'jurisprudence_maxims' => LegalMaxim::count(),
            'judicial_notes' => Note::count(),
            'ai_research_queries' => $user->aiConversations()->count(),
        ];

        // Precedents & rulings
        $landmarkDecisions = LandmarkJudgment::latest()->take(6)->get();

        // Statutory Bare Acts
        $recentBareActs = BareAct::latest()->take(4)->get();

        // Judicial Bench Notifications & Directives
        $benchNotifications = Notification::where(function ($q) {
            $q->where('recipient_type', 'all')
              ->orWhere('recipient_type', 'judge');
        })->latest()->take(5)->get();

        // User's private judicial AI research queries
        $aiResearchHistory = $user->aiConversations()->latest()->take(5)->get();

        return view('judge.dashboard', compact(
            'stats',
            'landmarkDecisions',
            'recentBareActs',
            'benchNotifications',
            'aiResearchHistory',
            'user'
        ));
    }
}
