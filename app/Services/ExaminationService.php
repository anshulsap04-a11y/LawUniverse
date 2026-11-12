<?php

namespace App\Services;

use App\Models\Examination;
use App\Models\ExaminationAttempt;
use App\Models\AttemptAnswer;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Carbon;

class ExaminationService
{
    /**
     * Start an examination attempt for a user.
     */
    public function startAttempt(Examination $exam, User $user): ExaminationAttempt
    {
        // Check for an existing in-progress attempt
        $existing = ExaminationAttempt::where('examination_id', $exam->id)
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existing) {
            return $existing;
        }

        $now = Carbon::now();
        $totalQuestions = $exam->questions()->count();

        return ExaminationAttempt::create([
            'examination_id' => $exam->id,
            'user_id' => $user->id,
            'total_questions' => $totalQuestions,
            'attempted_questions' => 0,
            'correct_answers' => 0,
            'wrong_answers' => 0,
            'score' => 0,
            'percentage' => 0,
            'is_passed' => false,
            'status' => 'in_progress',
            'started_at' => $now,
            'completed_at' => null,
            'time_taken_seconds' => 0,
        ]);
    }

    /**
     * Record or update an answer for an in-progress attempt.
     */
    public function saveAnswer(ExaminationAttempt $attempt, int $questionId, string $selectedOption): AttemptAnswer
    {
        if ($attempt->status !== 'in_progress') {
            throw new \RuntimeException('Cannot submit answers to a completed examination attempt.');
        }

        $question = Question::findOrFail($questionId);
        $isCorrect = (trim(strtoupper($question->correct_option)) === trim(strtoupper($selectedOption)));
        $marksAwarded = $isCorrect ? (float)$question->marks : 0.00;

        return AttemptAnswer::updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $questionId,
            ],
            [
                'selected_option' => $selectedOption,
                'is_correct' => $isCorrect,
                'marks_awarded' => $marksAwarded,
            ]
        );
    }

    /**
     * Finalize and score an examination attempt.
     */
    public function submitAttempt(ExaminationAttempt $attempt): ExaminationAttempt
    {
        if ($attempt->status === 'completed') {
            return $attempt;
        }

        $exam = $attempt->examination;
        $answers = $attempt->answers;

        $attemptedCount = $answers->count();
        $correctCount = $answers->where('is_correct', true)->count();
        $wrongCount = $attemptedCount - $correctCount;

        $earnedMarks = (float)$answers->where('is_correct', true)->sum('marks_awarded');
        $totalMarks = (float)$exam->total_marks > 0 ? (float)$exam->total_marks : 100;

        $percentage = round(($earnedMarks / $totalMarks) * 100, 2);
        $isPassed = $percentage >= (float)$exam->passing_percentage;

        $startedAt = Carbon::parse($attempt->started_at);
        $completedAt = Carbon::now();
        $timeTaken = $startedAt->diffInSeconds($completedAt);

        $attempt->update([
            'attempted_questions' => $attemptedCount,
            'correct_answers' => $correctCount,
            'wrong_answers' => $wrongCount,
            'score' => $earnedMarks,
            'percentage' => $percentage,
            'is_passed' => $isPassed,
            'status' => 'completed',
            'completed_at' => $completedAt,
            'time_taken_seconds' => $timeTaken,
        ]);

        return $attempt->fresh();
    }
}
