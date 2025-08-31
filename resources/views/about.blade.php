@extends('layouts.app')
@section('content')
    <!-- Hero Section -->
    <section class="relative h-screen overflow-hidden mt-16">
        <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=1740&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="About Workfit" class="w-full h-full object-cover">
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
                    <p class="mb-4 text-gray-700">Workfit is an Egyptian brand that mix between sports and lifestyle by offering sports clothing specially made to suit your physical fitness.
We have worked hard to provide the best we have in our clothing industry since 2016 in order to contribute to providing support to reach your goals.</p>

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
                    <p class="text-gray-400">Committed to eco-friendly materials and processes</p>
                </div>

                <!-- Value 2 -->
                <div class="value-card bg-gray-50 p-6 rounded-lg text-center animate-on-scroll">
                    <div class="value-icon text-red-600 text-4xl mb-4">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">INNOVATION</h3>
                    <p class="text-gray-400">Constantly pushing boundaries in design and technology</p>
                </div>

                <!-- Value 3 -->
                <div class="value-card bg-gray-50 p-6 rounded-lg text-center animate-on-scroll">
                    <div class="value-icon text-red-600 text-4xl mb-4">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">COMMUNITY</h3>
                    <p class="text-gray-400">Building connections through fitness and wellness</p>
                </div>

                <!-- Value 4 -->
                <div class="value-card bg-gray-50 p-6 rounded-lg text-center animate-on-scroll">
                    <div class="value-icon text-red-600 text-4xl mb-4">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">QUALITY</h3>
                    <p class="text-gray-400">Uncompromising standards in every product</p>
                </div>
            </div>
        </div>
    </section>
@endsection

