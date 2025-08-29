<div>
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600 mt-2">Welcome back! Here's your account overview.</p>
        </div>


        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <livewire:loyalty-points />

            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Orders</h3>
                <div class="space-y-3">
                    @forelse($recentOrders as $order)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">#{{ $order->order_number }}</p>
                                <p class="text-sm text-gray-600">{{ $order->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-gray-900">
                                    {{ $order->currency }} {{ number_format($order->total_amount, 2) }}
                                </p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' :
                                       ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No orders yet</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('products.index') }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                        Browse Products
                    </a>
                    <a href="{{ route('cart.index') }}" class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                        View Cart
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="block w-full text-center bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                        Wishlist
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
