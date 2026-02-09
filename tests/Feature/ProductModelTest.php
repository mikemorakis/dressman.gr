<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Label;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;

it('creates a simple product', function () {
    $product = Product::create([
        'name' => 'Test Product',
        'slug' => 'test-product',
        'price' => 29.99,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);

    $product->refresh();

    expect($product->exists)->toBeTrue();
    expect($product->has_variants)->toBeFalse();
    expect($product->price)->toBe('29.99');
});

it('computes available stock for simple products', function () {
    $product = Product::create([
        'name' => 'Stock Test',
        'slug' => 'stock-test',
        'price' => 10.00,
        'stock' => 20,
        'reserved_stock' => 5,
    ]);

    expect($product->available_stock)->toBe(15);
});

it('computes available stock from variants when has_variants is true', function () {
    $product = Product::create([
        'name' => 'Variant Product',
        'slug' => 'variant-product',
        'price' => 50.00,
        'has_variants' => true,
    ]);

    ProductVariant::create([
        'product_id' => $product->id,
        'sku' => 'VP-S',
        'stock' => 10,
        'reserved_stock' => 2,
        'signature' => ProductVariant::generateSignature([1]),
    ]);

    ProductVariant::create([
        'product_id' => $product->id,
        'sku' => 'VP-M',
        'stock' => 5,
        'reserved_stock' => 0,
        'signature' => ProductVariant::generateSignature([2]),
    ]);

    $product->load('variants');

    expect($product->available_stock)->toBe(13); // (10-2) + (5-0)
});

it('detects on sale products', function () {
    $product = Product::create([
        'name' => 'Sale Product',
        'slug' => 'sale-product',
        'price' => 19.99,
        'compare_price' => 29.99,
    ]);

    expect($product->is_on_sale)->toBeTrue();
});

it('detects low stock', function () {
    $product = Product::create([
        'name' => 'Low Stock',
        'slug' => 'low-stock',
        'price' => 10.00,
        'stock' => 3,
        'low_stock_threshold' => 5,
    ]);

    expect($product->is_low_stock)->toBeTrue();
    expect($product->is_in_stock)->toBeTrue();
});

it('scopes active products', function () {
    Product::create(['name' => 'Active', 'slug' => 'active', 'price' => 10, 'is_active' => true, 'published_at' => now()]);
    Product::create(['name' => 'Inactive', 'slug' => 'inactive', 'price' => 10, 'is_active' => false, 'published_at' => now()]);
    Product::create(['name' => 'Unpublished', 'slug' => 'unpublished', 'price' => 10, 'is_active' => true, 'published_at' => null]);

    expect(Product::active()->count())->toBe(1);
    expect(Product::active()->first()->name)->toBe('Active');
});

it('belongs to a brand', function () {
    $brand = Brand::create(['name' => 'TestBrand', 'slug' => 'testbrand']);
    $product = Product::create([
        'name' => 'Branded Product',
        'slug' => 'branded',
        'price' => 10,
        'brand_id' => $brand->id,
    ]);

    expect($product->brand->name)->toBe('TestBrand');
});

it('has many-to-many categories and labels', function () {
    $product = Product::create(['name' => 'Tagged', 'slug' => 'tagged', 'price' => 10]);
    $category = Category::create(['name' => 'Electronics', 'slug' => 'electronics']);
    $label = Label::create(['name' => 'New', 'slug' => 'new', 'color' => '#22c55e']);

    $product->categories()->attach($category);
    $product->labels()->attach($label);

    expect($product->categories)->toHaveCount(1);
    expect($product->labels)->toHaveCount(1);
    expect($category->products)->toHaveCount(1);
    expect($label->products)->toHaveCount(1);
});

it('has images ordered by sort_order', function () {
    $product = Product::create(['name' => 'Imaged', 'slug' => 'imaged', 'price' => 10]);

    ProductImage::create([
        'product_id' => $product->id,
        'path_large' => 'products/b_large.jpg',
        'path_medium' => 'products/b_medium.jpg',
        'path_thumb' => 'products/b_thumb.jpg',
        'alt_text' => 'Image B',
        'sort_order' => 1,
    ]);

    ProductImage::create([
        'product_id' => $product->id,
        'path_large' => 'products/a_large.jpg',
        'path_medium' => 'products/a_medium.jpg',
        'path_thumb' => 'products/a_thumb.jpg',
        'alt_text' => 'Image A',
        'sort_order' => 0,
    ]);

    expect($product->images)->toHaveCount(2);
    expect($product->images->first()->alt_text)->toBe('Image A');
});

it('uses withAvailableStock scope to avoid N+1 on variant stock', function () {
    $product = Product::create([
        'name' => 'Scoped Variant',
        'slug' => 'scoped-variant',
        'price' => 50.00,
        'has_variants' => true,
    ]);

    ProductVariant::create([
        'product_id' => $product->id,
        'sku' => 'SV-A',
        'stock' => 10,
        'reserved_stock' => 3,
        'signature' => ProductVariant::generateSignature([10]),
    ]);

    ProductVariant::create([
        'product_id' => $product->id,
        'sku' => 'SV-B',
        'stock' => 6,
        'reserved_stock' => 1,
        'signature' => ProductVariant::generateSignature([20]),
    ]);

    // Load via scope (single query with withSum)
    $loaded = Product::withAvailableStock()->find($product->id);

    expect($loaded->available_stock)->toBe(12); // (10-3) + (6-1)
});

it('soft deletes a product', function () {
    $product = Product::create(['name' => 'Deletable', 'slug' => 'deletable', 'price' => 10]);
    $product->delete();

    expect(Product::count())->toBe(0);
    expect(Product::withTrashed()->count())->toBe(1);
});
