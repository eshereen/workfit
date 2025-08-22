<?php

namespace App\Console\Commands;

use ReflectionClass;
use Exception;
use Illuminate\Console\Command;
use App\Services\CartService;
use App\Services\CountryCurrencyService;
use App\Services\PaymentService;
use App\Enums\PaymentMethod;
use Illuminate\Support\Facades\Log;

class DebugPaymentCalculation extends Command
{
    protected $signature = 'debug:payment-calculation {--user-id=}';
    protected $description = 'Debug payment calculation to identify amount discrepancies';

    public function handle()
    {
        $userId = $this->option('user-id');

        if (!$userId) {
            $this->error('Please provide a user ID with --user-id option');
            return 1;
        }

        $this->info("=== Payment Calculation Debug for User ID: {$userId} ===\n");

        try {
            // 1. Check Cart Service
            $this->info("1. CART SERVICE TOTALS:");
            $cartService = app(CartService::class);
            $cart = $cartService->getCart();

            if ($cart->isEmpty()) {
                $this->warn("Cart is empty!");
                return 1;
            }

            $this->info("   Cart Items Count: " . $cart->count());
            $this->info("   Subtotal (USD): $" . number_format($cartService->getSubtotal(), 2));
            $this->info("   Shipping (USD): $" . number_format($cartService->getShippingCost(), 2));
            $this->info("   Tax (USD): $" . number_format($cartService->getTaxAmount(), 2));
            $this->info("   Total (USD): $" . number_format($cartService->getTotal(), 2));

            // 2. Check Currency Conversion
            $this->info("\n2. CURRENCY CONVERSION (USD to EGP):");
            $currencyService = app(CountryCurrencyService::class);

            $subtotalUSD = $cartService->getSubtotal();
            $shippingUSD = $cartService->getShippingCost();
            $taxUSD = $cartService->getTaxAmount();
            $totalUSD = $cartService->getTotal();

            $subtotalEGP = $currencyService->convertFromUSD($subtotalUSD, 'EGP');
            $shippingEGP = $currencyService->convertFromUSD($shippingUSD, 'EGP');
            $taxEGP = $currencyService->convertFromUSD($taxUSD, 'EGP');
            $totalEGP = $currencyService->convertFromUSD($totalUSD, 'EGP');

            $this->info("   Subtotal: $" . number_format($subtotalUSD, 2) . " USD → " . number_format($subtotalEGP, 2) . " EGP");
            $this->info("   Shipping: $" . number_format($shippingUSD, 2) . " USD → " . number_format($shippingEGP, 2) . " EGP");
            $this->info("   Tax: $" . number_format($taxUSD, 2) . " USD → " . number_format($taxEGP, 2) . " EGP");
            $this->info("   Total: $" . number_format($totalUSD, 2) . " USD → " . number_format($totalEGP, 2) . " EGP");

                        // 3. Check Payment Service Conversion
            $this->info("\n3. PAYMENT SERVICE CONVERSION:");

            // Simulate order data
            $orderData = [
                'total_amount' => $totalEGP,
                'currency' => 'EGP'
            ];

            // Calculate amount minor manually (same logic as PaymentService)
            $amountMinor = (int) round($orderData['total_amount'] * 100);
            $this->info("   Order Total: " . number_format($orderData['total_amount'], 2) . " EGP");
            $this->info("   Amount Minor (cents): " . number_format($amountMinor));
            $this->info("   Amount Minor (piastres): " . number_format($amountMinor));
            $this->info("   Expected EGP: " . number_format($amountMinor / 100, 2));

            // 4. Check Exchange Rate
            $this->info("\n4. EXCHANGE RATE INFO:");
            // Use reflection to access protected method
            $reflection = new ReflectionClass($currencyService);
            $method = $reflection->getMethod('fetchExchangeRate');
            $method->setAccessible(true);
            $rate = $method->invoke($currencyService, 'EGP');
            $this->info("   USD to EGP Rate: " . ($rate ?: 'Failed to fetch'));

            // 5. Check Cart Items
            $this->info("\n5. CART ITEMS DETAILS:");
            foreach ($cart as $index => $item) {
                $this->info("   Item " . ($index + 1) . ":");
                $this->info("     Name: " . ($item['name'] ?? 'N/A'));
                $this->info("     Price (USD): $" . number_format($item['price'] ?? 0, 2));
                $this->info("     Price (EGP): " . number_format($currencyService->convertFromUSD($item['price'] ?? 0, 'EGP'), 2));
                $this->info("     Quantity: " . ($item['quantity'] ?? 0));
                $this->info("     Total (USD): $" . number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 0), 2));
                $this->info("     Total (EGP): " . number_format($currencyService->convertFromUSD(($item['price'] ?? 0) * ($item['quantity'] ?? 0), 'EGP'), 2));
                $this->info("");
            }

            $this->info("=== Debug Complete ===");
            return 0;

        } catch (Exception $e) {
            $this->error("Error during debug: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
    }
}
