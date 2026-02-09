<?php

use App\Models\Product;
use App\Services\CartService;

/**
 * Persist the session cookie so subsequent HTTP requests share the same cart.
 * Call this after the first HTTP request that creates a cart.
 */
function persistCheckoutSession(object $test): void
{
    $sessionId = app('session')->driver()->getId();
    $cookieName = config('session.cookie', 'laravel_session');
    $test->withCookies([$cookieName => $sessionId]);
}

// ──── CartService::checkoutTotals() ────

it('returns checkout totals with VAT breakdown', function () {
    $product = Product::create([
        'name' => 'Checkout Widget',
        'slug' => 'checkout-widget',
        'price' => 24.80,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $service = app(CartService::class);
    $service->add($product, null, 2);

    $totals = $service->checkoutTotals();

    expect($totals)
        ->toHaveKeys([
            'subtotal', 'net', 'vat_rate', 'vat_amount', 'shipping',
            'total', 'count', 'prices_include_vat',
            'free_shipping_remaining', 'free_shipping_progress',
        ])
        ->and($totals['subtotal'])->toBe(49.60)
        ->and($totals['vat_rate'])->toBe(24.00)
        ->and($totals['prices_include_vat'])->toBeTrue()
        ->and($totals['count'])->toBe(2)
        ->and($totals['vat_amount'])->toBeGreaterThan(0.0);
});

it('calculates correct total when prices include VAT', function () {
    $product = Product::create([
        'name' => 'VAT Widget',
        'slug' => 'vat-widget',
        'price' => 50.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $service = app(CartService::class);
    $service->add($product);

    $totals = $service->checkoutTotals();

    // 50.00 (gross, includes VAT) + 0.00 (free shipping, at threshold)
    expect($totals['total'])->toBe(50.00)
        ->and($totals['shipping'])->toBe(0.00);
});

// ──── GET /checkout ────

it('redirects to cart when cart is empty', function () {
    $response = $this->get('/checkout');

    $response->assertRedirect(route('cart'));
    $response->assertSessionHas('error');
});

it('renders the checkout page with items', function () {
    $product = Product::create([
        'name' => 'Page Widget',
        'slug' => 'page-widget',
        'price' => 15.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);
    persistCheckoutSession($this);

    $response = $this->get('/checkout');

    $response->assertOk();
    $response->assertSee('Checkout');
    $response->assertSee('Contact Information');
    $response->assertSee('Shipping Address');
    $response->assertSee('Billing Address');
    $response->assertSee('Order Summary');
    $response->assertSee('Page Widget');
});

it('shows VAT breakdown in order summary', function () {
    $product = Product::create([
        'name' => 'VAT Display Widget',
        'slug' => 'vat-display-widget',
        'price' => 24.80,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);
    persistCheckoutSession($this);

    $response = $this->get('/checkout');

    $response->assertOk();
    $response->assertSee('VAT');
    $response->assertSee('24%');
});

it('has proper form labels for accessibility', function () {
    $product = Product::create([
        'name' => 'A11y Widget',
        'slug' => 'a11y-widget',
        'price' => 10.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);
    persistCheckoutSession($this);

    $response = $this->get('/checkout');

    $response->assertOk();
    $response->assertSee('for="email"', false);
    $response->assertSee('for="shipping_first_name"', false);
    $response->assertSee('for="shipping_last_name"', false);
    $response->assertSee('autocomplete="email"', false);
    $response->assertSee('autocomplete="shipping given-name"', false);
});

// ──── POST /checkout ────

it('validates required fields', function () {
    $product = Product::create([
        'name' => 'Validate Widget',
        'slug' => 'validate-widget',
        'price' => 10.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);
    persistCheckoutSession($this);

    $response = $this->post('/checkout', []);

    $response->assertSessionHasErrors([
        'email',
        'shipping_first_name',
        'shipping_last_name',
        'shipping_address',
        'shipping_city',
        'shipping_postal_code',
        'shipping_country',
    ]);
});

it('validates email format', function () {
    $product = Product::create([
        'name' => 'Email Widget',
        'slug' => 'email-widget',
        'price' => 10.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);
    persistCheckoutSession($this);

    $response = $this->post('/checkout', [
        'email' => 'not-an-email',
        'shipping_first_name' => 'John',
        'shipping_last_name' => 'Doe',
        'shipping_address' => '123 Main St',
        'shipping_city' => 'Athens',
        'shipping_postal_code' => '10431',
        'shipping_country' => 'GR',
        'billing_same_as_shipping' => '1',
    ]);

    $response->assertSessionHasErrors(['email']);
});

it('stores checkout data in session on valid submission', function () {
    $product = Product::create([
        'name' => 'Store Widget',
        'slug' => 'store-widget',
        'price' => 10.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);
    persistCheckoutSession($this);

    $response = $this->post('/checkout', [
        'email' => 'test@example.com',
        'shipping_first_name' => 'John',
        'shipping_last_name' => 'Doe',
        'shipping_address' => '123 Main St',
        'shipping_city' => 'Athens',
        'shipping_postal_code' => '10431',
        'shipping_country' => 'GR',
        'billing_same_as_shipping' => '1',
    ]);

    $response->assertRedirect(route('checkout'));
    $response->assertSessionHas('success');
    $response->assertSessionHas('checkout', function (array $data) {
        return $data['email'] === 'test@example.com'
            && $data['shipping_first_name'] === 'John'
            && $data['shipping_last_name'] === 'Doe'
            && $data['billing_same_as_shipping'] === true
            && $data['billing_first_name'] === 'John';
    });
});

it('requires billing fields when billing differs from shipping', function () {
    $product = Product::create([
        'name' => 'Billing Widget',
        'slug' => 'billing-widget',
        'price' => 10.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);
    persistCheckoutSession($this);

    $response = $this->post('/checkout', [
        'email' => 'test@example.com',
        'shipping_first_name' => 'John',
        'shipping_last_name' => 'Doe',
        'shipping_address' => '123 Main St',
        'shipping_city' => 'Athens',
        'shipping_postal_code' => '10431',
        'shipping_country' => 'GR',
        // billing_same_as_shipping NOT sent (checkbox unchecked)
    ]);

    $response->assertSessionHasErrors([
        'billing_first_name',
        'billing_last_name',
        'billing_address',
        'billing_city',
        'billing_postal_code',
        'billing_country',
    ]);
});

it('stores separate billing address when provided', function () {
    $product = Product::create([
        'name' => 'Separate Widget',
        'slug' => 'separate-widget',
        'price' => 10.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);
    persistCheckoutSession($this);

    $response = $this->post('/checkout', [
        'email' => 'test@example.com',
        'shipping_first_name' => 'John',
        'shipping_last_name' => 'Doe',
        'shipping_address' => '123 Main St',
        'shipping_city' => 'Athens',
        'shipping_postal_code' => '10431',
        'shipping_country' => 'GR',
        'billing_first_name' => 'Jane',
        'billing_last_name' => 'Doe',
        'billing_address' => '456 Other St',
        'billing_city' => 'Thessaloniki',
        'billing_postal_code' => '54621',
        'billing_country' => 'GR',
    ]);

    $response->assertRedirect(route('checkout'));
    $response->assertSessionHas('checkout', function (array $data) {
        return $data['billing_same_as_shipping'] === false
            && $data['billing_first_name'] === 'Jane'
            && $data['billing_city'] === 'Thessaloniki';
    });
});

it('redirects POST to cart when cart is empty', function () {
    $response = $this->post('/checkout', [
        'email' => 'test@example.com',
        'shipping_first_name' => 'John',
        'shipping_last_name' => 'Doe',
        'shipping_address' => '123 Main St',
        'shipping_city' => 'Athens',
        'shipping_postal_code' => '10431',
        'shipping_country' => 'GR',
        'billing_same_as_shipping' => '1',
    ]);

    $response->assertRedirect(route('cart'));
    $response->assertSessionHas('error');
});

it('rejects notes exceeding 500 characters', function () {
    $product = Product::create([
        'name' => 'Notes Widget',
        'slug' => 'notes-widget',
        'price' => 10.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);
    persistCheckoutSession($this);

    $response = $this->post('/checkout', [
        'email' => 'test@example.com',
        'shipping_first_name' => 'John',
        'shipping_last_name' => 'Doe',
        'shipping_address' => '123 Main St',
        'shipping_city' => 'Athens',
        'shipping_postal_code' => '10431',
        'shipping_country' => 'GR',
        'billing_same_as_shipping' => '1',
        'notes' => str_repeat('A', 501),
    ]);

    $response->assertSessionHasErrors(['notes']);
});

// ──── Pre-fill + Cart Link ────

it('pre-fills form from saved session data', function () {
    $product = Product::create([
        'name' => 'Prefill Widget',
        'slug' => 'prefill-widget',
        'price' => 10.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);
    persistCheckoutSession($this);

    $this->post('/checkout', [
        'email' => 'prefill@example.com',
        'shipping_first_name' => 'John',
        'shipping_last_name' => 'Doe',
        'shipping_address' => '123 Main St',
        'shipping_city' => 'Athens',
        'shipping_postal_code' => '10431',
        'shipping_country' => 'GR',
        'billing_same_as_shipping' => '1',
    ]);

    $response = $this->get('/checkout');

    $response->assertOk();
    $response->assertSee('prefill@example.com');
    $response->assertSee('John');
    $response->assertSee('123 Main St');
});

it('shows proceed to checkout link on cart page', function () {
    $product = Product::create([
        'name' => 'Link Widget',
        'slug' => 'link-widget',
        'price' => 10.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);
    persistCheckoutSession($this);

    $response = $this->get('/cart');

    $response->assertOk();
    $response->assertSee(route('checkout'));
    $response->assertSee('Proceed to Checkout');
});
