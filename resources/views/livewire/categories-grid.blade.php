<div class="space-y-6">
    <!-- Search Controls -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <!-- Search -->
        <div class="w-full md:w-96">
            <input
                wire:model.live="search"
                type="text"
                placeholder="Search categories..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
            >
        </div>
    </div>

    <!-- Categories Grid -->
    @if($filteredCategories->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($filteredCategories as $category)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <!-- Category Image -->
                    <div class="relative group">
                        @if($category->media->count() > 0)
                            <picture class="w-full h-64">
                                {{-- Modern formats first --}}
                                <source srcset="{{ $category->getFirstMediaUrl('main_image', 'large_webp') }}" type="image/webp">
                                {{-- Fallback for older browsers --}}
                                <img src="{{ $category->getFirstMediaUrl('main_image') }}"
                                     alt="{{ $category->name }}"
                                     class="w-full h-64 object-cover"
                                     width="400"
                                     height="400"
                                     loading="lazy"
                                     decoding="async">
                            </picture>
                        @else
                            <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-400">No Image</span>
                            </div>
                        @endif

                        <!-- Quick Actions Overlay -->
                        <div class="absolute inset-0 bg-black/5 group-hover:bg-black/50 transition-all duration-300 flex items-center justify-center">
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <a
                                    href="{{ route('categories.index', $category->slug) }}"
                                    class="bg-white text-gray-900 px-6 py-3 rounded-full hover:bg-red-600 hover:text-white transition-colors font-medium"
                                >
                                    Explore Category
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Category Info -->
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 mb-2 text-lg">{{ $category->name }}</h3>

                        @if($category->description)
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $category->description }}</p>
                        @endif

                        <!-- Product Count -->
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm text-gray-500">{{ $category->products_count }} products</span>
                        </div>

                        <!-- Action Button -->
                        <a
                            href="{{ route('categories.index', $category->slug) }}"
                            class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors text-center block"
                        >
                            Explore Category
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- No Categories Found -->
        <div class="text-center py-12">
            <div class="text-gray-500">
                <i class="fas fa-search text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold mb-2">No categories found</h3>
                <p class="text-gray-600">
                    @if($search)
                        No categories match your search "{{ $search }}"
                    @else
                        No categories available at the moment.
                    @endif
                </p>
            </div>
        </div>
    @endif
</div>
