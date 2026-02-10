@php
    /** @var \App\Models\BlogCategory $category */
    /** @var \Illuminate\Database\Eloquent\Collection $posts */
    /** @var int $total */
@endphp

<x-layouts.app>
    <x-slot:seo>
        <x-seo-meta
            :title="$category->name . ' — Blog — ' . config('app.name', 'PeShop')"
            :description="$category->description"
            :canonical="route('blog.category', $category->slug)"
        />
    </x-slot:seo>

    <x-slot:breadcrumb>
        <x-breadcrumb :items="$breadcrumbs" />
    </x-slot:breadcrumb>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ $category->name }}</h1>
            @if($category->description)
                <p class="mt-2 text-gray-600 max-w-3xl">{{ $category->description }}</p>
            @endif
        </div>

        <p class="text-sm text-gray-500 mb-6">{{ $total }} {{ Str::plural('post', $total) }}</p>

        @if($posts->isNotEmpty())
            <div x-data="{ offset: 12, loading: false, hasMore: {{ $total > 12 ? 'true' : 'false' }} }">
                <div id="blog-category-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    @foreach($posts as $post)
                        <x-blog-card :post="$post" />
                    @endforeach
                </div>

                <div x-show="hasMore" class="mt-8 text-center">
                    <button
                        @click="
                            loading = true;
                            fetch('{{ route('blog.category.loadMore', $category->slug) }}?offset=' + offset)
                                .then(r => { if (r.status === 204) { hasMore = false; loading = false; return ''; } return r.text(); })
                                .then(html => {
                                    if (html) {
                                        document.getElementById('blog-category-grid').insertAdjacentHTML('beforeend', html);
                                        offset += 12;
                                    }
                                    loading = false;
                                })
                        "
                        :disabled="loading"
                        class="inline-flex items-center gap-2 bg-gray-900 text-white px-8 py-3 text-sm font-semibold hover:bg-gray-800 transition-colors disabled:opacity-50"
                    >
                        <svg x-show="loading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span x-text="loading ? 'Loading...' : 'Load More'"></span>
                    </button>
                </div>
            </div>
        @else
            <div class="text-center py-16">
                <h3 class="text-sm font-semibold text-gray-900">No posts in this category</h3>
                <p class="mt-1 text-sm text-gray-500">Check back later for new articles.</p>
            </div>
        @endif
    </div>
</x-layouts.app>
