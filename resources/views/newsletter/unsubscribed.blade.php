{{-- resources/views/newsletter/unsubscribed.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto my-16 px-4">
        <div class="text-center">
            <div class="mb-4">
                <i class="fas fa-envelope-open text-gray-400 text-6xl"></i>
            </div>
            <h1 class="text-3xl font-semibold text-gray-900 mb-4">Unsubscribed Successfully</h1>
            <p class="text-lg text-gray-600 mb-6">We're sorry to see you go. You've been successfully unsubscribed from our newsletter.</p>
            <a href="{{ route('home') }}" class="bg-gray-950 text-white px-6 py-3 rounded-lg hover:bg-gray-100 hover:text-gray-950 transition-colors inline-block">
                Return to Home
            </a>
        </div>
    </div>
@endsection
    