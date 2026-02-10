<?php

namespace App\Http\Controllers\Blog;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class CategoryController
{
    public function show(string $slug): View
    {
        $category = BlogCategory::where('slug', $slug)->firstOrFail();

        $query = BlogPost::published()
            ->whereHas('categories', fn ($q) => $q->where('blog_categories.id', $category->id))
            ->with(['author', 'categories'])
            ->latest('published_at');

        $total = (clone $query)->count();
        $posts = $query->take(12)->get();

        $breadcrumbs = [
            ['label' => 'Blog', 'url' => route('blog.index')],
            ['label' => $category->name],
        ];

        return view('pages.blog.category', compact('category', 'posts', 'breadcrumbs', 'total'));
    }

    public function loadMore(Request $request, string $slug): Response
    {
        $category = BlogCategory::where('slug', $slug)->firstOrFail();
        $offset = (int) $request->query('offset', 12);

        $posts = BlogPost::published()
            ->whereHas('categories', fn ($q) => $q->where('blog_categories.id', $category->id))
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
