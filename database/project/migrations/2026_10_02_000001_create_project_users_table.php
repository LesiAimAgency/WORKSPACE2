<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Standard Project Database Migration: Users & Authentication
     * Scope: PROJECT DATABASE ONLY (Isolated per tenant project)
     */
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('username')->nullable()->unique();
                $table->string('email')->unique();
                $table->unsignedBigInteger('gold')->default(0);
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->string('avatar')->nullable();
                $table->string('phone')->nullable();
                $table->string('address')->nullable();
                $table->string('status', 50)->default('active');
                $table->timestamp('last_login_at')->nullable();
                $table->json('preferences')->nullable();
                $table->string('role', 50)->default('user');
                $table->string('department', 100)->nullable();
                $table->unsignedInteger('level')->default(1);
                $table->rememberToken();
                $table->text('two_factor_secret')->nullable();
                $table->text('two_factor_recovery_codes')->nullable();
                $table->timestamp('two_factor_confirmed_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
