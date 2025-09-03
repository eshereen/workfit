<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Order;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>Fixing Payment Status</h2>";

// Check for orders with 'paid' status
$paidOrders = DB::table('orders')->where('payment_status', 'paid')->get();

echo "<h3>Found " . $paidOrders->count() . " orders with 'paid' status</h3>";

if ($paidOrders->count() > 0) {
    echo "<p>Updating orders to use PaymentStatus::PAID enum...</p>";
    
    // Update orders to use the enum value
    DB::table('orders')->where('payment_status', 'paid')->update([
        'payment_status' => PaymentStatus::PAID->value
    ]);
    
    echo "<p>✅ Successfully updated " . $paidOrders->count() . " orders</p>";
} else {
    echo "<p>No orders with 'paid' status found.</p>";
}

// Check for any other invalid payment statuses
echo "<h3>Checking for other invalid payment statuses...</h3>";

$validStatuses = array_map(fn($case) => $case->value, PaymentStatus::cases());
$allOrders = DB::table('orders')->select('id', 'payment_status')->get();

$invalidOrders = $allOrders->filter(function($order) use ($validStatuses) {
    return !in_array($order->payment_status, $validStatuses) && $order->payment_status !== null;
});

if ($invalidOrders->count() > 0) {
    echo "<p>⚠️ Found " . $invalidOrders->count() . " orders with invalid payment status:</p>";
    echo "<ul>";
    foreach ($invalidOrders as $order) {
        echo "<li>Order ID: " . $order->id . " - Status: '" . $order->payment_status . "'</li>";
    }
    echo "</ul>";
    
    // Set invalid statuses to PENDING
    $invalidStatuses = $invalidOrders->pluck('payment_status')->unique();
    foreach ($invalidStatuses as $status) {
        DB::table('orders')->where('payment_status', $status)->update([
            'payment_status' => PaymentStatus::PENDING->value
        ]);
    }
    
    echo "<p>✅ Set invalid statuses to PENDING</p>";
} else {
    echo "<p>✅ All orders have valid payment statuses</p>";
}

// Test the enum
echo "<h3>Testing PaymentStatus enum...</h3>";

$testOrder = Order::first();
if ($testOrder) {
    echo "<p>Sample order payment status: " . $testOrder->payment_status->label() . "</p>";
    echo "<p>Status value: " . $testOrder->payment_status->value . "</p>";
    echo "<p>Is successful: " . ($testOrder->isPaymentSuccessful() ? 'Yes' : 'No') . "</p>";
} else {
    echo "<p>No orders found to test.</p>";
}

echo "<h3>Available Payment Statuses:</h3>";
echo "<ul>";
foreach (PaymentStatus::cases() as $status) {
    echo "<li><strong>" . $status->value . "</strong> => " . $status->label() . " (Color: " . $status->color() . ")</li>";
}
echo "</ul>";

echo "<p><strong>✅ Payment status fix completed!</strong></p>";
