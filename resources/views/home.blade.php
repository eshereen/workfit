@extends('layouts.app')

@section('content')
    <!-- Hero Section with Video Background -->
    <section class="relative -top-28 left-0 right-0 h-screen overflow-hidden">
        <!-- Video Background -->
        <div class="video-container">
            <video autoplay muted loop playsinline poster="poster.jpg" class="w-full h-full object-cover">
                <source src="videos/workfit.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>

        <!-- Overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>

        <!-- Hero Content -->
        <div class="relative z-10 h-full flex items-center justify-center hero-content">
            <div class="text-center text-white px-4">
                <h1 class="text-4xl md:text-6xl font-bold mb-4 slide-in">WORKFIT</h1>
                <p class="text-xl md:text-2xl mb-8 max-w-2xl mx-auto fade-in">Premium activewear designed for performance and style</p>
                <div class="flex flex-col sm:flex-row justify-center gap-4 fade-in">
                    <a href="{{ route('categories.index', 'women') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 transition-colors uppercase">
                        SHOP WOMEN
                    </a>
                    <a href="{{ route('categories.index', 'men') }}" class="bg-white hover:bg-gray-100 text-black font-bold py-3 px-8 transition-colors">
                        SHOP MEN
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Categories -->
    <section class="px-4">
         <h1 class="text-center  font-bold text-5xl mb-2">Just Arrived</h1>
         <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto animate-on-scroll">Discover our latest collection of products</p>
        @livewire('product-index')
    </section>

    <!-- Full-width Lifestyle Banner -->
    <section class="relative h-96 overflow-hidden animate-on-scroll">
        <img src="https://picsum.photos/seed/workfit-banner1/1920/400.jpg" alt="Lifestyle Banner" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center">
            <div class="text-center text-white px-4">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">SUMMER COLLECTION</h2>
                <p class="text-xl mb-6 max-w-2xl mx-auto">Stay cool and stylish with our latest summer essentials</p>
                <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 transition-colors">
                    SHOP NOW
                </button>
            </div>
        </div>
    </section>

    <!-- Product Grid - Women's Collection -->
    <section class="py-16 px-4">
        <div class="container mx-auto">
            <h2 class="text-3xl font-bold text-center mb-4 animate-on-scroll uppercase">{{ $kids->name }}'S COLLECTION</h2>

            <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto animate-on-scroll">{{ $kids->description }}</p>

           @livewire('product-index',['products'=>$kids->products->take(8)])



            <div class="text-center mt-12 animate-on-scroll">
                <button class="border-2 border-red-600 hover:bg-red-600 hover:text-white font-bold py-3 px-8 transition-colors">
                    VIEW ALL KID'S
                </button>
            </div>
        </div>
    </section>

    <!-- Three Image Block Section -->
    <section class="py-16 px-4 bg-gray-100">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 h-screen">
                <!-- RUN Block -->
                <div class="relative overflow-hidden rounded-lg group animate-on-scroll">
                    <img src="https://picsum.photos/seed/run/600/400.jpg" alt="Run" class="w-full h-full object-cover hover-zoom">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex flex-col items-center justify-center">
                        <h3 class="text-white text-3xl lg:text-6xl font-bold mb-2">RUN</h3>
                        <p class="text-white text-center px-4 mb-4">Lightweight gear for your daily runs</p>

                    </div>
                </div>

                <!-- TRAIN Block -->
                <div class="relative overflow-hidden rounded-lg group animate-on-scroll">
                    <img src="https://picsum.photos/seed/train/600/400.jpg" alt="Train" class="w-full h-full object-cover hover-zoom">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex flex-col items-center justify-center">
                        <h3 class="text-white text-3xl lg:text-6xl font-bold mb-2">TRAIN</h3>
                        <p class="text-white text-center px-4 mb-4">Durable apparel for intense workouts</p>

                    </div>
                </div>

                <!-- REC Block -->
                <div class="relative overflow-hidden rounded-lg group animate-on-scroll">
                    <img src="https://picsum.photos/seed/rec/600/400.jpg" alt="Rec" class="w-full h-full object-cover hover-zoom">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex flex-col items-center justify-center">
                        <h3 class="text-white text-3xl lg:text-6xl font-bold mb-2">REC</h3>
                        <p class="text-white text-center px-4 mb-4">Comfortable styles for recovery days</p>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Grid - Men's Collection -->

     <section class="py-16 px-4">
        <div class="container mx-auto">
            <h2 class="text-3xl font-bold text-center mb-4 animate-on-scroll uppercase">{{ $men->name }}'S COLLECTION</h2>

            <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto animate-on-scroll">{{ $men->description }}</p>

           @livewire('product-index',['products'=>$men->products->take(8)])



            <div class="text-center mt-12 animate-on-scroll">
                <button class="border-2 border-red-600 hover:bg-red-600 hover:text-white font-bold py-3 px-8 transition-colors uppercase">
                    VIEW ALL {{ $men->name }}'S
                </button>
            </div>
        </div>
    </section>

    <!-- Full-width Lifestyle Banner 2 -->
    <section class="relative h-96 overflow-hidden animate-on-scroll">
        <img src="https://picsum.photos/seed/workfit-banner2/1920/400.jpg" alt="Lifestyle Banner" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center">
            <div class="text-center text-white px-4">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">NEW ARRIVALS</h2>
                <p class="text-xl mb-6 max-w-2xl mx-auto">Be the first to shop our latest collection</p>
                <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 transition-colors">
                    SHOP NOW
                </button>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-16 px-4">
        <div class="container mx-auto">
            <h2 class="text-3xl font-bold text-center mb-4 animate-on-scroll uppercase">Featured COLLECTION</h2>

            <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto animate-on-scroll">{{ $men->description }}</p>
           @livewire('product-index',['products'=>$featured])
        </div>
    </section>

@endsection
