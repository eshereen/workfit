<?php
// app/Enums/LoyaltyRuleType.php

namespace App\Enums;

enum CouponType: string
{
    case Percentage = 'percentage';
    case Fixed = 'fixed';
  // Another example

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
    public function label(): string
    {
        return match ($this) {
            self::Fixed => 'Fixed Amount',
            self::Percentage => 'Percentage',
        };
    }
}
