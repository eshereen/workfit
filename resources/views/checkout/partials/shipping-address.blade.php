<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-6">Shipping Address</h2>

    @if(!Auth::check())
        <!-- Guest checkout - use main form fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="shipping_first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                <input type="text"
                       id="shipping_first_name"
                       name="shipping_address[first_name]"
                       value="{{ old('shipping_address.first_name', old('first_name')) }}"
                       required
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                @error('shipping_address.first_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="shipping_last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                <input type="text"
                       id="shipping_last_name"
                       name="shipping_address[last_name]"
                       value="{{ old('shipping_address.last_name', old('last_name')) }}"
                       required
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                @error('shipping_address.last_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mb-4">
            <label for="shipping_email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
            <input type="email"
                   id="shipping_email"
                   name="shipping_address[email]"
                   value="{{ old('shipping_address.email', old('email')) }}"
                   required
                   class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
            @error('shipping_address.email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="shipping_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
            <input type="tel"
                   id="shipping_phone"
                   name="shipping_address[phone]"
                   value="{{ old('shipping_address.phone', old('phone_number')) }}"
                   required
                   class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
            @error('shipping_address.phone')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    @else
        <!-- Authenticated user - use shipping address fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="shipping_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                <input type="text"
                       id="shipping_name"
                       name="shipping_address[name]"
                       value="{{ old('shipping_address.name') }}"
                       required
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                @error('shipping_address.name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="shipping_email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email"
                       id="shipping_email"
                       name="shipping_address[email]"
                       value="{{ old('shipping_address.email') }}"
                       required
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                @error('shipping_address.email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mb-4">
            <label for="shipping_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
            <input type="tel"
                   id="shipping_phone"
                   name="shipping_address[phone]"
                   value="{{ old('shipping_address.phone') }}"
                   required
                   class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
            @error('shipping_address.phone')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    @endif

    <div class="mb-4">
        <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-1">Street Address *</label>
        <input type="text"
               id="shipping_address"
               name="shipping_address[address]"
               value="{{ old('shipping_address.address') }}"
               required
               class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
        @error('shipping_address.address')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2">
            <label for="shipping_city" class="block text-sm font-medium text-gray-700 mb-1">City *</label>
            <input type="text"
                   id="shipping_city"
                   name="shipping_address[city]"
                   value="{{ old('shipping_address.city') }}"
                   required
                   class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
            @error('shipping_address.city')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="shipping_state" class="block text-sm font-medium text-gray-700 mb-1">State/Province</label>
            <input type="text"
                   id="shipping_state"
                   name="shipping_address[state]"
                   value="{{ old('shipping_address.state') }}"
                   class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
            @error('shipping_address.state')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="shipping_postal_code" class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
            <input type="text"
                   id="shipping_postal_code"
                   name="shipping_address[postal_code]"
                   value="{{ old('shipping_address.postal_code') }}"
                   class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
            @error('shipping_address.postal_code')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="mb-4">
        <label for="shipping_country" class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
        <select id="shipping_country"
                name="shipping_address[country]"
                required
                class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-red-500 focus:border-transparent">
            <option value="">Select Country</option>
            @foreach($countries as $country)
                <option value="{{ $country->id }}" {{ old('shipping_address.country') == $country->id ? 'selected' : '' }}>
                    {{ $country->name }}
                </option>
            @endforeach
        </select>
        @error('shipping_address.country')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>
