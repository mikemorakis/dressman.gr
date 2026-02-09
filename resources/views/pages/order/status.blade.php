<x-layouts.app>
    <x-slot:seo>
        <x-seo-meta
            title="Order {{ $order->order_number }} — PeShop"
            description="Track your order status."
            :noindex="true"
        />
    </x-slot:seo>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        {{-- Order Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Order {{ $order->order_number }}</h1>
                <p class="mt-1 text-sm text-gray-500">Placed on {{ $order->created_at->format('M j, Y') }}</p>
            </div>

            @php
                $badgeColors = match($order->status) {
                    \App\Enums\OrderStatus::Pending => 'bg-yellow-100 text-yellow-800',
                    \App\Enums\OrderStatus::Paid => 'bg-blue-100 text-blue-800',
                    \App\Enums\OrderStatus::Processing => 'bg-indigo-100 text-indigo-800',
                    \App\Enums\OrderStatus::Shipped => 'bg-purple-100 text-purple-800',
                    \App\Enums\OrderStatus::Delivered => 'bg-green-100 text-green-800',
                    \App\Enums\OrderStatus::Cancelled => 'bg-red-100 text-red-800',
                };
            @endphp

            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $badgeColors }}">
                {{ $order->status->label() }}
            </span>
        </div>

        {{-- Status Timeline --}}
        @if($order->statusHistory->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Status Timeline</h2>

                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @foreach($order->statusHistory as $entry)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute left-3 top-3 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif

                                    <div class="relative flex items-start gap-x-3">
                                        <div class="relative">
                                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-900 ring-4 ring-white">
                                                <div class="h-1.5 w-1.5 rounded-full bg-white"></div>
                                            </div>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ ucfirst($entry->to_status) }}
                                            </p>

                                            @if($entry->notes)
                                                <p class="mt-0.5 text-sm text-gray-500">{{ $entry->notes }}</p>
                                            @endif

                                            <p class="mt-0.5 text-xs text-gray-400">
                                                {{ $entry->created_at->format('M j, Y \a\t H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Items --}}
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Items</h2>

            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $item->product_name }}
                                    @if($item->variant_label)
                                        <span class="block text-xs text-gray-500">{{ $item->variant_label }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-center">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ format_price($item->line_total) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Totals --}}
        <div class="mb-8 bg-gray-50 rounded-lg p-6">
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-600">Subtotal</dt>
                    <dd class="font-medium text-gray-900">{{ format_price($order->subtotal) }}</dd>
                </div>

                @if($order->prices_include_vat)
                    <div class="flex justify-between">
                        <dt class="text-gray-400">Includes VAT ({{ number_format((float) $order->vat_rate, 0) }}%)</dt>
                        <dd class="text-gray-400">{{ format_price($order->vat_amount) }}</dd>
                    </div>
                @else
                    <div class="flex justify-between">
                        <dt class="text-gray-600">VAT ({{ number_format((float) $order->vat_rate, 0) }}%)</dt>
                        <dd class="font-medium text-gray-900">{{ format_price($order->vat_amount) }}</dd>
                    </div>
                @endif

                <div class="flex justify-between">
                    <dt class="text-gray-600">Shipping</dt>
                    <dd class="font-medium text-gray-900">
                        {{ (float) $order->shipping_amount > 0 ? format_price($order->shipping_amount) : 'Free' }}
                    </dd>
                </div>

                <div class="flex justify-between border-t border-gray-200 pt-2">
                    <dt class="text-base font-semibold text-gray-900">Total</dt>
                    <dd class="text-base font-semibold text-gray-900">{{ format_price($order->total) }}</dd>
                </div>
            </dl>
        </div>

        {{-- Shipping Address --}}
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Shipping Address</h2>
            <address class="not-italic text-sm text-gray-600 leading-relaxed">
                {{ $order->shipping_address['first_name'] }} {{ $order->shipping_address['last_name'] }}<br>
                {{ $order->shipping_address['address'] }}<br>
                @if(!empty($order->shipping_address['address2']))
                    {{ $order->shipping_address['address2'] }}<br>
                @endif
                {{ $order->shipping_address['postal_code'] }} {{ $order->shipping_address['city'] }}<br>
                {{ $order->shipping_address['country'] }}
            </address>
        </div>

        {{-- Contact --}}
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Contact</h2>
            @php
                $email = $order->email;
                $parts = explode('@', $email);
                $local = $parts[0];
                $masked = substr($local, 0, 1) . str_repeat('*', max(strlen($local) - 1, 2)) . '@' . $parts[1];
            @endphp
            <p class="text-sm text-gray-600">{{ $masked }}</p>
        </div>

        {{-- Back link --}}
        <div class="text-center">
            <a href="{{ route('home') }}" class="text-sm font-medium text-primary-600 hover:text-primary-800">
                Continue Shopping
            </a>
        </div>
    </div>
</x-layouts.app>
