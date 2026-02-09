<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController
{
    public function show(Request $request, string $slug): \Illuminate\View\View
    {
        $category = Category::visible()->where('slug', $slug)->firstOrFail();

        $sort = $request->query('sort', 'newest');

        // Include products from this category AND all descendant categories
        $categoryIds = $this->getCategoryAndDescendantIds($category);

        $query = Product::active()
            ->withAvailableStock()
            ->with('images', 'labels')
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds));

        $query = match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name' => $query->orderBy('name'),
            default => $query->latest('published_at'),
        };

        $products = $query->paginate(12)->withQueryString();

        $children = $category->children()->visible()->orderBy('sort_order')->get();

        $breadcrumbs = [];
        if ($category->parent) {
            $breadcrumbs[] = ['label' => $category->parent->name, 'url' => route('category.show', $category->parent->slug)];
        }
        $breadcrumbs[] = ['label' => $category->name];

        return view('pages.category.show', compact('category', 'products', 'children', 'breadcrumbs', 'sort'));
    }

    /**
     * Get IDs for a category and all its descendants (recursive).
     *
     * @return list<int>
     */
    private function getCategoryAndDescendantIds(Category $category): array
    {
        $ids = [$category->id];

        $children = Category::where('parent_id', $category->id)->get();

        foreach ($children as $child) {
            $ids = array_merge($ids, $this->getCategoryAndDescendantIds($child));
        }

        return $ids;
    }
}
