<div class="space-y-6">
    <!-- Clear Filters Button -->
    @if(count($selectedCategories) > 0 || count($selectedSubcategories) > 0)
        <div>
            <button
                wire:click="clearFilters"
                class="w-full px-4 py-2 text-sm text-red-600 border border-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-colors"
            >
                Clear All Filters
            </button>
        </div>
    @endif

    <!-- Categories -->
    <div class="space-y-4">
        <h3 class="text-lg font-semibold text-gray-900">Categories</h3>

        @foreach($this->getCategories() as $category)
            <div class="">
                <!-- Category Header with Checkbox and + Symbol -->
                <div class="flex items-center justify-between p-3 hover:bg-gray-50 transition-colors border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <input
                            type="checkbox"
                            id="category_{{ $category->id }}"
                            wire:click="toggleCategorySelection({{ $category->id }})"
                            {{ $this->isCategorySelected($category->id) ? 'checked' : '' }}
                            class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500 focus:ring-2"
                        >
                        <label for="category_{{ $category->id }}" class="font-medium text-gray-700 cursor-pointer">
                            {{ $category->name }}
                            <span class="text-sm text-gray-500 ml-1">({{ $this->getProductCountForCategory($category->id) }})</span>
                        </label>
                    </div>

                    <!-- + Symbol for expanding subcategories -->
                    <button
                        @click="$wire.toggleCategory({{ $category->id }})"
                        class="text-gray-500 hover:text-gray-700 transition-colors"
                    >
                        <svg
                            class="w-5 h-5 transition-transform duration-300 {{ $this->isCategoryExpanded($category->id) ? 'rotate-45' : '' }}"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </button>
                </div>

                <!-- Subcategories -->
                @if($this->isCategoryExpanded($category->id))
                    <div class="border-t border-gray-200">
                        @foreach($this->getSubcategories($category->id) as $subcategory)
                            <div class="flex items-center gap-3 p-3 pl-6 hover:bg-gray-50 transition-colors">
                                <input
                                    type="checkbox"
                                    id="subcategory_{{ $subcategory->id }}"
                                    wire:click="toggleSubcategorySelection({{ $subcategory->id }}, {{ $category->id }})"
                                    {{ $this->isSubcategorySelected($subcategory->id) ? 'checked' : '' }}
                                    class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 focus:ring-2"
                                >
                                <label for="subcategory_{{ $subcategory->id }}" class="text-sm text-gray-600 cursor-pointer">
                                    {{ $subcategory->name }}
                                    <span class="text-xs text-gray-500 ml-1">({{ $this->getProductCountForSubcategory($subcategory->id) }})</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach

        @if($this->getCategories()->count() === 0)
            <div class="text-center py-4 text-gray-500">
                <p class="text-sm">No categories found</p>
            </div>
        @endif
    </div>

    <!-- Active Filters Summary -->
    @if(count($selectedCategories) > 0 || count($selectedSubcategories) > 0)
        <div class="border-t pt-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Active Filters:</h4>
            <div class="space-y-2">
                @foreach($selectedCategories as $categoryId)
                    @php
                        $category = $this->getCategories()->firstWhere('id', $categoryId);
                    @endphp
                    @if($category)
                        <div class="flex items-center gap-2">
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Category: {{ $category->name }}</span>
                        </div>
                    @endif
                @endforeach

                @foreach($selectedSubcategories as $subcategoryId)
                    @php
                        $subcategory = null;
                        foreach($this->getCategories() as $cat) {
                            $sub = $this->getSubcategories($cat->id)->firstWhere('id', $subcategoryId);
                            if($sub) {
                                $subcategory = $sub;
                                break;
                            }
                        }
                    @endphp
                    @if($subcategory)
                        <div class="flex items-center gap-2">
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Subcategory: {{ $subcategory->name }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</div>
