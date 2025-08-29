 <!-- Footer -->
 <footer class="bg-gray-950 text-white py-12 px-4">
    <div class="container mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <!-- Brand -->
            <div class="animate-on-scroll">
                <h3 class="text-2xl font-bold mb-4">WORKFIT</h3>
                <p class="text-gray-200 mb-4">Premium activewear for performance and style.</p>
                <div class="flex space-x-4">
                    <a href="https://www.facebook.com/WorkfitEgypt" class="text-gray-200 hover:text-red-600 transition-colors">
                        <i class="fab fa-facebook text-xl"></i>
                    </a>
                    <a href="https://www.instagram.com/workfit_official/" class="text-gray-200 hover:text-red-600 transition-colors">
                        <i class="fab fa-instagram text-xl"></i>
                    </a>
                    <a href="https://wa.me/message/LFV5D6TOO62SC1" class="text-gray-200 hover:text-red-600 transition-colors">
                        <i class="fa-brands fa-whatsapp text-xl"></i>
                    </a>
                    <a href="mailto:workfitheadoffice@gmail.com"  class="text-gray-200 hover:text-red-600 transition-colors">
                      <i class="fa-regular fa-envelope text-xl"></i>
                    </a>
                     <a href="tel:+201148438466" class="text-gray-200 hover:text-red-600 transition-colors">
                      <i class="fa-solid fa-phone text-xl"></i>
                    </a>
                </div>
            </div>

            <!-- Shop -->
            <div class="animate-on-scroll">
                <h4 class="font-bold mb-4">SHOP</h4>
                <ul class="space-y-2">
                    @foreach ($categories as $category)
                    <li><a href="{{ route('categories.index', $category->slug) }}" class="text-gray-200hover:text-red-600 transition-colors">{{ $category->name }}</a></li>
                    @endforeach

                </ul>
            </div>

            <!-- Support -->
            <div class="animate-on-scroll">
                <h4 class="font-bold mb-4">SUPPORT</h4>
                <ul class="space-y-2">
                     <li><a href="{{ route('about') }}" class="text-gray-200 hover:text-red-600 transition-colors capitalize">About Us</a></li>
                    <li><a href="{{ route('contact.index') }}" class="text-gray-200 hover:text-red-600 transition-colors capitalize">Contact Us</a></li>
                    <li><a href="{{ route('terms') }}" class="text-gray-200 hover:text-red-600 transition-colors capitalize">Terms & Conditions</a></li>
                    <li><a href="{{ route('return') }}" class="text-gray-200 hover:text-red-600 transition-colors capitalize">Shipping & Returns</a></li>
                    <li><a href="{{ route('privacy') }}" class="text-gray-200 hover:text-red-600 transition-colors capitalize">privacy Policy</a></li>

                </ul>
            </div>

            <!-- Newsletter -->
            <div class="animate-on-scroll">
                <h4 class="font-bold mb-4">JOIN OUR NEWSLETTER</h4>
                <p class="text-gray-200mb-4">Subscribe to receive updates, access to exclusive deals, and more.</p>
                <livewire:newsletter.subscribe-form />
            </div>
        </div>
        <div class="border-t border-gray-600 pt-8 text-center text-gray-200animate-on-scroll">
            <p>&copy; 2025 WORKFIT. All rights reserved.</p>
        </div>
    </div>
</footer>


