<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->char('guest_token', 64)->nullable()->unique()->after('stripe_payment_intent_id');
        });

        Schema::table('pending_emails', function (Blueprint $table) {
            $table->timestamp('next_attempt_at')->nullable()->after('attempts');
            $table->index(['sent_at', 'attempts', 'next_attempt_at'], 'pending_emails_unsent_index');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('guest_token');
        });

        Schema::table('pending_emails', function (Blueprint $table) {
            $table->dropIndex('pending_emails_unsent_index');
            $table->dropColumn('next_attempt_at');
        });
    }
};
