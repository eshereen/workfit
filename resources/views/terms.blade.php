<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Conditions - Workfit</title>
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
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
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
        /* Table of contents styling */
        .toc-link {
            transition: all 0.3s ease;
        }
        .toc-link:hover {
            color: #FF0000;
            padding-left: 8px;
        }
        .toc-link.active {
            color: #FF0000;
            font-weight: bold;
            border-left: 3px solid #FF0000;
            padding-left: 12px;
        }
        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }
        /* Section styling */
        .section-content {
            scroll-margin-top: 100px;
        }
    </style>
</head>
<body class="bg-white text-black font-sans antialiased" x-data="{
    mobileMenuOpen: false,
    cartItems: 0,
    scrolled: false,
    activeSection: ''
}" x-init="() => {
    // Initialize scroll listener
    window.addEventListener('scroll', () => {
        scrolled = window.scrollY > 10;

        // Update active section in table of contents
        const sections = document.querySelectorAll('.section-content');
        sections.forEach(section => {
            const rect = section.getBoundingClientRect();
            if (rect.top <= 150 && rect.bottom >= 150) {
                activeSection = section.id;
            }
        });
    });
}">

    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-white shadow-md py-2">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="index.html" class="text-2xl font-bold">WORKFIT</a>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex space-x-8">
                    <a href="index.html" class="font-medium hover:text-red-600 transition-colors">HOME</a>
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
                <a href="index.html" class="font-medium hover:text-red-600 transition-colors">HOME</a>
                <a href="#" class="font-medium hover:text-red-600 transition-colors">NEW ARRIVALS</a>
                <a href="#" class="font-medium hover:text-red-600 transition-colors">WOMEN</a>
                <a href="#" class="font-medium hover:text-red-600 transition-colors">MEN</a>
                <a href="#" class="font-medium hover:text-red-600 transition-colors">COLLECTIONS</a>
                <a href="#" class="font-medium hover:text-red-600 transition-colors">SALE</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-20 min-h-screen">
        <!-- Page Header -->
        <section class="bg-black text-white py-16 px-4">
            <div class="container mx-auto text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4 fade-in">TERMS & CONDITIONS</h1>
                <p class="text-lg text-gray-300 fade-in">Last updated: November 15, 2023</p>
            </div>
        </section>

        <!-- Terms Content -->
        <section class="py-12 px-4">
            <div class="container mx-auto">
                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- Table of Contents (Desktop) -->
                    <div class="lg:w-1/4 hidden lg:block">
                        <div class="sticky top-24 bg-gray-50 p-6 rounded-lg">
                            <h3 class="font-bold text-lg mb-4">TABLE OF CONTENTS</h3>
                            <nav class="space-y-2">
                                <a href="#introduction" class="block toc-link" :class="activeSection === 'introduction' ? 'active' : ''">Introduction</a>
                                <a href="#general" class="block toc-link" :class="activeSection === 'general' ? 'active' : ''">General Information</a>
                                <a href="#use-of-website" class="block toc-link" :class="activeSection === 'use-of-website' ? 'active' : ''">Use of Website</a>
                                <a href="#products" class="block toc-link" :class="activeSection === 'products' ? 'active' : ''">Products & Services</a>
                                <a href="#orders" class="block toc-link" :class="activeSection === 'orders' ? 'active' : ''">Orders & Payment</a>
                                <a href="#shipping" class="block toc-link" :class="activeSection === 'shipping' ? 'active' : ''">Shipping & Returns</a>
                                <a href="#accounts" class="block toc-link" :class="activeSection === 'accounts' ? 'active' : ''">User Accounts</a>
                                <a href="#intellectual" class="block toc-link" :class="activeSection === 'intellectual' ? 'active' : ''">Intellectual Property</a>
                                <a href="#liability" class="block toc-link" :class="activeSection === 'liability' ? 'active' : ''">Limitation of Liability</a>
                                <a href="#privacy" class="block toc-link" :class="activeSection === 'privacy' ? 'active' : ''">Privacy</a>
                                <a href="#changes" class="block toc-link" :class="activeSection === 'changes' ? 'active' : ''">Changes to Terms</a>
                                <a href="#governing" class="block toc-link" :class="activeSection === 'governing' ? 'active' : ''">Governing Law</a>
                                <a href="#contact" class="block toc-link" :class="activeSection === 'contact' ? 'active' : ''">Contact Information</a>
                            </nav>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="lg:w-3/4">
                        <div class="max-w-4xl mx-auto">
                            <!-- Introduction -->
                            <section id="introduction" class="section-content mb-12 fade-in">
                                <h2 class="text-3xl font-bold mb-4">1. Introduction</h2>
                                <p class="mb-4">Welcome to Workfit. These Terms and Conditions govern your use of our website, products, and services. By accessing or using Workfit, you agree to be bound by these terms.</p>
                                <p>Workfit is committed to providing high-quality activewear and exceptional customer service. Please read these terms carefully before using our website or making a purchase.</p>
                            </section>

                            <!-- General Information -->
                            <section id="general" class="section-content mb-12 fade-in">
                                <h2 class="text-3xl font-bold mb-4">2. General Information</h2>
                                <p class="mb-4">Workfit is a brand of premium activewear designed for performance and style. We operate through our website and physical retail locations.</p>
                                <ul class="list-disc pl-6 space-y-2">
                                    <li>Website: www.workfit.com</li>
                                    <li>Business Registration: Workfit Inc.</li>
                                    <li>Headquarters: 123 Fitness Street, Active City, AC 12345</li>
                                    <li>Customer Service: support@workfit.com</li>
                                </ul>
                            </section>

                            <!-- Use of Website -->
                            <section id="use-of-website" class="section-content mb-12 fade-in">
                                <h2 class="text-3xl font-bold mb-4">3. Use of Website</h2>
                                <p class="mb-4">By using our website, you agree to:</p>
                                <ul class="list-disc pl-6 space-y-2">
                                    <li>Provide accurate and complete information</li>
                                    <li>Use the website for lawful purposes only</li>
                                    <li>Not attempt to gain unauthorized access to our systems</li>
                                    <li>Not interfere with the proper functioning of the website</li>
                                    <li>Respect the intellectual property rights of Workfit and others</li>
                                </ul>
                                <p class="mt-4">We reserve the right to suspend or terminate your access to the website if you violate these terms.</p>
                            </section>

                            <!-- Products & Services -->
                            <section id="products" class="section-content mb-12 fade-in">
                                <h2 class="text-3xl font-bold mb-4">4. Products & Services</h2>
                                <p class="mb-4">Workfit offers a range of activewear products including:</p>
                                <ul class="list-disc pl-6 space-y-2">
                                    <li>Women's activewear (tops, bottoms, sports bras, etc.)</li>
                                    <li>Men's activewear (t-shirts, shorts, hoodies, etc.)</li>
                                    <li>Accessories (bags, water bottles, etc.)</li>
                                    <li>Customization services</li>
                                </ul>
                                <p class="mt-4">We make every effort to display accurate product information, including colors, sizes, and materials. However, we cannot guarantee that colors will appear exactly as displayed on your device.</p>
                            </section>

                            <!-- Orders & Payment -->
                            <section id="orders" class="section-content mb-12 fade-in">
                                <h2 class="text-3xl font-bold mb-4">5. Orders & Payment</h2>
                                <h3 class="text-xl font-semibold mb-2">Order Process</h3>
                                <p class="mb-4">To place an order:</p>
                                <ol class="list-decimal pl-6 space-y-2 mb-4">
                                    <li>Select products and add them to your cart</li>
                                    <li>Proceed to checkout</li>
                                    <li>Provide shipping and payment information</li>
                                    <li>Review and confirm your order</li>
                                </ol>
                                <h3 class="text-xl font-semibold mb-2">Payment Methods</h3>
                                <p class="mb-4">We accept the following payment methods:</p>
                                <ul class="list-disc pl-6 space-y-2">
                                    <li>Credit cards (Visa, Mastercard, American Express)</li>
                                    <li>Debit cards</li>
                                    <li>PayPal</li>
                                    <li>Apple Pay</li>
                                    <li>Google Pay</li>
                                </ul>
                                <p class="mt-4">All prices are listed in USD and are subject to applicable taxes and shipping fees.</p>
                            </section>

                            <!-- Shipping & Returns -->
                            <section id="shipping" class="section-content mb-12 fade-in">
                                <h2 class="text-3xl font-bold mb-4">6. Shipping & Returns</h2>
                                <h3 class="text-xl font-semibold mb-2">Shipping</h3>
                                <p class="mb-4">We offer several shipping options:</p>
                                <ul class="list-disc pl-6 space-y-2 mb-4">
                                    <li>Standard shipping (5-7 business days) - $5.99</li>
                                    <li>Express shipping (2-3 business days) - $12.99</li>
                                    <li>Overnight shipping (1 business day) - $24.99</li>
                                </ul>
                                <p class="mb-4">Free standard shipping is available for orders over $75.</p>
                                <h3 class="text-xl font-semibold mb-2">Returns</h3>
                                <p class="mb-4">We accept returns within 30 days of purchase. Items must be:</p>
                                <ul class="list-disc pl-6 space-y-2">
                                    <li>Unworn and unwashed</li>
                                    <li>In original packaging with tags attached</li>
                                    <li>Accompanied by proof of purchase</li>
                                </ul>
                                <p class="mt-4">Return shipping costs are the responsibility of the customer unless the return is due to a defect or error on our part.</p>
                            </section>

                            <!-- User Accounts -->
                            <section id="accounts" class="section-content mb-12 fade-in">
                                <h2 class="text-3xl font-bold mb-4">7. User Accounts</h2>
                                <p class="mb-4">Creating an account allows you to:</p>
                                <ul class="list-disc pl-6 space-y-2 mb-4">
                                    <li>Save your shipping and payment information</li>
                                    <li>Track your orders</li>
                                    <li>Access exclusive offers and promotions</li>
                                    <li>Manage your preferences</li>
                                </ul>
                                <p class="mb-4">You are responsible for maintaining the confidentiality of your account information and for all activities that occur under your account.</p>
                                <p>Notify us immediately if you suspect any unauthorized use of your account.</p>
                            </section>

                            <!-- Intellectual Property -->
                            <section id="intellectual" class="section-content mb-12 fade-in">
                                <h2 class="text-3xl font-bold mb-4">8. Intellectual Property</h2>
                                <p class="mb-4">All content on this website, including but not limited to text, graphics, logos, images, and software, is the property of Workfit or its content suppliers and is protected by intellectual property laws.</p>
                                <p class="mb-4">You may not:</p>
                                <ul class="list-disc pl-6 space-y-2">
                                    <li>Reproduce, distribute, or modify any content without permission</li>
                                    <li>Use our trademarks or logos without authorization</li>
                                    <li>Frame or mirror any part of the website</li>
                                </ul>
                                <p class="mt-4">Workfit respects the intellectual property rights of others. If you believe your work has been copied in a way that constitutes copyright infringement, please contact us.</p>
                            </section>

                            <!-- Limitation of Liability -->
                            <section id="liability" class="section-content mb-12 fade-in">
                                <h2 class="text-3xl font-bold mb-4">9. Limitation of Liability</h2>
                                <p class="mb-4">Workfit shall not be liable for any indirect, incidental, special, consequential, or punitive damages arising from your use of our website or products.</p>
                                <p class="mb-4">Our total liability for any claim related to the website or products shall not exceed the amount you paid for the products in question.</p>
                                <p>Workfit makes no warranties or representations about the accuracy or completeness of the content on this website. The content is provided "as is" without any warranty of any kind.</p>
                            </section>

                            <!-- Privacy -->
                            <section id="privacy" class="section-content mb-12 fade-in">
                                <h2 class="text-3xl font-bold mb-4">10. Privacy</h2>
                                <p class="mb-4">Your privacy is important to us. Our Privacy Policy, which is incorporated into these Terms by reference, describes how we collect, use, and protect your personal information.</p>
                                <p class="mb-4">By using our website, you consent to the collection and use of your information as described in our Privacy Policy.</p>
                                <p>We may update our Privacy Policy from time to time. The updated policy will be posted on our website with the revised effective date.</p>
                            </section>

                            <!-- Changes to Terms -->
                            <section id="changes" class="section-content mb-12 fade-in">
                                <h2 class="text-3xl font-bold mb-4">11. Changes to Terms</h2>
                                <p class="mb-4">Workfit reserves the right to modify these Terms and Conditions at any time. Changes will be effective immediately upon posting on the website.</p>
                                <p class="mb-4">Your continued use of the website after any changes constitutes acceptance of the new terms.</p>
                                <p>We encourage you to review these terms periodically for any updates.</p>
                            </section>

                            <!-- Governing Law -->
                            <section id="governing" class="section-content mb-12 fade-in">
                                <h2 class="text-3xl font-bold mb-4">12. Governing Law</h2>
                                <p class="mb-4">These Terms and Conditions shall be governed by and construed in accordance with the laws of the State of California, without regard to its conflict of law principles.</p>
                                <p class="mb-4">Any disputes arising from these terms or your use of the website shall be resolved in the state or federal courts located in California.</p>
                                <p>You agree to submit to the personal jurisdiction of these courts for the purpose of litigating any such disputes.</p>
                            </section>

                            <!-- Contact Information -->
                            <section id="contact" class="section-content mb-12 fade-in">
                                <h2 class="text-3xl font-bold mb-4">13. Contact Information</h2>
                                <p class="mb-4">If you have any questions about these Terms and Conditions, please contact us:</p>
                                <div class="bg-gray-50 p-6 rounded-lg">
                                    <p class="mb-2"><strong>Email:</strong> support@workfit.com</p>
                                    <p class="mb-2"><strong>Phone:</strong> (555) 123-4567</p>
                                    <p class="mb-2"><strong>Mail:</strong> Workfit Inc., 123 Fitness Street, Active City, AC 12345</p>
                                    <p><strong>Business Hours:</strong> Monday-Friday, 9:00 AM - 6:00 PM PST</p>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

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
                        <li><a href="terms.html" class="text-gray-600 hover:text-red-600 transition-colors">Terms & Conditions</a></li>
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
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Form submission handling
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
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
