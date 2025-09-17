<div x-data="{
        audio: null,

        init() {
            // Initialize audio
            this.audio = new Audio('/sounds/new-order.mp3');
            this.audio.preload = 'auto';
            this.audio.volume = 0.8;

            // Listen for new order events
            Livewire.on('new-order-created', (data) => {
                this.handleNewOrder(data[0]);
            });

            // Request notification permission
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission();
            }

            console.log('Order notification system initialized');
        },

        handleNewOrder(orderData) {
            console.log('New order detected:', orderData);

            // Play sound
            this.playNotificationSound();

            // Show browser notification
            this.showBrowserNotification(orderData);

            // Show Filament toast
            this.showFilamentToast(orderData);
        },

        playNotificationSound() {
            try {
                this.audio.currentTime = 0;
                this.audio.play()
                    .then(() => console.log('Order notification sound played'))
                    .catch(e => {
                        console.log('Audio play failed (user interaction required):', e);
                        // Try to play on next user interaction
                        document.addEventListener('click', () => {
                            this.audio.play().catch(() => {});
                        }, { once: true });
                    });
            } catch (err) {
                console.log('Audio initialization error:', err);
            }
        },

        showBrowserNotification(orderData) {
            if ('Notification' in window && Notification.permission === 'granted') {
                const notification = new Notification('🎉 New Order Received!', {
                    body: orderData.message,
                    icon: '/favicon.ico',
                    tag: 'new-order-' + orderData.orderId,
                    requireInteraction: true
                });

                // Auto close after 10 seconds
                setTimeout(() => notification.close(), 10000);
            }
        },

        showFilamentToast(orderData) {
            // Create a custom toast notification
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 z-[9999] bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full';
            toast.innerHTML = `
                <div class='flex items-center space-x-3'>
                    <div class='flex-shrink-0'>
                        <svg class='h-6 w-6' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                            <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' />
                        </svg>
                    </div>
                    <div class='flex-1'>
                        <p class='font-semibold'>New Order Received!</p>
                        <p class='text-sm'>${orderData.message}</p>
                    </div>
                    <button onclick='this.parentElement.parentElement.remove()' class='text-white hover:text-gray-200'>
                        <svg class='h-5 w-5' fill='currentColor' viewBox='0 0 20 20'>
                            <path fill-rule='evenodd' d='M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z' clip-rule='evenodd' />
                        </svg>
                    </button>
                </div>
            `;

            document.body.appendChild(toast);

            // Slide in
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);

            // Auto remove after 8 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 8000);
        }
     }"
     wire:poll.8s="checkForNewOrders">

    @if($hasNewOrder && $newOrder)
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-green-800">New Order Received!</h3>
                        <p class="text-sm text-green-700">Order #{{ $newOrder->id }} was just placed</p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <a href="/admin/orders/{{ $newOrder->id }}" target="_blank"
                       class="text-sm bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded">
                        View Order
                    </a>
                    <button wire:click="dismissNotification"
                            class="text-green-400 hover:text-green-600">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
