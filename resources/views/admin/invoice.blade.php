<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->order_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                background: white;
            }
            .no-print {
                display: none !important;
            }
            @page {
                margin: 1cm;
                size: A4;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <!-- Print Button -->
                <div class="flex justify-end mb-6 no-print">
                    <button onclick="printInvoice()"
                            type="button"
                            class="inline-flex items-center px-6 py-3 bg-gray-800 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors shadow-md mr-2">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print Invoice
                    </button>
                    <a href="{{ route('admin.invoice.pdf', $order) }}"
                       class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export PDF
                    </a>
                </div>

                @php
                    $currencyService = app(\App\Services\CountryCurrencyService::class);
                    $currencyInfo = $currencyService->getCurrentCurrencyInfo();
                @endphp

                <x-invoice :order="$order" :displayCurrency="$order->currency ?? 'USD'" :displaySymbol="$currencyInfo['currency_symbol']" />
            </div>
        </div>
    </div>

    <script>
        function printInvoice() {
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
            window.print();
            setTimeout(function() {
                const printStyle = document.getElementById('print-style');
                if (printStyle) {
                    printStyle.remove();
                }
            }, 1000);
        }
    </script>
</body>
</html>

