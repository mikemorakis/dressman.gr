<?php

use function Pest\Laravel\get;

it('has html lang attribute set', function () {
    $response = get('/');

    $response->assertSee('<html lang="en"', false);
});

it('has skip link that targets main content', function () {
    $response = get('/');
    $content = $response->getContent();

    // Skip link href matches main landmark id
    expect($content)
        ->toContain('href="#main-content"')
        ->toContain('Skip to main content');

    // Main landmark exists with matching id
    expect($content)->toContain('<main id="main-content"');
});

it('has exactly one main landmark', function () {
    $response = get('/');
    $content = $response->getContent();

    expect(substr_count($content, '<main '))->toBe(1);
});

it('does not load Livewire on non-search pages', function () {
    $response = get('/');
    $content = $response->getContent();

    // Livewire only loaded on search page, not globally
    expect($content)->not->toContain('@livewireScripts');
});

it('has proper aria labels on navigation', function () {
    $response = get('/');
    $content = $response->getContent();

    expect($content)
        ->toContain('aria-label="Main navigation"')
        ->toContain('aria-label="Mobile navigation"');
});
