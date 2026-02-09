<?php

use App\Livewire\ProductSearch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Livewire;

// ──── Search Page ────

it('renders the search page', function () {
    $response = $this->get('/search');

    $response->assertOk();
    $response->assertSee('Search products');
    $response->assertSeeLivewire(ProductSearch::class);
});

it('includes canonical URL without filter params', function () {
    $response = $this->get('/search?q=test&cat[]=1&min=10');

    $response->assertOk();
    $response->assertSee('canonical', false);
    $response->assertSee(url('/search').'?q=test', false);
    $response->assertDontSee('cat%5B', false);
});

// ──── Livewire Search Component ────

it('renders search results matching query', function () {
    Product::create([
        'name' => 'Wireless Headphones',
        'slug' => 'wireless-headphones',
        'price' => 29.99,
        'is_active' => true,
        'published_at' => now(),
    ]);

    Product::create([
        'name' => 'Bluetooth Speaker',
        'slug' => 'bluetooth-speaker',
        'price' => 49.99,
        'is_active' => true,
        'published_at' => now(),
    ]);

    Livewire::test(ProductSearch::class)
        ->set('query', 'Wireless')
        ->assertSee('Wireless Headphones')
        ->assertDontSee('Bluetooth Speaker');
});

it('shows all active products when query is empty', function () {
    Product::create([
        'name' => 'Product A',
        'slug' => 'product-a',
        'price' => 10.00,
        'is_active' => true,
        'published_at' => now(),
    ]);

    Product::create([
        'name' => 'Product B',
        'slug' => 'product-b',
        'price' => 20.00,
        'is_active' => true,
        'published_at' => now(),
    ]);

    Livewire::test(ProductSearch::class)
        ->assertSee('Product A')
        ->assertSee('Product B');
});

it('excludes inactive products from results', function () {
    Product::create([
        'name' => 'Active Product',
        'slug' => 'active-product',
        'price' => 10.00,
        'is_active' => true,
        'published_at' => now(),
    ]);

    Product::create([
        'name' => 'Inactive Product',
        'slug' => 'inactive-product',
        'price' => 10.00,
        'is_active' => false,
        'published_at' => now(),
    ]);

    Livewire::test(ProductSearch::class)
        ->assertSee('Active Product')
        ->assertDontSee('Inactive Product');
});

it('filters products by category', function () {
    $electronics = Category::create([
        'name' => 'Electronics',
        'slug' => 'electronics',
        'is_visible' => true,
    ]);

    $clothing = Category::create([
        'name' => 'Clothing',
        'slug' => 'clothing',
        'is_visible' => true,
    ]);

    $phone = Product::create([
        'name' => 'Phone',
        'slug' => 'phone',
        'price' => 499.99,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $phone->refresh();
    $phone->categories()->attach($electronics);

    $shirt = Product::create([
        'name' => 'Shirt',
        'slug' => 'shirt',
        'price' => 19.99,
        'is_active' => true,
        'published_at' => now(),
    ]);
    $shirt->refresh();
    $shirt->categories()->attach($clothing);

    Livewire::test(ProductSearch::class)
        ->set('categories', [(string) $electronics->id])
        ->assertSee('Phone')
        ->assertDontSee('Shirt');
});

it('filters products by brand', function () {
    $acme = Brand::create(['name' => 'Acme', 'slug' => 'acme', 'is_visible' => true]);
    $globex = Brand::create(['name' => 'Globex', 'slug' => 'globex', 'is_visible' => true]);

    Product::create([
        'name' => 'Acme Widget',
        'slug' => 'acme-widget',
        'price' => 10.00,
        'brand_id' => $acme->id,
        'is_active' => true,
        'published_at' => now(),
    ]);

    Product::create([
        'name' => 'Globex Gadget',
        'slug' => 'globex-gadget',
        'price' => 15.00,
        'brand_id' => $globex->id,
        'is_active' => true,
        'published_at' => now(),
    ]);

    Livewire::test(ProductSearch::class)
        ->set('brands', [(string) $acme->id])
        ->assertSee('Acme Widget')
        ->assertDontSee('Globex Gadget');
});

it('filters products by minimum price', function () {
    Product::create([
        'name' => 'Cheap Item',
        'slug' => 'cheap-item',
        'price' => 5.00,
        'is_active' => true,
        'published_at' => now(),
    ]);

    Product::create([
        'name' => 'Expensive Item',
        'slug' => 'expensive-item',
        'price' => 100.00,
        'is_active' => true,
        'published_at' => now(),
    ]);

    Livewire::test(ProductSearch::class)
        ->set('minPrice', '50')
        ->assertDontSee('Cheap Item')
        ->assertSee('Expensive Item');
});

it('filters products by maximum price', function () {
    Product::create([
        'name' => 'Budget Item',
        'slug' => 'budget-item',
        'price' => 15.00,
        'is_active' => true,
        'published_at' => now(),
    ]);

    Product::create([
        'name' => 'Premium Item',
        'slug' => 'premium-item',
        'price' => 200.00,
        'is_active' => true,
        'published_at' => now(),
    ]);

    Livewire::test(ProductSearch::class)
        ->set('maxPrice', '50')
        ->assertSee('Budget Item')
        ->assertDontSee('Premium Item');
});

it('sorts products by price ascending', function () {
    Product::create([
        'name' => 'Expensive First',
        'slug' => 'expensive-first',
        'price' => 99.99,
        'is_active' => true,
        'published_at' => now(),
    ]);

    Product::create([
        'name' => 'Cheap First',
        'slug' => 'cheap-first',
        'price' => 1.99,
        'is_active' => true,
        'published_at' => now(),
    ]);

    $component = Livewire::test(ProductSearch::class)
        ->set('sort', 'price_asc');

    // Cheap should appear before Expensive in the HTML
    $html = $component->html();
    $cheapPos = strpos($html, 'Cheap First');
    $expensivePos = strpos($html, 'Expensive First');

    expect($cheapPos)->toBeLessThan($expensivePos);
});

it('sorts products by price descending', function () {
    Product::create([
        'name' => 'Low Price',
        'slug' => 'low-price',
        'price' => 5.00,
        'is_active' => true,
        'published_at' => now(),
    ]);

    Product::create([
        'name' => 'High Price',
        'slug' => 'high-price',
        'price' => 95.00,
        'is_active' => true,
        'published_at' => now(),
    ]);

    $component = Livewire::test(ProductSearch::class)
        ->set('sort', 'price_desc');

    $html = $component->html();
    $highPos = strpos($html, 'High Price');
    $lowPos = strpos($html, 'Low Price');

    expect($highPos)->toBeLessThan($lowPos);
});

it('sorts products by name A-Z', function () {
    Product::create([
        'name' => 'Zeta Product',
        'slug' => 'zeta-product',
        'price' => 10.00,
        'is_active' => true,
        'published_at' => now(),
    ]);

    Product::create([
        'name' => 'Alpha Product',
        'slug' => 'alpha-product',
        'price' => 10.00,
        'is_active' => true,
        'published_at' => now(),
    ]);

    $component = Livewire::test(ProductSearch::class)
        ->set('sort', 'name');

    $html = $component->html();
    $alphaPos = strpos($html, 'Alpha Product');
    $zetaPos = strpos($html, 'Zeta Product');

    expect($alphaPos)->toBeLessThan($zetaPos);
});

it('clears all filters', function () {
    $category = Category::create([
        'name' => 'Test Cat',
        'slug' => 'test-cat',
        'is_visible' => true,
    ]);

    Livewire::test(ProductSearch::class)
        ->set('categories', [(string) $category->id])
        ->set('minPrice', '10')
        ->set('maxPrice', '100')
        ->set('sort', 'price_asc')
        ->call('clearFilters')
        ->assertSet('categories', [])
        ->assertSet('minPrice', '')
        ->assertSet('maxPrice', '')
        ->assertSet('sort', 'relevance');
});

it('shows empty state when no products match', function () {
    Livewire::test(ProductSearch::class)
        ->set('query', 'nonexistent-product-xyz')
        ->assertSee('No products found');
});

it('shows category and brand filter options', function () {
    Category::create([
        'name' => 'Filter Category',
        'slug' => 'filter-category',
        'is_visible' => true,
    ]);

    Brand::create([
        'name' => 'Filter Brand',
        'slug' => 'filter-brand',
        'is_visible' => true,
    ]);

    Livewire::test(ProductSearch::class)
        ->assertSee('Filter Category')
        ->assertSee('Filter Brand');
});

it('resets pagination when query changes', function () {
    // Create enough products to have page 2
    for ($i = 1; $i <= 15; $i++) {
        Product::create([
            'name' => "Paginated Product {$i}",
            'slug' => "paginated-product-{$i}",
            'price' => 10.00,
            'is_active' => true,
            'published_at' => now(),
        ]);
    }

    Livewire::test(ProductSearch::class)
        ->set('query', 'Paginated')
        ->assertSet('page', null);  // Page resets on query change
});

it('syncs query parameter to URL', function () {
    Livewire::test(ProductSearch::class)
        ->set('query', 'shoes')
        ->assertSet('query', 'shoes');
});

it('adds noindex when filter params are present', function () {
    $response = $this->get('/search?q=test&cat[]=1');

    $response->assertOk();
    $response->assertSee('noindex, follow', false);
});

it('omits noindex when only q param is present', function () {
    $response = $this->get('/search?q=test');

    $response->assertOk();
    $response->assertDontSee('noindex', false);
});
