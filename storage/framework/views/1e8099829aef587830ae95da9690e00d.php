<div class="space-y-6">
    <!-- Search Controls -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <!-- Search -->
        <div class="w-full md:w-96">
            <input
                wire:model.live="search"
                type="text"
                placeholder="Search collections..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
            >
        </div>
    </div>

    <!-- Collections Grid -->
    <!--[if BLOCK]><![endif]--><?php if($filteredCollections->count() > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $filteredCollections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $collection): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <!-- Collection Image -->
                    <div class="relative group">
                        <!--[if BLOCK]><![endif]--><?php if($collection->media->count() > 0): ?>
                            <img
                                src="<?php echo e($collection->getFirstMediaUrl('main_image','medium')); ?>"
                                alt="<?php echo e($collection->name); ?>"
                                class="w-full h-64 object-cover"
                            >
                        <?php else: ?>
                            <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-400">No Image</span>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <!-- Quick Actions Overlay -->
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <a
                                    href="<?php echo e(route('collection.show', $collection->slug)); ?>"
                                    class="bg-white text-gray-900 px-6 py-3 rounded-full hover:bg-red-600 hover:text-white transition-colors font-medium"
                                >
                                    View Collection
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Collection Info -->
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 mb-2 text-lg"><?php echo e($collection->name); ?></h3>

                        <!--[if BLOCK]><![endif]--><?php if($collection->description): ?>
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2"><?php echo e($collection->description); ?></p>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <!-- Product Count -->
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm text-gray-500"><?php echo e($collection->products_count); ?> products</span>
                        </div>

                        <!-- Action Button -->
                        <a
                            href="<?php echo e(route('collection.show', $collection->slug)); ?>"
                            class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors text-center block"
                        >
                            Browse Collection
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    <?php else: ?>
        <!-- No Collections Found -->
        <div class="text-center py-12">
            <div class="text-gray-500">
                <i class="fas fa-search text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold mb-2">No collections found</h3>
                <p class="text-gray-600">
                    <!--[if BLOCK]><![endif]--><?php if($search): ?>
                        No collections match your search "<?php echo e($search); ?>"
                    <?php else: ?>
                        No collections available at the moment.
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </p>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH /home/elragtow/workfit.medsite.dev/resources/views/livewire/collections-grid.blade.php ENDPATH**/ ?>