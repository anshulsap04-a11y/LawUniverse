<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Examination extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'exam_code',
        'description',
        'category_id',
        'subcategory_id',
        'first_label_id',
        'second_label_id',
        'start_date',
        'end_date',
        'duration_minutes',
        'total_marks',
        'passing_marks',
        'cutoff_percentage',
        'negative_marking_ratio',
        'flag_negative_marking',
        'instructions',
        'status',
        'max_attempts',
        'shuffle_questions',
        'show_result_immediately',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'total_marks' => 'decimal:2',
            'passing_marks' => 'decimal:2',
            'cutoff_percentage' => 'decimal:2',
            'negative_marking_ratio' => 'decimal:2',
            'flag_negative_marking' => 'boolean',
            'shuffle_questions' => 'boolean',
            'show_result_immediately' => 'boolean',
            'duration_minutes' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExaminationCategory::class, 'category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(ExaminationCategory::class, 'subcategory_id');
    }

    public function firstLabel(): BelongsTo
    {
        return $this->belongsTo(ExaminationLabel::class, 'first_label_id');
    }

    public function secondLabel(): BelongsTo
    {
        return $this->belongsTo(ExaminationLabel::class, 'second_label_id');
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'examination_questions')
            ->withPivot('order', 'marks')
            ->withTimestamps()
            ->orderBy('examination_questions.order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExaminationAttempt::class);
    }
}

