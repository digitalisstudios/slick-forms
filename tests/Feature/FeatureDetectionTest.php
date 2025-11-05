<?php

namespace DigitalisStudios\SlickForms\Tests\Feature;

use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Models\CustomFormSubmission;
use DigitalisStudios\SlickForms\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

class FeatureDetectionTest extends TestCase
{
    /** @test */
    public function feature_enabled_helper_returns_true_when_feature_enabled_in_config_and_database()
    {
        Config::set('slick-forms.features.analytics', true);

        DB::table('slick_form_features')->insert([
            'feature_name' => 'analytics',
            'enabled' => true,
            'installed_at' => now(),
            'enabled_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertTrue(slick_forms_feature_enabled('analytics'));
    }

    /** @test */
    public function feature_enabled_helper_returns_false_when_feature_disabled_in_config()
    {
        Config::set('slick-forms.features.analytics', false);

        DB::table('slick_form_features')->insert([
            'feature_name' => 'analytics',
            'enabled' => true,
            'installed_at' => now(),
            'enabled_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertFalse(slick_forms_feature_enabled('analytics'));
    }

    /** @test */
    public function feature_enabled_helper_returns_false_when_feature_disabled_in_database()
    {
        Config::set('slick-forms.features.analytics', true);

        DB::table('slick_form_features')->insert([
            'feature_name' => 'analytics',
            'enabled' => false,
            'installed_at' => now(),
            'enabled_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertFalse(slick_forms_feature_enabled('analytics'));
    }

    /** @test */
    public function feature_enabled_helper_returns_false_when_feature_not_in_database()
    {
        Config::set('slick-forms.features.analytics', true);

        // No database record for analytics
        $this->assertFalse(slick_forms_feature_enabled('analytics'));
    }

    /** @test */
    public function feature_enabled_helper_handles_missing_features_table_gracefully()
    {
        Schema::dropIfExists('slick_form_features');

        Config::set('slick-forms.features.analytics', true);

        // Should fallback to config value when table doesn't exist
        $this->assertTrue(slick_forms_feature_enabled('analytics'));

        // Re-create the table for other tests
        Artisan::call('migrate');
    }

    /** @test */
    public function feature_installed_helper_returns_true_when_feature_in_database()
    {
        DB::table('slick_form_features')->insert([
            'feature_name' => 'analytics',
            'enabled' => false,
            'installed_at' => now(),
            'enabled_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertTrue(slick_forms_feature_installed('analytics'));
    }

    /** @test */
    public function feature_installed_helper_returns_false_when_feature_not_in_database()
    {
        // No database record for analytics
        $this->assertFalse(slick_forms_feature_installed('analytics'));
    }

    /** @test */
    public function forms_work_without_analytics_feature()
    {
        Config::set('slick-forms.features.analytics', false);

        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
        ]);

        $field = CustomFormField::create([
            'slick_form_id' => $form->id,
            'name' => 'test_field',
            'field_type' => 'text',
            'label' => 'Test Field',
        ]);

        Livewire::test('slick-forms::form-renderer', ['formId' => $form->id])
            ->assertOk()
            ->assertSee('Test Field')
            ->set('formData.test_field', 'Test Value')
            ->call('submit')
            ->assertHasNoErrors();

        // Submission should be created without analytics
        $this->assertDatabaseHas('slick_form_submissions', [
            'slick_form_id' => $form->id,
        ]);

        // Analytics tables shouldn't be queried
        $this->assertDatabaseMissing('slick_form_analytics_sessions', [
            'form_id' => $form->id,
        ]);
    }

    /** @test */
    public function forms_work_without_email_notifications_feature()
    {
        Config::set('slick-forms.features.email_notifications', false);

        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
        ]);

        $field = CustomFormField::create([
            'slick_form_id' => $form->id,
            'name' => 'email',
            'field_type' => 'email',
            'label' => 'Email',
        ]);

        Livewire::test('slick-forms::form-renderer', ['formId' => $form->id])
            ->assertOk()
            ->set('formData.email', 'test@example.com')
            ->call('submit')
            ->assertHasNoErrors();

        // Submission should be created
        $this->assertDatabaseHas('slick_form_submissions', [
            'slick_form_id' => $form->id,
        ]);

        // Email logs shouldn't be created
        if (Schema::hasTable('slick_form_email_logs')) {
            $this->assertDatabaseMissing('slick_form_email_logs', [
                'submission_id' => CustomFormSubmission::latest()->first()->id,
            ]);
        }
    }

    /** @test */
    public function forms_work_without_webhooks_feature()
    {
        Config::set('slick-forms.features.webhooks', false);

        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
        ]);

        $field = CustomFormField::create([
            'slick_form_id' => $form->id,
            'name' => 'test_field',
            'field_type' => 'text',
            'label' => 'Test Field',
        ]);

        Livewire::test('slick-forms::form-renderer', ['formId' => $form->id])
            ->assertOk()
            ->set('formData.test_field', 'Test Value')
            ->call('submit')
            ->assertHasNoErrors();

        // Submission should be created
        $this->assertDatabaseHas('slick_form_submissions', [
            'slick_form_id' => $form->id,
        ]);

        // Webhook logs shouldn't be created
        if (Schema::hasTable('slick_form_webhook_logs')) {
            $this->assertDatabaseMissing('slick_form_webhook_logs', [
                'submission_id' => CustomFormSubmission::latest()->first()->id,
            ]);
        }
    }

    /** @test */
    public function email_logs_viewer_shows_disabled_message_when_feature_disabled()
    {
        Config::set('slick-forms.features.email_notifications', false);

        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
        ]);

        Livewire::test('slick-forms::email-logs-viewer', ['formId' => $form->id])
            ->assertSee('Email Notifications Feature Not Enabled')
            ->assertSee('This feature is currently disabled');
    }

    /** @test */
    public function spam_logs_viewer_shows_disabled_message_when_feature_disabled()
    {
        Config::set('slick-forms.features.spam_logs', false);

        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
        ]);

        Livewire::test('slick-forms::spam-logs-viewer', ['formId' => $form->id])
            ->assertSee('Spam Protection Logs Feature Not Enabled')
            ->assertSee('This feature is currently disabled');
    }

    /** @test */
    public function form_analytics_component_shows_disabled_message_when_feature_disabled()
    {
        Config::set('slick-forms.features.analytics', false);

        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
        ]);

        Livewire::test('slick-forms::form-analytics', ['formId' => $form->id])
            ->assertSee('Analytics Feature Not Enabled')
            ->assertSee('This feature is currently disabled');
    }

    /** @test */
    public function analytics_feature_flag_can_be_disabled()
    {
        Config::set('slick-forms.features.analytics', false);

        $this->assertFalse(Config::get('slick-forms.features.analytics'));
        $this->assertFalse(slick_forms_feature_enabled('analytics'));
    }

    /** @test */
    public function analytics_feature_flag_can_be_enabled()
    {
        Config::set('slick-forms.features.analytics', true);

        DB::table('slick_form_features')->insert([
            'feature_name' => 'analytics',
            'enabled' => true,
            'installed_at' => now(),
            'enabled_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertTrue(Config::get('slick-forms.features.analytics'));
        $this->assertTrue(slick_forms_feature_enabled('analytics'));
    }

    /** @test */
    public function version_history_button_hidden_when_versioning_disabled()
    {
        Config::set('slick-forms.features.versioning', false);

        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
        ]);

        Livewire::test('slick-forms::form-builder', ['formId' => $form->id])
            ->assertOk()
            ->assertDontSee('Version History');
    }

    /** @test */
    public function version_history_button_visible_when_versioning_enabled()
    {
        Config::set('slick-forms.features.versioning', true);

        DB::table('slick_form_features')->insert([
            'feature_name' => 'versioning',
            'enabled' => true,
            'installed_at' => now(),
            'enabled_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
        ]);

        Livewire::test('slick-forms::form-builder', ['formId' => $form->id])
            ->assertOk()
            ->assertSee('Version History');
    }

    /** @test */
    public function core_features_always_work()
    {
        // Disable all optional features
        Config::set('slick-forms.features.analytics', false);
        Config::set('slick-forms.features.email_notifications', false);
        Config::set('slick-forms.features.webhooks', false);
        Config::set('slick-forms.features.spam_logs', false);
        Config::set('slick-forms.features.versioning', false);
        Config::set('slick-forms.features.exports', false);

        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
        ]);

        $field = CustomFormField::create([
            'slick_form_id' => $form->id,
            'name' => 'test_field',
            'field_type' => 'text',
            'label' => 'Test Field',
        ]);

        // Form should still work with core features only
        Livewire::test('slick-forms::form-renderer', ['formId' => $form->id])
            ->assertOk()
            ->set('formData.test_field', 'Test Value')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('slick_form_submissions', [
            'slick_form_id' => $form->id,
        ]);

        $submission = CustomFormSubmission::latest()->first();

        $this->assertDatabaseHas('slick_form_field_values', [
            'slick_form_submission_id' => $submission->id,
            'slick_form_field_id' => $field->id,
            'value' => 'Test Value',
        ]);
    }
}
