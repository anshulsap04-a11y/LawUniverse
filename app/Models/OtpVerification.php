<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    protected $fillable = [
        'email',
        'otp_code',
        'action_type',
        'expires_at',
        'verified_at',
        'attempts',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'verified_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isValid(string $inputOtp): bool
    {
        return !$this->isExpired() && $this->verified_at === null && $this->otp_code === $inputOtp;
    }
}

