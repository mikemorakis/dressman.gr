<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\PendingEmail;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
    ) {}

    public function handleWebhook(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe webhook signature verification failed', [
                'error' => $e->getMessage(),
            ]);

            return response('Invalid signature', 400);
        }

        /** @var \Stripe\StripeObject $sessionObject */
        $sessionObject = $event->data['object'];

        match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutSessionCompleted($sessionObject),
            'checkout.session.expired' => $this->handleCheckoutSessionExpired($sessionObject),
            default => null,
        };

        return response('Webhook handled', 200);
    }

    /**
     * Handle successful payment. Idempotent: skips if already paid.
     * Verifies amount_total + currency match before marking paid.
     */
    private function handleCheckoutSessionCompleted(object $session): void
    {
        $order = Order::where('stripe_checkout_session_id', $session->id)->first();

        if (! $order) {
            Log::warning('Stripe webhook: order not found for session', [
                'session_id' => $session->id,
            ]);

            return;
        }

        // Idempotency guard (Rule 4)
        if ($order->payment_status === PaymentStatus::Paid) {
            Log::info('Stripe webhook: order already paid, skipping', [
                'order_id' => $order->id,
            ]);

            return;
        }

        // Verify amount and currency match order snapshot
        $expectedCents = (int) round((float) $order->total * 100);
        $sessionCents = (int) $session->amount_total;
        $sessionCurrency = strtoupper((string) $session->currency);

        if ($sessionCents !== $expectedCents || $sessionCurrency !== $order->currency) {
            Log::critical('Stripe webhook: amount/currency mismatch', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'expected_amount_cents' => $expectedCents,
                'session_amount_cents' => $sessionCents,
                'expected_currency' => $order->currency,
                'session_currency' => $sessionCurrency,
            ]);

            return;
        }

        $order->markAsPaid($session->payment_intent);
        $this->orderService->decrementStock($order);

        $this->enqueueOrderConfirmationEmail($order);

        Log::info('Stripe webhook: order marked as paid', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
        ]);
    }

    /**
     * Handle expired checkout session — release stock reservations.
     */
    private function handleCheckoutSessionExpired(object $session): void
    {
        $order = Order::where('stripe_checkout_session_id', $session->id)->first();

        if (! $order) {
            Log::info('Stripe webhook: order not found for expired session', [
                'session_id' => $session->id,
            ]);

            return;
        }

        $this->orderService->releaseReservations($order);
        $order->transitionTo(OrderStatus::Cancelled, 'Stripe checkout session expired');
        $order->update(['payment_status' => PaymentStatus::Failed]);

        Log::info('Stripe webhook: order cancelled due to expired session', [
            'order_id' => $order->id,
        ]);
    }

    /**
     * Enqueue order confirmation email for cron delivery.
     * Idempotent via confirmation_sent_at + duplicate PendingEmail check.
     */
    private function enqueueOrderConfirmationEmail(Order $order): void
    {
        if ($order->confirmation_sent_at) {
            return;
        }

        $exists = PendingEmail::where('mailable_class', OrderConfirmationMail::class)
            ->where('mailable_data->order_id', $order->id)
            ->exists();

        if ($exists) {
            return;
        }

        PendingEmail::create([
            'mailable_class' => OrderConfirmationMail::class,
            'mailable_data' => ['order_id' => $order->id],
            'to_email' => $order->email,
        ]);
    }
}
