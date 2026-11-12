<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExaminationCategory extends Model
{
    protected $fillable = ['name', 'slug', 'parent_id', 'description'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function examinations(): HasMany
    {
        return $this->hasMany(Examination::class, 'category_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'category_id');
    }
}
