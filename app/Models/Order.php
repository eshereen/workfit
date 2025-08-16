<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;
    protected $fillable = [
        'order_number',
        'guest_token',
        'user_id',
        'customer_id',
        'first_name',
        'last_name',
        'country_id',
        'state',
        'city',
        'email',
        'phone_number',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'discount_amount',
        'total_amount',
        'currency',
        'payment_method',
        'payment_status',
        'status',
        'billing_address',
        'shipping_address',
        'coupon_id',
        'notes',
        'is_guest',
        'loyalty_points_used'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
      public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getShippingAddressAttribute()
    {
                // If we have raw JSON data, decode it
        if (is_string($this->attributes['shipping_address'] ?? null)) {
            $decoded = json_decode($this->attributes['shipping_address'], true);
            Log::info('Shipping address decoded from JSON', [
                'original' => $this->attributes['shipping_address'],
                'decoded' => $decoded
            ]);
            return $decoded ?? [];
        }

        // If it's already an array, return it
        if (is_array($this->attributes['shipping_address'] ?? null)) {
            Log::info('Shipping address already an array', [
                'address' => $this->attributes['shipping_address']
            ]);
            return $this->attributes['shipping_address'];
        }

        // Fallback to empty array
        Log::info('Shipping address fallback to empty array');
        return [];
    }

        public function getBillingAddressAttribute()
    {
        // If we have raw JSON data, decode it
        if (is_string($this->attributes['billing_address'] ?? null)) {
            $decoded = json_decode($this->attributes['billing_address'], true);
            Log::info('Billing address decoded from JSON', [
                'original' => $this->attributes['billing_address'],
                'decoded' => $decoded
            ]);
            return $decoded ?? [];
        }

        // If it's already an array, return it
        if (is_array($this->attributes['billing_address'] ?? null)) {
            Log::info('Billing address already an array', [
                'address' => $this->attributes['billing_address']
            ]);
            return $this->attributes['billing_address'];
        }

        // Fallback to empty array
        Log::info('Billing address fallback to empty array');
        return [];
    }

    public function getCurrencySymbolAttribute()
    {
        $currencyService = app(\App\Services\CountryCurrencyService::class);
        return $currencyService->getCurrencySymbol($this->currency);
    }
}
