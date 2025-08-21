<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WorkFit</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Livewire Styles -->
    @livewireStyles

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/@preline/preline@2.0.0/dist/preline.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
     <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        mannikan: {
                            pink: '#FF6B98',
                            lightpink: '#FFD1DD',
                            darkpink: '#D84A77',
                        }
                    },
                    fontFamily: {
                        'playfair': ['"Playfair Display"', 'serif'],
                        'poppins': ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .hero-section {
            background-image: url('https://images.unsplash.com/photo-1490481651871-ab68de25d43d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
            background-size: cover;
            background-position: center;
        }
        .celebrity-slide {
            scroll-snap-type: x mandatory;
        }
        .celebrity-slide > div {
            scroll-snap-align: start;
        }
        .testimonial-card::before {
            content: '"';
            position: absolute;
            top: -30px;
            left: 20px;
            font-size: 80px;
            font-family: 'Playfair Display', serif;
            color: rgba(255, 107, 152, 0.2);
        }

        /* Custom styles for animations and transitions */
        .hover-zoom {
            transition: transform 0.3s ease;
        }
        .hover-zoom:hover {
            transform: scale(1.05);
        }
        .product-image-container {
            overflow: hidden;
        }
        .product-image-secondary {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .product-image-container:hover .product-image-primary {
            opacity: 0;
        }
        .product-image-container:hover .product-image-secondary {
            opacity: 1;
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .slide-in {
            animation: slideIn 0.7s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Video container styles for hero section */
        .video-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        .video-container video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
        }
        
        /* Hero content positioning */
        .hero-content {
            position: relative;
            z-index: 20;
        }
        
        /* Notification styles */
        .notification {
            transition: all 0.3s ease;
        }
        .notification.show {
            transform: translateX(0);
        }
        .notification.hide {
            transform: translateX(100%);
        }
    </style>
</head>
<body class="text-gray-800 bg-white" class="font-sans antialiased text-black bg-white">

    @include('layouts.navbar')
    
    <!-- Notification System -->
    <div id="notification-container" class="fixed top-4 right-4 z-50"></div>

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
