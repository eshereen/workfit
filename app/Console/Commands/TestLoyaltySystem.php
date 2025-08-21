<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Order;
use App\Services\LoyaltyService;

class TestLoyaltySystem extends Command
{
    protected $signature = 'loyalty:test {--user-id=}';
    protected $description = 'Test the loyalty system functionality';

    public function handle()
    {
        $userId = $this->option('user-id');

        if (!$userId) {
            $this->error('Please provide a user ID with --user-id option');
            return 1;
        }

        $user = User::find($userId);
        if (!$user) {
            $this->error('User not found');
            return 1;
        }

        $this->info("Testing loyalty system for user: {$user->name} ({$user->email})");

        // Check current balance
        $currentBalance = $user->loyaltyBalance();
        $this->info("Current loyalty balance: {$currentBalance} points");

        // Test adding points
        $loyaltyService = app(LoyaltyService::class);

        $this->info("\n--- Testing Point Addition ---");
        $pointsToAdd = 50;
        $transaction = $loyaltyService->addPoints($user, $pointsToAdd, 'test', null);
        $this->info("Added {$pointsToAdd} points. Transaction ID: {$transaction->id}");

        $newBalance = $user->loyaltyBalance();
        $this->info("New balance: {$newBalance} points");

        // Test redemption calculation
        $this->info("\n--- Testing Redemption Calculation ---");
        $testPoints = 100;
        $dollarValue = $loyaltyService->calculateRedemptionValue($testPoints);
        $this->info("{$testPoints} points = \${$dollarValue}");

        // Test if can redeem
        $canRedeem = $loyaltyService->canRedeemPoints($user, $testPoints);
        $this->info("Can redeem {$testPoints} points: " . ($canRedeem ? 'Yes' : 'No'));

        // Test redemption
        if ($canRedeem) {
            $this->info("\n--- Testing Point Redemption ---");
            $redemptionTransaction = $loyaltyService->redeemPointsForDiscount($user, $testPoints, null, 'Test redemption');
            $this->info("Redeemed {$testPoints} points. Transaction ID: {$redemptionTransaction->id}");

            $finalBalance = $user->loyaltyBalance();
            $this->info("Final balance: {$finalBalance} points");
        }

        // Show transaction history
        $this->info("\n--- Recent Transactions ---");
        $transactions = $user->loyaltyTransactions()->latest()->take(5)->get();
        foreach ($transactions as $transaction) {
            $this->info("ID: {$transaction->id} | Action: {$transaction->action} | Points: {$transaction->points} | Description: {$transaction->description}");
        }

        $this->info("\nLoyalty system test completed successfully!");
        return 0;
    }
}
