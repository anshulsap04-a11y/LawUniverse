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
            'start_time' => $now,
            'time_spent_seconds' => 0,
            'total_score' => 0.00,
            'percentage' => 0.00,
            'correct_count' => 0,
            'incorrect_count' => 0,
            'unanswered_count' => $totalQuestions,
            'is_passed' => false,
            'status' => 'in_progress',
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
        $isCorrect = (trim(strtoupper($question->correct_answer)) === trim(strtoupper($selectedOption)));
        $marksAwarded = $isCorrect ? (float)$question->marks : 0.00;

        return AttemptAnswer::updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $questionId,
            ],
            [
                'user_answer' => $selectedOption,
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
        $totalQuestions = $exam->questions()->count();
        $answers = $attempt->answers;

        $answeredCount = $answers->count();
        $correctCount = $answers->where('is_correct', true)->count();
        $wrongCount = $answeredCount - $correctCount;
        $unansweredCount = max(0, $totalQuestions - $answeredCount);

        $earnedMarks = (float)$answers->where('is_correct', true)->sum('marks_awarded');
        $totalMarks = (float)$exam->total_marks > 0 ? (float)$exam->total_marks : 100.00;

        $percentage = round(($earnedMarks / $totalMarks) * 100, 2);
        $passThreshold = (float)($exam->cutoff_percentage ?? 40.00);
        $isPassed = $percentage >= $passThreshold;

        $startTime = Carbon::parse($attempt->start_time);
        $endTime = Carbon::now();
        $timeTaken = $startTime->diffInSeconds($endTime);

        $attempt->update([
            'end_time' => $endTime,
            'time_spent_seconds' => $timeTaken,
            'total_score' => $earnedMarks,
            'percentage' => $percentage,
            'correct_count' => $correctCount,
            'incorrect_count' => $wrongCount,
            'unanswered_count' => $unansweredCount,
            'is_passed' => $isPassed,
            'status' => 'completed',
        ]);

        return $attempt->fresh();
    }
}
