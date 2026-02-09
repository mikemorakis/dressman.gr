@props([
    'product',
])

@php
    /** @var \App\Models\Product $product */
    $image = $product->images->first();
    $url = route('product.show', $product->slug);
@endphp

<article class="group relative flex flex-col bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
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
        <h3 class="text-sm font-medium text-gray-900 line-clamp-2">
            <a href="{{ $url }}" class="after:absolute after:inset-0">
                {{ $product->name }}
            </a>
        </h3>

        <div class="mt-auto pt-3 flex items-baseline gap-2">
            <span class="text-lg font-bold text-gray-900">{{ format_price($product->price) }}</span>
            @if($product->is_on_sale)
                <span class="text-sm text-gray-500 line-through">{{ format_price($product->compare_price) }}</span>
            @endif
        </div>

        @if(! $product->is_in_stock)
            <p class="mt-1 text-xs text-red-600 font-medium">Out of stock</p>
        @endif
    </div>
</article>
