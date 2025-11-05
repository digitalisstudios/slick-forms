<?php

namespace DigitalisStudios\SlickForms\Tests\Unit;

use DigitalisStudios\SlickForms\Events\DynamicOptionsFailed;
use DigitalisStudios\SlickForms\Events\DynamicOptionsLoaded;
use DigitalisStudios\SlickForms\Events\EmailNotificationFailed;
use DigitalisStudios\SlickForms\Events\EmailNotificationSending;
use DigitalisStudios\SlickForms\Events\EmailNotificationSent;
use DigitalisStudios\SlickForms\Events\FormAccessDenied;
use DigitalisStudios\SlickForms\Events\FormVersionPublished;
use DigitalisStudios\SlickForms\Events\ModelBound;
use DigitalisStudios\SlickForms\Events\ModelSaved;
use DigitalisStudios\SlickForms\Events\PrefillDataDecrypted;
use DigitalisStudios\SlickForms\Events\SignedUrlGenerated;
use DigitalisStudios\SlickForms\Events\SpamDetected;
use DigitalisStudios\SlickForms\Events\WebhookFailed;
use DigitalisStudios\SlickForms\Events\WebhookSending;
use DigitalisStudios\SlickForms\Events\WebhookSent;
use DigitalisStudios\SlickForms\Jobs\RefreshDynamicOptions;
use DigitalisStudios\SlickForms\Jobs\SendEmailNotification;
use DigitalisStudios\SlickForms\Jobs\SendWebhook;
use DigitalisStudios\SlickForms\Tests\TestCase;

/**
 * Test V2 events and jobs are defined correctly
 */
class V2EventsAndJobsTest extends TestCase
{
    /** @test */
    public function email_events_exist()
    {
        $events = [
            EmailNotificationSending::class,
            EmailNotificationSent::class,
            EmailNotificationFailed::class,
        ];

        foreach ($events as $eventClass) {
            $this->assertTrue(class_exists($eventClass), "Event {$eventClass} does not exist");
        }
    }

    /** @test */
    public function spam_events_exist()
    {
        $this->assertTrue(class_exists(SpamDetected::class));
    }

    /** @test */
    public function model_binding_events_exist()
    {
        $events = [
            ModelBound::class,
            ModelSaved::class,
        ];

        foreach ($events as $eventClass) {
            $this->assertTrue(class_exists($eventClass), "Event {$eventClass} does not exist");
        }
    }

    /** @test */
    public function webhook_events_exist()
    {
        $events = [
            WebhookSending::class,
            WebhookSent::class,
            WebhookFailed::class,
        ];

        foreach ($events as $eventClass) {
            $this->assertTrue(class_exists($eventClass), "Event {$eventClass} does not exist");
        }
    }

    /** @test */
    public function form_events_exist()
    {
        $events = [
            FormVersionPublished::class,
            FormAccessDenied::class,
        ];

        foreach ($events as $eventClass) {
            $this->assertTrue(class_exists($eventClass), "Event {$eventClass} does not exist");
        }
    }

    /** @test */
    public function dynamic_options_events_exist()
    {
        $events = [
            DynamicOptionsLoaded::class,
            DynamicOptionsFailed::class,
        ];

        foreach ($events as $eventClass) {
            $this->assertTrue(class_exists($eventClass), "Event {$eventClass} does not exist");
        }
    }

    /** @test */
    public function url_events_exist()
    {
        $events = [
            SignedUrlGenerated::class,
            PrefillDataDecrypted::class,
        ];

        foreach ($events as $eventClass) {
            $this->assertTrue(class_exists($eventClass), "Event {$eventClass} does not exist");
        }
    }

    /** @test */
    public function send_email_notification_job_exists()
    {
        $this->assertTrue(class_exists(SendEmailNotification::class));
    }

    /** @test */
    public function send_webhook_job_exists()
    {
        $this->assertTrue(class_exists(SendWebhook::class));
    }

    /** @test */
    public function refresh_dynamic_options_job_exists()
    {
        $this->assertTrue(class_exists(RefreshDynamicOptions::class));
    }

    /** @test */
    public function all_jobs_implement_should_queue()
    {
        $jobs = [
            SendEmailNotification::class,
            SendWebhook::class,
            RefreshDynamicOptions::class,
        ];

        foreach ($jobs as $jobClass) {
            $reflection = new \ReflectionClass($jobClass);
            $interfaces = $reflection->getInterfaceNames();

            $this->assertContains(
                'Illuminate\Contracts\Queue\ShouldQueue',
                $interfaces,
                "Job {$jobClass} should implement ShouldQueue"
            );
        }
    }
}
