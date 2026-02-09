<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\UrlRedirect;

// ──── Home Page ────

it('displays the home page with featured products', function () {
    $product = Product::create([
        'name' => 'Featured Widget',
        'slug' => 'featured-widget',
        'price' => 19.99,
        'is_active' => true,
        'is_featured' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('Featured Products');
    $response->assertSee('Featured Widget');
});

it('displays the home page with categories', function () {
    Category::create([
        'name' => 'Electronics',
        'slug' => 'electronics',
        'is_visible' => true,
    ]);

    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('Shop by Category');
    $response->assertSee('Electronics');
});

it('hides featured section when no featured products exist', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertDontSee('Featured Products');
});

// ──── Category Page ────

it('displays a category page with products', function () {
    $category = Category::create([
        'name' => 'Clothing',
        'slug' => 'clothing',
        'is_visible' => true,
    ]);

    $product = Product::create([
        'name' => 'Blue Shirt',
        'slug' => 'blue-shirt',
        'price' => 25.00,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();
    $product->categories()->attach($category);

    $response = $this->get('/category/clothing');

    $response->assertOk();
    $response->assertSee('Clothing');
    $response->assertSee('Blue Shirt');
});

it('returns 404 for non-existent category', function () {
    $response = $this->get('/category/does-not-exist');

    $response->assertNotFound();
});

it('displays subcategory chips', function () {
    $parent = Category::create([
        'name' => 'Clothing',
        'slug' => 'clothing',
        'is_visible' => true,
    ]);
    Category::create([
        'name' => 'T-Shirts',
        'slug' => 't-shirts',
        'is_visible' => true,
        'parent_id' => $parent->id,
    ]);

    $response = $this->get('/category/clothing');

    $response->assertOk();
    $response->assertSee('T-Shirts');
});

it('supports sorting by price ascending', function () {
    Category::create([
        'name' => 'Sort Test',
        'slug' => 'sort-test',
        'is_visible' => true,
    ]);

    $response = $this->get('/category/sort-test?sort=price_asc');

    $response->assertOk();
});

// ──── Product Page ────

it('displays a product detail page', function () {
    $brand = Brand::create(['name' => 'Acme Corp', 'slug' => 'acme-corp']);
    $product = Product::create([
        'name' => 'Super Widget',
        'slug' => 'super-widget',
        'sku' => 'SW-001',
        'brand_id' => $brand->id,
        'price' => 29.99,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    $response = $this->get('/product/super-widget');

    $response->assertOk();
    $response->assertSee('Super Widget');
    $response->assertSee('Acme Corp');
    $response->assertSee('29,99');
});

it('returns 404 for non-existent product', function () {
    $response = $this->get('/product/does-not-exist');

    $response->assertNotFound();
});

it('redirects from old slug via url_redirects', function () {
    Product::create([
        'name' => 'Renamed Widget',
        'slug' => 'renamed-widget',
        'price' => 10.00,
        'is_active' => true,
        'published_at' => now(),
    ]);

    UrlRedirect::create([
        'old_slug' => 'old-widget',
        'new_slug' => 'renamed-widget',
        'type' => 'product',
    ]);

    $response = $this->get('/product/old-widget');

    $response->assertRedirect('/product/renamed-widget');
    $response->assertStatus(301);
});

it('displays product with variant selector data', function () {
    $product = Product::create([
        'name' => 'Variant Product',
        'slug' => 'variant-product',
        'price' => 30.00,
        'has_variants' => true,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();

    ProductVariant::create([
        'product_id' => $product->id,
        'sku' => 'VP-001',
        'price' => 39.99,
        'stock' => 10,
        'is_active' => true,
        'signature' => ProductVariant::generateSignature([1]),
    ]);

    $response = $this->get('/product/variant-product');

    $response->assertOk();
    $response->assertSee('Variant Product');
    $response->assertSee('Select options');
});

it('outputs Schema.org JSON-LD on product page', function () {
    Product::create([
        'name' => 'Schema Widget',
        'slug' => 'schema-widget',
        'sku' => 'SCH-001',
        'price' => 49.99,
        'stock' => 5,
        'is_active' => true,
        'published_at' => now(),
    ]);

    $response = $this->get('/product/schema-widget');

    $response->assertOk();
    $response->assertSee('"@type":"Product"', false);
    $response->assertSee('"sku":"SCH-001"', false);
});

it('validates JSON-LD has EUR currency and correct availability for in-stock product', function () {
    Product::create([
        'name' => 'InStock Item',
        'slug' => 'instock-item',
        'sku' => 'IS-001',
        'price' => 19.99,
        'stock' => 10,
        'is_active' => true,
        'published_at' => now(),
    ]);

    $response = $this->get('/product/instock-item');

    $response->assertOk();
    $response->assertSee('"priceCurrency":"EUR"', false);
    $response->assertSee('"availability":"https://schema.org/InStock"', false);
    $response->assertSee('"url":"http', false);
    $response->assertSee('/product/instock-item', false);
});

it('validates JSON-LD shows OutOfStock for zero-stock product', function () {
    Product::create([
        'name' => 'OOS Item',
        'slug' => 'oos-item',
        'sku' => 'OOS-001',
        'price' => 9.99,
        'stock' => 0,
        'is_active' => true,
        'published_at' => now(),
    ]);

    $response = $this->get('/product/oos-item');

    $response->assertOk();
    $response->assertSee('"availability":"https://schema.org/OutOfStock"', false);
});

it('renders product breadcrumbs when category exists', function () {
    $category = Category::create([
        'name' => 'Gadgets',
        'slug' => 'gadgets',
        'is_visible' => true,
    ]);

    $product = Product::create([
        'name' => 'Cool Gadget',
        'slug' => 'cool-gadget',
        'price' => 15.00,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $product->refresh();
    $product->categories()->attach($category);

    $response = $this->get('/product/cool-gadget');

    $response->assertOk();
    $response->assertSee('Gadgets');
    $response->assertSee('Cool Gadget');
});

it('sanitizes product description with clean_html', function () {
    Product::create([
        'name' => 'Html Product',
        'slug' => 'html-product',
        'price' => 10.00,
        'description' => '<p>Safe content</p><script>alert("xss")</script>',
        'is_active' => true,
        'published_at' => now(),
    ]);

    $response = $this->get('/product/html-product');

    $response->assertOk();
    $response->assertSee('Safe content');
    $response->assertDontSee('<script>', false);
});
