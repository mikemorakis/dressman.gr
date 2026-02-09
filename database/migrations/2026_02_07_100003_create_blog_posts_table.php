<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->text('body');
            $table->string('featured_image_path_large', 500)->nullable();
            $table->string('featured_image_path_medium', 500)->nullable();
            $table->string('featured_image_path_thumb', 500)->nullable();
            $table->unsignedInteger('featured_image_width')->nullable();
            $table->unsignedInteger('featured_image_height')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_published', 'published_at']);
        });

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'])) {
            DB::statement('ALTER TABLE blog_posts ADD FULLTEXT blog_posts_search (title, excerpt, body)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
