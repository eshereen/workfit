# Order Factories and Seeders

This document explains how to use the comprehensive order factories and seeders for testing and development purposes.

## Overview

The order system includes factories and seeders for:
- **Orders** - Main order records
- **Order Items** - Individual products in orders
- **Customers** - Customer information
- **Payments** - Payment records for orders

## Factories

### 1. OrderFactory

Creates realistic order data with various scenarios.

```php
// Basic usage
$order = Order::factory()->create();

// Create guest order
$guestOrder = Order::factory()->guest()->create();

// Create registered user order
$registeredOrder = Order::factory()->registered()->create();

// Create paid order
$paidOrder = Order::factory()->paid()->create();

// Create pending order
$pendingOrder = Order::factory()->pending()->create();

// Create delivered order
$deliveredOrder = Order::factory()->delivered()->create();
```

**Key Features:**
- Generates realistic order numbers (ORD-12345678)
- Creates both guest and registered user orders
- Generates realistic pricing (stored in cents)
- Creates proper address structures (strings, not JSON)
- Supports multiple currencies (USD, EUR, GBP)
- Generates various order and payment statuses

**Important Note:** Address fields are stored as strings, not JSON objects, matching the database schema.

### 2. OrderItemFactory

Creates order items with relationships to orders and products.

```php
// Basic usage
$orderItem = OrderItem::factory()->create();

// Create item for specific order
$orderItem = OrderItem::factory()->forOrder($order)->create();

// Create item for specific product
$orderItem = OrderItem::factory()->forProduct($product)->create();

// Create item with variant
$orderItem = OrderItem::factory()->withVariant($variant)->create();

// High quantity items
$orderItem = OrderItem::factory()->highQuantity()->create();

// Low quantity items
$orderItem = OrderItem::factory()->lowQuantity()->create();
```

### 3. CustomerFactory

Creates customer records with realistic data matching the database schema.

```php
// Basic usage
$customer = CustomerFactory::factory()->create();

// Registered user customer
$customer = CustomerFactory::factory()->registered()->create();

// Guest customer
$customer = CustomerFactory::factory()->guest()->create();

// Use billing address for shipping
$customer = CustomerFactory::factory()->useBillingForShipping()->create();
```

**Customer Fields (matching database schema):**
- `user_id`, `country_id`
- `email`, `first_name`, `last_name`, `phone_number`
- `billing_country_id`, `billing_state`, `billing_city`, `billing_address`, `billing_building_number`
- `shipping_country_id`, `shipping_state`, `shipping_city`, `shipping_address`, `shipping_building_number`
- `use_billing_for_shipping`

### 4. PaymentFactory

Creates payment records with provider-specific data.

```php
// Basic usage
$payment = Payment::factory()->create();

// Payment for specific order
$payment = Payment::factory()->forOrder($order)->create();

// Successful payment
$payment = Payment::factory()->successful()->create();

// Failed payment
$payment = Payment::factory()->failed()->create();

// Pending payment
$payment = Payment::factory()->pending()->create();
```

**Supported Payment Providers:**
- Stripe (with realistic payment intent IDs)
- PayPal (with realistic order IDs)
- Cash on Delivery
- Bank Transfer

## Seeders

### 1. OrderSeeder

Creates a variety of sample orders with different scenarios.

```bash
# Run the seeder
php artisan db:seed --class=OrderSeeder

# Or run all seeders
php artisan db:seed
```

**Creates:**
- 50 basic orders with 1-4 items each
- 10 guest orders
- 5 high-value orders ($500-$1000)
- 8 cancelled orders
- 15 delivered orders

### 2. OrderWithPaymentsSeeder

Creates orders with associated payments and realistic order items.

```bash
# Run the seeder
php artisan db:seed --class=OrderWithPaymentsSeeder
```

**Creates:**
- 30 orders with successful payments
- 15 orders with pending payments
- 10 orders with failed payments
- Automatically calculates order totals based on items

### 3. DatabaseSeeder

The main seeder that runs all seeders in the correct order.

```bash
php artisan db:seed
```

## Artisan Commands

### CreateTestOrders Command

Quick command to create test orders for development.

```bash
# Create 5 test orders (default)
php artisan orders:create-test

# Create specific number of orders
php artisan orders:create-test 10

# Create 1 test order
php artisan orders:create-test 1
```

## Usage Examples

### Creating a Complete Order with Items

```php
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

// Create order
$order = Order::factory()->create([
    'order_number' => 'CUSTOM-001',
    'status' => 'pending',
]);

// Create order items
$products = Product::inRandomOrder()->limit(3)->get();

foreach ($products as $product) {
    OrderItem::factory()
        ->forOrder($order)
        ->forProduct($product)
        ->create([
            'quantity' => rand(1, 3),
            'price' => $product->price * rand(1, 3),
        ]);
}
```

### Creating Orders for Specific Products

```php
use Database\Seeders\OrderSeeder;

$seeder = new OrderSeeder();
$seeder->createOrdersForProducts([1, 2, 3], 5); // 5 orders for products 1, 2, 3
```

### Creating Orders for Specific Customer

```php
use Database\Seeders\OrderSeeder;

$customer = Customer::find(1);
$seeder = new OrderSeeder();
$seeder->createOrdersForCustomer($customer, 3); // 3 orders for customer
```

## Data Structure

### Order Fields (matching database schema)
- `order_number` - Unique order identifier
- `user_id` - Associated user (nullable for guests)
- `customer_id` - Associated customer
- `guest_token` - Token for guest orders
- `first_name`, `last_name`, `email`, `phone_number`
- `country_id`, `state`, `city`
- `subtotal`, `tax_amount`, `shipping_amount`, `discount_amount`, `total_amount`
- `currency` - 3-letter currency code
- `billing_address`, `shipping_address` - String addresses (not JSON)
- `billing_building_number`, `shipping_building_number` - Building numbers
- `use_billing_for_shipping` - Boolean flag
- `payment_method`, `payment_status`, `status`
- `is_guest` - Boolean flag for guest orders
- `notes` - Optional text notes
- `coupon_id` - Optional coupon reference

### OrderItem Fields
- `order_id` - Associated order
- `product_id` - Associated product
- `product_variant_id` - Associated variant (optional)
- `quantity` - Item quantity
- `price` - Total price for this item (quantity × unit price)

### Payment Fields
- `order_id` - Associated order
- `provider` - Payment provider (stripe, paypal, etc.)
- `provider_reference` - Provider-specific reference
- `status` - Payment status
- `currency` - Payment currency
- `amount_minor` - Amount in smallest currency unit (cents)
- `meta` - Provider-specific metadata

## Best Practices

1. **Always run seeders in order**: Products → Customers → Orders
2. **Use factory states** for specific scenarios (guest, paid, etc.)
3. **Update order totals** after creating order items
4. **Check for required data** before creating orders
5. **Use realistic data ranges** for pricing and quantities
6. **Remember address fields are strings**, not JSON objects

## Troubleshooting

### Common Issues

1. **No products found**: Run product seeders first
2. **No customers found**: Run customer seeders first
3. **Foreign key constraints**: Ensure related models exist
4. **Price calculations**: Remember prices are stored in cents
5. **Address fields**: Addresses are stored as strings, not JSON

### Validation

```bash
# Check if factories work
php artisan tinker
>>> App\Models\Order::factory()->create()

# Test specific scenarios
>>> App\Models\Order::factory()->guest()->create()
>>> App\Models\Order::factory()->paid()->create()
```

## Customization

You can extend the factories by adding new states or methods:

```php
// In OrderFactory
public function highValue()
{
    return $this->state(function (array $attributes) {
        return [
            'subtotal' => rand(100000, 500000), // $1000-$5000
            'total_amount' => rand(110000, 550000),
        ];
    });
}

// Usage
$highValueOrder = Order::factory()->highValue()->create();
```

## Database Schema Compliance

All factories have been updated to match the actual database schema:

- **Orders table**: All fields match the migration exactly
- **Customers table**: All fields match the migration exactly  
- **Order items table**: All fields match the migration exactly
- **Payments table**: All fields match the migration exactly

The factories generate data that will work correctly with your existing database structure and model relationships.

This comprehensive system provides realistic order data for testing, development, and demonstration purposes.
