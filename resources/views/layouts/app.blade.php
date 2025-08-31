<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'WorkFit' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Performance optimizations -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://images.unsplash.com">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="//images.unsplash.com">

    <!-- Resource hints for critical resources -->
    <link rel="preload" href="/videos/workfit-lg.mp4" as="video" type="video/mp4">
    <link rel="preload" href="/videos/workfit-mobile.mp4" as="video" type="video/mp4">

    <!-- Livewire Styles -->
    @livewireStyles
   </link>
   <script src="https://cdn.jsdelivr.net/npm/medium-zoom@1.0.6/dist/medium-zoom.min.js"></script>


    <!-- Preload critical CSS -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

    @include('layouts.navbar')



    <!-- Notification System -->
    <div id="notification-container" class="fixed top-4 right-4 z-50 p-4 text-white"></div>

    @yield('content')

    @include('layouts.footer')

    <!-- Livewire Scripts -->
    @livewireScripts



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
