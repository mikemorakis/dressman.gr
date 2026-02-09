<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockReservation;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Create an order from checkout session data + cart.
     * Validates stock, recomputes totals server-side, reserves stock.
     *
     * @param  array<string, mixed>  $checkoutData
     *
     * @throws \RuntimeException if stock is insufficient
     */
    public function createFromCheckout(array $checkoutData, Cart $cart): Order
    {
        return DB::transaction(function () use ($checkoutData, $cart) {
            $this->validateStock($cart);

            $totals = $this->computeTotals();

            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => Order::generateOrderNumber(),
                'status' => OrderStatus::Pending,
                'payment_status' => PaymentStatus::Pending,
                'email' => $checkoutData['email'],
                'phone' => $checkoutData['shipping_phone'] ?? null,
                'billing_address' => $this->formatAddress($checkoutData, 'billing'),
                'shipping_address' => $this->formatAddress($checkoutData, 'shipping'),
                'subtotal' => $totals['subtotal'],
                'vat_rate' => $totals['vat_rate'],
                'vat_amount' => $totals['vat_amount'],
                'shipping_amount' => $totals['shipping'],
                'shipping_method_code' => $totals['shipping_method_code'],
                'shipping_label' => $totals['shipping_label'],
                'shipping_zone_code' => $totals['shipping_zone_code'] ?? null,
                'total' => $totals['total'],
                'currency' => config('shop.currency', 'EUR'),
                'prices_include_vat' => $totals['prices_include_vat'],
                'notes' => $checkoutData['notes'] ?? null,
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'product_name' => $item->product->name,
                    'variant_label' => $item->variant_id ? $item->variant->sku : null,
                    'sku' => $item->variant_id ? $item->variant->sku : $item->product->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'line_total' => $item->line_total,
                ]);
            }

            $this->reserveStock($order, $cart);

            $order->statusHistory()->create([
                'from_status' => null,
                'to_status' => OrderStatus::Pending->value,
                'notes' => 'Order created',
            ]);

            return $order;
        });
    }

    /**
     * Reserve stock for all items in the cart.
     */
    public function reserveStock(Order $order, Cart $cart): void
    {
        $ttl = (int) config('shop.reservation_ttl_minutes', 30);
        $expiresAt = now()->addMinutes($ttl);

        foreach ($cart->items as $item) {
            if ($item->variant_id) {
                ProductVariant::where('id', $item->variant_id)
                    ->increment('reserved_stock', $item->quantity);
            } else {
                Product::where('id', $item->product_id)
                    ->increment('reserved_stock', $item->quantity);
            }

            StockReservation::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'variant_id' => $item->variant_id,
                'quantity' => $item->quantity,
                'expires_at' => $expiresAt,
            ]);
        }
    }

    /**
     * Release stock reservations for an order (decrement reserved_stock, delete rows).
     */
    public function releaseReservations(Order $order): void
    {
        $reservations = $order->stockReservations()->get();

        foreach ($reservations as $reservation) {
            if ($reservation->variant_id) {
                ProductVariant::where('id', $reservation->variant_id)
                    ->decrement('reserved_stock', $reservation->quantity);
            } else {
                Product::where('id', $reservation->product_id)
                    ->decrement('reserved_stock', $reservation->quantity);
            }

            $reservation->delete();
        }
    }

    /**
     * Decrement actual stock on successful payment (webhook).
     * Also clears reserved_stock and deletes reservation rows.
     */
    public function decrementStock(Order $order): void
    {
        $order->load('items', 'stockReservations');

        foreach ($order->items as $item) {
            if ($item->variant_id) {
                ProductVariant::where('id', $item->variant_id)
                    ->decrement('stock', $item->quantity);
                ProductVariant::where('id', $item->variant_id)
                    ->decrement('reserved_stock', $item->quantity);
            } else {
                Product::where('id', $item->product_id)
                    ->decrement('stock', $item->quantity);
                Product::where('id', $item->product_id)
                    ->decrement('reserved_stock', $item->quantity);
            }
        }

        $order->stockReservations()->delete();
    }

    /**
     * Release all expired reservations across all orders. Returns count released.
     */
    public function releaseAllExpired(): int
    {
        $expired = StockReservation::expired()->get();

        if ($expired->isEmpty()) {
            return 0;
        }

        $orderIds = $expired->pluck('order_id')->unique();

        foreach ($expired as $reservation) {
            if ($reservation->variant_id) {
                ProductVariant::where('id', $reservation->variant_id)
                    ->decrement('reserved_stock', $reservation->quantity);
            } else {
                Product::where('id', $reservation->product_id)
                    ->decrement('reserved_stock', $reservation->quantity);
            }

            $reservation->delete();
        }

        // Cancel orders that had all reservations expired
        Order::whereIn('id', $orderIds)
            ->where('payment_status', PaymentStatus::Pending)
            ->each(function (Order $order) {
                if ($order->stockReservations()->count() === 0) {
                    $order->transitionTo(OrderStatus::Cancelled, 'Stock reservation expired');
                    $order->update(['payment_status' => PaymentStatus::Failed]);
                }
            });

        return $expired->count();
    }

    /**
     * Validate that all cart items have sufficient available stock.
     *
     * @throws \RuntimeException
     */
    private function validateStock(Cart $cart): void
    {
        foreach ($cart->items as $item) {
            $available = $item->variant
                ? $item->variant->available_stock
                : $item->product->available_stock;

            if ($item->quantity > $available) {
                throw new \RuntimeException(
                    "Insufficient stock for \"{$item->product->name}\". Available: {$available}, requested: {$item->quantity}."
                );
            }
        }
    }

    /**
     * Server-side recompute totals from current cart (Rule 1: never trust session totals).
     *
     * @return array{subtotal: float, net: float, vat_rate: float, vat_amount: float, shipping: float, shipping_method_code: string, shipping_label: string, shipping_zone_code: string|null, total: float, count: int, prices_include_vat: bool, free_shipping_remaining: float, free_shipping_progress: int}
     */
    private function computeTotals(): array
    {
        return app(CartService::class)->checkoutTotals();
    }

    /**
     * Format address fields from checkout data into a storable array.
     *
     * @param  array<string, mixed>  $data
     * @return array{first_name: string, last_name: string, address: string, address2: string|null, city: string, state: string|null, postal_code: string, country: string}
     */
    private function formatAddress(array $data, string $prefix): array
    {
        return [
            'first_name' => $data["{$prefix}_first_name"],
            'last_name' => $data["{$prefix}_last_name"],
            'address' => $data["{$prefix}_address"],
            'address2' => $data["{$prefix}_address2"] ?? null,
            'city' => $data["{$prefix}_city"],
            'state' => $data["{$prefix}_state"] ?? null,
            'postal_code' => $data["{$prefix}_postal_code"],
            'country' => $data["{$prefix}_country"],
        ];
    }
}
