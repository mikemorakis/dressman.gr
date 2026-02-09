<x-layouts.app>
    <x-slot:seo>
        <x-seo-meta
            title="Payment Cancelled — PeShop"
            description="Your payment was cancelled."
            :noindex="true"
        />
    </x-slot:seo>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center">
            <svg class="mx-auto h-16 w-16 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>

            <h1 class="mt-4 text-3xl font-bold text-gray-900">Payment Cancelled</h1>

            <p class="mt-2 text-lg text-gray-600">
                Your payment was cancelled. Your cart items are still saved if you'd like to try again.
            </p>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('checkout') }}" class="btn-primary">
                    Return to Checkout
                </a>
                <a href="{{ route('cart') }}" class="text-sm font-medium text-primary-600 hover:text-primary-800">
                    View Cart
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
