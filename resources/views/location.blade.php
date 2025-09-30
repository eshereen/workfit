@extends('layouts.app')
@section('content')
<div class="font-sans antialiased text-black bg-white" x-data="{
    mobileMenuOpen: false,
    cartItems: 0,
    scrolled: false,
    activeFilter: 'all',
    searchQuery: '',
    locations: [
        {
            id: 1,
            name: 'WORKFIT - ROXY BRANCH',
            address: 'عمارات الميريلاند، ٨ Gesr Al Suez, El-Montaza, Heliopolis, Cairo Governorate 4460141, Egypt',
            phone: '01124689117',
            hours: {
                monday: '2pm - 1am',
                tuesday: '2pm - 1am',
                wednesday: '2pm - 1am',
                thursday: '2pm - 1am',
                friday: '2pm - 1am',
                saturday: '2pm - 1am',
                sunday: '2pm - 1am',
            },
            type: 'flagship',

            image: 'https://lh3.googleusercontent.com/p/AF1QipOrysemYW1kz7aVSOr-7zyLQJujcGq2zB_h-E77=w408-h408-k-no'
        },
        {
            id: 2,
            name: 'Workfit giza',
            address: '٨ج Khatem El-Morsaleen, Al Omraneyah Al Gharbeyah, El Omraniya, Giza Governorate 12211, Egypt',
            phone: '01091142903',
            hours: {
                 monday: '2pm - 12am',
                tuesday: '2pm - 12am',
                wednesday: '2pm - 12am',
                thursday: '2pm - 12am',
                friday: '2pm - 12am',
                saturday: '2pm - 12am',
                sunday: '2pm - 12am',
            },
            type: 'retail',

            image: 'https://lh3.googleusercontent.com/p/AF1QipMayxQ5Fw3RuLdqOvPJ343MZnUrJJY27eBIB2G6=w408-h408-k-no'
        },
       
    ],

    get filteredLocations() {
        let filtered = this.locations;

        if (this.activeFilter !== 'all') {
            filtered = filtered.filter(location => location.type === this.activeFilter);
        }

        if (this.searchQuery) {
            const query = this.searchQuery.toLowerCase();
            filtered = filtered.filter(location =>
                location.name.toLowerCase().includes(query) ||
                location.address.toLowerCase().includes(query) ||
                location.phone.includes(query)
            );
        }

        return filtered;
    }
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
    <section class="overflow-hidden relative mt-16 h-96">
        <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=2940&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Workfit Locations" class="object-cover w-full h-full">
        <div class="flex relative z-10 justify-center items-center h-full">
            <div class="px-4 text-center text-white">
                <div class="flex justify-center mb-4">
                    <div class="flex justify-center items-center w-16 h-16 bg-red-600 rounded-full">
                        <i class="text-2xl fas fa-map-marker-alt"></i>
                    </div>
                </div>
                <h1 class="mb-4 text-4xl font-bold md:text-5xl slide-in-left">OUR LOCATIONS</h1>
                <p class="mx-auto max-w-2xl text-xl md:text-2xl slide-in-right">Find a Workfit store near you and experience premium activewear</p>
            </div>
        </div>
    </section>
    <!-- Locations Grid -->
    <section class="px-4 py-16">
        <div class="container mx-auto">
            <div class="mb-12 text-center animate-on-scroll">
                <h2 class="mb-4 text-3xl font-bold md:text-4xl">FIND YOUR STORE</h2>
                <p class="mx-auto max-w-2xl text-gray-600">Discover our locations across the country</p>
            </div>

            <div x-show="filteredLocations.length === 0" class="py-12 text-center">
                <i class="mb-4 text-4xl text-gray-400 fas fa-search"></i>
                <p class="text-xl text-gray-600">No stores found matching your search criteria</p>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                <template x-for="location in filteredLocations" :key="location.id">
                    <div class="overflow-hidden bg-white rounded-lg shadow-md location-card animate-on-scroll">
                        <!-- Store Image -->
                        <div class="overflow-hidden h-48">
                            <img :src="location.image" :alt="location.name" class="object-cover w-full h-full hover-zoom">
                        </div>

                        <!-- Store Info -->
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-xl font-bold" x-text="location.name"></h3>
                                <span class="px-3 py-1 text-sm font-medium text-red-600 capitalize bg-red-100 rounded-full" x-text="location.type"></span>
                            </div>

                            <div class="mb-4 space-y-3">
                                <div class="flex items-start">
                                    <i class="mt-1 mr-3 text-red-600 fas fa-map-marker-alt"></i>
                                    <p class="text-gray-700" x-text="location.address"></p>
                                </div>
                                <div class="flex items-start">
                                    <i class="mt-1 mr-3 text-red-600 fas fa-phone"></i>
                                    <a :href="'tel:' + location.phone" class="text-gray-700 transition-colors hover:text-red-600" x-text="location.phone"></a>
                                </div>
                            </div>

                            <!-- Operating Hours -->
                            <div class="mb-4">
                                <h4 class="mb-2 font-semibold">Hours:</h4>
                                <div class="space-y-1 text-sm">
                                    <div class="flex justify-between">
                                        <span>Mon - Fri:</span>
                                        <span class="hours-open" x-text="location.hours.monday"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Saturday:</span>
                                        <span class="hours-open" x-text="location.hours.saturday"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Sunday:</span>
                                        <span :class="location.hours.sunday === 'Closed' ? 'hours-closed' : 'hours-open'" x-text="location.hours.sunday"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Google Map -->

                        </div>
                    </div>
                </template>


        <!---Google Map-->

    </div>
    <div class="mx-auto my-20 mb-4 w-full">
        <h4 class="mb-2 font-semibold">Google Map:</h4>
        <div class="w-full h-screen">
            <iframe class="w-full h-full" src="https://www.google.com/maps/embed?pb=!1m12!1m8!1m3!1d221018.70662885447!2d31.260221000000005!3d30.053570999999998!3m2!1i1024!2i768!4f13.1!2m1!1sWORKFIT!5e0!3m2!1sen!2sus!4v1756546456816!5m2!1sen!2sus" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
        </div>

    </section>
@endsection
