<?php

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductVariant;

it('generates a deterministic signature from attribute value IDs', function () {
    $sig1 = ProductVariant::generateSignature([3, 1, 2]);
    $sig2 = ProductVariant::generateSignature([1, 2, 3]);

    // Same IDs in different order produce the same signature
    expect($sig1)->toBe($sig2);
    expect(strlen($sig1))->toBe(40); // SHA1 hex length
});

it('generates different signatures for different attribute combinations', function () {
    $sig1 = ProductVariant::generateSignature([1, 2]);
    $sig2 = ProductVariant::generateSignature([1, 3]);

    expect($sig1)->not->toBe($sig2);
});

it('creates a variant with attributes and signature', function () {
    $product = Product::create([
        'name' => 'T-Shirt',
        'slug' => 't-shirt',
        'price' => 25.00,
        'has_variants' => true,
    ]);

    $color = Attribute::create(['name' => 'Color', 'type' => 'color']);
    $size = Attribute::create(['name' => 'Size', 'type' => 'select']);

    $red = AttributeValue::create(['attribute_id' => $color->id, 'value' => 'Red', 'color_hex' => '#ef4444']);
    $medium = AttributeValue::create(['attribute_id' => $size->id, 'value' => 'M']);

    $attributeValueIds = [$red->id, $medium->id];

    $variant = ProductVariant::create([
        'product_id' => $product->id,
        'sku' => 'TSHIRT-RED-M',
        'stock' => 15,
        'signature' => ProductVariant::generateSignature($attributeValueIds),
    ]);

    $variant->attributeValues()->attach($attributeValueIds);

    expect($variant->exists)->toBeTrue();
    expect($variant->attributeValues)->toHaveCount(2);
    expect($variant->effective_price)->toBe('25.00'); // Falls back to product price
});

it('uses variant price when set', function () {
    $product = Product::create([
        'name' => 'Premium Shirt',
        'slug' => 'premium-shirt',
        'price' => 30.00,
        'has_variants' => true,
    ]);

    $variant = ProductVariant::create([
        'product_id' => $product->id,
        'sku' => 'PS-XL',
        'price' => 35.00,
        'stock' => 5,
        'signature' => ProductVariant::generateSignature([99]),
    ]);

    expect($variant->effective_price)->toBe('35.00');
});

it('prevents duplicate variant signatures per product', function () {
    $product = Product::create([
        'name' => 'Unique Variant Product',
        'slug' => 'unique-variant',
        'price' => 20.00,
        'has_variants' => true,
    ]);

    $signature = ProductVariant::generateSignature([1, 2]);

    ProductVariant::create([
        'product_id' => $product->id,
        'sku' => 'UV-1',
        'stock' => 10,
        'signature' => $signature,
    ]);

    expect(fn () => ProductVariant::create([
        'product_id' => $product->id,
        'sku' => 'UV-2',
        'stock' => 5,
        'signature' => $signature,
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});
