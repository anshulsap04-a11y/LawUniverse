<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'category',
        'tags',
        'attachment_path',
        'is_private',
        'is_favorite',
    ];

    protected function casts(): array
    {
        return [
            'is_private' => 'boolean',
            'is_favorite' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
