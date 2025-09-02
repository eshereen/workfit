<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo e($title ?? 'WorkFit'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <!-- Performance optimizations -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="//fonts.googleapis.com">

    <!-- Resource hints for critical resources -->
    <link rel="preload" href="/videos/workfit-lg.mp4" as="video" type="video/mp4" media="(min-width: 768px)">
    <link rel="preload" href="/videos/workfit-mobile.mp4" as="video" type="video/mp4" media="(max-width: 767px)">

    <!-- Livewire Styles -->
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

   </link>
   <script src="https://cdn.jsdelivr.net/npm/medium-zoom@1.0.6/dist/medium-zoom.min.js"></script>


    <!-- Minimal critical CSS for performance -->
    <style>
        /* Only essential styles to prevent layout shift */
        .loading-lazy { opacity: 0; transition: opacity 0.3s; }
        .loading-lazy.loaded { opacity: 1; }
        .aspect-\[4\/5\] { aspect-ratio: 4/5; }

                /* Product image hover effect */
        .product-image-container {
            position: relative;
        }

        .product-image-container .main-image {
            opacity: 1;
            transition: opacity 0.5s ease;
            z-index: 1;
        }

        .product-image-container .gallery-image {
            opacity: 0;
            transition: opacity 0.5s ease;
            z-index: 2;
        }

        .product-image-container:hover .main-image {
            opacity: 0;
        }

        .product-image-container:hover .gallery-image {
            opacity: 1;
        }
    </style>

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

    <!-- Alpine.js for interactive components -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Livewire Scripts -->
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>




    <!-- Performance optimization script -->
    <script>
        // Lazy loading optimization
        document.addEventListener('DOMContentLoaded', function() {
            // Intersection Observer for lazy loading
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.classList.add('loaded');
                        observer.unobserve(img);
                    }
                });
            });

            // Observe all lazy images
            document.querySelectorAll('img[loading="lazy"]').forEach(img => {
                img.classList.add('loading-lazy');
                imageObserver.observe(img);
            });

            // Preload critical images
            const criticalImages = document.querySelectorAll('img[fetchpriority="high"]');
            criticalImages.forEach(img => {
                if (img.src) {
                    const link = document.createElement('link');
                    link.rel = 'preload';
                    link.as = 'image';
                    link.href = img.src;
                    document.head.appendChild(link);
                }
            });

            // Product image hover effect (JavaScript backup)
            document.querySelectorAll('.product-image-container').forEach(container => {
                const mainImage = container.querySelector('.main-image');
                const galleryImage = container.querySelector('.gallery-image');

                if (mainImage && galleryImage) {
                    // Ensure gallery image is hidden initially
                    galleryImage.style.opacity = '0';

                    container.addEventListener('mouseenter', function() {
                        mainImage.style.opacity = '0';
                        galleryImage.style.opacity = '1';
                    });

                    container.addEventListener('mouseleave', function() {
                        mainImage.style.opacity = '1';
                        galleryImage.style.opacity = '0';
                    });
                }
            });
        });
    </script>



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