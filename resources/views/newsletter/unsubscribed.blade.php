{{-- resources/views/newsletter/unsubscribed.blade.php --}}
@extends('layouts.app')
<x-logo class="h-12" />
@section('content')
    <div class="px-4 mx-auto my-16 max-w-xl">
        <div class="text-center">
            <div class="mb-4">
                <i class="text-6xl text-gray-400 fas fa-envelope-open"></i>
            </div>
            <h1 class="mb-4 text-3xl font-semibold text-gray-900">Unsubscribed Successfully</h1>
            <p class="mb-6 text-lg text-gray-600">We're sorry to see you go. You've been successfully unsubscribed from our newsletter.</p>
            <a href="{{ route('home') }}" class="inline-block px-6 py-3 text-white rounded-lg transition-colors bg-gray-950 hover:bg-gray-100 hover:text-gray-950">
                Return to Home
            </a>
        </div>
    </div>
@endsection
