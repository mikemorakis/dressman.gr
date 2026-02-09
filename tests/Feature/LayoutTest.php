<?php

use function Pest\Laravel\get;

it('renders the home page with layout', function () {
    $response = get('/');

    $response->assertStatus(200);
    $response->assertSee('PeShop', false);
    $response->assertSee('Skip to main content', false);
    $response->assertSee('id="main-content"', false);
});

it('includes SEO meta tags on the home page', function () {
    $response = get('/');

    $response->assertSee('<title>PeShop', false);
    $response->assertSee('og:title', false);
    $response->assertSee('og:type', false);
});

it('includes header navigation links', function () {
    $response = get('/');

    $response->assertSee('Home', false);
    $response->assertSee('Shop', false);
    $response->assertSee('Blog', false);
    $response->assertSee('About', false);
    $response->assertSee('Contact', false);
});

it('includes the footer', function () {
    $response = get('/');

    $response->assertSee('All rights reserved', false);
    $response->assertSee('Privacy Policy', false);
});

it('includes mobile menu markup', function () {
    $response = get('/');

    $response->assertSee('id="mobile-menu"', false);
    $response->assertSee('aria-label="Open navigation menu"', false);
});
