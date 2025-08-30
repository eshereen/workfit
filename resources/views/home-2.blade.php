<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workfit - Premium Activewear</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom styles for animations and transitions */
        .hover-zoom {
            transition: transform 0.3s ease;
        }
        .hover-zoom:hover {
            transform: scale(1.05);
        }
        .product-image-container {
            overflow: hidden;
        }
        .product-image-secondary {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .product-image-container:hover .product-image-primary {
            opacity: 0;
        }
        .product-image-container:hover .product-image-secondary {
            opacity: 1;
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .slide-in {
            animation: slideIn 0.7s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #FF0000;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #cc0000;
        }
    </style>
</head>
<body class="bg-white text-black font-sans antialiased" x-data="{
    mobileMenuOpen: false,
    currentSlide: 0,
    autoplay: true,
    cartItems: 0,
    scrolled: false
}" x-init="() => {
    // Initialize scroll listener
    window.addEventListener('scroll', () => {
        scrolled = window.scrollY > 10;
    });

    // Auto-play slider
    setInterval(() => {
        if (autoplay) {
            currentSlide = (currentSlide + 1) % 3;
        }
    }, 5000);

    // Intersection Observer for animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        observer.observe(el);
    });
}">

    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
            :class="scrolled ? 'bg-white shadow-md py-2' : 'bg-transparent py-4'">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="#" class="text-2xl font-bold">WORKFIT</a>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex space-x-8">
                    <a href="#" class="font-medium hover:text-red-600 transition-colors">NEW ARRIVALS</a>
                    <a href="#" class="font-medium hover:text-red-600 transition-colors">WOMEN</a>
                    <a href="#" class="font-medium hover:text-red-600 transition-colors">MEN</a>
                    <a href="#" class="font-medium hover:text-red-600 transition-colors">COLLECTIONS</a>
                    <a href="#" class="font-medium hover:text-red-600 transition-colors">SALE</a>
                </nav>

                <!-- Icons -->
                <div class="flex items-center space-x-4">
                    <button class="hover:text-red-600 transition-colors">
                        <i class="fas fa-search text-xl"></i>
                    </button>
                    <button class="hover:text-red-600 transition-colors">
                        <i class="fas fa-user text-xl"></i>
                    </button>
                    <button class="relative hover:text-red-600 transition-colors">
                        <i class="fas fa-shopping-bag text-xl"></i>
                        <span x-show="cartItems > 0"
                              x-text="cartItems"
                              class="absolute -top-2 -right-2 bg-red-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"></span>
                    </button>

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2"
             class="md:hidden bg-white py-4 px-4 shadow-lg">
            <nav class="flex flex-col space-y-4">
                <a href="#" class="font-medium hover:text-red-600 transition-colors">NEW ARRIVALS</a>
                <a href="#" class="font-medium hover:text-red-600 transition-colors">WOMEN</a>
                <a href="#" class="font-medium hover:text-red-600 transition-colors">MEN</a>
                <a href="#" class="font-medium hover:text-red-600 transition-colors">COLLECTIONS</a>
                <a href="#" class="font-medium hover:text-red-600 transition-colors">SALE</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section with Video Background -->
    <section class="relative h-screen overflow-hidden mt-16">
        <!-- Video Background -->
        <video autoplay muted loop class="absolute inset-0 w-full h-full object-cover">
            <source src="https://assets.mixkit.co/videos/preview/mixkit-group-of-friends-jogging-in-a-park-4103-large.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <!-- Overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>

        <!-- Hero Content -->
        <div class="relative z-10 h-full flex items-center justify-center">
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

        <!-- Slider Indicators -->
        <div class="absolute bottom-8 left-0 right-0 flex justify-center space-x-2">
            <button @click="currentSlide = 0; autoplay = false"
                    :class="currentSlide === 0 ? 'bg-white' : 'bg-white bg-opacity-50'"
                    class="w-3 h-3 rounded-full"></button>
            <button @click="currentSlide = 1; autoplay = false"
                    :class="currentSlide === 1 ? 'bg-white' : 'bg-white bg-opacity-50'"
                    class="w-3 h-3 rounded-full"></button>
            <button @click="currentSlide = 2; autoplay = false"
                    :class="currentSlide === 2 ? 'bg-white' : 'bg-white bg-opacity-50'"
                    class="w-3 h-3 rounded-full"></button>
        </div>
    </section>

    <!-- Featured Categories -->
    <section class="py-16 px-4">
        <div class="container mx-auto">
            <h2 class="text-3xl font-bold text-center mb-12 animate-on-scroll">SHOP BY CATEGORY</h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Category 1 -->
                <div class="relative overflow-hidden rounded-lg group animate-on-scroll">
                    <img src="https://picsum.photos/seed/workfit1/400/500.jpg" alt="Tops" class="w-full h-full object-cover hover-zoom">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="#" class="text-white font-bold text-xl">TOPS</a>
                    </div>
                </div>

                <!-- Category 2 -->
                <div class="relative overflow-hidden rounded-lg group animate-on-scroll">
                    <img src="https://picsum.photos/seed/workfit2/400/500.jpg" alt="Bottoms" class="w-full h-full object-cover hover-zoom">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="#" class="text-white font-bold text-xl">BOTTOMS</a>
                    </div>
                </div>

                <!-- Category 3 -->
                <div class="relative overflow-hidden rounded-lg group animate-on-scroll">
                    <img src="https://picsum.photos/seed/workfit3/400/500.jpg" alt="Accessories" class="w-full h-full object-cover hover-zoom">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="#" class="text-white font-bold text-xl">ACCESSORIES</a>
                    </div>
                </div>

                <!-- Category 4 -->
                <div class="relative overflow-hidden rounded-lg group animate-on-scroll">
                    <img src="https://picsum.photos/seed/workfit4/400/500.jpg" alt="Sale" class="w-full h-full object-cover hover-zoom">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="#" class="text-white font-bold text-xl">SALE</a>
                    </div>
                </div>
            </div>
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

    <!-- Footer -->
    <footer class="bg-white py-12 px-4 border-t border-gray-200">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Brand -->
                <div class="animate-on-scroll">
                    <h3 class="text-2xl font-bold mb-4">WORKFIT</h3>
                    <p class="text-gray-600 mb-4">Premium activewear for performance and style.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-600 hover:text-red-600 transition-colors">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-600 hover:text-red-600 transition-colors">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-600 hover:text-red-600 transition-colors">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-600 hover:text-red-600 transition-colors">
                            <i class="fab fa-youtube text-xl"></i>
                        </a>
                    </div>
                </div>

                <!-- Shop -->
                <div class="animate-on-scroll">
                    <h4 class="font-bold mb-4">SHOP</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-600 hover:text-red-600 transition-colors">Women's</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-red-600 transition-colors">Men's</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-red-600 transition-colors">New Arrivals</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-red-600 transition-colors">Sale</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-red-600 transition-colors">Collections</a></li>
                    </ul>
                </div>

                <!-- Support -->
                <div class="animate-on-scroll">
                    <h4 class="font-bold mb-4">SUPPORT</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-600 hover:text-red-600 transition-colors">Contact Us</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-red-600 transition-colors">FAQs</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-red-600 transition-colors">Shipping & Returns</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-red-600 transition-colors">Size Guide</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-red-600 transition-colors">Warranty</a></li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div class="animate-on-scroll">
                    <h4 class="font-bold mb-4">JOIN OUR NEWSLETTER</h4>
                    <p class="text-gray-600 mb-4">Subscribe to receive updates, access to exclusive deals, and more.</p>
                    <form class="flex">
                        <input type="email" placeholder="Enter your email" class="flex-grow px-4 py-2 border border-gray-300 focus:outline-none focus:border-red-600">
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 hover:bg-red-700 transition-colors">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-8 text-center text-gray-600 animate-on-scroll">
                <p>&copy; 2023 WORKFIT. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Additional JavaScript for enhanced functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Form submission handling (only for non-Livewire forms)
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                // Skip Livewire forms
                if (form.hasAttribute('wire:submit') || form.hasAttribute('wire:submit.prevent')) {
                    return;
                }

                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    // Add your form submission logic here
                    alert('Thank you for your submission!');
                    form.reset();
                });
            });
        });
    </script>
</body>
</html>
