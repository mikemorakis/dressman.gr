@php
    /** @var \App\Models\BlogPost $post */
    /** @var \Illuminate\Pagination\LengthAwarePaginator $comments */
    /** @var \Illuminate\Database\Eloquent\Collection $relatedPosts */
@endphp

<x-layouts.app>
    <x-slot:seo>
        <x-seo-meta
            :title="$post->meta_title ?: $post->title . ' — ' . config('app.name', 'PeShop')"
            :description="$post->meta_description ?: $post->excerpt"
            :canonical="route('blog.show', $post->slug)"
            :ogImage="$post->featured_image_path_large ? asset('storage/' . $post->featured_image_path_large) : ''"
            ogType="article"
            :jsonLd="$jsonLd"
        />
    </x-slot:seo>

    <x-slot:breadcrumb>
        <x-breadcrumb :items="$breadcrumbs" />
    </x-slot:breadcrumb>

    <article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <header class="mb-8">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900">{{ $post->title }}</h1>

            <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-gray-500">
                @if($post->author)
                    <div class="flex items-center gap-2">
                        @if($post->author->avatar_path)
                            <img src="{{ asset('storage/' . $post->author->avatar_path) }}" alt="" class="h-8 w-8 rounded-full object-cover">
                        @endif
                        <span class="font-medium text-gray-900">{{ $post->author->name }}</span>
                    </div>
                    <span aria-hidden="true">&middot;</span>
                @endif
                <time datetime="{{ $post->published_at->toDateString() }}">
                    {{ $post->published_at->format('F j, Y') }}
                </time>
                <span aria-hidden="true">&middot;</span>
                <span>{{ $post->reading_time }} min read</span>
            </div>

            {{-- Categories + Tags --}}
            @if($post->categories->isNotEmpty() || $post->tags->isNotEmpty())
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach($post->categories as $category)
                        <a href="{{ route('blog.category', $category->slug) }}"
                           class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-50 text-primary-700 hover:bg-primary-100 transition-colors">
                            {{ $category->name }}
                        </a>
                    @endforeach
                    @foreach($post->tags as $tag)
                        <a href="{{ route('blog.tag', $tag->slug) }}"
                           class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors">
                            #{{ $tag->name }}
                        </a>
                    @endforeach
                </div>
            @endif
        </header>

        {{-- Featured image --}}
        @if($post->featured_image_path_large)
            <figure class="mb-8 rounded-lg overflow-hidden bg-gray-100">
                <img
                    src="{{ asset('storage/' . $post->featured_image_path_large) }}"
                    alt=""
                    @if($post->featured_image_width && $post->featured_image_height)
                        width="{{ $post->featured_image_width }}"
                        height="{{ $post->featured_image_height }}"
                    @endif
                    class="w-full h-auto"
                >
            </figure>
        @endif

        {{-- Body --}}
        <div class="prose prose-gray max-w-none">
            {!! clean_html($post->body) !!}
        </div>
    </article>

    {{-- Comments --}}
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 border-t border-gray-200">
        <h2 class="text-xl font-bold text-gray-900">
            Comments ({{ $comments->total() }})
        </h2>

        @if($comments->isNotEmpty())
            <div class="mt-6 space-y-6">
                @foreach($comments as $comment)
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-sm font-medium">
                            {{ strtoupper(substr($comment->author_name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-900">{{ $comment->author_name }}</span>
                                <time class="text-xs text-gray-500" datetime="{{ $comment->created_at->toDateString() }}">
                                    {{ $comment->created_at->diffForHumans() }}
                                </time>
                            </div>
                            <p class="mt-1 text-sm text-gray-700">{{ $comment->body }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $comments->links() }}
            </div>
        @endif

        {{-- Comment form --}}
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-gray-900">Leave a comment</h3>

            @if(session('success'))
                <div class="mt-4 rounded-md bg-green-50 p-4">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            <form action="{{ route('blog.comment.store', $post->slug) }}" method="POST" class="mt-4 space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="author_name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="author_name" id="author_name" value="{{ old('author_name') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        @error('author_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="author_email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="author_email" id="author_email" value="{{ old('author_email') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        @error('author_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="comment_body" class="block text-sm font-medium text-gray-700">Comment</label>
                    <textarea name="body" id="comment_body" rows="4" required maxlength="2000"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('body') }}</textarea>
                    @error('body')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-primary">Submit Comment</button>
            </form>
        </div>
    </section>

    {{-- Related Posts --}}
    @if($relatedPosts->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 border-t border-gray-200">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Related Posts</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedPosts as $related)
                    <x-blog-card :post="$related" />
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.app>
