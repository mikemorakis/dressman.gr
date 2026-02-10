<?php

namespace App\Http\Controllers\Blog;

use App\Models\BlogPost;
use App\Models\UrlRedirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PostController
{
    public function index(): View
    {
        $query = BlogPost::published()
            ->with(['author', 'categories'])
            ->latest('published_at');

        $total = (clone $query)->count();
        $posts = $query->take(12)->get();

        $breadcrumbs = [['label' => 'Blog']];

        return view('pages.blog.index', compact('posts', 'breadcrumbs', 'total'));
    }

    public function indexLoadMore(Request $request): Response
    {
        $offset = (int) $request->query('offset', 12);

        $posts = BlogPost::published()
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->skip($offset)
            ->take(12)
            ->get();

        if ($posts->isEmpty()) {
            return response('', 204);
        }

        $html = '';
        foreach ($posts as $post) {
            $html .= view('components.blog-card', ['post' => $post])->render();
        }

        return response($html);
    }

    public function show(string $slug): View|RedirectResponse
    {
        $post = BlogPost::published()
            ->where('slug', $slug)
            ->with(['author', 'categories', 'tags'])
            ->first();

        if (! $post) {
            $redirect = UrlRedirect::where('old_slug', $slug)->where('type', 'blog_post')->first();

            if ($redirect) {
                return redirect()->route('blog.show', $redirect->new_slug, 301);
            }

            abort(404);
        }

        $commentsQuery = $post->comments()->approved()->oldest();
        $commentTotal = (clone $commentsQuery)->count();
        $comments = $commentsQuery->take(10)->get();

        $breadcrumbs = [
            ['label' => 'Blog', 'url' => route('blog.index')],
            ['label' => $post->title],
        ];

        // Related posts by shared categories or tags
        $categoryIds = $post->categories->pluck('id');
        $tagIds = $post->tags->pluck('id');

        $relatedPosts = collect();
        if ($categoryIds->isNotEmpty() || $tagIds->isNotEmpty()) {
            $relatedPosts = BlogPost::published()
                ->where('id', '!=', $post->id)
                ->where(function ($query) use ($categoryIds, $tagIds) {
                    if ($categoryIds->isNotEmpty()) {
                        $query->whereHas('categories', fn ($q) => $q->whereIn('blog_categories.id', $categoryIds));
                    }
                    if ($tagIds->isNotEmpty()) {
                        $query->orWhereHas('tags', fn ($q) => $q->whereIn('blog_tags.id', $tagIds));
                    }
                })
                ->with(['author', 'categories'])
                ->latest('published_at')
                ->take(4)
                ->get();
        }

        // BlogPosting JSON-LD
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->title,
            'description' => $post->meta_description ?? $post->excerpt ?? Str::limit(strip_tags((string) $post->body), 160),
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String(),
            'url' => route('blog.show', $post->slug),
            'author' => [
                '@type' => 'Person',
                'name' => $post->author ? $post->author->name : config('app.name'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => route('blog.show', $post->slug),
            ],
        ];

        if ($post->featured_image_path_large) {
            $jsonLd['image'] = asset('storage/'.$post->featured_image_path_large);
        }

        return view('pages.blog.show', compact('post', 'comments', 'commentTotal', 'breadcrumbs', 'relatedPosts', 'jsonLd'));
    }

    public function loadMoreComments(Request $request, string $slug): Response
    {
        $post = BlogPost::published()->where('slug', $slug)->firstOrFail();
        $offset = (int) $request->query('offset', 10);

        $comments = $post->comments()->approved()->oldest()
            ->skip($offset)
            ->take(10)
            ->get();

        if ($comments->isEmpty()) {
            return response('', 204);
        }

        $html = '';
        foreach ($comments as $comment) {
            $html .= '<div class="flex gap-4">'
                .'<div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-sm font-medium">'
                .strtoupper(substr($comment->author_name, 0, 1))
                .'</div>'
                .'<div class="flex-1 min-w-0">'
                .'<div class="flex items-center gap-2">'
                .'<span class="text-sm font-medium text-gray-900">'.e($comment->author_name).'</span>'
                .'<time class="text-xs text-gray-500" datetime="'.$comment->created_at->toDateString().'">'
                .$comment->created_at->diffForHumans()
                .'</time>'
                .'</div>'
                .'<p class="mt-1 text-sm text-gray-700">'.e($comment->body).'</p>'
                .'</div>'
                .'</div>';
        }

        return response($html);
    }

    public function storeComment(Request $request, string $slug): RedirectResponse
    {
        $post = BlogPost::published()->where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'author_name' => ['required', 'string', 'max:255'],
            'author_email' => ['required', 'email', 'max:255'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $post->comments()->create([
            ...$validated,
            'is_approved' => false,
        ]);

        return redirect()->back()->with('success', 'Your comment has been submitted and is awaiting moderation.');
    }
}
