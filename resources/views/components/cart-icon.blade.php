@props(['count' => 0])

<div
    x-data="{ count: {{ (int) $count }} }"
    @cart-count-updated.window="count = $event.detail.count"
    class="relative"
    aria-live="polite"
    aria-atomic="true"
>
    <button
        type="button"
        @click="$dispatch('cart-open')"
        class="p-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
        :aria-label="'Shopping cart, ' + count + ' ' + (count === 1 ? 'item' : 'items')"
    >
        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
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
