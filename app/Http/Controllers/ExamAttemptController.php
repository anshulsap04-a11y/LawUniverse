<?php

namespace App\Http\Controllers;

use App\Models\Examination;
use App\Models\ExaminationAttempt;
use App\Services\ExaminationService;
use Illuminate\Http\Request;

class ExamAttemptController extends Controller
{
    protected ExaminationService $examService;

    public function __construct(ExaminationService $examService)
    {
        $this->examService = $examService;
    }

    /**
     * Start or resume an examination attempt.
     */
    public function start(Examination $exam)
    {
        $user = auth()->user();
        $attempt = $this->examService->startAttempt($exam, $user);

        return redirect()->route('exams.attempt', $attempt->id);
    }

    /**
     * Interactive examination interface.
     */
    public function attempt(ExaminationAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        if ($attempt->status === 'completed') {
            return redirect()->route('exams.result', $attempt->id);
        }

        $exam = $attempt->examination()->with('questions')->first();
        $questions = $exam->questions;
        $savedAnswers = $attempt->answers->pluck('selected_option', 'question_id')->toArray();

        return view('exams.take', compact('attempt', 'exam', 'questions', 'savedAnswers'));
    }

    /**
     * Auto-save or submit a single answer (AJAX or form).
     */
    public function saveAnswer(Request $request, ExaminationAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'selected_option' => 'required|in:A,B,C,D',
        ]);

        $answer = $this->examService->saveAnswer($attempt, $validated['question_id'], $validated['selected_option']);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Answer saved.',
                'question_id' => $answer->question_id,
            ]);
        }

        return back();
    }

    /**
     * Finalize and score examination attempt.
     */
    public function submit(ExaminationAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        $attempt = $this->examService->submitAttempt($attempt);

        return redirect()->route('exams.result', $attempt->id)->with('success', 'Examination submitted successfully.');
    }

    /**
     * Show comprehensive score, breakdown and review.
     */
    public function result(ExaminationAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        $attempt->load(['examination.questions', 'answers.question']);

        return view('exams.result', compact('attempt'));
    }

    protected function authorizeAttempt(ExaminationAttempt $attempt): void
    {
        if (auth()->id() !== $attempt->user_id && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access to this examination session.');
        }
    }
}

