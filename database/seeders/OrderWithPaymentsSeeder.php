<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderWithPaymentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating orders with payments and order items...');

        // Create orders with payments
        $this->createOrdersWithPayments();
        
        // Create some orders without payments (pending)
        $this->createPendingOrders();
        
        // Create some failed payment orders
        $this->createFailedPaymentOrders();

        $this->command->info('Orders with payments created successfully!');
    }

    /**
     * Create orders with successful payments
     */
    private function createOrdersWithPayments(): void
    {
        $this->command->info('Creating orders with successful payments...');

        // Get existing products and customers
        $products = Product::all();
        $customers = Customer::all();

        if ($products->isEmpty() || $customers->isEmpty()) {
            $this->command->warn('No products or customers found. Please run other seeders first.');
            return;
        }

        // Create 30 orders with successful payments
        for ($i = 0; $i < 30; $i++) {
            $customer = $customers->random();
            
            // Create order
            $order = Order::factory()
                ->create([
                    'customer_id' => $customer->id,
                    'user_id' => $customer->user_id,
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'email' => $customer->email,
                    'payment_status' => 'paid',
                    'status' => $this->getRandomOrderStatus(),
                ]);

            // Create order items (1-4 items per order)
            $itemCount = rand(1, 4);
            $selectedProducts = $products->random($itemCount);
            
            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);
                $price = $product->price * $quantity;
                
                OrderItem::factory()
                    ->forOrder($order)
                    ->forProduct($product)
                    ->create([
                        'quantity' => $quantity,
                        'price' => $price,
                    ]);
            }

            // Create payment for the order
            Payment::factory()
                ->forOrder($order)
                ->successful()
                ->create([
                    'status' => 'completed',
                ]);

            // Update order totals based on items
            $this->updateOrderTotals($order);
        }
    }

    /**
     * Create orders with pending payments
     */
    private function createPendingOrders(): void
    {
        $this->command->info('Creating orders with pending payments...');

        $customers = Customer::all();
        $products = Product::all();

        if ($customers->isEmpty() || $products->isEmpty()) {
            return;
        }

        // Create 15 orders with pending payments
        for ($i = 0; $i < 15; $i++) {
            $customer = $customers->random();
            
            $order = Order::factory()
                ->create([
                    'customer_id' => $customer->id,
                    'user_id' => $customer->user_id,
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'email' => $customer->email,
                    'payment_status' => 'pending',
                    'status' => 'pending',
                ]);

            // Create order items
            $itemCount = rand(1, 3);
            $selectedProducts = $products->random($itemCount);
            
            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 2);
                $price = $product->price * $quantity;
                
                OrderItem::factory()
                    ->forOrder($order)
                    ->forProduct($product)
                    ->create([
                        'quantity' => $quantity,
                        'price' => $price,
                    ]);
            }

            // Create pending payment
            Payment::factory()
                ->forOrder($order)
                ->pending()
                ->create([
                    'status' => 'pending',
                ]);

            // Update order totals
            $this->updateOrderTotals($order);
        }
    }

    /**
     * Create orders with failed payments
     */
    private function createFailedPaymentOrders(): void
    {
        $this->command->info('Creating orders with failed payments...');

        $customers = Customer::all();
        $products = Product::all();

        if ($customers->isEmpty() || $products->isEmpty()) {
            return;
        }

        // Create 10 orders with failed payments
        for ($i = 0; $i < 10; $i++) {
            $customer = $customers->random();
            
            $order = Order::factory()
                ->create([
                    'customer_id' => $customer->id,
                    'user_id' => $customer->user_id,
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'email' => $customer->email,
                    'payment_status' => 'failed',
                    'status' => 'pending',
                ]);

            // Create order items
            $itemCount = rand(1, 2);
            $selectedProducts = $products->random($itemCount);
            
            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 2);
                $price = $product->price * $quantity;
                
                OrderItem::factory()
                    ->forOrder($order)
                    ->forProduct($product)
                    ->create([
                        'quantity' => $quantity,
                        'price' => $price,
                    ]);
            }

            // Create failed payment
            Payment::factory()
                ->forOrder($order)
                ->failed()
                ->create([
                    'status' => 'failed',
                ]);

            // Update order totals
            $this->updateOrderTotals($order);
        }
    }

    /**
     * Update order totals based on order items
     */
    private function updateOrderTotals(Order $order): void
    {
        $items = $order->items;
        $subtotal = $items->sum('price');
        
        // Calculate tax (10%)
        $taxAmount = (int)($subtotal * 0.1);
        
        // Shipping amount (5-20 in cents)
        $shippingAmount = rand(500, 2000);
        
        // Discount amount (0-15% of subtotal)
        $discountAmount = rand(0, (int)($subtotal * 0.15));
        
        // Total amount
        $totalAmount = $subtotal + $taxAmount + $shippingAmount - $discountAmount;

        $order->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'shipping_amount' => $shippingAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
        ]);
    }

    /**
     * Get random order status for successful payments
     */
    private function getRandomOrderStatus(): string
    {
        $statuses = ['processing', 'shipped', 'delivered'];
        return $statuses[array_rand($statuses)];
    }
}
