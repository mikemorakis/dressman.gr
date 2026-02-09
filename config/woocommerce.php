<?php

return [
    'url' => env('WC_URL', ''),
    'consumer_key' => env('WC_CONSUMER_KEY', ''),
    'consumer_secret' => env('WC_CONSUMER_SECRET', ''),
    'per_page' => 100,
    'timeout' => 30,
    'retry_times' => 3,
    'retry_sleep_ms' => 1000,
    'verify_ssl' => env('WC_VERIFY_SSL', true),
];
