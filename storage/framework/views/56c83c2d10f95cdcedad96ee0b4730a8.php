<?php $__env->startSection('content'); ?>
  <!-- Hero -->
  <section class="relative">
    <img src="https://images.unsplash.com/photo-1637666532931-b835a227b955?q=80&w=2940&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Hero" class="w-full h-64 md:h-96 object-cover">
    <div class="absolute inset-0 flex flex-col items-center justify-center text-white bg-black bg-opacity-40">
      <h1 class="text-4xl md:text-6xl font-bold capitalize">
        <?php if($category): ?>
          <?php echo e($category->name); ?>

        <?php else: ?>
          Categories
        <?php endif; ?>
      </h1>
      <p class="mt-2 text-lg">Technical solutions for warm-weather wear</p>
    </div>
  </section>

  <!-- Content -->
  <?php if($category): ?>
    <!-- Show products for specific category -->
    <div class="max-w-7xl mx-auto px-4 py-10 grid grid-cols-1 md:grid-cols-4 gap-8 my-8">
      <!-- Sidebar Filters -->
      <aside class="space-y-6">
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('category-filters', ['categorySlug' => $categorySlug]);

$__html = app('livewire')->mount($__name, $__params, 'lw-445776252-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
      </aside>

      <!-- Product Grid -->
      <main class="md:col-span-3">
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('category-products', ['categorySlug' => $categorySlug]);

$__html = app('livewire')->mount($__name, $__params, 'lw-445776252-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
      </main>
    </div>
  <?php else: ?>
    <!-- Show all categories in grid -->
    <div class="max-w-7xl mx-auto px-4 py-10 my-8">
      <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('categories-grid');

$__html = app('livewire')->mount($__name, $__params, 'lw-445776252-2', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    </div>
  <?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/elragtow/workfit.medsite.dev/resources/views/categories.blade.php ENDPATH**/ ?>