<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration creates analytics tracking tables for Slick Forms.
     * These tables track form views, starts, field interactions, and abandonment.
     *
     * REQUIRES: Core tables must be installed first
     * FEATURE: analytics
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

        // 1. Create slick_form_analytics_sessions table
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

        // 2. Create slick_form_analytics_events table
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
        Schema::dropIfExists('slick_form_analytics_events');
        Schema::dropIfExists('slick_form_analytics_sessions');
    }
};
