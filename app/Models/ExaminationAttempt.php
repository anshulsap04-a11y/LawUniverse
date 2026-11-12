<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExaminationAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'examination_id',
        'start_time',
        'end_time',
        'time_spent_seconds',
        'total_score',
        'percentage',
        'correct_count',
        'incorrect_count',
        'unanswered_count',
        'is_passed',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'total_score' => 'decimal:2',
            'percentage' => 'decimal:2',
            'is_passed' => 'boolean',
            'time_spent_seconds' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function examination(): BelongsTo
    {
        return $this->belongsTo(Examination::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AttemptAnswer::class, 'attempt_id');
    }
}

