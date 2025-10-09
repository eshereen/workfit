<?php

namespace App\Models;

use App\Enums\CouponType;
use Database\Factories\CouponFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    /** @use HasFactory<CouponFactory> */
    use HasFactory;
    protected $fillable = ['code', 'type', 'value', 'min_order_amount', 'usage_limit', 'used_count', 'starts_at', 'expires_at', 'active', 'meta_data'];


     protected $casts = [
        'type' => CouponType::class,
        'active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'meta_data' => 'array',
    ];

    public function isValid()
    {
        if (!$this->active) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        if ($this->starts_at && now()->lt($this->starts_at)) {
            return false;
        }

        if ($this->expires_at && now()->gt($this->expires_at)) {
            return false;
        }

        return true;
    }

    public function calculateDiscount($subtotal)
    {
        if ($this->min_order_amount !== null && $subtotal < $this->min_order_amount) {
            return 0;
        }

        if ($this->type === CouponType::Fixed) {
            return min((float) $this->value, (float) $subtotal);
        }

        // Percentage
        return (float) $subtotal * ((float) $this->value / 100);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

}
