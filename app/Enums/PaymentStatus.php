<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case PROCESSED = 'processed';
    case CONFIRMED = 'confirmed';
    case COMPLETED = 'completed';
    case PAID = 'paid';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    case PARTIALLY_REFUNDED = 'partially_refunded';
    case DECLINED = 'declined';
    case EXPIRED = 'expired';
    case VOIDED = 'voided';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::PROCESSED => 'Processed',
            self::CONFIRMED => 'Confirmed',
            self::COMPLETED => 'Completed',
            self::PAID => 'Paid',
            self::FAILED => 'Failed',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
            self::PARTIALLY_REFUNDED => 'Partially Refunded',
            self::DECLINED => 'Declined',
            self::EXPIRED => 'Expired',
            self::VOIDED => 'Voided',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::PROCESSING => 'info',
            self::PROCESSED => 'success',
            self::CONFIRMED => 'success',
            self::COMPLETED => 'success',
            self::PAID => 'success',
            self::FAILED => 'danger',
            self::CANCELLED => 'danger',
            self::REFUNDED => 'info',
            self::PARTIALLY_REFUNDED => 'warning',
            self::DECLINED => 'danger',
            self::EXPIRED => 'danger',
            self::VOIDED => 'danger',
        };
    }

    public function isSuccessful(): bool
    {
        return in_array($this, [
            self::PROCESSED,
            self::CONFIRMED,
            self::COMPLETED,
            self::PAID,
        ]);
    }

    public function isPending(): bool
    {
        return in_array($this, [
            self::PENDING,
            self::PROCESSING,
        ]);
    }

    public function isFailed(): bool
    {
        return in_array($this, [
            self::FAILED,
            self::CANCELLED,
            self::DECLINED,
            self::EXPIRED,
            self::VOIDED,
        ]);
    }
}
