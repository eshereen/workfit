<div>
    @if($showLoyaltySection)
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-semibold text-blue-900">Loyalty Points</h3>
            <div class="text-sm text-blue-700">
                Available: <span class="font-bold">{{ number_format($availablePoints) }} pts</span>
            </div>
        </div>

        <div class="text-sm text-blue-700 mb-4">
            <p>💡 Redeem 100 points for $1 off your order</p>
        </div>

        @if($errorMessage)
            <div class="mb-3 p-3 bg-red-100 border border-red-200 rounded-lg">
                <p class="text-sm text-red-700">{{ $errorMessage }}</p>
            </div>
        @endif

        @if($successMessage)
            <div class="mb-3 p-3 bg-green-100 border border-green-200 rounded-lg">
                <p class="text-sm text-green-700">{{ $successMessage }}</p>
            </div>
        @endif

        <div class="space-y-3">
            <div>
                <label for="loyaltyPoints" class="block text-sm font-medium text-blue-900 mb-1">
                    Points to Redeem
                </label>
                <input
                    type="number"
                    id="loyaltyPoints"
                    wire:model.live="pointsToRedeem"
                    min="100"
                    step="100"
                    max="{{ $availablePoints }}"
                    class="w-full px-3 py-2 border border-blue-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter points (min: 100)"
                >
                @error('pointsToRedeem')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @if($redemptionValue > 0)
                <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-700">
                        <strong>Discount Value:</strong> ${{ number_format($redemptionValue, 2) }}
                    </p>
                </div>
            @endif

            <div class="flex space-x-2">
                @if($pointsToRedeem > 0)
                    <button
                        wire:click="applyLoyaltyPoints"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200"
                    >
                        Apply Points
                    </button>
                    <button
                        wire:click="removeLoyaltyPoints"
                        class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition duration-200"
                    >
                        Remove
                    </button>
                @else
                    <button
                        wire:click="applyLoyaltyPoints"
                        disabled
                        class="flex-1 bg-gray-400 text-white font-medium py-2 px-4 rounded-lg cursor-not-allowed"
                    >
                        Apply Points
                    </button>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
