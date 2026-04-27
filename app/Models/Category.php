<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Expense;

class Category extends Model
{
    protected $fillable = ['name'];

    // Relationship with Expense
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
