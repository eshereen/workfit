@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container px-4 py-8 mx-auto">
    <!-- Search Header -->
    <div class="mb-8">
        <h1 class="mb-2 text-3xl font-bold text-gray-900">Search Results</h1>
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
        <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            @foreach($products as $product)
                <div class="overflow-hidden bg-white rounded-lg shadow-md transition-shadow duration-300 hover:shadow-lg group">
                    <!-- Product Image -->
                    <div class="overflow-hidden relative bg-gray-100 aspect-square">
                        <a href="{{ route('product.show', $product->slug) }}">
                            @if($product->getFirstMediaUrl('main_image'))
                                <img src="{{ $product->getFirstMediaUrl('main_image') }}"
                                     alt="{{ $product->name }}"
                                     class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-105">
                            @else
                                <div class="flex justify-center items-center w-full h-full bg-gray-200">
                                    <i class="text-4xl text-gray-400 fas fa-image"></i>
                                </div>
                            @endif
                        </a>

                        <!-- Quick Actions Overlay -->
                        <div class="flex absolute inset-0 justify-center items-center transition-all duration-300 pointer-events-none bg-black/5 group-hover:bg-black/20 group-hover:pointer-events-auto">
                            <div class="flex gap-2 opacity-0 transition-opacity duration-300 pointer-events-auto group-hover:opacity-100">
                                @if($product->variants->count() > 0)
                                    <a href="{{ route('product.show', $product->slug) }}"
                                       class="px-4 py-2 text-gray-900 bg-white rounded-full transition-colors hover:bg-red-600 hover:text-white">
                                        <i class="mr-2 fas fa-eye"></i>View Options
                                    </a>
                                @else
                                    @if($product->quantity > 0)
                                        <a href="{{ route('product.show', $product->slug) }}"
                                           class="px-4 py-2 text-gray-900 bg-white rounded-full transition-colors hover:bg-red-600 hover:text-white">
                                            <i class="mr-2 fas fa-shopping-cart"></i>Add to Cart
                                        </a>
                                    @else
                                        <span class="px-4 py-2 text-gray-500 bg-gray-300 rounded-full opacity-50 cursor-not-allowed">
                                            <i class="mr-2 fas fa-times"></i>Out of Stock
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="p-4">
                        <a href="{{ route('product.show', $product->slug) }}" class="block">
                            <h3 class="mb-2 font-semibold text-gray-900 transition-colors hover:text-red-600 line-clamp-2">
                                {{ $product->name }}
                            </h3>
                        </a>

                        @if($product->category)
                            <p class="mb-2 text-sm text-gray-500">
                                {{ $product->category->name }}
                                @if($product->subcategory)
                                    • {{ $product->subcategory->name }}
                                @endif
                            </p>
                        @endif

                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-lg font-bold text-gray-900">
                                    {{ $currencyInfo['currency_symbol'] }}{{ number_format($product->converted_price ?? $product->price, 2) }}
                                </span>
                                @if($product->compare_price > 0)
                                    <span class="ml-2 text-sm text-gray-500 line-through">
                                        {{ $currencyInfo['currency_symbol'] }}{{ number_format($product->converted_compare_price ?? $product->compare_price, 2) }}
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
        <div class="py-16 text-center">
            <div class="mb-4 text-gray-400">
                <i class="text-6xl fas fa-search"></i>
            </div>
            <h3 class="mb-2 text-xl font-semibold text-gray-900">No products found</h3>
            <p class="mb-6 text-gray-600">
                We couldn't find any products matching your search. Try adjusting your search terms or browse our categories.
            </p>
            <div class="flex flex-col gap-4 justify-center sm:flex-row">
                <a href="{{ route('products.index') }}"
                   class="px-6 py-3 text-white bg-red-600 rounded-lg transition-colors hover:bg-red-700">
                    Browse All Products
                </a>
                <a href="{{ route('categories.all') }}"
                   class="px-6 py-3 text-white bg-gray-600 rounded-lg transition-colors hover:bg-gray-700">
                    View Categories
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
