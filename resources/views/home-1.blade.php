<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fashion Store - E-commerce Website</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1a202c',
                        secondary: '#4a5568',
                        accent: '#dd6b20',
                        light: '#f7fafc',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        /* Custom styles for smooth transitions */
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
        .banner-overlay {
            background: linear-gradient(to right, rgba(0,0,0,0.7), rgba(0,0,0,0.3));
        }
        .category-banner {
            transition: all 0.3s ease;
        }
        .category-banner:hover {
            transform: scale(1.02);
        }
    </style>
</head>
<body class="font-sans text-primary" x-data="{
    mobileMenuOpen: false,
    cartOpen: false,
    cartItems: 3,
    activeCategory: 'all',
    categories: ['all', 'men', 'women', 'summer', 'new']
}">
    <!-- Header & Navigation -->
    <header class="bg-white shadow-sm sticky top-0 z-50" x-data="{ scrolled: false }"
            x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 10 })"
            :class="{ 'shadow-md': scrolled }">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Mobile menu button -->
                <button class="md:hidden text-primary" @click="mobileMenuOpen = !mobileMenuOpen">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <!-- Logo -->
                <div class="flex-1 md:flex-none text-center md:text-left">
                    <a href="#" class="text-2xl font-bold text-primary">WorkFit</a>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex space-x-8 flex-1 justify-center">
                    <a href="#" class="font-medium hover:text-accent transition-colors" @click="activeCategory = 'all'">Home</a>
                    <a href="#" class="font-medium hover:text-accent transition-colors" @click="activeCategory = 'men'">Men</a>
                    <a href="#" class="font-medium hover:text-accent transition-colors" @click="activeCategory = 'women'">Women</a>
                    <a href="#" class="font-medium hover:text-accent transition-colors" @click="activeCategory = 'summer'">Summer</a>
                    <a href="#" class="font-medium hover:text-accent transition-colors" @click="activeCategory = 'new'">New</a>
                </nav>

                <!-- Icons -->
                <div class="flex items-center space-x-4">
                    <button class="text-primary hover:text-accent transition-colors">
                        <i class="fas fa-search text-xl"></i>
                    </button>
                    <button class="text-primary hover:text-accent transition-colors">
                        <i class="fas fa-user text-xl"></i>
                    </button>
                    <div class="relative">
                        <button class="text-primary hover:text-accent transition-colors" @click="cartOpen = !cartOpen">
                            <i class="fas fa-shopping-bag text-xl"></i>
                            <span x-show="cartItems > 0" x-text="cartItems" class="absolute -top-2 -right-2 bg-accent text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"></span>
                        </button>

                        <!-- Cart Dropdown -->
                        <div x-show="cartOpen" x-transition class="absolute right-0 mt-2 w-80 bg-white shadow-lg rounded-lg p-4 z-50" @click.away="cartOpen = false">
                            <h3 class="font-bold text-lg mb-4">Your Cart</h3>
                            <div class="space-y-4">
                                <div class="flex items-center space-x-3">
                                    <img src="https://picsum.photos/seed/product1/60/60.jpg" alt="Product" class="w-16 h-16 object-cover rounded">
                                    <div class="flex-1">
                                        <h4 class="font-medium">Summer Dress</h4>
                                        <p class="text-secondary">$59.99</p>
                                    </div>
                                    <button class="text-secondary hover:text-primary">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <img src="https://picsum.photos/seed/product2/60/60.jpg" alt="Product" class="w-16 h-16 object-cover rounded">
                                    <div class="flex-1">
                                        <h4 class="font-medium">Men's T-Shirt</h4>
                                        <p class="text-secondary">$29.99</p>
                                    </div>
                                    <button class="text-secondary hover:text-primary">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t">
                                <div class="flex justify-between mb-4">
                                    <span class="font-medium">Total:</span>
                                    <span class="font-bold">$89.98</span>
                                </div>
                                <button class="w-full bg-primary text-white py-2 rounded-md hover:bg-secondary transition-colors">
                                    Checkout
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div x-show="mobileMenuOpen" x-transition class="md:hidden py-4 border-t">
                <nav class="flex flex-col space-y-3">
                    <a href="#" class="font-medium hover:text-accent transition-colors" @click="mobileMenuOpen = false; activeCategory = 'all'">Home</a>
                    <a href="#" class="font-medium hover:text-accent transition-colors" @click="mobileMenuOpen = false; activeCategory = 'men'">Men</a>
                    <a href="#" class="font-medium hover:text-accent transition-colors" @click="mobileMenuOpen = false; activeCategory = 'women'">Women</a>
                    <a href="#" class="font-medium hover:text-accent transition-colors" @click="mobileMenuOpen = false; activeCategory = 'summer'">Summer</a>
                    <a href="#" class="font-medium hover:text-accent transition-colors" @click="mobileMenuOpen = false; activeCategory = 'new'">New</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative h-[500px] md:h-[600px] overflow-hidden">
<iframe src="https://www.facebook.com/plugins/video.php?height=476&href=https%3A%2F%2Fwww.facebook.com%2Freel%2F539498605436475%2F&show_text=false&width=267&t=0" width="267" height="476" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share" allowFullScreen="true"></iframe>
        <div class="absolute inset-0 banner-overlay flex items-center">
            <div class="container mx-auto px-4">
                <div class="max-w-lg">
                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Summer Collection 2023</h1>
                    <p class="text-white text-lg mb-8">Discover the latest trends for the summer season</p>
                    <div class="flex flex-wrap gap-4">
                        <button class="bg-white text-primary px-6 py-3 rounded-md font-medium hover:bg-light transition-colors">
                            Shop Women
                        </button>
                        <button class="bg-transparent border-2 border-white text-white px-6 py-3 rounded-md font-medium hover:bg-white hover:text-primary transition-colors">
                            Shop Men
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Categories Filter -->
    <section class="container mx-auto px-4 py-8">
        <div class="flex overflow-x-auto pb-2 space-x-4">
            <button @click="activeCategory = 'all'" :class="{'bg-primary text-white': activeCategory === 'all'}" class="px-4 py-2 rounded-full border border-primary whitespace-nowrap">
                All Products
            </button>
            <button @click="activeCategory = 'men'" :class="{'bg-primary text-white': activeCategory === 'men'}" class="px-4 py-2 rounded-full border border-primary whitespace-nowrap">
                Men
            </button>
            <button @click="activeCategory = 'women'" :class="{'bg-primary text-white': activeCategory === 'women'}" class="px-4 py-2 rounded-full border border-primary whitespace-nowrap">
                Women
            </button>
            <button @click="activeCategory = 'summer'" :class="{'bg-primary text-white': activeCategory === 'summer'}" class="px-4 py-2 rounded-full border border-primary whitespace-nowrap">
                Summer
            </button>
            <button @click="activeCategory = 'new'" :class="{'bg-primary text-white': activeCategory === 'new'}" class="px-4 py-2 rounded-full border border-primary whitespace-nowrap">
                New Arrivals
            </button>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold">Featured Products</h2>
                <p class="text-secondary">Our best-selling products</p>
            </div>
            <button class="text-primary font-medium hover:text-accent transition-colors">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <!-- Product Card 1 -->
            <div class="product-card bg-white rounded-lg overflow-hidden shadow-md" data-category="women,summer,new" x-show="activeCategory === 'all' || activeCategory === 'women' || activeCategory === 'summer' || activeCategory === 'new'">
                <div class="relative overflow-hidden">
                    <img src="https://picsum.photos/seed/product3/400/500.jpg" alt="Summer Dress" class="w-full h-64 object-cover">
                    <div class="absolute top-2 right-2 bg-accent text-white text-xs px-2 py-1 rounded">New</div>
                </div>
                <div class="p-4">
                    <h3 class="font-medium mb-1">Summer Floral Dress</h3>
                    <p class="text-secondary mb-2">Women's Collection</p>
                    <div class="flex justify-between items-center">
                        <span class="font-bold">$59.99</span>
                        <button class="text-primary hover:text-accent">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Card 2 -->
            <div class="product-card bg-white rounded-lg overflow-hidden shadow-md" data-category="men,new" x-show="activeCategory === 'all' || activeCategory === 'men' || activeCategory === 'new'">
                <div class="relative overflow-hidden">
                    <img src="https://picsum.photos/seed/product4/400/500.jpg" alt="Men's Shirt" class="w-full h-64 object-cover">
                    <div class="absolute top-2 right-2 bg-accent text-white text-xs px-2 py-1 rounded">New</div>
                </div>
                <div class="p-4">
                    <h3 class="font-medium mb-1">Casual Cotton Shirt</h3>
                    <p class="text-secondary mb-2">Men's Collection</p>
                    <div class="flex justify-between items-center">
                        <span class="font-bold">$39.99</span>
                        <button class="text-primary hover:text-accent">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Card 3 -->
            <div class="product-card bg-white rounded-lg overflow-hidden shadow-md" data-category="women,summer" x-show="activeCategory === 'all' || activeCategory === 'women' || activeCategory === 'summer'">
                <div class="relative overflow-hidden">
                    <img src="https://picsum.photos/seed/product5/400/500.jpg" alt="Women's Sandals" class="w-full h-64 object-cover">
                    <div class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded">-20%</div>
                </div>
                <div class="p-4">
                    <h3 class="font-medium mb-1">Beach Sandals</h3>
                    <p class="text-secondary mb-2">Women's Collection</p>
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="font-bold">$29.99</span>
                            <span class="text-secondary line-through text-sm ml-1">$37.49</span>
                        </div>
                        <button class="text-primary hover:text-accent">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Card 4 -->
            <div class="product-card bg-white rounded-lg overflow-hidden shadow-md" data-category="men,summer" x-show="activeCategory === 'all' || activeCategory === 'men' || activeCategory === 'summer'">
                <div class="relative overflow-hidden">
                    <img src="https://picsum.photos/seed/product6/400/500.jpg" alt="Men's Shorts" class="w-full h-64 object-cover">
                </div>
                <div class="p-4">
                    <h3 class="font-medium mb-1">Linen Shorts</h3>
                    <p class="text-secondary mb-2">Men's Collection</p>
                    <div class="flex justify-between items-center">
                        <span class="font-bold">$49.99</span>
                        <button class="text-primary hover:text-accent">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Promotional Banner -->
    <section class="relative h-[400px] overflow-hidden my-12">
        <img src="https://picsum.photos/seed/promo1/1920/400.jpg" alt="Promotional Banner" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 banner-overlay flex items-center justify-center">
            <div class="text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Summer Sale</h2>
                <p class="text-white text-lg mb-8">Up to 50% off on selected items</p>
                <button class="bg-white text-primary px-6 py-3 rounded-md font-medium hover:bg-light transition-colors">
                    Shop Now
                </button>
            </div>
        </div>
    </section>

    <!-- Men's Collection Section -->
    <section class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold">Men's Collection</h2>
                <p class="text-secondary">Stylish outfits for the modern man</p>
            </div>
            <button class="text-primary font-medium hover:text-accent transition-colors">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <!-- Product Card 5 -->
            <div class="product-card bg-white rounded-lg overflow-hidden shadow-md" data-category="men" x-show="activeCategory === 'all' || activeCategory === 'men'">
                <div class="relative overflow-hidden">
                    <img src="https://picsum.photos/seed/product7/400/500.jpg" alt="Men's Jacket" class="w-full h-64 object-cover">
                </div>
                <div class="p-4">
                    <h3 class="font-medium mb-1">Denim Jacket</h3>
                    <p class="text-secondary mb-2">Men's Collection</p>
                    <div class="flex justify-between items-center">
                        <span class="font-bold">$79.99</span>
                        <button class="text-primary hover:text-accent">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Card 6 -->
            <div class="product-card bg-white rounded-lg overflow-hidden shadow-md" data-category="men" x-show="activeCategory === 'all' || activeCategory === 'men'">
                <div class="relative overflow-hidden">
                    <img src="https://picsum.photos/seed/product8/400/500.jpg" alt="Men's Jeans" class="w-full h-64 object-cover">
                </div>
                <div class="p-4">
                    <h3 class="font-medium mb-1">Slim Fit Jeans</h3>
                    <p class="text-secondary mb-2">Men's Collection</p>
                    <div class="flex justify-between items-center">
                        <span class="font-bold">$59.99</span>
                        <button class="text-primary hover:text-accent">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Card 7 -->
            <div class="product-card bg-white rounded-lg overflow-hidden shadow-md" data-category="men" x-show="activeCategory === 'all' || activeCategory === 'men'">
                <div class="relative overflow-hidden">
                    <img src="https://picsum.photos/seed/product9/400/500.jpg" alt="Men's Sneakers" class="w-full h-64 object-cover">
                </div>
                <div class="p-4">
                    <h3 class="font-medium mb-1">Sport Sneakers</h3>
                    <p class="text-secondary mb-2">Men's Collection</p>
                    <div class="flex justify-between items-center">
                        <span class="font-bold">$89.99</span>
                        <button class="text-primary hover:text-accent">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Card 8 -->
            <div class="product-card bg-white rounded-lg overflow-hidden shadow-md" data-category="men" x-show="activeCategory === 'all' || activeCategory === 'men'">
                <div class="relative overflow-hidden">
                    <img src="https://picsum.photos/seed/product10/400/500.jpg" alt="Men's Sunglasses" class="w-full h-64 object-cover">
                </div>
                <div class="p-4">
                    <h3 class="font-medium mb-1">Classic Sunglasses</h3>
                    <p class="text-secondary mb-2">Men's Collection</p>
                    <div class="flex justify-between items-center">
                        <span class="font-bold">$39.99</span>
                        <button class="text-primary hover:text-accent">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Women's Collection Section -->
    <section class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold">Women's Collection</h2>
                <p class="text-secondary">Trendy fashion for the modern woman</p>
            </div>
            <button class="text-primary font-medium hover:text-accent transition-colors">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <!-- Product Card 9 -->
            <div class="product-card bg-white rounded-lg overflow-hidden shadow-md" data-category="women" x-show="activeCategory === 'all' || activeCategory === 'women'">
                <div class="relative overflow-hidden">
                    <img src="https://picsum.photos/seed/product11/400/500.jpg" alt="Women's Top" class="w-full h-64 object-cover">
                </div>
                <div class="p-4">
                    <h3 class="font-medium mb-1">Floral Blouse</h3>
                    <p class="text-secondary mb-2">Women's Collection</p>
                    <div class="flex justify-between items-center">
                        <span class="font-bold">$49.99</span>
                        <button class="text-primary hover:text-accent">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Card 10 -->
            <div class="product-card bg-white rounded-lg overflow-hidden shadow-md" data-category="women" x-show="activeCategory === 'all' || activeCategory === 'women'">
                <div class="relative overflow-hidden">
                    <img src="https://picsum.photos/seed/product12/400/500.jpg" alt="Women's Skirt" class="w-full h-64 object-cover">
                </div>
                <div class="p-4">
                    <h3 class="font-medium mb-1">Midi Skirt</h3>
                    <p class="text-secondary mb-2">Women's Collection</p>
                    <div class="flex justify-between items-center">
                        <span class="font-bold">$59.99</span>
                        <button class="text-primary hover:text-accent">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Card 11 -->
            <div class="product-card bg-white rounded-lg overflow-hidden shadow-md" data-category="women" x-show="activeCategory === 'all' || activeCategory === 'women'">
                <div class="relative overflow-hidden">
                    <img src="https://picsum.photos/seed/product13/400/500.jpg" alt="Women's Handbag" class="w-full h-64 object-cover">
                </div>
                <div class="p-4">
                    <h3 class="font-medium mb-1">Leather Handbag</h3>
                    <p class="text-secondary mb-2">Women's Collection</p>
                    <div class="flex justify-between items-center">
                        <span class="font-bold">$99.99</span>
                        <button class="text-primary hover:text-accent">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Card 12 -->
            <div class="product-card bg-white rounded-lg overflow-hidden shadow-md" data-category="women" x-show="activeCategory === 'all' || activeCategory === 'women'">
                <div class="relative overflow-hidden">
                    <img src="https://picsum.photos/seed/product14/400/500.jpg" alt="Women's Sunglasses" class="w-full h-64 object-cover">
                </div>
                <div class="p-4">
                    <h3 class="font-medium mb-1">Designer Sunglasses</h3>
                    <p class="text-secondary mb-2">Women's Collection</p>
                    <div class="flex justify-between items-center">
                        <span class="font-bold">$69.99</span>
                        <button class="text-primary hover:text-accent">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Run/Train/Rec Section -->
    <section class="container mx-auto px-4 py-12">
        <h2 class="text-2xl font-bold mb-8 text-center">Shop By Activity</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Run Banner -->
            <div class="category-banner relative h-80 rounded-lg overflow-hidden cursor-pointer">
                <img src="https://picsum.photos/seed/run/600/400.jpg" alt="Run" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                    <div class="text-center text-white">
                        <h3 class="text-2xl font-bold mb-2">Run</h3>
                        <p class="mb-4">Performance running gear</p>
                        <button class="bg-white text-primary px-4 py-2 rounded-md font-medium hover:bg-light transition-colors">
                            Shop Now
                        </button>
                    </div>
                </div>
            </div>

            <!-- Train Banner -->
            <div class="category-banner relative h-80 rounded-lg overflow-hidden cursor-pointer">
                <img src="https://picsum.photos/seed/train/600/400.jpg" alt="Train" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                    <div class="text-center text-white">
                        <h3 class="text-2xl font-bold mb-2">Train</h3>
                        <p class="mb-4">Gym and training essentials</p>
                        <button class="bg-white text-primary px-4 py-2 rounded-md font-medium hover:bg-light transition-colors">
                            Shop Now
                        </button>
                    </div>
                </div>
            </div>

            <!-- Rec Banner -->
            <div class="category-banner relative h-80 rounded-lg overflow-hidden cursor-pointer">
                <img src="https://picsum.photos/seed/rec/600/400.jpg" alt="Recover" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                    <div class="text-center text-white">
                        <h3 class="text-2xl font-bold mb-2">Recover</h3>
                        <p class="mb-4">Comfort for rest days</p>
                        <button class="bg-white text-primary px-4 py-2 rounded-md font-medium hover:bg-light transition-colors">
                            Shop Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- App Promotion Section -->
    <section class="bg-light py-12">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-8 md:mb-0">
                    <img src="https://picsum.photos/seed/app/600/600.jpg" alt="App Screenshot" class="max-w-full mx-auto">
                </div>
                <div class="md:w-1/2 md:pl-12">
                    <h2 class="text-2xl font-bold mb-4">Download Our App</h2>
                    <p class="text-secondary mb-6">Get exclusive offers and a seamless shopping experience with our mobile app. Available for both iOS and Android devices.</p>
                    <div class="flex flex-wrap gap-4">
                        <button class="bg-black text-white px-6 py-3 rounded-md flex items-center hover:bg-secondary transition-colors">
                            <i class="fab fa-apple text-2xl mr-2"></i>
                            <div class="text-left">
                                <p class="text-xs">Download on the</p>
                                <p class="text-sm font-medium">App Store</p>
                            </div>
                        </button>
                        <button class="bg-black text-white px-6 py-3 rounded-md flex items-center hover:bg-secondary transition-colors">
                            <i class="fab fa-google-play text-2xl mr-2"></i>
                            <div class="text-left">
                                <p class="text-xs">Get it on</p>
                                <p class="text-sm font-medium">Google Play</p>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-primary text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div>
                    <h3 class="text-xl font-bold mb-4">FASHION STORE</h3>
                    <p class="text-light mb-4">Your destination for the latest fashion trends and styles.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-light hover:text-white transition-colors">
                            <i class="fab fa-facebook-f text-xl"></i>
                        </a>
                        <a href="#" class="text-light hover:text-white transition-colors">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="text-light hover:text-white transition-colors">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-light hover:text-white transition-colors">
                            <i class="fab fa-pinterest text-xl"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-light hover:text-white transition-colors">Home</a></li>
                        <li><a href="#" class="text-light hover:text-white transition-colors">Shop</a></li>
                        <li><a href="#" class="text-light hover:text-white transition-colors">About Us</a></li>
                        <li><a href="#" class="text-light hover:text-white transition-colors">Contact</a></li>
                        <li><a href="#" class="text-light hover:text-white transition-colors">Blog</a></li>
                    </ul>
                </div>

                <!-- Customer Service -->
                <div>
                    <h4 class="text-lg font-bold mb-4">Customer Service</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-light hover:text-white transition-colors">FAQ</a></li>
                        <li><a href="#" class="text-light hover:text-white transition-colors">Shipping & Returns</a></li>
                        <li><a href="#" class="text-light hover:text-white transition-colors">Size Guide</a></li>
                        <li><a href="#" class="text-light hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-light hover:text-white transition-colors">Terms & Conditions</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="text-lg font-bold mb-4">Contact Us</h4>
                    <ul class="space-y-2 text-light">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-2"></i>
                            <span>123 Fashion Street, New York, NY 10001</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-2"></i>
                            <span>(123) 456-7890</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2"></i>
                            <span>info@fashionstore.com</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-light mb-4 md:mb-0">&copy; 2023 Fashion Store. All rights reserved.</p>
                <div class="flex space-x-4">
                    <img src="https://picsum.photos/seed/visa/50/30.jpg" alt="Visa" class="h-8">
                    <img src="https://picsum.photos/seed/mastercard/50/30.jpg" alt="Mastercard" class="h-8">
                    <img src="https://picsum.photos/seed/paypal/50/30.jpg" alt="PayPal" class="h-8">
                    <img src="https://picsum.photos/seed/amex/50/30.jpg" alt="American Express" class="h-8">
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
