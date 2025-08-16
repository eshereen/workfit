<div class="relative" x-data="{ open: false }"
     x-init="$watch('open', value => $wire.set('showDropdown', value))"
     @currency-changed.window="open = false"
     wire:key="currency-selector">

    <button @click="open = !open"
            class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors">
        <!--<span class="text-lg">{{ $currentSymbol }}</span>-->
        <span class="hidden sm:inline">{{ $currentCurrency }}</span>
        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         @click.away="open = false"
         class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">

        <div class="py-1">
            <!-- Auto-detected currency info -->
            @if($isAutoDetected && $detectedCountry)
            <div class="px-4 py-2 text-xs text-gray-500 border-b border-gray-100">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Auto-detected from {{ $detectedCountry }}
                </div>
            </div>
            @endif

            <!-- Currency options -->
            <button type="button" onclick="changeCurrency('USD')" @click="open = false"
                    class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 {{ $currentCurrency === 'USD' ? 'bg-gray-50 text-gray-900' : 'text-gray-700' }}">
                <div class="flex items-center justify-between">
                    <span class="flex items-center">
                        <span class="text-lg mr-2">$</span>
                        <span>USD - US Dollar</span>
                    </span>
                    @if($currentCurrency === 'USD')
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </div>
            </button>

            <button type="button" onclick="changeCurrency('EGP')" @click="open = false"
                    class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 {{ $currentCurrency === 'EGP' ? 'bg-gray-50 text-gray-900' : 'text-gray-700' }}">
                <div class="flex items-center justify-between">
                    <span class="flex items-center">
                        <span class="text-lg mr-2">E£</span>
                        <span>EGP - Egyptian Pound</span>
                    </span>
                    @if($currentCurrency === 'EGP')
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </div>
            </button>

            <button type="button" onclick="changeCurrency('EUR')" @click="open = false"
                    class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 {{ $currentCurrency === 'EUR' ? 'bg-gray-50 text-gray-900' : 'text-gray-700' }}">
                <div class="flex items-center justify-between">
                    <span class="flex items-center">
                        <span class="text-lg mr-2">€</span>
                        <span>EUR - Euro</span>
                    </span>
                    @if($currentCurrency === 'EUR')
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </div>
            </button>

            <button type="button" onclick="changeCurrency('GBP')" @click="open = false"
                    class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 {{ $currentCurrency === 'GBP' ? 'bg-gray-50 text-gray-900' : 'text-gray-700' }}">
                <div class="flex items-center justify-between">
                    <span class="flex items-center">
                        <span class="text-lg mr-2">£</span>
                        <span>GBP - British Pound</span>
                    </span>
                    @if($currentCurrency === 'GBP')
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </div>
            </button>

            <button type="button" onclick="changeCurrency('AED')" @click="open = false"
                    class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 {{ $currentCurrency === 'AED' ? 'bg-gray-50 text-gray-900' : 'text-gray-700' }}">
                <div class="flex items-center justify-between">
                    <span class="flex items-center">
                        <span class="text-lg mr-2">د.إ</span>
                        <span>AED - UAE Dirham</span>
                    </span>
                    @if($currentCurrency === 'AED')
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </div>
            </button>

            <button type="button" onclick="changeCurrency('SAR')" @click="open = false"
                    class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 {{ $currentCurrency === 'SAR' ? 'bg-gray-50 text-gray-900' : 'text-gray-700' }}">
                <div class="flex items-center justify-between">
                    <span class="flex items-center">
                        <span class="text-lg mr-2">ر.س</span>
                        <span>SAR - Saudi Riyal</span>
                    </span>
                    @if($currentCurrency === 'SAR')
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </div>
            </button>

            <button type="button" onclick="changeCurrency('CAD')" @click="open = false"
                    class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 {{ $currentCurrency === 'CAD' ? 'bg-gray-50 text-gray-900' : 'text-gray-700' }}">
                <div class="flex items-center justify-between">
                    <span class="flex items-center">
                        <span class="text-lg mr-2">C$</span>
                        <span>CAD - Canadian Dollar</span>
                    </span>
                    @if($currentCurrency === 'CAD')
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </div>
            </button>

            <!-- Reset to detected currency -->
            @if($isAutoDetected && $detectedCountry && $currentCurrency !== Session::get('detected_currency', 'USD'))
            <div class="border-t border-gray-100">
                <button type="button" onclick="resetToDetected()" @click="open = false"
                        class="w-full text-left px-4 py-2 text-sm text-blue-600 hover:bg-blue-50">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Reset to detected currency
                    </div>
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Make functions globally available
    window.changeCurrency = function(currencyCode) {
        console.log('Changing currency to:', currencyCode);

        // Get CSRF token from meta tag
        const tokenElement = document.querySelector('meta[name="csrf-token"]');
        if (!tokenElement) {
            console.error('CSRF token meta tag not found');
            // Fallback: reload page anyway
            setTimeout(() => {
                window.location.reload(true);
            }, 1000);
            return;
        }

        const token = tokenElement.getAttribute('content');
        console.log('CSRF token found:', token ? 'Yes' : 'No');

        // Make AJAX request to change currency
        fetch('{{ route("currency.change") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                currency: currencyCode
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                console.log('Currency changed to:', currencyCode);
                // Reload page after 1 second to show new currency
                setTimeout(() => {
                    window.location.reload(true);
                }, 1000);
            } else {
                console.error('Failed to change currency:', data.message);
                // Fallback: reload page anyway
                setTimeout(() => {
                    window.location.reload(true);
                }, 1000);
            }
        })
        .catch(error => {
            console.error('Error changing currency:', error);
            // Fallback: reload page anyway
            setTimeout(() => {
                window.location.reload(true);
            }, 1000);
        });
    };

    window.resetToDetected = function() {
        console.log('Resetting to detected currency');

        // Get CSRF token from meta tag
        const tokenElement = document.querySelector('meta[name="csrf-token"]');
        if (!tokenElement) {
            console.error('CSRF token meta tag not found');
            // Fallback: reload page anyway
            setTimeout(() => {
                window.location.reload(true);
            }, 1000);
            return;
        }

        const token = tokenElement.getAttribute('content');
        console.log('CSRF token found:', token ? 'Yes' : 'No');

        // Make AJAX request to reset currency
        fetch('{{ route("currency.reset") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                console.log('Currency reset to detected');
                // Reload page after 1 second to show new currency
                setTimeout(() => {
                    window.location.reload(true);
                }, 300);
            } else {
                console.error('Failed to reset currency:', data.message);
                // Fallback: reload page anyway
                setTimeout(() => {
                    window.location.reload(true);
                }, 300);
            }
        })
        .catch(error => {
            console.error('Error resetting currency:', error);
            // Fallback: reload page anyway
            setTimeout(() => {
                window.location.reload(true);
            }, 300);
        });
    };
});
</script>
