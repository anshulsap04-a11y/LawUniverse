<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class AiVendor extends Model
{
    protected $fillable = [
        'vendor_name',
        'api_base_url',
        'api_key',
        'model_name',
        'api_version',
        'configuration',
        'email_address',
        'validity_date',
        'is_active',
        'is_default',
        'allowed_roles',
    ];

    protected function casts(): array
    {
        return [
            'configuration' => 'array',
            'allowed_roles' => 'array',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'validity_date' => 'date',
        ];
    }

    public function setApiKeyAttribute(?string $value): void
    {
        $this->attributes['api_key'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getApiKeyAttribute(?string $value): ?string
    {
        if (!$value) {
            return null;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Throwable) {
            return $value;
        }
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(AiConversation::class, 'vendor_id');
    }
}
