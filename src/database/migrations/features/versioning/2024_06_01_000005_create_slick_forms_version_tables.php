<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration creates form versioning tables for Slick Forms.
     * These tables enable snapshots and restoration of form versions.
     *
     * REQUIRES: Core tables must be installed first
     * FEATURE: versioning
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

        // Create slick_form_versions table
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

        // Add form_version_id to slick_form_submissions table
        // This is safe to run even if the column already exists
        if (! Schema::hasColumn('slick_form_submissions', 'form_version_id')) {
            Schema::table('slick_form_submissions', function (Blueprint $table) {
                $table->foreignId('form_version_id')->nullable()->after('slick_form_id')
                    ->constrained('slick_form_versions')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove form_version_id column from submissions if it exists
        if (Schema::hasColumn('slick_form_submissions', 'form_version_id')) {
            Schema::table('slick_form_submissions', function (Blueprint $table) {
                $table->dropForeign(['form_version_id']);
                $table->dropColumn('form_version_id');
            });
        }

        Schema::dropIfExists('slick_form_versions');
    }
};
