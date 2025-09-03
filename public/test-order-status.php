<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Order;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>OrderStatus Enum Test</h2>";

// Test 1: Create a new order with enum
echo "<h3>Test 1: Creating order with enums</h3>";
$order = new Order();
$order->order_number = 'TEST-002';
$order->first_name = 'Jane';
$order->last_name = 'Doe';
$order->email = 'jane@example.com';
$order->payment_status = PaymentStatus::PENDING;
$order->status = OrderStatus::PENDING;
$order->save();

echo "Order created with payment_status: " . $order->payment_status->value . "<br>";
echo "Order created with status: " . $order->status->value . "<br>";
echo "Payment status label: " . $order->payment_status->label() . "<br>";
echo "Order status label: " . $order->status->label() . "<br>";
echo "Payment status color: " . $order->payment_status->color() . "<br>";
echo "Order status color: " . $order->status->color() . "<br><br>";

// Test 2: Update order status
echo "<h3>Test 2: Updating order status</h3>";
$order->status = OrderStatus::CONFIRMED;
$order->save();

echo "Updated order status: " . $order->status->value . "<br>";
echo "Order status label: " . $order->status->label() . "<br>";
echo "Is active: " . ($order->status->isActive() ? 'Yes' : 'No') . "<br><br>";

// Test 3: Using helper methods
echo "<h3>Test 3: Using helper methods</h3>";
echo "isOrderActive(): " . ($order->isOrderActive() ? 'Yes' : 'No') . "<br>";
echo "isOrderCompleted(): " . ($order->isOrderCompleted() ? 'Yes' : 'No') . "<br>";
echo "isOrderCancelled(): " . ($order->isOrderCancelled() ? 'Yes' : 'No') . "<br>";
echo "canBeCancelled(): " . ($order->canBeCancelled() ? 'Yes' : 'No') . "<br>";
echo "canBeShipped(): " . ($order->canBeShipped() ? 'Yes' : 'No') . "<br>";
echo "canBeDelivered(): " . ($order->canBeDelivered() ? 'Yes' : 'No') . "<br>";
echo "getOrderStatusLabel(): " . $order->getOrderStatusLabel() . "<br>";
echo "getOrderStatusColor(): " . $order->getOrderStatusColor() . "<br><br>";

// Test 4: Using mark methods
echo "<h3>Test 4: Using mark methods</h3>";
$order->markAsProcessing();
echo "After markAsProcessing(): " . $order->status->label() . "<br>";

$order->markAsShipped();
echo "After markAsShipped(): " . $order->status->label() . "<br>";

$order->markAsDelivered();
echo "After markAsDelivered(): " . $order->status->label() . "<br><br>";

// Test 5: Database storage
echo "<h3>Test 5: Database storage</h3>";
$orderFromDB = Order::find($order->id);
echo "Retrieved from DB: " . $orderFromDB->status->value . "<br>";
echo "Type: " . get_class($orderFromDB->status) . "<br>";

// Test 6: All enum values
echo "<h3>Test 6: All OrderStatus values</h3>";
foreach (OrderStatus::cases() as $status) {
    echo $status->value . " => " . $status->label() . " (Color: " . $status->color() . ")<br>";
}

echo "<br><strong>Note:</strong> The database stores the string values ('pending', 'confirmed', etc.) but Laravel automatically casts them to/from the enum when you access the model.";
