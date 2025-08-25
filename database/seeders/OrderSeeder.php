<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Customer;
use App\Models\User;
use App\Models\Country;
use App\Models\Coupon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have the required data
        $this->ensureRequiredData();

        // Create orders with different scenarios
        $this->createSampleOrders();
    }

    /**
     * Ensure we have the required data before creating orders
     */
    private function ensureRequiredData(): void
    {
        // Create countries if they don't exist
        if (Country::count() === 0) {
            Country::factory(5)->create();
        }

        // Create customers if they don't exist
        if (Customer::count() === 0) {
            Customer::factory(20)->create();
        }

        // Create users if they don't exist
        if (User::count() === 0) {
            User::factory(15)->create();
        }

        // Create products if they don't exist
        if (Product::count() === 0) {
            Product::factory(30)->create();
        }

        // Create coupons if they don't exist
        if (Coupon::count() === 0) {
            Coupon::factory(5)->create();
        }
    }

    /**
     * Create sample orders with different scenarios
     */
    private function createSampleOrders(): void
    {
        $this->command->info('Creating sample orders...');

        // Create 50 orders with various statuses
        $orders = Order::factory(50)
            ->has(OrderItem::factory()->count(rand(1, 4))) // 1-4 items per order
            ->create();

        // Create some specific scenario orders
        $this->createGuestOrders();
        $this->createHighValueOrders();
        $this->createCancelledOrders();
        $this->createDeliveredOrders();

        $this->command->info('Sample orders created successfully!');
    }

    /**
     * Create guest orders
     */
    private function createGuestOrders(): void
    {
        $this->command->info('Creating guest orders...');

        Order::factory(10)
            ->guest()
            ->has(OrderItem::factory()->count(rand(1, 3)))
            ->create();
    }

    /**
     * Create high value orders
     */
    private function createHighValueOrders(): void
    {
        $this->command->info('Creating high value orders...');

        Order::factory(5)
            ->has(OrderItem::factory()->highQuantity()->count(rand(2, 5)))
            ->create([
                'subtotal' => rand(50000, 100000), // $500-$1000
                'total_amount' => rand(55000, 110000),
            ]);
    }

    /**
     * Create cancelled orders
     */
    private function createCancelledOrders(): void
    {
        $this->command->info('Creating cancelled orders...');

        Order::factory(8)
            ->has(OrderItem::factory()->count(rand(1, 3)))
            ->create([
                'status' => 'cancelled',
                'payment_status' => 'refunded',
            ]);
    }

    /**
     * Create delivered orders
     */
    private function createDeliveredOrders(): void
    {
        $this->command->info('Creating delivered orders...');

        Order::factory(15)
            ->delivered()
            ->has(OrderItem::factory()->count(rand(1, 4)))
            ->create();
    }

    /**
     * Create orders with specific products
     */
    public function createOrdersForProducts(array $productIds, int $count = 10): void
    {
        $this->command->info("Creating {$count} orders for specific products...");

        for ($i = 0; $i < $count; $i++) {
            $order = Order::factory()->create();

            // Add 1-3 items from the specified products
            $itemCount = rand(1, 3);
            $selectedProductIds = collect($productIds)->random($itemCount);

            foreach ($selectedProductIds as $productId) {
                $product = Product::find($productId);
                if ($product) {
                    OrderItem::factory()
                        ->forOrder($order)
                        ->forProduct($product)
                        ->create();
                }
            }
        }
    }

    /**
     * Create orders for a specific customer
     */
    public function createOrdersForCustomer(Customer $customer, int $count = 5): void
    {
        $this->command->info("Creating {$count} orders for customer: {$customer->full_name}");

        Order::factory($count)
            ->has(OrderItem::factory()->count(rand(1, 4)))
            ->create([
                'customer_id' => $customer->id,
                'user_id' => $customer->user_id,
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'email' => $customer->email,
            ]);
    }

    /**
     * Create orders with specific status distribution
     */
    public function createOrdersWithStatusDistribution(): void
    {
        $this->command->info('Creating orders with realistic status distribution...');

        $statuses = [
            'pending' => 20,
            'processing' => 15,
            'shipped' => 25,
            'delivered' => 30,
            'cancelled' => 10,
        ];

        foreach ($statuses as $status => $count) {
            Order::factory($count)
                ->has(OrderItem::factory()->count(rand(1, 4)))
                ->create([
                    'status' => $status,
                    'payment_status' => $this->getPaymentStatusForOrderStatus($status),
                ]);
        }
    }

    /**
     * Get appropriate payment status for order status
     */
    private function getPaymentStatusForOrderStatus(string $orderStatus): string
    {
        return match ($orderStatus) {
            'pending' => 'pending',
            'processing' => 'paid',
            'shipped' => 'paid',
            'delivered' => 'paid',
            'cancelled' => 'refunded',
            default => 'pending',
        };
    }
}
