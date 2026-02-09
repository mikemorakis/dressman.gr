<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Product;

function createTestOrder(array $overrides = []): Order
{
    return Order::create(array_merge([
        'order_number' => Order::generateOrderNumber(),
        'status' => OrderStatus::Paid,
        'payment_status' => PaymentStatus::Paid,
        'email' => 'guest@example.com',
        'billing_address' => ['first_name' => 'Test', 'last_name' => 'User', 'address' => '1 St', 'city' => 'Athens', 'postal_code' => '10431', 'country' => 'GR'],
        'shipping_address' => ['first_name' => 'Test', 'last_name' => 'User', 'address' => '1 St', 'city' => 'Athens', 'postal_code' => '10431', 'country' => 'GR'],
        'subtotal' => 50.00,
        'vat_rate' => 24.00,
        'vat_amount' => 9.68,
        'shipping_amount' => 0.00,
        'total' => 50.00,
        'currency' => 'EUR',
        'prices_include_vat' => true,
    ], $overrides));
}

it('displays order status page with valid token', function () {
    $order = createTestOrder();

    $response = $this->get(route('order.guest.show', [
        'orderNumber' => $order->order_number,
        'token' => $order->guest_token,
    ]));

    $response->assertOk();
    $response->assertSee($order->order_number);
    $response->assertSee('Order');
});

it('returns 404 with invalid token', function () {
    $order = createTestOrder();

    $response = $this->get(route('order.guest.show', [
        'orderNumber' => $order->order_number,
        'token' => 'invalid_token_here',
    ]));

    $response->assertNotFound();
});

it('returns 404 without token', function () {
    $order = createTestOrder();

    $response = $this->get('/order/'.$order->order_number);

    $response->assertNotFound();
});

it('returns 404 for non-existent order', function () {
    $response = $this->get(route('order.guest.show', [
        'orderNumber' => 'PE-99999999-XXXX',
        'token' => bin2hex(random_bytes(32)),
    ]));

    $response->assertNotFound();
});

it('shows order items on status page', function () {
    $product = Product::create([
        'name' => 'Status Widget',
        'slug' => 'status-widget',
        'price' => 25.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $order = createTestOrder();

    $order->items()->create([
        'product_id' => $product->id,
        'product_name' => 'Status Widget',
        'quantity' => 2,
        'unit_price' => 25.00,
        'line_total' => 50.00,
    ]);

    $response = $this->get(route('order.guest.show', [
        'orderNumber' => $order->order_number,
        'token' => $order->guest_token,
    ]));

    $response->assertOk();
    $response->assertSee('Status Widget');
    $response->assertSee('50,00');
});

it('shows status timeline on status page', function () {
    $order = createTestOrder();

    $order->statusHistory()->create([
        'from_status' => null,
        'to_status' => 'pending',
        'notes' => 'Order created',
    ]);
    $order->statusHistory()->create([
        'from_status' => 'pending',
        'to_status' => 'paid',
        'notes' => 'Payment confirmed',
    ]);

    $response = $this->get(route('order.guest.show', [
        'orderNumber' => $order->order_number,
        'token' => $order->guest_token,
    ]));

    $response->assertOk();
    $response->assertSee('Status Timeline');
    $response->assertSee('Order created');
    $response->assertSee('Payment confirmed');
});

it('masks email on status page', function () {
    $order = createTestOrder(['email' => 'test@example.com']);

    $response = $this->get(route('order.guest.show', [
        'orderNumber' => $order->order_number,
        'token' => $order->guest_token,
    ]));

    $response->assertOk();
    $response->assertSee('t***@example.com');
    $response->assertDontSee('test@example.com');
});
