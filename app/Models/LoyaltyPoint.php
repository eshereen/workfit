<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyPoint extends Model
{
    /** @use HasFactory<\Database\Factories\LoyaltyPointFactory> */
    use HasFactory;
    protected $fillable = ['user_id', 'points', 'type', 'description', 'order_id', 'referral_id', 'expires_at', 'is_processed'];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_processed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function referral()
    {
        return $this->belongsTo(User::class, 'referral_id');
    }

    // Scope for unprocessed points
    public function scopeUnprocessed($query)
    {
        return $query->where('is_processed', false);
    }

    // Scope for valid points (not expired)
    public function scopeValid($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }
}
