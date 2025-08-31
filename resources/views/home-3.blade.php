@extends('layouts.app')

@section('content')
    <!-- Hero Section with Video Background -->
    <section class="relative h-screen overflow-hidden mt-16">
        <!-- Video Background -->
        <div class="video-container">
            <video autoplay muted loop playsinline poster="poster.jpg" class="w-full h-full object-cover blur-sm">
                <source src="videos/workfit.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>

        <!-- Overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-0"></div>

        <!-- Hero Content -->
        <div class="relative z-10 h-full flex items-center justify-center hero-content">
            <div class="text-center text-white px-4">
                <h1 class="text-4xl md:text-6xl font-bold mb-4 slide-in">WORKFIT</h1>
                <p class="text-xl md:text-2xl mb-8 max-w-2xl mx-auto fade-in">Premium activewear designed for performance and style</p>
                <div class="flex flex-col sm:flex-row justify-center gap-4 fade-in">
                    <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 transition-colors">
                        SHOP WOMEN
                    </button>
                    <button class="bg-white hover:bg-gray-100 text-black font-bold py-3 px-8 transition-colors">
                        SHOP MEN
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Categories -->
    <section class="py-16 px-4">
        <div class="container mx-auto">
            <h2 class="text-3xl font-bold text-center mb-12 animate-on-scroll text-red-600">Featured Products</h2>
            <div class="container mx-auto px-4 py-10">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                  @foreach ($products as $product)


                  <!-- Card -->
                  <div
                    class="bg-white shadow-md rounded-lg overflow-hidden cursor-pointer group h-96"
                    x-data="{ hover: false }"
                    @mouseenter="hover = true"
                    @mouseleave="hover = false"
                  >
                    <!-- Image -->
                    <div class="relative h-64">
                      <img
                        :class="hover ? 'opacity-0' : 'opacity-100'"
                        src="{{$product->getFirstMediaUrl('main_image', 'medium');}}"
                        alt="{{ $product->name }}"
                        class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300"
                      >
                      <img
                        :class="hover ? 'opacity-100' : 'opacity-0'"
                        src="{{$product->getFirstMediaUrl('product_images', 'medium');}}"
                        alt="{{ $product->name }}"
                        class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300"
                      >
                    </div>

                    <!-- Content -->
                    <div class="p-4 text-center">
                      <h3 class="text-sm font-medium text-gray-700">Tech Essential™ Relaxed Tee - Faded Grey</h3>

                      <!-- Price / Sizes -->
                      <div class="mt-2">
                        <p
                          x-show="!hover"
                          class="text-gray-900 font-semibold"
                          x-transition
                        >
                         {{$product->price}}
                        </p>

                        <div
                          x-show="hover"
                          class="flex justify-center gap-2 text-xs font-medium text-gray-600"
                          x-transition
                        >
                        @foreach ($product->variants as $variant)
                          <span class="border border-gray-300 px-2 py-1 rounded">{{ $variant->size }}</span>
                          @endforeach
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- /Card -->
                  @endforeach
                  <!-- Repeat more cards as needed -->

                </div>
              </div>


       <!--     <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Category 1
                <div class="relative overflow-hidden rounded-lg group animate-on-scroll">
                    <img src="https://picsum.photos/seed/workfit1/400/500.jpg" alt="Tops" class="w-full h-full object-cover hover-zoom">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="#" class="text-white font-bold text-xl">MAN</a>
                    </div>
                </div>

                <!-- Category 2
                <div class="relative overflow-hidden rounded-lg group animate-on-scroll">
                    <img src="https://picsum.photos/seed/workfit2/400/500.jpg" alt="Bottoms" class="w-full h-full object-cover hover-zoom">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="#" class="text-white font-bold text-xl capitalize">Women</a>
                    </div>
                </div>

                <!-- Category 3
                <div class="relative overflow-hidden rounded-lg group animate-on-scroll">
                    <img src="https://picsum.photos/seed/workfit3/400/500.jpg" alt="Accessories" class="w-full h-full object-cover hover-zoom">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="#" class="text-white font-bold text-xl">ACCESSORIES</a>
                    </div>
                </div>

                <!-- Category 4
                <div class="relative overflow-hidden rounded-lg group animate-on-scroll">
                    <img src="https://picsum.photos/seed/workfit4/400/500.jpg" alt="Sale" class="w-full h-full object-cover hover-zoom">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="#" class="text-white font-bold text-xl">SALE</a>
                    </div>
                </div>
            </div>-->
        </div>
    </section>

    <!-- Full-width Lifestyle Banner -->
    <section class="relative h-96 overflow-hidden animate-on-scroll">
        <img src="https://picsum.photos/seed/workfit-banner1/1920/400.jpg" alt="Lifestyle Banner" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center">
            <div class="text-center text-white px-4">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">SUMMER COLLECTION</h2>
                <p class="text-xl mb-6 max-w-2xl mx-auto">Stay cool and stylish with our latest summer essentials</p>
                <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 transition-colors">
                    SHOP NOW
                </button>
            </div>
        </div>
    </section>

    <!-- Product Grid - Women's Collection -->
    <section class="py-16 px-4">
        <div class="container mx-auto">
            <h2 class="text-3xl font-bold text-center mb-4 animate-on-scroll">WOMEN'S COLLECTION</h2>
            <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto animate-on-scroll">Discover our range of premium activewear designed for performance and style</p>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                <!-- Product 1 -->
                <div class="animate-on-scroll">
                    <div class="product-image-container relative mb-4">
                        <img src="https://picsum.photos/seed/women1/300/400.jpg" alt="Product" class="w-full h-auto product-image-primary">
                        <img src="https://picsum.photos/seed/women1-alt/300/400.jpg" alt="Product Alternate" class="w-full h-auto product-image-secondary absolute top-0 left-0">
                        <button @click="cartItems++" class="absolute bottom-4 right-4 bg-white text-black p-2 rounded-full shadow-md hover:bg-red-600 hover:text-white transition-colors">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                    <h3 class="font-medium mb-1">Performance Tank Top</h3>
                    <p class="text-gray-600">$45.00</p>
                </div>

                <!-- Product 2 -->
                <div class="animate-on-scroll">
                    <div class="product-image-container relative mb-4">
                        <img src="https://picsum.photos/seed/women2/300/400.jpg" alt="Product" class="w-full h-auto product-image-primary">
                        <img src="https://picsum.photos/seed/women2-alt/300/400.jpg" alt="Product Alternate" class="w-full h-auto product-image-secondary absolute top-0 left-0">
                        <button @click="cartItems++" class="absolute bottom-4 right-4 bg-white text-black p-2 rounded-full shadow-md hover:bg-red-600 hover:text-white transition-colors">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                    <h3 class="font-medium mb-1">High-Waist Leggings</h3>
                    <p class="text-gray-600">$65.00</p>
                </div>

                <!-- Product 3 -->
                <div class="animate-on-scroll">
                    <div class="product-image-container relative mb-4">
                        <img src="https://picsum.photos/seed/women3/300/400.jpg" alt="Product" class="w-full h-auto product-image-primary">
                        <img src="https://picsum.photos/seed/women3-alt/300/400.jpg" alt="Product Alternate" class="w-full h-auto product-image-secondary absolute top-0 left-0">
                        <button @click="cartItems++" class="absolute bottom-4 right-4 bg-white text-black p-2 rounded-full shadow-md hover:bg-red-600 hover:text-white transition-colors">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                    <h3 class="font-medium mb-1">Sports Bra</h3>
                    <p class="text-gray-600">$40.00</p>
                </div>

                <!-- Product 4 -->
                <div class="animate-on-scroll">
                    <div class="product-image-container relative mb-4">
                        <img src="https://picsum.photos/seed/women4/300/400.jpg" alt="Product" class="w-full h-auto product-image-primary">
                        <img src="https://picsum.photos/seed/women4-alt/300/400.jpg" alt="Product Alternate" class="w-full h-auto product-image-secondary absolute top-0 left-0">
                        <button @click="cartItems++" class="absolute bottom-4 right-4 bg-white text-black p-2 rounded-full shadow-md hover:bg-red-600 hover:text-white transition-colors">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                    <h3 class="font-medium mb-1">Running Shorts</h3>
                    <p class="text-gray-600">$35.00</p>
                </div>

                <!-- Product 5 -->
                <div class="animate-on-scroll">
                    <div class="product-image-container relative mb-4">
                        <img src="https://picsum.photos/seed/women5/300/400.jpg" alt="Product" class="w-full h-auto product-image-primary">
                        <img src="https://picsum.photos/seed/women5-alt/300/400.jpg" alt="Product Alternate" class="w-full h-auto product-image-secondary absolute top-0 left-0">
                        <button @click="cartItems++" class="absolute bottom-4 right-4 bg-white text-black p-2 rounded-full shadow-md hover:bg-red-600 hover:text-white transition-colors">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                    <h3 class="font-medium mb-1">Hoodie</h3>
                    <p class="text-gray-600">$55.00</p>
                </div>
            </div>

            <div class="text-center mt-12 animate-on-scroll">
                <button class="border-2 border-black hover:bg-black hover:text-white font-bold py-3 px-8 transition-colors">
                    VIEW ALL WOMEN'S
                </button>
            </div>
        </div>
    </section>

    <!-- Three Image Block Section -->
    <section class="py-16 px-4 bg-gray-100">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- RUN Block -->
                <div class="relative overflow-hidden rounded-lg group animate-on-scroll">
                    <img src="https://picsum.photos/seed/run/600/400.jpg" alt="Run" class="w-full h-full object-cover hover-zoom">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex flex-col items-center justify-center">
                        <h3 class="text-white text-3xl font-bold mb-2">RUN</h3>
                        <p class="text-white text-center px-4 mb-4">Lightweight gear for your daily runs</p>
                        <a href="#" class="text-white font-medium underline hover:text-red-400 transition-colors">SHOP NOW</a>
                    </div>
                </div>

                <!-- TRAIN Block -->
                <div class="relative overflow-hidden rounded-lg group animate-on-scroll">
                    <img src="https://picsum.photos/seed/train/600/400.jpg" alt="Train" class="w-full h-full object-cover hover-zoom">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex flex-col items-center justify-center">
                        <h3 class="text-white text-3xl font-bold mb-2">TRAIN</h3>
                        <p class="text-white text-center px-4 mb-4">Durable apparel for intense workouts</p>
                        <a href="#" class="text-white font-medium underline hover:text-red-400 transition-colors">SHOP NOW</a>
                    </div>
                </div>

                <!-- REC Block -->
                <div class="relative overflow-hidden rounded-lg group animate-on-scroll">
                    <img src="https://picsum.photos/seed/rec/600/400.jpg" alt="Rec" class="w-full h-full object-cover hover-zoom">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex flex-col items-center justify-center">
                        <h3 class="text-white text-3xl font-bold mb-2">REC</h3>
                        <p class="text-white text-center px-4 mb-4">Comfortable styles for recovery days</p>
                        <a href="#" class="text-white font-medium underline hover:text-red-400 transition-colors">SHOP NOW</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Grid - Men's Collection -->
    <section class="py-16 px-4">
        <div class="container mx-auto">
            <h2 class="text-3xl font-bold text-center mb-4 animate-on-scroll">MEN'S COLLECTION</h2>
            <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto animate-on-scroll">Engineered for performance, designed for style</p>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                <!-- Product 1 -->
                <div class="animate-on-scroll">
                    <div class="product-image-container relative mb-4">
                        <img src="https://picsum.photos/seed/men1/300/400.jpg" alt="Product" class="w-full h-auto product-image-primary">
                        <img src="https://picsum.photos/seed/men1-alt/300/400.jpg" alt="Product Alternate" class="w-full h-auto product-image-secondary absolute top-0 left-0">
                        <button @click="cartItems++" class="absolute bottom-4 right-4 bg-white text-black p-2 rounded-full shadow-md hover:bg-red-600 hover:text-white transition-colors">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                    <h3 class="font-medium mb-1">Performance T-Shirt</h3>
                    <p class="text-gray-600">$40.00</p>
                </div>

                <!-- Product 2 -->
                <div class="animate-on-scroll">
                    <div class="product-image-container relative mb-4">
                        <img src="https://picsum.photos/seed/men2/300/400.jpg" alt="Product" class="w-full h-auto product-image-primary">
                        <img src="https://picsum.photos/seed/men2-alt/300/400.jpg" alt="Product Alternate" class="w-full h-auto product-image-secondary absolute top-0 left-0">
                        <button @click="cartItems++" class="absolute bottom-4 right-4 bg-white text-black p-2 rounded-full shadow-md hover:bg-red-600 hover:text-white transition-colors">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                    <h3 class="font-medium mb-1">Training Shorts</h3>
                    <p class="text-gray-600">$45.00</p>
                </div>

                <!-- Product 3 -->
                <div class="animate-on-scroll">
                    <div class="product-image-container relative mb-4">
                        <img src="https://picsum.photos/seed/men3/300/400.jpg" alt="Product" class="w-full h-auto product-image-primary">
                        <img src="https://picsum.photos/seed/men3-alt/300/400.jpg" alt="Product Alternate" class="w-full h-auto product-image-secondary absolute top-0 left-0">
                        <button @click="cartItems++" class="absolute bottom-4 right-4 bg-white text-black p-2 rounded-full shadow-md hover:bg-red-600 hover:text-white transition-colors">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                    <h3 class="font-medium mb-1">Hoodie</h3>
                    <p class="text-gray-600">$60.00</p>
                </div>

                <!-- Product 4 -->
                <div class="animate-on-scroll">
                    <div class="product-image-container relative mb-4">
                        <img src="https://picsum.photos/seed/men4/300/400.jpg" alt="Product" class="w-full h-auto product-image-primary">
                        <img src="https://picsum.photos/seed/men4-alt/300/400.jpg" alt="Product Alternate" class="w-full h-auto product-image-secondary absolute top-0 left-0">
                        <button @click="cartItems++" class="absolute bottom-4 right-4 bg-white text-black p-2 rounded-full shadow-md hover:bg-red-600 hover:text-white transition-colors">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                    <h3 class="font-medium mb-1">Joggers</h3>
                    <p class="text-gray-600">$55.00</p>
                </div>

                <!-- Product 5 -->
                <div class="animate-on-scroll">
                    <div class="product-image-container relative mb-4">
                        <img src="https://picsum.photos/seed/men5/300/400.jpg" alt="Product" class="w-full h-auto product-image-primary">
                        <img src="https://picsum.photos/seed/men5-alt/300/400.jpg" alt="Product Alternate" class="w-full h-auto product-image-secondary absolute top-0 left-0">
                        <button @click="cartItems++" class="absolute bottom-4 right-4 bg-white text-black p-2 rounded-full shadow-md hover:bg-red-600 hover:text-white transition-colors">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                    <h3 class="font-medium mb-1">Tank Top</h3>
                    <p class="text-gray-600">$35.00</p>
                </div>
            </div>

            <div class="text-center mt-12 animate-on-scroll">
                <button class="border-2 border-black hover:bg-black hover:text-white font-bold py-3 px-8 transition-colors">
                    VIEW ALL MEN'S
                </button>
            </div>
        </div>
    </section>

    <!-- Full-width Lifestyle Banner 2 -->
    <section class="relative h-96 overflow-hidden animate-on-scroll">
        <img src="https://picsum.photos/seed/workfit-banner2/1920/400.jpg" alt="Lifestyle Banner" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center">
            <div class="text-center text-white px-4">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">NEW ARRIVALS</h2>
                <p class="text-xl mb-6 max-w-2xl mx-auto">Be the first to shop our latest collection</p>
                <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 transition-colors">
                    SHOP NOW
                </button>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-16 px-4">
        <div class="container mx-auto">
            <h2 class="text-3xl font-bold text-center mb-4 animate-on-scroll">FEATURED PRODUCTS</h2>
            <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto animate-on-scroll">Our top picks for the season</p>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                <!-- Product 1 -->
                <div class="animate-on-scroll">
                    <div class="product-image-container relative mb-4">
                        <img src="https://picsum.photos/seed/featured1/300/400.jpg" alt="Product" class="w-full h-auto product-image-primary">
                        <img src="https://picsum.photos/seed/featured1-alt/300/400.jpg" alt="Product Alternate" class="w-full h-auto product-image-secondary absolute top-0 left-0">
                        <div class="absolute top-4 left-4 bg-red-600 text-white text-xs font-bold px-2 py-1">NEW</div>
                        <button @click="cartItems++" class="absolute bottom-4 right-4 bg-white text-black p-2 rounded-full shadow-md hover:bg-red-600 hover:text-white transition-colors">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                    <h3 class="font-medium mb-1">Ultra Leggings</h3>
                    <p class="text-gray-600">$75.00</p>
                </div>

                <!-- Product 2 -->
                <div class="animate-on-scroll">
                    <div class="product-image-container relative mb-4">
                        <img src="https://picsum.photos/seed/featured2/300/400.jpg" alt="Product" class="w-full h-auto product-image-primary">
                        <img src="https://picsum.photos/seed/featured2-alt/300/400.jpg" alt="Product Alternate" class="w-full h-auto product-image-secondary absolute top-0 left-0">
                        <div class="absolute top-4 left-4 bg-red-600 text-white text-xs font-bold px-2 py-1">BESTSELLER</div>
                        <button @click="cartItems++" class="absolute bottom-4 right-4 bg-white text-black p-2 rounded-full shadow-md hover:bg-red-600 hover:text-white transition-colors">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                    <h3 class="font-medium mb-1">Flex Tank</h3>
                    <p class="text-gray-600">$45.00</p>
                </div>

                <!-- Product 3 -->
                <div class="animate-on-scroll">
                    <div class="product-image-container relative mb-4">
                        <img src="https://picsum.photos/seed/featured3/300/400.jpg" alt="Product" class="w-full h-auto product-image-primary">
                        <img src="https://picsum.photos/seed/featured3-alt/300/400.jpg" alt="Product Alternate" class="w-full h-auto product-image-secondary absolute top-0 left-0">
                        <div class="absolute top-4 left-4 bg-red-600 text-white text-xs font-bold px-2 py-1">LIMITED</div>
                        <button @click="cartItems++" class="absolute bottom-4 right-4 bg-white text-black p-2 rounded-full shadow-md hover:bg-red-600 hover:text-white transition-colors">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                    <h3 class="font-medium mb-1">Pro Shorts</h3>
                    <p class="text-gray-600">$50.00</p>
                </div>

                <!-- Product 4 -->
                <div class="animate-on-scroll">
                    <div class="product-image-container relative mb-4">
                        <img src="https://picsum.photos/seed/featured4/300/400.jpg" alt="Product" class="w-full h-auto product-image-primary">
                        <img src="https://picsum.photos/seed/featured4-alt/300/400.jpg" alt="Product Alternate" class="w-full h-auto product-image-secondary absolute top-0 left-0">
                        <button @click="cartItems++" class="absolute bottom-4 right-4 bg-white text-black p-2 rounded-full shadow-md hover:bg-red-600 hover:text-white transition-colors">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                    <h3 class="font-medium mb-1">Training Jacket</h3>
                    <p class="text-gray-600">$85.00</p>
                </div>

                <!-- Product 5 -->
                <div class="animate-on-scroll">
                    <div class="product-image-container relative mb-4">
                        <img src="https://picsum.photos/seed/featured5/300/400.jpg" alt="Product" class="w-full h-auto product-image-primary">
                        <img src="https://picsum.photos/seed/featured5-alt/300/400.jpg" alt="Product Alternate" class="w-full h-auto product-image-secondary absolute top-0 left-0">
                        <button @click="cartItems++" class="absolute bottom-4 right-4 bg-white text-black p-2 rounded-full shadow-md hover:bg-red-600 hover:text-white transition-colors">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                    <h3 class="font-medium mb-1">Sports Bra</h3>
                    <p class="text-gray-600">$40.00</p>
                </div>
            </div>
        </div>
    </section>

    <!-- App Download Section -->
    <section class="py-16 px-4 bg-black text-white">
        <div class="container mx-auto">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="mb-8 md:mb-0 md:w-1/2 animate-on-scroll">
                    <h2 class="text-3xl font-bold mb-4">GET THE WORKFIT APP</h2>
                    <p class="mb-6 max-w-lg">Exclusive offers, early access to new collections, and personalized recommendations.</p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button class="bg-white text-black font-bold py-3 px-6 flex items-center justify-center hover:bg-gray-200 transition-colors">
                            <i class="fab fa-apple text-2xl mr-2"></i>
                            <div class="text-left">
                                <div class="text-xs">Download on the</div>
                                <div class="text-sm">App Store</div>
                            </div>
                        </button>
                        <button class="bg-white text-black font-bold py-3 px-6 flex items-center justify-center hover:bg-gray-200 transition-colors">
                            <i class="fab fa-google-play text-2xl mr-2"></i>
                            <div class="text-left">
                                <div class="text-xs">Get it on</div>
                                <div class="text-sm">Google Play</div>
                            </div>
                        </button>
                    </div>
                </div>
                <div class="md:w-1/2 flex justify-center animate-on-scroll">
                    <img src="https://picsum.photos/seed/workfit-app/300/600.jpg" alt="Workfit App" class="h-auto max-h-80">
                </div>
            </div>
        </div>
    </section>
@endsection
