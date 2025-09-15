<div class="relative" x-data="{ showSearch: false }" @click.away="showSearch = false">
    <!-- Search Button (Default State) -->
    <button @click="showSearch = !showSearch"
            class="relative font-xs hover:text-red-600 transition-colors {{ request()->routeIs('home') ? 'text-white group-hover:text-gray-900' : 'text-gray-800' }}">
        <i class="fas fa-search text-xl"></i>
    </button>

    <!-- Full-Screen Search Banner (Expanded State) -->
    <div x-show="showSearch"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="fixed top-0 left-0 right-0 w-full bg-white shadow-lg border-b border-gray-200 z-[1300]"
         style="display: none;">

        <div class="container mx-auto px-4">
            <div class="flex items-center py-4 gap-4">
                <!-- Search Icon -->
                <div class="flex-shrink-0">
                    <i class="fas fa-search text-gray-500 text-lg"></i>
                </div>

                <!-- Search Input -->
                <div class="flex-1 relative">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        wire:focus="$set('showResults', true)"
                        placeholder="Search products..."
                        class="w-full px-4 py-2 text-lg border-0 focus:outline-none focus:ring-0 bg-transparent placeholder-gray-500"
                        autofocus
                    >
                </div>

                <!-- Close Button -->
                <div class="flex-shrink-0">
                    <button @click="showSearch = false"
                            class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                        <i class="fas fa-times text-gray-500 text-lg"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Search Results Dropdown -->
        @if($showResults && count($searchResults) > 0)
            <div class="border-t border-gray-200 bg-white">
                <div class="container mx-auto px-4">
                    <div class="max-h-96 overflow-y-auto">
                        @foreach($searchResults as $product)
                            <div wire:click="selectProduct({{ $product->id }})"
                                 @click="showSearch = false"
                                 class="flex items-center p-4 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                                <div class="flex-shrink-0 w-12 h-12 mr-4">
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
                                    <p class="text-base font-medium text-gray-900 truncate">{{ $product->name }}</p>
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
                            <div class="p-4 text-center border-t border-gray-200">
                                <button wire:click="searchAll"
                                        @click="showSearch = false"
                                        class="text-base text-red-600 hover:text-red-700 font-medium">
                                    View all results for "{{ $search }}"
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @elseif($showResults && $search && count($searchResults) === 0)
            <div class="border-t border-gray-200 bg-white">
                <div class="container mx-auto px-4">
                    <div class="p-8 text-center">
                        <p class="text-base text-gray-500">No products found for "{{ $search }}"</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

</div>
