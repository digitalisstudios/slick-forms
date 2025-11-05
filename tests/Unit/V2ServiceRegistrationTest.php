<?php

namespace DigitalisStudios\SlickForms\Tests\Unit;

use DigitalisStudios\SlickForms\Services\DynamicOptionsService;
use DigitalisStudios\SlickForms\Services\EmailNotificationService;
use DigitalisStudios\SlickForms\Services\ModelBindingService;
use DigitalisStudios\SlickForms\Services\SpamProtectionService;
use DigitalisStudios\SlickForms\Services\UrlObfuscationService;
use DigitalisStudios\SlickForms\Services\WebhookService;
use DigitalisStudios\SlickForms\Tests\TestCase;

/**
 * Test V2 services are registered correctly
 */
class V2ServiceRegistrationTest extends TestCase
{
    /** @test */
    public function email_notification_service_is_registered()
    {
        $service = app(EmailNotificationService::class);

        $this->assertInstanceOf(EmailNotificationService::class, $service);
    }

    /** @test */
    public function spam_protection_service_is_registered()
    {
        $service = app(SpamProtectionService::class);

        $this->assertInstanceOf(SpamProtectionService::class, $service);
    }

    /** @test */
    public function dynamic_options_service_is_registered()
    {
        $service = app(DynamicOptionsService::class);

        $this->assertInstanceOf(DynamicOptionsService::class, $service);
    }

    /** @test */
    public function model_binding_service_is_registered()
    {
        $service = app(ModelBindingService::class);

        $this->assertInstanceOf(ModelBindingService::class, $service);
    }

    /** @test */
    public function url_obfuscation_service_is_registered()
    {
        $service = app(UrlObfuscationService::class);

        $this->assertInstanceOf(UrlObfuscationService::class, $service);
    }

    /** @test */
    public function webhook_service_is_registered()
    {
        $service = app(WebhookService::class);

        $this->assertInstanceOf(WebhookService::class, $service);
    }

    /** @test */
    public function services_are_singletons()
    {
        $service1 = app(EmailNotificationService::class);
        $service2 = app(EmailNotificationService::class);

        $this->assertSame($service1, $service2);
    }
}
