<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            padding: 20px;
            color: #000;
        }
        .wavy-border {
            border-top: 2px solid #d1d5db;
            border-image: repeating-linear-gradient(
                90deg,
                transparent,
                transparent 10px,
                #d1d5db 10px,
                #d1d5db 20px
            ) 1;
            margin: 20px 0;
        }
        .bg-white {
            background: white;
        }
        .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .p-8 {
            padding: 2rem;
        }
        .mb-8 {
            margin-bottom: 2rem;
        }
        .mb-6 {
            margin-bottom: 1.5rem;
        }
        .mb-4 {
            margin-bottom: 1rem;
        }
        .mb-2 {
            margin-bottom: 0.5rem;
        }
        .mb-1 {
            margin-bottom: 0.25rem;
        }
        .pb-4 {
            padding-bottom: 1rem;
        }
        .pt-4 {
            padding-top: 1rem;
        }
        .pt-2 {
            padding-top: 0.5rem;
        }
        .mt-2 {
            margin-top: 0.5rem;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-sm {
            font-size: 0.875rem;
        }
        .text-lg {
            font-size: 1.125rem;
        }
        .font-semibold {
            font-weight: 600;
        }
        .font-bold {
            font-weight: 700;
        }
        .grid {
            display: grid;
        }
        .grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .gap-8 {
            gap: 2rem;
        }
        .flex {
            display: flex;
        }
        .justify-between {
            justify-content: space-between;
        }
        .space-y-2 > * + * {
            margin-top: 0.5rem;
        }
        .space-y-1 > * + * {
            margin-top: 0.25rem;
        }
        .border-b {
            border-bottom-width: 1px;
        }
        .border-t-2 {
            border-top-width: 2px;
        }
        .border-gray-200 {
            border-color: #e5e7eb;
        }
        .border-gray-300 {
            border-color: #d1d5db;
        }
        .text-gray-700 {
            color: #374151;
        }
        .h-16 {
            height: 4rem;
        }
        .mx-auto {
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>
    <x-invoice :order="$order" :displayCurrency="$displayCurrency" :displaySymbol="$displaySymbol" />
</body>
</html>

