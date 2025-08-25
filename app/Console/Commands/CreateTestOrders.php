<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Console\Command;

class CreateTestOrders extends Command
{
    protected $signature = 'orders:create-test {count=5 : Number of test orders to create}';
    protected $description = 'Create test orders using factories for testing purposes';

    public function handle()
    {
        $count = (int) $this->argument('count');

        $this->info("Creating {$count} test orders...");

        if (Product::count() === 0) {
            $this->error('No products found. Please run product seeders first.');
            return 1;
        }

        if (Customer::count() === 0) {
            $this->error('No customers found. Please run customer seeders first.');
            return 1;
        }

        for ($i = 0; $i < $count; $i++) {
            $this->createTestOrder($i + 1);
        }

        $this->info("Successfully created {$count} test orders!");
        return 0;
    }

    private function createTestOrder(int $orderNumber): void
    {
        $this->info("Creating order #{$orderNumber}...");

        $order = Order::factory()->create([
            'order_number' => 'TEST-' . str_pad($orderNumber, 6, '0', STR_PAD_LEFT),
        ]);

        $itemCount = rand(1, 3);
        $products = Product::inRandomOrder()->limit($itemCount)->get();

        foreach ($products as $product) {
            $quantity = rand(1, 3);
            $price = $product->price * $quantity;

            OrderItem::factory()
                ->forOrder($order)
                ->forProduct($product)
                ->create([
                    'quantity' => $quantity,
                    'price' => $price,
                ]);

            $this->line("  - Added {$quantity}x {$product->name} ($" . number_format($price/100, 2) . ")");
        }

        $this->updateOrderTotals($order);

        $this->info("  Order total: $" . number_format($order->total_amount / 100, 2));
    }

    private function updateOrderTotals(Order $order): void
    {
        $items = $order->items;
        $subtotal = $items->sum('price');

        $taxAmount = (int)($subtotal * 0.1);
        $shippingAmount = rand(500, 2000);
        $discountAmount = rand(0, (int)($subtotal * 0.15));
        $totalAmount = $subtotal + $taxAmount + $shippingAmount - $discountAmount;

        $order->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'shipping_amount' => $shippingAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
        ]);
    }
}
