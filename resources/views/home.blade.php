@extends('layouts.app')

@section('content')
    <!-- Hero Section with Video Background -->
    <section class="relative -top-28 left-0 right-0 h-screen overflow-hidden">
        <!-- Video Background -->
        <div class="video-container">
            <!-- Desktop Video (hidden on mobile) -->
            <video autoplay muted loop playsinline preload="metadata" class=" w-full h-full object-cover">
                <source src="videos/workfit-lg.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>

        </div>

        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/50"></div>

        <!-- Hero Content -->
        <div class="relative z-10 h-full flex items-center justify-center hero-content">
            <div class="text-center text-white px-4">
                <h1 class="text-4xl md:text-6xl font-bold mb-4 slide-in">WORKFIT</h1>
                <p class="text-xl md:text-2xl mb-8 max-w-2xl mx-auto fade-in">Premium activewear designed for performance and style</p>
                <div class="flex flex-col sm:flex-row justify-center gap-4 fade-in">
                    <a href="{{ route('categories.index', 'women') }}" class="w-2/3 mx-auto bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 transition-colors uppercase">
                        SHOP WOMEN
                    </a>
                    <a href="{{ route('categories.index', 'men') }}" class="w-2/3 mx-auto bg-white hover:bg-gray-100 text-black font-bold py-3 px-8 transition-colors">
                        SHOP MEN
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="px-4">
         <h1 class="text-center font-bold text-5xl mb-2">Just Arrived</h1>
         <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto animate-on-scroll">Discover our latest collection of products</p>
         @if($recent->isNotEmpty())
        @livewire('product-index',['products'=>$recent])
        @endif
    </section>

    <!-- Full-width Lifestyle Banner -->
    <section class="relative h-96 overflow-hidden animate-on-scroll">
        <img src="https://images.unsplash.com/photo-1607962837359-5e7e89f86776?q=80&w=1740&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
             loading="lazy"
             alt="Lifestyle Banner"
             class="w-full h-full object-cover">
             @if($collections->isNotEmpty())
        <!-- Overlay with only background dark -->
        <div class="absolute inset-0 bg-black/50 flex items-center justify-center z-0">
            <div class="relative z-40 text-center text-white px-4">
                <h2 class="text-3xl md:text-4xl font-bold mb-4 uppercase">{{ $collections->first()->name }} 'S COLLECTIONS</h2>
                <p class="text-xl mb-6 max-w-2xl mx-auto">
                    {{ $collections->first()->description }}
                </p>
                <a href="{{ route('collections.index') }}"
                   class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 transition-colors">
                    SHOP NOW
                </a>
            </div>
        </div>
        @endif
    </section>


    <!-- Product Grid - Kids's Collection -->
    <section class="py-16 px-4">
        @if($categories->isNotEmpty())
        <div class="container mx-auto">
            <h2 class="text-3xl font-bold text-center mb-4 animate-on-scroll uppercase">{{ $categories->first()->name }}'S COLLECTION</h2>

            <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto animate-on-scroll">{{ $categories->first()->description }}</p>

           @livewire('product-index',['products'=>$categories->first()->products->take(8)])



            <div class="text-center mt-12 animate-on-scroll">
                <a href="{{ route('categories.index', $categories->first()->slug) }}" class="border-2 border-red-600 hover:bg-red-600 hover:text-white font-bold py-3 px-8 transition-colors">
                    VIEW ALL {{ $categories->first()->name }}'S
                </a>
            </div>
        </div>
        @endif
    </section>

    <!-- Three Image Block Section -->
    <section class="py-16 px-4 bg-gray-100">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 h-screen">
                <!-- RUN Block -->
                <div class="relative overflow-hidden rounded-lg group animate-on-scroll">
                    <img src="https://images.unsplash.com/photo-1679216129631-fbcec034558c?q=70&w=600&auto=format&fit=crop"
                         width="600"
                         height="400"
                         loading="lazy"
                         alt="Run"
                         class="w-full h-full object-cover hover-zoom">
                    <div class="absolute inset-0 bg-black/50 flex flex-col items-center justify-center">
                        <h3 class="text-white text-3xl lg:text-6xl font-bold mb-2">RUN</h3>
                        <p class="text-white text-center px-4 mb-4">Lightweight gear for your daily runs</p>
                        <a href="{{ route('collections.index') }}" class="text-white font-medium underline hover:text-red-400 transition-colors">SHOP NOW</a>
                    </div>
                </div>

                <!-- TRAIN Block -->
                <div class="relative overflow-hidden rounded-lg group animate-on-scroll">
                    <img src="https://images.unsplash.com/photo-1646072508263-af94f0218bf0?q=70&w=600&auto=format&fit=crop"
                         width="600"
                         height="400"
                         loading="lazy"
                         alt="Train"
                         class="w-full h-full object-cover hover-zoom">
                    <div class="absolute inset-0 bg-black/50 flex flex-col items-center justify-center">
                        <h3 class="text-white text-3xl lg:text-6xl font-bold mb-2">TRAIN</h3>
                        <p class="text-white text-center px-4 mb-4">Durable apparel for intense workouts</p>
                        <a href="{{ route('collections.index') }}" class="text-white font-medium underline hover:text-red-400 transition-colors">SHOP NOW</a>
                    </div>
                </div>

                <!-- REC Block -->
                <div class="relative overflow-hidden rounded-lg group animate-on-scroll">
                    <img src="https://images.unsplash.com/photo-1715192383684-24c6614d2b54?q=70&w=600&auto=format&fit=crop"
                         width="600"
                         height="400"
                         loading="lazy"
                         alt="Rec"
                         class="w-full h-full object-cover hover-zoom">
                    <div class="absolute inset-0 bg-black/50 flex flex-col items-center justify-center">
                        <h3 class="text-white text-3xl lg:text-6xl font-bold mb-2">REC</h3>
                        <p class="text-white text-center px-4 mb-4">Comfortable styles for recovery days</p>
                        <a href="{{route('collections.index')}}" class="text-white font-medium underline hover:text-red-400 transition-colors">SHOP NOW</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Grid - Men's Collection -->

     <section class="py-16 px-4">
        @if($categories->isNotEmpty())
        <div class="container mx-auto">
            @if($categories->count() > 1)
            <h2 class="text-3xl font-bold text-center mb-4 animate-on-scroll uppercase">{{ $categories->get(1)->name }}'S COLLECTION</h2>

            <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto animate-on-scroll">{{ $categories->get(1)->description }}</p>


           @livewire('product-index',['products'=>$categories->get(1)->products->take(8)])



            <div class="text-center mt-12 animate-on-scroll">
                <a href="{{ route('categories.index', $categories->get(1)->slug) }}" class="border-2 border-red-600 hover:bg-red-600 hover:text-white font-bold py-3 px-8 transition-colors uppercase">
                    VIEW ALL {{ $categories->get(1)->name }}'S
                </a>
            </div>
            @endif
        </div>
        @endif
    </section>

    <!-- Full-width Lifestyle Banner 2 -->
    <section class="relative h-96 overflow-hidden animate-on-scroll">
        <img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438?q=70&w=800&auto=format&fit=crop"
             width="800"
             height="400"
             loading="lazy"
             alt="Lifestyle Banner"
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
            <div class="text-center text-white px-4">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">NEW ARRIVALS</h2>
                <p class="text-xl mb-6 max-w-2xl mx-auto">Be the first to shop our latest collection</p>
                <a href="{{ route('collections.index') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 transition-colors">
                    SHOP NOW
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-16 px-4">
        @if($featured->isNotEmpty())
        <div class="container mx-auto">
            <h2 class="text-3xl font-bold text-center mb-4 animate-on-scroll uppercase">Featured COLLECTION</h2>

            <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto animate-on-scroll">Our featured collection</p>
           @livewire('product-index',['products'=>$featured])
        </div>
        @endif
    </section>

@endsection
