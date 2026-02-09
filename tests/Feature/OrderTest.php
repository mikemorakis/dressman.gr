<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\PendingEmail;
use App\Models\Product;
use App\Models\StockReservation;
use App\Services\CartService;
use App\Services\OrderService;

function persistOrderSession(object $test): void
{
    $sessionId = app('session')->driver()->getId();
    $cookieName = config('session.cookie', 'laravel_session');
    $test->withCookies([$cookieName => $sessionId]);
}

function validCheckoutData(): array
{
    return [
        'email' => 'order@example.com',
        'shipping_first_name' => 'John',
        'shipping_last_name' => 'Doe',
        'shipping_address' => '123 Main St',
        'shipping_address2' => null,
        'shipping_city' => 'Athens',
        'shipping_state' => null,
        'shipping_postal_code' => '10431',
        'shipping_country' => 'GR',
        'shipping_phone' => null,
        'billing_same_as_shipping' => true,
        'billing_first_name' => 'John',
        'billing_last_name' => 'Doe',
        'billing_address' => '123 Main St',
        'billing_address2' => null,
        'billing_city' => 'Athens',
        'billing_state' => null,
        'billing_postal_code' => '10431',
        'billing_country' => 'GR',
        'notes' => null,
    ];
}

// ──── Order Creation ────

it('creates order from checkout with correct money snapshot', function () {
    $product = Product::create([
        'name' => 'Order Widget',
        'slug' => 'order-widget',
        'price' => 24.80,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $cartService = app(CartService::class);
    $cartService->add($product, null, 2);

    $orderService = app(OrderService::class);
    $order = $orderService->createFromCheckout(validCheckoutData(), $cartService->cart());

    expect($order)->toBeInstanceOf(Order::class)
        ->and($order->status)->toBe(OrderStatus::Pending)
        ->and($order->payment_status)->toBe(PaymentStatus::Pending)
        ->and($order->email)->toBe('order@example.com')
        ->and((float) $order->subtotal)->toBe(49.60)
        ->and((float) $order->vat_rate)->toBe(24.00)
        ->and((float) $order->vat_amount)->toBeGreaterThan(0.0)
        ->and((float) $order->total)->toBeGreaterThan(0.0)
        ->and($order->currency)->toBe('EUR')
        ->and($order->prices_include_vat)->toBeTrue()
        ->and($order->items)->toHaveCount(1)
        ->and($order->items->first()->quantity)->toBe(2)
        ->and($order->items->first()->product_name)->toBe('Order Widget')
        ->and((float) $order->items->first()->unit_price)->toBe(24.80)
        ->and($order->shipping_address['first_name'])->toBe('John')
        ->and($order->billing_address['city'])->toBe('Athens');
});

it('generates order number in correct format', function () {
    $number = Order::generateOrderNumber();

    $prefix = config('shop.order_prefix', 'PE');
    expect($number)->toMatch('/^' . $prefix . '-\d{8}-[A-F0-9]{4}$/');
});

// ──── Stock Reservations ────

it('reserves stock when order is created', function () {
    $product = Product::create([
        'name' => 'Reserve Widget',
        'slug' => 'reserve-widget',
        'price' => 20.00,
        'stock' => 10,
        'reserved_stock' => 0,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $cartService = app(CartService::class);
    $cartService->add($product, null, 3);

    $orderService = app(OrderService::class);
    $order = $orderService->createFromCheckout(validCheckoutData(), $cartService->cart());

    $product->refresh();

    expect($product->reserved_stock)->toBe(3)
        ->and($product->available_stock)->toBe(7)
        ->and($order->stockReservations)->toHaveCount(1)
        ->and($order->stockReservations->first()->quantity)->toBe(3)
        ->and($order->stockReservations->first()->expires_at)->not->toBeNull();
});

it('throws exception when stock is insufficient', function () {
    $product = Product::create([
        'name' => 'Low Stock Widget',
        'slug' => 'low-stock-widget',
        'price' => 10.00,
        'stock' => 2,
        'reserved_stock' => 0,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $cartService = app(CartService::class);
    $cartService->add($product, null, 5);

    $orderService = app(OrderService::class);

    expect(fn () => $orderService->createFromCheckout(validCheckoutData(), $cartService->cart()))
        ->toThrow(\RuntimeException::class, 'Insufficient stock');
});

it('releases expired reservations', function () {
    $product = Product::create([
        'name' => 'Expire Widget',
        'slug' => 'expire-widget',
        'price' => 15.00,
        'stock' => 10,
        'reserved_stock' => 5,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $order = Order::create([
        'order_number' => Order::generateOrderNumber(),
        'status' => OrderStatus::Pending,
        'payment_status' => PaymentStatus::Pending,
        'email' => 'expire@example.com',
        'billing_address' => ['first_name' => 'Test', 'last_name' => 'User', 'address' => '1 St', 'city' => 'Athens', 'postal_code' => '10431', 'country' => 'GR'],
        'shipping_address' => ['first_name' => 'Test', 'last_name' => 'User', 'address' => '1 St', 'city' => 'Athens', 'postal_code' => '10431', 'country' => 'GR'],
        'subtotal' => 15.00,
        'vat_rate' => 24.00,
        'vat_amount' => 2.90,
        'shipping_amount' => 3.50,
        'total' => 18.50,
        'currency' => 'EUR',
        'prices_include_vat' => true,
    ]);

    StockReservation::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 5,
        'expires_at' => now()->subMinutes(10),
    ]);

    $orderService = app(OrderService::class);
    $count = $orderService->releaseAllExpired();

    $product->refresh();
    $order->refresh();

    expect($count)->toBe(1)
        ->and($product->reserved_stock)->toBe(0)
        ->and($product->available_stock)->toBe(10)
        ->and(StockReservation::count())->toBe(0)
        ->and($order->status)->toBe(OrderStatus::Cancelled)
        ->and($order->payment_status)->toBe(PaymentStatus::Failed);
});

// ──── Stock Decrement on Payment ────

it('decrements stock on payment confirmation', function () {
    $product = Product::create([
        'name' => 'Decrement Widget',
        'slug' => 'decrement-widget',
        'price' => 25.00,
        'stock' => 10,
        'reserved_stock' => 2,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $order = Order::create([
        'order_number' => Order::generateOrderNumber(),
        'status' => OrderStatus::Pending,
        'payment_status' => PaymentStatus::Pending,
        'email' => 'decrement@example.com',
        'billing_address' => ['first_name' => 'Test', 'last_name' => 'User', 'address' => '1 St', 'city' => 'Athens', 'postal_code' => '10431', 'country' => 'GR'],
        'shipping_address' => ['first_name' => 'Test', 'last_name' => 'User', 'address' => '1 St', 'city' => 'Athens', 'postal_code' => '10431', 'country' => 'GR'],
        'subtotal' => 50.00,
        'vat_rate' => 24.00,
        'vat_amount' => 9.68,
        'shipping_amount' => 0.00,
        'total' => 50.00,
        'currency' => 'EUR',
        'prices_include_vat' => true,
    ]);

    $order->items()->create([
        'product_id' => $product->id,
        'product_name' => $product->name,
        'quantity' => 2,
        'unit_price' => 25.00,
        'line_total' => 50.00,
    ]);

    StockReservation::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'expires_at' => now()->addMinutes(30),
    ]);

    $orderService = app(OrderService::class);
    $orderService->decrementStock($order);

    $product->refresh();

    expect($product->stock)->toBe(8)
        ->and($product->reserved_stock)->toBe(0)
        ->and(StockReservation::count())->toBe(0);
});

// ──── Idempotency ────

it('marks order as paid only once (idempotent)', function () {
    $order = Order::create([
        'order_number' => Order::generateOrderNumber(),
        'status' => OrderStatus::Pending,
        'payment_status' => PaymentStatus::Pending,
        'email' => 'idempotent@example.com',
        'stripe_checkout_session_id' => 'cs_test_idempotent',
        'billing_address' => ['first_name' => 'Test', 'last_name' => 'User', 'address' => '1 St', 'city' => 'Athens', 'postal_code' => '10431', 'country' => 'GR'],
        'shipping_address' => ['first_name' => 'Test', 'last_name' => 'User', 'address' => '1 St', 'city' => 'Athens', 'postal_code' => '10431', 'country' => 'GR'],
        'subtotal' => 50.00,
        'vat_rate' => 24.00,
        'vat_amount' => 9.68,
        'shipping_amount' => 0.00,
        'total' => 50.00,
        'currency' => 'EUR',
        'prices_include_vat' => true,
    ]);

    $order->markAsPaid('pi_test_123');
    $order->refresh();

    expect($order->payment_status)->toBe(PaymentStatus::Paid)
        ->and($order->status)->toBe(OrderStatus::Paid)
        ->and($order->paid_at)->not->toBeNull()
        ->and($order->stripe_payment_intent_id)->toBe('pi_test_123')
        ->and($order->statusHistory)->toHaveCount(1); // paid transition only (no initial history from direct create)

    // Second call: no-op
    $order->markAsPaid('pi_test_123');
    $order->refresh();

    expect($order->statusHistory)->toHaveCount(1); // still 1, not 2
});

// ──── Webhook ────

it('webhook returns 400 on invalid signature', function () {
    $response = $this->post('/stripe/webhook', [], [
        'HTTP_STRIPE_SIGNATURE' => 'invalid_sig',
    ]);

    $response->assertStatus(400);
});

// ──── HTTP Routes ────

it('POST /checkout/pay redirects to checkout when no checkout data', function () {
    $product = Product::create([
        'name' => 'Pay Widget',
        'slug' => 'pay-widget',
        'price' => 10.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);
    persistOrderSession($this);

    $response = $this->post('/checkout/pay');

    $response->assertRedirect(route('checkout'));
    $response->assertSessionHas('error');
});

it('POST /checkout/pay redirects to cart when cart is empty', function () {
    session(['checkout' => validCheckoutData()]);

    $response = $this->post('/checkout/pay');

    $response->assertRedirect(route('cart'));
    $response->assertSessionHas('error');
});

it('GET /checkout/success shows order details', function () {
    $order = Order::create([
        'order_number' => 'PE-20260206-TEST',
        'status' => OrderStatus::Paid,
        'payment_status' => PaymentStatus::Paid,
        'email' => 'success@example.com',
        'stripe_checkout_session_id' => 'cs_test_success',
        'billing_address' => ['first_name' => 'Test', 'last_name' => 'User', 'address' => '1 St', 'city' => 'Athens', 'postal_code' => '10431', 'country' => 'GR'],
        'shipping_address' => ['first_name' => 'Test', 'last_name' => 'User', 'address' => '1 St', 'city' => 'Athens', 'postal_code' => '10431', 'country' => 'GR'],
        'subtotal' => 50.00,
        'vat_rate' => 24.00,
        'vat_amount' => 9.68,
        'shipping_amount' => 0.00,
        'total' => 50.00,
        'currency' => 'EUR',
        'prices_include_vat' => true,
    ]);

    $response = $this->get("/checkout/success?session_id=cs_test_success&token={$order->guest_token}");

    $response->assertOk();
    $response->assertSee('PE-20260206-TEST');
    $response->assertSee('Order Confirmed');
    $response->assertSee('success@example.com');
});

it('GET /checkout/success rejects invalid or missing token', function () {
    Order::create([
        'order_number' => 'PE-20260206-SEC',
        'status' => OrderStatus::Paid,
        'payment_status' => PaymentStatus::Paid,
        'email' => 'secure@example.com',
        'stripe_checkout_session_id' => 'cs_test_secure',
        'billing_address' => ['first_name' => 'Test', 'last_name' => 'User', 'address' => '1 St', 'city' => 'Athens', 'postal_code' => '10431', 'country' => 'GR'],
        'shipping_address' => ['first_name' => 'Test', 'last_name' => 'User', 'address' => '1 St', 'city' => 'Athens', 'postal_code' => '10431', 'country' => 'GR'],
        'subtotal' => 50.00,
        'vat_rate' => 24.00,
        'vat_amount' => 9.68,
        'shipping_amount' => 0.00,
        'total' => 50.00,
        'currency' => 'EUR',
        'prices_include_vat' => true,
    ]);

    // Invalid token
    $this->get('/checkout/success?session_id=cs_test_secure&token=invalid')
        ->assertRedirect(route('home'));

    // Missing token
    $this->get('/checkout/success?session_id=cs_test_secure')
        ->assertRedirect(route('home'));
});

it('GET /checkout/cancel shows cancellation message', function () {
    $response = $this->get('/checkout/cancel');

    $response->assertOk();
    $response->assertSee('Payment Cancelled');
    $response->assertSee('Return to Checkout');
});

it('GET /checkout/cancel with session_id releases reservations', function () {
    $product = Product::create([
        'name' => 'Cancel Widget',
        'slug' => 'cancel-widget',
        'price' => 20.00,
        'stock' => 10,
        'reserved_stock' => 2,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $order = Order::create([
        'order_number' => Order::generateOrderNumber(),
        'status' => OrderStatus::Pending,
        'payment_status' => PaymentStatus::Pending,
        'email' => 'cancel@example.com',
        'stripe_checkout_session_id' => 'cs_test_cancel_release',
        'billing_address' => ['first_name' => 'Test', 'last_name' => 'User', 'address' => '1 St', 'city' => 'Athens', 'postal_code' => '10431', 'country' => 'GR'],
        'shipping_address' => ['first_name' => 'Test', 'last_name' => 'User', 'address' => '1 St', 'city' => 'Athens', 'postal_code' => '10431', 'country' => 'GR'],
        'subtotal' => 40.00,
        'vat_rate' => 24.00,
        'vat_amount' => 7.74,
        'shipping_amount' => 3.50,
        'total' => 43.50,
        'currency' => 'EUR',
        'prices_include_vat' => true,
    ]);

    StockReservation::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'expires_at' => now()->addMinutes(30),
    ]);

    $response = $this->get('/checkout/cancel?session_id=cs_test_cancel_release');

    $response->assertOk();
    $response->assertSee('Payment Cancelled');

    $product->refresh();
    $order->refresh();

    expect($product->reserved_stock)->toBe(0)
        ->and(StockReservation::count())->toBe(0)
        ->and($order->status)->toBe(OrderStatus::Cancelled)
        ->and($order->payment_status)->toBe(PaymentStatus::Failed);
});

// ──── Artisan Command ────

it('releases expired reservations via artisan command', function () {
    $product = Product::create([
        'name' => 'Cmd Widget',
        'slug' => 'cmd-widget',
        'price' => 20.00,
        'stock' => 10,
        'reserved_stock' => 3,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $order = Order::create([
        'order_number' => Order::generateOrderNumber(),
        'status' => OrderStatus::Pending,
        'payment_status' => PaymentStatus::Pending,
        'email' => 'cmd@example.com',
        'billing_address' => ['first_name' => 'Test', 'last_name' => 'User', 'address' => '1 St', 'city' => 'Athens', 'postal_code' => '10431', 'country' => 'GR'],
        'shipping_address' => ['first_name' => 'Test', 'last_name' => 'User', 'address' => '1 St', 'city' => 'Athens', 'postal_code' => '10431', 'country' => 'GR'],
        'subtotal' => 20.00,
        'vat_rate' => 24.00,
        'vat_amount' => 3.87,
        'shipping_amount' => 3.50,
        'total' => 23.50,
        'currency' => 'EUR',
        'prices_include_vat' => true,
    ]);

    StockReservation::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 3,
        'expires_at' => now()->subMinutes(5),
    ]);

    $this->artisan('reservations:release-expired')
        ->expectsOutput('Released 1 expired reservation(s).')
        ->assertExitCode(0);

    $product->refresh();

    expect($product->reserved_stock)->toBe(0)
        ->and(StockReservation::count())->toBe(0);
});

// ──── Hardening ────

it('auto-generates guest_token on order creation', function () {
    $order = Order::create([
        'order_number' => Order::generateOrderNumber(),
        'status' => OrderStatus::Pending,
        'payment_status' => PaymentStatus::Pending,
        'email' => 'token@example.com',
        'billing_address' => ['first_name' => 'Test', 'last_name' => 'User', 'address' => '1 St', 'city' => 'Athens', 'postal_code' => '10431', 'country' => 'GR'],
        'shipping_address' => ['first_name' => 'Test', 'last_name' => 'User', 'address' => '1 St', 'city' => 'Athens', 'postal_code' => '10431', 'country' => 'GR'],
        'subtotal' => 10.00,
        'vat_rate' => 24.00,
        'vat_amount' => 1.94,
        'shipping_amount' => 0.00,
        'total' => 10.00,
        'currency' => 'EUR',
        'prices_include_vat' => true,
    ]);

    expect($order->guest_token)->not->toBeNull()
        ->and(strlen($order->guest_token))->toBe(64);
});

it('PendingEmail stops retrying after 5 attempts', function () {
    PendingEmail::create([
        'mailable_class' => 'App\\Mail\\OrderConfirmationMail',
        'mailable_data' => ['order_id' => 1],
        'to_email' => 'maxed@example.com',
        'attempts' => 5,
    ]);

    expect(PendingEmail::unsent()->count())->toBe(0);
});

it('PendingEmail respects backoff via next_attempt_at', function () {
    PendingEmail::create([
        'mailable_class' => 'App\\Mail\\OrderConfirmationMail',
        'mailable_data' => ['order_id' => 1],
        'to_email' => 'backoff@example.com',
        'attempts' => 1,
        'next_attempt_at' => now()->addMinutes(2),
    ]);

    // Should NOT be picked up yet
    expect(PendingEmail::unsent()->count())->toBe(0);

    // Travel forward past the backoff
    $this->travel(3)->minutes();

    expect(PendingEmail::unsent()->count())->toBe(1);
});
