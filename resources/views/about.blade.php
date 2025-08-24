@extends('layouts.app')
@section('content')
    <!-- Hero Section -->
    <section class="relative h-screen overflow-hidden mt-16">
        <img src="https://picsum.photos/seed/workfit-about-hero/1920/1080.jpg" alt="About Workfit" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>
        <div class="relative z-10 h-full flex items-center justify-center">
            <div class="text-center text-white px-4">
                <h1 class="text-4xl md:text-6xl font-bold mb-4 slide-in-left">ABOUT WORKFIT</h1>
                <p class="text-xl md:text-2xl mb-8 max-w-2xl mx-auto slide-in-right">Empowering your fitness journey with premium activewear</p>
                <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 transition-colors fade-in">
                    OUR STORY
                </button>
            </div>
        </div>
    </section>

    <!-- Our Story Section -->
    <section class="py-16 px-4">
        <div class="container mx-auto">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="lg:w-1/2 animate-on-scroll">
                    <h2 class="text-3xl md:text-4xl font-bold mb-6">OUR STORY</h2>
                    <p class="mb-4 text-gray-700">Founded in 2015, Workfit began with a simple mission: to create activewear that combines performance, style, and sustainability. Our founder, Sarah Johnson, an avid fitness enthusiast, was frustrated with the lack of quality activewear that didn't compromise on style or environmental responsibility.</p>
                    <p class="mb-4 text-gray-700">What started as a small operation in a garage has grown into a global brand, serving fitness enthusiasts in over 50 countries. Throughout our journey, we've remained committed to our core values of quality, innovation, and sustainability.</p>
                    <p class="text-gray-700">Today, Workfit is more than just a clothing brand – we're a community of fitness enthusiasts who believe in pushing boundaries and living life to the fullest.</p>
                </div>
                <div class="lg:w-1/2 animate-on-scroll">
                    <img src="https://picsum.photos/seed/workfit-story/600/400.jpg" alt="Our Story" class="rounded-lg shadow-lg hover-zoom">
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision Section -->
    <section class="py-16 px-4 bg-gray-100">
        <div class="container mx-auto">
            <div class="text-center mb-12 animate-on-scroll">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">MISSION & VISION</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Our purpose drives everything we do</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Mission -->
                <div class="bg-white p-8 rounded-lg shadow-md animate-on-scroll">
                    <div class="text-red-600 text-4xl mb-4">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">OUR MISSION</h3>
                    <p class="text-gray-700">To empower individuals to achieve their fitness goals by providing high-performance, sustainable activewear that combines cutting-edge technology with timeless style.</p>
                </div>

                <!-- Vision -->
                <div class="bg-white p-8 rounded-lg shadow-md animate-on-scroll">
                    <div class="text-red-600 text-4xl mb-4">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">OUR VISION</h3>
                    <p class="text-gray-700">To become the world's most sustainable activewear brand, leading the industry in innovation while making a positive impact on people and the planet.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="py-16 px-4">
        <div class="container mx-auto">
            <div class="text-center mb-12 animate-on-scroll">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">OUR VALUES</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">The principles that guide our every decision</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Value 1 -->
                <div class="value-card bg-gray-50 p-6 rounded-lg text-center animate-on-scroll">
                    <div class="value-icon text-red-600 text-4xl mb-4">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">SUSTAINABILITY</h3>
                    <p class="text-gray-700">Committed to eco-friendly materials and processes</p>
                </div>

                <!-- Value 2 -->
                <div class="value-card bg-gray-50 p-6 rounded-lg text-center animate-on-scroll">
                    <div class="value-icon text-red-600 text-4xl mb-4">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">INNOVATION</h3>
                    <p class="text-gray-700">Constantly pushing boundaries in design and technology</p>
                </div>

                <!-- Value 3 -->
                <div class="value-card bg-gray-50 p-6 rounded-lg text-center animate-on-scroll">
                    <div class="value-icon text-red-600 text-4xl mb-4">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">COMMUNITY</h3>
                    <p class="text-gray-700">Building connections through fitness and wellness</p>
                </div>

                <!-- Value 4 -->
                <div class="value-card bg-gray-50 p-6 rounded-lg text-center animate-on-scroll">
                    <div class="value-icon text-red-600 text-4xl mb-4">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">QUALITY</h3>
                    <p class="text-gray-700">Uncompromising standards in every product</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Timeline Section -->
    <section class="py-16 px-4 bg-gray-100">
        <div class="container mx-auto">
            <div class="text-center mb-12 animate-on-scroll">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">OUR JOURNEY</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Key milestones in our evolution</p>
            </div>

            <div class="relative max-w-4xl mx-auto">
                <!-- Timeline Line -->
                <div class="timeline-line"></div>

                <!-- Timeline Items -->
                <div class="space-y-12">
                    <!-- 2015 -->
                    <div class="flex items-center animate-on-scroll">
                        <div class="w-1/2 text-right pr-8">
                            <h3 class="text-xl font-bold">2015</h3>
                            <p class="text-gray-700">Workfit founded with a vision for sustainable activewear</p>
                        </div>
                        <div class="timeline-dot w-1/2 flex justify-center">
                            <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center text-white font-bold">
                                <i class="fas fa-flag"></i>
                            </div>
                        </div>
                    </div>

                    <!-- 2017 -->
                    <div class="flex items-center animate-on-scroll">
                        <div class="timeline-dot w-1/2 flex justify-center">
                            <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center text-white font-bold">
                                <i class="fas fa-store"></i>
                            </div>
                        </div>
                        <div class="w-1/2 pl-8">
                            <h3 class="text-xl font-bold">2017</h3>
                            <p class="text-gray-700">First flagship store opens in San Francisco</p>
                        </div>
                    </div>

                    <!-- 2019 -->
                    <div class="flex items-center animate-on-scroll">
                        <div class="w-1/2 text-right pr-8">
                            <h3 class="text-xl font-bold">2019</h3>
                            <p class="text-gray-700">Launched international shipping to 20 countries</p>
                        </div>
                        <div class="timeline-dot w-1/2 flex justify-center">
                            <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center text-white font-bold">
                                <i class="fas fa-globe"></i>
                            </div>
                        </div>
                    </div>

                    <!-- 2021 -->
                    <div class="flex items-center animate-on-scroll">
                        <div class="timeline-dot w-1/2 flex justify-center">
                            <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center text-white font-bold">
                                <i class="fas fa-award"></i>
                            </div>
                        </div>
                        <div class="w-1/2 pl-8">
                            <h3 class="text-xl font-bold">2021</h3>
                            <p class="text-gray-700">Received Sustainability Award for eco-friendly practices</p>
                        </div>
                    </div>

                    <!-- 2023 -->
                    <div class="flex items-center animate-on-scroll">
                        <div class="w-1/2 text-right pr-8">
                            <h3 class="text-xl font-bold">2023</h3>
                            <p class="text-gray-700">Expanded to 50+ countries with 1 million+ customers</p>
                        </div>
                        <div class="timeline-dot w-1/2 flex justify-center">
                            <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center text-white font-bold">
                                <i class="fas fa-rocket"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-16 px-4">
        <div class="container mx-auto">
            <div class="text-center mb-12 animate-on-scroll">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">MEET THE TEAM</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">The passionate people behind Workfit</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Team Member 1 -->
                <div class="team-card bg-white rounded-lg overflow-hidden shadow-md animate-on-scroll">
                    <img src="https://picsum.photos/seed/team1/300/300.jpg" alt="Sarah Johnson" class="w-full h-64 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-1">Sarah Johnson</h3>
                        <p class="text-gray-600 mb-4">Founder & CEO</p>
                        <div class="team-social flex space-x-4 justify-center">
                            <a href="#" class="text-gray-600 hover:text-red-600 transition-colors">
                                <i class="fab fa-linkedin text-xl"></i>
                            </a>
                            <a href="#" class="text-gray-600 hover:text-red-600 transition-colors">
                                <i class="fab fa-instagram text-xl"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Team Member 2 -->
                <div class="team-card bg-white rounded-lg overflow-hidden shadow-md animate-on-scroll">
                    <img src="https://picsum.photos/seed/team2/300/300.jpg" alt="Michael Chen" class="w-full h-64 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-1">Michael Chen</h3>
                        <p class="text-gray-600 mb-4">Head of Design</p>
                        <div class="team-social flex space-x-4 justify-center">
                            <a href="#" class="text-gray-600 hover:text-red-600 transition-colors">
                                <i class="fab fa-linkedin text-xl"></i>
                            </a>
                            <a href="#" class="text-gray-600 hover:text-red-600 transition-colors">
                                <i class="fab fa-instagram text-xl"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Team Member 3 -->
                <div class="team-card bg-white rounded-lg overflow-hidden shadow-md animate-on-scroll">
                    <img src="https://picsum.photos/seed/team3/300/300.jpg" alt="Emily Rodriguez" class="w-full h-64 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-1">Emily Rodriguez</h3>
                        <p class="text-gray-600 mb-4">Marketing Director</p>
                        <div class="team-social flex space-x-4 justify-center">
                            <a href="#" class="text-gray-600 hover:text-red-600 transition-colors">
                                <i class="fab fa-linkedin text-xl"></i>
                            </a>
                            <a href="#" class="text-gray-600 hover:text-red-600 transition-colors">
                                <i class="fab fa-instagram text-xl"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Team Member 4 -->
                <div class="team-card bg-white rounded-lg overflow-hidden shadow-md animate-on-scroll">
                    <img src="https://picsum.photos/seed/team4/300/300.jpg" alt="David Kim" class="w-full h-64 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-1">David Kim</h3>
                        <p class="text-gray-600 mb-4">Operations Manager</p>
                        <div class="team-social flex space-x-4 justify-center">
                            <a href="#" class="text-gray-600 hover:text-red-600 transition-colors">
                                <i class="fab fa-linkedin text-xl"></i>
                            </a>
                            <a href="#" class="text-gray-600 hover:text-red-600 transition-colors">
                                <i class="fab fa-instagram text-xl"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



@endsection

