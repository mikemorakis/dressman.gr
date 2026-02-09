<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingsSeeder::class,
            ShippingSeeder::class,
            AdminUserSeeder::class,
            CatalogSeeder::class,
            BlogSeeder::class,
        ]);
    }
}
