@extends('layouts.app')
@section('content')

    <!-- Main Content -->
    <main class="pt-20 min-h-screen">
        <!-- Page Header -->
        <section class="bg-black text-white py-16 px-4">
            <div class="container mx-auto text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4 fade-in">TERMS & CONDITIONS</h1>
                <p class="text-lg text-gray-300 fade-in">Last updated: August 15, 2025</p>
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

            // Form submission handling (only for non-Livewire forms)
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                // Skip Livewire forms
                const wireSubmit = form.getAttribute('wire:submit');
                if (wireSubmit && wireSubmit.includes('prevent')) {
                    return;
                }

                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    // Visible success feedback
                    if (typeof showNotification === 'function') {
                        showNotification('Thank you for your submission!', 'success');
                    } else {
                        const msg = document.createElement('div');
                        msg.className = 'mt-3 rounded-md bg-green-50 p-3 text-sm text-green-700';
                        msg.textContent = 'Thank you for your submission!';
                        form.parentNode.insertBefore(msg, form.nextSibling);
                        setTimeout(() => msg.remove(), 3000);
                    }

                    form.reset();
                });
            });
        });
    </script>
@endsection
