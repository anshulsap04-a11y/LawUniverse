<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttemptAnswer extends Model
{
    protected $fillable = [
        'attempt_id',
        'question_id',
        'user_answer',
        'is_correct',
        'marks_awarded',
        'is_marked_for_review',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'is_marked_for_review' => 'boolean',
            'marks_awarded' => 'decimal:2',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExaminationAttempt::class, 'attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}

