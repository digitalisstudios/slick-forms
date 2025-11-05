<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration creates webhook tables for Slick Forms.
     * These tables enable POST-ing form submissions to external APIs.
     *
     * REQUIRES: Core tables must be installed first
     * FEATURE: webhooks
     */
    public function up(): void
    {
        // Verify core tables exist
        if (! Schema::hasTable('slick_forms')) {
            throw new \RuntimeException(
                'Core tables not found. Run core migration first: '.
                'php artisan migrate --path=packages/digitalisstudios/slick-forms/src/database/migrations/2024_01_01_000000_create_slick_forms_core_tables.php'
            );
        }

        // 1. Create slick_form_webhooks table
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

        // 2. Create slick_form_webhook_logs table
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slick_form_webhook_logs');
        Schema::dropIfExists('slick_form_webhooks');
    }
};
