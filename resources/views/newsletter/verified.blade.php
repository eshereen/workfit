{{-- resources/views/newsletter/verified.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="px-4 mx-auto my-40 max-w-xl">
        <div class="text-center">
            <div class="mb-4">
                <i class="text-6xl text-green-500 fas fa-check-circle"></i>
            </div>
            <h1 class="mb-4 text-3xl font-semibold text-gray-900">Subscription Confirmed!</h1>
            <p class="mb-6 text-lg text-gray-600">Thank you for subscribing to WorkFit updates. You'll start receiving our latest news and exclusive offers.</p>
            <a href="{{ route('home') }}" class="inline-block px-6 py-3 text-white rounded-lg transition-colors bg-gray-950 hover:bg-gray-100 hover:text-gray-950">
                Return to Home
            </a>
        </div>
    </div>
@endsection
