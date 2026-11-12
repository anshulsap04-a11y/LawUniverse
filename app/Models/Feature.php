<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Feature extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'module_scope',
        'feature_type',
        'default_value',
    ];

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'package_features')
            ->withPivot('value')
            ->withTimestamps();
    }
}

