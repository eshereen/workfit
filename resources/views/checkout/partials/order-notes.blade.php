<div class="p-6 bg-white rounded-lg shadow-md">
    <h2 class="mb-6 text-xl font-semibold text-gray-900">Additional Information</h2>

    <div class="mb-4">
        <label for="notes" class="block mb-1 text-sm font-medium text-gray-700">Order Notes (Optional)</label>
        <textarea id="notes"
                  name="notes"
                  rows="4"
                  placeholder="Add any special instructions or notes for your order..."
                  class="px-4 py-2 w-full rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">{{ old('notes') }}</textarea>
        @error('notes')
            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
        <div class="flex items-start">
            <svg class="mt-0.5 mr-2 w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                
            </div>
        </div>
    </div>
</div>
