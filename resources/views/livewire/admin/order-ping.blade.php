<div x-data
     x-init="
        const audio = new Audio('/sounds/new-order.mp3');
        window.addEventListener('admin-new-order', (e) => {
            try { audio.currentTime = 0; audio.play(); } catch (err) {}
            if (window.filament?.notifications) {
                window.filament.notifications.notify({
                    title: 'New order received',
                    body: 'Order #' + (e.detail?.number ?? e.detail?.id),
                    status: 'success',
                });
            }
        });
     "
     wire:poll.15s="check">
</div>
