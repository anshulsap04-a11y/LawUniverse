<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\BareAct;
use App\Models\Examination;
use App\Models\LegalMaxim;
use App\Models\Note;
use App\Models\Notification;
use App\Models\PreviousYearPaper;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        // Exam metrics strictly scoped to current user
        $userAttempts = $user->examinationAttempts()->with('examination')->latest()->get();
        $completedAttempts = $userAttempts->where('status', 'completed');
        $passedAttempts = $completedAttempts->where('is_passed', true);
        $avgScore = $completedAttempts->count() > 0 ? round($completedAttempts->avg('percentage'), 1) : 0;

        $stats = [
            'available_exams' => Examination::where('status', 'published')->count(),
            'my_attempts' => $userAttempts->count(),
            'completed_exams' => $completedAttempts->count(),
            'passed_exams' => $passedAttempts->count(),
            'avg_score' => $avgScore,
            'previous_papers' => PreviousYearPaper::count(),
            'bare_acts' => BareAct::count(),
            'legal_maxims' => LegalMaxim::count(),
            'notes' => Note::count(),
            'ai_queries' => $user->aiConversations()->count(),
        ];

        // Available exams for practice
        $availableExams = Examination::where('status', 'published')
            ->withCount('questions')
            ->latest()
            ->take(6)
            ->get();

        // Recent attempts by this student
        $recentAttempts = $userAttempts->take(5);

        // Previous year papers
        $previousPapers = PreviousYearPaper::latest()->take(5)->get();

        // Student notifications
        $notifications = Notification::where(function ($q) {
            $q->where('recipient_type', 'all')
              ->orWhere('recipient_type', 'student');
        })->latest()->take(5)->get();

        // Active subscription status
        $activeSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->where('expiry_date', '>=', now())
            ->with('package')
            ->latest()
            ->first();

        // User's private AI conversations
        $aiConversations = $user->aiConversations()->latest()->take(5)->get();

        return view('student.dashboard', compact(
            'stats',
            'availableExams',
            'recentAttempts',
            'previousPapers',
            'notifications',
            'activeSubscription',
            'aiConversations',
            'user'
        ));
    }
}
