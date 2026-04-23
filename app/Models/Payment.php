<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'status',
        'month',
        'updated_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    // 🔵 Member (jis ne payment deni hai)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔴 Manager/Admin (jis ne update kiya)
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
