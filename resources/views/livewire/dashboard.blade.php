<div class="w-full h-full">
    <div class="p-6 lg:p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-200">Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Welcome back! Here's your account overview.</p>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            <livewire:loyalty-points />

            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Recent Orders</h3>
                <div class="space-y-3">
                    @forelse($recentOrders as $order)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-zinc-700 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-gray-100">#{{ $order->order_number }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $order->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $order->currency }} {{ number_format($order->total_amount, 2) }}
                                </p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $order->status->value === 'delivered' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                       ($order->status->value === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                                       'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200') }}">
                                    {{ ucfirst($order->status->value) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 text-center py-4">No orders yet</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-8">Quick Actions</h3>
                <div class="space-y-3 ">
                    <a href="{{ route('products.index') }}" class="block w-full text-center bg-white hover:bg-red-700 hover:text-white border-2 border-red-600 text-gray-800 font-medium py-2 px-4 rounded-lg transition duration-200">
                        Browse Products
                    </a>
                    <a href="{{ route('cart.index') }}" class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                        View Cart
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="block w-full text-center bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                        Wishlist
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
