<?php

namespace App\Console\Commands;

use App\Services\WooCommerceImportService;
use Illuminate\Console\Command;

class ImportWooCommerce extends Command
{
    protected $signature = 'import:woocommerce
        {--categories-only : Import only categories, tags, and attributes}
        {--products-only : Import only products (assumes categories/attributes exist)}
        {--dry-run : Show what would be imported without writing to DB}
        {--skip-images : Skip image download for faster testing}';

    protected $description = 'Import products, categories, and attributes from a WooCommerce store via REST API v3';

    public function handle(WooCommerceImportService $service): int
    {
        if (! config('woocommerce.url') || ! config('woocommerce.consumer_key') || ! config('woocommerce.consumer_secret')) {
            $this->error('WooCommerce API credentials not configured. Set WC_URL, WC_CONSUMER_KEY, and WC_CONSUMER_SECRET in .env');

            return self::FAILURE;
        }

        $this->info('Starting WooCommerce import from '.config('woocommerce.url'));
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE — no data will be written to the database.');
            $this->newLine();
        }

        $startTime = microtime(true);

        $counters = $service->import(
            options: [
                'dryRun' => (bool) $this->option('dry-run'),
                'skipImages' => (bool) $this->option('skip-images'),
                'categoriesOnly' => (bool) $this->option('categories-only'),
                'productsOnly' => (bool) $this->option('products-only'),
            ],
            command: $this,
        );

        $elapsed = round(microtime(true) - $startTime, 1);
        $peakMb = round(memory_get_peak_usage(true) / 1024 / 1024, 1);

        $this->newLine();
        $this->info("Import completed in {$elapsed}s (peak memory: {$peakMb} MB)");
        $this->newLine();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Categories', $counters['categories']],
                ['Labels (from tags)', $counters['labels']],
                ['Attributes', $counters['attributes']],
                ['Attribute Values', $counters['attribute_values']],
                ['Products', $counters['products']],
                ['Variants', $counters['variants']],
                ['Images', $counters['images']],
                ['Skipped', $counters['skipped']],
                ['Errors', $counters['errors']],
            ]
        );

        if ($counters['errors'] > 0) {
            $this->warn("{$counters['errors']} error(s) occurred. Check storage/logs/laravel.log for details.");
        }

        return $counters['errors'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
