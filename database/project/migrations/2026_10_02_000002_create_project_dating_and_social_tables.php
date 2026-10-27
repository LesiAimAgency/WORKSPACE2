<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Standard Project Database Migration: Dating & Social Network
     * Scope: PROJECT DATABASE ONLY (Standardized names, no project prefix)
     */
    public function up(): void
    {
        if (! Schema::hasTable('provinces')) {
            Schema::create('provinces', function (Blueprint $table) {
                $table->id();
                $table->string('code', 20)->nullable();
                $table->string('name', 100);
                $table->string('type', 50)->default('tinh');
                $table->timestamps();

                $table->index('name');
            });
        }

        if (! Schema::hasTable('profiles')) {
            Schema::create('profiles', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('display_name', 150);
                $table->string('slug', 150)->nullable()->index();
                $table->string('headline', 255)->nullable();
                $table->string('target_type', 100)->nullable();
                $table->enum('gender', ['male', 'female', 'other'])->default('female');
                $table->date('birthday')->nullable();
                $table->unsignedSmallInteger('age')->default(22)->index();
                $table->unsignedBigInteger('province_id')->nullable()->index();
                $table->string('province_name', 100)->nullable();
                $table->string('district_name', 100)->nullable();
                $table->string('marital_status', 100)->nullable();
                $table->string('occupation', 150)->nullable();
                $table->string('height', 50)->nullable();
                $table->string('weight', 50)->nullable();
                $table->string('education', 100)->nullable();
                $table->string('body_type', 100)->nullable();
                $table->text('about_me')->nullable();
                $table->text('looking_for')->nullable();
                $table->text('interests')->nullable();
                $table->string('personality', 150)->nullable();
                $table->string('lifestyle', 150)->nullable();
                $table->string('precious', 150)->nullable();
                $table->string('religion', 100)->nullable();
                $table->string('smoking', 100)->nullable();
                $table->string('drinking', 100)->nullable();
                $table->string('children', 100)->nullable();
                $table->string('avatar_url', 500)->nullable();
                $table->json('photos')->nullable();
                $table->boolean('is_featured')->default(false)->index();
                $table->boolean('is_online')->default(false)->index();
                $table->timestamp('last_active_at')->nullable();
                $table->string('status', 50)->default('active')->index();
                $table->string('privacy_option', 255)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('conversations')) {
            Schema::create('conversations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_one_id')->index();
                $table->unsignedBigInteger('user_two_id')->index();
                $table->timestamp('last_message_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('conversation_id')->index();
                $table->unsignedBigInteger('sender_id')->index();
                $table->unsignedBigInteger('recipient_id')->index();
                $table->string('subject', 255)->nullable();
                $table->text('body');
                $table->boolean('is_read')->default(false)->index();
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('social_connections')) {
            Schema::create('social_connections', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('target_profile_id')->index();
                $table->enum('relation_type', ['like', 'bookmark', 'block', 'contact'])->index();
                $table->timestamp('created_at')->useCurrent();

                $table->unique(['user_id', 'target_profile_id', 'relation_type'], 'user_target_rel_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('social_connections');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('profiles');
        Schema::dropIfExists('provinces');
    }
};
