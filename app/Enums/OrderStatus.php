<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    case RETURNED = 'returned';
    case ON_HOLD = 'on_hold';
    case BACKORDERED = 'backordered';
    case PARTIALLY_SHIPPED = 'partially_shipped';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::CONFIRMED => 'Confirmed',
            self::PROCESSING => 'Processing',
            self::SHIPPED => 'Shipped',
            self::DELIVERED => 'Delivered',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
            self::RETURNED => 'Returned',
            self::ON_HOLD => 'On Hold',
            self::BACKORDERED => 'Backordered',
            self::PARTIALLY_SHIPPED => 'Partially Shipped',
            self::COMPLETED => 'Completed',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::CONFIRMED => 'info',
            self::PROCESSING => 'info',
            self::SHIPPED => 'primary',
            self::DELIVERED => 'success',
            self::CANCELLED => 'danger',
            self::REFUNDED => 'info',
            self::RETURNED => 'warning',
            self::ON_HOLD => 'warning',
            self::BACKORDERED => 'warning',
            self::PARTIALLY_SHIPPED => 'primary',
            self::COMPLETED => 'success',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [
            self::PENDING,
            self::CONFIRMED,
            self::PROCESSING,
            self::SHIPPED,
            self::PARTIALLY_SHIPPED,
            self::ON_HOLD,
            self::BACKORDERED,
        ]);
    }

    public function isCompleted(): bool
    {
        return in_array($this, [
            self::DELIVERED,
            self::COMPLETED,
        ]);
    }

    public function isCancelled(): bool
    {
        return in_array($this, [
            self::CANCELLED,
            self::REFUNDED,
            self::RETURNED,
        ]);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this, [
            self::PENDING,
            self::CONFIRMED,
            self::PROCESSING,
            self::ON_HOLD,
            self::BACKORDERED,
        ]);
    }

    public function canBeShipped(): bool
    {
        return in_array($this, [
            self::CONFIRMED,
            self::PROCESSING,
            self::ON_HOLD,
        ]);
    }

    public function canBeDelivered(): bool
    {
        return in_array($this, [
            self::SHIPPED,
            self::PARTIALLY_SHIPPED,
        ]);
    }
}
