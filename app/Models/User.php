<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    protected $with = ['roles.permissions'];

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
    ];

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function permissions(): Collection
    {
        return $this->roles
            ->flatMap(fn ($role) => $role->permissions)
            ->pluck('name')
            ->filter()
            ->unique()
            ->values();
    }

    public function hasRole(string $role): bool
    {
        return $this->roleNames()->contains($role);
    }

    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->contains($permission);
    }

    public function hasAnyPermission(array $permissions): bool
    {
        return $this->permissions()->intersect($permissions)->isNotEmpty();
    }

    public function roleNames(): Collection
    {
        return $this->roles
            ->pluck('name')
            ->filter()
            ->unique()
            ->values();
    }
}
