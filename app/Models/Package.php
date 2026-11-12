<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    protected $fillable = [
        'plan_code',
        'title',
        'subtitle',
        'tagline',
        'pre_discount_price',
        'current_price',
        'duration_days',
        'description',
        'status',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'pre_discount_price' => 'decimal:2',
            'current_price' => 'decimal:2',
            'duration_days' => 'integer',
            'display_order' => 'integer',
        ];
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'package_features')
            ->withPivot('value')
            ->withTimestamps();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getNameAttribute(): string
    {
        return $this->title ?? 'Package';
    }

    public function getPriceAttribute(): float
    {
        return (float) ($this->current_price ?? 0);
    }

    public function getBillingCycleAttribute(): string
    {
        return $this->duration_days >= 365 ? 'Yearly' : ($this->duration_days >= 30 ? 'Monthly' : $this->duration_days . ' Days');
    }
}

