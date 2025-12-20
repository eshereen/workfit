{{-- 
    BANNER USAGE EXAMPLES FOR HOME.BLADE.PHP
    
    This file shows how to use the Banner system in your views.
    Copy these examples into your home.blade.php file.
--}}

{{-- Example 1: Single Hero Banner --}}
@php
    $heroBanner = \App\Models\Banner::getBySection('hero');
@endphp

@if($heroBanner)
<section class="relative h-96 md:h-[600px]">
    <img src="{{ $heroBanner->getImageUrl() }}" 
         alt="{{ $heroBanner->title }}" 
         class="w-full h-full object-cover">
    
    <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
        <div class="text-center text-white px-4">
            @if($heroBanner->title)
            <h1 class="text-4xl md:text-6xl font-bold mb-4">
                {{ $heroBanner->title }}
            </h1>
            @endif
            
            @if($heroBanner->description)
            <p class="text-lg md:text-xl mb-8 max-w-2xl mx-auto">
                {{ $heroBanner->description }}
            </p>
            @endif
            
            @if($heroBanner->getLink())
            <a href="{{ $heroBanner->getLink() }}" 
               class="inline-block bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition">
                {{ $heroBanner->button_text ?? 'Shop Now' }}
            </a>
            @endif
        </div>
    </div>
</section>
@endif

{{-- Example 2: Multiple Featured Banners (Grid Layout) --}}
@php
    $featuredBanners = \App\Models\Banner::getBySectionPattern('featured-%');
@endphp

@if($featuredBanners->count() > 0)
<section class="container mx-auto px-4 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($featuredBanners as $banner)
        <div class="relative group overflow-hidden rounded-lg shadow-lg">
            <img src="{{ $banner->getImageUrl() }}" 
                 alt="{{ $banner->title }}" 
                 class="w-full h-64 object-cover group-hover:scale-110 transition duration-300">
            
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-6">
                <div class="text-white">
                    @if($banner->title)
                    <h3 class="text-2xl font-bold mb-2">{{ $banner->title }}</h3>
                    @endif
                    
                    @if($banner->description)
                    <p class="text-sm mb-4 opacity-90">{{ $banner->description }}</p>
                    @endif
                    
                    @if($banner->getLink())
                    <a href="{{ $banner->getLink() }}" 
                       class="inline-block bg-white text-black px-6 py-2 rounded font-semibold hover:bg

-gray-100 transition">
                        {{ $banner->button_text ?? 'Explore' }}
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endif

{{-- Example 3: Specific Section Banner --}}
@php
    $promoBanner = \App\Models\Banner::getBySection('promo-banner');
@endphp

@if($promoBanner)
<section class="bg-red-600 text-white py-8 px-4">
    <div class="container mx-auto flex flex-col md:flex-row items-center justify-between">
        <div class="md:w-2/3 mb-4 md:mb-0">
            @if($promoBanner->title)
            <h2 class="text-3xl font-bold mb-2">{{ $promoBanner->title }}</h2>
            @endif
            
            @if($promoBanner->description)
            <p class="text-lg">{{ $promoBanner->description }}</p>
            @endif
        </div>
        
        @if($promoBanner->getLink())
        <a href="{{ $promoBanner->getLink() }}" 
           class="bg-white text-red-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
            {{ $promoBanner->button_text ?? 'Shop Now' }}
        </a>
        @endif
    </div>
</section>
@endif

{{-- 
    CONTROLLER APPROACH (RECOMMENDED FOR PERFORMANCE)
    
    Instead of querying in the blade file, add this to your FrontendController:
    
    public function index()
    {
        $heroBanner = Banner::getBySection('hero');
        $featuredBanners = Banner::getBySectionPattern('featured-%');
        $promoBanner = Banner::getBySection('promo-banner');
        
        return view('home', compact('heroBanner', 'featuredBanners', 'promoBanner'));
    }
--}}
