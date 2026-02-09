<?php

namespace App\Console\Commands;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportWordPressPosts extends Command
{
    protected $signature = 'import:wordpress-posts
        {--dry-run : Show what would be imported without writing to DB}
        {--skip-images : Skip featured image download}';

    protected $description = 'Import blog posts from dressman.gr WordPress REST API';

    private const WP_API = 'https://dressman.gr/wp-json/wp/v2';

    private const PER_PAGE = 50;

    /** @var array<int, BlogCategory> */
    private array $categoryMap = [];

    /** @var array<int, BlogTag> */
    private array $tagMap = [];

    private int $imported = 0;

    private int $skipped = 0;

    private int $errors = 0;

    private int $images = 0;

    public function handle(ImageService $imageService): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $skipImages = (bool) $this->option('skip-images');

        if ($dryRun) {
            $this->warn('DRY RUN — no data will be written.');
            $this->newLine();
        }

        $startTime = microtime(true);

        // Step 1: Import categories
        $this->info('Importing categories...');
        $this->importCategories($dryRun);

        // Step 2: Import tags
        $this->info('Importing tags...');
        $this->importTags($dryRun);

        // Step 3: Import posts
        $this->info('Importing posts...');
        $this->importPosts($imageService, $dryRun, $skipImages);

        $elapsed = round(microtime(true) - $startTime, 1);
        $peakMb = round(memory_get_peak_usage(true) / 1024 / 1024, 1);

        $this->newLine();
        $this->info("Import completed in {$elapsed}s (peak memory: {$peakMb} MB)");
        $this->newLine();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Categories', count($this->categoryMap)],
                ['Tags', count($this->tagMap)],
                ['Posts imported', $this->imported],
                ['Posts skipped', $this->skipped],
                ['Images downloaded', $this->images],
                ['Errors', $this->errors],
            ]
        );

        if ($this->errors > 0) {
            $this->warn("{$this->errors} error(s) occurred. Check storage/logs/laravel.log for details.");
        }

        return $this->errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function importCategories(bool $dryRun): void
    {
        $wpCategories = $this->fetchAll('/categories');

        foreach ($wpCategories as $wpCat) {
            $name = html_entity_decode($wpCat['name'], ENT_QUOTES, 'UTF-8');
            $slug = $wpCat['slug'];

            if ($dryRun) {
                $this->line("  [dry-run] Category: {$name} ({$slug})");

                continue;
            }

            $category = BlogCategory::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'description' => $wpCat['description'] ?? '',
                ]
            );

            $this->categoryMap[$wpCat['id']] = $category;
        }

        $this->info('  '.count($wpCategories).' categories processed.');
    }

    private function importTags(bool $dryRun): void
    {
        $wpTags = $this->fetchAll('/tags');

        foreach ($wpTags as $wpTag) {
            $name = html_entity_decode($wpTag['name'], ENT_QUOTES, 'UTF-8');
            $slug = $wpTag['slug'];

            if ($dryRun) {
                $this->line("  [dry-run] Tag: {$name} ({$slug})");

                continue;
            }

            $tag = BlogTag::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );

            $this->tagMap[$wpTag['id']] = $tag;
        }

        $this->info('  '.count($wpTags).' tags processed.');
    }

    private function importPosts(ImageService $imageService, bool $dryRun, bool $skipImages): void
    {
        $author = $dryRun ? null : User::where('is_admin', true)->first();

        $wpPosts = $this->fetchAll('/posts', ['_embed' => '']);

        foreach ($wpPosts as $wpPost) {
            $title = html_entity_decode($wpPost['title']['rendered'] ?? '', ENT_QUOTES, 'UTF-8');
            $slug = $wpPost['slug'];

            // Skip if already imported
            if (! $dryRun && BlogPost::where('slug', $slug)->exists()) {
                $this->line("  Skipped (exists): {$title}");
                $this->skipped++;

                continue;
            }

            if ($dryRun) {
                $this->line("  [dry-run] Post: {$title}");
                $this->imported++;

                continue;
            }

            try {
                DB::transaction(function () use ($wpPost, $title, $slug, $author, $imageService, $skipImages) {
                    // Extract content — strip Elementor wrappers but keep HTML
                    $body = $wpPost['content']['rendered'] ?? '';
                    $excerpt = strip_tags($wpPost['excerpt']['rendered'] ?? '');
                    $excerpt = trim(html_entity_decode($excerpt, ENT_QUOTES, 'UTF-8'));

                    // Download featured image
                    $imagePaths = [];
                    if (! $skipImages) {
                        $imagePaths = $this->downloadFeaturedImage($wpPost, $imageService);
                    }

                    $post = BlogPost::create([
                        'author_id' => $author?->id,
                        'title' => $title,
                        'slug' => $slug,
                        'excerpt' => Str::limit($excerpt, 500),
                        'body' => $body,
                        'featured_image_path_large' => $imagePaths['path_large'] ?? null,
                        'featured_image_path_medium' => $imagePaths['path_medium'] ?? null,
                        'featured_image_path_thumb' => $imagePaths['path_thumb'] ?? null,
                        'featured_image_width' => $imagePaths['width'] ?? null,
                        'featured_image_height' => $imagePaths['height'] ?? null,
                        'is_published' => $wpPost['status'] === 'publish',
                        'published_at' => $wpPost['date'] ?? now(),
                        'meta_title' => Str::limit($title, 70),
                        'meta_description' => Str::limit($excerpt, 160),
                    ]);

                    // Attach categories
                    $categoryIds = [];
                    foreach ($wpPost['categories'] ?? [] as $wpCatId) {
                        if (isset($this->categoryMap[$wpCatId])) {
                            $categoryIds[] = $this->categoryMap[$wpCatId]->id;
                        }
                    }
                    if ($categoryIds) {
                        $post->categories()->attach($categoryIds);
                    }

                    // Attach tags
                    $tagIds = [];
                    foreach ($wpPost['tags'] ?? [] as $wpTagId) {
                        if (isset($this->tagMap[$wpTagId])) {
                            $tagIds[] = $this->tagMap[$wpTagId]->id;
                        }
                    }
                    if ($tagIds) {
                        $post->tags()->attach($tagIds);
                    }

                    $this->imported++;
                    $this->line("  Imported: {$title}");
                });
            } catch (\Throwable $e) {
                $this->errors++;
                $this->error("  Failed: {$title} — {$e->getMessage()}");
                Log::error("WP import failed for post '{$slug}'", ['exception' => $e]);
            }
        }
    }

    /**
     * Download and process the featured image via ImageService.
     *
     * @param  array<string, mixed>  $wpPost
     * @return array{path_large?: string, path_medium?: string, path_thumb?: string, width?: int, height?: int}
     */
    private function downloadFeaturedImage(array $wpPost, ImageService $imageService): array
    {
        // Try to get featured image URL from _embedded
        $mediaUrl = null;

        $embedded = $wpPost['_embedded'] ?? [];
        $featuredMedia = $embedded['wp:featuredmedia'] ?? [];

        if (! empty($featuredMedia[0]['source_url'])) {
            $mediaUrl = $featuredMedia[0]['source_url'];
        }

        // Fallback: fetch from media endpoint if we have featured_media ID
        if (! $mediaUrl && ! empty($wpPost['featured_media'])) {
            try {
                $response = Http::withoutVerifying()->timeout(15)->get(self::WP_API.'/media/'.$wpPost['featured_media']);

                if ($response->successful()) {
                    $media = $response->json();
                    $mediaUrl = $media['source_url'] ?? null;
                }
            } catch (\Throwable $e) {
                Log::warning("Could not fetch media {$wpPost['featured_media']}: {$e->getMessage()}");
            }
        }

        if (! $mediaUrl) {
            return [];
        }

        try {
            $response = Http::withoutVerifying()->timeout(30)->get($mediaUrl);

            if (! $response->successful()) {
                $this->warn("    Image download failed ({$response->status()}): {$mediaUrl}");

                return [];
            }

            $contentType = $response->header('Content-Type') ?: 'image/jpeg';
            $extension = match (true) {
                str_contains($contentType, 'png') => 'png',
                str_contains($contentType, 'webp') => 'webp',
                default => 'jpg',
            };

            $tempPath = tempnam(sys_get_temp_dir(), 'wp_blog_');

            if ($tempPath === false) {
                return [];
            }

            file_put_contents($tempPath, $response->body());

            try {
                $uploadedFile = new UploadedFile(
                    path: $tempPath,
                    originalName: 'wp_import.'.$extension,
                    mimeType: $contentType,
                    error: UPLOAD_ERR_OK,
                    test: true,
                );

                $result = $imageService->process($uploadedFile, 'blog');
                $this->images++;

                return $result;
            } finally {
                @unlink($tempPath);
            }
        } catch (\Throwable $e) {
            $this->warn("    Image processing failed: {$e->getMessage()}");
            Log::warning("Blog image processing failed for {$mediaUrl}: {$e->getMessage()}");

            return [];
        }
    }

    /**
     * Fetch all items from a paginated WP REST API endpoint.
     *
     * @param  array<string, string>  $extraParams
     * @return list<array<string, mixed>>
     */
    private function fetchAll(string $endpoint, array $extraParams = []): array
    {
        $all = [];
        $page = 1;

        do {
            $params = array_merge([
                'per_page' => self::PER_PAGE,
                'page' => $page,
            ], $extraParams);

            $response = Http::withoutVerifying()->timeout(30)->get(self::WP_API.$endpoint, $params);

            if (! $response->successful()) {
                $this->error("API request failed: {$endpoint} (page {$page}) — HTTP {$response->status()}");

                break;
            }

            $items = $response->json();

            if (! is_array($items) || empty($items)) {
                break;
            }

            array_push($all, ...$items);

            $totalPages = (int) $response->header('X-WP-TotalPages', '1');
            $page++;
        } while ($page <= $totalPages);

        return $all;
    }
}
