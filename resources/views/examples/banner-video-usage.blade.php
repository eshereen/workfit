{{-- 
    HERO BANNER WITH VIDEO SUPPORT
    
    This example shows how to display either an image or video banner
--}}

@php
    $heroBanner = \App\Models\Banner::getBySection('hero');
@endphp

@if($heroBanner)
<section class="relative h-96 md:h-[600px] overflow-hidden">
    {{-- Background Media (Video or Image) --}}
    @if($heroBanner->isVideo())
        <video 
            autoplay 
            muted 
            loop 
            playsinline
            class="absolute inset-0 w-full h-full object-cover"
            @if($heroBanner->image)
            poster="{{ $heroBanner->getImageUrl() }}"
            @endif
        >
            <source src="{{ $heroBanner->getVideoUrl() }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    @else
        <img 
            src="{{ $heroBanner->getImageUrl() }}" 
            alt="{{ $heroBanner->title }}" 
            class="absolute inset-0 w-full h-full object-cover uppercase">
    @endif
    
    {{-- Overlay Content --}}
    <div class="absolute inset-0 bg-black/40 flex items-center justify-center z-10">
        <div class="text-center text-white px-4">
            @if($heroBanner->title)
            <h1 class="text-4xl md:text-6xl font-bold mb-4 animate-fade-in-up uppercase">
                {{ $heroBanner->title }}
            </h1>
            @endif
            
            @if($heroBanner->description)
            <p class="text-lg md:text-xl mb-8 max-w-2xl mx-auto animate-fade-in-up animation-delay-200 uppercase">
                {{ $heroBanner->description }}
            </p>
            @endif
            
            @if($heroBanner->getLink())
            <a href="{{ $heroBanner->getLink() }}" 
               class="inline-block bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition transform hover:scale-105 animate-fade-in-up animation-delay-400 uppercase">
                {{ $heroBanner->button_text ?? 'Shop Now' }}
            </a>
            @endif
        </div>
    </div>
</section>

{{-- Optional: Add CSS for animations --}}
<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out forwards;
}

.animation-delay-200 {
    animation-delay: 0.2s;
    opacity: 0;
}

.animation-delay-400 {
    animation-delay: 0.4s;
    opacity: 0;
}
</style>
@endif

{{-- 
    ALTERNATIVE: Full-width Video Banner with Controls
--}}

@php
    $videoBanner = \App\Models\Banner::getBySection('video-section');
@endphp

@if($videoBanner && $videoBanner->isVideo())
<section class="container mx-auto px-4 py-12">
    <div class="relative rounded-lg overflow-hidden">
        <video 
            controls
            class="w-full h-auto"
            @if($videoBanner->image)
            poster="{{ $videoBanner->getImageUrl() }}"
            @endif
        >
            <source src="{{ $videoBanner->getVideoUrl() }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        
        @if($videoBanner->title || $videoBanner->description)
        <div class="mt-6 text-center">
            @if($videoBanner->title)
            <h2 class="text-3xl font-bold mb-2">{{ $videoBanner->title }}</h2>
            @endif
            
            @if($videoBanner->description)
            <p class="text-gray-600">{{ $videoBanner->description }}</p>
            @endif
        </div>
        @endif
    </div>
</section>
@endif
