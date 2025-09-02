<?php $__env->startSection('content'); ?>
    <!-- Main Content -->
    <main class="container mx-auto px-6 py-40">
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('cart-index');

$__html = app('livewire')->mount($__name, $__params, 'lw-1693210563-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/shereenelshayp/Herd/workfit/resources/views/cart.blade.php ENDPATH**/ ?>