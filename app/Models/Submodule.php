<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submodule extends Model
{
    protected $fillable = [
        'module_id',
        'name',
        'slug',
        'route_name',
        'icon',
        'order',
        'description',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }

    public function rolePermissions(): HasMany
    {
        return $this->hasMany(RoleModulePermission::class);
    }
}

