<div x-data="{
        audio: null,
        init() {
            this.audio = new Audio('/sounds/new-order.mp3');
            this.audio.preload = 'auto';

            // Listen for Livewire dispatched events
            Livewire.on('new-order-notification', (data) => {
                this.playNotification(data[0]);
            });
        },

        playNotification(orderData) {
            // Play sound
            try {
                this.audio.currentTime = 0;
                this.audio.play().catch(e => console.log('Audio play failed:', e));
            } catch (err) {
                console.log('Audio error:', err);
            }

            // Show Filament notification
            new FilamentNotification()
                .title('New Order Received!')
                .body(orderData.message || 'A new order has been placed')
                .success()
                .persistent()
                .actions([
                    new FilamentNotificationAction('view')
                        .label('View Order')
                        .url('/admin/orders/' + orderData.orderId)
                        .markAsRead(),
                    new FilamentNotificationAction('dismiss')
                        .label('Dismiss')
                        .close()
                ])
                .send();
        }
     }"
     wire:poll.10s="checkForNewOrders"
     class="hidden">

    @if($hasNewOrder)
        <div class="fixed top-4 right-4 z-50 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg animate-pulse">
            <div class="flex items-center gap-2">
                <span class="animate-ping inline-flex h-2 w-2 rounded-full bg-white opacity-75"></span>
                New Order Alert!
                <button wire:click="markAsRead" class="ml-2 text-white hover:text-gray-200">
                    ✕
                </button>
            </div>
        </div>
    @endif
</div>
