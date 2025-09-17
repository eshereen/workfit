<div x-data="{
        audio: null,

        init() {
            // Initialize audio
            this.audio = new Audio('/sounds/new-order.mp3');
            this.audio.preload = 'auto';
            this.audio.volume = 0.8;
            this.audioEnabled = false;

            // Enable audio on first user interaction with better error handling
            const enableAudio = async () => {
                try {
                    // Create audio context if needed
                    if (window.AudioContext || window.webkitAudioContext) {
                        const AudioContext = window.AudioContext || window.webkitAudioContext;
                        if (!this.audioContext) {
                            this.audioContext = new AudioContext();
                        }
                        if (this.audioContext.state === 'suspended') {
                            await this.audioContext.resume();
                        }
                    }

                    // Test play audio
                    await this.audio.play();
                    this.audio.pause();
                    this.audio.currentTime = 0;
                    this.audioEnabled = true;
                    console.log('Audio enabled for notifications');

                    // Remove listeners
                    document.removeEventListener('click', enableAudio);
                    document.removeEventListener('keydown', enableAudio);
                    document.removeEventListener('touchstart', enableAudio);
                } catch (e) {
                    console.log('Audio enable failed:', e);
                }
            };

            // Listen for multiple interaction types
            document.addEventListener('click', enableAudio);
            document.addEventListener('keydown', enableAudio);
            document.addEventListener('touchstart', enableAudio);

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

            // Play sound immediately
            this.playNotificationSound();

            // Small delay for other notifications
            setTimeout(() => {
                this.showBrowserNotification(orderData);
                this.showFilamentToast(orderData);
            }, 200);
        },

        playNotificationSound() {
            console.log('Attempting to play notification sound, audioEnabled:', this.audioEnabled);

            if (!this.audioEnabled) {
                console.log('Audio not enabled yet, will play on next interaction');
                return;
            }

            try {
                // Reset audio
                this.audio.currentTime = 0;

                // Ensure volume is set
                this.audio.volume = 0.8;

                // Play with multiple fallbacks
                const playPromise = this.audio.play();

                if (playPromise !== undefined) {
                    playPromise
                        .then(() => {
                            console.log('✅ Order notification sound played successfully');
                        })
                        .catch(e => {
                            console.log('❌ Audio play failed:', e);

                            // Try to play a simple beep as fallback
                            try {
                                const audioContext = this.audioContext || new (window.AudioContext || window.webkitAudioContext)();
                                const oscillator = audioContext.createOscillator();
                                const gainNode = audioContext.createGain();

                                oscillator.connect(gainNode);
                                gainNode.connect(audioContext.destination);

                                oscillator.frequency.value = 800;
                                gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);

                                oscillator.start(audioContext.currentTime);
                                oscillator.stop(audioContext.currentTime + 0.5);

                                console.log('📢 Played fallback beep sound');
                            } catch (beepError) {
                                console.log('❌ Fallback sound also failed:', beepError);
                            }
                        });
                }
            } catch (err) {
                console.log('❌ Audio initialization error:', err);
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
                        <svg class='h-5 w-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                            <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' />
                        </svg>
                    </div>
                    <div class='flex-1'>
                        <p class='font-semibold'>New Order Received!</p>
                        <p class='text-sm'>${orderData.message}</p>
                    </div>
                    <button onclick='this.parentElement.parentElement.remove()' class='text-white hover:text-gray-200'>
                        <svg class='h-4 w-4' fill='currentColor' viewBox='0 0 20 20'>
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
        <div style="margin-bottom: 1rem; padding: 0.75rem; background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 0.5rem; font-size: 14px;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="flex-shrink: 0;">
                        <div style="width: 20px; height: 20px; background-color: #22c55e; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <svg style="width: 12px; height: 12px; color: white;" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 style="font-weight: 500; color: #166534; margin: 0;">New Order Received!</h3>
                        <p style="color: #15803d; margin: 0;">Order #{{ $newOrder->id }} was just placed</p>
                    </div>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <a href="/admin/orders/{{ $newOrder->id }}" target="_blank"
                       style="background-color: #16a34a; color: white; padding: 0.25rem 0.75rem; border-radius: 0.25rem; text-decoration: none; font-size: 14px;"
                       onmouseover="this.style.backgroundColor='#15803d'"
                       onmouseout="this.style.backgroundColor='#16a34a'">
                        View Order
                    </a>
                    <button wire:click="dismissNotification"
                            style="color: #4ade80; background: none; border: none; cursor: pointer; padding: 0.25rem;"
                            onmouseover="this.style.color='#16a34a'"
                            onmouseout="this.style.color='#4ade80'">
                        <svg style="width: 16px; height: 16px;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
