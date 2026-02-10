<?php

namespace App\Http\Controllers\Blog;

use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TagController
{
    public function show(string $slug): View
    {
        $tag = BlogTag::where('slug', $slug)->firstOrFail();

        $query = BlogPost::published()
            ->whereHas('tags', fn ($q) => $q->where('blog_tags.id', $tag->id))
            ->with(['author', 'categories'])
            ->latest('published_at');

        $total = (clone $query)->count();
        $posts = $query->take(12)->get();

        $breadcrumbs = [
            ['label' => 'Blog', 'url' => route('blog.index')],
            ['label' => '#'.$tag->name],
        ];

        return view('pages.blog.tag', compact('tag', 'posts', 'breadcrumbs', 'total'));
    }

    public function loadMore(Request $request, string $slug): Response
    {
        $tag = BlogTag::where('slug', $slug)->firstOrFail();
        $offset = (int) $request->query('offset', 12);

        $posts = BlogPost::published()
            ->whereHas('tags', fn ($q) => $q->where('blog_tags.id', $tag->id))
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
}
