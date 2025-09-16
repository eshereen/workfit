<?php $__env->startSection('content'); ?>


    <!-- Main 404 Content -->
    <main class="min-h-screen flex items-center justify-center pt-16">
        <div class="container mx-auto px-4 py-16">
            <div class="max-w-4xl mx-auto text-center">
                <!-- 404 Number with Animation -->
                <div class="mb-8 animate-on-scroll">
                    <h1 class="text-9xl md:text-[200px] font-black text-red-600 leading-none">404</h1>
                    <div class="relative -mt-16 md:-mt-24">
                        <i class="fas fa-exclamation-triangle text-4xl md:text-6xl text-red-600 float-animation"></i>
                    </div>
                </div>
                <!-- Error Message -->
                <div class="mb-12 animate-on-scroll">
                    <h2 class="text-3xl md:text-5xl font-bold mb-4 slide-in-left">Oops! Page Not Found</h2>
                    <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto slide-in-right">
                        The page you're looking for might have been removed, had its name changed, or is temporarily unavailable.
                    </p>
                </div>
            </div>
        </div>
    </main>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/elragtow/workfit.medsite.dev/resources/views/errors/404.blade.php ENDPATH**/ ?>