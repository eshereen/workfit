<div class="relative" x-data="{ showSearch: false }" @click.away="showSearch = false">
    <!-- Search Button (Mobile) -->
    <button @click="showSearch = !showSearch"
            class="md:hidden relative font-xs hover:text-red-600 transition-colors {{ request()->routeIs('home') ? 'text-white group-hover:text-gray-900' : 'text-gray-800' }}">
        <i class="fas fa-search text-xl"></i>
    </button>

    <!-- Search Bar (Desktop) -->
    <div class="hidden md:block relative">
        <div class="relative">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                wire:focus="$set('showResults', true)"
                placeholder="Search products..."
                class="w-64 px-4 py-2 pl-10 pr-4 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent {{ request()->routeIs('home') ? 'bg-white/90 placeholder-gray-500' : 'bg-white' }}"
            >
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400 text-sm"></i>
            </div>

            @if($search)
                <button
                    wire:click="$set('search', '')"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <i class="fas fa-times text-gray-400 hover:text-gray-600 text-sm"></i>
                </button>
            @endif
        </div>

        <!-- Search Results Dropdown -->
        @if($showResults && count($searchResults) > 0)
            <div class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-[1200] max-h-96 overflow-y-auto">
                @foreach($searchResults as $product)
                    <div wire:click="selectProduct({{ $product->id }})"
                         class="flex items-center p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                        <div class="flex-shrink-0 w-12 h-12 mr-3">
                            @if($product->getFirstMediaUrl('main_image'))
                                <img src="{{ $product->getFirstMediaUrl('main_image') }}"
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover rounded">
                            @else
                                <div class="w-full h-full bg-gray-200 rounded flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</p>
                            <p class="text-sm text-gray-500">
                                @if($product->category)
                                    {{ $product->category->name }}
                                    @if($product->subcategory)
                                        • {{ $product->subcategory->name }}
                                    @endif
                                @endif
                            </p>
                            <p class="text-sm font-semibold text-red-600">${{ number_format($product->price, 2) }}</p>
                        </div>
                    </div>
                @endforeach

                @if(count($searchResults) >= 5)
                    <div class="p-3 text-center border-t border-gray-200">
                        <button wire:click="searchAll"
                                class="text-sm text-red-600 hover:text-red-700 font-medium">
                            View all results for "{{ $search }}"
                        </button>
                    </div>
                @endif
            </div>
        @elseif($showResults && $search && count($searchResults) === 0)
            <div class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-[1200] p-4 text-center">
                <p class="text-sm text-gray-500">No products found for "{{ $search }}"</p>
            </div>
        @endif
    </div>

    <!-- Mobile Search Overlay -->
    <div x-show="showSearch"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="md:hidden fixed inset-0 z-[1200] bg-black bg-opacity-50 flex items-start justify-center pt-20"
         style="display: none;">

        <div class="bg-white mx-4 w-full max-w-md rounded-lg shadow-xl">
            <div class="p-4">
                <div class="relative mb-4">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search products..."
                        class="w-full px-4 py-3 pl-10 pr-4 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        autofocus
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>

                <!-- Mobile Search Results -->
                @if(count($searchResults) > 0)
                    <div class="max-h-80 overflow-y-auto">
                        @foreach($searchResults as $product)
                            <div wire:click="selectProduct({{ $product->id }})"
                                 @click="showSearch = false"
                                 class="flex items-center p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                                <div class="flex-shrink-0 w-12 h-12 mr-3">
                                    @if($product->getFirstMediaUrl('main_image'))
                                        <img src="{{ $product->getFirstMediaUrl('main_image') }}"
                                             alt="{{ $product->name }}"
                                             class="w-full h-full object-cover rounded">
                                    @else
                                        <div class="w-full h-full bg-gray-200 rounded flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</p>
                                    <p class="text-sm text-gray-500">
                                        @if($product->category)
                                            {{ $product->category->name }}
                                        @endif
                                    </p>
                                    <p class="text-sm font-semibold text-red-600">${{ number_format($product->price, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if(count($searchResults) >= 5)
                        <div class="p-3 text-center border-t border-gray-200">
                            <button wire:click="searchAll"
                                    @click="showSearch = false"
                                    class="text-sm text-red-600 hover:text-red-700 font-medium">
                                View all results
                            </button>
                        </div>
                    @endif
                @elseif($search && count($searchResults) === 0)
                    <div class="p-4 text-center">
                        <p class="text-sm text-gray-500">No products found</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
