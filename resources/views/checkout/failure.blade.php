@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <!-- Error Icon -->
            <div class="flex justify-center mb-6">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>

            <!-- Error Message -->
            <div class="text-center">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Payment Failed</h1>
                <p class="text-gray-600 mb-6">We're sorry, but your payment could not be processed at this time.</p>

                <!-- Order Details -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Order Details</h3>
                    <p class="text-sm text-gray-600">
                        <strong>Order Number:</strong> {{ $order->order_number }}<br>
                        <strong>Total Amount:</strong> {{ $order->currency_symbol }}{{ number_format($order->total, 2) }}<br>
                        <strong>Status:</strong> <span class="text-red-600 font-semibold">{{ ucfirst($order->status) }}</span>
                    </p>
                </div>

                <!-- Help Text -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-yellow-800 mb-2">What can you do?</h4>
                    <ul class="text-sm text-yellow-700 text-left space-y-1">
                        <li>• Check your payment method and try again</li>
                        <li>• Contact your bank if the issue persists</li>
                        <li>• Try a different payment method</li>
                        <li>• Contact our support team for assistance</li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <a href="{{ route('checkout.index') }}"
                       class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Try Again
                    </a>

                    <a href="{{ route('home') }}"
                       class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
