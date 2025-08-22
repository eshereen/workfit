<?php

namespace App\Services;
use Exception;
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
            throw new Exception('Points to redeem must be positive');
        }

        // Check if user has sufficient points
        if ($user->loyaltyBalance() < $points) {
            throw new Exception('Insufficient points');
        }

        return $user->loyaltyTransactions()->create([
            'points' => -$points,
            'action' => 'redeem',
            'description' => $description,
            'source_type' => $source ? get_class($source) : null,
            'source_id' => $source?->id,
        ]);
    }

    public function calculateRedemptionValue(int $points): float
    {
        $ratio = config('loyalty.redemption.ratio', 100);
        return $points / $ratio; // Returns dollar value
    }

    public function canRedeemPoints(User $user, int $points): bool
    {
        $minimum = config('loyalty.redemption.minimum_redemption', 100);
        return $points >= $minimum && $user->loyaltyBalance() >= $points;
    }

    public function redeemPointsForDiscount(User $user, int $points, $source = null, string $description = null)
    {
        if (!$this->canRedeemPoints($user, $points)) {
            throw new Exception('Cannot redeem points. Check minimum requirement and balance.');
        }

        $dollarValue = $this->calculateRedemptionValue($points);

        // Use custom description if provided, otherwise use default
        $defaultDescription = "Redeemed {$points} points for \${$dollarValue} discount";
        $finalDescription = $description ?? $defaultDescription;

        return $this->redeemPoints(
            $user,
            $points,
            $finalDescription,
            $source
        );
    }
}
