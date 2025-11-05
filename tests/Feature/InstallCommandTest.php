<?php

namespace DigitalisStudios\SlickForms\Tests\Feature;

use DigitalisStudios\SlickForms\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InstallCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Drop all Slick Forms tables to test fresh installation
        $this->dropAllSlickFormsTables();
    }

    protected function dropAllSlickFormsTables(): void
    {
        $tables = [
            'slick_forms',
            'slick_form_fields',
            'slick_form_layout_elements',
            'slick_form_pages',
            'slick_form_submissions',
            'slick_form_field_values',
            'slick_form_analytics_sessions',
            'slick_form_analytics_events',
            'slick_form_features',
            'slick_form_email_templates',
            'slick_form_email_logs',
            'slick_form_webhooks',
            'slick_form_webhook_logs',
            'slick_form_spam_logs',
            'slick_dynamic_options_cache',
            'slick_form_model_bindings',
            'slick_form_signed_urls',
            'slick_form_versions',
        ];

        Schema::disableForeignKeyConstraints();
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
        Schema::enableForeignKeyConstraints();
    }

    /** @test */
    public function it_creates_core_tables_when_installing_with_no_features()
    {
        // Simulate selecting no features in wizard
        Artisan::call('slick-forms:install', [
            '--no-interaction' => true,
            '--skip-features' => true,
        ]);

        // Core tables should exist
        $this->assertTrue(Schema::hasTable('slick_forms'));
        $this->assertTrue(Schema::hasTable('slick_form_fields'));
        $this->assertTrue(Schema::hasTable('slick_form_layout_elements'));
        $this->assertTrue(Schema::hasTable('slick_form_pages'));
        $this->assertTrue(Schema::hasTable('slick_form_submissions'));
        $this->assertTrue(Schema::hasTable('slick_form_field_values'));
        $this->assertTrue(Schema::hasTable('slick_form_analytics_sessions'));
        $this->assertTrue(Schema::hasTable('slick_form_analytics_events'));
        $this->assertTrue(Schema::hasTable('slick_form_features'));

        // Feature tables should NOT exist
        $this->assertFalse(Schema::hasTable('slick_form_email_templates'));
        $this->assertFalse(Schema::hasTable('slick_form_webhooks'));
        $this->assertFalse(Schema::hasTable('slick_form_spam_logs'));
        $this->assertFalse(Schema::hasTable('slick_form_versions'));
    }

    /** @test */
    public function it_creates_analytics_tables_when_analytics_feature_selected()
    {
        Artisan::call('slick-forms:install', [
            '--no-interaction' => true,
            '--features' => 'analytics',
        ]);

        // Core tables + analytics tables should exist
        $this->assertTrue(Schema::hasTable('slick_forms'));
        $this->assertTrue(Schema::hasTable('slick_form_analytics_sessions'));
        $this->assertTrue(Schema::hasTable('slick_form_analytics_events'));

        // Other feature tables should not exist
        $this->assertFalse(Schema::hasTable('slick_form_email_templates'));
        $this->assertFalse(Schema::hasTable('slick_form_webhooks'));

        // Feature should be marked as enabled
        $this->assertDatabaseHas('slick_form_features', [
            'feature_name' => 'analytics',
            'enabled' => true,
        ]);
    }

    /** @test */
    public function it_creates_email_tables_when_email_notifications_feature_selected()
    {
        Artisan::call('slick-forms:install', [
            '--no-interaction' => true,
            '--features' => 'email_notifications',
        ]);

        $this->assertTrue(Schema::hasTable('slick_form_email_templates'));
        $this->assertTrue(Schema::hasTable('slick_form_email_logs'));

        $this->assertDatabaseHas('slick_form_features', [
            'feature_name' => 'email_notifications',
            'enabled' => true,
        ]);
    }

    /** @test */
    public function it_creates_webhook_tables_when_webhooks_feature_selected()
    {
        Artisan::call('slick-forms:install', [
            '--no-interaction' => true,
            '--features' => 'webhooks',
        ]);

        $this->assertTrue(Schema::hasTable('slick_form_webhooks'));
        $this->assertTrue(Schema::hasTable('slick_form_webhook_logs'));

        $this->assertDatabaseHas('slick_form_features', [
            'feature_name' => 'webhooks',
            'enabled' => true,
        ]);
    }

    /** @test */
    public function it_creates_spam_tables_when_spam_logs_feature_selected()
    {
        Artisan::call('slick-forms:install', [
            '--no-interaction' => true,
            '--features' => 'spam_logs',
        ]);

        $this->assertTrue(Schema::hasTable('slick_form_spam_logs'));

        $this->assertDatabaseHas('slick_form_features', [
            'feature_name' => 'spam_logs',
            'enabled' => true,
        ]);
    }

    /** @test */
    public function it_creates_versioning_tables_when_versioning_feature_selected()
    {
        Artisan::call('slick-forms:install', [
            '--no-interaction' => true,
            '--features' => 'versioning',
        ]);

        $this->assertTrue(Schema::hasTable('slick_form_versions'));

        $this->assertDatabaseHas('slick_form_features', [
            'feature_name' => 'versioning',
            'enabled' => true,
        ]);
    }

    /** @test */
    public function it_creates_all_tables_when_all_features_selected()
    {
        Artisan::call('slick-forms:install', [
            '--no-interaction' => true,
            '--features' => 'analytics,email_notifications,webhooks,spam_logs,versioning,exports',
        ]);

        // All core tables
        $this->assertTrue(Schema::hasTable('slick_forms'));
        $this->assertTrue(Schema::hasTable('slick_form_fields'));
        $this->assertTrue(Schema::hasTable('slick_form_layout_elements'));
        $this->assertTrue(Schema::hasTable('slick_form_pages'));
        $this->assertTrue(Schema::hasTable('slick_form_submissions'));
        $this->assertTrue(Schema::hasTable('slick_form_field_values'));
        $this->assertTrue(Schema::hasTable('slick_form_analytics_sessions'));
        $this->assertTrue(Schema::hasTable('slick_form_analytics_events'));
        $this->assertTrue(Schema::hasTable('slick_form_features'));

        // All feature tables
        $this->assertTrue(Schema::hasTable('slick_form_email_templates'));
        $this->assertTrue(Schema::hasTable('slick_form_email_logs'));
        $this->assertTrue(Schema::hasTable('slick_form_webhooks'));
        $this->assertTrue(Schema::hasTable('slick_form_webhook_logs'));
        $this->assertTrue(Schema::hasTable('slick_form_spam_logs'));
        $this->assertTrue(Schema::hasTable('slick_dynamic_options_cache'));
        $this->assertTrue(Schema::hasTable('slick_form_model_bindings'));
        $this->assertTrue(Schema::hasTable('slick_form_signed_urls'));
        $this->assertTrue(Schema::hasTable('slick_form_versions'));

        // All features should be enabled
        $features = ['analytics', 'email_notifications', 'webhooks', 'spam_logs', 'versioning', 'exports'];
        foreach ($features as $feature) {
            $this->assertDatabaseHas('slick_form_features', [
                'feature_name' => $feature,
                'enabled' => true,
            ]);
        }
    }

    /** @test */
    public function it_disables_feature_when_deselected_on_reinstall()
    {
        // Initial install with analytics enabled
        Artisan::call('slick-forms:install', [
            '--no-interaction' => true,
            '--features' => 'analytics',
        ]);

        $this->assertDatabaseHas('slick_form_features', [
            'feature_name' => 'analytics',
            'enabled' => true,
        ]);

        // Re-run install without analytics
        Artisan::call('slick-forms:install', [
            '--no-interaction' => true,
            '--skip-features' => true,
        ]);

        // Feature should now be disabled
        $this->assertDatabaseHas('slick_form_features', [
            'feature_name' => 'analytics',
            'enabled' => false,
        ]);

        // Tables should still exist (not dropped)
        $this->assertTrue(Schema::hasTable('slick_form_analytics_sessions'));
        $this->assertTrue(Schema::hasTable('slick_form_analytics_events'));
    }

    /** @test */
    public function it_re_enables_feature_when_selected_again()
    {
        // Install with analytics
        Artisan::call('slick-forms:install', [
            '--no-interaction' => true,
            '--features' => 'analytics',
        ]);

        // Disable analytics
        Artisan::call('slick-forms:install', [
            '--no-interaction' => true,
            '--skip-features' => true,
        ]);

        $this->assertDatabaseHas('slick_form_features', [
            'feature_name' => 'analytics',
            'enabled' => false,
        ]);

        // Re-enable analytics
        Artisan::call('slick-forms:install', [
            '--no-interaction' => true,
            '--features' => 'analytics',
        ]);

        // Feature should be enabled again
        $this->assertDatabaseHas('slick_form_features', [
            'feature_name' => 'analytics',
            'enabled' => true,
        ]);

        // Should have enabled_at timestamp updated
        $feature = DB::table('slick_form_features')
            ->where('feature_name', 'analytics')
            ->first();

        $this->assertNotNull($feature->enabled_at);
    }

    /** @test */
    public function it_preserves_data_when_disabling_and_re_enabling_features()
    {
        // Install with webhooks
        Artisan::call('slick-forms:install', [
            '--no-interaction' => true,
            '--features' => 'webhooks',
        ]);

        // Create test data
        DB::table('slick_form_webhooks')->insert([
            'form_id' => 1,
            'name' => 'Test Webhook',
            'url' => 'https://example.com/webhook',
            'method' => 'POST',
            'format' => 'json',
            'enabled' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Disable webhooks
        Artisan::call('slick-forms:install', [
            '--no-interaction' => true,
            '--skip-features' => true,
        ]);

        // Data should still exist
        $this->assertDatabaseHas('slick_form_webhooks', [
            'name' => 'Test Webhook',
        ]);

        // Re-enable webhooks
        Artisan::call('slick-forms:install', [
            '--no-interaction' => true,
            '--features' => 'webhooks',
        ]);

        // Data should still be there
        $this->assertDatabaseHas('slick_form_webhooks', [
            'name' => 'Test Webhook',
        ]);
    }

    /** @test */
    public function it_updates_config_with_enabled_features()
    {
        Config::set('slick-forms.features.analytics', false);
        Config::set('slick-forms.features.email_notifications', false);

        Artisan::call('slick-forms:install', [
            '--no-interaction' => true,
            '--features' => 'analytics,email_notifications',
            '--update-config' => true,
        ]);

        // Config should be updated (in runtime, not file)
        $this->assertTrue(Config::get('slick-forms.features.analytics'));
        $this->assertTrue(Config::get('slick-forms.features.email_notifications'));
    }

    /** @test */
    public function it_handles_partial_feature_selection()
    {
        Artisan::call('slick-forms:install', [
            '--no-interaction' => true,
            '--features' => 'analytics,webhooks',
        ]);

        // Selected features should be enabled
        $this->assertDatabaseHas('slick_form_features', [
            'feature_name' => 'analytics',
            'enabled' => true,
        ]);

        $this->assertDatabaseHas('slick_form_features', [
            'feature_name' => 'webhooks',
            'enabled' => true,
        ]);

        // Non-selected features should not have records
        $this->assertDatabaseMissing('slick_form_features', [
            'feature_name' => 'email_notifications',
        ]);

        $this->assertDatabaseMissing('slick_form_features', [
            'feature_name' => 'versioning',
        ]);

        // Only selected feature tables should exist
        $this->assertTrue(Schema::hasTable('slick_form_analytics_sessions'));
        $this->assertTrue(Schema::hasTable('slick_form_webhooks'));
        $this->assertFalse(Schema::hasTable('slick_form_email_templates'));
        $this->assertFalse(Schema::hasTable('slick_form_versions'));
    }
}
