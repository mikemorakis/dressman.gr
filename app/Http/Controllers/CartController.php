<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
    ) {}

    /**
     * Show the full cart page.
     */
    public function index(): View
    {
        $cartItemProductIds = $this->cartService->cart()->items->pluck('product_id')->all();

        $crossSell = Product::active()
            ->whereHas('categories', fn ($q) => $q->where('slug', 'axesouar'))
            ->whereNotIn('id', $cartItemProductIds)
            ->with(['images', 'labels'])
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('pages.cart', [
            'items' => $this->cartService->cart()->items,
            'totals' => $this->cartService->totals(),
            'crossSell' => $crossSell,
        ]);
    }

    /**
     * Return the drawer inner HTML fragment (no layout).
     */
    public function drawer(): Response
    {
        return response(
            view('partials.cart-drawer-content', [
                'items' => $this->cartService->cart()->items,
                'totals' => $this->cartService->totals(),
            ])->render()
        );
    }

    /**
     * Add a product (or variant) to the cart. Returns JSON.
     */
    public function add(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'variant_id' => 'nullable|integer|exists:product_variants,id',
            'quantity' => 'integer|min:1|max:99',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if ($product->has_variants && empty($validated['variant_id'])) {
            return response()->json(['error' => 'Please select a variant.'], 422);
        }

        $variant = ! empty($validated['variant_id'])
            ? ProductVariant::findOrFail($validated['variant_id'])
            : null;

        $this->cartService->add($product, $variant, $validated['quantity'] ?? 1);

        return response()->json([
            'count' => $this->cartService->count(),
            'drawer_html' => $this->drawerHtml(),
        ]);
    }

    /**
     * Update a cart item's quantity.
     */
    public function update(Request $request, CartItem $item): JsonResponse|RedirectResponse
    {
        if ($item->cart_id !== $this->cartService->cart()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:0|max:99',
        ]);

        $this->cartService->updateQuantity($item, $validated['quantity']);

        if ($request->wantsJson()) {
            return response()->json([
                'count' => $this->cartService->count(),
                'drawer_html' => $this->drawerHtml(),
            ]);
        }

        return redirect()->route('cart');
    }

    /**
     * Remove a cart item.
     */
    public function remove(Request $request, CartItem $item): JsonResponse|RedirectResponse
    {
        if ($item->cart_id !== $this->cartService->cart()->id) {
            abort(403);
        }

        $this->cartService->remove($item);

        if ($request->wantsJson()) {
            return response()->json([
                'count' => $this->cartService->count(),
                'drawer_html' => $this->drawerHtml(),
            ]);
        }

        return redirect()->route('cart');
    }

    /**
     * Clear all cart items.
     */
    public function clear(Request $request): JsonResponse|RedirectResponse
    {
        $this->cartService->clear();

        if ($request->wantsJson()) {
            return response()->json([
                'count' => 0,
                'drawer_html' => $this->drawerHtml(),
            ]);
        }

        return redirect()->route('cart');
    }

    /**
     * Render the drawer partial to a string.
     */
    private function drawerHtml(): string
    {
        return view('partials.cart-drawer-content', [
            'items' => $this->cartService->cart()->items,
            'totals' => $this->cartService->totals(),
        ])->render();
    }
}
