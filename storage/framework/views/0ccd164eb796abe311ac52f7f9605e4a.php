<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo e($title ?? 'WorkFit'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <!-- Performance optimizations -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://images.unsplash.com">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="//images.unsplash.com">

    <!-- Resource hints for critical resources -->
    <link rel="preload" href="/videos/workfit-lg.mp4" as="video" type="video/mp4">
    <link rel="preload" href="/videos/workfit-mobile.mp4" as="video" type="video/mp4">

    <!-- Livewire Styles -->
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

   </link>
   <script src="https://cdn.jsdelivr.net/npm/medium-zoom@1.0.6/dist/medium-zoom.min.js"></script>


    <!-- Preload critical CSS -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>

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
    <div id="notification-container" class="fixed top-4 right-4 z-[9999] p-4 text-white" style="pointer-events: none;"></div>

    <?php echo $__env->yieldContent('content'); ?>

    <?php echo $__env->make('layouts.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Livewire Scripts -->
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>




    <!-- Notification System Script -->
    <script>
                // Global function to show notifications
        function showNotification(message, type = 'success') {
            const container = document.getElementById('notification-container');

            if (!container) {
                return;
            }

            const notification = document.createElement('div');
            notification.className = `notification mb-4 p-4 rounded-lg shadow-lg transform translate-x-full transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            }`;

            // Create a span element for the text
            const textSpan = document.createElement('span');
            textSpan.textContent = message;
            textSpan.style.color = 'white';
            textSpan.style.fontSize = '18px';
            textSpan.style.fontWeight = 'bold';
            textSpan.style.display = 'block';
            textSpan.style.textAlign = 'center';

            notification.appendChild(textSpan);

            notification.style.zIndex = '9999';
            notification.style.minWidth = '300px';
            notification.style.border = '3px solid black';
            notification.style.backgroundColor = type === 'success' ? '#10B981' : '#EF4444';
            notification.style.padding = '16px';
            notification.style.marginBottom = '16px';

            container.appendChild(notification);

            // Show notification
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
                notification.style.transform = 'translateX(0)';
            }, 100);

            // Hide and remove notification
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                notification.style.opacity = '0';
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
                let message, type;

                if (Array.isArray(data)) {
                    message = data[0]?.message || data[0];
                    type = data[0]?.type || 'success';
                } else if (typeof data === 'object') {
                    message = data.message;
                    type = data.type || 'success';
                } else {
                    message = data;
                    type = 'success';
                }

                showNotification(message, type);
            });
        });
    </script>
</body>
</html>
<?php /**PATH /Users/shereenelshayp/Herd/workfit/resources/views/layouts/app.blade.php ENDPATH**/ ?>