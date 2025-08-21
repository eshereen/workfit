<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Workfit</title>
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
        /* Privacy icon styling */
        .privacy-icon {
            width: 60px;
            height: 60px;
            background-color: #FF0000;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 1rem;
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
                <div class="flex justify-center mb-4">
                    <div class="privacy-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold mb-4 fade-in">PRIVACY POLICY</h1>
                <p class="text-lg text-gray-300 fade-in">Last updated: November 15, 2023</p>
            </div>
        </section>

        <!-- Privacy Content -->
        <section class="py-12 px-4">
            <div class="container mx-auto">
                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- Table of Contents (Desktop) -->
                    <div class="lg:w-1/4 hidden lg:block">
                        <div class="sticky top-24 bg-gray-50 p-6 rounded-lg">
                            <h3 class="font-bold text-lg mb-4">TABLE OF CONTENTS</h3>
                            <nav class="space-y-2">
                                <a href="#introduction" class="block toc-link" :class="activeSection === 'introduction' ? 'active' : ''">Introduction</a>
                                <a href="#information-collect" class="block toc-link" :class="activeSection === 'information-collect' ? 'active' : ''">Information We Collect</a>
                                <a href="#how-collect" class="block toc-link" :class="activeSection === 'how-collect' ? 'active' : ''">How We Collect</a>
                                <a href="#how-use" class="block toc-link" :class="activeSection === 'how-use' ? 'active' : ''">How We Use</a>
                                <a href="#how-share" class="block toc-link" :class="activeSection === 'how-share' ? 'active' : ''">How We Share</a>
                                <a href="#third-party" class="block toc-link" :class="activeSection === 'third-party' ? 'active' : ''">Third-Party Services</a>
                                <a href="#cookies" class="block toc-link" :class="activeSection === 'cookies' ? 'active' : ''">Cookies & Tracking</a>
                                <a href="#data-security" class="block toc-link" :class="activeSection === 'data-security' ? 'active' : ''">Data Security</a>
                                <a href="#your-rights" class="block toc-link" :class="activeSection === 'your-rights' ? 'active' : ''">Your Rights</a>
                                <a href="#children" class="block toc-link" :class="activeSection === 'children' ? 'active' : ''">Children's Privacy</a>
                                <a href="#international" class="block toc-link" :class="activeSection === 'international' ? 'active' : ''">International Transfers</a>
                                <a href="#changes" class="block toc-link" :class="activeSection === 'changes' ? 'active' : ''">Changes to Policy</a>
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
                                <p class="mb-4">At Workfit, we respect your privacy and are committed to protecting your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website, use our services, or make a purchase.</p>
                                <p>By using Workfit, you agree to the collection and use of information in accordance with this policy. We have designed this policy to be transparent and easy to understand.</p>
                            </section>

                            <!-- Information We Collect -->
                            <section id="information-collect" class="section-content mb-12 fade-in">
                                <h2 class="text-3xl font-bold mb-4">2. Information We Collect</h2>
                                <h3 class="text-xl font-semibold mb-2">Personal Information</h3>
                                <p class="mb-4">We may collect personal information that can be used to identify you, including:</p>
                                <ul class="list-disc pl-6 space-y-2 mb-4">
                                    <li>Name and contact information (email, phone, address)</li                                    <>
li>Payment information (credit card details, billing address)</li>
                                    <li>Account credentials (username, password)</li>
                                    <li>Demographic information (age, gender, preferences)</li>
                                </ul>
                                <h3 class="text-xl font-semibold mb-2">Automatically Collected Information</h3>
                                <p class="mb-4">When you visit our website, we automatically collect certain information, including:</p>
                                <ul class="list-disc pl-6 space-y-2">
                                    <li>IP address and browser information</li>
                                    <li>Device information (type, operating system)</li>
                                    <li>Pages visited and time spent on our site</li>
                                    <li>Referring websites and search terms</li>
                                </ul>
                            </section>

                            <!-- How We Collect Information -->
                            <section id="how-collect" class="section-content mb-12 fade-in">
                                <h2 class="text-3xl font-bold mb-4">3. How We Collect Information</h2>
                                <p class="mb-4">We collect information through various methods:</p>
                                <ul class="list-disc pl-6 space-y-2">
                                    <li><strong>Direct Collection:</strong> When you provide information directly to us (e.g., creating an account, placing an order)</li>
                                    <li><strong>Automated Technologies:</strong> Through cookies, web beacons, and similar technologies</li>
                                    <li><strong>Third Parties:</strong> From payment processors, shipping carriers, and analytics providers</li>
                                    <li><strong>Cookies:</strong> Small text files stored on your device to enhance your experience</li>
                                </ul>
                            </section>

                            <!-- How We Use Your Information -->
                            <section id="how-use" class="section-content mb-12 fade-in">
                                <h2 class="text-3xl font-bold mb-4">4. How We Use Your Information</h2>
                                <p class="mb-4">We use your information for various purposes, including:</p>
                                <ul class="list-disc pl-6 space-y-2 mb-4">
                                    <li>To process and fulfill your orders</li>
                                    <li>To provide customer service and support</li>
                                    <li>To personalize your experience on our website</li>
                                    <li>To send you marketing communications (with your consent)</li>
                                    <li>To improve our products, services, and website</li>
                                    <li>To detect and prevent fraud</li>
                                    <li>To comply with legal obligations</li>
                                </ul>
                                <p>We will not use your information for purposes other than those described in this policy without your prior consent.</p>
                            </section>

                            <!-- How We Share Your Information -->
                            <section id="how-share" class="section-content mb-12 fade-in">
                                <h2 class="text-3xl font-bold mb-4">5. How We Share Your Information</h2>
                                <p class="mb-4">We may share your information with third parties in the following circumstances:</p>
                                <ul class="list-disc pl-6 space-y-2 mb-4">
                                    <li><strong>Service Providers:</strong> With companies that perform services on our behalf (payment processing, shipping, email delivery)</li>
                                    <li><strong>Business Transfers:</strong> In connection with a merger, acquisition, or sale of company assets</li>
                                    <li><strong>Legal Requirements:</strong> When required by law or to protect our rights</li>
                                    <li><strong>With Your Consent:</strong> When you explicitly authorize us to share</li>
                                </ul>
                                <p>We do not sell your personal information to third parties for their marketing purposes.</p>
                            </section>

                            <!-- Third-Party Services -->
                            <section id="third-party" class="section-content mb-12 fade-in">
                                <h2 class="text-3xl font-bold mb-4">6. Third-Party Services</h2>
                                <p class="mb-4">Our website and services may integrate with third-party services, including:</p>
                                <ul class="list-disc pl-6 space-y-2 mb-4">
                                    <li>Payment processors (Stripe, PayPal)</li>
                                    <li>Shipping carriers (UPS, FedEx, USPS)</li>
                                    <li>Email marketing platforms (Mailchimp)</li>
                                    <li>Analytics services (Google Analytics)</li>
                                    <li>Social media platforms (Facebook, Instagram)</li>
                                </ul>
                                <p>These third parties have their own privacy policies, and we are not responsible for their practices. We encourage you to review their privacy policies.</p>
                            </section>

                            <!-- Cookies and Tracking Technologies -->
                            <section id="cookies" class="section-content mb-12 fade-in">
                                <h2 class="text-3xl font-bold mb-4">7. Cookies and Tracking Technologies</h2>
                                <p class="mb-4">We use cookies and similar tracking technologies to enhance your experience on our website:</p>
                                <h3 class="text-xl font-semibold mb-2">Types of Cookies We Use</h3>
                                <ul class="list-disc pl-6 space-y-2 mb-4">
                                    <li><strong>Essential Cookies:</strong> Necessary for the website to function properly</li>
                                    <li><strong>Performance Cookies:</strong> Help us understand how visitors interact with our site</li>
                                    <li><strong>Functional Cookies:</strong> Remember your preferences and settings</li>
                                    <li><strong>Advertising Cookies:</strong> Used to deliver relevant advertisements</li>
                                </ul>
                                <p>You can manage your cookie preferences through your browser settings. However, disabling certain cookies may affect your experience on our website.</p>
                            </section>

                            <!-- Data Security -->
                            <section id="data-security" class="section-content mb-12 fade-in">
                                <h2 class="text-3xl font-bold mb-4">8. Data Security</h2>
                                <p class="mb-4">We implement appropriate security measures to protect your personal information, including:</p>
                                <ul class="list-disc pl-6 space-y-2 mb-4">
                                    <li>SSL encryption for data transmission</li>
                                    <li>Secure payment processing</li>
                                    <li>Access controls and authentication</li>
                                    <li>Regular security assessments</li>
                                    <li>Employee training on data protection</li>
                                </ul>
                                <p>While we strive to protect your information, no method of transmission over the internet is 100% secure. We cannot guarantee absolute security but are committed to maintaining reasonable protections.</p>
                            </section>

                            <!-- Your Rights and Choices -->
                            <section id="your-rights" class="section-content mb-12 fade-in">
                                <h2 class="text-3xl font-bold mb-4">9. Your Rights and Choices</h2>
                                <p class="mb-4">You have certain rights regarding your personal information:</p>
                                <h3 class="text-xl font-semibold mb-2">Access and Correction</h3>
                                <p class="mb-4">You can access and update your personal information through your account settings or by contacting us.</p>
                                <h3 class="text-xl font-semibold mb-2">Data Portability</h3>
                                <p class="mb-4">You have the right to receive your personal information in a portable format.</p>
                                <h3 class="text-xl font-semibold mb-2">Deletion</h3>
                                <p class="mb-4">You can request the deletion of your personal information, subject to legal requirements.</p>
                                <h3 class="text-xl font-semibold mb-2">Marketing Communications</h3>
                                <p class="mb-4">You can opt out of marketing communications at any time by following the unsubscribe instructions or contacting us.</p>
                            </section>

                            <!-- Children's Privacy -->
                            <section id="children" class="section-content mb-12 fade-in">
                                <h2 class="text-3xl font-bold mb-4">10. Children's Privacy</h2>
                                <p class="mb-4">Our website and services are not intended for children under the age of 13. We do not knowingly collect personal information from children under 13.</p>
                                <p class="mb-4">If we become aware that we have collected personal information from a child under 13 without parental consent, we will take steps to remove that information.</p>
                                <p>If you are a parent or guardian and believe your child has provided us with personal information, please contact us.</p>
                            </section>

                            <!-- International Data Transfers -->
                            <section id="international" class="section-content mb-12 fade-in">
                                <h2 class="text-3xl font-bold mb-4">11. International Data Transfers</h2>
                                <p class="mb-4">Workfit is based in the United States, and we may transfer your personal information to other countries where we operate or use service providers.</p>
                                <p class="mb-4">When we transfer your information internationally, we ensure appropriate safeguards are in place, including:</p>
                                <ul class="list-disc pl-6 space-y-2">
                                    <li>Standard contractual clauses</li>
                                    <li>Compliance with applicable privacy frameworks</li>
                                    <li>Technical and organizational security measures</li>
                                </ul>
                            </section>

                            <!-- Changes to This Privacy Policy -->
                            <section id="changes" class="section-content mb-12 fade-in">
                                <h2 class="text-3xl font-bold mb-4">12. Changes to This Privacy Policy</h2>
                                <p class="mb-4">We may update this Privacy Policy from time to time to reflect changes in our practices or applicable laws.</p>
                                <p class="mb-4">When we make changes, we will:</p>
                                <ul class="list-disc pl-6 space-y-2">
                                    <li>Update the "Last updated" date at the top of this policy</li>
                                    <li>Post the revised policy on our website</li>
                                    <li>Notify you of material changes via email or website notice</li>
                                </ul>
                                <p>Your continued use of our services after any changes constitutes acceptance of the updated policy.</p>
                            </section>

                            <!-- Contact Information -->
                            <section id="contact" class="section-content mb-12 fade-in">
                                <h2 class="text-3xl font-bold mb-4">13. Contact Information</h2>
                                <p class="mb-4">If you have any questions about this Privacy Policy or our privacy practices, please contact us:</p>
                                <div class="bg-gray-50 p-6 rounded-lg">
                                    <p class="mb-2"><strong>Email:</strong> privacy@workfit.com</p>
                                    <p class="mb-2"><strong>Phone:</strong> (555) 123-4567</p>
                                    <p class="mb-2"><strong>Mail:</strong> Workfit Privacy Officer, 123 Fitness Street, Active City, AC 12345</p>
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
                        <li><a href="privacy.html" class="text-gray-600 hover:text-red-600 transition-colors">Privacy Policy</a></li>
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
