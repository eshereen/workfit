<div>
    <div class="space-y-6">
        <!-- Search and Sort Controls -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <!-- Search -->
            <div class="w-full md:w-96">
                <input
                    wire:model.live="search"
                    type="text"
                    placeholder="Search products..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                >
            </div>

            <!-- Sort -->
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">Sort by:</label>
                <select
                    wire:model.live="sortBy"
                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                >
                    <option value="newest">Newest</option>
                    <option value="price_low">Price: Low to High</option>
                    <option value="price_high">Price: High to Low</option>
                </select>
            </div>
        </div>

        <!-- Active Filters Display -->
        <!--[if BLOCK]><![endif]--><?php if(count($selectedCategories) > 0 || count($selectedSubcategories) > 0): ?>
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-sm font-medium text-gray-700">Showing products for:</span>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $selectedCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoryId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $category = \App\Models\Category::find($categoryId);
                        ?>
                        <!--[if BLOCK]><![endif]--><?php if($category): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                Category: <?php echo e($category->name); ?>

                            </span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $selectedSubcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategoryId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $subcategory = \App\Models\Subcategory::find($subcategoryId);
                        ?>
                        <!--[if BLOCK]><![endif]--><?php if($subcategory): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                Subcategory: <?php echo e($subcategory->name); ?>

                            </span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!-- Products Grid -->
        <!--[if BLOCK]><![endif]--><?php if($products->count() > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div  class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <!-- Product Image -->
                        <div class="relative group" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                            <a href="<?php echo e(route('product.show', $product->slug)); ?>" class="block relative z-10 w-full h-64">
                                <!--[if BLOCK]><![endif]--><?php if($product->media->count() > 0): ?>
                                    
                                    <picture class="w-full h-full transition-opacity duration-500"
                                             :class="hover ? 'opacity-0' : 'opacity-100'">
                                        
                                        <source srcset="<?php echo e($product->getFirstMediaUrl('main_image', 'large_avif')); ?>" type="image/avif">
                                        <source srcset="<?php echo e($product->getFirstMediaUrl('main_image', 'large_webp')); ?>" type="image/webp">
                                        
                                        <img src="<?php echo e($product->getFirstMediaUrl('main_image')); ?>"
                                             alt="<?php echo e($product->name); ?>"
                                             class="w-full h-full object-cover"
                                             width="800"
                                             height="800"
                                             loading="lazy"
                                             decoding="async"
                                             fetchpriority="high">
                                    </picture>

                                    
                                    <?php
                                        $galleryImage = $product->getFirstMediaUrl('product_images');
                                    ?>

                                    <!--[if BLOCK]><![endif]--><?php if($galleryImage): ?>
                                        <picture class="absolute top-0 left-0 w-full h-full transition-opacity duration-500"
                                                 :class="hover ? 'opacity-100' : 'opacity-0'">
                                            
                                            <source srcset="<?php echo e($product->getFirstMediaUrl('product_images', 'zoom_avif')); ?>" type="image/avif">
                                            <source srcset="<?php echo e($product->getFirstMediaUrl('product_images', 'zoom_webp')); ?>" type="image/webp">
                                            
                                            <img src="<?php echo e($galleryImage); ?>"
                                                 alt="<?php echo e($product->name); ?>"
                                                 class="w-full h-full object-cover"
                                                 width="800"
                                                 height="800"
                                                 loading="lazy"
                                                 decoding="async">
                                        </picture>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <?php else: ?>
                                    <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-400">No Image</span>
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </a>

                            <!-- Quick Actions Overlay -->
                            <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-20 transition-all duration-300 flex items-center justify-center pointer-events-none group-hover:pointer-events-auto">
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex gap-2 pointer-events-auto">
                                    <!--[if BLOCK]><![endif]--><?php if($product->variants->count() > 0): ?>
                                        <button
                                            wire:click="openVariantModal(<?php echo e($product->id); ?>)"
                                            class="bg-white text-gray-900 px-4 py-2 rounded-full hover:bg-red-600 hover:text-white transition-colors pointer-events-auto"
                                        >
                                            <i class="fas fa-eye mr-2"></i>View Options
                                        </button>
                                    <?php else: ?>
                                        <button
                                            wire:click="addSimpleProductToCart(<?php echo e($product->id); ?>)"
                                            class="bg-white text-gray-900 px-4 py-2 rounded-full hover:bg-red-600 hover:text-white transition-colors"
                                        >
                                            <i class="fas fa-shopping-cart mr-2"></i>Add to Cart
                                        </button>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>

                            <!-- Wishlist Button -->
                            <button
                                wire:click="toggleWishlist(<?php echo e($product->id); ?>)"
                                class="absolute top-3 right-3 p-2 rounded-full bg-white shadow-md hover:bg-red-600 hover:text-white transition-colors <?php echo e(in_array($product->id, $wishlistProductIds) ? 'text-red-600' : 'text-gray-400'); ?>"
                            >
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>

                        <!-- Product Info -->
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2"><?php echo e($product->name); ?></h3>

                            <!--[if BLOCK]><![endif]--><?php if($product->category): ?>
                                <p class="text-sm text-gray-600 mb-2"><?php echo e($product->category->name); ?></p>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            <!-- Price -->
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-lg font-bold text-gray-900"><?php echo e($currencySymbol); ?><?php echo e(number_format($this->convertPrice($product->price), 2)); ?></span>

                                <!--[if BLOCK]><![endif]--><?php if($product->compare_price && $product->compare_price > $product->price): ?>
                                    <span class="text-sm text-gray-500 line-through"><?php echo e($currencySymbol); ?><?php echo e(number_format($this->convertPrice($product->compare_price), 2)); ?></span>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            <!-- Variant Colors (if any) -->
                            <!--[if BLOCK]><![endif]--><?php if($product->variants->count() > 0): ?>
                                <div class="flex gap-2 mb-3">
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $product->variants->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <!--[if BLOCK]><![endif]--><?php if($variant->color): ?>
                                            <div
                                                class="w-4 h-4 rounded-full border border-gray-300"
                                                style="background-color: <?php echo e($this->getColorCode($variant->color)); ?>"
                                                title="<?php echo e(ucfirst($variant->color)); ?>"
                                            ></div>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($product->variants->count() > 5): ?>
                                        <span class="text-xs text-gray-500">+<?php echo e($product->variants->count() - 5); ?> more</span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <!--[if BLOCK]><![endif]--><?php if($product->variants->count() > 0): ?>
                                    <button
                                        wire:click="openVariantModal(<?php echo e($product->id); ?>)"
                                        class="flex-1 bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors cursor-pointer"
                                    >
                                        View Options
                                    </button>
                                <?php else: ?>
                                    <button
                                        wire:click="addSimpleProductToCart(<?php echo e($product->id); ?>)"
                                        class="flex-1 bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors cursor-pointer"
                                    >
                                        Add to Cart
                                    </button>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                <?php echo e($products->links()); ?>

            </div>
        <?php else: ?>
            <!-- No Products Found -->
            <div class="text-center py-12">
                <div class="text-gray-500">
                    <i class="fas fa-search text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">No products found</h3>
                    <p class="text-gray-600">
                        <?php if($search): ?>
                            No products match your search "<?php echo e($search); ?>"
                        <?php elseif(count($selectedCategories) > 0 || count($selectedSubcategories) > 0): ?>
                            No products found for the selected filters
                        <?php else: ?>
                            No products found in this category.
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </p>
                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!-- Variant Selection Modal -->
        <!--[if BLOCK]><![endif]--><?php if($showVariantModal && $selectedProduct): ?>
            <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-lg max-w-md w-full max-h-[90vh] overflow-y-auto">
                    <div class="p-6">
                        <!-- Modal Header -->
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold"><?php echo e($selectedProduct->name); ?></h3>
                            <button
                                wire:click="$set('showVariantModal', false)"
                                class="text-gray-400 hover:text-gray-600"
                            >
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <!-- Product Image -->
                        <!--[if BLOCK]><![endif]--><?php if($selectedProduct->media->count() > 0): ?>
                            <img
                                src="<?php echo e($selectedProduct->getFirstMediaUrl('main_image','medium')); ?>"
                                alt="<?php echo e($selectedProduct->name); ?>"
                                class="w-full h-48 object-cover rounded-lg mb-4"
                            >
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <!-- Variant Selection -->
                        <!--[if BLOCK]><![endif]--><?php if($selectedProduct->variants->count() > 0): ?>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Variant:</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $selectedProduct->variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <button
                                            wire:click="selectVariant(<?php echo e($variant->id); ?>)"
                                            class="p-3 border rounded-lg text-left hover:border-red-500 transition-colors <?php echo e($selectedVariantId == $variant->id ? 'border-red-500 bg-red-50' : 'border-gray-300'); ?>"
                                        >
                                            <div class="flex items-center gap-2">
                                                <!--[if BLOCK]><![endif]--><?php if($variant->color): ?>
                                                    <div
                                                        class="w-4 h-4 rounded-full border border-gray-300"
                                                        style="background-color: <?php echo e($this->getColorCode($variant->color)); ?>"
                                                    ></div>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                <span class="text-sm font-medium">
                                                    <?php echo e($variant->color ? ucfirst($variant->color) : ''); ?><?php echo e($variant->size ? ' - ' . $variant->size : ''); ?>

                                                </span>
                                            </div>
                                            <div class="text-sm text-gray-600 mt-1">
                                                <!--[if BLOCK]><![endif]--><?php if($variant->price && $variant->price > 0): ?>
                                                    <?php echo e($currencySymbol); ?><?php echo e(number_format($this->convertPrice($variant->price), 2)); ?>

                                                <?php else: ?>
                                                    <?php echo e($currencySymbol); ?><?php echo e(number_format($this->convertPrice($selectedProduct->price), 2)); ?>

                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                Stock: <?php echo e($variant->stock); ?>

                                            </div>
                                        </button>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <!-- Quantity Selection -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity:</label>
                            <div class="flex items-center gap-3">
                                <button
                                    wire:click="decrementQty"
                                    class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100"
                                    <?php echo e($quantity <= 1 ? 'disabled' : ''); ?>

                                >
                                    <i class="fas fa-minus text-sm"></i>
                                </button>
                                <span class="text-lg font-medium w-12 text-center"><?php echo e($quantity); ?></span>
                                <button
                                    wire:click="incrementQty"
                                    class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100"
                                    <?php echo e($quantity >= ($selectedVariant ? $selectedVariant->stock : 10) ? 'disabled' : ''); ?>

                                >
                                    <i class="fas fa-plus text-sm"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Add to Cart Button -->
                        <button
                            wire:click="addToCart"
                            class="w-full bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            <?php echo e(!$selectedVariant ? 'disabled' : ''); ?>

                        >
                            <?php if($selectedVariant): ?>
                                Add to Cart -
                                <!--[if BLOCK]><![endif]--><?php if($selectedVariant->price && $selectedVariant->price > 0): ?>
                                    <?php echo e($currencySymbol); ?><?php echo e(number_format($this->convertPrice($selectedVariant->price), 2)); ?>

                                <?php else: ?>
                                    <?php echo e($currencySymbol); ?><?php echo e(number_format($this->convertPrice($selectedProduct->price), 2)); ?>

                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            <?php else: ?>
                                Select a variant first
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
</div>
<?php /**PATH /Users/shereenelshayp/Herd/workfit/resources/views/livewire/category-products.blade.php ENDPATH**/ ?>