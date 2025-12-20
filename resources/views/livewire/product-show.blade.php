<div class="container px-6 py-8 mx-auto my-20">
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-green-700 bg-green-100 rounded-lg border border-green-400">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 mb-4 text-red-700 bg-red-100 rounded-lg border border-red-400">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col gap-8 lg:flex-row">
        <!-- Product Images -->
        <div class="lg:w-1/2"
        x-data="{
           currentImage: '{{ $product->getFirstMediaUrl('main_image') }}',
           currentZoomImage: '{{ $product->getFirstMediaUrl('main_image', 'zoom_webp') }}',
           images: [
               {
                   large: '{{ $product->getFirstMediaUrl('main_image') }}',
                   zoom: '{{ $product->getFirstMediaUrl('main_image', 'zoom_webp') }}',
                   medium: '{{ $product->getFirstMediaUrl('main_image', 'medium_webp') }}',
                   thumb: '{{ $product->getFirstMediaUrl('main_image', 'thumb_webp') }}',
                   avif: '{{ $product->getFirstMediaUrl('main_image', 'medium_avif') }}',
               },
               @foreach($product->getMedia('product_images') as $image)
               {
                   large: '{{ $image->getUrl() }}',
                   zoom: '{{ $image->getUrl('zoom_webp') }}',
                   medium: '{{ $image->getUrl('medium_webp') }}',
                   thumb: '{{ $image->getUrl('thumb_webp') }}',
                   avif: '{{ $image->getUrl('medium_avif') }}',
               },
               @endforeach
           ]
        }">

       <!-- Main image with zoom -->
       <div class="flex overflow-hidden relative justify-center items-center bg-white rounded-lg"
       x-data="{
          magnifierEnabled: false,
          zoomX: 0,
          zoomY: 0,
          zoomW: 0,
          zoomH: 0
       }"
       @mousemove="if (magnifierEnabled) { zoomX = $event.offsetX; zoomY = $event.offsetY; zoomW = $event.target.clientWidth; zoomH = $event.target.clientHeight }"
       @mouseleave="magnifierEnabled = false">

      <!-- Magnifier toggle -->
      <button type="button"
              class="absolute top-3 right-3 z-30 px-2 py-1 text-xs text-gray-700 rounded border border-gray-200 bg-white/90 hover:bg-gray-100"
              @click.stop="magnifierEnabled = !magnifierEnabled"
              :aria-pressed="magnifierEnabled.toString()">
          <i class="fas" :class="magnifierEnabled ? 'fa-search-minus' : 'fa-search-plus'"></i>
          <span class="sr-only" x-text="magnifierEnabled ? 'Disable magnifier' : 'Enable magnifier'"></span>
      </button>

      <!-- Base product image (always visible) -->
      <picture class="block w-full h-full select-none" :class="magnifierEnabled ? 'cursor-crosshair' : 'cursor-default'">
        <img :src="currentImage"
             alt="{{ $product->name }}"
             class="block object-cover object-center max-w-full max-h-full"
             style="object-position: top;"
             width="800"
             height="800"
             decoding="async"
             fetchpriority="high">
    </picture>


      <!-- Magnifier lens -->
      <div x-show="magnifierEnabled"
           class="absolute rounded-full ring-2 ring-gray-200 shadow-sm pointer-events-none"
           x-transition.opacity
           :style="(() => { const lens=180; const scale=2.5; const top=zoomY - lens/2; const left=zoomX - lens/2; const bgX = (zoomX*scale) - lens/2; const bgY = (zoomY*scale) - lens/2; return `width:${lens}px;height:${lens}px;top:${top}px;left:${left}px;background-image:url(${currentZoomImage});background-repeat:no-repeat;background-size:${zoomW*scale}px ${zoomH*scale}px;background-position:-${bgX}px -${bgY}px;`; })()">
      </div>
   </div>


       <!-- Thumbnails -->
       <div class="grid grid-cols-4 gap-1 mt-2 md:mt-3">
           <template x-for="(image, index) in images" :key="index">
               <div class="overflow-hidden rounded border transition-colors cursor-pointer hover:border-gray-500"
                    :class="currentImage === image.large ? 'border-gray-500 ring-2 ring-gray-200' : 'border-gray-200'"
                    @click="magnifierEnabled = false; currentImage = image.large; currentZoomImage = image.zoom">

                   <picture class="object-cover w-full h-24 transition-opacity hover:opacity-80">
                       <source :srcset="image.avif" type="image/avif">
                       <source :srcset="image.medium" type="image/webp">
                       <img :src="image.thumb"
                            alt="{{ $product->name }}"
                            class="object-cover object-center w-full h-24"
                            style="object-position: center;"
                            width="150"
                            height="150"
                            loading="lazy"
                            decoding="async">
                   </picture>
               </div>
           </template>
       </div>
   </div>


        <!-- Product Info -->
        <div class="lg:w-1/2">
            @php
                $currencyServiceView = app(\App\Services\CountryCurrencyService::class);
                $displayCurrency = $currencyCode ?? 'USD';
                $displaySymbol = $currencySymbol ?? $currencyServiceView->getCurrencySymbol($displayCurrency);
                $convertAmount = function ($amount) use ($displayCurrency, $currencyServiceView) {
                    if ($amount === null) {
                        return null;
                    }
                    return $displayCurrency === 'USD'
                        ? $amount
                        : $currencyServiceView->convertFromUSD($amount, $displayCurrency);
                };
            @endphp
            @if($displayCurrency !== 'USD')
            <div class="p-3 mb-4 bg-green-50 rounded-lg border border-green-200">
                <div class="text-sm text-center text-green-800">
                    @if($isAutoDetected)
                        Prices automatically converted to {{ $displayCurrency }} ({{ $displaySymbol }}) based on your location
                    @else
                        Prices converted to {{ $displayCurrency }} ({{ $displaySymbol }})
                    @endif
                </div>
            </div>
            @endif

            <div class="flex justify-between items-start mb-4">
                <h1 class="text-3xl font-bold">{{ $product->name }}</h1>
                @auth
                <button wire:click="toggleWishlist"
                        wire:loading.attr="disabled"
                        wire:target="toggleWishlist"
                        class="wishlist-btn p-2 transition-colors {{ $isInWishlist ? 'text-yellow-900' : 'text-gray-400 hover:text-yellow-900' }}"
                        title="{{ $isInWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' }}">
                    <svg class="w-8 h-8" fill="{{ $isInWishlist ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    <span wire:loading wire:target="toggleWishlist" class="flex absolute inset-0 justify-center items-center">
                        <svg class="w-5 h-5 text-yellow-900 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
                @endauth
            </div>

            <div class="flex items-center mb-4">
                @php
                    $primaryPrice = $convertAmount($selectedVariant->price ?? $product->price);
                    $comparePrice = $convertAmount($product->compare_price);
                @endphp
                @if($product->compare_price > 0)
                <span class="mr-3 text-2xl font-bold text-red-600">
                    {{ $displaySymbol }}{{ number_format($primaryPrice, 2) }}
                </span>
                <span class="text-lg text-gray-500 line-through">
                    {{ $displaySymbol }}{{ number_format($comparePrice, 2) }}
                </span>
                <span class="px-2 py-1 ml-3 text-sm text-red-800 bg-red-100 rounded">
                    Save {{ $product->discount_percentage }}%
                </span>
                @else
                <span class="text-2xl font-bold text-red-600">
                    {{ $displaySymbol }}{{ number_format($primaryPrice, 2) }}
                </span>
                @endif
            </div>

            <div class="mb-6">

                @if($product->variants->isNotEmpty())
                <div class="mb-6">


                    <!-- Color Selection -->
                    @php
                        $colors = $product->variants->unique('color')->pluck('color');
                    @endphp
                    @if($colors->count() > 1)
                    <div class="mb-4">
                        <h4 class="mb-2 text-sm font-medium text-gray-700">Color</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($colors as $color)
                            @php
                                $variantForColor = $product->variants->where('color', $color)->first();
                                $colorCode = $this->getColorCode($color);
                            @endphp
                            <button wire:click="selectVariant('{{ $variantForColor->id }}')"
                                    class="px-2 py-2 mx-2 border rounded-xs text-sm transition-all duration-200 {{ $selectedVariant && $selectedVariant->color === $color ? 'ring-2 ring-gray-700 ring-offset-2' : 'hover:scale-105' }}"
                                    @if($selectedVariant && $selectedVariant->color === $color) aria-pressed="true" @endif
                                    style="background-color: {{ $colorCode }}; color: {{ $this->getContrastColor($colorCode) }}; border-color: {{ $colorCode }};">

                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Size Selection -->
                    @if($selectedVariant)
                    <div class="mb-6">
                        <h4 class="mb-2 text-sm font-medium text-gray-700">Size</h4>
                        <div class="flex flex-wrap gap-2 my-2">
                            @foreach($product->variants->where('color', $selectedVariant->color) as $variant)
                            <button type="button"
                                    wire:click="selectVariant('{{ $variant->id }}')"
                                    class="px-2  flex items-center justify-center border rounded-xs text-sm {{ $selectedVariantId == $variant->id ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}"
                                    @if($selectedVariantId == $variant->id) aria-pressed="true" @endif>
                                {{ $variant->size }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Quantity Selector -->
                @if(($selectedVariant && $selectedVariant->stock > 0) || ($product->variants->isEmpty() && $product->quantity > 0))
                    <div class="mb-4">
                        <h4 class="block mb-2 text-sm font-medium text-gray-700">Quantity:</h4>
                        <div class="flex overflow-hidden items-center">
                            <button type="button"
                                    wire:click="decrementQty"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50 cursor-not-allowed"
                                    class="px-3 py-2 text-gray-600 hover:bg-gray-100 transition-colors {{ $quantity <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    {{ $quantity <= 1 ? 'disabled' : '' }}
                                    title="Decrease quantity">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                            <input type="number"
                                   wire:model.live="quantity"
                                   min="1"
                                   max="{{ $selectedVariant ? min($selectedVariant->stock, 10) : min($product->quantity, 10) }}"
                                   class="w-16 text-center border-0 focus:ring-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                   id="quantity-input">
                            <button type="button"
                                    wire:click="incrementQty"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50 cursor-not-allowed"
                                    class="px-3 py-2 text-gray-600 hover:bg-gray-100 transition-colors {{ $quantity >= ($selectedVariant ? min($selectedVariant->stock, 10) : min($product->quantity, 10)) ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    {{ $quantity >= ($selectedVariant ? min($selectedVariant->stock, 10) : min($product->quantity, 10)) ? 'disabled' : '' }}
                                    title="Increase quantity">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif
                     <!--Description-->
                     <div class="py-6">
                        <h3 class="mb-3 text-lg font-semibold">Description</h3>
                        <div class="max-w-none prose">
                            {!! $product->description !!}
                        </div>
                    </div>
                <!-- Selected Variant Info -->
                @if($selectedVariant)
                <div class="p-3 mb-4 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-sm text-gray-600">Selected:</span>
                            <span class="ml-2 font-medium">{{ $selectedVariant->color }} - {{ $selectedVariant->size }}</span>
                        </div>
                        @php
                            $selectedPriceDisplay = $convertAmount($selectedVariant->price ?? $product->price);
                        @endphp
                        <span class="text-lg font-bold">{{ $displaySymbol }}{{ number_format($selectedPriceDisplay, 2) }}</span>
                    </div>
                  {{--    <div class="mt-1 text-sm text-gray-600">
                        Stock: {{ $selectedVariant->stock }} available
                    </div>--}}
                </div>
                @endif


                <!-- Add to Cart Button -->
                @if(($selectedVariant && $selectedVariant->stock > 0) || ($product->variants->isEmpty() && $product->quantity > 0))
                    <button wire:click="addToCart"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-75 cursor-not-allowed"
                            wire:target="addToCart"
                            class="px-6 py-3 w-full font-semibold text-white rounded-lg border-2 transition-colors cursor-pointer bg-gray-950 hover:bg-gray-100 hover:text-gray-950 border-gray-950 disabled:opacity-50 disabled:cursor-not-allowed hover:border-2 hover:border-gray-900">
                        <span wire:loading.remove wire:target="addToCart">Add to Cart</span>
                        <span wire:loading wire:target="addToCart">
                            <svg class="inline-block mr-2 -ml-1 w-4 h-4 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Adding...
                        </span>
                    </button>
                @else
                    <button disabled class="px-6 py-3 w-full font-semibold text-white bg-gray-400 rounded-lg cursor-not-allowed">
                        Out of Stock
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
    <div class="mt-16">
        <h2 class="mb-6 text-2xl font-bold text-center lg:text-left">You May Also Like</h2>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            @foreach($relatedProducts as $relatedProduct)
            <div class="overflow-hidden bg-white transition rounded-xs">
                <a href="{{ route('product.show', $relatedProduct->slug) }}">
                    <picture class="w-full h-80">
                        {{-- Modern formats first --}}
                        <source srcset="{{ $relatedProduct->getFirstMediaUrl('main_image', 'large_avif') }}" type="image/avif">
                        <source srcset="{{ $relatedProduct->getFirstMediaUrl('main_image', 'large_webp') }}" type="image/webp">
                        {{-- Fallback for older browsers --}}
                        <img src="{{ $relatedProduct->getFirstMediaUrl('main_image') }}"
                             alt="{{ $relatedProduct->name }}"
                                class="object-cover w-full h-80"
                                width="400"
                                style="object-position: top;"
                             height="400"
                             loading="lazy"
                             decoding="async">
                    </picture>
                </a>
                <div class="p-4 text-center lg:text-left">
                    <a href="{{ route('product.show', $relatedProduct->slug) }}"
                       class="block mb-1 text-xs font-semibold hover:text-red-600">
                        {{ $relatedProduct->name }}
                    </a>
                    @php
                        $relatedPrice = $convertAmount($relatedProduct->price);
                    @endphp
                    <span class="text-xs font-bold">{{ $displaySymbol }}{{ number_format($relatedPrice, 2) }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Listen for currency change events
    window.addEventListener('currency-changed', function(e) {
        console.log('ProductShow: Currency change event received:', e.detail);
        // Trigger the Livewire method to refresh currency
        @this.call('handleCurrencyChange', e.detail);
    });

    // Listen for Facebook Pixel AddToCart event
    Livewire.on('fbPixelAddToCart', (data) => {
        console.log('Facebook Pixel - AddToCart Event:', data);
        if (typeof fbq !== 'undefined') {
            fbq('track', 'AddToCart', {
                content_name: data[0].content_name,
                content_ids: data[0].content_ids,
                content_type: data[0].content_type,
                value: data[0].value,
                currency: data[0].currency,
                content_category: data[0].content_category,
                num_items: data[0].quantity
            });
        }
    });
});
</script>

<script>
@php
    // Use same price calculation as display (line 159)
    $fbPrice = $convertAmount($selectedVariant->price ?? $product->price) ?? 0;
    $fbPrice = is_numeric($fbPrice) ? (float)$fbPrice : 0;
@endphp

// Track ViewContent event
fbq('track', 'ViewContent', {
    content_ids: ['{{ $product->id }}'],
    content_type: 'product',
    content_name: '{{ addslashes($product->name ?? "") }}',
    value: {{ number_format($fbPrice, 2, '.', '') }},
    currency: '{{ $displayCurrency ?? "EGP" }}',
    content_category: '{{ addslashes($product->category->name ?? "") }}'
});

console.log('✅ Facebook Pixel: ViewContent', {
    id: '{{ $product->id }}',
    value: {{ number_format($fbPrice, 2, '.', '') }},
    currency: '{{ $displayCurrency ?? "EGP" }}'
});
</script>

