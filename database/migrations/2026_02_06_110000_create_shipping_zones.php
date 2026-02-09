<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 255);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('shipping_zone_postal_prefixes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')->constrained()->cascadeOnDelete();
            $table->string('postal_prefix', 10);

            $table->index(['shipping_zone_id', 'postal_prefix'], 'shipping_zone_prefixes_lookup');
        });

        Schema::table('shipping_rates', function (Blueprint $table) {
            $table->foreignId('shipping_zone_id')->nullable()->after('country_code')
                ->constrained('shipping_zones')->nullOnDelete();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_zone_code', 50)->nullable()->after('shipping_label');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('shipping_zone_code');
        });

        Schema::table('shipping_rates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shipping_zone_id');
        });

        Schema::dropIfExists('shipping_zone_postal_prefixes');
        Schema::dropIfExists('shipping_zones');
    }
};
