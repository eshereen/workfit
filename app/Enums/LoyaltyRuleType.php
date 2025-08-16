<?php
// app/Enums/LoyaltyRuleType.php

namespace App\Enums;

enum LoyaltyRuleType: string
{
    case REDEMPTION = 'redemption_rule';
    case EARNING = 'earning_rule';
    case BIRTHDAY = 'birthday_rule'; // Example of additional type
    case SIGNUP = 'signup_bonus';    // Another example

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
