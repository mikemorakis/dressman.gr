@php
    /** @var \App\Models\Category $category */
    /** @var \Illuminate\Pagination\LengthAwarePaginator $products */
    /** @var \Illuminate\Database\Eloquent\Collection $children */

    $sortOptions = [
        'newest' => 'Newest',
        'price_asc' => 'Price: Low to High',
        'price_desc' => 'Price: High to Low',
        'name' => 'Name: A–Z',
    ];
@endphp

<x-layouts.app>
    <x-slot:seo>
        <x-seo-meta
            :title="$category->meta_title ?: $category->name . ' — PeShop'"
            :description="$category->meta_description ?: $category->description"
        />
    </x-slot:seo>

    <x-slot:breadcrumb>
        <x-breadcrumb :items="$breadcrumbs" />
    </x-slot:breadcrumb>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Category header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ $category->name }}</h1>
            @if($category->description)
                <div class="mt-2 text-gray-600 max-w-none prose prose-sm">{!! $category->description !!}</div>
            @endif
        </div>

        {{-- Subcategories --}}
        @if($children->isNotEmpty())
            <div class="flex flex-wrap gap-2 mb-8">
                @foreach($children as $child)
                    <a href="{{ route('category.show', $child->slug) }}"
                       class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-gray-100 text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500">
                        {{ $child->name }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Sort + Count --}}
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
            <p class="text-sm text-gray-500">{{ $products->total() }} {{ Str::plural('product', $products->total()) }}</p>

            <div class="flex items-center gap-2">
                <label for="sort-select" class="text-sm text-gray-500 hidden sm:inline">Sort by:</label>
                <select
                    id="sort-select"
                    x-data
                    @change="window.location.href = $el.value"
                    class="text-sm border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500"
                >
                    @foreach($sortOptions as $value => $label)
                        <option
                            value="{{ route('category.show', $category->slug) }}?sort={{ $value }}"
                            @selected($sort === $value)
                        >
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Product grid --}}
        @if($products->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <h3 class="mt-4 text-sm font-semibold text-gray-900">No products found</h3>
                <p class="mt-1 text-sm text-gray-500">Check back later or browse other categories.</p>
            </div>
        @endif
    </div>
</x-layouts.app>
