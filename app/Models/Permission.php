<?php

namespace App\Models;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Permission → Roles
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
