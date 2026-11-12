<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'coupon_name',
        'coupon_code',
        'description',
        'terms_and_conditions',
        'discount_type',
        'discount_value',
        'start_date',
        'expiration_date',
        'usage_limit',
        'per_user_limit',
        'applicable_package_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'start_date' => 'datetime',
            'expiration_date' => 'datetime',
            'usage_limit' => 'integer',
            'per_user_limit' => 'integer',
        ];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'applicable_package_id');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function isValidFor(?User $user, ?Package $package = null): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->expiration_date && $this->expiration_date->isPast()) {
            return false;
        }

        if ($this->start_date && $this->start_date->isFuture()) {
            return false;
        }

        if ($this->usage_limit && $this->usages()->count() >= $this->usage_limit) {
            return false;
        }

        if ($user && $this->per_user_limit) {
            if ($this->usages()->where('user_id', $user->id)->count() >= $this->per_user_limit) {
                return false;
            }
        }

        if ($package && $this->applicable_package_id && $this->applicable_package_id !== $package->id) {
            return false;
        }

        return true;
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    public function calculateDiscount(float $amount): float
    {
        if ($this->discount_type === 'percentage') {
            return round(($amount * (float)$this->discount_value) / 100, 2);
        }
        return min($amount, (float)$this->discount_value);
    }
}

