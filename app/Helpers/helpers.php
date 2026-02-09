<?php

use App\Models\Setting;

if (! function_exists('format_price')) {
    /**
     * Format a price value according to shop settings.
     * Outputs e.g. "19,99 €" for right position or "€19.99" for left position.
     */
    function format_price(float|int|string $amount): string
    {
        $amount = (float) $amount;
        $decimals = (int) config('shop.currency_decimals', 2);
        $decSep = Setting::get('currency_decimal_separator', config('shop.currency_decimal_separator', ','));
        $thousandsSep = Setting::get('currency_thousands_separator', config('shop.currency_thousands_separator', '.'));
        $symbol = Setting::get('currency_symbol', config('shop.currency_symbol', "\u{20AC}"));
        $position = Setting::get('currency_position', config('shop.currency_position', 'right'));

        $formatted = number_format($amount, $decimals, $decSep, $thousandsSep);

        return $position === 'right'
            ? "{$formatted} {$symbol}"
            : "{$symbol}{$formatted}";
    }
}

if (! function_exists('calculate_vat')) {
    /**
     * Calculate VAT amount from a gross or net price.
     *
     * @param  float  $price  The price (gross or net, depending on setting)
     * @param  float|null  $rate  Optional VAT rate override (percentage, e.g. 24.00)
     * @return array{net: float, vat: float, gross: float}
     */
    function calculate_vat(float $price, ?float $rate = null): array
    {
        $rate = $rate ?? (float) Setting::get('vat_rate', config('shop.vat_rate', 24.00));
        $pricesIncludeVat = filter_var(
            Setting::get('prices_include_vat', config('shop.prices_include_vat', true)),
            FILTER_VALIDATE_BOOLEAN
        );

        $multiplier = 1 + ($rate / 100);

        if ($pricesIncludeVat) {
            $gross = round($price, 2);
            $net = round($price / $multiplier, 2);
            $vat = round($gross - $net, 2);
        } else {
            $net = round($price, 2);
            $vat = round($price * ($rate / 100), 2);
            $gross = round($net + $vat, 2);
        }

        return compact('net', 'vat', 'gross');
    }
}

if (! function_exists('clean_html')) {
    /**
     * Sanitize HTML from the RichEditor to prevent XSS and layout breakage.
     * Only allows a safe subset of tags and strips everything else.
     */
    function clean_html(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        $allowed = '<p><br><strong><b><em><i><u><s><ul><ol><li><h2><h3><h4><a><blockquote><code><pre>';

        $clean = strip_tags($html, $allowed);

        // Strip dangerous attributes (on*, style, class for safety)
        /** @var string $clean */
        $clean = preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']/i', '', $clean);
        /** @var string $clean */
        $clean = preg_replace('/\s+style\s*=\s*["\'][^"\']*["\']/i', '', $clean);

        // Ensure <a> tags only have href with http(s) or mailto
        /** @var string $clean */
        $clean = preg_replace_callback(
            '/<a\s+([^>]*)>/i',
            function (array $matches): string {
                if (preg_match('/href\s*=\s*["\']((https?:\/\/|mailto:)[^"\']*)["\']/', $matches[1], $href)) {
                    return '<a href="'.e($href[1]).'" rel="nofollow noopener">';
                }

                return '<a>';
            },
            $clean
        );

        return $clean;
    }
}

if (! function_exists('shop_setting')) {
    /**
     * Get a shop setting value with fallback to config.
     */
    function shop_setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}
