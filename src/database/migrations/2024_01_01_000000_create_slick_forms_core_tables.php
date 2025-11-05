<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration creates the 8 core tables required for Slick Forms to function.
     * Optional feature tables (analytics, webhooks, spam logs, email, versioning)
     * are installed separately via feature-specific migrations.
     */
    public function up(): void
    {
        // 1. Create slick_forms table (with V2 columns)
        Schema::create('slick_forms', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable()->unique();
            $table->string('url_strategy')->default('hashid'); // 'id', 'hashid', 'uuid'
            $table->string('hashid_salt')->nullable(); // Per-form salt (optional)
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_template')->default(false);
            $table->boolean('is_public')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('time_limited')->default(0)->comment('Maximum time to complete form in seconds (0 = unlimited)');
            $table->string('template_category')->nullable();
            $table->text('template_description')->nullable();
            $table->string('preview_image')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Create slick_form_pages table
        Schema::create('slick_form_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slick_form_id')->constrained('slick_forms')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->string('icon')->nullable();
            $table->boolean('show_in_progress')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->index(['slick_form_id', 'order']);
        });

        // 3. Create slick_form_layout_elements table
        Schema::create('slick_form_layout_elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slick_form_id')->constrained('slick_forms')->onDelete('cascade');
            $table->foreignId('slick_form_page_id')->nullable()->constrained('slick_form_pages')->onDelete('set null');
            $table->foreignId('parent_id')->nullable()->constrained('slick_form_layout_elements')->onDelete('cascade');
            $table->unsignedBigInteger('parent_field_id')->nullable(); // Foreign key added after slick_form_fields table
            $table->string('element_type');
            $table->string('element_id')->nullable();
            $table->integer('order')->default(0);
            $table->json('settings')->nullable();
            $table->json('conditional_logic')->nullable();
            $table->string('class')->nullable();
            $table->text('style')->nullable();
            $table->timestamps();

            $table->index('slick_form_page_id');
        });

        // 4. Create slick_form_fields table
        Schema::create('slick_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slick_form_id')->constrained('slick_forms')->onDelete('cascade');
            $table->foreignId('slick_form_page_id')->nullable()->constrained('slick_form_pages')->onDelete('set null');
            $table->foreignId('slick_form_layout_element_id')->nullable()->constrained('slick_form_layout_elements')->onDelete('cascade');
            $table->foreignId('parent_field_id')->nullable()->constrained('slick_form_fields')->onDelete('set null');
            $table->string('field_type');
            $table->string('name')->nullable();
            $table->string('element_id')->nullable();
            $table->string('class')->nullable();
            $table->text('style')->nullable();
            $table->string('label');
            $table->boolean('show_label')->default(true);
            $table->string('placeholder')->nullable();
            $table->text('help_text')->nullable();
            $table->boolean('help_text_as_popover')->default(false);
            $table->json('validation_rules')->nullable();
            $table->json('conditional_logic')->nullable();
            $table->json('options')->nullable();
            $table->integer('order')->default(0);
            $table->integer('column_width')->default(12);
            $table->boolean('is_required')->default(false);
            $table->timestamps();

            $table->index('slick_form_page_id');
            $table->unique(['slick_form_id', 'name'], 'unique_field_name_per_form');
        });

        // 5. Create slick_form_submissions table (with V2 columns)
        Schema::create('slick_form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slick_form_id')->constrained('slick_forms')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('ip_address')->nullable();
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
        });

        // 6. Create slick_form_field_values table
        Schema::create('slick_form_field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slick_form_submission_id')->constrained('slick_form_submissions')->onDelete('cascade');
            $table->foreignId('slick_form_field_id')->constrained('slick_form_fields')->onDelete('cascade');
            $table->longText('value')->nullable();
            $table->timestamps();
        });

        // 7. Create slick_form_model_bindings table (Core Feature)
        Schema::create('slick_form_model_bindings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('slick_forms')->onDelete('cascade');
            $table->string('model_class'); // Full class name (e.g., App\Models\User)
            $table->string('route_parameter')->default('model'); // Route param name
            $table->string('route_key')->default('id'); // Model key (id, slug, uuid, email)
            $table->json('field_mappings'); // ['form_field_name' => 'model_attribute']
            $table->json('relationship_mappings')->nullable(); // ['field_name' => 'relationship.attribute']
            $table->boolean('allow_create')->default(true);
            $table->boolean('allow_update')->default(true);
            $table->text('custom_population_logic')->nullable(); // PHP code or class name
            $table->text('custom_save_logic')->nullable(); // PHP code or class name
            $table->timestamps();

            $table->unique('form_id');
        });

        // 8. Create slick_form_signed_urls table (Core Feature)
        Schema::create('slick_form_signed_urls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('slick_forms')->onDelete('cascade');
            $table->string('signature')->unique();
            $table->json('prefill_data')->nullable(); // Encrypted form data
            $table->timestamp('expires_at')->nullable();
            $table->integer('max_uses')->nullable(); // Optional: limit number of uses
            $table->integer('uses')->default(0);
            $table->timestamps();

            $table->index(['signature', 'expires_at']);
        });

        // 9. Add foreign key constraint for parent_field_id after all tables exist
        Schema::table('slick_form_layout_elements', function (Blueprint $table) {
            $table->foreign('parent_field_id')
                ->references('id')
                ->on('slick_form_fields')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key constraint first
        Schema::table('slick_form_layout_elements', function (Blueprint $table) {
            $table->dropForeign(['parent_field_id']);
        });

        // Drop in reverse order to respect foreign key constraints
        Schema::dropIfExists('slick_form_signed_urls');
        Schema::dropIfExists('slick_form_model_bindings');
        Schema::dropIfExists('slick_form_field_values');
        Schema::dropIfExists('slick_form_submissions');
        Schema::dropIfExists('slick_form_fields');
        Schema::dropIfExists('slick_form_layout_elements');
        Schema::dropIfExists('slick_form_pages');
        Schema::dropIfExists('slick_forms');
    }
};
