<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Standard Project Database Migration: E-Commerce & Catalog
     * Scope: PROJECT DATABASE ONLY (Isolated per tenant project)
     */
    public function up(): void
    {
        if (! Schema::hasTable('product_categories')) {
            Schema::create('product_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->index();
                $table->text('description')->nullable();
                $table->string('image')->nullable();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->integer('level')->default(0);
                $table->string('path')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('brands')) {
            Schema::create('brands', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->index();
                $table->string('logo')->nullable();
                $table->text('description')->nullable();
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('products_enhanced')) {
            Schema::create('products_enhanced', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->index();
                $table->text('short_description')->nullable();
                $table->longText('description')->nullable();
                $table->string('sku')->nullable()->index();
                $table->decimal('price', 15, 2)->nullable();
                $table->decimal('sale_price', 15, 2)->nullable();
                $table->boolean('has_price')->default(true);
                $table->integer('stock_quantity')->default(0);
                $table->boolean('manage_stock')->default(false);
                $table->string('stock_status', 50)->default('in_stock');
                $table->string('featured_image')->nullable();
                $table->json('gallery')->nullable();
                $table->decimal('weight', 8, 2)->nullable();
                $table->string('dimensions')->nullable();
                $table->unsignedBigInteger('product_category_id')->nullable()->index();
                $table->unsignedBigInteger('brand_id')->nullable()->index();
                $table->string('status', 50)->default('published')->index();
                $table->boolean('is_featured')->default(false)->index();
                $table->json('badges')->nullable();
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->json('settings')->nullable();
                $table->unsignedBigInteger('views')->default(0);
                $table->decimal('rating_average', 3, 2)->default(5.00);
                $table->unsignedInteger('rating_count')->default(0);
                $table->string('product_type', 50)->default('simple');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('product_category_product')) {
            Schema::create('product_category_product', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('category_id')->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('brand_product')) {
            Schema::create('brand_product', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('brand_id')->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('order_number')->unique();
                $table->string('status', 50)->default('pending')->index();
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('shipping_amount', 15, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->string('currency', 10)->default('VND');
                $table->string('customer_name')->nullable();
                $table->string('customer_email')->nullable();
                $table->string('customer_phone')->nullable();
                $table->text('billing_address')->nullable();
                $table->text('shipping_address')->nullable();
                $table->string('payment_method')->nullable();
                $table->string('payment_status', 50)->default('pending');
                $table->timestamp('paid_at')->nullable();
                $table->text('customer_notes')->nullable();
                $table->text('internal_notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id')->index();
                $table->unsignedBigInteger('product_id')->nullable()->index();
                $table->unsignedBigInteger('product_variation_id')->nullable();
                $table->string('product_name');
                $table->string('product_sku')->nullable();
                $table->json('product_attributes')->nullable();
                $table->decimal('unit_price', 15, 2)->default(0);
                $table->integer('quantity')->default(1);
                $table->decimal('total_price', 15, 2)->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('product_reviews')) {
            Schema::create('product_reviews', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id')->index();
                $table->string('reviewer_name');
                $table->string('reviewer_email');
                $table->unsignedTinyInteger('rating')->default(5);
                $table->text('comment');
                $table->string('status', 50)->default('approved');
                $table->boolean('is_verified')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('brand_product');
        Schema::dropIfExists('product_category_product');
        Schema::dropIfExists('products_enhanced');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('product_categories');
    }
};
