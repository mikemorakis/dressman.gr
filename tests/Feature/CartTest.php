<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;

/**
 * Persist the session cookie so subsequent HTTP requests share the same cart.
 * Call this after the first HTTP request that creates a cart.
 */
function persistSession(object $test): void
{
    $sessionId = app('session')->driver()->getId();
    $cookieName = config('session.cookie', 'laravel_session');
    $test->withCookies([$cookieName => $sessionId]);
}

// ──── CartService ────

it('adds a product to the cart', function () {
    $product = Product::create([
        'name' => 'Cart Widget',
        'slug' => 'cart-widget',
        'price' => 19.99,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $service = app(CartService::class);
    $service->add($product);

    expect($service->count())->toBe(1)
        ->and($service->subtotal())->toBe(19.99);
});

it('merges quantity when adding same product twice', function () {
    $product = Product::create([
        'name' => 'Merge Widget',
        'slug' => 'merge-widget',
        'price' => 10.00,
        'stock' => 20,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $service = app(CartService::class);
    $service->add($product, null, 2);
    $service->add($product, null, 3);

    expect($service->count())->toBe(5)
        ->and($service->subtotal())->toBe(50.00);
});

it('adds a product variant to the cart', function () {
    $product = Product::create([
        'name' => 'Variant Widget',
        'slug' => 'variant-widget',
        'price' => 20.00,
        'has_variants' => true,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $variant = ProductVariant::create([
        'product_id' => $product->id,
        'sku' => 'VW-001',
        'price' => 25.00,
        'stock' => 5,
        'is_active' => true,
        'signature' => ProductVariant::generateSignature([1]),
    ]);

    $service = app(CartService::class);
    $service->add($product, $variant);

    expect($service->count())->toBe(1)
        ->and($service->subtotal())->toBe(25.00);
});

it('updates item quantity', function () {
    $product = Product::create([
        'name' => 'Update Widget',
        'slug' => 'update-widget',
        'price' => 15.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $service = app(CartService::class);
    $item = $service->add($product, null, 1);

    $service->updateQuantity($item, 4);

    expect($service->count())->toBe(4)
        ->and($service->subtotal())->toBe(60.00);
});

it('removes item when quantity set to zero', function () {
    $product = Product::create([
        'name' => 'Remove Widget',
        'slug' => 'remove-widget',
        'price' => 10.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $service = app(CartService::class);
    $item = $service->add($product);

    $service->updateQuantity($item, 0);

    expect($service->count())->toBe(0);
});

it('removes an item from the cart', function () {
    $product = Product::create([
        'name' => 'Delete Widget',
        'slug' => 'delete-widget',
        'price' => 10.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $service = app(CartService::class);
    $item = $service->add($product);

    $service->remove($item);

    expect($service->count())->toBe(0);
});

it('clears all cart items', function () {
    $p1 = Product::create(['name' => 'A', 'slug' => 'a', 'price' => 10, 'stock' => 5, 'is_active' => true, 'published_at' => now()]);
    $p2 = Product::create(['name' => 'B', 'slug' => 'b', 'price' => 20, 'stock' => 5, 'is_active' => true, 'published_at' => now()]);
    $p1->refresh();
    $p2->refresh();

    $service = app(CartService::class);
    $service->add($p1);
    $service->add($p2);

    expect($service->count())->toBe(2);

    $service->clear();

    expect($service->count())->toBe(0);
});

it('calculates shipping with free shipping threshold', function () {
    $product = Product::create([
        'name' => 'Ship Widget',
        'slug' => 'ship-widget',
        'price' => 49.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $service = app(CartService::class);
    $service->add($product);

    // Below threshold (50.00): shipping = 3.50
    expect($service->shipping())->toBe(3.50)
        ->and($service->freeShippingRemaining())->toBe(1.00)
        ->and($service->total())->toBe(52.50);
});

it('gives free shipping when threshold reached', function () {
    $product = Product::create([
        'name' => 'Free Ship Widget',
        'slug' => 'free-ship-widget',
        'price' => 55.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $service = app(CartService::class);
    $service->add($product);

    expect($service->shipping())->toBe(0.00)
        ->and($service->freeShippingRemaining())->toBe(0.00)
        ->and($service->freeShippingProgress())->toBe(100)
        ->and($service->total())->toBe(55.00);
});

it('returns complete totals array', function () {
    $product = Product::create([
        'name' => 'Totals Widget',
        'slug' => 'totals-widget',
        'price' => 30.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $service = app(CartService::class);
    $service->add($product, null, 2);

    $totals = $service->totals();

    expect($totals)
        ->toHaveKeys(['subtotal', 'shipping', 'total', 'count', 'free_shipping_remaining', 'free_shipping_progress'])
        ->and($totals['subtotal'])->toBe(60.00)
        ->and($totals['shipping'])->toBe(0.00)
        ->and($totals['total'])->toBe(60.00)
        ->and($totals['count'])->toBe(2)
        ->and($totals['free_shipping_remaining'])->toBe(0.00)
        ->and($totals['free_shipping_progress'])->toBe(100);
});

// ──── Cart Page (HTTP) ────

it('renders the cart page', function () {
    $response = $this->get('/cart');

    $response->assertOk();
    $response->assertSee('Shopping Cart');
});

it('shows empty cart state', function () {
    $response = $this->get('/cart');

    $response->assertOk();
    $response->assertSee('Your cart is empty');
});

it('shows cart items on cart page', function () {
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
    persistSession($this);

    $response = $this->get('/cart');

    $response->assertOk();
    $response->assertSee('Page Widget');
    $response->assertSee('Order Summary');
});

// ──── Cart Icon ────

it('renders cart icon with count', function () {
    $product = Product::create([
        'name' => 'Icon Widget',
        'slug' => 'icon-widget',
        'price' => 10.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    app(CartService::class)->add($product, null, 3);

    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('aria-live="polite"', false);
});

it('has aria-live on cart icon for accessibility', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('aria-live="polite"', false);
});

// ──── Cart Drawer (HTTP) ────

it('returns drawer HTML fragment with items', function () {
    $product = Product::create([
        'name' => 'Drawer Widget',
        'slug' => 'drawer-widget',
        'price' => 25.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);
    persistSession($this);

    $response = $this->get('/cart/drawer');

    $response->assertOk();
    $response->assertSee('Drawer Widget');
    $response->assertSee('25,00');
});

it('shows free shipping progress in drawer', function () {
    $product = Product::create([
        'name' => 'Progress Widget',
        'slug' => 'progress-widget',
        'price' => 30.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);
    persistSession($this);

    $response = $this->get('/cart/drawer');

    $response->assertOk();
    $response->assertSee('free shipping');
});

// ──── Add to Cart (POST) ────

it('adds product to cart via POST endpoint', function () {
    $product = Product::create([
        'name' => 'ATC Widget',
        'slug' => 'atc-widget',
        'price' => 12.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $response = $this->postJson('/cart/add', [
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $response->assertOk();
    $response->assertJsonFragment(['count' => 1]);
    $response->assertJsonStructure(['count', 'drawer_html']);

    expect(app(CartService::class)->count())->toBe(1);
});

it('rejects adding variant product without variant_id', function () {
    $product = Product::create([
        'name' => 'Var Required Widget',
        'slug' => 'var-required',
        'price' => 20.00,
        'has_variants' => true,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $response = $this->postJson('/cart/add', [
        'product_id' => $product->id,
    ]);

    $response->assertStatus(422);
});

// ──── Update Quantity (PATCH) ────

it('updates cart item quantity via PATCH endpoint', function () {
    $product = Product::create([
        'name' => 'Patch Widget',
        'slug' => 'patch-widget',
        'price' => 10.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);
    persistSession($this);
    $item = CartItem::where('product_id', $product->id)->firstOrFail();

    $response = $this->patch("/cart/{$item->id}", [
        'quantity' => 5,
    ], ['Accept' => 'application/json']);

    $response->assertOk();
    $response->assertJsonFragment(['count' => 5]);
});

it('redirects when updating quantity via form', function () {
    $product = Product::create([
        'name' => 'Form Widget',
        'slug' => 'form-widget',
        'price' => 10.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);
    persistSession($this);
    $item = CartItem::where('product_id', $product->id)->firstOrFail();

    $response = $this->patch("/cart/{$item->id}", [
        'quantity' => 3,
    ]);

    $response->assertRedirect(route('cart'));
});

// ──── Remove Item (DELETE) ────

it('removes cart item via DELETE endpoint', function () {
    $product = Product::create([
        'name' => 'Del Widget',
        'slug' => 'del-widget',
        'price' => 10.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);
    persistSession($this);
    $item = CartItem::where('product_id', $product->id)->firstOrFail();

    $response = $this->delete("/cart/{$item->id}", [], ['Accept' => 'application/json']);

    $response->assertOk();
    $response->assertJsonFragment(['count' => 0]);
});

// ──── Clear Cart (POST) ────

it('clears cart via POST endpoint', function () {
    $product = Product::create([
        'name' => 'Clear Widget',
        'slug' => 'clear-widget',
        'price' => 10.00,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 1]);
    persistSession($this);

    $response = $this->postJson('/cart/clear');

    $response->assertOk();
    $response->assertJsonFragment(['count' => 0]);
});
