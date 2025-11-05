<?php

namespace DigitalisStudios\SlickForms\Tests\Feature;

use DigitalisStudios\SlickForms\Services\DynamicOptionsService;
use DigitalisStudios\SlickForms\Services\EmailNotificationService;
use DigitalisStudios\SlickForms\Services\FormAnalyticsService;
use DigitalisStudios\SlickForms\Services\FormLayoutService;
use DigitalisStudios\SlickForms\Services\FormVersionService;
use DigitalisStudios\SlickForms\Services\ModelBindingService;
use DigitalisStudios\SlickForms\Services\SpamProtectionService;
use DigitalisStudios\SlickForms\Services\SubmissionExportService;
use DigitalisStudios\SlickForms\Services\UrlObfuscationService;
use DigitalisStudios\SlickForms\Services\WebhookService;
use DigitalisStudios\SlickForms\Tests\TestCase;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class ConditionalServiceRegistrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Clear service container bindings to test fresh registration
        $this->app->forgetInstance(FormAnalyticsService::class);
        $this->app->forgetInstance(EmailNotificationService::class);
        $this->app->forgetInstance(WebhookService::class);
        $this->app->forgetInstance(SpamProtectionService::class);
        $this->app->forgetInstance(FormVersionService::class);
        $this->app->forgetInstance(SubmissionExportService::class);
    }

    /** @test */
    public function core_services_are_always_registered()
    {
        // Core services should always be available regardless of feature flags
        $this->assertInstanceOf(FormLayoutService::class, app(FormLayoutService::class));
        $this->assertInstanceOf(UrlObfuscationService::class, app(UrlObfuscationService::class));
        $this->assertInstanceOf(DynamicOptionsService::class, app(DynamicOptionsService::class));
        $this->assertInstanceOf(ModelBindingService::class, app(ModelBindingService::class));
    }

    /** @test */
    public function analytics_service_not_registered_when_feature_disabled()
    {
        Config::set('slick-forms.features.analytics', false);

        // Re-register services
        $this->app->register(\DigitalisStudios\SlickForms\SlickFormsServiceProvider::class, true);

        $this->expectException(BindingResolutionException::class);

        app(FormAnalyticsService::class);
    }

    /** @test */
    public function analytics_service_registered_when_feature_enabled()
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

        // Re-register services
        $this->app->register(\DigitalisStudios\SlickForms\SlickFormsServiceProvider::class, true);

        $service = app(FormAnalyticsService::class);

        $this->assertInstanceOf(FormAnalyticsService::class, $service);
    }

    /** @test */
    public function email_notification_service_not_registered_when_feature_disabled()
    {
        Config::set('slick-forms.features.email_notifications', false);

        // Re-register services
        $this->app->register(\DigitalisStudios\SlickForms\SlickFormsServiceProvider::class, true);

        $this->expectException(BindingResolutionException::class);

        app(EmailNotificationService::class);
    }

    /** @test */
    public function email_notification_service_registered_when_feature_enabled()
    {
        Config::set('slick-forms.features.email_notifications', true);

        DB::table('slick_form_features')->insert([
            'feature_name' => 'email_notifications',
            'enabled' => true,
            'installed_at' => now(),
            'enabled_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Re-register services
        $this->app->register(\DigitalisStudios\SlickForms\SlickFormsServiceProvider::class, true);

        $service = app(EmailNotificationService::class);

        $this->assertInstanceOf(EmailNotificationService::class, $service);
    }

    /** @test */
    public function webhook_service_not_registered_when_feature_disabled()
    {
        Config::set('slick-forms.features.webhooks', false);

        // Re-register services
        $this->app->register(\DigitalisStudios\SlickForms\SlickFormsServiceProvider::class, true);

        $this->expectException(BindingResolutionException::class);

        app(WebhookService::class);
    }

    /** @test */
    public function webhook_service_registered_when_feature_enabled()
    {
        Config::set('slick-forms.features.webhooks', true);

        DB::table('slick_form_features')->insert([
            'feature_name' => 'webhooks',
            'enabled' => true,
            'installed_at' => now(),
            'enabled_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Re-register services
        $this->app->register(\DigitalisStudios\SlickForms\SlickFormsServiceProvider::class, true);

        $service = app(WebhookService::class);

        $this->assertInstanceOf(WebhookService::class, $service);
    }

    /** @test */
    public function spam_protection_service_not_registered_when_feature_disabled()
    {
        Config::set('slick-forms.features.spam_logs', false);

        // Re-register services
        $this->app->register(\DigitalisStudios\SlickForms\SlickFormsServiceProvider::class, true);

        $this->expectException(BindingResolutionException::class);

        app(SpamProtectionService::class);
    }

    /** @test */
    public function spam_protection_service_registered_when_feature_enabled()
    {
        Config::set('slick-forms.features.spam_logs', true);

        DB::table('slick_form_features')->insert([
            'feature_name' => 'spam_logs',
            'enabled' => true,
            'installed_at' => now(),
            'enabled_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Re-register services
        $this->app->register(\DigitalisStudios\SlickForms\SlickFormsServiceProvider::class, true);

        $service = app(SpamProtectionService::class);

        $this->assertInstanceOf(SpamProtectionService::class, $service);
    }

    /** @test */
    public function versioning_service_not_registered_when_feature_disabled()
    {
        Config::set('slick-forms.features.versioning', false);

        // Re-register services
        $this->app->register(\DigitalisStudios\SlickForms\SlickFormsServiceProvider::class, true);

        $this->expectException(BindingResolutionException::class);

        app(FormVersionService::class);
    }

    /** @test */
    public function versioning_service_registered_when_feature_enabled()
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

        // Re-register services
        $this->app->register(\DigitalisStudios\SlickForms\SlickFormsServiceProvider::class, true);

        $service = app(FormVersionService::class);

        $this->assertInstanceOf(FormVersionService::class, $service);
    }

    /** @test */
    public function export_service_not_registered_when_feature_disabled()
    {
        Config::set('slick-forms.features.exports', false);

        // Re-register services
        $this->app->register(\DigitalisStudios\SlickForms\SlickFormsServiceProvider::class, true);

        $this->expectException(BindingResolutionException::class);

        app(SubmissionExportService::class);
    }

    /** @test */
    public function export_service_registered_when_feature_enabled()
    {
        Config::set('slick-forms.features.exports', true);

        DB::table('slick_form_features')->insert([
            'feature_name' => 'exports',
            'enabled' => true,
            'installed_at' => now(),
            'enabled_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Re-register services
        $this->app->register(\DigitalisStudios\SlickForms\SlickFormsServiceProvider::class, true);

        $service = app(SubmissionExportService::class);

        $this->assertInstanceOf(SubmissionExportService::class, $service);
    }

    /** @test */
    public function all_services_registered_when_all_features_enabled()
    {
        // Enable all features
        Config::set('slick-forms.features.analytics', true);
        Config::set('slick-forms.features.email_notifications', true);
        Config::set('slick-forms.features.webhooks', true);
        Config::set('slick-forms.features.spam_logs', true);
        Config::set('slick-forms.features.versioning', true);
        Config::set('slick-forms.features.exports', true);

        // Insert all feature records
        $features = ['analytics', 'email_notifications', 'webhooks', 'spam_logs', 'versioning', 'exports'];
        foreach ($features as $feature) {
            DB::table('slick_form_features')->insert([
                'feature_name' => $feature,
                'enabled' => true,
                'installed_at' => now(),
                'enabled_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Re-register services
        $this->app->register(\DigitalisStudios\SlickForms\SlickFormsServiceProvider::class, true);

        // All services should be available
        $this->assertInstanceOf(FormAnalyticsService::class, app(FormAnalyticsService::class));
        $this->assertInstanceOf(EmailNotificationService::class, app(EmailNotificationService::class));
        $this->assertInstanceOf(WebhookService::class, app(WebhookService::class));
        $this->assertInstanceOf(SpamProtectionService::class, app(SpamProtectionService::class));
        $this->assertInstanceOf(FormVersionService::class, app(FormVersionService::class));
        $this->assertInstanceOf(SubmissionExportService::class, app(SubmissionExportService::class));
    }

    /** @test */
    public function services_not_registered_when_all_features_disabled()
    {
        // Disable all features
        Config::set('slick-forms.features.analytics', false);
        Config::set('slick-forms.features.email_notifications', false);
        Config::set('slick-forms.features.webhooks', false);
        Config::set('slick-forms.features.spam_logs', false);
        Config::set('slick-forms.features.versioning', false);
        Config::set('slick-forms.features.exports', false);

        // Re-register services
        $this->app->register(\DigitalisStudios\SlickForms\SlickFormsServiceProvider::class, true);

        // All optional services should fail to resolve
        $services = [
            FormAnalyticsService::class,
            EmailNotificationService::class,
            WebhookService::class,
            SpamProtectionService::class,
            FormVersionService::class,
            SubmissionExportService::class,
        ];

        foreach ($services as $service) {
            try {
                app($service);
                $this->fail("Service {$service} should not be registered when feature is disabled");
            } catch (BindingResolutionException $e) {
                // Expected
                $this->assertTrue(true);
            }
        }
    }
}
