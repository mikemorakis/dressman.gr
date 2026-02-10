<x-layouts.app>
    <x-slot:seo>
        <x-seo-meta
            title="My Wishlist — Dressman"
            description="View your saved wishlist items."
            :noindex="true"
        />
    </x-slot:seo>

    <x-slot:breadcrumb>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Wishlist'],
        ]" />
    </x-slot:breadcrumb>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{
        async removeItem(itemId, el) {
            const res = await fetch(`/wishlist/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
            });
            if (res.ok) {
                const data = await res.json();
                window.dispatchEvent(new CustomEvent('wishlist-count-updated', { detail: { count: data.count } }));
                el.closest('[data-wishlist-item]').remove();
                if (data.count === 0) location.reload();
            }
        },
        async moveToCart(itemId, el) {
            const res = await fetch(`/wishlist/${itemId}/move-to-cart`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
            });
            if (res.ok) {
                const data = await res.json();
                window.dispatchEvent(new CustomEvent('wishlist-count-updated', { detail: { count: data.wishlist_count } }));
                window.dispatchEvent(new CustomEvent('cart-count-updated', { detail: { count: data.cart_count } }));
                window.dispatchEvent(new CustomEvent('cart-open', { detail: { html: data.cart_drawer_html } }));
                el.closest('[data-wishlist-item]').remove();
                if (data.wishlist_count === 0) location.reload();
            }
        }
    }">
        <h1 class="text-2xl font-bold text-gray-900">My Wishlist</h1>

        @if($items->isEmpty())
            <div class="text-center py-16">
                <svg class="mx-auto h-16 w-16 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
                <h2 class="mt-4 text-lg font-semibold text-gray-900">Your wishlist is empty</h2>
                <p class="mt-2 text-sm text-gray-500">Save products you love and come back to them later.</p>
                <a href="{{ url('/') }}" class="mt-6 inline-block btn-primary">
                    Continue Shopping
                </a>
            </div>
        @else
            <ul role="list" class="mt-6 divide-y divide-gray-200 border-t border-b border-gray-200">
                @foreach($items as $item)
                    <li class="py-6 flex gap-4 sm:gap-6" data-wishlist-item>
                        {{-- Image --}}
                        <div class="h-24 w-24 sm:h-32 sm:w-32 flex-shrink-0 rounded-md overflow-hidden bg-gray-100">
                            @php $image = $item->product->images->first(); @endphp
                            @if($image)
                                <img src="{{ asset('storage/' . $image->path_medium) }}" alt="{{ $image->alt_text }}" class="h-full w-full object-cover" loading="lazy">
                            @endif
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 min-w-0 flex flex-col">
                            <div class="flex justify-between">
                                <div>
                                    <h3 class="text-base font-medium text-gray-900">
                                        <a href="{{ route('product.show', $item->product->slug) }}" class="hover:text-primary-600">{{ $item->product->name }}</a>
                                    </h3>
                                    @if($item->variant)
                                        <p class="mt-0.5 text-sm text-gray-500">{{ $item->variant->sku }}</p>
                                    @endif
                                </div>
                                <p class="text-base font-medium text-gray-900 flex-shrink-0 ml-4">
                                    {{ format_price($item->variant ? $item->variant->effective_price : $item->product->price) }}
                                </p>
                            </div>

                            @if(! $item->product->is_in_stock)
                                <p class="mt-1 text-xs text-red-600 font-medium">Out of stock</p>
                            @endif

                            <div class="mt-auto pt-4 flex items-center gap-4">
                                <button
                                    type="button"
                                    @click="moveToCart({{ $item->id }}, $el)"
                                    class="text-sm font-medium text-primary-600 hover:text-primary-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-sm"
                                >
                                    Move to Cart
                                </button>
                                <button
                                    type="button"
                                    @click="removeItem({{ $item->id }}, $el)"
                                    class="text-sm text-red-600 hover:text-red-800 font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 rounded-sm"
                                    aria-label="Remove {{ $item->product->name }} from wishlist"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="mt-4">
                <a href="{{ url('/') }}" class="text-sm font-medium text-primary-600 hover:text-primary-800">
                    Continue Shopping
                </a>
            </div>
        @endif
    </div>
</x-layouts.app>
