<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreviousYearPaper extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'exam_name',
        'year',
        'category_id',
        'subject',
        'file_path',
        'answer_key_path',
        'explanation',
        'is_free',
        'is_copyright',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_free' => 'boolean',
            'is_copyright' => 'boolean',
            'year' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExaminationCategory::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
