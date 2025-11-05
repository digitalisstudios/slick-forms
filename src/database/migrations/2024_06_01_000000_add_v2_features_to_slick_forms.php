<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration adds all V2 features to Slick Forms:
     * - Email notification system (templates + logs)
     * - Spam protection (spam logs)
     * - Dynamic options (cascading dropdowns with caching)
     * - Model binding (Eloquent integration)
     * - URL obfuscation (signed URLs, hashids, UUIDs)
     * - Webhooks (webhook configs + logs)
     * - Form versioning (snapshots)
     * - Additional columns for V2 features
     * - Data migration for carousel slides
     */
    public function up(): void
    {
        // ==========================================
        // NEW V2 TABLES
        // ==========================================

        // Email Notification System
        Schema::create('slick_form_email_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('slick_forms')->onDelete('cascade');
            $table->string('type'); // 'admin', 'user_confirmation'
            $table->boolean('enabled')->default(true);
            $table->json('recipients'); // Array of email addresses or field references
            $table->string('subject');
            $table->text('body_template'); // Blade template with {{ }} variables
            $table->boolean('attach_pdf')->default(false);
            $table->json('conditional_rules')->nullable(); // Send only if conditions met
            $table->integer('priority')->default(0); // Order of execution
            $table->timestamps();
        });

        Schema::create('slick_form_email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('slick_form_submissions')->onDelete('cascade');
            $table->foreignId('template_id')->nullable()->constrained('slick_form_email_templates')->onDelete('set null');
            $table->string('to');
            $table->string('subject');
            $table->text('body')->nullable();
            $table->string('status'); // 'sent', 'failed', 'queued'
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        // Spam Protection
        Schema::create('slick_form_spam_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('slick_forms')->onDelete('cascade');
            $table->string('ip_address');
            $table->string('detection_method'); // 'honeypot', 'recaptcha', 'hcaptcha', 'rate_limit'
            $table->json('details')->nullable(); // Additional context
            $table->timestamps();
        });

        // Dynamic Options (Cascading Dropdowns)
        // NOTE: Removed in v2.1.0 - Now using Laravel's cache system instead of database table
        // Schema::create('slick_dynamic_options_cache', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('field_id')->constrained('slick_form_fields')->onDelete('cascade');
        //     $table->string('cache_key'); // Hash of source URL/model query
        //     $table->json('options'); // Cached option data
        //     $table->timestamp('cached_at');
        //     $table->integer('ttl_seconds'); // Time to live
        //     $table->timestamps();
        //
        //     $table->index(['field_id', 'cache_key']);
        // });

        // Model Binding (Eloquent Integration)
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

        // URL Obfuscation (Signed URLs)
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

        // Webhooks
        Schema::create('slick_form_webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('slick_forms')->onDelete('cascade');
            $table->string('name'); // Human-readable name
            $table->string('url');
            $table->string('method')->default('POST'); // POST, PUT, PATCH
            $table->json('headers')->nullable();
            $table->string('format')->default('json'); // json, form_data, xml
            $table->json('trigger_conditions')->nullable(); // When to send
            $table->boolean('enabled')->default(true);
            $table->integer('max_retries')->default(3);
            $table->integer('retry_delay_seconds')->default(60);
            $table->timestamps();
        });

        Schema::create('slick_form_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_id')->constrained('slick_form_webhooks')->onDelete('cascade');
            $table->foreignId('submission_id')->nullable()->constrained('slick_form_submissions')->onDelete('set null');
            $table->string('event_type'); // 'submission', 'validation_fail', etc.
            $table->text('request_url');
            $table->json('request_headers')->nullable();
            $table->text('request_body')->nullable();
            $table->integer('response_status')->nullable();
            $table->json('response_headers')->nullable();
            $table->text('response_body')->nullable();
            $table->string('status'); // 'pending', 'sent', 'failed'
            $table->integer('retry_count')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        // Form Versioning
        Schema::create('slick_form_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('slick_forms')->onDelete('cascade');
            $table->integer('version_number'); // Auto-increment per form
            $table->string('version_name')->nullable(); // Optional tag
            $table->json('form_snapshot'); // Complete form structure
            $table->foreignId('published_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('change_summary')->nullable(); // Auto-generated or manual
            $table->timestamp('published_at');
            $table->timestamps();

            $table->unique(['form_id', 'version_number']);
        });

        // ==========================================
        // MODIFY EXISTING V1 TABLES
        // ==========================================

        // Add V2 columns to slick_forms table
        Schema::table('slick_forms', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
            $table->string('url_strategy')->default('id')->after('uuid'); // 'id', 'hashid', 'uuid'
            $table->string('hashid_salt')->nullable()->after('url_strategy'); // Per-form salt (optional)
        });

        // Add V2 columns to slick_form_submissions table
        Schema::table('slick_form_submissions', function (Blueprint $table) {
            $table->foreignId('form_version_id')->nullable()->after('slick_form_id')
                ->constrained('slick_form_versions')->onDelete('set null');
            $table->string('model_type')->nullable()->after('ip_address');
            $table->unsignedBigInteger('model_id')->nullable()->after('model_type');

            $table->index(['model_type', 'model_id']);
        });

        // Add parent_field_id column to slick_form_layout_elements table (for repeater fields)
        Schema::table('slick_form_layout_elements', function (Blueprint $table) {
            $table->foreignId('parent_field_id')
                ->nullable()
                ->after('parent_id')
                ->constrained('slick_form_fields')
                ->nullOnDelete();
        });

        // ==========================================
        // DATA MIGRATIONS
        // ==========================================

        // Convert existing container elements with isSlide flag to carousel_slide type
        // This ensures backward compatibility for forms created before V2
        DB::table('slick_form_layout_elements')
            ->where('element_type', 'container')
            ->whereRaw("JSON_EXTRACT(settings, '$.isSlide') = true")
            ->update([
                'element_type' => 'carousel_slide',
                // Update settings to remove isSlide flag and add slide_title
                'settings' => DB::raw("JSON_SET(
                    JSON_REMOVE(settings, '$.isSlide'),
                    '$.slide_title', COALESCE(JSON_EXTRACT(settings, '$.slide_title'), CONCAT('Slide ', `order` + 1)),
                    '$.slide_icon', COALESCE(JSON_EXTRACT(settings, '$.slide_icon'), '')
                )"),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert data migrations
        DB::table('slick_form_layout_elements')
            ->where('element_type', 'carousel_slide')
            ->update([
                'element_type' => 'container',
                'settings' => DB::raw("JSON_SET(settings, '$.isSlide', true)"),
            ]);

        // Remove columns from existing tables
        Schema::table('slick_form_layout_elements', function (Blueprint $table) {
            if (Schema::hasColumn('slick_form_layout_elements', 'parent_field_id')) {
                $table->dropConstrainedForeignId('parent_field_id');
            }
        });

        Schema::table('slick_form_submissions', function (Blueprint $table) {
            $table->dropForeign(['form_version_id']);
            $table->dropIndex(['model_type', 'model_id']);
            $table->dropColumn(['form_version_id', 'model_type', 'model_id']);
        });

        Schema::table('slick_forms', function (Blueprint $table) {
            $table->dropUnique(['uuid']);
            $table->dropColumn(['uuid', 'url_strategy', 'hashid_salt']);
        });

        // Drop V2 tables (in reverse order of creation to respect foreign keys)
        Schema::dropIfExists('slick_form_versions');
        Schema::dropIfExists('slick_form_webhook_logs');
        Schema::dropIfExists('slick_form_webhooks');
        Schema::dropIfExists('slick_form_signed_urls');
        Schema::dropIfExists('slick_form_model_bindings');
        // Schema::dropIfExists('slick_dynamic_options_cache'); // Removed in v2.1.0
        Schema::dropIfExists('slick_form_spam_logs');
        Schema::dropIfExists('slick_form_email_logs');
        Schema::dropIfExists('slick_form_email_templates');
    }
};
