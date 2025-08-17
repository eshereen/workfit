<?php

namespace App\Services;
use App\Models\User;
use App\Models\LoyaltyTransaction;

class LoyaltyService
{

    public function addPoints(User $user, int $points, string $action, $source = null)
    {
        $rule = config("loyalty.rules.{$action}", []);

        return $user->loyaltyTransactions()->create([
            'points' => $points,
            'action' => $action,
            'description' => $rule['description'] ?? "Points earned from {$action}",
            'source_type' => $source ? get_class($source) : null,
            'source_id' => $source?->id,
        ]);
    }
    public function redeemPoints(User $user, int $points, string $description = 'Points redeemed', $source = null)
    {
        // Validate points are positive
        if ($points <= 0) {
            throw new \Exception('Points to redeem must be positive');
        }

        // Check if user has sufficient points
        if ($user->loyaltyBalance() < $points) {
            throw new \Exception('Insufficient points');
        }

        return $user->loyaltyTransactions()->create([
            'points' => -$points,
            'action' => 'redeem',
            'description' => $description,
            'source_type' => $source ? get_class($source) : null,
            'source_id' => $source?->id,
        ]);
    }
}
