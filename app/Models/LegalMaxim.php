<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LegalMaxim extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'latin_maxim',
        'english_meaning',
        'hindi_meaning',
        'description',
        'legal_explanation',
        'example',
        'reference',
        'category_id',
        'subcategory_id',
        'created_by',
    ];

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

