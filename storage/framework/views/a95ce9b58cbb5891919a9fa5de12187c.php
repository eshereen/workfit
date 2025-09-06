<?php $__env->startSection('content'); ?>
    <!-- Hero Section -->
    <section class="relative h-96 overflow-hidden mt-16">
        <img src=https://images.unsplash.com/photo-1631010231931-d2c396b444ec?q=80&w=2940&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Shipping & Returns" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative z-10 h-full flex items-center justify-center">
            <div class="text-center text-white px-4">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-shipping-fast text-2xl"></i>
                    </div>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold mb-4 slide-in-left">SHIPPING & RETURNS</h1>
                <p class="text-xl md:text-2xl max-w-2xl mx-auto slide-in-right">Everything you need to know about delivery and returns</p>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="py-12 px-4">
        <div class="container mx-auto">

            <!-- Returns Policy -->
            <div x-show="activeTab === 'returns'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <section class="mb-16 animate-on-scroll">
                    <h2 class="text-3xl font-bold mb-8 text-center">RETURNS POLICY</h2>

                    <div class="max-w-4xl mx-auto">
                        <div class="bg-white p-8 rounded-lg shadow-md mb-8">
                            <h3 class="text-2xl font-bold mb-4">14-Day Return Policy</h3>
                            <p class="text-gray-700 mb-6">We want you to love your Workfit products. If for any reason you're not completely satisfied, you can return items within 14 days of delivery for a full refund.</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                                <div>
                                    <h4 class="text-xl font-bold mb-4">Eligibility for Returns</h4>
                                    <ul class="space-y-2">
                                        <li class="flex items-start">
                                            <i class="fas fa-check-circle text-red-600 mt-1 mr-2"></i>
                                            <span>Items must be unworn, unwashed, and in original condition</span>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-check-circle text-red-600 mt-1 mr-2"></i>
                                            <span>Original tags must be attached</span>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-check-circle text-red-600 mt-1 mr-2"></i>
                                            <span>Original packaging must be included</span>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-check-circle text-red-600 mt-1 mr-2"></i>
                                            <span>Proof of purchase (order confirmation or receipt) is required</span>
                                        </li>
                                    </ul>
                                </div>

                                <div>
                                    <h4 class="text-xl font-bold mb-4">Non-Returnable Items</h4>
                                    <ul class="space-y-2">
                                        <li class="flex items-start">
                                            <i class="fas fa-times-circle text-red-600 mt-1 mr-2"></i>
                                            <span>Items marked as "Final Sale"</span>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-times-circle text-red-600 mt-1 mr-2"></i>
                                            <span>Underwear and swimwear for hygiene reasons</span>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-times-circle text-red-600 mt-1 mr-2"></i>
                                            <span>Gift cards</span>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-times-circle text-red-600 mt-1 mr-2"></i>
                                            <span>Items damaged after delivery</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-xl font-bold mb-4">Return Process</h4>
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <div class="step-number">1</div>
                                        <div>
                                            <h5 class="font-bold mb-1">Initiate Return</h5>
                                            <p class="text-gray-700">Log in to your Workfit account, go to "Order History," and select the item(s) you wish to return.</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <div class="step-number">2</div>
                                        <div>
                                            <h5 class="font-bold mb-1">Print Return Label</h5>
                                            <p class="text-gray-700">Once your return is approved, you'll receive a prepaid shipping label via email. Print the label and securely package your items.</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <div class="step-number">3</div>
                                        <div>
                                            <h5 class="font-bold mb-1">Ship Items</h5>
                                            <p class="text-gray-700">Attach the shipping label to your package and drop it off at any authorized shipping location.</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <div class="step-number">4</div>
                                        <div>
                                            <h5 class="font-bold mb-1">Receive Refund</h5>
                                            <p class="text-gray-700">Once we receive and inspect your return, we'll process your refund within 5-7 business days. Refunds will be issued to your original payment method.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Exchanges -->
            <div x-show="activeTab === 'exchanges'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <section class="mb-16 animate-on-scroll">
                    <h2 class="text-3xl font-bold mb-8 text-center">EXCHANGES</h2>

                    <div class="max-w-4xl mx-auto">
                        <div class="bg-white p-8 rounded-lg shadow-md mb-8">
                            <h3 class="text-2xl font-bold mb-4">Easy Exchange Process</h3>
                            <p class="text-gray-700 mb-6">If you'd like to exchange your item for a different size, color, or style, we make it easy. Simply follow our exchange process below.</p>

                            <div class="space-y-6 mb-8">
                                <div class="flex items-start">
                                    <div class="step-number">1</div>
                                    <div>
                                        <h4 class="text-xl font-bold mb-2">Request Exchange</h4>
                                        <p class="text-gray-700">Log in to your Workfit account, go to "Order History," and select the item(s) you wish to exchange. Choose the new size, color, or style you'd like instead.</p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="step-number">2</div>
                                    <div>
                                        <h4 class="text-xl font-bold mb-2">Return Original Item</h4>
                                        <p class="text-gray-700">We'll send you a prepaid shipping label via email. Print the label and securely package your original item. Drop it off at any authorized shipping location.</p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="step-number">3</div>
                                    <div>
                                        <h4 class="text-xl font-bold mb-2">Receive New Item</h4>
                                        <p class="text-gray-700">Once we receive your original item, we'll ship out your new item within 1-2 business days. You'll receive a shipping confirmation with tracking information.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-red-50 p-6 rounded-lg">
                                <h4 class="text-xl font-bold mb-4">Exchange Policy Details</h4>
                                <ul class="space-y-2">
                                    <li class="flex items-start">
                                        <i class="fas fa-info-circle text-red-600 mt-1 mr-2"></i>
                                        <span>Exchanges must be requested within 14 days of delivery</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-info-circle text-red-600 mt-1 mr-2"></i>
                                        <span>Items must be in original condition with tags attached</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-info-circle text-red-600 mt-1 mr-2"></i>
                                        <span>If the new item costs more than the original, you'll be charged the difference</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-info-circle text-red-600 mt-1 mr-2"></i>
                                        <span>If the new item costs less, you'll receive a refund for the difference</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-info-circle text-red-600 mt-1 mr-2"></i>
                                        <span>Exchange shipping is free for domestic orders</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-info-circle text-red-600 mt-1 mr-2"></i>
                                        <span>International exchanges are subject to a shipping fee</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="policy-card bg-white p-6 rounded-lg shadow-md">
                                <div class="flex items-center mb-4">
                                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center text-red-600 mr-4">
                                        <i class="fas fa-exchange-alt text-xl"></i>
                                    </div>
                                    <h3 class="text-xl font-bold">Size Exchanges</h3>
                                </div>
                                <p class="text-gray-700 mb-4">Need a different size? No problem. We offer free size exchanges for all our products.</p>
                                <p class="text-gray-700">Simply follow our exchange process and select the new size you need. We'll send you a prepaid shipping label for the original item.</p>
                            </div>

                            <div class="policy-card bg-white p-6 rounded-lg shadow-md">
                                <div class="flex items-center mb-4">
                                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center text-red-600 mr-4">
                                        <i class="fas fa-palette text-xl"></i>
                                    </div>
                                    <h3 class="text-xl font-bold">Color Exchanges</h3>
                                </div>
                                <p class="text-gray-700 mb-4">Changed your mind about the color? We've got you covered.</p>
                                <p class="text-gray-700">Request a color exchange through your account, and we'll help you get the right color for your style.</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/shereenelshayp/Herd/workfit/resources/views/return.blade.php ENDPATH**/ ?>