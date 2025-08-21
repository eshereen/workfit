@extends('layouts.app')

@vite(['resources/js/checkout.js'])


@section('content')

<div class="min-h-screen bg-gray-50 py-40">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
                <p class="text-gray-600 mt-2">Complete your order</p>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Checkout Form -->
                <div class="lg:col-span-2">
                    @if(Auth::check())
                        <!-- Authenticated User Checkout -->
                        <form action="{{ route('checkout.process') }}" method="POST" class="space-y-6">
                            @csrf
                            @include('checkout.partials.customer-info')
                            @livewire('checkout-form')
                            @include('checkout.partials.order-notes')

                            <div class="bg-white rounded-lg shadow-md p-6">
                                <button type="submit" class="w-full bg-red-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-red-700 transition-colors" onclick="updateFormBeforeSubmit()">
                                    Place Order
                                </button>
                            </div>
                        </form>
                    @else
                        <!-- Guest Checkout -->
                        <form action="{{ route('checkout.guest') }}" method="POST" class="space-y-6">
                            @csrf
                            @livewire('checkout-form')
                            @include('checkout.partials.order-notes')

                            <div class="bg-white rounded-lg shadow-md p-6">
                                <button type="submit" class="w-full bg-red-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-red-700 transition-colors" onclick="updateFormBeforeSubmit()">
                                    Place Order as Guest
                                </button>
                            </div>
                        </form>
                    @endif
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                        @livewire('order-summary')

                        <!-- Continue Shopping -->
                        <div class="mt-6">
                            <a href="{{ route('products.index') }}"
                               class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
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

@push('scripts')
<!-- Inline JavaScript removed - now handled by external checkout.js -->
@endpush


