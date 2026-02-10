@php
    $featuredProducts = \App\Models\Product::active()
        ->featured()
        ->with('images', 'labels')
        ->take(8)
        ->get();

    if ($featuredProducts->isEmpty()) {
        $featuredProducts = \App\Models\Product::active()
            ->with('images', 'labels')
            ->latest('published_at')
            ->take(8)
            ->get();
    }
@endphp

<x-layouts.app>
    <x-slot:seo>
        <x-seo-meta
            title="404 — Η σελίδα δεν βρέθηκε — Dressman"
            description="Η σελίδα που ψάχνετε δεν βρέθηκε."
            :noindex="true"
        />
    </x-slot:seo>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        {{-- 404 message --}}
        <div class="text-center">
            <p class="text-6xl font-bold text-gray-200">404</p>
            <h1 class="mt-4 text-2xl font-bold text-gray-900">Η σελίδα δεν βρέθηκε</h1>
            <p class="mt-2 text-gray-600">Η σελίδα που ψάχνετε δεν υπάρχει ή έχει μετακινηθεί.</p>

            {{-- Search bar --}}
            <form action="{{ url('/search') }}" method="GET" class="mt-8 max-w-lg mx-auto">
                <div class="relative">
                    <input
                        type="search"
                        name="q"
                        placeholder="Αναζήτηση προϊόντων..."
                        class="w-full pl-12 pr-4 py-3 text-base border border-gray-300 rounded-none focus:outline-none focus:ring-2 focus:ring-black focus:border-black"
                    >
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>
            </form>

            <a href="{{ url('/') }}" class="mt-6 inline-block btn-primary">
                Επιστροφή στην Αρχική
            </a>
        </div>

        {{-- Best sellers --}}
        @if($featuredProducts->isNotEmpty())
            <div class="mt-16 pt-12 border-t border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 text-center">Δημοφιλή Προϊόντα</h2>
                <div class="mt-8 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                    @foreach($featuredProducts as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
