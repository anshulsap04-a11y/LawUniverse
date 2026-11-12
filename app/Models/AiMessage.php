<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiMessage extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender',
        'message',
        'attachment_path',
        'attachment_type',
        'legal_citations',
        'tokens_used',
        'cost',
    ];

    protected function casts(): array
    {
        return [
            'legal_citations' => 'array',
            'tokens_used' => 'integer',
            'cost' => 'decimal:4',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AiConversation::class, 'conversation_id');
    }
}
