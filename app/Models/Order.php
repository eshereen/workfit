<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\OrderStatus;
use App\Services\CountryCurrencyService;
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
        'use_billing_for_shipping',
        'coupon_id',
        'notes',
        'is_guest',
    ];
    protected $casts = [
        'subtotal' => 'integer',
        'tax_amount' => 'integer',
        'shipping_amount' => 'integer',
        'discount_amount' => 'integer',
        'total_amount' => 'integer',
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
        $currencyService = app(CountryCurrencyService::class);
        return $currencyService->getCurrencySymbol($this->currency);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    // Payment Status Helper Methods
    public function isPaymentSuccessful(): bool
    {
        return $this->payment_status?->isSuccessful() ?? false;
    }

    public function isPaymentPending(): bool
    {
        return $this->payment_status?->isPending() ?? false;
    }

    public function isPaymentFailed(): bool
    {
        return $this->payment_status?->isFailed() ?? false;
    }

    public function getPaymentStatusLabel(): string
    {
        return $this->payment_status?->label() ?? 'Unknown';
    }

    public function getPaymentStatusColor(): string
    {
        return $this->payment_status?->color() ?? 'gray';
    }

    public function markAsProcessed(): void
    {
        $this->update(['payment_status' => PaymentStatus::PROCESSED]);
    }

    public function markAsPaymentConfirmed(): void
    {
        $this->update(['payment_status' => PaymentStatus::CONFIRMED]);
    }

    public function markAsPaymentCompleted(): void
    {
        $this->update(['payment_status' => PaymentStatus::COMPLETED]);
    }

    public function markAsFailed(): void
    {
        $this->update(['payment_status' => PaymentStatus::FAILED]);
    }

    public function markAsPaymentCancelled(): void
    {
        $this->update(['payment_status' => PaymentStatus::CANCELLED]);
    }

    public function markAsPaid(): void
    {
        $this->update(['payment_status' => PaymentStatus::PAID]);
    }

    // Order Status Helper Methods
    public function isOrderActive(): bool
    {
        return $this->status?->isActive() ?? false;
    }

    public function isOrderCompleted(): bool
    {
        return $this->status?->isCompleted() ?? false;
    }

    public function isOrderCancelled(): bool
    {
        return $this->status?->isCancelled() ?? false;
    }

    public function canBeCancelled(): bool
    {
        return $this->status?->canBeCancelled() ?? false;
    }

    public function canBeShipped(): bool
    {
        return $this->status?->canBeShipped() ?? false;
    }

    public function canBeDelivered(): bool
    {
        return $this->status?->canBeDelivered() ?? false;
    }

    public function getOrderStatusLabel(): string
    {
        return $this->status?->label() ?? 'Unknown';
    }

    public function getOrderStatusColor(): string
    {
        return $this->status?->color() ?? 'gray';
    }

    // Order Status Update Methods
    public function markAsConfirmed(): void
    {
        $this->update(['status' => OrderStatus::CONFIRMED]);
    }

    public function markAsProcessing(): void
    {
        $this->update(['status' => OrderStatus::PROCESSING]);
    }

    public function markAsShipped(): void
    {
        $this->update(['status' => OrderStatus::SHIPPED]);
    }

    public function markAsDelivered(): void
    {
        $this->update(['status' => OrderStatus::DELIVERED]);
    }

    public function markAsCompleted(): void
    {
        $this->update(['status' => OrderStatus::COMPLETED]);
    }

    public function markAsCancelled(): void
    {
        $this->update(['status' => OrderStatus::CANCELLED]);
    }

    public function markAsOnHold(): void
    {
        $this->update(['status' => OrderStatus::ON_HOLD]);
    }

    public function markAsBackordered(): void
    {
        $this->update(['status' => OrderStatus::BACKORDERED]);
    }

    public function markAsPartiallyShipped(): void
    {
        $this->update(['status' => OrderStatus::PARTIALLY_SHIPPED]);
    }

    public function markAsReturned(): void
    {
        $this->update(['status' => OrderStatus::RETURNED]);
    }

    public function markAsRefunded(): void
    {
        $this->update(['status' => OrderStatus::REFUNDED]);
    }
}
