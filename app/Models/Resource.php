<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resource extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'resource_type',
        'category_id',
        'subcategory_id',
        'year',
        'author',
        'language',
        'price',
        'is_free',
        'is_copyright',
        'status',
        'file_path',
        'file_size',
        'file_type',
        'thumbnail',
        'reference_link',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'is_free' => 'boolean',
            'is_copyright' => 'boolean',
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

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function accessRules(): HasMany
    {
        return $this->hasMany(ResourceAccessRule::class);
    }

    public function isAccessibleBy(?User $user): bool
    {
        if ($this->status !== 'published') {
            return $user && ($user->isSuperAdmin() || $user->id === $this->uploaded_by);
        }

        if (!$user) {
            return $this->is_free;
        }

        if ($user->isSuperAdmin() || $user->id === $this->uploaded_by) {
            return true;
        }

        if ($this->is_free) {
            return true;
        }

        // Check if user has active subscription
        if ($user->hasActiveSubscription()) {
            return true;
        }

        // Check role specific access rules
        $userRoleIds = $user->roles()->pluck('roles.id')->toArray();
        if ($user->role) {
            $primaryRole = Role::where('name', $user->role)->first();
            if ($primaryRole) {
                $userRoleIds[] = $primaryRole->id;
            }
        }

        return $this->accessRules()
            ->whereIn('role_id', $userRoleIds)
            ->where('can_view', true)
            ->exists();
    }
}
