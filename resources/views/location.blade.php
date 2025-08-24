@extends('layouts.app')
@section('content')
<div class="bg-white text-black font-sans antialiased" x-data="{ 
    mobileMenuOpen: false,
    cartItems: 0,
    scrolled: false,
    activeFilter: 'all',
    searchQuery: '',
    locations: [
        {
            id: 1,
            name: 'Flagship Store',
            address: '123 Fitness Street, Active City, AC 12345',
            phone: '(555) 123-4567',
            hours: {
                monday: '9am - 6pm',
                tuesday: '9am - 6pm',
                wednesday: '9am - 6pm',
                thursday: '9am - 6pm',
                friday: '9am - 6pm',
                saturday: '10am - 4pm',
                sunday: 'Closed'
            },
            type: 'flagship',
            features: ['Full product range', 'Personal styling', 'In-store pickup'],
            image: 'https://picsum.photos/seed/workfit-flagship/400/300.jpg'
        },
        {
            id: 2,
            name: 'Downtown Store',
            address: '456 Main Street, Metro City, MC 67890',
            phone: '(555) 234-5678',
            hours: {
                monday: '10am - 7pm',
                tuesday: '10am - 7pm',
                wednesday: '10am - 7pm',
                thursday: '10am - 7pm',
                friday: '10am - 7pm',
                saturday: '10am - 6pm',
                sunday: '11am - 5pm'
            },
            type: 'retail',
            features: ['Urban collection', 'Express service', 'Free parking'],
            image: 'https://picsum.photos/seed/workfit-downtown/400/300.jpg'
        },
        {
            id: 3,
            name: 'Westfield Mall',
            address: '789 Shopping Blvd, Westfield, WF 13579',
            phone: '(555) 345-6789',
            hours: {
                monday: '10am - 9pm',
                tuesday: '10am - 9pm',
                wednesday: '10am - 9pm',
                thursday: '10am - 9pm',
                friday: '10am - 9pm',
                saturday: '10am - 9pm',
                sunday: '11am - 7pm'
            },
            type: 'mall',
            features: ['Extended hours', 'Mall amenities', 'Gift wrapping'],
            image: 'https://picsum.photos/seed/workfit-mall/400/300.jpg'
        },
        {
            id: 4,
            name: 'Beachside Store',
            address: '321 Ocean Drive, Coastal City, CC 24680',
            phone: '(555) 456-7890',
            hours: {
                monday: '9am - 7pm',
                tuesday: '9am - 7pm',
                wednesday: '9am - 7pm',
                thursday: '9am - 7pm',
                friday: '9am - 7pm',
                saturday: '9am - 8pm',
                sunday: '10am - 6pm'
            },
            type: 'retail',
            features: ['Beach collection', 'Sun protection', 'Rental equipment'],
            image: 'https://picsum.photos/seed/workfit-beach/400/300.jpg'
        },
        {
            id: 5,
            name: 'University Store',
            address: '654 Campus Way, College Town, CT 97531',
            phone: '(555) 567-8901',
            hours: {
                monday: '10am - 6pm',
                tuesday: '10am - 6pm',
                wednesday: '10am - 6pm',
                thursday: '10am - 6pm',
                friday: '10am - 6pm',
                saturday: '11am - 5pm',
                sunday: '12pm - 4pm'
            },
            type: 'retail',
            features: ['Student discount', 'Athletic gear', 'Team uniforms'],
            image: 'https://picsum.photos/seed/workfit-university/400/300.jpg'
        },
        {
            id: 6,
            name: 'Airport Store',
            address: '987 Terminal Rd, Airport City, AC 86420',
            phone: '(555) 678-9012',
            hours: {
                monday: '6am - 10pm',
                tuesday: '6am - 10pm',
                wednesday: '6am - 10pm',
                thursday: '6am - 10pm',
                friday: '6am - 10pm',
                saturday: '6am - 10pm',
                sunday: '6am - 10pm'
            },
            type: 'airport',
            features: ['Travel essentials', '24/7 access', 'Duty-free shopping'],
            image: 'https://picsum.photos/seed/workfit-airport/400/300.jpg'
        }
    ],
    upcomingStores: [
        {
            name: 'Tech Hub Store',
            location: 'Silicon Valley, CA',
            opening: 'Q1 2024',
            status: 'Coming Soon'
        },
        {
            name: 'Fashion District',
            location: 'New York, NY',
            opening: 'Q2 2024',
            status: 'Under Construction'
        },
        {
            name: 'Sports Complex',
            location: 'Los Angeles, CA',
            opening: 'Q3 2024',
            status: 'Planned'
        }
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
    <section class="relative h-96 overflow-hidden mt-16">
        <img src="https://picsum.photos/seed/workfit-locations-hero/1920/600.jpg" alt="Workfit Locations" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-60"></div>
        <div class="relative z-10 h-full flex items-center justify-center">
            <div class="text-center text-white px-4">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-2xl"></i>
                    </div>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold mb-4 slide-in-left">OUR LOCATIONS</h1>
                <p class="text-xl md:text-2xl max-w-2xl mx-auto slide-in-right">Find a Workfit store near you and experience premium activewear</p>
            </div>
        </div>
    </section>

    <!-- Search and Filter Section -->
    <section class="py-12 px-4 bg-gray-50 sticky top-16 z-40">
        <div class="container mx-auto">
            <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                <!-- Search Bar -->
                <div class="w-full md:w-1/2">
                    <div class="relative">
                        <input type="text" x-model="searchQuery" placeholder="Search by location, city, or store name..." 
                               class="search-input w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Filter Buttons -->
                <div class="flex flex-wrap gap-2">
                    <button @click="activeFilter = 'all'" 
                            :class="activeFilter === 'all' ? 'active' : ''"
                            class="filter-btn px-4 py-2 border border-gray-300 rounded-lg font-medium">
                        All Stores
                    </button>
                    <button @click="activeFilter = 'flagship'" 
                            :class="activeFilter === 'flagship' ? 'active' : ''"
                            class="filter-btn px-4 py-2 border border-gray-300 rounded-lg font-medium">
                        Flagship
                    </button>
                    <button @click="activeFilter = 'retail'" 
                            :class="activeFilter === 'retail' ? 'active' : ''"
                            class="filter-btn px-4 py-2 border border-gray-300 rounded-lg font-medium">
                        Retail
                    </button>
                    <button @click="activeFilter = 'mall'" 
                            :class="activeFilter === 'mall' ? 'active' : ''"
                            class="filter-btn px-4 py-2 border border-gray-300 rounded-lg font-medium">
                        Mall
                    </button>
                    <button @click="activeFilter = 'airport'" 
                            :class="activeFilter === 'airport' ? 'active' : ''"
                            class="filter-btn px-4 py-2 border border-gray-300 rounded-lg font-medium">
                        Airport
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Locations Grid -->
    <section class="py-16 px-4">
        <div class="container mx-auto">
            <div class="text-center mb-12 animate-on-scroll">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">FIND YOUR STORE</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Discover our locations across the country</p>
            </div>
            
            <div x-show="filteredLocations.length === 0" class="text-center py-12">
                <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
                <p class="text-xl text-gray-600">No stores found matching your search criteria</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <template x-for="location in filteredLocations" :key="location.id">
                    <div class="location-card bg-white rounded-lg shadow-md overflow-hidden animate-on-scroll">
                        <!-- Store Image -->
                        <div class="h-48 overflow-hidden">
                            <img :src="location.image" :alt="location.name" class="w-full h-full object-cover hover-zoom">
                        </div>
                        
                        <!-- Store Info -->
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-xl font-bold" x-text="location.name"></h3>
                                <span class="px-3 py-1 bg-red-100 text-red-600 text-sm rounded-full font-medium capitalize" x-text="location.type"></span>
                            </div>
                            
                            <div class="space-y-3 mb-4">
                                <div class="flex items-start">
                                    <i class="fas fa-map-marker-alt text-red-600 mt-1 mr-3"></i>
                                    <p class="text-gray-700" x-text="location.address"></p>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-phone text-red-600 mt-1 mr-3"></i>
                                    <a :href="'tel:' + location.phone" class="text-gray-700 hover:text-red-600 transition-colors" x-text="location.phone"></a>
                                </div>
                            </div>
                            
                            <!-- Operating Hours -->
                            <div class="mb-4">
                                <h4 class="font-semibold mb-2">Hours:</h4>
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
                            
                            <!-- Features -->
                            <div class="mb-4">
                                <h4 class="font-semibold mb-2">Features:</h4>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="feature in location.features" :key="feature">
                                        <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded" x-text="feature"></span>
                                    </template>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <button class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                    <i class="fas fa-directions mr-2"></i>Directions
                                </button>
                                <button class="flex-1 border border-gray-300 hover:bg-gray-100 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors">
                                    <i class="fas fa-phone mr-2"></i>Call
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </section>

    <!-- Interactive Map Section -->
    <section class="py-16 px-4 bg-gray-50">
        <div class="container mx-auto">
            <div class="text-center mb-12 animate-on-scroll">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">STORE LOCATOR MAP</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Explore all our locations on the interactive map</p>
            </div>
            
            <div class="max-w-6xl mx-auto animate-on-scroll">
                <div class="map-container bg-gray-200 rounded-lg overflow-hidden shadow-lg">
                    <img src="https://picsum.photos/seed/workfit-locations-map/1200/675.jpg" alt="Workfit Locations Map" class="w-full h-full object-cover">
                </div>
                
                <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold mb-4">Map Legend</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-red-600 rounded-full mr-2"></div>
                            <span>Flagship Stores</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-blue-600 rounded-full mr-2"></div>
                            <span>Retail Stores</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-600 rounded-full mr-2"></div>
                            <span>Mall Locations</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-purple-600 rounded-full mr-2"></div>
                            <span>Airport Stores</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Upcoming Stores Section -->
    <section class="py-16 px-4">
        <div class="container mx-auto">
            <div class="text-center mb-12 animate-on-scroll">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">COMING SOON</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">New Workfit stores opening near you</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <template x-for="store in upcomingStores" :key="store.name">
                    <div class="bg-white p-6 rounded-lg shadow-md text-center animate-on-scroll">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center text-red-600 mx-auto mb-4">
                            <i class="fas fa-store text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2" x-text="store.name"></h3>
                        <p class="text-gray-600 mb-2" x-text="store.location"></p>
                        <p class="text-sm text-gray-500 mb-4">Opening: <span x-text="store.opening"></span></p>
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm rounded-full font-medium" x-text="store.status"></span>
                    </div>
                </template>
            </div>
        </div>
    </section>

@endsection