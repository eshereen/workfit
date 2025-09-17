<div x-data="{
        audio: null,
        lastCheck: Date.now(),

        init() {
            this.audio = new Audio('/sounds/new-order.mp3');
            this.audio.preload = 'auto';
            this.audio.volume = 0.7;

            // Listen for order sound event
            Livewire.on('play-order-sound', (data) => {
                this.playOrderSound(data[0]);
            });

            console.log('Order notification widget initialized');
        },

        playOrderSound(data) {
            console.log('Playing order notification sound', data);

            try {
                this.audio.currentTime = 0;
                this.audio.play()
                    .then(() => console.log('Sound played successfully'))
                    .catch(e => {
                        console.log('Audio play failed:', e);
                        // Try again on next user interaction
                        document.addEventListener('click', () => {
                            this.audio.play().catch(() => {});
                        }, { once: true });
                    });
            } catch (err) {
                console.log('Audio error:', err);
            }

            // Show browser notification if permission granted
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification('New Order Received!', {
                    body: `Order #${data.latestOrderId} has been placed`,
                    icon: '/favicon.ico',
                    tag: 'new-order'
                });
            }
        },

        requestNotificationPermission() {
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission();
            }
        }
     }"
     wire:poll.8s="checkForNewOrders"
     x-init="requestNotificationPermission()"
     class="hidden">

    <!-- Hidden widget that handles order notifications -->
</div>
