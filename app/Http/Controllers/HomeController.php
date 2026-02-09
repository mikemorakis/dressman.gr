<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HomeController
{
    public function __invoke(): \Illuminate\View\View
    {
        // Best sellers — γαμπριάτικα κοστούμια (first 12, load more via JS)
        $bestSellers = Product::active()
            ->whereHas('categories', fn ($q) => $q->where('slug', 'gabriatika-kostoumia'))
            ->withAvailableStock()
            ->with('images', 'labels')
            ->latest('published_at')
            ->take(12)
            ->get();

        // Shop by category — key storefront categories
        $categories = Category::visible()
            ->whereIn('slug', [
                'kostoumia',
                'gabriatika-kostoumia',
                'sakakia',
                'poykamisa',
                'pantelonia',
                'axesouar',
            ])
            ->orderBy('sort_order')
            ->get();

        return view('pages.home', compact('bestSellers', 'categories'));
    }

    public function loadMore(Request $request): Response
    {
        $offset = (int) $request->query('offset', 12);

        $products = Product::active()
            ->whereHas('categories', fn ($q) => $q->where('slug', 'gabriatika-kostoumia'))
            ->withAvailableStock()
            ->with('images', 'labels')
            ->latest('published_at')
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
}
