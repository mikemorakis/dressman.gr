<?php

use App\Models\Product;
use App\Models\Setting;
use App\Models\ShippingMethod;
use App\Models\ShippingRate;
use App\Models\ShippingZone;
use App\Models\ShippingZonePostalPrefix;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Support\Str;

function createZoneShippingMethod(string $code = 'standard', string $name = 'Standard Shipping', bool $isActive = true): ShippingMethod
{
    return ShippingMethod::create([
        'code' => $code,
        'name' => $name,
        'is_active' => $isActive,
        'sort_order' => 0,
    ]);
}

function createZoneShippingRate(
    ShippingMethod $method,
    string $country = 'GR',
    float $flatAmount = 3.50,
    bool $isActive = true,
    ?int $zoneId = null,
): ShippingRate {
    return ShippingRate::create([
        'shipping_method_id' => $method->id,
        'country_code' => $country,
        'shipping_zone_id' => $zoneId,
        'flat_amount' => $flatAmount,
        'is_active' => $isActive,
    ]);
}

function createZoneProduct(string $name, float $price, int $stock = 10): Product
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

/**
 * Seed ATTICA + NON_ATTICA zones with postal prefixes 10-19 for ATTICA.
 *
 * @return array{attica: ShippingZone, non_attica: ShippingZone}
 */
function seedZones(): array
{
    $attica = ShippingZone::create([
        'code' => 'ATTICA',
        'name' => 'Αποστολή εντός Αττικής',
        'is_active' => true,
        'sort_order' => 0,
    ]);

    $nonAttica = ShippingZone::create([
        'code' => 'NON_ATTICA',
        'name' => 'Αποστολή εκτός Αττικής',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    foreach (range(10, 19) as $prefix) {
        ShippingZonePostalPrefix::create([
            'shipping_zone_id' => $attica->id,
            'postal_prefix' => (string) $prefix,
        ]);
    }

    return ['attica' => $attica, 'non_attica' => $nonAttica];
}

// ──── ATTICA postal code resolves to ATTICA zone rate ────

it('charges ATTICA rate for postal code in 10-19 range', function () {
    Setting::set('free_shipping_threshold', '100.00');
    Setting::set('default_shipping_method_code', 'standard');

    $zones = seedZones();
    $method = createZoneShippingMethod();
    createZoneShippingRate($method, 'GR', 3.50, true, $zones['attica']->id);
    createZoneShippingRate($method, 'GR', 5.00, true, $zones['non_attica']->id);

    $product = createZoneProduct('Attica Widget', 20.00);

    $service = app(CartService::class);
    $service->add($product);

    expect($service->shipping('GR', '11527'))->toBe(3.50);
});

// ──── NON_ATTICA postal code resolves to fallback zone ────

it('charges NON_ATTICA rate for postal code outside 10-19 range', function () {
    Setting::set('free_shipping_threshold', '100.00');
    Setting::set('default_shipping_method_code', 'standard');

    $zones = seedZones();
    $method = createZoneShippingMethod();
    createZoneShippingRate($method, 'GR', 3.50, true, $zones['attica']->id);
    createZoneShippingRate($method, 'GR', 5.00, true, $zones['non_attica']->id);

    $product = createZoneProduct('Thessaloniki Widget', 20.00);

    $service = app(CartService::class);
    $service->add($product);

    expect($service->shipping('GR', '55133'))->toBe(5.00);
});

// ──── CY ignores zones ────

it('uses country-level rate for CY ignoring zones', function () {
    Setting::set('free_shipping_threshold', '100.00');
    Setting::set('default_shipping_method_code', 'standard');

    $zones = seedZones();
    $method = createZoneShippingMethod();
    createZoneShippingRate($method, 'GR', 3.50, true, $zones['attica']->id);
    createZoneShippingRate($method, 'GR', 5.00, true, $zones['non_attica']->id);
    createZoneShippingRate($method, 'CY', 5.00, true, null); // country-level

    $product = createZoneProduct('CY Widget', 20.00);

    $service = app(CartService::class);
    $service->add($product);

    expect($service->shipping('CY'))->toBe(5.00);
});

// ──── resolveShipping returns zone name as label for GR ────

it('resolveShipping returns zone name as label for GR postal code', function () {
    Setting::set('free_shipping_threshold', '100.00');
    Setting::set('default_shipping_method_code', 'standard');

    $zones = seedZones();
    $method = createZoneShippingMethod();
    createZoneShippingRate($method, 'GR', 3.50, true, $zones['attica']->id);
    createZoneShippingRate($method, 'GR', 5.00, true, $zones['non_attica']->id);

    $product = createZoneProduct('Label Widget', 20.00);

    $service = app(CartService::class);
    $service->add($product);

    $info = $service->resolveShipping('GR', '11527');
    expect($info['label'])->toBe('Αποστολή εντός Αττικής')
        ->and($info['zone_code'])->toBe('ATTICA');

    $info2 = $service->resolveShipping('GR', '55133');
    expect($info2['label'])->toBe('Αποστολή εκτός Αττικής')
        ->and($info2['zone_code'])->toBe('NON_ATTICA');
});

// ──── CY resolveShipping returns method name, no zone ────

it('resolveShipping returns method name for CY with no zone', function () {
    Setting::set('free_shipping_threshold', '100.00');
    Setting::set('default_shipping_method_code', 'standard');

    seedZones();
    $method = createZoneShippingMethod();
    createZoneShippingRate($method, 'CY', 5.00, true, null);

    $product = createZoneProduct('CY Label Widget', 20.00);

    $service = app(CartService::class);
    $service->add($product);

    $info = $service->resolveShipping('CY');
    expect($info['label'])->toBe('Standard Shipping')
        ->and($info['zone_code'])->toBeNull();
});

// ──── Order snapshot includes shipping_zone_code ────

it('persists shipping_zone_code on order', function () {
    Setting::set('free_shipping_threshold', '100.00');
    Setting::set('default_shipping_method_code', 'standard');

    $zones = seedZones();
    $method = createZoneShippingMethod();
    createZoneShippingRate($method, 'GR', 3.50, true, $zones['attica']->id);
    createZoneShippingRate($method, 'GR', 5.00, true, $zones['non_attica']->id);

    $product = createZoneProduct('Order Widget', 20.00);

    $cartService = app(CartService::class);
    $cartService->add($product);

    session(['checkout' => [
        'shipping_country' => 'GR',
        'shipping_postal_code' => '11527',
    ]]);

    $orderService = app(OrderService::class);
    $order = $orderService->createFromCheckout([
        'email' => 'zone@example.com',
        'shipping_first_name' => 'John',
        'shipping_last_name' => 'Doe',
        'shipping_address' => '123 Main St',
        'shipping_city' => 'Athens',
        'shipping_postal_code' => '11527',
        'shipping_country' => 'GR',
        'billing_same_as_shipping' => true,
        'billing_first_name' => 'John',
        'billing_last_name' => 'Doe',
        'billing_address' => '123 Main St',
        'billing_city' => 'Athens',
        'billing_postal_code' => '11527',
        'billing_country' => 'GR',
    ], $cartService->cart());

    expect($order->shipping_zone_code)->toBe('ATTICA')
        ->and($order->shipping_label)->toBe('Αποστολή εντός Αττικής')
        ->and((float) $order->shipping_amount)->toBe(3.50);
});
