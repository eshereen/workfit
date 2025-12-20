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
            name: 'WORKFIT - Heliopolis BRANCH',
            address: 'عمارات الميريلاند، ٨ Gesr Al Suez, El-Montaza, Heliopolis, Cairo Governorate',
            phone: '0114 843846',
       
          

            image: 'https://lh3.googleusercontent.com/p/AF1QipOrysemYW1kz7aVSOr-7zyLQJujcGq2zB_h-E77=w408-h408-k-no'
        },
        {
            id: 2,
            name: 'Workfit giza',
            address: '٨ج Khatem El-Morsaleen, Al Omraneyah Al Gharbeyah, El Omraniya, Giza Governorate',
            phone: '0155 5858366',
       
          

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


    <!-- Locations Grid -->
    <section class="px-4 py-16 my-8 max-w-5xl lg:max-w-7xl">
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
