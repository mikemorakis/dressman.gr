<?php

namespace App\Services;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Label;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WooCommerceImportService
{
    /** @var array{categories: int, labels: int, attributes: int, attribute_values: int, products: int, variants: int, images: int, skipped: int, errors: int} */
    private array $counters;

    private bool $dryRun = false;

    private bool $skipImages = false;

    private ?Command $command = null;

    /** @var array<string, Category> */
    private array $categoryMap = [];

    /** @var array<string, Label> */
    private array $labelMap = [];

    /** @var array<string, Attribute> */
    private array $attributeMap = [];

    /** @var array<string, AttributeValue> */
    private array $attributeValueMap = [];

    public function __construct(
        private readonly ImageService $imageService,
    ) {}

    /**
     * Run the full WooCommerce import.
     *
     * @param  array{dryRun: bool, skipImages: bool, categoriesOnly: bool, productsOnly: bool}  $options
     * @return array{categories: int, labels: int, attributes: int, attribute_values: int, products: int, variants: int, images: int, skipped: int, errors: int}
     */
    public function import(array $options, ?Command $command = null): array
    {
        $this->counters = [
            'categories' => 0,
            'labels' => 0,
            'attributes' => 0,
            'attribute_values' => 0,
            'products' => 0,
            'variants' => 0,
            'images' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];

        $this->dryRun = $options['dryRun'];
        $this->skipImages = $options['skipImages'];
        $this->command = $command;

        if (! $options['productsOnly']) {
            $this->importCategories();
            $this->importTags();
            $this->importAttributes();
        }

        if (! $options['categoriesOnly']) {
            $this->loadExistingMaps();
            $this->importProducts();
        }

        return $this->counters;
    }

    // ──── API Methods ────

    /**
     * @param  array<string, mixed>  $query
     */
    private function apiGet(string $endpoint, array $query = []): Response
    {
        $baseUrl = rtrim((string) config('woocommerce.url'), '/');
        $url = "{$baseUrl}/wp-json/wc/v3/{$endpoint}";

        return Http::withBasicAuth(
            (string) config('woocommerce.consumer_key'),
            (string) config('woocommerce.consumer_secret'),
        )
            ->withOptions(['verify' => (bool) config('woocommerce.verify_ssl', true)])
            ->timeout((int) config('woocommerce.timeout', 30))
            ->retry(
                (int) config('woocommerce.retry_times', 3),
                (int) config('woocommerce.retry_sleep_ms', 1000),
            )
            ->get($url, $query);
    }

    /**
     * Paginate through all pages of a WC endpoint, yielding one item at a time.
     *
     * @param  array<string, mixed>  $query
     * @return \Generator<int, array<string, mixed>>
     */
    private function paginateAll(string $endpoint, array $query = []): \Generator
    {
        $page = 1;
        $perPage = (int) config('woocommerce.per_page', 100);
        $index = 0;

        do {
            $response = $this->apiGet($endpoint, array_merge($query, [
                'page' => $page,
                'per_page' => $perPage,
            ]));

            $totalPages = (int) $response->header('X-WP-TotalPages') ?: 1;

            /** @var list<array<string, mixed>> $items */
            $items = $response->json();

            foreach ($items as $item) {
                yield $index => $item;
                $index++;
            }

            $page++;
        } while ($page <= $totalPages);
    }

    // ──── Categories ────

    private function importCategories(): void
    {
        $this->info('Importing categories...');

        /** @var list<array<string, mixed>> $wcCategories */
        $wcCategories = [];

        foreach ($this->paginateAll('products/categories') as $cat) {
            $wcCategories[] = $cat;
        }

        // Sort: root categories first (parent=0), then children
        usort($wcCategories, fn (array $a, array $b) => ($a['parent'] ?? 0) <=> ($b['parent'] ?? 0));

        /** @var array<int, string> $wcIdToSlug */
        $wcIdToSlug = [];

        foreach ($wcCategories as $index => $wc) {
            $slug = $wc['slug'];
            $wcIdToSlug[$wc['id']] = $slug;

            $parentId = null;
            if (($wc['parent'] ?? 0) > 0 && isset($wcIdToSlug[$wc['parent']])) {
                $parent = $this->categoryMap[$wcIdToSlug[$wc['parent']]] ?? null;
                $parentId = $parent?->id;
            }

            $description = clean_html($wc['description'] ?? '');

            $name = html_entity_decode($wc['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8');

            if (! $this->dryRun) {
                $category = Category::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $name,
                        'parent_id' => $parentId,
                        'description' => $description ?: null,
                        'sort_order' => $index,
                        'is_visible' => true,
                        'meta_title' => $name,
                        'meta_description' => $description ? Str::limit(strip_tags($description), 490) : null,
                    ]
                );

                $this->categoryMap[$slug] = $category;
            }

            $this->counters['categories']++;
            $this->output("  Category: {$wc['name']}");
        }

        $this->info("  {$this->counters['categories']} categories imported.");
    }

    // ──── Tags → Labels ────

    private function importTags(): void
    {
        $this->info('Importing tags as labels...');

        foreach ($this->paginateAll('products/tags') as $wc) {
            $slug = $wc['slug'];

            $color = match (strtolower($wc['name'])) {
                'sale', 'έκπτωση', 'προσφορά' => '#ef4444',
                'new', 'νέο', 'new arrival' => '#22c55e',
                'best seller', 'δημοφιλές' => '#f59e0b',
                default => '#000000',
            };

            $name = html_entity_decode($wc['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8');

            if (! $this->dryRun) {
                $label = Label::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $name,
                        'color' => $color,
                    ]
                );

                $this->labelMap[$slug] = $label;
            }

            $this->counters['labels']++;
            $this->output("  Label: {$wc['name']} ({$color})");
        }

        $this->info("  {$this->counters['labels']} labels imported.");
    }

    // ──── Attributes ────

    private function importAttributes(): void
    {
        $this->info('Importing attributes...');

        $response = $this->apiGet('products/attributes');

        /** @var list<array<string, mixed>> $wcAttributes */
        $wcAttributes = $response->json();

        foreach ($wcAttributes as $wc) {
            $name = html_entity_decode($wc['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $nameLower = strtolower($name);
            $type = (str_contains($nameLower, 'color') || str_contains($nameLower, 'colour') || str_contains($nameLower, 'χρώμα')) ? 'color' : 'select';

            if (! $this->dryRun) {
                $attribute = Attribute::updateOrCreate(
                    ['name' => $name],
                    ['type' => $type]
                );

                $this->attributeMap[$nameLower] = $attribute;
            }

            $this->counters['attributes']++;
            $this->output("  Attribute: {$name} ({$type})");

            // Import terms for this attribute
            foreach ($this->paginateAll("products/attributes/{$wc['id']}/terms") as $sortOrder => $term) {
                $termName = html_entity_decode($term['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8');

                if (! $this->dryRun && isset($attribute)) {
                    $attrValue = AttributeValue::updateOrCreate(
                        ['attribute_id' => $attribute->id, 'value' => $termName],
                        ['sort_order' => $sortOrder]
                    );

                    $this->attributeValueMap["{$attribute->id}:{$term['name']}"] = $attrValue;
                }

                $this->counters['attribute_values']++;
            }
        }

        $this->info("  {$this->counters['attributes']} attributes, {$this->counters['attribute_values']} values imported.");
    }

    // ──── Products ────

    /**
     * Load existing categories/labels/attributes into lookup maps (for --products-only mode).
     */
    private function loadExistingMaps(): void
    {
        if (empty($this->categoryMap)) {
            foreach (Category::all() as $cat) {
                $this->categoryMap[$cat->slug] = $cat;
            }
        }

        if (empty($this->labelMap)) {
            foreach (Label::all() as $label) {
                $this->labelMap[$label->slug] = $label;
            }
        }

        if (empty($this->attributeMap)) {
            foreach (Attribute::with('values')->get() as $attr) {
                $this->attributeMap[strtolower($attr->name)] = $attr;

                foreach ($attr->values as $val) {
                    $this->attributeValueMap["{$attr->id}:{$val->value}"] = $val;
                }
            }
        }
    }

    private function importProducts(): void
    {
        $this->info('Importing products...');

        foreach ($this->paginateAll('products', ['status' => 'any']) as $index => $wcProduct) {
            try {
                $this->importSingleProduct($wcProduct);
            } catch (\Throwable $e) {
                Log::error("WC Import: Failed to import product {$wcProduct['slug']}", [
                    'wc_id' => $wcProduct['id'],
                    'error' => $e->getMessage(),
                ]);
                $this->counters['errors']++;
                $this->output("  ERROR: {$wcProduct['name']} — {$e->getMessage()}");
            }

            if ($index > 0 && $index % 25 === 0) {
                gc_collect_cycles();
            }
        }

        $this->info("  {$this->counters['products']} products, {$this->counters['variants']} variants, {$this->counters['images']} images imported.");
    }

    /**
     * @param  array<string, mixed>  $wcProduct
     */
    private function importSingleProduct(array $wcProduct): void
    {
        $slug = $wcProduct['slug'];
        $wcProduct['name'] = html_entity_decode($wcProduct['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $prices = $this->mapPrices($wcProduct);
        $stock = $this->mapStock($wcProduct);
        $sku = $this->resolveProductSku($wcProduct['sku'] ?? null, $slug);
        $shortDesc = clean_html($wcProduct['short_description'] ?? '');

        if ($this->dryRun) {
            $this->counters['products']++;
            $this->output("  Product: {$wcProduct['name']} [{$wcProduct['type']}]");

            return;
        }

        $product = DB::transaction(function () use ($wcProduct, $slug, $prices, $stock, $sku, $shortDesc): Product {
            $product = Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $wcProduct['name'],
                    'sku' => $sku,
                    'description' => clean_html($wcProduct['description'] ?? ''),
                    'short_description' => Str::limit(strip_tags($shortDesc), 497),
                    ...$prices,
                    ...$stock,
                    'has_variants' => $wcProduct['type'] === 'variable',
                    'is_active' => $wcProduct['status'] === 'publish',
                    'is_featured' => $wcProduct['featured'] ?? false,
                    'weight' => $this->toDecimalOrNull($wcProduct['weight'] ?? null),
                    'meta_title' => $wcProduct['name'],
                    'meta_description' => Str::limit(strip_tags($shortDesc), 497) ?: null,
                    'published_at' => $wcProduct['status'] === 'publish' ? now() : null,
                ]
            );

            // Attach categories
            $categoryIds = [];
            foreach ($wcProduct['categories'] ?? [] as $wcCat) {
                if (isset($this->categoryMap[$wcCat['slug']])) {
                    $categoryIds[] = $this->categoryMap[$wcCat['slug']]->id;
                }
            }
            $product->categories()->sync($categoryIds);

            // Attach labels (from WC tags)
            $labelIds = [];
            foreach ($wcProduct['tags'] ?? [] as $wcTag) {
                if (isset($this->labelMap[$wcTag['slug']])) {
                    $labelIds[] = $this->labelMap[$wcTag['slug']]->id;
                }
            }
            $product->labels()->sync($labelIds);

            return $product;
        });

        $this->counters['products']++;
        $this->output("  Product: {$wcProduct['name']} [{$wcProduct['type']}]");

        // Images (outside transaction to avoid long locks)
        $this->importProductImages($product, $wcProduct['images'] ?? []);

        // Variants
        if ($wcProduct['type'] === 'variable') {
            $this->importVariants($product, $wcProduct);

            // Set parent product price to min variant price (WC leaves parent price empty)
            $minPrice = $product->variants()->where('price', '>', 0)->min('price');
            if ($minPrice !== null && (float) $product->price === 0.0) {
                $product->update(['price' => $minPrice]);
            }
        }
    }

    // ──── Images ────

    /**
     * @param  list<array<string, mixed>>  $wcImages
     */
    private function importProductImages(Product $product, array $wcImages): void
    {
        if ($this->skipImages || empty($wcImages)) {
            return;
        }

        // Delete existing images for idempotency
        foreach ($product->images as $existingImage) {
            $this->imageService->delete($existingImage->only(['path_large', 'path_medium', 'path_thumb']));
            $existingImage->delete();
        }

        foreach ($wcImages as $sortOrder => $wcImage) {
            try {
                $imageData = $this->downloadAndProcessImage($wcImage['src']);

                if ($imageData === null) {
                    continue;
                }

                ProductImage::create([
                    'product_id' => $product->id,
                    'path_large' => $imageData['path_large'],
                    'path_medium' => $imageData['path_medium'],
                    'path_thumb' => $imageData['path_thumb'],
                    'alt_text' => ($wcImage['alt'] ?? '') ?: $product->name,
                    'width' => $imageData['width'],
                    'height' => $imageData['height'],
                    'sort_order' => $sortOrder,
                ]);

                $this->counters['images']++;
            } catch (\Throwable $e) {
                Log::warning("WC Import: Failed to download image for product {$product->slug}", [
                    'src' => $wcImage['src'],
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Download image from URL and process via ImageService.
     *
     * @return array{path_large: string, path_medium: string, path_thumb: string, width: int, height: int}|null
     */
    private function downloadAndProcessImage(string $url): ?array
    {
        $response = Http::withOptions(['verify' => (bool) config('woocommerce.verify_ssl', true)])
            ->timeout(15)
            ->get($url);

        if (! $response->successful()) {
            Log::warning("WC Import: Image download failed (HTTP {$response->status()})", ['url' => $url]);

            return null;
        }

        $contentType = $response->header('Content-Type') ?: 'image/jpeg';
        $extension = match (true) {
            str_contains($contentType, 'png') => 'png',
            str_contains($contentType, 'webp') => 'webp',
            default => 'jpg',
        };

        $tempPath = tempnam(sys_get_temp_dir(), 'wc_img_');

        if ($tempPath === false) {
            return null;
        }

        file_put_contents($tempPath, $response->body());

        try {
            $uploadedFile = new UploadedFile(
                path: $tempPath,
                originalName: 'wc_import.'.$extension,
                mimeType: $contentType,
                error: UPLOAD_ERR_OK,
                test: true,
            );

            return $this->imageService->process($uploadedFile, 'products');
        } finally {
            @unlink($tempPath);
        }
    }

    // ──── Variants ────

    /**
     * @param  array<string, mixed>  $wcProduct
     */
    private function importVariants(Product $product, array $wcProduct): void
    {
        // Collect all variations first to detect shared SKUs
        /** @var list<array<string, mixed>> $allVariations */
        $allVariations = [];
        foreach ($this->paginateAll("products/{$wcProduct['id']}/variations") as $wcVariation) {
            $allVariations[] = $wcVariation;
        }

        // Detect if multiple variations share the same SKU
        $skuCounts = [];
        foreach ($allVariations as $v) {
            $sku = ($v['sku'] ?? '') !== '' ? $v['sku'] : '';
            if ($sku !== '') {
                $skuCounts[$sku] = ($skuCounts[$sku] ?? 0) + 1;
            }
        }
        $sharedSkus = array_filter($skuCounts, fn (int $count): bool => $count > 1);

        foreach ($allVariations as $wcVariation) {
            try {
                $this->importSingleVariant($product, $wcVariation, $sharedSkus);
            } catch (\Throwable $e) {
                Log::error("WC Import: Failed to import variant for product {$product->slug}", [
                    'wc_variation_id' => $wcVariation['id'],
                    'error' => $e->getMessage(),
                ]);
                $this->counters['errors']++;
            }
        }
    }

    /**
     * @param  array<string, mixed>  $wcVariation
     * @param  array<string, int>  $sharedSkus  SKUs that appear on multiple variations
     */
    private function importSingleVariant(Product $product, array $wcVariation, array $sharedSkus): void
    {
        $attributeValueIds = $this->resolveVariantAttributeValueIds($wcVariation['attributes'] ?? []);
        $signature = ProductVariant::generateSignature($attributeValueIds);

        // Resolve variant SKU — append attribute options when WC uses the same
        // SKU for multiple variations of the same product
        $wcSku = ($wcVariation['sku'] ?? '') !== '' ? (string) $wcVariation['sku'] : '';

        if ($wcSku === '' || isset($sharedSkus[$wcSku])) {
            /** @var list<array{name: string, option: string}> $attrs */
            $attrs = $wcVariation['attributes'] ?? [];
            $optionsSuffix = collect($attrs)
                ->pluck('option')
                ->map(fn (string $o): string => Str::slug($o))
                ->implode('-');

            $baseSku = $wcSku !== '' ? $wcSku : $product->slug;
            $variantSku = Str::limit("{$baseSku}-{$optionsSuffix}", 100, '');
        } else {
            $variantSku = $wcSku;
        }

        // Price mapping
        $variantPrice = $this->mapVariantPrice($wcVariation);
        $stock = $this->mapStock($wcVariation);

        $variant = ProductVariant::updateOrCreate(
            ['product_id' => $product->id, 'signature' => $signature],
            [
                'sku' => $variantSku,
                'price' => $variantPrice,
                'stock' => $stock['stock'],
                'is_active' => ($wcVariation['stock_status'] ?? 'instock') !== 'outofstock',
            ]
        );

        $variant->attributeValues()->sync($attributeValueIds);
        $this->counters['variants']++;
    }

    /**
     * Resolve WC variation attributes to PeShop AttributeValue IDs.
     *
     * @param  list<array<string, mixed>>  $wcAttributes
     * @return list<int>
     */
    private function resolveVariantAttributeValueIds(array $wcAttributes): array
    {
        $ids = [];

        foreach ($wcAttributes as $wcAttr) {
            $attrName = strtolower((string) $wcAttr['name']);
            $optionValue = (string) $wcAttr['option'];

            $attribute = $this->attributeMap[$attrName] ?? null;

            if ($attribute === null) {
                $attribute = Attribute::firstOrCreate(
                    ['name' => $wcAttr['name']],
                    ['type' => (str_contains($attrName, 'color') || str_contains($attrName, 'colour') || str_contains($attrName, 'χρώμα')) ? 'color' : 'select']
                );
                $this->attributeMap[$attrName] = $attribute;
            }

            $cacheKey = "{$attribute->id}:{$optionValue}";
            if (! isset($this->attributeValueMap[$cacheKey])) {
                $this->attributeValueMap[$cacheKey] = AttributeValue::firstOrCreate(
                    ['attribute_id' => $attribute->id, 'value' => $optionValue],
                    ['sort_order' => 0]
                );
            }

            $ids[] = $this->attributeValueMap[$cacheKey]->id;
        }

        return $ids;
    }

    // ──── Price / Stock Helpers ────

    /**
     * @param  array<string, mixed>  $wc
     * @return array{price: float, compare_price: float|null}
     */
    private function mapPrices(array $wc): array
    {
        $regularPrice = $this->toDecimalOrNull($wc['regular_price'] ?? null);
        $salePrice = $this->toDecimalOrNull($wc['sale_price'] ?? null);

        if ($salePrice !== null && $salePrice > 0) {
            return [
                'price' => $salePrice,
                'compare_price' => $regularPrice,
            ];
        }

        return [
            'price' => $regularPrice ?? 0,
            'compare_price' => null,
        ];
    }

    /**
     * @param  array<string, mixed>  $wc
     * @return array{stock: int}
     */
    private function mapStock(array $wc): array
    {
        if (($wc['manage_stock'] ?? false) && ($wc['stock_quantity'] ?? null) !== null) {
            return ['stock' => max(0, (int) $wc['stock_quantity'])];
        }

        if (($wc['stock_status'] ?? '') === 'instock') {
            return ['stock' => 1];
        }

        return ['stock' => 0];
    }

    /**
     * @param  array<string, mixed>  $wcVariation
     */
    private function mapVariantPrice(array $wcVariation): ?float
    {
        $salePrice = $this->toDecimalOrNull($wcVariation['sale_price'] ?? null);
        $regularPrice = $this->toDecimalOrNull($wcVariation['regular_price'] ?? null);

        if ($salePrice !== null && $salePrice > 0) {
            return $salePrice;
        }

        return $regularPrice;
    }

    private function toDecimalOrNull(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
    }

    private function resolveProductSku(?string $sku, string $slug): ?string
    {
        if ($sku === null || $sku === '') {
            return null;
        }

        // Check if SKU exists for a different product
        $existing = Product::withTrashed()->where('sku', $sku)->where('slug', '!=', $slug)->first();

        if ($existing !== null) {
            Log::warning("WC Import: SKU collision — '{$sku}' already used by product '{$existing->slug}', appending suffix.");

            return Str::limit("{$sku}-{$slug}", 100, '');
        }

        return $sku;
    }

    // ──── Output Helpers ────

    private function output(string $message): void
    {
        $this->command?->line($message);
    }

    private function info(string $message): void
    {
        $this->command?->info($message);
    }
}
