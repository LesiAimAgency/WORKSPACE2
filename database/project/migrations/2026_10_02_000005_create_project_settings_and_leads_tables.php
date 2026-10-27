<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Standard Project Database Migration: Settings, Leads & Inquiries
     * Scope: PROJECT DATABASE ONLY (Standard Schema)
     */
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->json('payload')->nullable();
                $table->string('type', 50)->default('string');
                $table->string('group', 50)->default('general')->index();
                $table->boolean('locked')->default(false);
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('contacts')) {
            Schema::create('contacts', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email');
                $table->string('phone', 50)->nullable();
                $table->string('subject', 255)->nullable();
                $table->text('message');
                $table->string('status', 50)->default('new')->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('email_subscribers')) {
            Schema::create('email_subscribers', function (Blueprint $table) {
                $table->id();
                $table->string('email')->unique();
                $table->string('status', 50)->default('active');
                $table->timestamp('subscribed_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('faqs')) {
            Schema::create('faqs', function (Blueprint $table) {
                $table->id();
                $table->string('question');
                $table->text('answer');
                $table->string('category', 100)->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('email_subscribers');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('settings');
    }
};
