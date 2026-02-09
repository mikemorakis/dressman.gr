@php
    $q = request()->query('q', '');
    $canonicalUrl = url('/search') . ($q ? '?q=' . urlencode($q) : '');
    $hasNonQueryParams = request()->hasAny(['cat', 'brand', 'min', 'max', 'sort', 'page']);
@endphp

<x-layouts.app>
    <x-slot:seo>
        <x-seo-meta
            :title="$q ? 'Search: ' . e($q) . ' — PeShop' : 'Search — PeShop'"
            description="Search our catalog of quality products."
            :canonical="$canonicalUrl"
            :noindex="$hasNonQueryParams"
        />
    </x-slot:seo>

    @push('styles')
        @livewireStyles
    @endpush

    <livewire:product-search />

    @push('scripts')
        @livewireScripts
    @endpush
</x-layouts.app>
