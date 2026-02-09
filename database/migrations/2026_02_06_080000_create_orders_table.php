<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_number', 50)->unique();
            $table->string('status', 30)->default('pending');
            $table->string('payment_status', 30)->default('pending');

            $table->string('stripe_checkout_session_id', 255)->nullable()->unique();
            $table->string('stripe_payment_intent_id', 255)->nullable();

            $table->string('email', 255);
            $table->string('phone', 30)->nullable();

            $table->json('billing_address');
            $table->json('shipping_address');

            $table->decimal('subtotal', 10, 2);
            $table->decimal('vat_rate', 5, 2);
            $table->decimal('vat_amount', 10, 2);
            $table->decimal('shipping_amount', 10, 2);
            $table->decimal('total', 10, 2);
            $table->char('currency', 3)->default('EUR');
            $table->boolean('prices_include_vat')->default(true);

            $table->text('notes')->nullable();
            $table->timestamp('confirmation_sent_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
