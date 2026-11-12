<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LandmarkJudgment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'case_name',
        'case_number',
        'court',
        'bench',
        'judgment_date',
        'citation',
        'legal_principle',
        'case_summary',
        'full_judgment',
        'category_id',
        'subcategory_id',
        'year',
        'language',
        'document_path',
        'is_free',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'judgment_date' => 'date',
            'is_free' => 'boolean',
            'year' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ResourceCategory::class, 'category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(ResourceCategory::class, 'subcategory_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

