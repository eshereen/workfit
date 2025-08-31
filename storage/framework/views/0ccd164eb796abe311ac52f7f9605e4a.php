<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo e($title ?? 'WorkFit'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">


    <!-- Livewire Styles -->
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

   </link>
   <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">    </link>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
<!--Favicons-->
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/site.webmanifest">

     <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'playfair': ['"Playfair Display"', 'serif'],
                        'poppins': ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>

</head>
<body class="bg-white text-gray-950 antialiased">

    <?php echo $__env->make('layouts.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>



    <!-- Notification System -->
    <div id="notification-container" class="fixed top-4 right-4 z-50 p-4 text-white"></div>

    <?php echo $__env->yieldContent('content'); ?>

    <?php echo $__env->make('layouts.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Livewire Scripts -->
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>




    <!-- Notification System Script -->
    <script>
        // Global function to show notifications
        function showNotification(message, type = 'success') {
            const container = document.getElementById('notification-container');
            const notification = document.createElement('div');

            notification.className = `notification mb-4 p-4 rounded-lg shadow-lg text-white transform translate-x-full ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            }`;
            notification.textContent = message;

            container.appendChild(notification);

            // Show notification
            setTimeout(() => {
                notification.classList.add('show');
                notification.classList.remove('translate-x-full');
            }, 100);

            // Hide and remove notification
            setTimeout(() => {
                notification.classList.add('hide');
                notification.classList.remove('show');
                setTimeout(() => {
                    if (container.contains(notification)) {
                        container.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        // Listen for Livewire notification events
        document.addEventListener('livewire:init', () => {
            Livewire.on('showNotification', (data) => {
                showNotification(data.message, data.type);
            });
        });
    </script>
</body>
</html>
<?php /**PATH /Users/shereenelshayp/Herd/workfit/resources/views/layouts/app.blade.php ENDPATH**/ ?>