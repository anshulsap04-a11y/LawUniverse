<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogView extends Model
{
    protected $fillable = ['blog_id', 'ip_address', 'user_agent'];

    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }
}

