<?php

namespace App\Services;

use App\Models\Order;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a Stripe Checkout Session for the given order.
     * Returns the Session object (use ->url to redirect).
     */
    public function createCheckoutSession(Order $order): Session
    {
        $lineItems = [];

        foreach ($order->items as $item) {
            $itemData = [
                'price_data' => [
                    'currency' => strtolower($order->currency),
                    'product_data' => [
                        'name' => $item->product_name,
                    ],
                    'unit_amount' => (int) round((float) $item->unit_price * 100),
                ],
                'quantity' => $item->quantity,
            ];

            if ($item->variant_label) {
                $itemData['price_data']['product_data']['description'] = "SKU: {$item->sku}";
            }

            $lineItems[] = $itemData;
        }

        // Add shipping as line item if > 0
        if ((float) $order->shipping_amount > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => strtolower($order->currency),
                    'product_data' => [
                        'name' => $order->shipping_label ?? 'Shipping',
                    ],
                    'unit_amount' => (int) round((float) $order->shipping_amount * 100),
                ],
                'quantity' => 1,
            ];
        }

        // Add VAT as line item when prices don't include VAT
        if (! $order->prices_include_vat && (float) $order->vat_amount > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => strtolower($order->currency),
                    'product_data' => [
                        'name' => 'VAT ('.number_format((float) $order->vat_rate, 0).'%)',
                    ],
                    'unit_amount' => (int) round((float) $order->vat_amount * 100),
                ],
                'quantity' => 1,
            ];
        }

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('checkout.success').'?session_id={CHECKOUT_SESSION_ID}&token='.$order->guest_token,
            'cancel_url' => route('checkout.cancel').'?session_id={CHECKOUT_SESSION_ID}',
            'customer_email' => $order->email,
            'metadata' => [
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
            ],
            'expires_at' => now()->addMinutes((int) config('shop.reservation_ttl_minutes', 30))->timestamp,
        ]);

        $order->update(['stripe_checkout_session_id' => $session->id]);

        return $session;
    }
}
