<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration creates email notification tables for Slick Forms.
     * These tables enable sending customized emails on form submission.
     *
     * REQUIRES: Core tables must be installed first
     * FEATURE: email_notifications
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

        // 1. Create slick_form_email_templates table
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

        // 2. Create slick_form_email_logs table
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slick_form_email_logs');
        Schema::dropIfExists('slick_form_email_templates');
    }
};
