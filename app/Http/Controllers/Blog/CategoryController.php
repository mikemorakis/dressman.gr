<?php

namespace App\Http\Controllers\Blog;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\View\View;

class CategoryController
{
    public function show(string $slug): View
    {
        $category = BlogCategory::where('slug', $slug)->firstOrFail();

        $posts = BlogPost::published()
            ->whereHas('categories', fn ($q) => $q->where('blog_categories.id', $category->id))
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->paginate(12);

        $breadcrumbs = [
            ['label' => 'Blog', 'url' => route('blog.index')],
            ['label' => $category->name],
        ];

        return view('pages.blog.category', compact('category', 'posts', 'breadcrumbs'));
    }
}
