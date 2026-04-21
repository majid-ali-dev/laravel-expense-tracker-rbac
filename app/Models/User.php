<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Permission;
use App\Models\Role;

class User extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
    ];

    // Relationships
    // User → Roles
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Helper
    public function hasRole($role)
    {
        return $this->roles->contains('name', $role);
    }

    // User → Permissions (via roles)
    public function permissions()
    {
        return $this->hasManyThrough(
            Permission::class,
            Role::class,
            'user_id',
            'role_id'
        );
    }
}
