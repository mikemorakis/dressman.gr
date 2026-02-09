<?php

it('formats price with EUR symbol on the right', function () {
    // Default config: right position, comma decimal, dot thousands
    $result = format_price(1299.50);
    expect($result)->toBe('1.299,50 €');
});

it('formats zero price correctly', function () {
    $result = format_price(0);
    expect($result)->toBe('0,00 €');
});

it('calculates VAT from gross price (prices include VAT)', function () {
    $result = calculate_vat(24.99, 24.00);

    expect($result['gross'])->toBe(24.99)
        ->and($result['net'])->toBe(20.15)
        ->and($result['vat'])->toBe(4.84);
});

// ──── clean_html sanitization ────

it('allows safe HTML tags through clean_html', function () {
    $html = '<p>Hello <strong>world</strong> and <em>universe</em></p><ul><li>item</li></ul>';
    $result = clean_html($html);

    expect($result)->toContain('<p>')
        ->toContain('<strong>')
        ->toContain('<em>')
        ->toContain('<ul>')
        ->toContain('<li>');
});

it('strips script tags via clean_html', function () {
    $html = '<p>Safe</p><script>alert("xss")</script>';
    $result = clean_html($html);

    expect($result)->toContain('<p>Safe</p>')
        ->not->toContain('<script>');
});

it('strips on-event handlers via clean_html', function () {
    $html = '<p onclick="alert(1)">Click me</p><a onmouseover="hack()">link</a>';
    $result = clean_html($html);

    expect($result)->not->toContain('onclick')
        ->not->toContain('onmouseover');
});

it('strips style attributes via clean_html', function () {
    $html = '<p style="color:red;position:fixed">Styled</p>';
    $result = clean_html($html);

    expect($result)->not->toContain('style=')
        ->toContain('Styled');
});

it('allows safe href in anchor tags via clean_html', function () {
    $html = '<a href="https://example.com">Link</a>';
    $result = clean_html($html);

    expect($result)->toContain('href=')
        ->toContain('https://example.com')
        ->toContain('rel="nofollow noopener"');
});

it('strips javascript href in anchor tags via clean_html', function () {
    $html = '<a href="javascript:alert(1)">Evil</a>';
    $result = clean_html($html);

    expect($result)->not->toContain('javascript:');
});

it('returns empty string for null input via clean_html', function () {
    expect(clean_html(null))->toBe('')
        ->and(clean_html(''))->toBe('');
});
