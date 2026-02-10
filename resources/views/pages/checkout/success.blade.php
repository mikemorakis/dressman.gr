<x-layouts.app>
    <x-slot:seo>
        <x-seo-meta
            title="Order Confirmed — Dressman"
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
                Thank you for your order.
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
                        <dt class="text-gray-600">Payment Method</dt>
                        <dd class="font-medium text-gray-900">
                            @if($order->payment_method === 'stripe')
                                Credit / Debit Card
                            @elseif($order->payment_method === 'bank_transfer')
                                Bank Transfer
                            @elseif($order->payment_method === 'store_pickup')
                                Store Pickup
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Email</dt>
                        <dd class="font-medium text-gray-900">{{ $order->email }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Bank Transfer Instructions --}}
            @if($order->payment_method === 'bank_transfer')
                <div class="mt-6 bg-amber-50 border border-amber-200 rounded-lg p-6 text-left">
                    <h2 class="text-lg font-semibold text-gray-900">Bank Transfer Details</h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Please transfer <strong>{{ format_price($order->total) }}</strong> to the following bank account.
                        Use your order number <strong>{{ $order->order_number }}</strong> as the payment reference.
                    </p>

                    <dl class="mt-4 space-y-2 text-sm">
                        <div>
                            <dt class="text-gray-500">Bank</dt>
                            <dd class="font-medium text-gray-900">Eurobank</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Account Holder</dt>
                            <dd class="font-medium text-gray-900">DRESSMAN</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">IBAN</dt>
                            <dd class="font-medium text-gray-900 font-mono">GR00 0000 0000 0000 0000 0000 000</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">BIC / SWIFT</dt>
                            <dd class="font-medium text-gray-900 font-mono">XXXXXXXX</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Payment Reference</dt>
                            <dd class="font-medium text-gray-900">{{ $order->order_number }}</dd>
                        </div>
                    </dl>

                    <p class="mt-4 text-xs text-amber-700">
                        Your order will be processed once the payment is received. Please complete the transfer within 3 business days.
                    </p>
                </div>
            @endif

            {{-- Store Pickup Instructions --}}
            @if($order->payment_method === 'store_pickup')
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6 text-left">
                    <h2 class="text-lg font-semibold text-gray-900">Store Pickup Information</h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Your order is being prepared. You can pick it up and pay at one of our stores:
                    </p>

                    <div class="mt-4 space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Boutique — Kolonaki</p>
                            <p class="text-sm text-gray-600">Skoufa 10, 10673 Kolonaki, Athens</p>
                            <p class="text-sm text-gray-600">Tel: <a href="tel:+302155004038" class="text-primary-600 hover:text-primary-800">+30 215 500 4038</a></p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Boutique & Stockhouse — Ano Liosia</p>
                            <p class="text-sm text-gray-600">Leoforos Filis 116, 13341 Ano Liosia, Athens</p>
                            <p class="text-sm text-gray-600">Tel: <a href="tel:+302102483370" class="text-primary-600 hover:text-primary-800">+30 210 2483 370</a></p>
                        </div>
                    </div>

                    <p class="mt-4 text-xs text-blue-700">
                        We will contact you when your order is ready for pickup. Please bring your order number: <strong>{{ $order->order_number }}</strong>
                    </p>
                </div>
            @endif

            <div class="mt-6 text-left">
                <p class="text-sm text-gray-600">
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
