<x-layouts.app>
    <x-slot:seo>
        <x-seo-meta
            title="Blog — {{ config('app.name', 'PeShop') }}"
            description="Read our latest articles, tips, and updates."
            :canonical="route('blog.index')"
        />
    </x-slot:seo>

    <x-slot:breadcrumb>
        <x-breadcrumb :items="$breadcrumbs" />
    </x-slot:breadcrumb>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Blog</h1>
        </div>

        @if($posts->isNotEmpty())
            <div x-data="{ offset: 12, loading: false, hasMore: {{ $total > 12 ? 'true' : 'false' }} }">
                <div id="blog-posts-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    @foreach($posts as $post)
                        <x-blog-card :post="$post" />
                    @endforeach
                </div>

                <div x-show="hasMore" class="mt-8 text-center">
                    <button
                        @click="
                            loading = true;
                            fetch('{{ route('blog.loadMore') }}?offset=' + offset)
                                .then(r => { if (r.status === 204) { hasMore = false; loading = false; return ''; } return r.text(); })
                                .then(html => {
                                    if (html) {
                                        document.getElementById('blog-posts-grid').insertAdjacentHTML('beforeend', html);
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
                <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5" />
                </svg>
                <h3 class="mt-4 text-sm font-semibold text-gray-900">No posts yet</h3>
                <p class="mt-1 text-sm text-gray-500">Check back later for new articles.</p>
            </div>
        @endif
    </div>
</x-layouts.app>
