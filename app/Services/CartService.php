<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\ShippingMethod;
use App\Models\ShippingRate;
use App\Models\ShippingZone;
use Illuminate\Support\Facades\Log;

class CartService
{
    private ?Cart $cart = null;

    /**
     * Get or create the current session cart (eager-loads items + products + images).
     */
    public function cart(): Cart
    {
        if ($this->cart) {
            return $this->cart;
        }

        $sessionId = session()->getId();

        $this->cart = Cart::with('items.product.images', 'items.variant')
            ->where('session_id', $sessionId)
            ->first();

        if (! $this->cart) {
            $this->cart = Cart::create(['session_id' => $sessionId]);
            $this->cart->setRelation('items', collect());
        }

        return $this->cart;
    }

    /**
     * Add a product (or variant) to the cart. Merges quantity if item already exists.
     */
    public function add(Product $product, ?ProductVariant $variant = null, int $quantity = 1): CartItem
    {
        $cart = $this->cart();

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('variant_id', $variant?->id)
            ->first();

        if ($item) {
            $item->update(['quantity' => $item->quantity + $quantity]);
        } else {
            $item = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'quantity' => $quantity,
            ]);
        }

        $this->refreshCart();

        return $item;
    }

    /**
     * Update quantity for a cart item. Removes if quantity <= 0.
     */
    public function updateQuantity(CartItem $item, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->remove($item);

            return;
        }

        $item->update(['quantity' => $quantity]);
        $this->refreshCart();
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(CartItem $item): void
    {
        $item->delete();
        $this->refreshCart();
    }

    /**
     * Clear all items from the cart.
     */
    public function clear(): void
    {
        $this->cart()->items()->delete();
        $this->refreshCart();
    }

    /**
     * Total number of individual items (sum of quantities).
     */
    public function count(): int
    {
        return $this->cart()->items->sum('quantity');
    }

    /**
     * Cart subtotal (sum of line totals).
     */
    public function subtotal(): float
    {
        return round($this->cart()->items->sum(fn (CartItem $item): float => $item->line_total), 2);
    }

    /**
     * Shipping cost based on the default shipping method's rate for the given country/zone.
     * For GR with a postal code, resolves zone and uses zone-specific rate.
     * Falls back to the flat_shipping_rate setting if no matching DB rate is found.
     */
    public function shipping(?string $countryCode = null, ?string $postalCode = null): float
    {
        $subtotal = $this->subtotal();
        $threshold = (float) Setting::get('free_shipping_threshold', 50.00);

        if ($subtotal >= $threshold) {
            return 0.00;
        }

        $countryCode = $countryCode ?? 'GR';
        $methodCode = (string) Setting::get('default_shipping_method_code', 'standard');

        $query = ShippingRate::whereHas('shippingMethod', fn ($q) => $q
            ->where('code', $methodCode)
            ->where('is_active', true)
        )
            ->where('country_code', $countryCode)
            ->where('is_active', true)
            ->where(fn ($q) => $q
                ->whereNull('min_subtotal')
                ->orWhere('min_subtotal', '<=', $subtotal)
            )
            ->where(fn ($q) => $q
                ->whereNull('max_subtotal')
                ->orWhere('max_subtotal', '>=', $subtotal)
            );

        // Zone-based lookup for GR with postal code
        if ($countryCode === 'GR' && $postalCode) {
            $zoneId = $this->resolveZoneId($postalCode);
            if ($zoneId) {
                $query->where('shipping_zone_id', $zoneId);
            } else {
                $query->whereNull('shipping_zone_id');
            }
        } else {
            $query->whereNull('shipping_zone_id');
        }

        $rate = $query->orderBy('flat_amount')->first();

        if (! $rate) {
            Log::warning('No shipping rate found', [
                'method_code' => $methodCode,
                'country_code' => $countryCode,
                'postal_code' => $postalCode,
                'subtotal' => $subtotal,
            ]);

            return (float) Setting::get('flat_shipping_rate', 3.50);
        }

        return (float) $rate->flat_amount;
    }

    /**
     * Resolve the active shipping method (code + label + amount + zone) for the given country/postal code.
     *
     * @return array{code: string, label: string, amount: float, zone_code: string|null}
     */
    public function resolveShipping(?string $countryCode = null, ?string $postalCode = null): array
    {
        $amount = $this->shipping($countryCode, $postalCode);
        $methodCode = (string) Setting::get('default_shipping_method_code', 'standard');

        $method = ShippingMethod::where('code', $methodCode)
            ->where('is_active', true)
            ->first();

        $zoneCode = null;
        $label = $method ? $method->name : 'Standard Shipping';

        // For GR with postal code, use zone name as label
        $countryCode = $countryCode ?? 'GR';
        if ($countryCode === 'GR' && $postalCode) {
            $zone = $this->resolveZone($postalCode);
            if ($zone) {
                $label = $zone->name;
                $zoneCode = $zone->code;
            }
        }

        return [
            'code' => $method ? $method->code : $methodCode,
            'label' => $label,
            'amount' => $amount,
            'zone_code' => $zoneCode,
        ];
    }

    /**
     * Grand total = subtotal + shipping.
     */
    public function total(): float
    {
        return round($this->subtotal() + $this->shipping(), 2);
    }

    /**
     * Amount remaining for free shipping (0 if already qualified).
     */
    public function freeShippingRemaining(): float
    {
        $threshold = (float) Setting::get('free_shipping_threshold', 50.00);

        return max(0, round($threshold - $this->subtotal(), 2));
    }

    /**
     * Free shipping progress as a percentage (0–100).
     */
    public function freeShippingProgress(): int
    {
        $threshold = (float) Setting::get('free_shipping_threshold', 50.00);

        if ($threshold <= 0) {
            return 100;
        }

        return min(100, (int) round($this->subtotal() / $threshold * 100));
    }

    /**
     * @return array{subtotal: float, shipping: float, total: float, count: int, free_shipping_remaining: float, free_shipping_progress: int}
     */
    public function totals(): array
    {
        return [
            'subtotal' => $this->subtotal(),
            'shipping' => $this->shipping(),
            'total' => $this->total(),
            'count' => $this->count(),
            'free_shipping_remaining' => $this->freeShippingRemaining(),
            'free_shipping_progress' => $this->freeShippingProgress(),
        ];
    }

    /**
     * Extended totals with VAT breakdown for the checkout page.
     *
     * @return array{subtotal: float, net: float, vat_rate: float, vat_amount: float, shipping: float, shipping_method_code: string, shipping_label: string, shipping_zone_code: string|null, total: float, count: int, prices_include_vat: bool, free_shipping_remaining: float, free_shipping_progress: int}
     */
    public function checkoutTotals(): array
    {
        $subtotal = $this->subtotal();
        $countryCode = session('checkout.shipping_country', 'GR');
        $postalCode = session('checkout.shipping_postal_code');
        $shippingInfo = $this->resolveShipping($countryCode, $postalCode);
        $shipping = $shippingInfo['amount'];
        $vatRate = (float) Setting::get('vat_rate', config('shop.vat_rate', 24.00));
        $pricesIncludeVat = filter_var(
            Setting::get('prices_include_vat', config('shop.prices_include_vat', true)),
            FILTER_VALIDATE_BOOLEAN
        );

        $vatBreakdown = calculate_vat($subtotal, $vatRate);

        $total = $pricesIncludeVat
            ? round($subtotal + $shipping, 2)
            : round($subtotal + $vatBreakdown['vat'] + $shipping, 2);

        return [
            'subtotal' => $subtotal,
            'net' => $vatBreakdown['net'],
            'vat_rate' => $vatRate,
            'vat_amount' => $vatBreakdown['vat'],
            'shipping' => $shipping,
            'shipping_method_code' => $shippingInfo['code'],
            'shipping_label' => $shippingInfo['label'],
            'shipping_zone_code' => $shippingInfo['zone_code'],
            'total' => $total,
            'count' => $this->count(),
            'prices_include_vat' => $pricesIncludeVat,
            'free_shipping_remaining' => $this->freeShippingRemaining(),
            'free_shipping_progress' => $this->freeShippingProgress(),
        ];
    }

    /**
     * Resolve shipping zone ID by postal code prefix.
     * Returns zone with matching prefix, or fallback zone (no prefixes), or null.
     */
    private function resolveZoneId(string $postalCode): ?int
    {
        return $this->resolveZone($postalCode)?->id;
    }

    /**
     * Resolve shipping zone model by postal code prefix.
     */
    private function resolveZone(string $postalCode): ?ShippingZone
    {
        $zones = ShippingZone::where('is_active', true)
            ->with('postalPrefixes')
            ->get();

        // First pass: find zone with matching prefix
        foreach ($zones as $zone) {
            foreach ($zone->postalPrefixes as $prefix) {
                if (str_starts_with($postalCode, $prefix->postal_prefix)) {
                    return $zone;
                }
            }
        }

        // Fallback: zone with no prefixes (e.g. NON_ATTICA)
        return $zones->first(fn (ShippingZone $z) => $z->postalPrefixes->isEmpty());
    }

    /**
     * Force-reload cart from DB (invalidates in-memory cache).
     */
    private function refreshCart(): void
    {
        if ($this->cart) {
            $this->cart->load('items.product.images', 'items.variant');
        }
    }
}
