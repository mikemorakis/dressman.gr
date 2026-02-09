<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\UrlRedirect;

// ──── Observer: redirect creation ────

it('creates a url redirect when product slug changes', function () {
    $product = Product::create([
        'name' => 'Widget',
        'slug' => 'widget',
        'price' => 10.00,
        'is_active' => true,
        'published_at' => now(),
    ]);

    $product->update(['slug' => 'new-widget']);

    expect(UrlRedirect::where('old_slug', 'widget')->where('new_slug', 'new-widget')->exists())->toBeTrue();
});

it('creates a url redirect when category slug changes', function () {
    $category = Category::create([
        'name' => 'Shoes',
        'slug' => 'shoes',
        'is_visible' => true,
    ]);

    $category->update(['slug' => 'footwear']);

    expect(UrlRedirect::where('old_slug', 'shoes')->where('new_slug', 'footwear')->where('type', 'category')->exists())->toBeTrue();
});

// ──── Observer: circular redirect prevention ────

it('removes circular redirect when slug reverts to old value', function () {
    $product = Product::create([
        'name' => 'Widget',
        'slug' => 'alpha',
        'price' => 10.00,
    ]);

    // alpha → beta
    $product->update(['slug' => 'beta']);
    expect(UrlRedirect::where('old_slug', 'alpha')->where('new_slug', 'beta')->exists())->toBeTrue();

    // beta → alpha (revert): beta→alpha created, alpha→beta should be cleaned up (circular)
    $product->update(['slug' => 'alpha']);
    expect(UrlRedirect::where('old_slug', 'beta')->where('new_slug', 'alpha')->exists())->toBeTrue();
    expect(UrlRedirect::where('old_slug', 'alpha')->where('new_slug', 'beta')->exists())->toBeFalse();
});

// ──── Observer: chain collapsing ────

it('collapses redirect chains on slug change', function () {
    $product = Product::create([
        'name' => 'Widget',
        'slug' => 'original',
        'price' => 10.00,
    ]);

    // original → renamed
    $product->update(['slug' => 'renamed']);
    expect(UrlRedirect::where('old_slug', 'original')->where('new_slug', 'renamed')->exists())->toBeTrue();

    // renamed → final: original should now point to final (chain collapsed)
    $product->update(['slug' => 'final']);
    expect(UrlRedirect::where('old_slug', 'renamed')->where('new_slug', 'final')->exists())->toBeTrue();
    expect(UrlRedirect::where('old_slug', 'original')->where('new_slug', 'final')->exists())->toBeTrue();
});

// ──── Controller: 301 redirect behavior ────

it('follows url_redirect to current product slug', function () {
    Product::create([
        'name' => 'Final Product',
        'slug' => 'final-slug',
        'price' => 10.00,
        'is_active' => true,
        'published_at' => now(),
    ]);

    UrlRedirect::create([
        'old_slug' => 'original-slug',
        'new_slug' => 'final-slug',
        'type' => 'product',
    ]);

    $response = $this->get('/product/original-slug');
    $response->assertRedirect('/product/final-slug');
    $response->assertStatus(301);
});

it('returns 404 when no product and no redirect exist', function () {
    $this->get('/product/nonexistent')->assertNotFound();
});
