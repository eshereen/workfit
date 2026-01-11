<script>
    // Fix for profile dropdown auto-opening after login
    document.addEventListener('DOMContentLoaded', function() {
        // Close any open Alpine.js dropdowns on page load
        setTimeout(() => {
            // Find all Alpine components with open state
            const alpineElements = document.querySelectorAll('[x-data]');
            
            alpineElements.forEach(element => {
                // Check if this is a dropdown with an 'open' or 'isOpen' state
                if (element.__x) {
                    const alpineData = element.__x.$data;
                    
                    // Close common dropdown states
                    if (alpineData.hasOwnProperty('open')) {
                        alpineData.open = false;
                    }
                    if (alpineData.hasOwnProperty('isOpen')) {
                        alpineData.isOpen = false;
                    }
                }
            });
        }, 100);
    });

    // Additional fix: Listen for Livewire navigation
    document.addEventListener('livewire:navigated', function() {
        setTimeout(() => {
            const openDropdowns = document.querySelectorAll('[x-data]');
            openDropdowns.forEach(element => {
                if (element.__x) {
                    const alpineData = element.__x.$data;
                    if (alpineData.hasOwnProperty('open')) {
                        alpineData.open = false;
                    }
                    if (alpineData.hasOwnProperty('isOpen')) {
                        alpineData.isOpen = false;
                    }
                }
            });
        }, 50);
    });
</script>
