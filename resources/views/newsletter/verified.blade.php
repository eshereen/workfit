{{-- resources/views/newsletter/verified.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto my-40 px-4">
        <div class="text-center">
            <div class="mb-4">
                <i class="fas fa-check-circle text-green-500 text-6xl"></i>
            </div>
            <h1 class="text-3xl font-semibold text-gray-900 mb-4">Subscription Confirmed!</h1>
            <p class="text-lg text-gray-600 mb-6">Thank you for subscribing to WorkFit updates. You'll start receiving our latest news and exclusive offers.</p>
            <a href="{{ route('home') }}" class="bg-gray-950 text-white px-6 py-3 rounded-lg hover:bg-gray-100 hover:text-gray-950 transition-colors inline-block">
                Return to Home
            </a>
        </div>
    </div>
@endsection
