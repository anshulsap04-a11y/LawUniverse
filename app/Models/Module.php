<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'order',
        'description',
    ];

    public function submodules(): HasMany
    {
        return $this->hasMany(Submodule::class)->orderBy('order');
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

