<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'question_code',
        'question_title',
        'question_description',
        'question_type',
        'options',
        'correct_answer',
        'explanation',
        'marks',
        'negative_marks',
        'category_id',
        'subcategory_id',
        'first_label_id',
        'second_label_id',
        'difficulty',
        'year',
        'is_interactive',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'marks' => 'decimal:2',
            'negative_marks' => 'decimal:2',
            'is_interactive' => 'boolean',
            'year' => 'integer',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function examinations(): BelongsToMany
    {
        return $this->belongsToMany(Examination::class, 'examination_questions')
            ->withPivot('order', 'marks')
            ->withTimestamps();
    }
}

