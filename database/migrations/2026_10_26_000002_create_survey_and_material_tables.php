<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Surveys Table
        if (! Schema::hasTable('surveys')) {
            Schema::create('surveys', function (Blueprint $table) {
                $table->id();
                $table->string('code', 100)->unique();
                $table->string('title');
                $table->string('subtitle')->nullable();
                $table->text('intro_note')->nullable();
                $table->text('thank_you_note')->nullable();
                $table->string('footer_text')->nullable();
                $table->boolean('is_active')->default(true);
                $table->json('settings')->nullable();
                $table->timestamps();
            });
        }

        // 2. Survey Questions Table
        if (! Schema::hasTable('survey_questions')) {
            Schema::create('survey_questions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('survey_id')->constrained('surveys')->cascadeOnDelete();
                $table->string('section', 50)->default('A'); // A, B, NPS, OPEN
                $table->string('code', 50); // A1, A2, B1, B5...
                $table->text('question_text');
                $table->string('question_type', 50)->default('rating_5'); // rating_5, rating_10_nps, text, textarea, select, radio, checkbox
                $table->boolean('is_required')->default(true);
                $table->integer('sort_order')->default(0);
                $table->json('options')->nullable();
                $table->timestamps();

                $table->index(['survey_id', 'section', 'sort_order']);
            });
        }

        // 3. Survey Invitations Table
        if (! Schema::hasTable('survey_invitations')) {
            Schema::create('survey_invitations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('survey_id')->constrained('surveys')->cascadeOnDelete();
                $table->string('recipient_email');
                $table->string('recipient_name')->nullable();
                $table->string('project_name')->nullable();
                $table->string('token', 64)->unique();
                $table->enum('status', ['pending', 'sent', 'failed', 'responded'])->default('pending');
                $table->unsignedTinyInteger('attempts')->default(0);
                $table->text('error_message')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamp('responded_at')->nullable();
                $table->timestamps();

                $table->index(['survey_id', 'status']);
            });
        }

        // 4. Survey Responses Table
        if (! Schema::hasTable('survey_responses')) {
            Schema::create('survey_responses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('survey_id')->constrained('surveys')->cascadeOnDelete();
                $table->foreignId('invitation_id')->nullable()->constrained('survey_invitations')->nullOnDelete();
                $table->string('customer_name');
                $table->string('project_name');
                $table->date('handover_date')->nullable();
                $table->string('pm_name')->nullable();
                $table->string('customer_email')->nullable();
                $table->string('customer_phone')->nullable();
                $table->decimal('score_a', 3, 1)->nullable();
                $table->decimal('score_b', 3, 1)->nullable();
                $table->unsignedTinyInteger('nps_score')->nullable();
                $table->text('open_feedback_best')->nullable();
                $table->text('open_feedback_improve')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->string('status', 50)->default('completed');
                $table->timestamp('submitted_at')->nullable();
                $table->timestamps();

                $table->index(['survey_id', 'created_at']);
                $table->index('customer_name');
                $table->index('project_name');
            });
        }

        // 5. Survey Answers Table
        if (! Schema::hasTable('survey_answers')) {
            Schema::create('survey_answers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('response_id')->constrained('survey_responses')->cascadeOnDelete();
                $table->foreignId('question_id')->nullable()->constrained('survey_questions')->nullOnDelete();
                $table->string('question_code', 50);
                $table->smallInteger('rating_value')->nullable();
                $table->text('text_value')->nullable();
                $table->timestamps();

                $table->index(['response_id', 'question_code']);
            });
        }

        // 6. Materials Table (Material Lab)
        if (! Schema::hasTable('materials')) {
            Schema::create('materials', function (Blueprint $table) {
                $table->id();
                $table->string('code', 100)->unique();
                $table->string('name');
                $table->string('brand');
                $table->string('category', 100);
                $table->string('origin', 50)->default('Vietnam'); // Vietnam, Import, Both
                $table->string('origin_label')->nullable();
                $table->string('availability', 100)->default('Stock'); // Stock, 2-3 weeks, 6-8 weeks, Any
                $table->integer('durability_years')->default(5);
                $table->string('cost_label', 100)->nullable();
                $table->tinyInteger('budget_level')->default(2); // 1: Tiết kiệm, 2: Cân bằng, 3: Cao cấp, 4: Siêu cao cấp
                $table->string('finish')->nullable();
                $table->string('tech_specs')->nullable();
                $table->string('application')->nullable();
                $table->json('suitable_locations')->nullable(); // ["Floor", "Wall"]
                $table->text('tech_description')->nullable(); // AI / standard tech explanation
                $table->text('experience_description')->nullable(); // Workspace practical field experience
                $table->string('image_url')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->index(['category', 'is_active']);
                $table->index(['budget_level', 'durability_years']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_answers');
        Schema::dropIfExists('survey_responses');
        Schema::dropIfExists('survey_invitations');
        Schema::dropIfExists('survey_questions');
        Schema::dropIfExists('surveys');
        Schema::dropIfExists('materials');
    }
};
