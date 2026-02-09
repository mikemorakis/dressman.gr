<?php

use App\Models\Product;
use App\Models\Setting;
use App\Models\ShippingMethod;
use App\Models\ShippingRate;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Support\Str;

function createShippingMethod(string $code = 'standard', string $name = 'Standard Shipping', bool $isActive = true): ShippingMethod
{
    return ShippingMethod::create([
        'code' => $code,
        'name' => $name,
        'is_active' => $isActive,
        'sort_order' => 0,
    ]);
}

function createShippingRate(
    ShippingMethod $method,
    string $country = 'GR',
    float $flatAmount = 3.50,
    bool $isActive = true,
    ?float $minSubtotal = null,
    ?float $maxSubtotal = null,
): ShippingRate {
    return ShippingRate::create([
        'shipping_method_id' => $method->id,
        'country_code' => $country,
        'flat_amount' => $flatAmount,
        'is_active' => $isActive,
        'min_subtotal' => $minSubtotal,
        'max_subtotal' => $maxSubtotal,
    ]);
}

function createShippingProduct(string $name, float $price, int $stock = 10): Product
{
    $product = Product::create([
        'name' => $name,
        'slug' => Str::slug($name).'-'.Str::random(4),
        'price' => $price,
        'stock' => $stock,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    return $product;
}

// ──── Shipping Amount: Under Threshold ────

it('charges shipping when subtotal is below free shipping threshold', function () {
    Setting::set('free_shipping_threshold', '50.00');
    Setting::set('default_shipping_method_code', 'standard');

    $method = createShippingMethod();
    createShippingRate($method, 'GR', 3.50);

    $product = createShippingProduct('Under-Threshold Widget', 20.00);

    $service = app(CartService::class);
    $service->add($product);

    expect($service->shipping('GR'))->toBe(3.50);
});

// ──── Shipping Amount: Over Threshold (Free) ────

it('gives free shipping when subtotal exceeds threshold', function () {
    Setting::set('free_shipping_threshold', '50.00');
    Setting::set('default_shipping_method_code', 'standard');

    $method = createShippingMethod();
    createShippingRate($method, 'GR', 3.50);

    $product = createShippingProduct('Over-Threshold Widget', 55.00);

    $service = app(CartService::class);
    $service->add($product);

    expect($service->shipping('GR'))->toBe(0.00);
});

// ──── Shipping Amount: Exactly at Threshold ────

it('gives free shipping when subtotal equals threshold exactly', function () {
    Setting::set('free_shipping_threshold', '50.00');
    Setting::set('default_shipping_method_code', 'standard');

    $method = createShippingMethod();
    createShippingRate($method, 'GR', 3.50);

    $product = createShippingProduct('Exact-Threshold Widget', 50.00);

    $service = app(CartService::class);
    $service->add($product);

    expect($service->shipping('GR'))->toBe(0.00);
});

// ──── Inactive Rate Not Used ────

it('ignores inactive shipping rates', function () {
    Setting::set('free_shipping_threshold', '100.00');
    Setting::set('default_shipping_method_code', 'standard');
    Setting::set('flat_shipping_rate', '9.99');

    $method = createShippingMethod();
    createShippingRate($method, 'GR', 3.50, isActive: false);

    $product = createShippingProduct('Inactive-Rate Widget', 20.00);

    $service = app(CartService::class);
    $service->add($product);

    // Falls back to flat_shipping_rate since no active rate found
    expect($service->shipping('GR'))->toBe(9.99);
});

// ──── Inactive Method Not Used ────

it('ignores inactive shipping methods', function () {
    Setting::set('free_shipping_threshold', '100.00');
    Setting::set('default_shipping_method_code', 'standard');
    Setting::set('flat_shipping_rate', '9.99');

    $method = createShippingMethod('standard', 'Standard Shipping', isActive: false);
    createShippingRate($method, 'GR', 3.50);

    $product = createShippingProduct('Inactive-Method Widget', 20.00);

    $service = app(CartService::class);
    $service->add($product);

    expect($service->shipping('GR'))->toBe(9.99);
});

// ──── Country-Specific Rate ────

it('uses country-specific shipping rate', function () {
    Setting::set('free_shipping_threshold', '100.00');
    Setting::set('default_shipping_method_code', 'standard');

    $method = createShippingMethod();
    createShippingRate($method, 'GR', 3.50);
    createShippingRate($method, 'CY', 5.00);

    $product = createShippingProduct('Country Widget', 20.00);

    $service = app(CartService::class);
    $service->add($product);

    expect($service->shipping('GR'))->toBe(3.50)
        ->and($service->shipping('CY'))->toBe(5.00);
});

// ──── Fallback to flat_shipping_rate Setting ────

it('falls back to flat_shipping_rate when no DB rate matches', function () {
    Setting::set('free_shipping_threshold', '100.00');
    Setting::set('default_shipping_method_code', 'standard');
    Setting::set('flat_shipping_rate', '7.77');

    // No shipping methods or rates in DB

    $product = createShippingProduct('Fallback Widget', 20.00);

    $service = app(CartService::class);
    $service->add($product);

    expect($service->shipping('GR'))->toBe(7.77);
});

// ──── Order Snapshot Includes Shipping Method ────

it('persists shipping method code and label on order', function () {
    Setting::set('free_shipping_threshold', '100.00');
    Setting::set('default_shipping_method_code', 'standard');

    $method = createShippingMethod('standard', 'Standard Shipping');
    createShippingRate($method, 'GR', 3.50);

    $product = createShippingProduct('Snapshot Widget', 20.00);

    $cartService = app(CartService::class);
    $cartService->add($product);

    session(['checkout' => ['shipping_country' => 'GR']]);

    $orderService = app(OrderService::class);
    $order = $orderService->createFromCheckout([
        'email' => 'shipping@example.com',
        'shipping_first_name' => 'John',
        'shipping_last_name' => 'Doe',
        'shipping_address' => '123 Main St',
        'shipping_city' => 'Athens',
        'shipping_postal_code' => '10431',
        'shipping_country' => 'GR',
        'billing_same_as_shipping' => true,
        'billing_first_name' => 'John',
        'billing_last_name' => 'Doe',
        'billing_address' => '123 Main St',
        'billing_city' => 'Athens',
        'billing_postal_code' => '10431',
        'billing_country' => 'GR',
    ], $cartService->cart());

    expect($order->shipping_method_code)->toBe('standard')
        ->and($order->shipping_label)->toBe('Standard Shipping')
        ->and((float) $order->shipping_amount)->toBe(3.50);
});

// ──── Order Snapshot: Free Shipping ────

it('persists zero shipping amount when threshold met', function () {
    Setting::set('free_shipping_threshold', '50.00');
    Setting::set('default_shipping_method_code', 'standard');

    $method = createShippingMethod('standard', 'Standard Shipping');
    createShippingRate($method, 'GR', 3.50);

    $product = createShippingProduct('Free-Ship Widget', 55.00);

    $cartService = app(CartService::class);
    $cartService->add($product);

    session(['checkout' => ['shipping_country' => 'GR']]);

    $orderService = app(OrderService::class);
    $order = $orderService->createFromCheckout([
        'email' => 'freeship@example.com',
        'shipping_first_name' => 'John',
        'shipping_last_name' => 'Doe',
        'shipping_address' => '123 Main St',
        'shipping_city' => 'Athens',
        'shipping_postal_code' => '10431',
        'shipping_country' => 'GR',
        'billing_same_as_shipping' => true,
        'billing_first_name' => 'John',
        'billing_last_name' => 'Doe',
        'billing_address' => '123 Main St',
        'billing_city' => 'Athens',
        'billing_postal_code' => '10431',
        'billing_country' => 'GR',
    ], $cartService->cart());

    expect($order->shipping_method_code)->toBe('standard')
        ->and($order->shipping_label)->toBe('Standard Shipping')
        ->and((float) $order->shipping_amount)->toBe(0.00);
});

// ──── checkoutTotals Includes Shipping Method Info ────

it('checkoutTotals returns shipping method code and label', function () {
    Setting::set('free_shipping_threshold', '100.00');
    Setting::set('default_shipping_method_code', 'standard');

    $method = createShippingMethod('standard', 'Standard Shipping');
    createShippingRate($method, 'GR', 3.50);

    $product = createShippingProduct('Totals Widget', 20.00);

    $service = app(CartService::class);
    $service->add($product);

    session(['checkout' => ['shipping_country' => 'GR']]);

    $totals = $service->checkoutTotals();

    expect($totals)
        ->toHaveKey('shipping_method_code', 'standard')
        ->toHaveKey('shipping_label', 'Standard Shipping')
        ->and($totals['shipping'])->toBe(3.50);
});
