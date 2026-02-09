<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'currency' => 'EUR',
            'currency_symbol' => "\u{20AC}",
            'currency_position' => 'right',
            'currency_decimal_separator' => ',',
            'currency_thousands_separator' => '.',
            'country' => 'GR',
            'locale' => 'el_GR',
            'vat_rate' => '24.00',
            'prices_include_vat' => 'true',
            'free_shipping_threshold' => '50.00',
            'flat_shipping_rate' => '3.50',
            'default_shipping_method_code' => 'standard',
            'low_stock_threshold' => '5',
            'reservation_ttl_minutes' => '30',
            'site_name' => 'Dressman',
        ];

        foreach ($settings as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
