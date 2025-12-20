@extends('layouts.app')

@section('content')
    {{-- Preload hero banner for better LCP --}}
    @if($heroBanner)
        @push('head')
            @if($heroBanner->isVideo())
                {{-- CRITICAL: Preload poster image for LCP optimization --}}
                @if($heroBanner->poster_image)
                    <link rel="preload" as="image" href="{{ $heroBanner->getPosterImageUrl() }}" fetchpriority="high" imagesrcset="{{ $heroBanner->getPosterImageUrl() }} 1920w" imagesizes="100vw">
                @elseif($heroBanner->image)
                    <link rel="preload" as="image" href="{{ $heroBanner->getImageUrl() }}" fetchpriority="high" imagesrcset="{{ $heroBanner->getImageUrl() }} 1920w" imagesizes="100vw">
                @endif
            @else
                <link rel="preload" as="image" href="{{ $heroBanner->getImageUrl() }}" fetchpriority="high">
            @endif
        @endpush
    @endif

    {{-- Promotional Modal --}}
@livewire('promo-modal')

@if($heroBanner)
  <section class="relative -top-28 h-screen overflow-hidden">
    {{-- Background Media (Video or Image) --}}
    @if($heroBanner->isVideo())
        {{-- Show poster image as LCP element --}}
        <img 
            id="hero-poster"
            @if($heroBanner->poster_image)
                src="{{ $heroBanner->getPosterImageUrl() }}"
            @elseif($heroBanner->image)
                src="{{ $heroBanner->getImageUrl() }}"
            @else
                src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='1920' height='1080'%3E%3Crect fill='%23000' width='1920' height='1080'/%3E%3C/svg%3E"
            @endif
            alt="{{ $heroBanner->title }}"
            class="w-full h-full object-cover"
            fetchpriority="high"
            loading="eager"
            decoding="sync"
        >
        
        {{-- Video loads after page render --}}
        <video 
            id="hero-video"
            muted loop playsinline preload="none"
            class="w-full h-full object-cover absolute inset-0 opacity-0 transition-opacity duration-500"
            data-src="{{ $heroBanner->getVideoUrl() }}"
            @if($heroBanner->poster_image)
                poster="{{ $heroBanner->getPosterImageUrl() }}"
            @elseif($heroBanner->image)
                poster="{{ $heroBanner->getImageUrl() }}"
            @endif
        >
            <source data-src="{{ $heroBanner->getVideoUrl() }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    @else
        <img 
            src="{{ $heroBanner->getImageUrl() }}" 
            alt="{{ $heroBanner->title }}" 
            class="absolute inset-0 w-full h-full object-cover uppercase"
            fetchpriority="high"
            loading="eager"
            decoding="sync">
    @endif
    
    {{-- Dark Overlay --}}
    <div class="absolute inset-0 bg-black/50 z-0"></div>
    
    {{-- Overlay Content --}}
 <div class="absolute inset-0 z-10">
           <div class="absolute  top-1/2 -translate-y-1/2 lg:bottom-8 lg:top-auto left-0 text-left  text-white pl-4 md:pl-12 pr-4">
              @if($heroBanner->title)
                <h1 class="text-center md:text-left text-4xl md:text-6xl font-bold mb-4 slide-in">  {{ $heroBanner->title }}</h1>
          
         
            @endif
            
            @if($heroBanner->description)
            <p class="text-center lg:text-left  text-xl md:text-2xl pr-8 md:pr-0 mb-8 max-w-xl fade-in !capitalize">
                {{ $heroBanner->description }}
            </p>
            @endif
             <div class="flex flex-col sm:flex-row justify-center lg:mx-0 lg:justify-start gap-4 fade-in">
            @if($heroBanner->getLink())
             
           
                    <a href="{{ $heroBanner->getLink() }}" class="bg-white hover:bg-gray-700 text-gray-950 hover:text-white font-bold py-3 px-6 transition-colors uppercase text-center w-2/3 lg:w-auto mx-auto lg:mx-0 ">
                        {{ $heroBanner->button_text ?? 'Shop Now' }}
                    </a>
                  
            @endif
              @if($heroBanner->getLink2())
                     <a href="{{ $heroBanner->getLink2() }}" class="bg-white hover:bg-gray-700 text-gray-950 hover:text-white font-bold py-3 px-6 transition-colors uppercase text-center w-2/3 lg:w-auto mx-auto lg:mx-0 ">
                        {{ $heroBanner->button_text_2 ?? 'Shop Now' }}
                    </a>
                  @endif
                </div>
        </div>
    </div>
</section>
@endif

    <!-- Men Products -->
    <section class="py-16  px-4">
        <h1 class="text-center font-bold text-3xl md:text-4xl lg:text-5xl mb-2 animate-on-scroll uppercase">Men's Collection</h1>
        <p class="text-center text-gray-600 mb-6 max-w-2xl mx-auto animate-on-scroll">Discover our latest collection of products</p>

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
     @if($women_banner)
    <section class="relative h-[1100px] bg-cover overflow-hidden animate-on-scroll my-8">
        <img src="{{ $women_banner->getImageUrl()}}"
             loading="lazy"
             alt="Lifestyle Banner"

             height="600"
             class="w-full h-full object-cover">
            
        <!-- Overlay with only background dark -->
        <div class="absolute inset-0 bg-black/50 z-0 flex flex-col justify-center items-center lg:justify-end lg:items-start lg:pb-16 lg:pl-12">
            <div class="text-center lg:text-left text-white px-4 md:px-12 lg:px-0 w-full lg:w-auto">
                <h2 class="text-4xl md:text-6xl font-bold mb-4 slide-in">{{ $women_banner->title }}</h2>
                <p class="text-xl md:text-2xl mb-8 max-w-xl mx-auto lg:mx-0 fade-in">
                    {{ $women_banner->description }}
                </p>
                <div class="flex flex-col lg:flex-row justify-center lg:justify-start items-center lg:items-center gap-4 fade-in">
                    @if($women_banner->getLink())
                    <a href="{{ $women_banner->getLink() }}" class="bg-white hover:bg-gray-700 text-gray-950 hover:text-white font-bold py-3 px-6 transition-colors uppercase text-center w-2/3 lg:w-auto">
                        {{ $women_banner->button_text }}
                    </a>
                    @endif
                    @if($women_banner->getLink2())
                    <a href="{{ $women_banner->getLink2() }}" class="bg-white hover:bg-gray-400 text-black font-bold py-3 px-6 transition-colors text-center w-2/3 lg:w-auto">
                        {{ $women_banner->button_text_2 }}
                    </a>
                    @endif
                </div>  
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

           @livewire('product-index',['products'=>$women->directProducts->take(4), 'useBestSellerLogic' => true, 'category' => $women->id, 'disableEagerLoading' => true])
            <div class="text-center mt-12 animate-on-scroll">
                <a href="{{ route('categories.index', $women->slug) }}" class="border-2 border-gray-900 hover:bg-gray-900 hover:text-white font-bold py-3 px-8 transition-colors">
                    VIEW ALL {{ $women->name }}'S
                </a>
            </div>
        </div>
        @endif
    </section>

    <!-- Three Image Block Section -->
    <section class="py-16  px-4 bg-gray-100">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 h-auto">
                <!-- RUN Block -->
                 @if($run)
                <div class="relative overflow-hidden rounded-lg group animate-on-scroll">
                    <img src="{{$run->getImageUrl()}}"
                        width="600"
                         height="600"
                         loading="lazy"
                         alt="Run"
                         class="w-full h-full object-cover hover-zoom">
                    <div class="absolute inset-0 bg-black/50 flex flex-col items-center justify-center">
                          @if($run->title)
                        <h3 class="text-white text-3xl lg:text-6xl font-bold mb-2">{{$run->title}}</h3>
                        @endif
                        @if($run->description)
                        <p class="text-white text-center px-4 mb-4">{{$run->description}}</p>
                        @endif
                        <div class="flex flex-col gap-4 md:flex-row">
                         
                                <a href="{{ $run->getLink() }}" 
                                   class="px-6 py-2 text-sm font-bold text-white uppercase transition-colors border-2 border-white hover:bg-white hover:bg-opacity-50 hover:text-black">
                                    {{ $run->button_text }}
                                </a>
                          
                        </div>
                    </div>
                </div>
                @endif
                <!-- TRAIN Block -->
                     @if($train)
                <div class="relative overflow-hidden rounded-lg group animate-on-scroll">
                    <img src="{{$train->getImageUrl()}}"
                         width="600"
                         height="600"
                         loading="lazy"
                         alt="Train"
                         class="w-full h-full object-cover hover-zoom">
                    <div class="absolute inset-0 bg-black/50 flex flex-col items-center justify-center">
                        @if($train->title)
                        <h3 class="text-white text-3xl lg:text-6xl font-bold mb-2">{{$train->title}}</h3>
                        @endif
                        @if($train->description)
                        <p class="text-white text-center px-4 mb-4">{{$train->description}}</p>
                        @endif
                        <div class="flex flex-col gap-4 md:flex-row">
                          @if($train->getLink())
                                <a href="{{ $train->getLink() }}" 
                                   class="px-6 py-2 text-sm font-bold text-white uppercase transition-colors border-2 border-white hover:bg-white hover:bg-opacity-50 hover:text-black">
                                    {{ $train->button_text }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
   @endif
                <!-- REC Block -->
                @if($rec)
                <div class="relative overflow-hidden rounded-lg group animate-on-scroll">
                    <img src="{{$rec->getImageUrl()}}"
                         width="600"
                         height="600"
                         loading="lazy"
                         alt="Rec"
                         class="w-full h-full object-cover hover-zoom">
                    <div class="absolute inset-0 bg-black/50 flex flex-col items-center justify-center">
                        @if($rec->title)
                        <h3 class="text-white text-3xl lg:text-6xl font-bold mb-2">{{$rec->title}}</h3>
                        @endif
                        @if($rec->description)
                        <p class="text-white text-center px-4 mb-4">{{$rec->description}}</p>
                        @endif
                        <div class="flex flex-col gap-4 md:flex-row">
                         @if($rec->getLink())
                                <a href="{{ $rec->getLink() }}" 
                                   class="px-6 py-2 text-sm font-bold text-white uppercase transition-colors border-2 border-white hover:bg-white hover:bg-opacity-50 hover:text-black">
                                    {{ $rec->button_text }}
                                </a>
                            @endif
                       
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Product Grid - Men's Collection -->

     <section class="py-8  px-4">
        @if($featured && $featured->isNotEmpty())
        <div class="container mx-auto">
            <h2 class="text-center font-bold text-3xl md:text-4xl lg:text-5xl mb-2 animate-on-scroll uppercase">FEATURED PRODUCTS</h2>

            <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto animate-on-scroll px-8">Discover our handpicked selection of premium products</p>

           @livewire('product-index',['products'=>$featured->take(4), 'disableEagerLoading' => true])

            <div class="text-center mt-12 animate-on-scroll">
                <a href="{{ route('products.index') }}" class="border-2 border-gray-900 hover:bg-gray-800 hover:text-white font-bold py-3 px-8 transition-colors uppercase">
                    VIEW ALL PRODUCTS
                </a>
            </div>
        </div>
        @endif
    </section>

    <!-- Full-width Lifestyle Banner 2 -->
     @if($group_banner)
    <section class="relative w-full h-[1100px] overflow-hidden animate-on-scroll my-8">
        <img src="{{$group_banner->getImageUrl()}}"
             loading="lazy"
             alt="Lifestyle Banner"
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/50 flex flex-col justify-center items-center lg:justify-end lg:items-start lg:pb-16 lg:pl-12">
            <div class="text-center lg:text-left text-white px-4 md:px-12 lg:px-0 w-full lg:w-auto">
                @if($group_banner->title)   
                <h1 class="text-4xl md:text-6xl font-bold mb-4 slide-in">{{ $group_banner->title }}</h1>
                @endif
                @if($group_banner->description)
                <p class="text-xl md:text-2xl mb-8 max-w-xl mx-auto lg:mx-0 fade-in">{{ $group_banner->description }}</p>
                @endif
                <div class="flex flex-col lg:flex-row justify-center lg:justify-start items-center lg:items-center gap-4 fade-in">
                    @if($group_banner->getLink())
                    <a href="{{ $group_banner->getLink() }}" class="bg-white hover:bg-gray-700 text-gray-950 hover:text-white font-bold py-3 px-6 transition-colors uppercase text-center w-2/3 lg:w-auto">
                        {{ $group_banner->button_text }}
                    </a>
                    @endif
                    @if($group_banner->getLink2())
                    <a href="{{ $group_banner->getLink2() }}" class="bg-white hover:bg-gray-400 text-black font-bold py-3 px-6 transition-colors text-center w-2/3 lg:w-auto">
                        {{ $group_banner->button_text_2 }}
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Just Arrived Products -->
    <section class="py-16  px-4">
        <h1 class="text-center font-bold text-3xl md:text-4xl lg:text-5xl mb-2 animate-on-scroll uppercase">Just Arrived</h1>
        <p class="text-center text-gray-600 mb-4 max-w-2xl mx-auto animate-on-scroll px-8">Discover our latest collection of products</p>
        @if($recent && $recent->isNotEmpty())
       @livewire('product-index',['products'=>$recent, 'disableEagerLoading' => true])
       @endif
       @if($categories->isNotEmpty())
       <div class="text-center mt-4 animate-on-scroll">
        <a href="{{ route('categories.index', $categories->get(0)->slug) }}" class="border-2 border-gray-900 hover:bg-gray-800 hover:text-white font-bold py-3 px-8 transition-colors uppercase">
          shop now
        </a>
       </div>
       @endif
   </section>
@if($featured_banner)
   <section class="relative h-[1100px] overflow-hidden animate-on-scroll mt-20 mb-0">
        <img src="{{$featured_banner->getImageUrl()}}"
             loading="lazy"
             alt="Featured Banner"
             class="w-full h-full object-cover object-top">
             
    <div class="absolute inset-0 bg-black/50 w-full flex flex-col justify-center items-center lg:justify-end lg:items-start lg:pb-16 lg:pl-12">
        <div class="text-center lg:text-left text-white px-4 md:px-12 lg:px-0 w-full lg:w-auto">
            @if($featured_banner->title)
            <h1 class="text-4xl md:text-6xl font-bold mb-4 slide-in">{{$featured_banner->title}}</h1>
            @endif
            @if($featured_banner->description)
            <p class="text-xl md:text-2xl mb-8 max-w-xl mx-auto lg:mx-0 fade-in">{{$featured_banner->description}}</p>
            @endif
             <div class="flex flex-col lg:flex-row justify-center lg:justify-start items-center lg:items-center gap-4 fade-in">
                    @if($featured_banner->getLink())
                    <a href="{{ $featured_banner->getLink() }}" class="bg-white hover:bg-gray-700 text-gray-950 hover:text-white font-bold py-3 px-6 transition-colors uppercase text-center w-2/3 lg:w-auto">
                        {{ $featured_banner->button_text }}
                    </a>
                    @endif
                    @if($featured_banner->getLink2())
                    <a href="{{ $featured_banner->getLink2() }}" class="bg-white hover:bg-gray-400 text-black font-bold py-3 px-6 transition-colors text-center w-2/3 lg:w-auto">
                        {{ $featured_banner->button_text_2 }}
                    </a>
                    @endif
            </div>
        </div>
    </div>
</section> 
@endif
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Lazy load hero video after LCP
    const heroVideo = document.getElementById('hero-video');
    const heroPoster = document.getElementById('hero-poster');
    
    if (heroVideo) {
        // Wait for page to be mostly loaded before loading video
        window.addEventListener('load', function() {
            setTimeout(function() {
                // Load video sources
                const sources = heroVideo.querySelectorAll('source[data-src]');
                sources.forEach(function(source) {
                    source.src = source.getAttribute('data-src');
                });
                
                // Set video src
                if (heroVideo.hasAttribute('data-src')) {
                    heroVideo.src = heroVideo.getAttribute('data-src');
                }
                
                // Load and play video
                heroVideo.load();
                const playPromise = heroVideo.play();
                
                if (playPromise !== undefined) {
                    playPromise.then(function() {
                        // Fade out poster, fade in video
                        if (heroPoster) {
                            heroPoster.style.opacity = '0';
                            setTimeout(function() {
                                heroPoster.style.display = 'none';
                            }, 500);
                        }
                        heroVideo.style.opacity = '1';
                    }).catch(function(error) {
                        console.log('Video autoplay prevented:', error);
                        // Keep poster visible if video fails
                    });
                }
            }, 500); // Small delay to ensure page is stable
        });
    }
    
    // Original mobile video code (if needed)
    const mobileVideo = document.getElementById('mobile-video');
    if (mobileVideo && window.innerWidth < 768) {
        const playPromise = mobileVideo.play();

        if (playPromise !== undefined) {
            playPromise.then(() => {
                console.log('Mobile video started playing');
            }).catch(error => {
                console.log('Mobile video autoplay failed:', error);
                document.addEventListener('touchstart', function() {
                    mobileVideo.play().catch(e => console.log('Video play failed:', e));
                }, { once: true });
            });
        }

        mobileVideo.addEventListener('loadeddata', function() {
            mobileVideo.play().catch(e => console.log('Video play on load failed:', e));
        });
    }
});
</script>

@endsection
