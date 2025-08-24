@extends('layouts.app')
@section('content')
<div class="bg-white text-black font-sans antialiased" x-data="{ 
    mobileMenuOpen: false,
    cartItems: 0,
    scrolled: false,
    activeAccordion: null,
    formData: {
        name: '',
        email: '',
        subject: '',
        message: ''
    },
    formErrors: {},
    formSubmitted: false,
    submitting: false
}" x-init="() => {
    // Initialize scroll listener
    window.addEventListener('scroll', () => {
        scrolled = window.scrollY > 10;
    });
    
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
 <!-- Hero Section -->
    <section class="relative h-96 overflow-hidden mt-16">
        <img src="https://picsum.photos/seed/workfit-contact-hero/1920/600.jpg" alt="Contact Workfit" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-60"></div>
        <div class="relative z-10 h-full flex items-center justify-center">
            <div class="text-center text-white px-4">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-phone-alt text-2xl"></i>
                    </div>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold mb-4 slide-in-left">CONTACT US</h1>
                <p class="text-xl md:text-2xl max-w-2xl mx-auto slide-in-right">We're here to help and answer any question you might have</p>
            </div>
        </div>
    </section>

    <!-- Contact Information Section -->
    <section class="py-16 px-4">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Contact Card 1 -->
                <div class="contact-card bg-white p-8 rounded-lg shadow-md text-center animate-on-scroll">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center text-red-600 mx-auto mb-4">
                        <i class="fas fa-map-marker-alt text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">VISIT US</h3>
                    <p class="text-gray-600 mb-4">123 Fitness Street<br>Active City, AC 12345</p>
                    <p class="text-gray-600">Monday - Friday: 9am - 6pm<br>Saturday: 10am - 4pm<br>Sunday: Closed</p>
                </div>
                
                <!-- Contact Card 2 -->
                <div class="contact-card bg-white p-8 rounded-lg shadow-md text-center animate-on-scroll">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center text-red-600 mx-auto mb-4">
                        <i class="fas fa-phone-alt text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">CALL US</h3>
                    <p class="text-gray-600 mb-4">Customer Service<br>Monday - Friday: 9am - 6pm EST</p>
                    <a href="tel:+15551234567" class="text-red-600 font-bold text-lg hover:underline">(555) 123-4567</a>
                </div>
                
                <!-- Contact Card 3 -->
                <div class="contact-card bg-white p-8 rounded-lg shadow-md text-center animate-on-scroll">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center text-red-600 mx-auto mb-4">
                        <i class="fas fa-envelope text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">EMAIL US</h3>
                    <p class="text-gray-600 mb-4">We'll respond within 24 hours</p>
                    <a href="mailto:support@workfit.com" class="text-red-600 font-bold text-lg hover:underline">support@workfit.com</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="py-16 px-4 bg-gray-50">
        <div class="container mx-auto">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-12 animate-on-scroll">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">SEND US A MESSAGE</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Fill out the form below and we'll get back to you as soon as possible</p>
                </div>
                
                <div x-show="formSubmitted" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <p class="font-bold">Thank you for your message!</p>
                    <p>We've received your inquiry and will get back to you within 24 hours.</p>
                </div>
                
                <form @submit.prevent="submitForm()" class="bg-white p-8 rounded-lg shadow-md animate-on-scroll">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                            <input type="text" id="name" x-model="formData.name" 
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none"
                                   :class="formErrors.name ? 'border-red-500' : ''">
                            <p x-show="formErrors.name" class="text-red-500 text-sm mt-1" x-text="formErrors.name"></p>
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                            <input type="email" id="email" x-model="formData.email" 
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none"
                                   :class="formErrors.email ? 'border-red-500' : ''">
                            <p x-show="formErrors.email" class="text-red-500 text-sm mt-1" x-text="formErrors.email"></p>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                        <input type="text" id="subject" x-model="formData.subject" 
                               class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none"
                               :class="formErrors.subject ? 'border-red-500' : ''">
                        <p x-show="formErrors.subject" class="text-red-500 text-sm mt-1" x-text="formErrors.subject"></p>
                    </div>
                    
                    <div class="mb-6">
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                        <textarea id="message" x-model="formData.message" rows="6" 
                                  class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none resize-none"
                                  :class="formErrors.message ? 'border-red-500' : ''"></textarea>
                        <p x-show="formErrors.message" class="text-red-500 text-sm mt-1" x-text="formErrors.message"></p>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" :disabled="submitting" 
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-lg transition-colors disabled:opacity-50">
                            <span x-show="!submitting">SEND MESSAGE</span>
                            <span x-show="submitting">SENDING...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="py-16 px-4">
        <div class="container mx-auto">
            <div class="text-center mb-12 animate-on-scroll">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">FIND OUR STORE</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Visit our flagship store and experience the Workfit difference</p>
            </div>
            
            <div class="max-w-6xl mx-auto animate-on-scroll">
                <div class="map-container bg-gray-200 rounded-lg overflow-hidden shadow-lg">
                    <img src="https://picsum.photos/seed/workfit-map/1200/675.jpg" alt="Workfit Store Location" class="w-full h-full object-cover">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-xl font-bold mb-4">Flagship Store</h3>
                        <p class="text-gray-600 mb-2"><i class="fas fa-map-marker-alt text-red-600 mr-2"></i>123 Fitness Street, Active City, AC 12345</p>
                        <p class="text-gray-600 mb-2"><i class="fas fa-phone text-red-600 mr-2"></i>(555) 123-4567</p>
                        <p class="text-gray-600 mb-4"><i class="fas fa-clock text-red-600 mr-2"></i>Mon-Fri: 9am-6pm, Sat: 10am-4pm</p>
                        <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                            GET DIRECTIONS
                        </button>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-xl font-bold mb-4">Store Features</h3>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-red-600 mt-1 mr-3"></i>
                                <span>Full product range available</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-red-600 mt-1 mr-3"></i>
                                <span>Expert fitting specialists</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-red-600 mt-1 mr-3"></i>
                                <span>Free personal styling sessions</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-red-600 mt-1 mr-3"></i>
                                <span>In-store pickup for online orders</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-red-600 mt-1 mr-3"></i>
                                <span>Easy returns and exchanges</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-16 px-4 bg-gray-50">
        <div class="container mx-auto">
            <div class="text-center mb-12 animate-on-scroll">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">FREQUENTLY ASKED QUESTIONS</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Quick answers to common questions</p>
            </div>
            
            <div class="max-w-3xl mx-auto space-y-4">
                <!-- FAQ Item 1 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden animate-on-scroll">
                    <button @click="activeAccordion = activeAccordion === 'faq1' ? null : 'faq1'" class="w-full p-6 text-left flex justify-between items-center">
                        <h3 class="text-xl font-bold">What are your customer service hours?</h3>
                        <i class="fas fa-chevron-down transition-transform" :class="activeAccordion === 'faq1' ? 'rotate-180' : ''"></i>
                    </button>
                    <div class="accordion-content" :class="activeAccordion === 'faq1' ? 'active' : ''">
                        <div class="p-6 pt-0 text-gray-700">
                            <p>Our customer service team is available Monday through Friday from 9:00 AM to 6:00 PM EST. You can reach us by phone, email, or live chat during these hours. For urgent inquiries outside of these hours, please send us an email and we'll respond the next business day.</p>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Item 2 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden animate-on-scroll">
                    <button @click="activeAccordion = activeAccordion === 'faq2' ? null : 'faq2'" class="w-full p-6 text-left flex justify-between items-center">
                        <h3 class="text-xl font-bold">How quickly will you respond to my message?</h3>
                        <i class="fas fa-chevron-down transition-transform" :class="activeAccordion === 'faq2' ? 'rotate-180' : ''"></i>
                    </button>
                    <div class="accordion-content" :class="activeAccordion === 'faq2' ? 'active' : ''">
                        <div class="p-6 pt-0 text-gray-700">
                            <p>We strive to respond to all inquiries within 24 hours during business days. For urgent matters, we recommend calling us directly at (555) 123-4567 during our customer service hours. Live chat typically offers the fastest response time during business hours.</p>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Item 3 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden animate-on-scroll">
                    <button @click="activeAccordion = activeAccordion === 'faq3' ? null : 'faq3'" class="w-full p-6 text-left flex justify-between items-center">
                        <h3 class="text-xl font-bold">Do you offer international customer support?</h3>
                        <i class="fas fa-chevron-down transition-transform" :class="activeAccordion === 'faq3' ? 'rotate-180' : ''"></i>
                    </button>
                    <div class="accordion-content" :class="activeAccordion === 'faq3' ? 'active' : ''">
                        <div class="p-6 pt-0 text-gray-700">
                            <p>Yes, we provide customer support for our international customers. While our phone support is primarily in English, we offer email support in multiple languages. Our website is also available in several languages to better serve our global customer base.</p>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Item 4 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden animate-on-scroll">
                    <button @click="activeAccordion = activeAccordion === 'faq4' ? null : 'faq4'" class="w-full p-6 text-left flex justify-between items-center">
                        <h3 class="text-xl font-bold">Can I visit your headquarters or warehouse?</h3>
                        <i class="fas fa-chevron-down transition-transform" :class="activeAccordion === 'faq4' ? 'rotate-180' : ''"></i>
                    </button>
                    <div class="accordion-content" :class="activeAccordion === 'faq4' ? 'active' : ''">
                        <div class="p-6 pt-0 text-gray-700">
                            <p>Our warehouse is not open to the public for safety and security reasons. However, we welcome you to visit our flagship store at 123 Fitness Street in Active City. The store features our complete product line and our knowledgeable staff can assist with any questions you may have.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Social Media Section -->
    <section class="py-16 px-4">
        <div class="container mx-auto">
            <div class="text-center mb-12 animate-on-scroll">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">CONNECT WITH US</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Follow us on social media for the latest updates, fitness tips, and exclusive offers</p>
            </div>
            
            <div class="flex justify-center space-x-8 animate-on-scroll">
                <a href="#" class="social-icon text-gray-600 hover:text-red-600 transition-colors">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center hover:bg-red-100">
                        <i class="fab fa-facebook text-2xl"></i>
                    </div>
                    <p class="mt-2 text-sm">Facebook</p>
                </a>
                
                <a href="#" class="social-icon text-gray-600 hover:text-red-600 transition-colors">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center hover:bg-red-100">
                        <i class="fab fa-instagram text-2xl"></i>
                    </div>
                    <p class="mt-2 text-sm">Instagram</p>
                </a>
                
                <a href="#" class="social-icon text-gray-600 hover:text-red-600 transition-colors">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center hover:bg-red-100">
                        <i class="fab fa-twitter text-2xl"></i>
                    </div>
                    <p class="mt-2 text-sm">Twitter</p>
                </a>
                
                <a href="#" class="social-icon text-gray-600 hover:text-red-600 transition-colors">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center hover:bg-red-100">
                        <i class="fab fa-youtube text-2xl"></i>
                    </div>
                    <p class="mt-2 text-sm">YouTube</p>
                </a>
                
                <a href="#" class="social-icon text-gray-600 hover:text-red-600 transition-colors">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center hover:bg-red-100">
                        <i class="fab fa-tiktok text-2xl"></i>
                    </div>
                    <p class="mt-2 text-sm">TikTok</p>
                </a>
            </div>
        </div>
    </section>
</div>
@endsection