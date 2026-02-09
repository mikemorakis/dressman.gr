<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 255);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_method_id')->constrained()->cascadeOnDelete();
            $table->char('country_code', 2)->default('GR');
            $table->string('region_code', 10)->nullable();
            $table->decimal('min_subtotal', 10, 2)->nullable();
            $table->decimal('max_subtotal', 10, 2)->nullable();
            $table->decimal('flat_amount', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(
                ['shipping_method_id', 'country_code', 'region_code', 'is_active'],
                'shipping_rates_lookup_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_rates');
        Schema::dropIfExists('shipping_methods');
    }
};
