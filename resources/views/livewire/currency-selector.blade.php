<style>
    .currency-dropdown-scroll {
        scrollbar-width: thin;
        scrollbar-color: #D1D5DB #F3F4F6;
    }
    .currency-dropdown-scroll::-webkit-scrollbar {
        width: 6px;
    }
    .currency-dropdown-scroll::-webkit-scrollbar-track {
        background: #F3F4F6;
        border-radius: 3px;
    }
    .currency-dropdown-scroll::-webkit-scrollbar-thumb {
        background: #D1D5DB;
        border-radius: 3px;
    }
    .currency-dropdown-scroll::-webkit-scrollbar-thumb:hover {
        background: #9CA3AF;
    }
</style>

<div class="relative currency-selector x-cloak"
     x-data="{
       open: false,
       scrolled: false,
       isHome: {{ request()->routeIs('home') ? 'true' : 'false' }},
       init() {
         this.$watch('open', value => $wire?.set('showDropdown', value));
         // Listen to scroll events from parent navbar
         window.addEventListener('scroll', () => {
           this.scrolled = window.scrollY > 10;
         });
       }
     }"
     @currency-changed.window="open = false"
     wire:key="{{ $this->getId() }}"
     data-component-id="{{ $this->getId() }}"
     data-component-name="currency-selector">

    <button @click="open = !open" type="button"
            class="flex items-center px-1 py-2 space-x-2 text-sm font-semibold rounded-md transition-colors md:px-3 hover:text-red-600"
            :class="isHome && !scrolled ? 'text-white' : 'text-gray-900'">
            @php
                $countryCodeMap = [
                    'USD' => 'US',
                    'EUR' => 'EU',
                    'GBP' => 'GB',
                    'EGP' => 'EG',
                    'AED' => 'AE',
                    'SAR' => 'SA',
                    'KWD' => 'KW',
                    'QAR' => 'QA',
                    'BHD' => 'BH',
                    'OMR' => 'OM',
                    'JOD' => 'JO',
                    'LBP' => 'LB',
                    'IQD' => 'IQ',
                    'LYD' => 'LY',
                    'TND' => 'TN',
                    'DZD' => 'DZ',
                    'MAD' => 'MA',
                    'SDG' => 'SD',
                ];
                $countryCode = $countryCodeMap[$currentCurrency] ?? 'US';
            @endphp
            <span class="inline-flex items-center">
                <span class="overflow-hidden w-6 h-4 rounded shadow-sm">{!! country_flag($countryCode) !!}</span>
            </span>
            <span class="hidden sm:inline" :class="isHome && !scrolled ? 'text-white' : 'text-gray-900'">{{ $currentCurrency }}</span>


     {{-- <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg> --}}
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
         class="absolute right-0 z-50 mt-2 w-80 bg-white rounded-md ring-1 ring-black ring-opacity-5 shadow-lg md:w-96 md:right-4"
         style="display: none;">

        <div class="overflow-y-scroll py-1 currency-dropdown-scroll" style="max-height: 400px;">
            <!-- Auto-detected currency info -->
            @if($isAutoDetected && $detectedCountry)
            <div class="px-4 py-2 text-xs text-gray-500 border-b border-gray-100">
                <div class="flex items-center">
                    <svg class="mr-2 w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Auto-detected from {{ $detectedCountry }}
                </div>
            </div>
            @endif

            <!-- Currency options -->
            @foreach ([
                'USD' => ['country' => 'US', 'name' => 'USD - US Dollar'],
                'EUR' => ['country' => 'EU', 'name' => 'EUR - Euro'],
                'GBP' => ['country' => 'GB', 'name' => 'GBP - British Pound'],
                'EGP' => ['country' => 'EG', 'name' => 'EGP - Egyptian Pound'],
                'AED' => ['country' => 'AE', 'name' => 'AED - UAE Dirham'],
                'SAR' => ['country' => 'SA', 'name' => 'SAR - Saudi Riyal'],
                'KWD' => ['country' => 'KW', 'name' => 'KWD - Kuwaiti Dinar'],
                'QAR' => ['country' => 'QA', 'name' => 'QAR - Qatari Riyal'],
                'BHD' => ['country' => 'BH', 'name' => 'BHD - Bahraini Dinar'],
                'OMR' => ['country' => 'OM', 'name' => 'OMR - Omani Rial'],
                'JOD' => ['country' => 'JO', 'name' => 'JOD - Jordanian Dinar'],
                'LBP' => ['country' => 'LB', 'name' => 'LBP - Lebanese Pound'],
                'IQD' => ['country' => 'IQ', 'name' => 'IQD - Iraqi Dinar'],
                'LYD' => ['country' => 'LY', 'name' => 'LYD - Libyan Dinar'],
                'TND' => ['country' => 'TN', 'name' => 'TND - Tunisian Dinar'],
                'DZD' => ['country' => 'DZ', 'name' => 'DZD - Algerian Dinar'],
                'MAD' => ['country' => 'MA', 'name' => 'MAD - Moroccan Dirham'],
                'SDG' => ['country' => 'SD', 'name' => 'SDG - Sudanese Pound'],
            ] as $code => $currency)
                <button type="button" onclick="changeCurrencyManual('{{ $code }}')" @click="open = false"
                        class="w-full text-left px-4 py-2 text-xs hover:bg-gray-100 {{ $currentCurrency === $code ? 'bg-gray-50 text-gray-900' : 'text-gray-700' }}">
                    <div class="flex justify-between items-center">
                        <span class="flex items-center space-x-3">
                            <span class="inline-flex items-center">
                                <span class="overflow-hidden w-6 h-4 rounded shadow-sm">{!! country_flag($currency['country']) !!}</span>
                            </span>
                            <span>{{ $currency['name'] }}</span>
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
                        class="px-4 py-2 w-full text-sm text-left text-blue-600 hover:bg-blue-50">
                    <div class="flex items-center">
                        <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                // Reload the page to reflect currency changes
                setTimeout(() => {
                    window.location.reload();
                }, 300); // Small delay to show notification
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

        // CSRF
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!token) {
            console.error('❌ CSRF token not found');
            return;
        }

        fetch('/currency/reset', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('✅ Reset response:', data);
            if (data.success) {
                // Show notification if available
                if (window.showNotification) {
                    window.showNotification('Currency reset to detected location', 'success');
                }

                // Reload the page to reflect currency changes
                setTimeout(() => {
                    window.location.reload();
                }, 300); // Small delay to show notification
            } else {
                console.error('❌ Currency reset failed:', data.message);
            }
        })
        .catch(error => {
            console.error('❌ Reset error:', error);
        });
    };
});
</script>
