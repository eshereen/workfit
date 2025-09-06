<div class="relative currency-selector x-cloak" x-data="{ open: false }"
          x-init="$watch('open', value => $wire?.set('showDropdown', value))"
     @currency-changed.window="open = false"
     wire:key="{{ $this->getId() }}"
     data-component-id="{{ $this->getId() }}"
     data-component-name="currency-selector">


@if(request()->routeIs('home'))
    <button @click="open = !open"
            class="flex items-center space-x-2 px-3 py-2 text-sm font-semibold text-white group-hover:text-gray-900 hover:text-red-600  rounded-md transition-colors ">
            @else
     <button @click="open = !open"
            class="flex items-center space-x-2 px-3 py-2 text-sm font-semibold text-gray-900 hover:text-red-600  rounded-md transition-colors">
            @endif
        <span class="text-lg">{{ $currentSymbol }}</span>
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
            <button type="button" onclick="changeCurrencyManual('USD')" @click="open = false"
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

            <button type="button" onclick="changeCurrencyManual('EGP')" @click="open = false"
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

            <button type="button" onclick="changeCurrencyManual('EUR')" @click="open = false"
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

            <button type="button" onclick="changeCurrencyManual('GBP')" @click="open = false"
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

            <button type="button" onclick="changeCurrencyManual('AED')" @click="open = false"
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

            <button type="button" onclick="changeCurrencyManual('SAR')" @click="open = false"
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

            <button type="button" onclick="changeCurrencyManual('AUD')" @click="open = false"
                    class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 {{ $currentCurrency === 'AUD' ? 'bg-gray-50 text-gray-900' : 'text-gray-700' }}">
                <div class="flex items-center justify-between">
                    <span class="flex items-center">
                        <span class="text-lg mr-2">A$</span>
                        <span>AUD - Australian Dollar</span>
                    </span>
                    @if($currentCurrency === 'AUD')
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </div>
            </button>

            <button type="button" onclick="changeCurrencyManual('CAD')" @click="open = false"
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

            <button type="button" onclick="changeCurrencyManual('JPY')" @click="open = false"
                    class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 {{ $currentCurrency === 'JPY' ? 'bg-gray-50 text-gray-900' : 'text-gray-700' }}">
                <div class="flex items-center justify-between">
                    <span class="flex items-center">
                        <span class="text-lg mr-2">¥</span>
                        <span>JPY - Japanese Yen</span>
                    </span>
                    @if($currentCurrency === 'JPY')
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </div>
            </button>

            <button type="button" onclick="changeCurrencyManual('CHF')" @click="open = false"
                    class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 {{ $currentCurrency === 'CHF' ? 'bg-gray-50 text-gray-900' : 'text-gray-700' }}">
                <div class="flex items-center justify-between">
                    <span class="flex items-center">
                        <span class="text-lg mr-2">CHF</span>
                        <span>CHF - Swiss Franc</span>
                    </span>
                    @if($currentCurrency === 'CHF')
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </div>
            </button>

            <!-- Reset to detected currency -->
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
    // Listen for Livewire events from other components
    document.addEventListener('livewire:init', () => {
        Livewire.on('currency-changed', (data) => {
            console.log('💱 CurrencySelector: Received currency-changed event:', data);
        });

        Livewire.on('country-changed', (data) => {
            console.log('🌍 CurrencySelector: Received country-changed event:', data);
        });
    });

    // Also listen for global Livewire events
    window.addEventListener('currency-changed', function(event) {
        console.log('💱 Global currency-changed event:', event.detail);
    });

    window.addEventListener('country-changed', function(event) {
        console.log('🌍 Global country-changed event:', event.detail);
    });

    // Listen for custom currency update event
    window.addEventListener('currency-updated', function(event) {
        console.log('💱 Custom currency-updated event received:', event.detail);
        const { currency, symbol } = event.detail;

        // Try to call Livewire method directly on CurrencySelector only
        if (window.Livewire) {
            const currencySelector = document.querySelector('.currency-selector[wire\\:id]');
            if (currencySelector) {
                const wireId = currencySelector.getAttribute('wire:id');
                console.log('🎯 Found currency selector component, calling updateToCurrency');
                try {
                    const livewireComponent = window.Livewire.find(wireId);
                    if (livewireComponent && livewireComponent.call) {
                        livewireComponent.call('updateToCurrency', currency);
                    }
                } catch (e) {
                    console.error('Error calling CurrencySelector method:', e);
                }
            }
        }
    });

    // Listen for browser events from CheckoutForm
    window.addEventListener('livewire-currency-changed', function(event) {
        console.log('🌐 Browser livewire-currency-changed event received:', event.detail);
        const { currency, symbol } = event.detail;

        if (window.Livewire) {
            const currencySelector = document.querySelector('.currency-selector[wire\\:id]');
            if (currencySelector) {
                const wireId = currencySelector.getAttribute('wire:id');
                console.log('🎯 Updating currency selector from browser event');
                try {
                    const livewireComponent = window.Livewire.find(wireId);
                    if (livewireComponent) {
                        livewireComponent.call('updateToCurrency', currency);
                        livewireComponent.$refresh();
                    }
                } catch (e) {
                    console.error('Error calling CurrencySelector from browser event:', e);
                }
            }
        }
    });

    window.addEventListener('livewire-country-changed', function(event) {
        console.log('🌍 Browser livewire-country-changed event received:', event.detail);
        const { countryCode, currency } = event.detail;

        if (window.Livewire) {
            const currencySelector = document.querySelector('.currency-selector[wire\\:id]');
            if (currencySelector) {
                const wireId = currencySelector.getAttribute('wire:id');
                console.log('🎯 Updating currency selector from country change');
                try {
                    const livewireComponent = window.Livewire.find(wireId);
                    if (livewireComponent) {
                        livewireComponent.call('handleCountryChanged', countryCode);
                    }
                } catch (e) {
                    console.error('Error calling CurrencySelector from country event:', e);
                }
            }
        }
    });

    // Global test function for currency update
    window.testCurrencyFromConsole = function(currency = 'AUD') {
        console.log('🧪 Testing currency update from console to:', currency);

        const currencySelector = document.querySelector('.currency-selector[wire\\:id]');
        if (currencySelector && window.Livewire) {
            const wireId = currencySelector.getAttribute('wire:id');
            const component = window.Livewire.find(wireId);
            if (component) {
                console.log('📞 Calling updateToCurrency on currency selector');
                component.call('updateToCurrency', currency);
            } else {
                console.error('❌ Currency component not found');
            }
        } else {
            console.error('❌ Currency selector element not found');
        }
    };

        // Simple JavaScript function to change currency (bypassing Livewire frontend issues)
    window.changeCurrencyManual = function(currencyCode) {
        console.log('🔄 Manual currency change to:', currencyCode);

        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!token) {
            console.error('❌ CSRF token not found');
            return;
        }

        // Make AJAX call to Laravel route
        fetch('/currency/change', {
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
        .then(response => response.json())
        .then(data => {
            console.log('✅ Currency change response:', data);
            if (data.success) {
                // Show success notification
                if (window.showNotification) {
                    window.showNotification(`Currency changed to ${currencyCode}`, 'success');
                }

                // Force page reload to update navbar symbol
                console.log('🔄 Reloading page to update navbar symbol');
                window.location.reload();

                return;
            } else {
                console.error('❌ Currency change failed:', data.message);
            }
        })
        .catch(error => {
            console.error('❌ Error changing currency:', error);
        });
    };

    console.log('💱 CurrencySelector: Using manual AJAX approach');

    // Reset function - simplified for Alpine.js approach
    window.resetToDetected = function() {
        console.log('🔄 Resetting to detected currency (Alpine.js approach)');
        // Reload page as simple fallback - can be enhanced with Livewire method later
                window.location.reload(true);
    };
});
</script>
