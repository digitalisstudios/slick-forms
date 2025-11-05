<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration creates spam protection logging tables for Slick Forms.
     * These tables track blocked spam attempts for analysis and IP blacklisting.
     *
     * REQUIRES: Core tables must be installed first
     * FEATURE: spam_logs
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

        // Create slick_form_spam_logs table
        Schema::create('slick_form_spam_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('slick_forms')->onDelete('cascade');
            $table->string('ip_address');
            $table->string('detection_method'); // 'honeypot', 'recaptcha', 'hcaptcha', 'rate_limit'
            $table->json('details')->nullable(); // Additional context
            $table->timestamps();

            $table->index(['form_id', 'ip_address']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slick_form_spam_logs');
    }
};
