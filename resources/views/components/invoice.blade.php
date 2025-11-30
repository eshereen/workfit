@props(['order', 'displayCurrency' => null, 'displaySymbol' => null])

@php
    $currencyService = app(\App\Services\CountryCurrencyService::class);

    // If currency not provided, use order's currency
    if (!$displayCurrency) {
        $displayCurrency = $order->currency ?? 'USD';
    }

    if (!$displaySymbol) {
        $currencyInfo = $currencyService->getCurrentCurrencyInfo();
        $displaySymbol = $currencyInfo['currency_symbol'];
    }

    // Order amounts are already stored in the order's currency
    // Only convert if the display currency differs from the order's currency
    $orderCurrency = $order->currency ?? 'USD';

    // If currencies match, use values as-is
    // Otherwise, convert from order currency -> USD -> display currency
    $convertAmount = function ($amount) use ($currencyService, $displayCurrency, $orderCurrency) {
        // If currencies match, return as-is
        if ($orderCurrency === $displayCurrency) {
            return $amount;
        }

        // If order currency is USD, convert directly
        if ($orderCurrency === 'USD') {
            if ($displayCurrency === 'USD') {
                return $amount;
            }
            return $currencyService->convertFromUSD($amount, $displayCurrency);
        }

        // If display currency is USD, convert from order currency to USD
        if ($displayCurrency === 'USD') {
            try {
                $usdAmount = \Mgcodeur\CurrencyConverter\Facades\CurrencyConverter::convert($amount)
                    ->from($orderCurrency)
                    ->to('USD')
                    ->get();
                return $usdAmount;
            } catch (Exception $e) {
                return $amount;
            }
        }

        // Convert between two non-USD currencies: order currency -> USD -> display currency
        try {
            $usdAmount = \Mgcodeur\CurrencyConverter\Facades\CurrencyConverter::convert($amount)
                ->from($orderCurrency)
                ->to('USD')
                ->get();
            return $currencyService->convertFromUSD($usdAmount, $displayCurrency);
        } catch (Exception $e) {
            return $amount;
        }
    };
@endphp

<div class="bg-white shadow-lg p-8 mb-8" id="invoice">
    <!-- Header Section with Logo and Company Info -->
    <div class="text-center mb-8">
        <div class="mb-4">
            <img src="{{ asset('imgs/workfit_logo_black.png') }}" alt="WorkFit Logo" class="h-16 mx-auto">
        </div>
        <div class="space-y-1 text-sm">
            <div><strong>AD:</strong> 8 Gesr Al Suez, Heliopolis Cairo.</div>
            <div><strong>TEL:</strong> +201148438466</div>
            <div><strong>MAIL:</strong> info@workfiteg.com</div>
        </div>
    </div>

    <!-- Decorative Line -->
    <div class="wavy-border mb-6"></div>

    <!-- Invoice Details and Customer Info -->
    <div class="grid grid-cols-2 gap-8 mb-6">
        <!-- Left: Invoice Details -->
        <div>
            <div class="mb-2">
                <strong>INVOICE NO:</strong> {{ $order->order_number }}
            </div>
            <div>
                <strong>DATE:</strong> {{ $order->created_at->format('d/m/Y') }}
            </div>
        </div>

        <!-- Right: Customer Details -->
        <div>
            <div class="mb-1"><strong>Customer Name:</strong> {{ $order->first_name }} {{ $order->last_name }}</div>
            <div class="mb-1"><strong>Phone Number:</strong> {{ $order->phone_number ?? 'N/A' }}</div>
            <div class="mb-1"><strong>Address:</strong> {{ $order->billing_address }}{{ $order->billing_building_number ? ', ' . $order->billing_building_number : '' }}</div>
            <div class="mb-1"><strong>Country:</strong> {{ $order->country->name ?? 'N/A' }}</div>
            <div><strong>Email:</strong> {{ $order->email }}</div>
        </div>
    </div>

    <!-- Decorative Line -->
    <div class="wavy-border mb-6"></div>

    <!-- Itemized List -->
    <div class="mb-6">
        @foreach($order->items as $index => $item)
            @php
                $itemNumber = $index + 1;
                $product = $item->product;
                $variant = $item->variant;
                // Order item prices are stored in USD, so convert from USD to display currency
                $itemPrice = $currencyService->convertFromUSD($item->price, $displayCurrency);
                $originalPrice = $product->compare_price ? $currencyService->convertFromUSD($product->compare_price, $displayCurrency) : null;
                $discountPrice = $itemPrice;
                $itemTotal = $itemPrice * $item->quantity;
            @endphp
            <div class="mb-4 pb-4 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                <div class="font-semibold mb-1">
                    {{ $itemNumber }}. {{ strtoupper($product->name ?? 'Product') }}
                </div>
                <div class="text-sm text-gray-700 mb-1">
                    SIZE {{ $variant->size ?? 'N/A' }} . {{ $variant->sku ?? 'N/A' }}
                </div>
                <div class="text-sm text-gray-700 mb-1">
                    @if($originalPrice && $originalPrice > $itemPrice)
                        {{ number_format($originalPrice, 2) }}{{ $displaySymbol }}*{{ $item->quantity }} DISC {{ number_format($discountPrice, 2) }}{{ $displaySymbol }}
                    @else
                        {{ number_format($itemPrice, 2) }}{{ $displaySymbol }}*{{ $item->quantity }}
                    @endif
                </div>
                <div class="text-right font-semibold">
                    Amount: {{ number_format($itemTotal, 2) }}{{ $displaySymbol }}
                </div>
            </div>
        @endforeach
    </div>

    <!-- Decorative Line -->
    <div class="wavy-border mb-6"></div>

    <!-- Summary Section -->
    <div class="space-y-2 mb-6">
        @php
            $convertedSubtotal = $convertAmount($order->subtotal);
            $convertedShipping = $convertAmount($order->shipping_amount);
            $convertedTax = $convertAmount($order->tax_amount);
            $convertedDiscount = $convertAmount($order->discount_amount);

            // Recalculate total to ensure it's correct: subtotal + shipping + tax - discount
            $calculatedTotal = $convertedSubtotal + $convertedShipping + $convertedTax - $convertedDiscount;
            $calculatedTotal = max(0, $calculatedTotal); // Prevent negative totals

            // Use calculated total (more reliable than stored value)
            $convertedTotal = $calculatedTotal;
        @endphp
        <div class="flex justify-between">
            <strong>SUBTOTAL:</strong>
            <span>{{ number_format($convertedSubtotal, 2) }}{{ $displaySymbol }}</span>
        </div>
        @if($convertedShipping > 0)
            <div class="flex justify-between">
                <strong>SHIPPING:</strong>
                <span>{{ number_format($convertedShipping, 2) }}{{ $displaySymbol }}</span>
            </div>
        @endif
        @if($convertedTax > 0)
            <div class="flex justify-between">
                <strong>TAX:</strong>
                <span>{{ number_format($convertedTax, 2) }}{{ $displaySymbol }}</span>
            </div>
        @endif
        @if($convertedDiscount > 0)
            <div class="flex justify-between">
                <strong>DISC/CODE:</strong>
                <span>-{{ number_format($convertedDiscount, 2) }}{{ $displaySymbol }}</span>
            </div>
        @endif
        <div class="flex justify-between text-lg font-bold border-t-2 border-gray-300 pt-2 mt-2">
            <strong>TOTAL:</strong>
            <span>{{ number_format($convertedTotal, 2) }}{{ $displaySymbol }}</span>
        </div>
    </div>

    <!-- Payment Information -->
    <div class="border-t-2 border-gray-300 pt-4">
        <div>
            <strong>Payment Via:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
        </div>
    </div>
</div>

