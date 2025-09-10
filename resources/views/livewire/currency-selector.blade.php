<div class="relative currency-selector x-cloak"
     x-data="{ open: false }"
     x-init="$watch('open', value => $wire?.set('showDropdown', value))"
     @currency-changed.window="open = false"
     wire:key="{{ $this->getId() }}"
     data-component-id="{{ $this->getId() }}"
     data-component-name="currency-selector">

    @if(request()->routeIs('home'))
        <button @click="open = !open" type="button"
                class="flex items-center space-x-2 px-3 py-2 text-sm font-semibold text-white group-hover:text-gray-900 hover:text-red-600 rounded-md transition-colors">
    @else
        <button @click="open = !open" type="button"
                class="flex items-center space-x-2 px-3 py-2 text-sm font-semibold text-gray-900 hover:text-red-600 rounded-md transition-colors">
    @endif
        <span class="text-lg">{{ $currentSymbol }}</span>
        <span class="hidden sm:inline">{{ $currentCurrency }}</span>
        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <!-- Dropdown -->
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
            @foreach ([
                'USD' => '$ USD - US Dollar',
                'EGP' => 'E£ EGP - Egyptian Pound',
                'EUR' => '€ EUR - Euro',
                'GBP' => '£ GBP - British Pound',
                'AED' => 'د.إ AED - UAE Dirham',
                'SAR' => 'ر.س SAR - Saudi Riyal',
                'AUD' => 'A$ AUD - Australian Dollar',
                'CAD' => 'C$ CAD - Canadian Dollar',
                'JPY' => '¥ JPY - Japanese Yen',
                'CHF' => 'CHF CHF - Swiss Franc',
            ] as $code => $label)
                <button type="button" onclick="changeCurrencyManual('{{ $code }}')" @click="open = false"
                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 {{ $currentCurrency === $code ? 'bg-gray-50 text-gray-900' : 'text-gray-700' }}">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center">
                            <span class="text-lg mr-2">{{ explode(' ', $label)[0] }}</span>
                            <span>{{ substr($label, strpos($label, ' ') + 1) }}</span>
                        </span>
                        @if($currentCurrency === $code)
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                    </div>
                </button>
            @endforeach

            <!-- Reset -->
            @if($isAutoDetected && $detectedCountry && $currentCurrency !== Session::get('detected_currency', 'USD'))
            <div class="border-t border-gray-100">
                <button type="button" wire:click="resetToDetected()" @click="open = false"
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
    // Listen for currency changes
    window.changeCurrencyManual = function(currencyCode) {
        console.log('🔄 Changing currency to:', currencyCode);

        // CSRF
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!token) {
            console.error('❌ CSRF token not found');
            return;
        }

        fetch('/currency/change', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ currency: currencyCode })
        })
        .then(response => response.json())
        .then(data => {
            console.log('✅ Response:', data);
            if (data.success) {
                // Show notification if available
                if (window.showNotification) {
                    window.showNotification(`Currency changed to ${currencyCode}`, 'success');
                }

                // Refresh Livewire component instead of reloading page
                const currencySelector = document.querySelector('.currency-selector[wire\\:id]');
                if (currencySelector && window.Livewire) {
                    const wireId = currencySelector.getAttribute('wire:id');
                    const component = window.Livewire.find(wireId);
                    if (component) {
                        component.call('updateToCurrency', currencyCode);
                        component.$refresh();
                    }
                }

                // Dispatch global events for other components to listen to
                window.dispatchEvent(new CustomEvent('currency-changed', { detail: currencyCode }));
                window.dispatchEvent(new CustomEvent('global-currency-changed', { detail: currencyCode }));

                // Also dispatch Livewire events
                if (window.Livewire) {
                    window.Livewire.dispatch('currencyChanged', { currency: currencyCode });
                    window.Livewire.dispatch('currency-changed', { currency: currencyCode });
                    window.Livewire.dispatch('global-currency-changed', { currency: currencyCode });
                }
            } else {
                console.error('❌ Currency change failed:', data.message);
            }
        })
        .catch(error => {
            console.error('❌ Error:', error);
        });
    };

    // Reset currency handler
    window.resetToDetected = function() {
        console.log('🔄 Resetting to detected currency');
        const currencySelector = document.querySelector('.currency-selector[wire\\:id]');
        if (currencySelector && window.Livewire) {
            const wireId = currencySelector.getAttribute('wire:id');
            const component = window.Livewire.find(wireId);
            if (component) {
                component.call('resetToDetected');
                component.$refresh();

                // Dispatch global events for other components to listen to
                window.dispatchEvent(new CustomEvent('currency-changed', { detail: null }));
                window.dispatchEvent(new CustomEvent('global-currency-changed', { detail: null }));

                // Also dispatch Livewire events
                window.Livewire.dispatch('currencyChanged', { currency: null });
                window.Livewire.dispatch('currency-changed', { currency: null });
                window.Livewire.dispatch('global-currency-changed', { currency: null });
            }
        }
    };
});
</script>
