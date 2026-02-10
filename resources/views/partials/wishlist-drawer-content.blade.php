{{-- Wishlist drawer inner content — rendered as HTML fragment via GET /wishlist/drawer --}}

{{-- Header --}}
<div class="flex items-center justify-between px-4 h-16 border-b border-gray-200 flex-shrink-0">
    <h2 class="text-lg font-semibold text-gray-900">Your Wishlist</h2>
    <button
        type="button"
        data-close-drawer
        class="p-2 -mr-2 rounded-md text-gray-400 hover:text-gray-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
        aria-label="Close wishlist"
    >
        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
        </svg>
    </button>
</div>

{{-- Items --}}
<div class="flex-1 overflow-y-auto px-4 py-4">
    @if($items->isEmpty())
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
            </svg>
            <p class="mt-4 text-sm text-gray-500">Your wishlist is empty</p>
            <a href="{{ url('/') }}" data-close-drawer class="mt-4 inline-block text-sm font-medium text-primary-600 hover:text-primary-800">
                Continue shopping
            </a>
        </div>
    @else
        <ul role="list" class="divide-y divide-gray-200">
            @foreach($items as $item)
                <li class="py-4 flex gap-4">
                    {{-- Image --}}
                    <div class="h-20 w-20 flex-shrink-0 rounded-md overflow-hidden bg-gray-100">
                        @php $image = $item->product->images->first(); @endphp
                        @if($image)
                            <img src="{{ asset('storage/' . $image->path_thumb) }}" alt="{{ $image->alt_text }}" class="h-full w-full object-cover" loading="lazy">
                        @endif
                    </div>

                    {{-- Details --}}
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-medium text-gray-900 truncate">
                            <a href="{{ route('product.show', $item->product->slug) }}" data-close-drawer>{{ $item->product->name }}</a>
                        </h3>
                        @if($item->variant)
                            <p class="mt-0.5 text-xs text-gray-500">{{ $item->variant->sku }}</p>
                        @endif
                        <p class="mt-1 text-sm font-medium text-gray-900">
                            {{ format_price($item->variant ? $item->variant->effective_price : $item->product->price) }}
                        </p>

                        <div class="mt-2 flex items-center gap-3">
                            <button
                                data-move-to-cart="{{ $item->id }}"
                                type="button"
                                class="text-sm text-primary-600 hover:text-primary-800 font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-sm"
                            >
                                Move to Cart
                            </button>
                            <button
                                data-remove="{{ $item->id }}"
                                type="button"
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
    @endif
</div>

{{-- Footer --}}
@if($items->isNotEmpty())
    <div class="border-t border-gray-200 px-4 py-4 flex-shrink-0">
        <a href="{{ route('wishlist') }}" data-close-drawer class="btn-primary w-full text-center block">
            View Wishlist
        </a>
    </div>
@endif
