<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceAccessRule extends Model
{
    protected $fillable = [
        'resource_id',
        'role_id',
        'can_view',
        'can_preview',
        'can_download',
        'plan_id',
    ];

    protected function casts(): array
    {
        return [
            'can_view' => 'boolean',
            'can_preview' => 'boolean',
            'can_download' => 'boolean',
        ];
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
