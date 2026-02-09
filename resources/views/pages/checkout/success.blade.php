<x-layouts.app>
    <x-slot:seo>
        <x-seo-meta
            title="Order Confirmed — PeShop"
            description="Your order has been confirmed."
            :noindex="true"
        />
    </x-slot:seo>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center">
            <svg class="mx-auto h-16 w-16 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>

            <h1 class="mt-4 text-3xl font-bold text-gray-900">Order Confirmed!</h1>

            <p class="mt-2 text-lg text-gray-600">
                Thank you for your purchase.
            </p>

            <div class="mt-8 bg-gray-50 rounded-lg p-6 text-left">
                <h2 class="text-lg font-semibold text-gray-900">Order Details</h2>

                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Order Number</dt>
                        <dd class="font-medium text-gray-900">{{ $order->order_number }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Total</dt>
                        <dd class="font-medium text-gray-900">{{ format_price($order->total) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Email</dt>
                        <dd class="font-medium text-gray-900">{{ $order->email }}</dd>
                    </div>
                </dl>

                <p class="mt-6 text-sm text-gray-600">
                    A confirmation email will be sent to <strong>{{ $order->email }}</strong> with your order details.
                </p>
            </div>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('order.guest.show', ['orderNumber' => $order->order_number, 'token' => $order->guest_token]) }}" class="btn-primary">
                    Track Your Order
                </a>
                <a href="{{ route('home') }}" class="text-sm font-medium text-primary-600 hover:text-primary-800">
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
