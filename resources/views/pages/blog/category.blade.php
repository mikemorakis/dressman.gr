@php
    /** @var \App\Models\BlogCategory $category */
    /** @var \Illuminate\Pagination\LengthAwarePaginator $posts */
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

        <p class="text-sm text-gray-500 mb-6">{{ $posts->total() }} {{ Str::plural('post', $posts->total()) }}</p>

        @if($posts->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @foreach($posts as $post)
                    <x-blog-card :post="$post" />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <h3 class="text-sm font-semibold text-gray-900">No posts in this category</h3>
                <p class="mt-1 text-sm text-gray-500">Check back later for new articles.</p>
            </div>
        @endif
    </div>
</x-layouts.app>
