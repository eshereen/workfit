<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-6">Billing Address</h2>

    <div class="mb-4">
        <label class="flex items-center">
            <input type="checkbox"
                   id="use_billing_for_shipping"
                   name="use_billing_for_shipping"
                   value="1"
                   {{ old('use_billing_for_shipping') ? 'checked' : '' }}
                   class="mr-2 text-red-600 focus:ring-red-500 border-gray-300 rounded">
            <span class="text-sm text-gray-700">Use billing address for shipping</span>
        </label>
    </div>

    @if(!Auth::check())
        <!-- Guest checkout - use main form fields -->
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
    @else
        <!-- Authenticated user - use billing address fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="billing_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                <input type="text"
                       id="billing_name"
                       name="billing_address[name]"
                       value="{{ old('billing_address.name') }}"
                       required
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                @error('billing_address.name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="billing_email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email"
                       id="billing_email"
                       name="billing_address[email]"
                       value="{{ old('billing_address.email') }}"
                       required
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                @error('billing_address.email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mb-4">
            <label for="billing_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
            <input type="tel"
                   id="billing_phone"
                   name="billing_phone"
                   value="{{ old('billing_address.phone') }}"
                   required
                   class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
            @error('billing_address.phone')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    @endif

    <div class="mb-4">
        <label for="billing_address" class="block text-sm font-medium text-gray-700 mb-1">Street Address *</label>
        <input type="text"
               id="billing_address"
               name="billing_address[address]"
               value="{{ old('billing_address.address') }}"
               required
               class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
        @error('billing_address.address')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2">
            <label for="billing_city" class="block text-sm font-medium text-gray-700 mb-1">City *</label>
            <input type="text"
                   id="billing_city"
                   name="billing_address[city]"
                   value="{{ old('billing_address.city') }}"
                   required
                   class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
            @error('billing_address.city')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="billing_state" class="block text-sm font-medium text-gray-700 mb-1">State/Province</label>
            <input type="text"
                   id="billing_state"
                   name="billing_address[state]"
                   value="{{ old('billing_address.state') }}"
                   class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
            @error('billing_address.state')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="billing_postal_code" class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
            <input type="text"
                   id="billing_postal_code"
                   name="billing_address[postal_code]"
                   value="{{ old('billing_address.postal_code') }}"
                   class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
            @error('billing_address.postal_code')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="mb-4">
        <label for="billing_country" class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
        <select id="billing_country"
                name="billing_address[country]"
                required
                class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
            <option value="">Select Country</option>
            @foreach($countries as $country)
                <option value="{{ $country->id }}" {{ old('billing_address.country') == $country->id ? 'selected' : '' }}>
                    {{ $country->name }}
                </option>
            @endforeach
        </select>
        @error('billing_address.country')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>
