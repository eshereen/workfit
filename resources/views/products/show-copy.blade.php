<!DOCTYPE html>
<html lang="en" x-data="{
    selectedImage: 0,
    selectedColor: 'Black',
    selectedSize: 'M',
    quantity: 1
}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mannikan - Elegant Dresses</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .product-image {
            transition: opacity 0.3s ease;
        }
    </style>
</head>
<body class="bg-white font-sans">
    <!-- Header with Logo -->
@include('layouts.navbar')

    <!-- Product Section -->
    <main class="container mx-auto px-6 py-12 ">
        <div class="flex flex-col lg:flex-row gap-12 mt-40">
            <!-- Product Images -->
            <div class="lg:w-1/2">
                <div class="mb-4 rounded-lg overflow-hidden bg-gray-50">
                    <img
                        x-bind:src="['https://images.unsplash.com/photo-1605763240000-7e93b172d754?q=80&w=3087&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'https://images.unsplash.com/photo-1605763269552-4705d8c8e301?q=80&w=3087&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'https://images.unsplash.com/photo-1605763306689-28e85dac65d4?q=80&w=3087&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'https://images.unsplash.com/photo-1605763306721-21a4a72687ab?q=80&w=3087&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'][selectedImage]"
                        alt="Elegant Dress"
                        class="w-full h-auto product-image"
                    >
                </div>
                <div class="grid grid-cols-4 gap-3">
                    <button
                        x-on:click="selectedImage = 0"
                        class="border rounded-md overflow-hidden hover:border-pink-300 transition"
                        :class="{ 'border-pink-500': selectedImage === 0 }"
                    >
                        <img src="https://images.unsplash.com/photo-1605763240000-7e93b172d754?q=80&w=3087&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Dress Front" class="w-full h-auto">
                    </button>
                    <button
                        x-on:click="selectedImage = 1"
                        class="border rounded-md overflow-hidden hover:border-pink-300 transition"
                        :class="{ 'border-pink-500': selectedImage === 1 }"
                    >
                        <img src="https://images.unsplash.com/photo-1605763269552-4705d8c8e301?q=80&w=3087&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Dress Side" class="w-full h-auto">
                    </button>
                    <button
                        x-on:click="selectedImage = 2"
                        class="border rounded-md overflow-hidden hover:border-pink-300 transition"
                        :class="{ 'border-pink-500': selectedImage === 2 }"
                    >
                        <img src="https://images.unsplash.com/photo-1605763306689-28e85dac65d4?q=80&w=3087&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Dress Back" class="w-full h-auto">
                    </button>
                    <button
                        x-on:click="selectedImage = 3"
                        class="border rounded-md overflow-hidden hover:border-pink-300 transition"
                        :class="{ 'border-pink-500': selectedImage === 3 }"
                    >
                        <img src="https://images.unsplash.com/photo-1605763306721-21a4a72687ab?q=80&w=3087&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Dress Detail" class="w-full h-auto">
                    </button>
                </div>
            </div>

            <!-- Product Info -->
            <div class="lg:w-1/2">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Elegant Wrap Dress</h1>
                <p class="text-red-600 text-2xl font-medium mb-6">$89.99</p>

                <div class="mb-6">
                    <p class="text-gray-600 mb-4">
                        This elegant wrap dress features a flattering V-neckline and adjustable waist tie.
                        Made from premium sustainable fabric that drapes beautifully. Perfect for both
                        daytime elegance and evening sophistication.
                    </p>
                    <div class="flex items-center text-sm text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Delivery in 3-5 business days
                    </div>
                </div>

                <!-- Color Selection -->
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Color: <span x-text="selectedColor" class="font-normal"></span></h3>
                    <div class="flex space-x-2">
                        <button
                            x-on:click="selectedColor = 'Black'"
                            class="w-8 h-8 rounded-full bg-gray-900 border-2"
                            :class="{ 'border-pink-500': selectedColor === 'Black' }"
                        ></button>
                        <button
                            x-on:click="selectedColor = 'Dusty Pink'"
                            class="w-8 h-8 rounded-full bg-pink-200 border-2"
                            :class="{ 'border-pink-500': selectedColor === 'Dusty Pink' }"
                        ></button>
                        <button
                            x-on:click="selectedColor = 'Navy'"
                            class="w-8 h-8 rounded-full bg-blue-900 border-2"
                            :class="{ 'border-pink-500': selectedColor === 'Navy' }"
                        ></button>
                    </div>
                </div>

                <!-- Size Selection -->
                <div class="mb-8">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Size: <span x-text="selectedSize" class="font-normal"></span></h3>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="size in ['XS', 'S', 'M', 'L', 'XL']">
                            <button
                                x-on:click="selectedSize = size"
                                class="px-4 py-2 border rounded-md text-sm"
                                :class="{
                                    'border-pink-500 bg-pink-50 text-pink-700': selectedSize === size,
                                    'border-gray-200 hover:border-gray-300': selectedSize !== size
                                }"
                                x-text="size"
                            ></button>
                        </template>
                    </div>
                </div>

                <!-- Quantity and Buttons -->
                <div class="flex items-center mb-8">
                    <div class="mr-6">
                        <label class="text-sm font-medium text-gray-700 mb-1 block">Quantity</label>
                        <div class="flex border rounded-md">
                            <button
                                x-on:click="quantity > 1 ? quantity-- : null"
                                class="px-3 py-1 text-gray-600 hover:bg-gray-100"
                            >-</button>
                            <span x-text="quantity" class="px-3 py-1 border-x"></span>
                            <button
                                x-on:click="quantity++"
                                class="px-3 py-1 text-gray-600 hover:bg-gray-100"
                            >+</button>
                        </div>
                    </div>
                    <button class="bg-red-600 hover:bg-pink-600 text-white px-8 py-3 rounded-md font-medium transition mr-4">
                        Add to Cart
                    </button>
                    <button class="border border-gray-300 hover:bg-gray-50 px-4 py-3 rounded-md font-medium transition">
                        Back to Gallery
                    </button>
                </div>

                <!-- Product Details -->
                <div class="border-t border-gray-100 pt-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Details</h3>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li>• 100% Sustainable Viscose</li>
                        <li>• Machine wash cold</li>
                        <li>• Model is 5'9" wearing size S</li>

                    </ul>
                </div>
            </div>
        </div>


        <!-- You May Also Like
        <div class="mt-20">
            <h2 class="text-2xl font-bold text-gray-800 mb-8">You May Also Like</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <!-- Product 1
                <div class="group">
                    <div class="bg-gray-50 rounded-lg overflow-hidden mb-3">
                        <img src="/images/related-1.jpg" alt="Silk Blouse" class="w-full h-auto group-hover:opacity-90 transition">
                    </div>
                    <h3 class="font-medium text-gray-800">Silk Blouse</h3>
                    <p class="text-red-600">$65.00</p>
                </div>
                <!-- Product 2
                <div class="group">
                    <div class="bg-gray-50 rounded-lg overflow-hidden mb-3">
                        <img src="/images/related-2.jpg" alt="Wool Coat" class="w-full h-auto group-hover:opacity-90 transition">
                    </div>
                    <h3 class="font-medium text-gray-800">Wool Coat</h3>
                    <p class="text-red-600">$129.00</p>
                </div>
                <!-- Product 3
                <div class="group">
                    <div class="bg-gray-50 rounded-lg overflow-hidden mb-3">
                        <img src="/images/related-3.jpg" alt="Linen Jumpsuit" class="w-full h-auto group-hover:opacity-90 transition">
                    </div>
                    <h3 class="font-medium text-gray-800">Linen Jumpsuit</h3>
                    <p class="text-red-600">$78.00</p>
                </div>
                <!-- Product 4
                <div class="group">
                    <div class="bg-gray-50 rounded-lg overflow-hidden mb-3">
                        <img src="/images/related-4.jpg" alt="Cashmere Sweater" class="w-full h-auto group-hover:opacity-90 transition">
                    </div>
                    <h3 class="font-medium text-gray-800">Cashmere Sweater</h3>
                    <p class="text-red-600">$95.00</p>
                </div>
            </div>-->
        </div>
    </main>
      @include('layouts.footer')
</body>
</html>
