@extends('layouts.app')

@section('content')
<style>
    .wavy-border {
        border-top: 2px solid #d1d5db;
        border-image: repeating-linear-gradient(
            90deg,
            transparent,
            transparent 10px,
            #d1d5db 10px,
            #d1d5db 20px
        ) 1;
    }
    @media print {
        body * {
            visibility: hidden;
        }
        #invoice, #invoice * {
            visibility: visible;
        }
        #invoice {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        body {
            background: white;
            padding: 0;
            margin: 0;
        }
        .no-print {
            display: none !important;
        }
        .bg-gray-50 {
            background: white !important;
        }
        .bg-white {
            background: white !important;
        }
        .shadow-lg {
            box-shadow: none !important;
        }
        @page {
            margin: 1cm;
            size: A4;
        }
    }
</style>
<div class="min-h-screen bg-gray-50 py-10">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            @php
                $currencyService = app(\App\Services\CountryCurrencyService::class);
                $currencyInfo = $currencyService->getCurrentCurrencyInfo();
                $displayCurrency = $currencyInfo['currency_code'];
                $displaySymbol = $currencyInfo['currency_symbol'];

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
                        // Reverse conversion: convert order currency -> USD
                        // We need to get the rate and reverse it
                        try {
                            $usdAmount = \Mgcodeur\CurrencyConverter\Facades\CurrencyConverter::convert($amount)
                                ->from($orderCurrency)
                                ->to('USD')
                                ->get();
                            return $usdAmount;
                        } catch (Exception $e) {
                            // Fallback: return original amount if conversion fails
                            return $amount;
                        }
                    }

                    // Convert between two non-USD currencies: order currency -> USD -> display currency
                    try {
                        // First convert to USD
                        $usdAmount = \Mgcodeur\CurrencyConverter\Facades\CurrencyConverter::convert($amount)
                            ->from($orderCurrency)
                            ->to('USD')
                            ->get();
                        // Then convert to display currency
                        return $currencyService->convertFromUSD($usdAmount, $displayCurrency);
                    } catch (Exception $e) {
                        // Fallback: return original amount if conversion fails
                        return $amount;
                    }
                };
            @endphp

            <!-- Thank You Message -->
            <div class="text-center mb-6 no-print">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Thank You for Your Order!</h1>
                <p class="text-lg text-gray-600">
                    Your order has been successfully placed. We've sent a confirmation email to <strong>{{ $order->email }}</strong>
                </p>
            </div>

            <!-- Print Button -->
            <div class="flex justify-end mb-6 no-print">
                <button onclick="printInvoice()"
                        type="button"
                        class="inline-flex items-center px-6 py-3 bg-gray-800 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print Invoice
                </button>
            </div>

            <script>
                function printInvoice() {
                    // Hide everything except the invoice using CSS
                    const style = document.createElement('style');
                    style.id = 'print-style';
                    style.textContent = `
                        @media print {
                            body * {
                                visibility: hidden;
                            }
                            #invoice, #invoice * {
                                visibility: visible;
                            }
                            #invoice {
                                position: absolute;
                                left: 0;
                                top: 0;
                                width: 100%;
                                background: white;
                            }
                            .no-print {
                                display: none !important;
                            }
                        }
                    `;
                    document.head.appendChild(style);

                    // Trigger print
                    window.print();

                    // Remove the style after printing
                    setTimeout(function() {
                        const printStyle = document.getElementById('print-style');
                        if (printStyle) {
                            printStyle.remove();
                        }
                    }, 1000);
                }
            </script>

            <!-- Invoice -->
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
                    @php
                        // Calculate total USD value of all items to get the proportion
                        $totalUsdValue = $order->items->sum(function($item) {
                            return $item->price * $item->quantity;
                        });
                        // Get stored subtotal (already in session currency)
                        $storedSubtotal = $order->subtotal ?? 0;
                    @endphp
                    
                    @foreach($order->items as $index => $item)
                        @php
                            $itemNumber = $index + 1;
                            $product = $item->product;
                            $variant = $item->variant;
                            
                            // Calculate this item's proportion of the total
                            $itemUsdTotal = $item->price * $item->quantity;
                            
                            // Calculate item amount proportionally from stored subtotal
                            // This ensures all items add up exactly to the subtotal
                            if ($totalUsdValue > 0) {
                                $itemTotal = ($itemUsdTotal / $totalUsdValue) * $storedSubtotal;
                                $itemPrice = $itemTotal / $item->quantity;
                            } else {
                                $itemTotal = 0;
                                $itemPrice = 0;
                            }
                            
                            // For original/compare price, use same ratio
                            $originalPrice = null;
                            if ($product->compare_price && $product->compare_price > $item->price) {
                                $originalPrice = ($product->compare_price / $item->price) * $itemPrice;
                            }
                            $discountPrice = $itemPrice;
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
                        // Order totals are already stored in the session currency - no conversion needed
                        $displaySubtotal = $order->subtotal ?? 0;
                        $displayShipping = $order->shipping_amount ?? 0;
                        $displayTax = $order->tax_amount ?? 0;
                        $displayDiscount = $order->discount_amount ?? 0;
                        $displayTotal = $order->total_amount ?? ($displaySubtotal + $displayShipping + $displayTax - $displayDiscount);
                    @endphp

                    <div class="flex justify-between">
                        <strong>SUBTOTAL:</strong>
                        <span>{{ number_format($displaySubtotal, 2) }}{{ $displaySymbol }}</span>
                    </div>
                    @if($displayShipping > 0)
                        <div class="flex justify-between">
                            <strong>SHIPPING:</strong>
                            <span>{{ number_format($displayShipping, 2) }}{{ $displaySymbol }}</span>
                        </div>
                    @endif
                    @if($displayTax > 0)
                        <div class="flex justify-between">
                            <strong>TAX:</strong>
                            <span>{{ number_format($displayTax, 2) }}{{ $displaySymbol }}</span>
                        </div>
                    @endif
                    @if($displayDiscount > 0)
                        <div class="flex justify-between">
                            <strong>DISC/CODE:</strong>
                            <span>-{{ number_format($displayDiscount, 2) }}{{ $displaySymbol }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-lg font-bold border-t-2 border-gray-300 pt-2 mt-2">
                        <strong>TOTAL:</strong>
                        <span>{{ number_format($displayTotal, 2) }}{{ $displaySymbol }}</span>
                    </div>
                </div>


                <!-- Payment Information -->
                <div class="border-t-2 border-gray-300 pt-4">
                    <div>
                        <strong>Payment Via:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center no-print">
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700 transition-colors">
                    Continue Shopping
                </a>

                @if($order->is_guest)
                    <a href="{{ route('checkout.confirmation', $order) }}?token={{ $order->guest_token }}"
                       class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        View Order Details
                    </a>
                @else
                    <a href="{{ route('checkout.confirmation', $order) }}"
                       class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        View Order Details
                    </a>
                @endif
            </div>

            @if($currencyInfo['currency_code'] !== 'USD')
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg no-print">
                <div class="text-sm text-blue-800 text-center">
                    @if($currencyInfo['is_auto_detected'])
                        Prices automatically converted to {{ $currencyInfo['currency_code'] }} ({{ $currencyInfo['currency_symbol'] }}) based on your location
                    @else
                        Prices converted to {{ $currencyInfo['currency_code'] }} ({{ $currencyInfo['currency_symbol'] }})
                    @endif
                </div>
            </div>
            @endif

            <!-- Additional Info -->
            <div class="mt-8 text-sm text-gray-500 no-print">
                <p>If you have any questions about your order, please contact our customer support.</p>
                <p class="mt-2">Email: support@workfit.com </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
@php
    // Facebook Pixel Purchase tracking
    // Order totals are already stored in the session currency - no conversion needed
    
    $fbPixelContentIds = [];
    $fbPixelContents = [];
    $fbPixelNumItems = 0;
    
    // Extract product IDs and details from order items
    if (isset($order) && $order->items && $order->items->isNotEmpty()) {
        $fbPixelContentIds = $order->items
            ->pluck('product_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();
        
        $fbPixelNumItems = $order->items->sum('quantity');

        // Calculate item prices proportionally to match invoice display
        // (Items are in USD, Subtotal is in Session Currency)
        $totalUsdValue = $order->items->sum(function($item) {
            return $item->price * $item->quantity;
        });
        $storedSubtotal = $order->subtotal ?? 0;

        foreach ($order->items as $item) {
            $itemUsdTotal = $item->price * $item->quantity;
            $itemPriceProps = 0;
            
            if ($totalUsdValue > 0) {
                 $itemTotalProps = ($itemUsdTotal / $totalUsdValue) * $storedSubtotal;
                 if ($item->quantity > 0) {
                     $itemPriceProps = $itemTotalProps / $item->quantity;
                 }
            }

            $fbPixelContents[] = [
                'id' => (string)$item->product_id,
                'quantity' => (int)$item->quantity,
                'item_price' => round($itemPriceProps, 2)
            ];
        }
    }
    
    // Use order's total directly - it's already in session currency
    // User indicated total_amount is the correct field
    $fbPixelValue = $order->total_amount ?? $order->total ?? 0;
    $fbPixelValue = round($fbPixelValue, 2);
    
    // Use order's currency (already stored at order time)
    $fbPixelCurrency = $order->currency ?? 'EGP';
    
    // Get order number for transaction ID
    $fbPixelTransactionId = $order->order_number ?? $order->id ?? '';
@endphp

fbq('track', 'Purchase', {
    content_ids: @json($fbPixelContentIds),
    contents: @json($fbPixelContents),
    content_type: 'product',
    num_items: {{ $fbPixelNumItems }},
    value: {{ $fbPixelValue }},
    currency: '{{ $fbPixelCurrency }}',
    transaction_id: '{{ $fbPixelTransactionId }}'
});


</script>
@endpush

