<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\WishlistItem;
use App\Services\CartService;
use App\Services\WishlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function __construct(
        private readonly WishlistService $wishlistService,
    ) {}

    /**
     * Show the full wishlist page.
     */
    public function index(): View
    {
        return view('pages.wishlist', [
            'items' => $this->wishlistService->wishlist()->items,
        ]);
    }

    /**
     * Return the drawer inner HTML fragment (no layout).
     */
    public function drawer(): Response
    {
        return response(
            view('partials.wishlist-drawer-content', [
                'items' => $this->wishlistService->wishlist()->items,
            ])->render()
        );
    }

    /**
     * Toggle a product in the wishlist. Returns JSON.
     */
    public function toggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'variant_id' => 'nullable|integer|exists:product_variants,id',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $variant = ! empty($validated['variant_id'])
            ? ProductVariant::findOrFail($validated['variant_id'])
            : null;

        $added = $this->wishlistService->toggle($product, $variant);

        return response()->json([
            'count' => $this->wishlistService->count(),
            'in_wishlist' => $added,
            'drawer_html' => $this->drawerHtml(),
        ]);
    }

    /**
     * Remove a wishlist item.
     */
    public function remove(Request $request, WishlistItem $item): JsonResponse|RedirectResponse
    {
        if ($item->wishlist_id !== $this->wishlistService->wishlist()->id) {
            abort(403);
        }

        $this->wishlistService->remove($item);

        if ($request->wantsJson()) {
            return response()->json([
                'count' => $this->wishlistService->count(),
                'drawer_html' => $this->drawerHtml(),
            ]);
        }

        return redirect()->route('wishlist');
    }

    /**
     * Move a wishlist item to the cart.
     */
    public function moveToCart(Request $request, WishlistItem $item): JsonResponse|RedirectResponse
    {
        if ($item->wishlist_id !== $this->wishlistService->wishlist()->id) {
            abort(403);
        }

        $cartService = app(CartService::class);
        $this->wishlistService->moveToCart($item, $cartService);

        if ($request->wantsJson()) {
            return response()->json([
                'wishlist_count' => $this->wishlistService->count(),
                'cart_count' => $cartService->count(),
                'wishlist_drawer_html' => $this->drawerHtml(),
                'cart_drawer_html' => view('partials.cart-drawer-content', [
                    'items' => $cartService->cart()->items,
                    'totals' => $cartService->totals(),
                ])->render(),
            ]);
        }

        return redirect()->route('wishlist');
    }

    /**
     * Render the wishlist drawer partial to a string.
     */
    private function drawerHtml(): string
    {
        return view('partials.wishlist-drawer-content', [
            'items' => $this->wishlistService->wishlist()->items,
        ])->render();
    }
}
