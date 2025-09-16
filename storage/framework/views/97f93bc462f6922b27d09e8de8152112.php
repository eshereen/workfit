<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="dark">
    <head>
        <?php echo $__env->make('partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-sm flex-col gap-2">
                <div class="flex items-center flex-1 justify-center">

                    <img src="/imgs/workfit_logo_white.png" alt="logo" class="w-20 block group-hover:hidden">
                             <!-- Black logo (only visible on hover) -->
                             <img src="/imgs/workfit_logo_black.png" alt="logo" class="w-20 hidden group-hover:block">
                             </div>
                <div class="flex flex-col gap-6">
                    <?php echo e($slot); ?>

                </div>
            </div>
        </div>
        <?php app('livewire')->forceAssetInjection(); ?>
<?php echo app('flux')->scripts(); ?>

    </body>
</html>
<?php /**PATH /home/elragtow/workfit.medsite.dev/resources/views/components/layouts/auth/simple.blade.php ENDPATH**/ ?>