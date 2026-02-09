<?php

use App\Services\ImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

it('processes an uploaded image into 3 sizes with dimensions', function () {
    $service = new ImageService;
    $file = UploadedFile::fake()->image('product.jpg', 2000, 1500);

    $result = $service->process($file, 'products');

    expect($result)->toHaveKeys(['path_large', 'path_medium', 'path_thumb', 'width', 'height']);

    foreach (['path_large', 'path_medium', 'path_thumb'] as $key) {
        Storage::disk('public')->assertExists($result[$key]);
        expect($result[$key])->toStartWith('products/');
        expect($result[$key])->toEndWith('.jpg');
    }

    // Large image should be scaled down to max 1200px width
    expect($result['width'])->toBeLessThanOrEqual(1200);
    expect($result['width'])->toBeGreaterThan(0);
    expect($result['height'])->toBeGreaterThan(0);
});

it('processes a PNG image', function () {
    $service = new ImageService;
    $file = UploadedFile::fake()->image('product.png', 800, 600);

    $result = $service->process($file, 'products');

    foreach (['path_large', 'path_medium', 'path_thumb'] as $key) {
        expect($result[$key])->toEndWith('.png');
    }
});

it('rejects non-image files', function () {
    $service = new ImageService;
    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    expect(fn () => $service->process($file))->toThrow(
        \InvalidArgumentException::class,
        'Invalid image type'
    );
});

it('rejects files exceeding max size', function () {
    $service = new ImageService;
    // Create a fake file > 5MB
    $file = UploadedFile::fake()->create('huge.jpg', 6000, 'image/jpeg');

    expect(fn () => $service->process($file))->toThrow(
        \InvalidArgumentException::class,
        'Image file too large'
    );
});

it('deletes all 3 sizes', function () {
    $service = new ImageService;
    $file = UploadedFile::fake()->image('product.jpg', 800, 600);

    $result = $service->process($file, 'products');

    foreach (['path_large', 'path_medium', 'path_thumb'] as $key) {
        Storage::disk('public')->assertExists($result[$key]);
    }

    $service->delete($result);

    foreach (['path_large', 'path_medium', 'path_thumb'] as $key) {
        Storage::disk('public')->assertMissing($result[$key]);
    }
});

it('reports WebP support based on GD info', function () {
    // Simply verify the method runs without error and returns a boolean
    expect(ImageService::supportsWebP())->toBeBool();
});

it('includes WebP in allowed mimes only when supported', function () {
    $service = new ImageService;
    $mimes = $service->allowedMimes();

    expect($mimes)->toContain('image/jpeg');
    expect($mimes)->toContain('image/png');

    if (ImageService::supportsWebP()) {
        expect($mimes)->toContain('image/webp');
    } else {
        expect($mimes)->not->toContain('image/webp');
    }
});
