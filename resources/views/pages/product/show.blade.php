@php
    /** @var \App\Models\Product $product */
    /** @var array $variantData */
    /** @var array $jsonLd */

    $hasVariants = $product->has_variants && count($variantData) > 0;
@endphp

<x-layouts.app>
    <x-slot:seo>
        <x-seo-meta
            :title="$product->meta_title ?: $product->name . ' — PeShop'"
            :description="$product->meta_description ?: $product->short_description"
            :jsonLd="$jsonLd"
        />
    </x-slot:seo>

    <x-slot:breadcrumb>
        <x-breadcrumb :items="$breadcrumbs" />
    </x-slot:breadcrumb>

    <div
        x-data="{
            variants: {{ Js::from($variantData) }},
            selected: {},
            stickyVisible: false,
            adding: false,
            added: false,
            inWishlist: {{ app(\App\Services\WishlistService::class)->hasProduct($product->id) ? 'true' : 'false' }},
            togglingWishlist: false,

            async toggleWishlist(productId, variantId = null) {
                this.togglingWishlist = true;
                try {
                    const res = await fetch('{{ route("wishlist.toggle") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({ product_id: productId, variant_id: variantId }),
                    });
                    if (res.ok) {
                        const data = await res.json();
                        this.inWishlist = data.in_wishlist;
                        window.dispatchEvent(new CustomEvent('wishlist-count-updated', { detail: { count: data.count } }));
                    }
                } finally {
                    this.togglingWishlist = false;
                }
            },

            async addToCart(productId, variantId = null) {
                this.adding = true;
                this.added = false;
                try {
                    const res = await fetch('{{ route("cart.add") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({ product_id: productId, variant_id: variantId, quantity: 1 }),
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    this.added = true;
                    window.dispatchEvent(new CustomEvent('cart-count-updated', { detail: { count: data.count } }));
                    window.dispatchEvent(new CustomEvent('cart-open', { detail: { html: data.drawer_html } }));
                    setTimeout(() => { this.added = false; }, 2000);
                } finally {
                    this.adding = false;
                }
            },

            init() {
                const sentinel = this.$refs.addToCart;
                if (sentinel && 'IntersectionObserver' in window) {
                    new IntersectionObserver(([e]) => {
                        this.stickyVisible = !e.isIntersecting;
                    }).observe(sentinel);
                } else if (sentinel) {
                    this.stickyVisible = true;
                }
            },

            get attributeNames() {
                const names = new Set();
                this.variants.forEach(v => Object.keys(v.attributes).forEach(k => names.add(k)));
                return [...names];
            },

            attributeValues(name) {
                const seen = new Set();
                return this.variants.reduce((acc, v) => {
                    const val = v.attributes[name];
                    if (val && !seen.has(val)) { seen.add(val); acc.push(val); }
                    return acc;
                }, []);
            },

            get currentVariant() {
                const names = this.attributeNames;
                if (Object.keys(this.selected).length < names.length) return null;
                return this.variants.find(v => names.every(n => v.attributes[n] === this.selected[n])) || null;
            },

            isAvailable(name, value) {
                const test = { ...this.selected, [name]: value };
                return this.variants.some(v =>
                    Object.entries(test).every(([k, val]) => v.attributes[k] === val)
                );
            }
        }"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="lg:grid lg:grid-cols-2 lg:gap-x-12 lg:items-start">
                {{-- Images — vertical stack, full resolution --}}
                <div>
                    @if($product->images->isNotEmpty())
                        <div class="space-y-4">
                            @foreach($product->images as $index => $image)
                                <div class="bg-gray-100">
                                    <img
                                        src="{{ asset('storage/' . $image->path_large) }}"
                                        alt="{{ $image->alt_text ?: $product->name }}"
                                        width="{{ $image->width ?: 1200 }}"
                                        height="{{ $image->height ?: 1200 }}"
                                        @if($index === 0) fetchpriority="high" @else loading="lazy" @endif
                                        decoding="async"
                                        class="w-full h-auto"
                                    >
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="aspect-square bg-gray-100 flex items-center justify-center">
                            <svg class="h-16 w-16 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z" />
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Product Info — sticky on desktop --}}
                <div class="mt-8 lg:mt-0 lg:sticky lg:top-[150px] lg:self-start">
                    {{-- Labels --}}
                    @if($product->labels->isNotEmpty())
                        <div class="flex flex-wrap gap-2 mb-3">
                            @foreach($product->labels as $label)
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium text-white"
                                      style="background-color: {{ $label->color }}">
                                    {{ $label->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $product->name }}</h1>

                    {{-- Brand --}}
                    @if($product->brand)
                        <p class="mt-1 text-sm text-gray-500">{{ $product->brand->name }}</p>
                    @endif

                    {{-- Price --}}
                    <div class="mt-4 flex items-baseline gap-3">
                        @if($hasVariants)
                            <span class="text-2xl font-bold text-gray-900 font-price"
                                  x-text="currentVariant ? currentVariant.price_formatted : '{{ format_price($product->price) }}'">
                                {{ format_price($product->price) }}
                            </span>
                        @else
                            <span class="text-2xl font-bold text-gray-900 font-price">{{ format_price($product->price) }}</span>
                            @if($product->is_on_sale)
                                <span class="text-base text-gray-500 line-through font-price">{{ format_price($product->compare_price) }}</span>
                            @endif
                        @endif
                    </div>

                    {{-- Short description --}}
                    @if($product->short_description)
                        <div class="mt-4 text-gray-600 prose prose-sm max-w-none">{!! clean_html($product->short_description) !!}</div>
                    @endif

                    {{-- Variant Selector --}}
                    @if($hasVariants)
                        <div class="mt-6 space-y-5">
                            <template x-for="name in attributeNames" :key="name">
                                <fieldset>
                                    <legend class="text-sm font-medium text-gray-900 uppercase" x-text="name"></legend>

                                    {{-- Dropdown for attributes with many values (>4) --}}
                                    <template x-if="attributeValues(name).length > 4">
                                        <select
                                            x-model="selected[name]"
                                            class="mt-2 block w-full border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2.5"
                                        >
                                            <option value="" x-text="'Select ' + name.toLowerCase()"></option>
                                            <template x-for="value in attributeValues(name)" :key="value">
                                                <option :value="value" :disabled="!isAvailable(name, value)" x-text="value"></option>
                                            </template>
                                        </select>
                                    </template>

                                    {{-- Buttons for attributes with few values (<=4) --}}
                                    <template x-if="attributeValues(name).length <= 4">
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <template x-for="value in attributeValues(name)" :key="value">
                                                <button
                                                    @click="selected[name] = value"
                                                    :class="{
                                                        'ring-2 ring-primary-500 bg-primary-50 text-primary-700': selected[name] === value,
                                                        'ring-1 ring-gray-300 text-gray-700 hover:ring-gray-400': selected[name] !== value,
                                                        'opacity-40 cursor-not-allowed': !isAvailable(name, value)
                                                    }"
                                                    :disabled="!isAvailable(name, value)"
                                                    :aria-pressed="(selected[name] === value).toString()"
                                                    class="min-w-[2.75rem] min-h-[2.75rem] px-4 py-2 text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                                                    type="button"
                                                    x-text="value"
                                                ></button>
                                            </template>
                                        </div>
                                    </template>
                                </fieldset>
                            </template>
                        </div>

                        {{-- Variant stock status --}}
                        <div class="mt-4">
                            <template x-if="currentVariant">
                                <p class="text-sm font-medium"
                                   :class="currentVariant.in_stock ? 'text-green-600' : 'text-red-600'"
                                   x-text="currentVariant.in_stock ? 'In stock' : 'Out of stock'">
                                </p>
                            </template>
                        </div>
                    @else
                        {{-- Simple product stock --}}
                        <div class="mt-4">
                            @if($product->is_in_stock)
                                <p class="text-sm font-medium text-green-600">In stock</p>
                            @else
                                <p class="text-sm font-medium text-red-600">Out of stock</p>
                            @endif
                        </div>
                    @endif

                    {{-- Description --}}
                    @if($product->description)
                        <div class="mt-6 prose prose-sm max-w-none text-gray-700">
                            {!! clean_html($product->description) !!}
                        </div>
                    @endif

                    {{-- Add to Cart --}}
                    <div x-ref="addToCart" class="mt-6">
                        @if($hasVariants)
                            <button
                                class="btn-primary w-full py-3 text-base"
                                :disabled="!currentVariant || !currentVariant.in_stock || adding"
                                @click="addToCart({{ $product->id }}, currentVariant?.id)"
                                type="button"
                            >
                                <span x-show="adding" class="inline-flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    Adding...
                                </span>
                                <span x-show="!adding && added">Added to cart</span>
                                <span x-show="!adding && !added" x-text="!currentVariant
                                    ? 'Select options'
                                    : (!currentVariant.in_stock ? 'Out of stock' : 'Add to Cart')">
                                    Select options
                                </span>
                            </button>
                        @else
                            <button
                                class="btn-primary w-full py-3 text-base"
                                @disabled(!$product->is_in_stock)
                                :disabled="adding"
                                @click="addToCart({{ $product->id }})"
                                type="button"
                            >
                                <span x-show="adding" class="inline-flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    Adding...
                                </span>
                                <span x-show="!adding && added">Added to cart</span>
                                <span x-show="!adding && !added">{{ $product->is_in_stock ? 'Add to Cart' : 'Out of Stock' }}</span>
                            </button>
                        @endif

                        {{-- Wishlist button --}}
                        <button
                            type="button"
                            @click="toggleWishlist({{ $product->id }}, currentVariant?.id ?? null)"
                            :disabled="togglingWishlist"
                            class="mt-3 w-full py-3 text-base border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50 transition-colors inline-flex items-center justify-center gap-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                        >
                            {{-- Filled heart --}}
                            <svg x-show="inWishlist" x-cloak class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                            </svg>
                            {{-- Outline heart --}}
                            <svg x-show="!inWishlist" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                            </svg>
                            <span x-text="inWishlist ? 'In Wishlist' : 'Add to Wishlist'">Add to Wishlist</span>
                        </button>
                    </div>

                    {{-- SKU --}}
                    <p class="mt-4 text-xs text-gray-500">
                        SKU:
                        @if($hasVariants)
                            <span x-text="currentVariant ? currentVariant.sku : '{{ e($product->sku) }}'">{{ $product->sku }}</span>
                        @else
                            {{ $product->sku }}
                        @endif
                    </p>
                </div>
            </div>

        </div>

        {{-- Recently Viewed --}}
        @if($recentlyViewed->isNotEmpty())
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <h2 class="text-xl font-bold text-gray-900">Recently Viewed</h2>
                <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                    @foreach($recentlyViewed as $recentProduct)
                        <x-product-card :product="$recentProduct" />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Similar Products --}}
        @if($similarProducts->isNotEmpty())
            <section class="bg-[#f5f5f5] {{ $recentlyViewed->isEmpty() ? 'mt-12' : '' }}">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                    <h2 class="text-xl font-bold text-gray-900">Similar Products</h2>
                    <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                        @foreach($similarProducts as $similarProduct)
                            <x-product-card :product="$similarProduct" />
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- Mobile sticky add-to-cart bar --}}
        <div
            x-show="stickyVisible"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            x-cloak
            class="fixed bottom-0 inset-x-0 z-20 lg:hidden bg-white border-t border-gray-200 shadow-[0_-2px_10px_rgba(0,0,0,0.08)]"
            style="padding-bottom: env(safe-area-inset-bottom, 0px)"
        >
            <div class="flex items-center justify-between gap-3 px-4 py-3">
                <div class="flex flex-col min-w-0">
                    <span class="text-lg font-bold text-gray-900 truncate font-price"
                          x-text="currentVariant ? currentVariant.price_formatted : '{{ format_price($product->price) }}'">
                        {{ format_price($product->price) }}
                    </span>
                    @if($hasVariants)
                        <span class="text-xs"
                              :class="currentVariant?.in_stock ? 'text-green-600' : 'text-gray-500'"
                              x-text="currentVariant ? (currentVariant.in_stock ? 'In stock' : 'Out of stock') : 'Select options'">
                            Select options
                        </span>
                    @else
                        <span class="text-xs {{ $product->is_in_stock ? 'text-green-600' : 'text-red-600' }}">
                            {{ $product->is_in_stock ? 'In stock' : 'Out of stock' }}
                        </span>
                    @endif
                </div>
                <button
                    type="button"
                    @click="toggleWishlist({{ $product->id }}, currentVariant?.id ?? null)"
                    :disabled="togglingWishlist"
                    class="flex-shrink-0 p-2 rounded-md text-gray-600 hover:text-red-500 transition-colors"
                    :aria-label="inWishlist ? 'Remove from wishlist' : 'Add to wishlist'"
                >
                    <svg x-show="inWishlist" x-cloak class="h-6 w-6 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                    </svg>
                    <svg x-show="!inWishlist" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                    </svg>
                </button>
                @if($hasVariants)
                    <button
                        class="btn-primary text-sm px-6 py-2.5 flex-shrink-0"
                        :disabled="!currentVariant || !currentVariant.in_stock || adding"
                        @click="addToCart({{ $product->id }}, currentVariant?.id)"
                        type="button"
                    >
                        <span x-show="adding">Adding...</span>
                        <span x-show="!adding && added">Added</span>
                        <span x-show="!adding && !added">Add to Cart</span>
                    </button>
                @else
                    <button
                        class="btn-primary text-sm px-6 py-2.5 flex-shrink-0"
                        @disabled(!$product->is_in_stock)
                        :disabled="adding"
                        @click="addToCart({{ $product->id }})"
                        type="button"
                    >
                        <span x-show="adding">Adding...</span>
                        <span x-show="!adding && added">Added</span>
                        <span x-show="!adding && !added">{{ $product->is_in_stock ? 'Add to Cart' : 'Sold Out' }}</span>
                    </button>
                @endif
            </div>
        </div>

        {{-- Spacer for mobile sticky bar --}}
        <div class="h-20 lg:hidden" aria-hidden="true"></div>
    </div>
</x-layouts.app>
