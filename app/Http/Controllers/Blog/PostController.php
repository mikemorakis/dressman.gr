<?php

namespace App\Http\Controllers\Blog;

use App\Models\BlogPost;
use App\Models\UrlRedirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PostController
{
    public function index(): View
    {
        $posts = BlogPost::published()
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->paginate(12);

        $breadcrumbs = [['label' => 'Blog']];

        return view('pages.blog.index', compact('posts', 'breadcrumbs'));
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

        $comments = $post->comments()->approved()->oldest()->paginate(10);

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

        return view('pages.blog.show', compact('post', 'comments', 'breadcrumbs', 'relatedPosts', 'jsonLd'));
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
