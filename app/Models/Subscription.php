<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'package_id',
        'start_date',
        'expiry_date',
        'status',
        'auto_renew',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'expiry_date' => 'datetime',
            'auto_renew' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expiry_date->isFuture();
    }
}

