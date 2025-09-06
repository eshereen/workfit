<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';

    /**
     * Get all enum values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get enum values with labels for forms
     */
    public static function options(): array
    {
        return [
            self::PENDING->value => 'Pending',
            self::PAID->value => 'Paid',
            self::FAILED->value => 'Failed',
            self::REFUNDED->value => 'Refunded',
        ];
    }

    /**
     * Get human-readable label for a status
     */
    public function getLabel(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::PAID => 'Paid',
            self::FAILED => 'Failed',
            self::REFUNDED => 'Refunded',
        };
    }
}