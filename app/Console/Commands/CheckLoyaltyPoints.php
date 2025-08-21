<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\User;
use App\Models\LoyaltyTransaction;
use App\Services\LoyaltyService;

class CheckLoyaltyPoints extends Command
{
    protected $signature = 'loyalty:check {--user-id=} {--order-id=}';
    protected $description = 'Check loyalty points status and debug issues';

    public function handle()
    {
        $userId = $this->option('user-id');
        $orderId = $this->option('order-id');

        if ($userId) {
            $this->checkUserLoyalty($userId);
        } elseif ($orderId) {
            $this->checkOrderLoyalty($orderId);
        } else {
            $this->checkAllLoyalty();
        }

        return 0;
    }

    protected function checkUserLoyalty($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID {$userId} not found");
            return;
        }

        $this->info("=== Loyalty Status for User: {$user->name} ({$user->email}) ===");

        // Check current balance
        $balance = $user->loyaltyBalance();
        $this->info("Current Loyalty Balance: {$balance} points");

        // Check orders
        $orders = $user->orders()->latest()->get();
        $this->info("\n=== Orders ===");
        foreach ($orders as $order) {
            $amount = number_format($order->total_amount / 100, 2);
            $this->info("Order #{$order->order_number}: \${$amount} - Status: {$order->payment_status} - Created: {$order->created_at}");
        }

        // Check loyalty transactions
        $transactions = $user->loyaltyTransactions()->latest()->get();
        $this->info("\n=== Loyalty Transactions ===");
        foreach ($transactions as $transaction) {
            $this->info("ID: {$transaction->id} | Action: {$transaction->action} | Points: {$transaction->points} | Description: {$transaction->description} | Created: {$transaction->created_at}");
        }
    }

    protected function checkOrderLoyalty($orderId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            $this->error("Order with ID {$orderId} not found");
            return;
        }

        $this->info("=== Loyalty Status for Order #{$order->order_number} ===");
        $amount = number_format($order->total_amount / 100, 2);
        $this->info("Total Amount: \${$amount}");
        $this->info("Payment Status: {$order->payment_status}");
        $this->info("User: " . ($order->user ? $order->user->name : 'Guest'));

        if ($order->user) {
            $balance = $order->user->loyaltyBalance();
            $this->info("User Loyalty Balance: {$balance} points");

            // Check if points were awarded for this order
            $transaction = $order->user->loyaltyTransactions()
                ->where('source_type', Order::class)
                ->where('source_id', $order->id)
                ->first();

            if ($transaction) {
                $this->info("Loyalty Transaction: {$transaction->points} points awarded");
            } else {
                $this->info("No loyalty transaction found for this order");
            }
        }
    }

    protected function checkAllLoyalty()
    {
        $this->info("=== Overall Loyalty System Status ===");

        // Check total users with loyalty points
        $usersWithPoints = User::whereHas('loyaltyTransactions')->count();
        $this->info("Users with loyalty points: {$usersWithPoints}");

        // Check total loyalty transactions
        $totalTransactions = LoyaltyTransaction::count();
        $this->info("Total loyalty transactions: {$totalTransactions}");

        // Check orders by payment status
        $paidOrders = Order::where('payment_status', 'paid')->count();
        $pendingOrders = Order::where('payment_status', 'pending')->count();
        $this->info("Orders - Paid: {$paidOrders}, Pending: {$pendingOrders}");

        // Check recent loyalty transactions
        $recentTransactions = LoyaltyTransaction::with('user')->latest()->take(5)->get();
        $this->info("\n=== Recent Loyalty Transactions ===");
        foreach ($recentTransactions as $transaction) {
            $userName = $transaction->user ? $transaction->user->name : 'Unknown';
            $this->info("User: {$userName} | Action: {$transaction->action} | Points: {$transaction->points} | Created: {$transaction->created_at}");
        }
    }
}
