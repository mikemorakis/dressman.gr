@props(['count' => 0])

<div
    x-data="{ count: {{ (int) $count }} }"
    @wishlist-count-updated.window="count = $event.detail.count"
    class="relative"
    aria-live="polite"
    aria-atomic="true"
>
    <button
        type="button"
        @click="$dispatch('wishlist-open')"
        class="p-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
        :aria-label="'Wishlist, ' + count + ' ' + (count === 1 ? 'item' : 'items')"
    >
        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
        </svg>
        <span
            x-show="count > 0"
            x-text="count"
            x-cloak
            class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center h-4 min-w-[1rem] px-1 text-[10px] font-bold leading-none text-white bg-primary-600 rounded-full"
            aria-hidden="true"
        ></span>
    </button>
</div>
