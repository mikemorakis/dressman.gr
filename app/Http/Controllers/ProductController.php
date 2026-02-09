<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\UrlRedirect;

class ProductController
{
    public function show(string $slug): \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
    {
        $product = Product::active()->where('slug', $slug)->with([
            'images',
            'brand',
            'categories',
            'labels',
            'variants.attributeValues.attribute',
        ])->first();

        // Try 301 redirect from old slug
        if (! $product) {
            $redirect = UrlRedirect::where('old_slug', $slug)->where('type', 'product')->first();

            if ($redirect) {
                return redirect()->route('product.show', $redirect->new_slug, 301);
            }

            abort(404);
        }

        $breadcrumbs = [];
        $category = $product->categories->first();
        if ($category) {
            if ($category->parent) {
                $breadcrumbs[] = ['label' => $category->parent->name, 'url' => route('category.show', $category->parent->slug)];
            }
            $breadcrumbs[] = ['label' => $category->name, 'url' => route('category.show', $category->slug)];
        }
        $breadcrumbs[] = ['label' => $product->name];

        // Build variant data for Alpine.js
        $variantData = [];
        if ($product->has_variants) {
            foreach ($product->variants->where('is_active', true) as $variant) {
                $attrs = [];
                foreach ($variant->attributeValues as $av) {
                    $attrs[$av->attribute->name] = $av->value;
                }
                $variantData[] = [
                    'id' => $variant->id,
                    'sku' => $variant->sku,
                    'price' => $variant->effective_price,
                    'price_formatted' => format_price($variant->effective_price),
                    'in_stock' => $variant->is_in_stock,
                    'stock' => $variant->available_stock,
                    'attributes' => $attrs,
                ];
            }
        }

        // Schema.org Product JSON-LD
        $firstImage = $product->images->first();
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $product->short_description ?? strip_tags((string) $product->description),
            'sku' => $product->sku,
            'offers' => [
                '@type' => 'Offer',
                'url' => route('product.show', $product->slug),
                'priceCurrency' => 'EUR',
                'price' => $product->price,
                'availability' => $product->is_in_stock
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
            ],
        ];
        if ($firstImage) {
            $jsonLd['image'] = asset('storage/'.$firstImage->path_large);
        }
        if ($product->brand) {
            $jsonLd['brand'] = ['@type' => 'Brand', 'name' => $product->brand->name];
        }

        // Similar products — same category, excluding current
        $similarProducts = collect();
        if ($category) {
            $similarProducts = Product::active()
                ->whereHas('categories', fn ($q) => $q->where('categories.id', $category->id))
                ->where('id', '!=', $product->id)
                ->with(['images', 'labels'])
                ->inRandomOrder()
                ->limit(4)
                ->get();
        }

        // Recently viewed — session-based tracking
        $recentIds = session('recently_viewed', []);
        $recentIds = array_diff($recentIds, [$product->id]);
        array_unshift($recentIds, $product->id);
        $recentIds = array_values(array_slice($recentIds, 0, 10));
        session(['recently_viewed' => $recentIds]);

        // Fetch recently viewed (exclude current, max 4)
        $recentViewedIds = array_slice(array_diff($recentIds, [$product->id]), 0, 4);
        $recentlyViewed = collect();
        if (! empty($recentViewedIds)) {
            $recentlyViewed = Product::active()
                ->whereIn('id', $recentViewedIds)
                ->with(['images', 'labels'])
                ->get()
                ->sortBy(fn ($p) => array_search($p->id, $recentViewedIds));
        }

        return view('pages.product.show', compact(
            'product', 'breadcrumbs', 'variantData', 'jsonLd',
            'similarProducts', 'recentlyViewed',
        ));
    }
}
