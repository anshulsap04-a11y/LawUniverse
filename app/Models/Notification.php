<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'subject_code',
        'subject',
        'message',
        'recipient_type',
        'target_roles',
        'target_users',
        'post_date',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'target_roles' => 'array',
            'target_users' => 'array',
            'post_date' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(NotificationRecipient::class);
    }
}

