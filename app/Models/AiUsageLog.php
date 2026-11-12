<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiUsageLog extends Model
{
    protected $fillable = [
        'user_id',
        'vendor_id',
        'request_type',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'estimated_cost',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'prompt_tokens' => 'integer',
            'completion_tokens' => 'integer',
            'total_tokens' => 'integer',
            'estimated_cost' => 'decimal:4',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(AiVendor::class);
    }
}
