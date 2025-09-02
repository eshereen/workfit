<div class="space-y-6">
    <!-- Clear Filters Button -->
    <!--[if BLOCK]><![endif]--><?php if(count($selectedCategories) > 0 || count($selectedSubcategories) > 0): ?>
        <div>
            <button 
                wire:click="clearFilters"
                class="w-full px-4 py-2 text-sm text-red-600 border border-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-colors"
            >
                Clear All Filters
            </button>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Categories -->
    <div class="space-y-4">
        <h3 class="text-lg font-semibold text-gray-900">Categories</h3>
        
        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->getCategories(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="">
                <!-- Category Header with Checkbox and + Symbol -->
                <div class="flex items-center justify-between p-3 hover:bg-gray-50 transition-colors border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <input 
                            type="checkbox" 
                            id="category_<?php echo e($category->id); ?>"
                            wire:click="toggleCategorySelection(<?php echo e($category->id); ?>)"
                            <?php echo e($this->isCategorySelected($category->id) ? 'checked' : ''); ?>

                            class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500 focus:ring-2"
                        >
                        <label for="category_<?php echo e($category->id); ?>" class="font-medium text-gray-700 cursor-pointer">
                            <?php echo e($category->name); ?>

                        </label>
                    </div>
                    
                    <!-- + Symbol for expanding subcategories -->
                    <button 
                        @click="$wire.toggleCategory(<?php echo e($category->id); ?>)"
                        class="text-gray-500 hover:text-gray-700 transition-colors"
                    >
                        <svg 
                            class="w-5 h-5 transition-transform duration-300 <?php echo e($this->isCategoryExpanded($category->id) ? 'rotate-45' : ''); ?>"
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </button>
                </div>

                <!-- Subcategories -->
                <!--[if BLOCK]><![endif]--><?php if($this->isCategoryExpanded($category->id)): ?>
                    <div class="border-t border-gray-200">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->getSubcategories($category->id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center gap-3 p-3 pl-6 hover:bg-gray-50 transition-colors">
                                <input 
                                    type="checkbox" 
                                    id="subcategory_<?php echo e($subcategory->id); ?>"
                                    wire:click="toggleSubcategorySelection(<?php echo e($subcategory->id); ?>, <?php echo e($category->id); ?>)"
                                    <?php echo e($this->isSubcategorySelected($subcategory->id) ? 'checked' : ''); ?>

                                    class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 focus:ring-2"
                                >
                                <label for="subcategory_<?php echo e($subcategory->id); ?>" class="text-sm text-gray-600 cursor-pointer">
                                    <?php echo e($subcategory->name); ?>

                                </label>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]--><?php if($this->getCategories()->count() === 0): ?>
            <div class="text-center py-4 text-gray-500">
                <p class="text-sm">No categories found</p>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <!-- Active Filters Summary -->
    <!--[if BLOCK]><![endif]--><?php if(count($selectedCategories) > 0 || count($selectedSubcategories) > 0): ?>
        <div class="border-t pt-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Active Filters:</h4>
            <div class="space-y-2">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $selectedCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoryId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $category = $this->getCategories()->firstWhere('id', $categoryId);
                    ?>
                    <!--[if BLOCK]><![endif]--><?php if($category): ?>
                        <div class="flex items-center gap-2">
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Category: <?php echo e($category->name); ?></span>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $selectedSubcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategoryId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $subcategory = null;
                        foreach($this->getCategories() as $cat) {
                            $sub = $this->getSubcategories($cat->id)->firstWhere('id', $subcategoryId);
                            if($sub) {
                                $subcategory = $sub;
                                break;
                            }
                        }
                    ?>
                    <!--[if BLOCK]><![endif]--><?php if($subcategory): ?>
                        <div class="flex items-center gap-2">
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Subcategory: <?php echo e($subcategory->name); ?></span>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH /Users/shereenelshayp/Herd/workfit/resources/views/livewire/collection-filters.blade.php ENDPATH**/ ?>