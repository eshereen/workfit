@extends('layouts.app')

@vite(['resources/js/checkout.js'])


@section('content')

<div class="py-40 min-h-screen bg-gray-50">
    <div class="container px-4 mx-auto">
        <div class="mx-auto mx-w-5xl lg:max-w-7xl">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
                <p class="mt-2 text-gray-600">Complete your order</p>
            </div>

            @if(session('error'))
                <div class="p-4 mb-6 text-red-700 bg-red-100 rounded-lg border border-red-400">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Checkout Form -->
                <div class="lg:col-span-2">
                    <!-- Unified Checkout Form -->
                    <div class="space-y-6">
                        @livewire('checkout-form')

                    </div>
                </div>
                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="sticky top-4 p-6 bg-white rounded-lg shadow-md">
                        <!-- Loyalty Points Section -->
                        @livewire('checkout-loyalty-points')

                        @livewire('order-summary')
                        <!-- Continue Shopping -->
                        <div class="mt-6">
                            <a href="{{ route('products.index') }}"
                               class="inline-flex justify-center items-center px-4 py-2 w-full text-sm font-medium text-gray-700 bg-white rounded-md border border-gray-300 transition-colors hover:bg-gray-50">
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




