<div class="flex flex-col gap-6">
    <div class="text-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e(__('Log in to your account')); ?></h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2"><?php echo e(__('Enter your email and password below to log in')); ?></p>
    </div>

    <!-- Session Status -->
    <!--[if BLOCK]><![endif]--><?php if(session('status')): ?>
        <div class="text-center p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-green-700"><?php echo e(session('status')); ?></p>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <form wire:submit="login" class="flex flex-col gap-6">
        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <?php echo e(__('Email address')); ?>

            </label>
            <input
                id="email"
                wire:model="email"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-white dark:bg-gray-700 dark:text-white"
            />
        </div>

        <!-- Password -->
        <div class="relative">
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <?php echo e(__('Password')); ?>

            </label>
            <input
                id="password"
                wire:model="password"
                type="password"
                required
                autocomplete="current-password"
                placeholder="<?php echo e(__('Password')); ?>"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-white dark:bg-gray-700 dark:text-white"
            />

            <!--[if BLOCK]><![endif]--><?php if(Route::has('password.request')): ?>
                <a href="<?php echo e(route('password.request')); ?>" class="absolute end-0 top-0 text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400" wire:navigate>
                    <?php echo e(__('Forgot your password?')); ?>

                </a>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input
                id="remember"
                wire:model="remember"
                type="checkbox"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                <?php echo e(__('Remember me')); ?>

            </label>
        </div>

        <div class="flex items-center justify-end">
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                <?php echo e(__('Log in')); ?>

            </button>
        </div>
    </form>

    <!--[if BLOCK]><![endif]--><?php if(Route::has('register')): ?>
        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span><?php echo e(__('Don\'t have an account?')); ?></span>
            <a href="<?php echo e(route('register')); ?>" class="text-blue-600 hover:text-blue-500 dark:text-blue-400" wire:navigate><?php echo e(__('Sign up')); ?></a>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH /Users/shereenelshayp/Herd/workfit/resources/views/livewire/auth/login.blade.php ENDPATH**/ ?>