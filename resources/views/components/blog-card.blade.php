@props([
    'post',
])

@php
    /** @var \App\Models\BlogPost $post */
    $url = route('blog.show', $post->slug);
@endphp

<article class="group relative flex flex-col bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
    {{-- Featured image --}}
    <a href="{{ $url }}" class="block aspect-[3/2] overflow-hidden bg-gray-100" aria-label="{{ $post->title }}">
        @if($post->featured_image_path_medium)
            <img
                src="{{ asset('storage/' . $post->featured_image_path_medium) }}"
                alt=""
                @if($post->featured_image_width && $post->featured_image_height)
                    width="{{ (int) round($post->featured_image_width * 600 / max($post->featured_image_width, 1)) }}"
                    height="{{ (int) round($post->featured_image_height * 600 / max($post->featured_image_width, 1)) }}"
                @endif
                loading="lazy"
                decoding="async"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            >
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-400">
                <svg class="h-12 w-12" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5" />
                </svg>
            </div>
        @endif
    </a>

    {{-- Content --}}
    <div class="flex flex-col flex-1 p-4">
        {{-- Categories --}}
        @if($post->categories->isNotEmpty())
            <div class="flex flex-wrap gap-1 mb-2">
                @foreach($post->categories as $category)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-primary-50 text-primary-700">
                        {{ $category->name }}
                    </span>
                @endforeach
            </div>
        @endif

        <h3 class="text-sm font-semibold text-gray-900 line-clamp-2">
            <a href="{{ $url }}" class="after:absolute after:inset-0">
                {{ $post->title }}
            </a>
        </h3>

        @if($post->excerpt)
            <p class="mt-2 text-sm text-gray-600 line-clamp-3">{{ $post->excerpt }}</p>
        @endif

        <div class="mt-auto pt-3 flex items-center gap-2 text-xs text-gray-500">
            @if($post->author)
                <span>{{ $post->author->name }}</span>
                <span aria-hidden="true">&middot;</span>
            @endif
            <time datetime="{{ $post->published_at->toDateString() }}">
                {{ $post->published_at->format('M j, Y') }}
            </time>
        </div>
    </div>
</article>
