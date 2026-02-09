<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Shop Settings
    |--------------------------------------------------------------------------
    |
    | These are the fallback values used when the settings table is empty or
    | a key is missing. In production, all settings are read from the DB
    | and cached using the file cache driver.
    |
    */

    'currency' => 'EUR',
    'currency_symbol' => "\u{20AC}",
    'currency_position' => 'right', // 'left' or 'right'
    'currency_decimals' => 2,
    'currency_decimal_separator' => ',',
    'currency_thousands_separator' => '.',

    'country' => 'GR',
    'locale' => 'el_GR',

    'vat_rate' => 24.00,
    'prices_include_vat' => true,

    'free_shipping_threshold' => 50.00,
    'flat_shipping_rate' => 3.50,
    'default_shipping_method_code' => 'standard',

    'order_prefix' => 'DR',

    'low_stock_threshold' => 5,
    'reservation_ttl_minutes' => 30,

];
