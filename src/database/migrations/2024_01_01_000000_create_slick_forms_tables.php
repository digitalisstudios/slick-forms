<?php

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
        // 1. Create slick_forms table
        Schema::create('slick_forms', function (Blueprint $table) {
            $table->id();
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

        // 5. Create slick_form_submissions table
        Schema::create('slick_form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slick_form_id')->constrained('slick_forms')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('ip_address')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamps();
        });

        // 6. Create slick_form_field_values table
        Schema::create('slick_form_field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slick_form_submission_id')->constrained('slick_form_submissions')->onDelete('cascade');
            $table->foreignId('slick_form_field_id')->constrained('slick_form_fields')->onDelete('cascade');
            $table->longText('value')->nullable();
            $table->timestamps();
        });

        // 7. Create slick_form_analytics_sessions table
        Schema::create('slick_form_analytics_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slick_form_id')->constrained('slick_forms')->onDelete('cascade');
            $table->foreignId('slick_form_submission_id')->nullable()->constrained('slick_form_submissions')->onDelete('set null');
            $table->string('session_id')->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_type', 50)->nullable();
            $table->string('browser', 100)->nullable();
            $table->string('platform', 100)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('abandoned_at')->nullable();
            $table->integer('time_spent_seconds')->nullable();
            $table->integer('current_page_index')->default(0);
            $table->string('referrer_url', 500)->nullable();
            $table->timestamps();

            $table->index(['slick_form_id', 'started_at']);
            $table->index(['slick_form_id', 'submitted_at']);
        });

        // 8. Create slick_form_analytics_events table
        Schema::create('slick_form_analytics_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('slick_form_analytics_session_id');
            $table->string('event_type', 50);
            $table->foreignId('slick_form_field_id')->nullable()->constrained('slick_form_fields')->onDelete('cascade');
            $table->integer('page_index')->nullable();
            $table->json('event_data')->nullable();
            $table->timestamp('created_at');

            $table->foreign('slick_form_analytics_session_id', 'analytics_events_session_fk')
                ->references('id')
                ->on('slick_form_analytics_sessions')
                ->onDelete('cascade');

            $table->index(['slick_form_analytics_session_id', 'created_at'], 'analytics_events_session_created_idx');
            $table->index(['event_type', 'slick_form_field_id'], 'analytics_events_type_field_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop in reverse order to respect foreign key constraints
        Schema::dropIfExists('slick_form_analytics_events');
        Schema::dropIfExists('slick_form_analytics_sessions');
        Schema::dropIfExists('slick_form_field_values');
        Schema::dropIfExists('slick_form_submissions');
        Schema::dropIfExists('slick_form_fields');
        Schema::dropIfExists('slick_form_layout_elements');
        Schema::dropIfExists('slick_form_pages');
        Schema::dropIfExists('slick_forms');
    }
};
