<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Standard Project Database Migration: Content Management (CMS)
     * Scope: PROJECT DATABASE ONLY (Isolated per tenant project)
     */
    public function up(): void
    {
        if (! Schema::hasTable('posts')) {
            Schema::create('posts', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->index();
                $table->text('excerpt')->nullable();
                $table->longText('content');
                $table->string('featured_image')->nullable();
                $table->string('post_type', 50)->default('post')->index();
                $table->string('template', 100)->nullable();
                $table->string('status', 50)->default('published')->index();
                $table->integer('order')->default(0)->index();
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->json('seo_data')->nullable();
                $table->json('meta_data')->nullable();
                $table->unsignedBigInteger('views')->default(0);
                $table->timestamp('published_at')->nullable();
                $table->unsignedBigInteger('author_id')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('post_categories')) {
            Schema::create('post_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('tags')) {
            Schema::create('tags', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('post_tag')) {
            Schema::create('post_tag', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('post_id')->index();
                $table->unsignedBigInteger('tag_id')->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('widgets')) {
            Schema::create('widgets', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('type', 50)->default('custom_html');
                $table->string('location', 50)->nullable();
                $table->longText('content')->nullable();
                $table->json('settings')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('menus')) {
            Schema::create('menus', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('location', 50)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('menu_items')) {
            Schema::create('menu_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('menu_id')->index();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->string('title');
                $table->string('url')->nullable();
                $table->string('target', 20)->default('_self');
                $table->string('icon')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('post_id')->nullable()->index();
                $table->string('reviewer_name');
                $table->string('reviewer_email')->nullable();
                $table->string('reviewer_avatar')->nullable();
                $table->string('reviewer_title')->nullable();
                $table->text('content');
                $table->unsignedTinyInteger('rating')->default(5);
                $table->string('image')->nullable();
                $table->string('status', 50)->default('approved');
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('widgets');
        Schema::dropIfExists('post_tag');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('post_categories');
        Schema::dropIfExists('posts');
    }
};
