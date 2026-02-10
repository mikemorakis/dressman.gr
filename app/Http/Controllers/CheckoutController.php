<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\StripeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly OrderService $orderService,
        private readonly StripeService $stripeService,
    ) {}

    public function show(): View|RedirectResponse
    {
        if ($this->cartService->count() === 0) {
            return redirect()->route('cart')
                ->with('error', 'Your cart is empty. Please add items before checking out.');
        }

        return view('pages.checkout', [
            'items' => $this->cartService->cart()->items,
            'totals' => $this->cartService->checkoutTotals(),
            'checkout' => session('checkout', []),
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        if ($this->cartService->count() === 0) {
            return redirect()->route('cart')
                ->with('error', 'Your cart is empty. Please add items before checking out.');
        }

        $data = $request->validated();

        if ($request->boolean('billing_same_as_shipping')) {
            $data['billing_same_as_shipping'] = true;
            $data['billing_first_name'] = $data['shipping_first_name'];
            $data['billing_last_name'] = $data['shipping_last_name'];
            $data['billing_address'] = $data['shipping_address'];
            $data['billing_address2'] = $data['shipping_address2'] ?? null;
            $data['billing_city'] = $data['shipping_city'];
            $data['billing_state'] = $data['shipping_state'] ?? null;
            $data['billing_postal_code'] = $data['shipping_postal_code'];
            $data['billing_country'] = $data['shipping_country'];
        } else {
            $data['billing_same_as_shipping'] = false;
        }

        session(['checkout' => $data]);

        return redirect()->route('checkout')
            ->with('success', 'Your information has been saved.');
    }

    public function pay(): RedirectResponse
    {
        $checkoutData = session('checkout');

        if (! $checkoutData) {
            return redirect()->route('checkout')
                ->with('error', 'Please fill out the checkout form first.');
        }

        if ($this->cartService->count() === 0) {
            return redirect()->route('cart')
                ->with('error', 'Your cart is empty.');
        }

        $paymentMethod = $checkoutData['payment_method'] ?? 'stripe';

        try {
            $order = $this->orderService->createFromCheckout(
                $checkoutData,
                $this->cartService->cart()
            );

            if ($paymentMethod === 'stripe') {
                $session = $this->stripeService->createCheckoutSession($order);

                return redirect()->away($session->url);
            }

            // For bank_transfer and store_pickup: order is created with Pending status
            // Clear cart and checkout session immediately
            $this->cartService->clear();
            session()->forget('checkout');

            return redirect()->route('checkout.success', [
                'token' => $order->guest_token,
            ]);
        } catch (\RuntimeException $e) {
            return redirect()->route('checkout')
                ->with('error', $e->getMessage());
        }
    }

    public function success(Request $request): View|RedirectResponse
    {
        $token = $request->query('token');
        $sessionId = $request->query('session_id');

        // Find order by Stripe session ID (Stripe flow) or by guest token (bank/pickup flow)
        if ($sessionId && $token) {
            $order = Order::where('stripe_checkout_session_id', $sessionId)
                ->where('guest_token', $token)
                ->first();
        } elseif ($token) {
            $order = Order::where('guest_token', $token)
                ->whereIn('payment_method', ['bank_transfer', 'store_pickup'])
                ->first();
        } else {
            return redirect()->route('home');
        }

        if (! $order) {
            return redirect()->route('home');
        }

        // Clear cart and checkout session (we have session access here)
        $this->cartService->clear();
        session()->forget('checkout');

        return view('pages.checkout.success', ['order' => $order]);
    }

    public function cancel(Request $request): View
    {
        $sessionId = $request->query('session_id');

        if ($sessionId) {
            $order = Order::where('stripe_checkout_session_id', $sessionId)
                ->where('payment_status', PaymentStatus::Pending)
                ->first();

            if ($order) {
                $this->orderService->releaseReservations($order);
                $order->transitionTo(OrderStatus::Cancelled, 'Customer cancelled checkout');
                $order->update(['payment_status' => PaymentStatus::Failed]);

                Log::info('Checkout cancelled: reservations released', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ]);
            }
        }

        return view('pages.checkout.cancel');
    }
}
