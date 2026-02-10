<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Wishlist;
use App\Models\WishlistItem;

class WishlistService
{
    private ?Wishlist $wishlist = null;

    /**
     * Get or create the current session wishlist (eager-loads items + products + images).
     */
    public function wishlist(): Wishlist
    {
        if ($this->wishlist) {
            return $this->wishlist;
        }

        $sessionId = session()->getId();

        $this->wishlist = Wishlist::with('items.product.images', 'items.variant')
            ->where('session_id', $sessionId)
            ->first();

        if (! $this->wishlist) {
            $this->wishlist = Wishlist::create(['session_id' => $sessionId]);
            $this->wishlist->setRelation('items', collect());
        }

        return $this->wishlist;
    }

    /**
     * Toggle a product in the wishlist. Returns true if added, false if removed.
     */
    public function toggle(Product $product, ?ProductVariant $variant = null): bool
    {
        $wishlist = $this->wishlist();

        $item = WishlistItem::where('wishlist_id', $wishlist->id)
            ->where('product_id', $product->id)
            ->where('variant_id', $variant?->id)
            ->first();

        if ($item) {
            $item->delete();
            $this->refreshWishlist();

            return false;
        }

        WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'product_id' => $product->id,
            'variant_id' => $variant?->id,
        ]);

        $this->refreshWishlist();

        return true;
    }

    /**
     * Check if a product (optionally with variant) is in the wishlist.
     */
    public function has(int $productId, ?int $variantId = null): bool
    {
        return $this->wishlist()->items->contains(function (WishlistItem $item) use ($productId, $variantId) {
            return $item->product_id === $productId && $item->variant_id === $variantId;
        });
    }

    /**
     * Check if a product is in the wishlist (any variant).
     */
    public function hasProduct(int $productId): bool
    {
        return $this->wishlist()->items->contains('product_id', $productId);
    }

    /**
     * Remove an item from the wishlist.
     */
    public function remove(WishlistItem $item): void
    {
        $item->delete();
        $this->refreshWishlist();
    }

    /**
     * Total number of wishlist items.
     */
    public function count(): int
    {
        return $this->wishlist()->items->count();
    }

    /**
     * Return array of product IDs currently in the wishlist.
     */
    public function productIds(): array
    {
        return $this->wishlist()->items->pluck('product_id')->unique()->values()->all();
    }

    /**
     * Move a wishlist item to the cart (quantity 1), then remove from wishlist.
     */
    public function moveToCart(WishlistItem $item, CartService $cartService): void
    {
        $cartService->add($item->product, $item->variant);
        $this->remove($item);
    }

    /**
     * Force-reload wishlist from DB (invalidates in-memory cache).
     */
    private function refreshWishlist(): void
    {
        if ($this->wishlist) {
            $this->wishlist->load('items.product.images', 'items.variant');
        }
    }
}
