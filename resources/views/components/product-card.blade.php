@props([
    'product',
    'inWishlist' => null,
])

@php
    /** @var \App\Models\Product $product */
    $image = $product->images->first();
    $url = route('product.show', $product->slug);
    if ($inWishlist === null) {
        $inWishlist = app(\App\Services\WishlistService::class)->hasProduct($product->id);
    }
@endphp

<article
    class="group relative flex flex-col bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow"
    x-data="{
        inWishlist: {{ $inWishlist ? 'true' : 'false' }},
        toggling: false,
        async toggleWishlist() {
            this.toggling = true;
            try {
                const res = await fetch('/wishlist/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ product_id: {{ $product->id }} }),
                });
                if (res.ok) {
                    const data = await res.json();
                    this.inWishlist = data.in_wishlist;
                    window.dispatchEvent(new CustomEvent('wishlist-count-updated', { detail: { count: data.count } }));
                }
            } finally {
                this.toggling = false;
            }
        }
    }"
>
    {{-- Image --}}
    <a href="{{ $url }}" class="block overflow-hidden bg-gray-100" aria-label="{{ $product->name }}">
        @if($image)
            <img
                src="{{ asset('storage/' . $image->path_large) }}"
                alt="{{ $image->alt_text }}"
                width="{{ $image->width ?: 1200 }}"
                height="{{ $image->height ?: 1200 }}"
                loading="lazy"
                decoding="async"
                class="w-full h-auto"
            >
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-400">
                <svg class="h-12 w-12" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z" />
                </svg>
            </div>
        @endif
    </a>

    {{-- Wishlist heart button --}}
    <button
        type="button"
        @click.prevent.stop="toggleWishlist()"
        :disabled="toggling"
        class="absolute top-2 right-2 z-10 p-1.5 rounded-full transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
        :aria-label="inWishlist ? 'Remove from wishlist' : 'Add to wishlist'"
    >
        {{-- Filled heart --}}
        <svg x-show="inWishlist" x-cloak class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
        </svg>
        {{-- Outline heart --}}
        <svg x-show="!inWishlist" class="h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
        </svg>
    </button>

    {{-- Labels --}}
    @if($product->labels->isNotEmpty())
        <div class="absolute top-2 left-2 flex flex-wrap gap-1">
            @foreach($product->labels as $label)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium text-white"
                      style="background-color: {{ $label->color }}">
                    {{ $label->name }}
                </span>
            @endforeach
        </div>
    @endif

    {{-- Content --}}
    <div class="flex flex-col flex-1 p-4">
        <h3 class="text-base font-bold text-gray-900 line-clamp-2">
            <a href="{{ $url }}" class="after:absolute after:inset-0">
                {{ $product->name }}
            </a>
        </h3>

        <div class="mt-auto pt-3 flex items-baseline gap-2">
            <span class="text-lg font-bold text-gray-900 font-price">{{ format_price($product->price) }}</span>
            @if($product->is_on_sale)
                <span class="text-sm text-gray-500 line-through font-price">{{ format_price($product->compare_price) }}</span>
            @endif
        </div>

        @if(! $product->is_in_stock)
            <p class="mt-1 text-xs text-red-600 font-medium">Out of stock</p>
        @endif
    </div>
</article>
