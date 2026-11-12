<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BareAct extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'resource_id',
        'act_name',
        'act_number',
        'year',
        'description',
        'category_id',
        'subcategory_id',
        'jurisdiction',
        'language',
        'content',
        'file_path',
        'amendment_status',
        'publication_date',
        'is_free',
        'is_copyright',
        'price',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_free' => 'boolean',
            'is_copyright' => 'boolean',
            'publication_date' => 'date',
            'price' => 'decimal:2',
            'year' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ResourceCategory::class, 'category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(ResourceCategory::class, 'subcategory_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
