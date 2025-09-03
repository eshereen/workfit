<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Order;
use App\Enums\PaymentStatus;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>PaymentStatus Enum Test</h2>";

// Test 1: Create a new order with enum
echo "<h3>Test 1: Creating order with enum</h3>";
$order = new Order();
$order->order_number = 'TEST-001';
$order->first_name = 'John';
$order->last_name = 'Doe';
$order->email = 'john@example.com';
$order->payment_status = PaymentStatus::PENDING; // Using enum
$order->save();

echo "Order created with payment_status: " . $order->payment_status->value . "<br>";
echo "Payment status label: " . $order->payment_status->label() . "<br>";
echo "Payment status color: " . $order->payment_status->color() . "<br>";
echo "Is successful: " . ($order->payment_status->isSuccessful() ? 'Yes' : 'No') . "<br><br>";

// Test 2: Update payment status
echo "<h3>Test 2: Updating payment status</h3>";
$order->payment_status = PaymentStatus::PROCESSED;
$order->save();

echo "Updated payment_status: " . $order->payment_status->value . "<br>";
echo "Payment status label: " . $order->payment_status->label() . "<br>";
echo "Is successful: " . ($order->payment_status->isSuccessful() ? 'Yes' : 'No') . "<br><br>";

// Test 3: Using helper methods
echo "<h3>Test 3: Using helper methods</h3>";
echo "isPaymentSuccessful(): " . ($order->isPaymentSuccessful() ? 'Yes' : 'No') . "<br>";
echo "isPaymentPending(): " . ($order->isPaymentPending() ? 'Yes' : 'No') . "<br>";
echo "isPaymentFailed(): " . ($order->isPaymentFailed() ? 'Yes' : 'No') . "<br>";
echo "getPaymentStatusLabel(): " . $order->getPaymentStatusLabel() . "<br>";
echo "getPaymentStatusColor(): " . $order->getPaymentStatusColor() . "<br><br>";

// Test 4: Using mark methods
echo "<h3>Test 4: Using mark methods</h3>";
$order->markAsConfirmed();
echo "After markAsConfirmed(): " . $order->payment_status->label() . "<br>";

$order->markAsCompleted();
echo "After markAsCompleted(): " . $order->payment_status->label() . "<br><br>";

// Test 5: Database storage
echo "<h3>Test 5: Database storage</h3>";
$orderFromDB = Order::find($order->id);
echo "Retrieved from DB: " . $orderFromDB->payment_status->value . "<br>";
echo "Type: " . get_class($orderFromDB->payment_status) . "<br>";

// Test 6: All enum values
echo "<h3>Test 6: All PaymentStatus values</h3>";
foreach (PaymentStatus::cases() as $status) {
    echo $status->value . " => " . $status->label() . " (Color: " . $status->color() . ")<br>";
}

echo "<br><strong>Note:</strong> The database stores the string values ('pending', 'processed', etc.) but Laravel automatically casts them to/from the enum when you access the model.";
