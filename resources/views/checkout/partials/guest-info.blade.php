<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-6">Guest Information</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
            <input type="text" 
                   id="first_name" 
                   name="first_name" 
                   value="{{ old('first_name') }}" 
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
                   value="{{ old('last_name') }}" 
                   required 
                   class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
            @error('last_name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
    
    <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
        <input type="email" 
               id="email" 
               name="email" 
               value="{{ old('email') }}" 
               required 
               class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
        @error('email')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
    
    <div class="mb-4">
        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
        <input type="tel" 
               id="phone_number" 
               name="phone_number" 
               value="{{ old('phone_number') }}" 
               required 
               class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
        @error('phone_number')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
