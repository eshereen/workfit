<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use App\Models\Order;
use App\Events\OrderPlaced;
use App\Services\LoyaltyService;

class RetroactiveLoyaltyPoints extends Command
{
    protected $signature = 'loyalty:retroactive {--dry-run}';
    protected $description = 'Award loyalty points retroactively for existing paid orders';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
        }

        // Find all paid orders with registered users that don't have loyalty transactions
        $orders = Order::where('payment_status', 'paid')
            ->whereNotNull('user_id')
            ->whereDoesntHave('user.loyaltyTransactions', function($query) {
                $query->where('source_type', Order::class);
            })
            ->with('user')
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No orders found that need retroactive loyalty points');
            return 0;
        }

        $this->info("Found {$orders->count()} orders that need retroactive loyalty points");

        $totalPoints = 0;
        $processedOrders = 0;

        foreach ($orders as $order) {
            if (!$order->user) {
                $this->warn("Order #{$order->order_number} has no user, skipping");
                continue;
            }

            // Calculate points based on dollar amount
            $dollarAmount = $order->total_amount / 100;
            $points = $dollarAmount * config('loyalty.rules.purchase.points_per_dollar', 1);
            $points = (int) $points;

            $this->info("Order #{$order->order_number}: \${$dollarAmount} = {$points} points for user {$order->user->name}");

            if (!$dryRun) {
                try {
                    $loyaltyService = app(LoyaltyService::class);
                    $transaction = $loyaltyService->addPoints(
                        $order->user,
                        $points,
                        'purchase',
                        $order
                    );

                    $this->info("  ✅ Points awarded successfully (Transaction ID: {$transaction->id})");
                    $processedOrders++;
                    $totalPoints += $points;
                } catch (Exception $e) {
                    $this->error("  ❌ Failed to award points: " . $e->getMessage());
                }
            } else {
                $processedOrders++;
                $totalPoints += $points;
            }
        }

        if ($dryRun) {
            $this->info("\nDRY RUN SUMMARY:");
            $this->info("Would process {$processedOrders} orders");
            $this->info("Would award {$totalPoints} total points");
        } else {
            $this->info("\nRETROACTIVE LOYALTY POINTS COMPLETED:");
            $this->info("Processed {$processedOrders} orders");
            $this->info("Awarded {$totalPoints} total points");
        }

        return 0;
    }
}
