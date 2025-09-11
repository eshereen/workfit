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
        <img src="https://images.unsplash.com/photo-1653289755854-a41949e96282?q=80&w=2940&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Contact Workfit" class="w-full h-full object-cover object-top">
        <div class="absolute inset-0 bg-black/50"></div>
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
                    <p class="text-gray-600 mb-4"> - شارع جسر السويس - روكسي<br>عمارات االمريلاند</p>
                    <p class="text-gray-600">Monday - Friday: 9am - 6pm<br>Saturday: 10am - 4pm<br>Sunday: Closed</p>
                </div>

                <!-- Contact Card 2 -->
                <div class="contact-card bg-white p-8 rounded-lg shadow-md text-center animate-on-scroll">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center text-red-600 mx-auto mb-4">
                        <i class="fas fa-phone-alt text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">CALL US</h3>
                    <p class="text-gray-600 mb-4">Customer Service<br>Monday - Friday: 9am - 6pm EST</p>
                    <a href="tel:+15551234567" class="text-red-600 font-bold text-lg hover:underline">01124689117</a>
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


</div>
@endsection
