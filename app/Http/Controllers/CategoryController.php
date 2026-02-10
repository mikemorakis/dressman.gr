<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController
{
    public function show(Request $request, string $slug): \Illuminate\View\View
    {
        $category = Category::visible()->where('slug', $slug)->firstOrFail();

        $sort = $request->query('sort', 'newest');

        $query = $this->buildCategoryQuery($category, $sort);

        $total = (clone $query)->count();
        $products = $query->take(12)->get();

        $children = $category->children()->visible()->orderBy('sort_order')->get();

        $breadcrumbs = [];
        if ($category->parent) {
            $breadcrumbs[] = ['label' => $category->parent->name, 'url' => route('category.show', $category->parent->slug)];
        }
        $breadcrumbs[] = ['label' => $category->name];

        return view('pages.category.show', compact('category', 'products', 'children', 'breadcrumbs', 'sort', 'total'));
    }

    public function loadMore(Request $request, string $slug): Response
    {
        $category = Category::visible()->where('slug', $slug)->firstOrFail();
        $offset = (int) $request->query('offset', 12);
        $sort = $request->query('sort', 'newest');

        $products = $this->buildCategoryQuery($category, $sort)
            ->skip($offset)
            ->take(12)
            ->get();

        if ($products->isEmpty()) {
            return response('', 204);
        }

        $html = '';
        foreach ($products as $product) {
            $html .= view('components.product-card', ['product' => $product])->render();
        }

        return response($html);
    }

    /**
     * @return Builder<Product>
     */
    private function buildCategoryQuery(Category $category, string $sort): Builder
    {
        $categoryIds = $this->getCategoryAndDescendantIds($category);

        $query = Product::active()
            ->withAvailableStock()
            ->with('images', 'labels')
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds));

        return match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name' => $query->orderBy('name'),
            default => $query->latest('published_at'),
        };
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
