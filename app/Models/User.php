<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'user_code',
        'first_name',
        'last_name',
        'name',
        'email',
        'password',
        'dob',
        'education',
        'role',
        'address',
        'mobile_number',
        'about',
        'country',
        'profile_photo',
        'status',
        'receive_welcome_letter',
        'terms_accepted',
        'enable_2fa',
        'enable_instant_otp',
        'google_id',
        'facebook_id',
        'oauth_provider',
        'approved_at',
        'approved_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'dob' => 'date',
            'approved_at' => 'datetime',
            'password' => 'hashed',
            'receive_welcome_letter' => 'boolean',
            'terms_accepted' => 'boolean',
            'enable_2fa' => 'boolean',
            'enable_instant_otp' => 'boolean',
        ];
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        if (in_array('super_admin', $roles, true) && $this->isSuperAdmin()) {
            return true;
        }

        if ($this->role && in_array($this->role, $roles, true)) {
            return true;
        }

        return $this->roles()->whereIn('name', $roles)->exists();
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin' || $this->roles()->where('name', 'super_admin')->exists();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isDeactivated(): bool
    {
        return $this->status === 'deactivated';
    }

    public function hasPermission(string $permissionName): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        foreach ($this->roles as $role) {
            if ($role->permissions()->where('name', $permissionName)->exists()) {
                return true;
            }
        }

        return false;
    }

    public function hasModuleAccess(string $moduleSlug, ?string $submoduleSlug = null, string $action = 'can_view'): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $module = Module::where('slug', $moduleSlug)->first();
        if (!$module) {
            return false;
        }

        $submoduleId = null;
        if ($submoduleSlug) {
            $submodule = Submodule::where('module_id', $module->id)->where('slug', $submoduleSlug)->first();
            $submoduleId = $submodule ? $submodule->id : null;
        }

        $roleIds = $this->roles()->pluck('roles.id')->toArray();
        if ($this->role) {
            $primaryRole = Role::where('name', $this->role)->first();
            if ($primaryRole && !in_array($primaryRole->id, $roleIds, true)) {
                $roleIds[] = $primaryRole->id;
            }
        }

        $query = RoleModulePermission::whereIn('role_id', $roleIds)
            ->where('module_id', $module->id)
            ->where($action, true);

        if ($submoduleId) {
            $query->where(function ($q) use ($submoduleId) {
                $q->where('submodule_id', $submoduleId)
                  ->orWhereNull('submodule_id');
            });
        }

        return $query->exists();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function hasActiveSubscription(): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->subscriptions()
            ->where('status', 'active')
            ->where('expiry_date', '>=', now())
            ->exists();
    }

    public function getDashboardRoute(): string
    {
        return match ($this->role) {
            'super_admin', 'admin' => route('admin.dashboard'),
            'judge' => route('judge.dashboard'),
            'advocate' => route('advocate.dashboard'),
            'student' => route('student.dashboard'),
            default => route('public.dashboard'),
        };
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->profile_photo && file_exists(public_path('storage/' . $this->profile_photo))) {
            return asset('storage/' . $this->profile_photo);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=d4af37&color=111111&bold=true';
    }
}
