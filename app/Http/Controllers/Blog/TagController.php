<?php

namespace App\Http\Controllers\Blog;

use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\View\View;

class TagController
{
    public function show(string $slug): View
    {
        $tag = BlogTag::where('slug', $slug)->firstOrFail();

        $posts = BlogPost::published()
            ->whereHas('tags', fn ($q) => $q->where('blog_tags.id', $tag->id))
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->paginate(12);

        $breadcrumbs = [
            ['label' => 'Blog', 'url' => route('blog.index')],
            ['label' => '#'.$tag->name],
        ];

        return view('pages.blog.tag', compact('tag', 'posts', 'breadcrumbs'));
    }
}
