<?php

use App\Models\Category;
use App\Models\Label;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\WooCommerceImportService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

// ──── Helper ────

function wcResponse(array $items, ?int $total = null, ?int $totalPages = null): GuzzleHttp\Promise\PromiseInterface
{
    $total = $total ?? count($items);
    $totalPages = $totalPages ?? 1;

    return Http::response($items, 200, [
        'X-WP-Total' => (string) $total,
        'X-WP-TotalPages' => (string) $totalPages,
    ]);
}

function wcConfig(): void
{
    config([
        'woocommerce.url' => 'https://example.com',
        'woocommerce.consumer_key' => 'ck_test',
        'woocommerce.consumer_secret' => 'cs_test',
    ]);
}

function importService(): WooCommerceImportService
{
    return app(WooCommerceImportService::class);
}

/**
 * @return array{dryRun: bool, skipImages: bool, categoriesOnly: bool, productsOnly: bool}
 */
function defaultOptions(array $overrides = []): array
{
    return array_merge([
        'dryRun' => false,
        'skipImages' => true,
        'categoriesOnly' => false,
        'productsOnly' => false,
    ], $overrides);
}

function simpleProduct(array $overrides = []): array
{
    return array_merge([
        'id' => 101,
        'name' => 'Test Product',
        'slug' => 'test-product',
        'type' => 'simple',
        'status' => 'publish',
        'description' => '<p>Description</p>',
        'short_description' => 'Short desc',
        'sku' => 'TP-001',
        'price' => '29.99',
        'regular_price' => '29.99',
        'sale_price' => '',
        'manage_stock' => true,
        'stock_quantity' => 25,
        'stock_status' => 'instock',
        'weight' => '',
        'featured' => false,
        'categories' => [],
        'tags' => [],
        'attributes' => [],
        'images' => [],
    ], $overrides);
}

function emptyEndpoints(): array
{
    return [
        '*/wc/v3/products/categories*' => wcResponse([]),
        '*/wc/v3/products/tags*' => wcResponse([]),
        '*/wc/v3/products/attributes' => Http::response([], 200),
    ];
}

beforeEach(function () {
    Storage::fake('public');
    wcConfig();
});

// ──── Categories ────

it('imports categories with parent-child hierarchy', function () {
    Http::fake([
        '*/wc/v3/products/categories*' => wcResponse([
            ['id' => 1, 'name' => 'Clothing', 'slug' => 'clothing', 'parent' => 0, 'description' => 'All clothing', 'image' => null],
            ['id' => 2, 'name' => 'T-Shirts', 'slug' => 't-shirts', 'parent' => 1, 'description' => '', 'image' => null],
        ]),
        '*/wc/v3/products/tags*' => wcResponse([]),
        '*/wc/v3/products/attributes' => Http::response([], 200),
    ]);

    $counters = importService()->import(defaultOptions(['categoriesOnly' => true]));

    expect($counters['categories'])->toBe(2);
    expect(Category::count())->toBe(2);

    $parent = Category::where('slug', 'clothing')->first();
    $child = Category::where('slug', 't-shirts')->first();

    expect($parent->parent_id)->toBeNull();
    expect($child->parent_id)->toBe($parent->id);
});

// ──── Tags → Labels ────

it('imports WooCommerce tags as labels', function () {
    Http::fake([
        '*/wc/v3/products/categories*' => wcResponse([]),
        '*/wc/v3/products/tags*' => wcResponse([
            ['id' => 1, 'name' => 'Sale', 'slug' => 'sale'],
            ['id' => 2, 'name' => 'New Arrival', 'slug' => 'new-arrival'],
        ]),
        '*/wc/v3/products/attributes' => Http::response([], 200),
    ]);

    $counters = importService()->import(defaultOptions(['categoriesOnly' => true]));

    expect($counters['labels'])->toBe(2);
    expect(Label::count())->toBe(2);
    expect(Label::where('slug', 'sale')->first()->name)->toBe('Sale');
    expect(Label::where('slug', 'sale')->first()->color)->toBe('#ef4444');
});

// ──── Simple Product ────

it('imports a simple product with sale price mapping', function () {
    Http::fake([
        ...emptyEndpoints(),
        '*/wc/v3/products?*' => wcResponse([
            simpleProduct([
                'regular_price' => '39.99',
                'sale_price' => '29.99',
                'price' => '29.99',
            ]),
        ]),
    ]);

    $counters = importService()->import(defaultOptions());

    expect($counters['products'])->toBe(1);

    $product = Product::where('slug', 'test-product')->first();
    expect($product)->not->toBeNull();
    expect((float) $product->price)->toBe(29.99);
    expect((float) $product->compare_price)->toBe(39.99);
    expect($product->sku)->toBe('TP-001');
    expect($product->stock)->toBe(25);
    expect($product->is_active)->toBeTrue();
    expect($product->has_variants)->toBeFalse();
});

it('maps regular price when no sale price exists', function () {
    Http::fake([
        ...emptyEndpoints(),
        '*/wc/v3/products?*' => wcResponse([
            simpleProduct([
                'slug' => 'no-sale',
                'regular_price' => '49.99',
                'sale_price' => '',
                'price' => '49.99',
            ]),
        ]),
    ]);

    importService()->import(defaultOptions());

    $product = Product::where('slug', 'no-sale')->first();
    expect((float) $product->price)->toBe(49.99);
    expect($product->compare_price)->toBeNull();
});

// ──── Variable Product ────

it('imports a variable product with variants and attribute values', function () {
    Http::fake([
        ...emptyEndpoints(),
        '*/wc/v3/products/attributes' => Http::response([
            ['id' => 1, 'name' => 'Size', 'slug' => 'size', 'type' => 'select'],
        ], 200),
        '*/wc/v3/products/attributes/1/terms*' => wcResponse([
            ['id' => 10, 'name' => 'S', 'slug' => 's'],
            ['id' => 11, 'name' => 'M', 'slug' => 'm'],
        ]),
        '*/wc/v3/products?*' => wcResponse([
            simpleProduct([
                'id' => 200,
                'name' => 'Variant Tee',
                'slug' => 'variant-tee',
                'type' => 'variable',
                'sku' => 'VT-001',
                'attributes' => [['id' => 1, 'name' => 'Size', 'options' => ['S', 'M']]],
            ]),
        ]),
        '*/wc/v3/products/200/variations*' => wcResponse([
            [
                'id' => 301,
                'sku' => 'VT-001-S',
                'regular_price' => '25.00',
                'sale_price' => '',
                'price' => '25.00',
                'manage_stock' => true,
                'stock_quantity' => 10,
                'stock_status' => 'instock',
                'attributes' => [['id' => 1, 'name' => 'Size', 'option' => 'S']],
                'image' => null,
            ],
            [
                'id' => 302,
                'sku' => 'VT-001-M',
                'regular_price' => '25.00',
                'sale_price' => '',
                'price' => '25.00',
                'manage_stock' => true,
                'stock_quantity' => 5,
                'stock_status' => 'instock',
                'attributes' => [['id' => 1, 'name' => 'Size', 'option' => 'M']],
                'image' => null,
            ],
        ]),
    ]);

    $counters = importService()->import(defaultOptions());

    expect($counters['products'])->toBe(1);
    expect($counters['variants'])->toBe(2);

    $product = Product::where('slug', 'variant-tee')->first();
    expect($product->has_variants)->toBeTrue();
    expect($product->variants)->toHaveCount(2);

    $variantS = ProductVariant::where('sku', 'VT-001-S')->first();
    expect($variantS->stock)->toBe(10);
    expect($variantS->attributeValues)->toHaveCount(1);
    expect($variantS->attributeValues->first()->value)->toBe('S');
    expect(strlen($variantS->signature))->toBe(40);
});

// ──── Idempotency ────

it('is idempotent — running twice does not create duplicates', function () {
    Http::fake([
        ...emptyEndpoints(),
        '*/wc/v3/products?*' => wcResponse([
            simpleProduct(['slug' => 'idempotent-tee', 'sku' => 'ID-001']),
        ]),
    ]);

    importService()->import(defaultOptions());
    expect(Product::count())->toBe(1);

    importService()->import(defaultOptions());
    expect(Product::count())->toBe(1);
});

// ──── Dry Run ────

it('does not write to database in dry-run mode', function () {
    Http::fake([
        '*/wc/v3/products/categories*' => wcResponse([
            ['id' => 1, 'name' => 'Test Cat', 'slug' => 'test-cat', 'parent' => 0, 'description' => '', 'image' => null],
        ]),
        '*/wc/v3/products/tags*' => wcResponse([
            ['id' => 1, 'name' => 'Sale', 'slug' => 'sale'],
        ]),
        '*/wc/v3/products/attributes' => Http::response([], 200),
        '*/wc/v3/products?*' => wcResponse([
            simpleProduct(['slug' => 'dry-run-tee', 'sku' => 'DR-001']),
        ]),
    ]);

    importService()->import(defaultOptions(['dryRun' => true]));

    expect(Category::count())->toBe(0);
    expect(Label::count())->toBe(0);
    expect(Product::count())->toBe(0);
});

// ──── Edge Cases ────

it('handles null and empty SKUs without error', function () {
    Http::fake([
        ...emptyEndpoints(),
        '*/wc/v3/products?*' => wcResponse([
            simpleProduct(['slug' => 'no-sku', 'sku' => '']),
        ]),
    ]);

    $counters = importService()->import(defaultOptions());

    expect($counters['products'])->toBe(1);
    expect(Product::where('slug', 'no-sku')->first()->sku)->toBeNull();
});

it('attaches categories and labels to imported products', function () {
    Http::fake([
        '*/wc/v3/products/categories*' => wcResponse([
            ['id' => 5, 'name' => 'Shirts', 'slug' => 'shirts', 'parent' => 0, 'description' => '', 'image' => null],
        ]),
        '*/wc/v3/products/tags*' => wcResponse([
            ['id' => 3, 'name' => 'New', 'slug' => 'new'],
        ]),
        '*/wc/v3/products/attributes' => Http::response([], 200),
        '*/wc/v3/products?*' => wcResponse([
            simpleProduct([
                'slug' => 'tagged-shirt',
                'sku' => 'TS-001',
                'categories' => [['id' => 5, 'name' => 'Shirts', 'slug' => 'shirts']],
                'tags' => [['id' => 3, 'name' => 'New', 'slug' => 'new']],
            ]),
        ]),
    ]);

    importService()->import(defaultOptions());

    $product = Product::where('slug', 'tagged-shirt')->first();
    expect($product->categories)->toHaveCount(1);
    expect($product->categories->first()->slug)->toBe('shirts');
    expect($product->labels)->toHaveCount(1);
    expect($product->labels->first()->slug)->toBe('new');
});

// ──── Command ────

it('fails gracefully when WooCommerce config is missing', function () {
    config(['woocommerce.url' => '']);

    $this->artisan('import:woocommerce')
        ->expectsOutput('WooCommerce API credentials not configured. Set WC_URL, WC_CONSUMER_KEY, and WC_CONSUMER_SECRET in .env')
        ->assertExitCode(1);
});
