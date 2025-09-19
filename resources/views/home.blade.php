@extends('layouts.app')

@section('content')
    {{-- Promotional Modal --}}
@livewire('promo-modal')
    <!-- Hero Section with Video Background -->
    <section class="relative -top-28 h-screen overflow-hidden">
        <!-- Video Background -->
        <video autoplay muted loop playsinline preload="metadata" poster="/imgs/group.jpg" class="w-full h-full object-cover hidden md:block">
            <source src="videos/workfit-lg.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <!-- Mobile Video -->
        <video autoplay muted loop playsinline preload="metadata" poster="/imgs/group.jpg" class="w-full h-full object-cover md:hidden" id="mobile-video">
            <source src="videos/workfit-mobile.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/50"></div>

        <!-- Hero Content (bottom-left aligned) -->
        <div class="absolute inset-0 z-10">
            <div class="absolute  top-1/2 -translate-y-1/2 lg:bottom-8 lg:top-auto left-0 text-left  text-white pl-4 md:pl-12 pr-4">
                <h1 class="text-center md:text-left text-4xl md:text-6xl font-bold mb-4 slide-in">WORKFIT</h1>
                <p class="text-center lg:text-left  text-xl md:text-2xl pr-8 md:pr-0 mb-8 max-w-xl fade-in">Premium activewear designed for performance </p>
                <div class="flex flex-col sm:flex-row justify-center lg:mx-0 lg:justify-start gap-4 fade-in">
                    <a href="{{ route('categories.index', 'women') }}" class="bg-white hover:bg-gray-700 text-gray-950 hover:text-white font-bold py-3 px-6 transition-colors uppercase text-center w-2/3 lg:w-auto mx-auto lg:mx-0 ">
                        SHOP WOMEN
                    </a>
                    <a href="{{ route('categories.index', 'men') }}" class="bg-white hover:bg-gray-400 text-black font-bold py-3 px-6 transition-colors text-center w-2/3 lg:w-auto mx-auto lg:mx-0 ">
                        SHOP MEN
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Men Products -->
    <section class="py-8 px-4">
        <h1 class="text-center font-bold text-3xl md:text-4xl lg:text-5xl mb-2 animate-on-scroll uppercase">Men's Collection</h1>
        <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto animate-on-scroll">Discover our latest collection of products</p>

        @if($men && $men->directProducts && $men->directProducts->isNotEmpty())
       @livewire('product-index',['products'=>$men->directProducts->take(4), 'useBestSellerLogic' => true, 'category' => $men->id])
       @endif
       @if($men)
       <div class="text-center mb-12 animate-on-scroll">
        <a href="{{ route('categories.index', $men->slug) }}" class="border-2 border-gray-900 hover:bg-gray-900 hover:text-white font-bold py-3 px-8 transition-colors">
            VIEW ALL {{ $men->name }}'S
        </a>
    </div>
    @endif

   </section>


    <!-- Full-width Lifestyle Banner -->
    <section class="relative h-[1200px] bg-cover overflow-hidden animate-on-scroll my-4">
        <img src="{{ asset('imgs/women.jpg')}}"
             loading="lazy"
             alt="Lifestyle Banner"

             height="600"
             class="w-full h-full object-cover">
             @if($collections->isNotEmpty())
        <!-- Overlay with only background dark -->
        <div class="absolute inset-0 bg-black/50 z-0">
            <div class="absolute bottom-8 left-0 z-40 text-left text-white pl-8 md:pl-12">
                <h2 class="text-center font-bold text-3xl md:text-4xl lg:text-5xl mb-2 animate-on-scroll uppercase">{{ $collections->first()->name }} 'S COLLECTIONS</h2>
                <p class="text-xl mb-6 max-w-2xl">
                    {{ $collections->first()->description }}
                </p>
                <a href="{{ route('collections.index') }}"
                   class="bg-white hover:bg-gray-900 text-gray-950 hover:text-white font-bold py-3 px-8 transition-colors">
                    SHOP NOW
                </a>
            </div>
        </div>
        @endif
    </section>
    <!-- Product Grid - First Category Collection -->
    <section class="py-8  px-4">
        @if($women && $women->directProducts && $women->directProducts->isNotEmpty())
        <div class="container mx-auto">
            <h2 class="text-center font-bold text-3xl md:text-4xl lg:text-5xl mb-2 animate-on-scroll uppercase">{{ $women->name }}'S COLLECTION</h2>

            <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto animate-on-scroll">{{ $women->description }}</p>

           @livewire('product-index',['products'=>$women->directProducts->take(4), 'useBestSellerLogic' => true, 'category' => $women->id])
            <div class="text-center mt-12 animate-on-scroll">
                <a href="{{ route('categories.index', $women->slug) }}" class="border-2 border-gray-900 hover:bg-gray-900 hover:text-white font-bold py-3 px-8 transition-colors">
                    VIEW ALL {{ $women->name }}'S
                </a>
            </div>
        </div>
        @endif
    </section>

    <!-- Three Image Block Section -->
    <section class="py-8 px-4 bg-gray-100">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 h-auto">
                <!-- RUN Block -->
                <div class="relative overflow-hidden rounded-lg group animate-on-scroll">
                    <img src="https://images.unsplash.com/photo-1679216129631-fbcec034558c?q=70&w=600&auto=format&fit=crop"
                        width="600"
                         height="600"
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
                         height="600"
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
                         height="600"
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

     <section class="py-8  px-4">
        @if($categories->isNotEmpty())
        <div class="container mx-auto">
            @if($categories->count() > 1)
            <h2 class="text-center font-bold text-3xl md:text-4xl lg:text-5xl mb-2 animate-on-scroll uppercase">{{ $categories->get(0)->name }}'S COLLECTION</h2>

            <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto animate-on-scroll px-8">{{ $categories->get(0)->description }}</p>


           @livewire('product-index',['products'=>$categories->get(0)->directProducts->take(4)])

            <div class="text-center mt-12 animate-on-scroll">
                <a href="{{ route('categories.index', $categories->get(0)->slug) }}" class="border-2 border-gray-900 hover:bg-gray-800 hover:text-white font-bold py-3 px-8 transition-colors uppercase">
                    VIEW ALL {{ $categories->get(0)->name }}'S
                </a>
            </div>
            @endif
        </div>
        @endif
    </section>

    <!-- Full-width Lifestyle Banner 2 -->
    <section class="relative w-full h-[1200px] overflow-hidden animate-on-scroll mt-8 mb-8">
        <img src="{{ asset('imgs/group.jpg')}}"
             loading="lazy"
             alt="Lifestyle Banner"
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/50">
            <div class="absolute bottom-8 left-0 text-left text-white pl-8 md:pl-12">
                <h1 class="text-3xl  lg:text-4xl font-bold my-4 uppercase playfair">WorkFit</h1>
                <p class="text-xl mb-6 max-w-2xl">Be the first to shop our latest collection</p>
                <a href="{{ route('collections.index') }}" class="bg-white hover:bg-gray-900  text-gray-950 hover:text-white playfair font-bold py-3 px-8 transition-colors">
                    SHOP NOW
                </a>
            </div>
        </div>
    </section>


    <!-- Just Arrived Products -->
    <section class="py-8 px-4">
        <h1 class="text-center font-bold text-3xl md:text-4xl lg:text-5xl mb-2 animate-on-scroll uppercase">Just Arrived</h1>
        <p class="text-center text-gray-600 mb-4 max-w-2xl mx-auto animate-on-scroll px-8">Discover our latest collection of products</p>
        @if($recent && $recent->isNotEmpty())
       @livewire('product-index',['products'=>$recent])
       @endif
       <div class="text-center mt-4 animate-on-scroll">
        <a href="{{ route('categories.index', $categories->get(0)->slug) }}" class="border-2 border-gray-900 hover:bg-gray-800 hover:text-white font-bold py-3 px-8 transition-colors uppercase">
          shop now
        </a>
    </div>
   </section>

   <section class="relative h-[1200px] overflow-hidden animate-on-scroll bg-[url(/imgs/bg-footer.jpg)] bg-cover bg-top mt-20 mb-0">

    <div class="absolute inset-0 bg-black/50 w-full">
        <div class="absolute bottom-8 left-0 text-left text-white pl-8 md:pl-12">
            <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-4 uppercase playfair">WorkFit</h1>
            <p class="text-xl mb-6 max-w-2xl">Be the first to shop our latest collection</p>

        </div>
    </div>
</section>

@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ensure mobile video plays
    const mobileVideo = document.getElementById('mobile-video');
    if (mobileVideo && window.innerWidth < 768) {
        // Try to play the video
        const playPromise = mobileVideo.play();

        if (playPromise !== undefined) {
            playPromise.then(() => {
                console.log('Mobile video started playing');
            }).catch(error => {
                console.log('Mobile video autoplay failed:', error);
                // If autoplay fails, try to play on first user interaction
                document.addEventListener('touchstart', function() {
                    mobileVideo.play().catch(e => console.log('Video play failed:', e));
                }, { once: true });
            });
        }

        // Also try to play when video is loaded
        mobileVideo.addEventListener('loadeddata', function() {
            mobileVideo.play().catch(e => console.log('Video play on load failed:', e));
        });
    }
});
</script>
