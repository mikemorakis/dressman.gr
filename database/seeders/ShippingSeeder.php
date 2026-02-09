<?php

namespace Database\Seeders;

use App\Models\ShippingMethod;
use App\Models\ShippingRate;
use App\Models\ShippingZone;
use App\Models\ShippingZonePostalPrefix;
use Illuminate\Database\Seeder;

class ShippingSeeder extends Seeder
{
    public function run(): void
    {
        $standard = ShippingMethod::firstOrCreate(
            ['code' => 'standard'],
            [
                'name' => 'Standard Shipping',
                'is_active' => true,
                'sort_order' => 0,
            ]
        );

        // ── Shipping Zones (GR only) ──

        $attica = ShippingZone::firstOrCreate(
            ['code' => 'ATTICA'],
            [
                'name' => 'Αποστολή εντός Αττικής',
                'is_active' => true,
                'sort_order' => 0,
            ]
        );

        $nonAttica = ShippingZone::firstOrCreate(
            ['code' => 'NON_ATTICA'],
            [
                'name' => 'Αποστολή εκτός Αττικής',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        // ATTICA postal prefixes: 10-19
        foreach (range(10, 19) as $prefix) {
            ShippingZonePostalPrefix::firstOrCreate([
                'shipping_zone_id' => $attica->id,
                'postal_prefix' => (string) $prefix,
            ]);
        }

        // ── Rates ──

        // GR ATTICA: €3.50
        ShippingRate::firstOrCreate(
            [
                'shipping_method_id' => $standard->id,
                'country_code' => 'GR',
                'shipping_zone_id' => $attica->id,
            ],
            [
                'flat_amount' => 3.50,
                'is_active' => true,
            ]
        );

        // GR NON_ATTICA: €5.00
        ShippingRate::firstOrCreate(
            [
                'shipping_method_id' => $standard->id,
                'country_code' => 'GR',
                'shipping_zone_id' => $nonAttica->id,
            ],
            [
                'flat_amount' => 5.00,
                'is_active' => true,
            ]
        );

        // Cyprus: flat €5.00 (no zone)
        ShippingRate::firstOrCreate(
            [
                'shipping_method_id' => $standard->id,
                'country_code' => 'CY',
                'shipping_zone_id' => null,
                'region_code' => null,
                'min_subtotal' => null,
                'max_subtotal' => null,
            ],
            [
                'flat_amount' => 5.00,
                'is_active' => true,
            ]
        );
    }
}
