 <!-- Footer -->
 <footer class="bg-gray-950 text-white py-12 px-4">
    <div class="container mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8 text-center md:text-left">
            <!-- Brand -->
            <div class="animate-on-scroll">
                <h3 class="text-2xl font-bold mb-4">WORKFIT</h3>
                <p class="text-gray-200 mb-4">Premium activewear for performance and style.</p>
                <div class="flex space-x-4 justify-center md:justify-start">
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
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><a href="<?php echo e(route('categories.index', $category->slug)); ?>" class="text-gray-200hover:text-red-600 transition-colors capitalize"><?php echo e($category->name); ?></a></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </ul>
            </div>

            <!-- Support -->
            <div class="animate-on-scroll">
                <h4 class="font-bold mb-4">SUPPORT</h4>
                <ul class="space-y-2">
                     <li><a href="<?php echo e(route('about')); ?>" class="text-gray-200 hover:text-red-600 transition-colors capitalize">About Us</a></li>
                    <li><a href="<?php echo e(route('contact.index')); ?>" class="text-gray-200 hover:text-red-600 transition-colors capitalize">Contact Us</a></li>
                    <li><a href="<?php echo e(route('terms')); ?>" class="text-gray-200 hover:text-red-600 transition-colors capitalize">Terms & Conditions</a></li>
                    <li><a href="<?php echo e(route('return')); ?>" class="text-gray-200 hover:text-red-600 transition-colors capitalize">Shipping & Returns</a></li>
                    <li><a href="<?php echo e(route('privacy')); ?>" class="text-gray-200 hover:text-red-600 transition-colors capitalize">privacy Policy</a></li>

                </ul>
            </div>

            <!-- Newsletter -->
            <div class="animate-on-scroll">
                <h4 class="font-bold mb-4">JOIN OUR NEWSLETTER</h4>
                <p class="text-gray-200mb-4">Subscribe to receive updates, access to exclusive deals, and more.</p>
                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('newsletter.subscribe-form', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-2391806995-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
            </div>
        </div>
        <div class="border-t border-gray-600 pt-8 text-center text-gray-200 animate-on-scroll">
            <p>&copy; 2025 WORKFIT. All rights reserved.</p>
            <a href="https://medsite.dev" class=" py-0 pointer-none hover:pointer-none" style="color: transparent; cursor: default; pointer-events: none; text-decoration: none;">>Developed by Medsite</a>
        </div>
    </div>
</footer>


<?php /**PATH /Users/shereenelshayp/Herd/workfit/resources/views/layouts/footer.blade.php ENDPATH**/ ?>