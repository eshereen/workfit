<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-6">Customer Information</h2>
    
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-blue-800 text-sm">
                Welcome back, <strong>{{ $user->name ?? $user->email }}</strong>! Your information will be pre-filled from your account.
            </p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
            <input type="text" 
                   id="first_name" 
                   name="first_name" 
                   value="{{ old('first_name', $user->first_name ?? '') }}" 
                   required 
                   class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
            @error('first_name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
            <input type="text" 
                   id="last_name" 
                   name="last_name" 
                   value="{{ old('last_name', $user->last_name ?? '') }}" 
                   required 
                   class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
            @error('last_name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
    
    <div class="mb-4">
        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
        <input type="tel" 
               id="phone_number" 
               name="phone_number" 
               value="{{ old('phone_number', $user->phone ?? '') }}" 
               class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
        @error('phone_number')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label for="country_id" class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
            <select id="country_id" 
                    name="country_id" 
                    required 
                    class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                <option value="">Select Country</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>
            @error('country_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State/Province</label>
            <input type="text" 
                   id="state" 
                   name="state" 
                   value="{{ old('state') }}" 
                   class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
            @error('state')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
            <input type="text" 
                   id="city" 
                   name="city" 
                   value="{{ old('city') }}" 
                   class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
            @error('city')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
