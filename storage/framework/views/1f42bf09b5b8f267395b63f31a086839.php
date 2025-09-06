<?php $__env->startSection('content'); ?>
    <!-- Hero Section with Video Background -->
    <section class="relative -top-28 h-screen overflow-hidden">
        <!-- Video Background -->
        <video autoplay muted loop playsinline preload="metadata" poster="poster.jpg" class="w-full h-full object-cover hidden md:block">
            <source src="videos/workfit-lg.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <!-- Mobile Video -->
        <video autoplay muted loop playsinline preload="metadata" poster="poster.jpg" class="w-full h-full object-cover  md:hidden">
            <source src="videos/workfit-mobile.mp4" type="video/mp4">
            Your browser does not support the video tag.
            </video>
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/50"></div>

        <!-- Hero Content (bottom-left aligned) -->
        <div class="absolute inset-0 z-10">
            <div class="absolute bottom-8 left-0 text-left text-white pl-4 md:pl-12 pr-4">
                <h1 class="sm:text-center md:text-left text-4xl md:text-6xl font-bold mb-4 slide-in">WORKFIT</h1>
                <p class="text-left text-xl md:text-2xl pr-8 md:pr-0 mb-8 max-w-xl fade-in">Premium activewear designed for performance </p>
                <div class="flex flex-col sm:flex-row justify-start gap-4 fade-in">
                    <a href="<?php echo e(route('categories.index', 'women')); ?>" class="bg-white hover:bg-red-600 text-gray-950 hover:text-white font-bold py-3 px-6 transition-colors uppercase text-center">
                        SHOP WOMEN
                    </a>
                    <a href="<?php echo e(route('categories.index', 'men')); ?>" class="bg-white hover:bg-gray-400 text-black font-bold py-3 px-6 transition-colors text-center">
                        SHOP MEN
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Men Products -->
    <section class="container mx-auto">
        <h1 class="text-center font-bold sm:text-3xl md:text-4xl lg:text-5xl mb-2 uppercase">Men's Collection</h1>
        <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto animate-on-scroll">Discover our latest collection of products</p>
        <?php if($men && $men->directProducts->isNotEmpty()): ?>
       <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('product-index',['products'=>$men->directProducts->take(8)]);

$__html = app('livewire')->mount($__name, $__params, 'lw-3234203928-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
       <?php endif; ?>
   </section>


    <!-- Full-width Lifestyle Banner -->
    <section class="relative h-auto overflow-hidden animate-on-scroll">
        <img src="<?php echo e(asset('imgs/women.jpeg')); ?>"
             loading="lazy"
             alt="Lifestyle Banner"

             height="600"
             class="w-full h-full object-cover">
             <?php if($collections->isNotEmpty()): ?>
        <!-- Overlay with only background dark -->
        <div class="absolute inset-0 bg-black/50 z-0">
            <div class="absolute bottom-8 left-0 z-40 text-left text-white pl-8 md:pl-12">
                <h2 class="text-3xl md:text-4xl font-bold mb-4 uppercase"><?php echo e($collections->first()->name); ?> 'S COLLECTIONS</h2>
                <p class="text-xl mb-6 max-w-2xl">
                    <?php echo e($collections->first()->description); ?>

                </p>
                <a href="<?php echo e(route('collections.index')); ?>"
                   class="bg-white hover:bg-red-600  text-gray-950 hover:text-white font-bold py-3 px-8 transition-colors">
                    SHOP NOW
                </a>
            </div>
        </div>
        <?php endif; ?>
    </section>
    <!-- Product Grid - First Category Collection -->
    <section class="py-16 px-4">
        <?php if($women && $women->directProducts->isNotEmpty()): ?>
        <div class="container mx-auto">
            <h2 class="text-3xl font-bold text-center mb-4 animate-on-scroll uppercase"><?php echo e($women->name); ?>'S COLLECTION</h2>

            <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto animate-on-scroll"><?php echo e($women->description); ?></p>

           <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('product-index',['products'=>$women->directProducts->take(8)]);

$__html = app('livewire')->mount($__name, $__params, 'lw-3234203928-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>



            <div class="text-center mt-12 animate-on-scroll">
                <a href="<?php echo e(route('categories.index', $women->slug)); ?>" class="border-2 border-red-600 hover:bg-red-600 hover:text-white font-bold py-3 px-8 transition-colors">
                    VIEW ALL <?php echo e($women->name); ?>'S
                </a>
            </div>
        </div>
        <?php endif; ?>
    </section>

    <!-- Three Image Block Section -->
    <section class="py-16 px-4 bg-gray-100">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 h-auto">
                <!-- RUN Block -->
                <div class="relative overflow-hidden rounded-lg group animate-on-scroll">
                    <img src="https://images.unsplash.com/photo-1679216129631-fbcec034558c?q=70&w=600&auto=format&fit=crop"
                        width="600"
                         height="600"
                         loading="lazy"
                         alt="Run"
                         class="w-full h-full object-cover hover-zoom">
                    <div class="absolute inset-0 bg-black/50 flex flex-col items-center justify-center">
                        <h3 class="text-white text-3xl lg:text-6xl font-bold mb-2">RUN</h3>
                        <p class="text-white text-center px-4 mb-4">Lightweight gear for your daily runs</p>
                        <a href="<?php echo e(route('collections.index')); ?>" class="text-white font-medium underline hover:text-red-400 transition-colors">SHOP NOW</a>
                    </div>
                </div>

                <!-- TRAIN Block -->
                <div class="relative overflow-hidden rounded-lg group animate-on-scroll">
                    <img src="https://images.unsplash.com/photo-1646072508263-af94f0218bf0?q=70&w=600&auto=format&fit=crop"
                         width="600"
                         height="600"
                         loading="lazy"
                         alt="Train"
                         class="w-full h-full object-cover hover-zoom">
                    <div class="absolute inset-0 bg-black/50 flex flex-col items-center justify-center">
                        <h3 class="text-white text-3xl lg:text-6xl font-bold mb-2">TRAIN</h3>
                        <p class="text-white text-center px-4 mb-4">Durable apparel for intense workouts</p>
                        <a href="<?php echo e(route('collections.index')); ?>" class="text-white font-medium underline hover:text-red-400 transition-colors">SHOP NOW</a>
                    </div>
                </div>

                <!-- REC Block -->
                <div class="relative overflow-hidden rounded-lg group animate-on-scroll">
                    <img src="https://images.unsplash.com/photo-1715192383684-24c6614d2b54?q=70&w=600&auto=format&fit=crop"
                         width="600"
                         height="600"
                         loading="lazy"
                         alt="Rec"
                         class="w-full h-full object-cover hover-zoom">
                    <div class="absolute inset-0 bg-black/50 flex flex-col items-center justify-center">
                        <h3 class="text-white text-3xl lg:text-6xl font-bold mb-2">REC</h3>
                        <p class="text-white text-center px-4 mb-4">Comfortable styles for recovery days</p>
                        <a href="<?php echo e(route('collections.index')); ?>" class="text-white font-medium underline hover:text-red-400 transition-colors">SHOP NOW</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Grid - Men's Collection -->

     <section class="py-16 px-4">
        <?php if($categories->isNotEmpty()): ?>
        <div class="container mx-auto">
            <?php if($categories->count() > 1): ?>
            <h2 class="text-3xl font-bold text-center mb-4 animate-on-scroll uppercase"><?php echo e($categories->get(0)->name); ?>'S COLLECTION</h2>

            <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto animate-on-scroll"><?php echo e($categories->get(1)->description); ?></p>


           <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('product-index',['products'=>$categories->get(0)->directProducts->take(8)]);

$__html = app('livewire')->mount($__name, $__params, 'lw-3234203928-2', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

            <div class="text-center mt-12 animate-on-scroll">
                <a href="<?php echo e(route('categories.index', $categories->get(0)->slug)); ?>" class="border-2 border-red-600 hover:bg-red-600 hover:text-white font-bold py-3 px-8 transition-colors uppercase">
                    VIEW ALL <?php echo e($categories->get(1)->name); ?>'S
                </a>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </section>

    <!-- Full-width Lifestyle Banner 2 -->
    <section class="relative w-full h-screen overflow-hidden animate-on-scroll my-20">
        <img src="<?php echo e(asset('imgs/group.jpeg')); ?>"
             loading="lazy"
             alt="Lifestyle Banner"
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/50">
            <div class="absolute bottom-8 left-0 text-left text-white pl-8 md:pl-12">
                <h1 class="text-3xl  lg:text-4xl font-bold my-4 uppercase playfair">WorkFit</h1>
                <p class="text-xl mb-6 max-w-2xl">Be the first to shop our latest collection</p>
                <a href="<?php echo e(route('collections.index')); ?>" class="bg-white hover:bg-red-600  text-gray-950 hover:text-white playfair font-bold py-3 px-8 transition-colors">
                    SHOP NOW
                </a>
            </div>
        </div>
    </section>


    <!-- Just Arrived Products -->
    <section class="px-4">
        <h1 class="text-center font-bold text-5xl mb-2">Just Arrived</h1>
        <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto animate-on-scroll">Discover our latest collection of products</p>
        <?php if($recent->isNotEmpty()): ?>
       <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('product-index',['products'=>$recent]);

$__html = app('livewire')->mount($__name, $__params, 'lw-3234203928-3', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
       <?php endif; ?>
   </section>

   <section class="relative overflow-hidden animate-on-scroll bg-cover bg-fixed"
    style="background-image: url('<?php echo e(asset('imgs/men-bg.jpeg')); ?>'); height: 800px;">

    <div class="absolute inset-0 bg-black/50 w-full">
        <div class="absolute bottom-8 left-0 text-left text-white pl-8 md:pl-12">
            <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-4 uppercase playfair">WorkFit</h1>
            <p class="text-xl mb-6 max-w-2xl">Be the first to shop our latest collection</p>
            <a href="<?php echo e(route('collections.index')); ?>" class="bg-white hover:bg-red-600  text-gray-950 hover:text-white playfair font-bold py-3 px-8 transition-colors">
                SHOP NOW
            </a>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/shereenelshayp/Herd/workfit/resources/views/home.blade.php ENDPATH**/ ?>