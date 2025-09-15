@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Search Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Search Results</h1>
        <p class="text-gray-600">
            @if($products->total() > 0)
                Showing {{ $products->firstItem() }} - {{ $products->lastItem() }} of {{ $products->total() }} results for "<strong>{{ $query }}</strong>"
            @else
                No results found for "<strong>{{ $query }}</strong>"
            @endif
        </p>
    </div>

    @if($products->count() > 0)
        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8">
            @foreach($products as $product)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 group">
                    <!-- Product Image -->
                    <div class="aspect-square overflow-hidden bg-gray-100 relative">
                        <a href="{{ route('product.show', $product->slug) }}">
                            @if($product->getFirstMediaUrl('main_image'))
                                <img src="{{ $product->getFirstMediaUrl('main_image') }}"
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                    <i class="fas fa-image text-gray-400 text-4xl"></i>
                                </div>
                            @endif
                        </a>

                        <!-- Quick Actions Overlay -->
                        <div class="absolute inset-0 bg-black/5 group-hover:bg-black/20 transition-all duration-300 flex items-center justify-center pointer-events-none group-hover:pointer-events-auto">
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex gap-2 pointer-events-auto">
                                @if($product->variants->count() > 0)
                                    <a href="{{ route('product.show', $product->slug) }}"
                                       class="bg-white text-gray-900 px-4 py-2 rounded-full hover:bg-red-600 hover:text-white transition-colors">
                                        <i class="fas fa-eye mr-2"></i>View Options
                                    </a>
                                @else
                                    @if($product->quantity > 0)
                                        <a href="{{ route('product.show', $product->slug) }}"
                                           class="bg-white text-gray-900 px-4 py-2 rounded-full hover:bg-red-600 hover:text-white transition-colors">
                                            <i class="fas fa-shopping-cart mr-2"></i>Add to Cart
                                        </a>
                                    @else
                                        <span class="bg-gray-300 text-gray-500 px-4 py-2 rounded-full cursor-not-allowed opacity-50">
                                            <i class="fas fa-times mr-2"></i>Out of Stock
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="p-4">
                        <a href="{{ route('product.show', $product->slug) }}" class="block">
                            <h3 class="font-semibold text-gray-900 mb-2 hover:text-red-600 transition-colors line-clamp-2">
                                {{ $product->name }}
                            </h3>
                        </a>

                        @if($product->category)
                            <p class="text-sm text-gray-500 mb-2">
                                {{ $product->category->name }}
                                @if($product->subcategory)
                                    • {{ $product->subcategory->name }}
                                @endif
                            </p>
                        @endif

                        <div class="flex items-center justify-between">
                            <div>
                                <span class="font-bold text-lg text-gray-900">
                                    {{ $currencyInfo['symbol'] }}{{ number_format($product->converted_price ?? $product->price, 2) }}
                                </span>
                                @if($product->compare_price > 0)
                                    <span class="text-sm text-gray-500 line-through ml-2">
                                        {{ $currencyInfo['symbol'] }}{{ number_format($product->converted_compare_price ?? $product->compare_price, 2) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $products->appends(['q' => $query])->links() }}
        </div>
    @else
        <!-- No Results -->
        <div class="text-center py-16">
            <div class="text-gray-400 mb-4">
                <i class="fas fa-search text-6xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No products found</h3>
            <p class="text-gray-600 mb-6">
                We couldn't find any products matching your search. Try adjusting your search terms or browse our categories.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('products.index') }}"
                   class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition-colors">
                    Browse All Products
                </a>
                <a href="{{ route('categories.all') }}"
                   class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">
                    View Categories
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
