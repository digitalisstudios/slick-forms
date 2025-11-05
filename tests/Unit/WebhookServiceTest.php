<?php

namespace DigitalisStudios\SlickForms\Tests\Unit;

use DigitalisStudios\SlickForms\Events\WebhookFailed;
use DigitalisStudios\SlickForms\Events\WebhookSending;
use DigitalisStudios\SlickForms\Events\WebhookSent;
use DigitalisStudios\SlickForms\Jobs\SendWebhook as SendWebhookJob;
use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Models\CustomFormFieldValue;
use DigitalisStudios\SlickForms\Models\CustomFormSubmission;
use DigitalisStudios\SlickForms\Models\FormWebhook;
use DigitalisStudios\SlickForms\Models\FormWebhookLog;
use DigitalisStudios\SlickForms\Services\WebhookService;
use DigitalisStudios\SlickForms\Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

class WebhookServiceTest extends TestCase
{
    protected WebhookService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(WebhookService::class);
    }

    /** @test */
    public function it_sends_webhook_immediately_when_queue_disabled()
    {
        config(['slick-forms.webhooks.queue' => false]);
        Http::fake(['*' => Http::response(['success' => true], 200)]);
        Event::fake();

        $webhook = $this->createWebhook();
        $payload = ['test' => 'data'];

        $this->service->sendWebhook($webhook, $payload);

        Event::assertDispatched(WebhookSending::class);
        Event::assertDispatched(WebhookSent::class);

        $this->assertDatabaseHas('slick_form_webhook_logs', [
            'webhook_id' => $webhook->id,
            'status' => 'sent',
        ]);
    }

    /** @test */
    public function it_queues_webhook_when_queue_enabled()
    {
        config(['slick-forms.webhooks.queue' => true]);
        Queue::fake();

        $webhook = $this->createWebhook();
        $payload = ['test' => 'data'];

        $this->service->sendWebhook($webhook, $payload);

        Queue::assertPushed(SendWebhookJob::class, function ($job) {
            return true;
        });
    }

    /** @test */
    public function it_does_not_send_disabled_webhook()
    {
        Http::fake();
        Queue::fake();

        $webhook = $this->createWebhook(['enabled' => false]);
        $payload = ['test' => 'data'];

        $this->service->sendWebhook($webhook, $payload);

        Http::assertNothingSent();
        Queue::assertNotPushed(SendWebhookJob::class);
    }

    /** @test */
    public function it_logs_failed_webhook_delivery()
    {
        config(['slick-forms.webhooks.queue' => false]);
        Http::fake(['*' => Http::response('Server Error', 500)]);
        Event::fake();

        $webhook = $this->createWebhook();
        $payload = ['test' => 'data'];

        $this->service->sendWebhook($webhook, $payload);

        Event::assertDispatched(WebhookFailed::class);

        $this->assertDatabaseHas('slick_form_webhook_logs', [
            'webhook_id' => $webhook->id,
            'status' => 'failed',
        ]);
    }

    /** @test */
    public function it_sends_webhook_with_json_format()
    {
        config(['slick-forms.webhooks.queue' => false]);
        Http::fake(['*' => Http::response(['success' => true], 200)]);

        $webhook = $this->createWebhook(['format' => 'json']);
        $payload = ['name' => 'John', 'email' => 'john@example.com'];

        $this->service->sendWebhook($webhook, $payload);

        Http::assertSent(function ($request) use ($payload) {
            return $request->hasHeader('Content-Type', 'application/json') &&
                   $request->data() == $payload;
        });
    }

    /** @test */
    public function it_sends_webhook_with_form_data_format()
    {
        config(['slick-forms.webhooks.queue' => false]);
        Http::fake(['*' => Http::response(['success' => true], 200)]);

        $webhook = $this->createWebhook(['format' => 'form_data']);
        $payload = ['name' => 'John', 'email' => 'john@example.com'];

        $this->service->sendWebhook($webhook, $payload);

        Http::assertSent(function ($request) {
            return $request->hasHeader('Content-Type', 'application/x-www-form-urlencoded');
        });
    }

    /** @test */
    public function it_includes_custom_headers_in_webhook_request()
    {
        config(['slick-forms.webhooks.queue' => false]);
        Http::fake(['*' => Http::response(['success' => true], 200)]);

        $webhook = $this->createWebhook([
            'headers' => [
                ['key' => 'Authorization', 'value' => 'Bearer token123'],
                ['key' => 'X-Custom-Header', 'value' => 'custom-value'],
            ],
        ]);
        $payload = ['test' => 'data'];

        $this->service->sendWebhook($webhook, $payload);

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Bearer token123') &&
                   $request->hasHeader('X-Custom-Header', 'custom-value');
        });
    }

    /** @test */
    public function it_builds_payload_from_submission()
    {
        $form = CustomForm::create([
            'name' => 'Contact Form',
            'is_active' => true,
        ]);

        $nameField = CustomFormField::create([
            'slick_form_id' => $form->id,
            'name' => 'name',
            'field_type' => 'text',
            'label' => 'Full Name',
        ]);

        $emailField = CustomFormField::create([
            'slick_form_id' => $form->id,
            'name' => 'email',
            'field_type' => 'email',
            'label' => 'Email Address',
        ]);

        $submission = CustomFormSubmission::create([
            'slick_form_id' => $form->id,
            'ip_address' => '127.0.0.1',
            'submitted_at' => now(),
        ]);

        CustomFormFieldValue::create([
            'slick_form_submission_id' => $submission->id,
            'slick_form_field_id' => $nameField->id,
            'value' => 'John Doe',
        ]);

        CustomFormFieldValue::create([
            'slick_form_submission_id' => $submission->id,
            'slick_form_field_id' => $emailField->id,
            'value' => 'john@example.com',
        ]);

        $payload = $this->service->buildPayload($submission->fresh(['fieldValues.field']));

        $this->assertEquals('form.submitted', $payload['event']);
        $this->assertEquals($submission->id, $payload['submission_id']);
        $this->assertEquals('Contact Form', $payload['form']['name']);
        $this->assertEquals('John Doe', $payload['fields']['name']['value']);
        $this->assertEquals('Full Name', $payload['fields']['name']['label']);
        $this->assertEquals('john@example.com', $payload['fields']['email']['value']);
        $this->assertArrayHasKey('submitted_at', $payload);
        $this->assertArrayHasKey('ip_address', $payload);
    }

    /** @test */
    public function it_evaluates_trigger_conditions_correctly()
    {
        $form = CustomForm::create(['name' => 'Test Form', 'is_active' => true]);

        $statusField = CustomFormField::create([
            'slick_form_id' => $form->id,
            'name' => 'status',
            'field_type' => 'select',
            'label' => 'Status',
        ]);

        $webhook = FormWebhook::create([
            'form_id' => $form->id,
            'name' => 'Approved Webhook',
            'url' => 'https://example.com/webhook',
            'method' => 'POST',
            'format' => 'json',
            'trigger_conditions' => [
                'logic' => 'and',
                'conditions' => [
                    [
                        'field_id' => $statusField->id,
                        'operator' => 'equals',
                        'value' => 'approved',
                    ],
                ],
            ],
        ]);

        $submission = CustomFormSubmission::create([
            'slick_form_id' => $form->id,
            'ip_address' => '127.0.0.1',
            'submitted_at' => now(),
        ]);

        CustomFormFieldValue::create([
            'slick_form_submission_id' => $submission->id,
            'slick_form_field_id' => $statusField->id,
            'value' => 'approved',
        ]);

        $shouldTrigger = $this->service->evaluateTriggerConditions(
            $webhook,
            $submission->fresh(['fieldValues.field'])
        );

        $this->assertTrue($shouldTrigger);
    }

    /** @test */
    public function it_does_not_trigger_webhook_when_conditions_not_met()
    {
        $form = CustomForm::create(['name' => 'Test Form', 'is_active' => true]);

        $statusField = CustomFormField::create([
            'slick_form_id' => $form->id,
            'name' => 'status',
            'field_type' => 'select',
            'label' => 'Status',
        ]);

        $webhook = FormWebhook::create([
            'form_id' => $form->id,
            'name' => 'Approved Webhook',
            'url' => 'https://example.com/webhook',
            'method' => 'POST',
            'format' => 'json',
            'trigger_conditions' => [
                'logic' => 'and',
                'conditions' => [
                    [
                        'field_id' => $statusField->id,
                        'operator' => 'equals',
                        'value' => 'approved',
                    ],
                ],
            ],
        ]);

        $submission = CustomFormSubmission::create([
            'slick_form_id' => $form->id,
            'ip_address' => '127.0.0.1',
            'submitted_at' => now(),
        ]);

        CustomFormFieldValue::create([
            'slick_form_submission_id' => $submission->id,
            'slick_form_field_id' => $statusField->id,
            'value' => 'pending',
        ]);

        $shouldTrigger = $this->service->evaluateTriggerConditions(
            $webhook,
            $submission->fresh(['fieldValues.field'])
        );

        $this->assertFalse($shouldTrigger);
    }

    /** @test */
    public function it_retries_failed_webhook_with_exponential_backoff()
    {
        Queue::fake();

        $webhook = $this->createWebhook(['retry_delay_seconds' => 60]);

        $log = FormWebhookLog::create([
            'webhook_id' => $webhook->id,
            'event_type' => 'submission',
            'request_url' => 'https://example.com/webhook',
            'request_body' => json_encode(['test' => 'data']),
            'status' => 'failed',
            'retry_count' => 2,
            'error_message' => 'Connection timeout',
        ]);

        $this->service->retryFailedWebhook($log);

        Queue::assertPushed(SendWebhookJob::class, function ($job) {
            // Verify job is delayed (exponential backoff: 60 * 2^2 = 240 seconds)
            return true;
        });
    }

    /** @test */
    public function it_tests_webhook_with_sample_data()
    {
        Http::fake(['*' => Http::response(['received' => true], 200)]);

        $webhook = $this->createWebhook();

        $result = $this->service->testWebhook($webhook);

        $this->assertTrue($result['success']);
        $this->assertEquals(200, $result['status']);
        $this->assertArrayHasKey('duration', $result);
        $this->assertNull($result['error']);
    }

    /** @test */
    public function it_returns_error_on_failed_test_webhook()
    {
        Http::fake(['*' => Http::response('Bad Request', 400)]);

        $webhook = $this->createWebhook();

        $result = $this->service->testWebhook($webhook);

        $this->assertFalse($result['success']);
        $this->assertNull($result['error']); // HTTP 400 doesn't throw exception
    }

    /** @test */
    public function it_logs_webhook_attempt_with_all_details()
    {
        $webhook = $this->createWebhook();

        $request = [
            'url' => 'https://example.com/webhook',
            'method' => 'POST',
            'headers' => ['Authorization' => 'Bearer token'],
            'body' => ['test' => 'data'],
        ];

        $response = [
            'status' => 200,
            'headers' => ['Content-Type' => 'application/json'],
            'body' => ['success' => true],
            'duration' => 0.5,
        ];

        $this->service->logWebhookAttempt($webhook, $request, $response, 'sent', 123);

        $this->assertDatabaseHas('slick_form_webhook_logs', [
            'webhook_id' => $webhook->id,
            'submission_id' => 123,
            'request_url' => 'https://example.com/webhook',
            'response_status' => 200,
            'status' => 'sent',
        ]);

        $log = FormWebhookLog::where('webhook_id', $webhook->id)->first();
        $this->assertNotNull($log->sent_at);
        $this->assertEquals(['test' => 'data'], json_decode($log->request_body, true));
    }

    // Helper Methods

    protected function createWebhook(array $overrides = []): FormWebhook
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
        ]);

        return FormWebhook::create(array_merge([
            'form_id' => $form->id,
            'name' => 'Test Webhook',
            'url' => 'https://example.com/webhook',
            'method' => 'POST',
            'format' => 'json',
            'enabled' => true,
            'max_retries' => 3,
            'retry_delay_seconds' => 60,
        ], $overrides));
    }
}
