<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'category_id',
        'keywords',
        'tags',
        'featured_image',
        'content',
        'references',
        'seo_title',
        'meta_description',
        'publication_date',
        'status',
        'disable_view_reporting',
        'views_count',
        'author_id',
    ];

    protected function casts(): array
    {
        return [
            'publication_date' => 'datetime',
            'disable_view_reporting' => 'boolean',
            'views_count' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function views(): HasMany
    {
        return $this->hasMany(BlogView::class);
    }
}
