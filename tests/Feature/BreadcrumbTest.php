<?php

use Illuminate\Support\Facades\Blade;

it('renders breadcrumb with items', function () {
    $html = Blade::render('<x-breadcrumb :items="$items" />', [
        'items' => [
            ['label' => 'Electronics', 'url' => '/shop/electronics'],
            ['label' => 'Laptops', 'url' => null],
        ],
    ]);

    expect($html)
        ->toContain('aria-label="Breadcrumb"')
        ->toContain('Electronics')
        ->toContain('Laptops')
        ->toContain('aria-current="page"')
        ->toContain('BreadcrumbList');
});

it('renders nothing when items are empty', function () {
    $html = Blade::render('<x-breadcrumb :items="$items" />', [
        'items' => [],
    ]);

    expect(trim($html))->toBe('');
});
