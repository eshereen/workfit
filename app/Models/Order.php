<?php

namespace App\Models;

use App\Models\Shipping;
use App\Services\CountryCurrencyService;
use App\Enums\PaymentStatus;
use App\Enums\OrderStatus;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
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
        'tax_rate',
        'tax_amount',
        'shipping_amount',
        'discount_amount',
        'total_amount',
        'currency',
        'payment_method',
        'payment_status',
        'status',
        'billing_address',
        'billing_building_number',
        'shipping_address',
        'shipping_building_number',
        'shipping_id',
        'use_billing_for_shipping',
        'coupon_id',
        'notes',
        'is_guest',
    ];
    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'billing_address' => 'string',
        'shipping_address' => 'string',
        'billing_building_number' => 'string',
        'shipping_building_number' => 'string',
        'use_billing_for_shipping' => 'boolean',
        'payment_status' => PaymentStatus::class,
        'status' => OrderStatus::class,
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
      public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function shipping()
    {
        return $this->belongsTo(Shipping::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getShippingAddressAttribute()
    {
        $address = $this->attributes['shipping_address'] ?? null;

        // If it's null or empty, return null
        if (empty($address)) {
            return null;
        }

        // If it's a string, check if it's JSON
        if (is_string($address)) {
            $decoded = json_decode($address, true);
            // If JSON decode was successful, return the decoded array
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
            // Otherwise, return the string as-is
            return $address;
        }

        // If it's already an array, return it
        if (is_array($address)) {
            return $address;
        }

        return null;
    }

    public function getBillingAddressAttribute()
    {
        $address = $this->attributes['billing_address'] ?? null;

        // If it's null or empty, return null
        if (empty($address)) {
            return null;
        }

        // If it's a string, check if it's JSON
        if (is_string($address)) {
            $decoded = json_decode($address, true);
            // If JSON decode was successful, return the decoded array
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
            // Otherwise, return the string as-is
            return $address;
        }

        // If it's already an array, return it
        if (is_array($address)) {
            return $address;
        }

        return null;
    }

    public function getCurrencySymbolAttribute()
    {
        $currencyService = app(CountryCurrencyService::class);
        return $currencyService->getCurrencySymbol($this->currency);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
