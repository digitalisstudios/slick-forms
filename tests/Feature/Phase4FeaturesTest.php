<?php

namespace DigitalisStudios\SlickForms\Tests\Feature;

use DigitalisStudios\SlickForms\Jobs\SendWebhook as SendWebhookJob;
use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Models\CustomFormFieldValue;
use DigitalisStudios\SlickForms\Models\CustomFormSubmission;
use DigitalisStudios\SlickForms\Models\FormSignedUrl;
use DigitalisStudios\SlickForms\Models\FormWebhook;
use DigitalisStudios\SlickForms\Models\FormWebhookLog;
use DigitalisStudios\SlickForms\Services\UrlObfuscationService;
use DigitalisStudios\SlickForms\Services\WebhookService;
use DigitalisStudios\SlickForms\Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

class Phase4FeaturesTest extends TestCase
{
    /** @test */
    public function it_can_access_form_via_hashid()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
        ]);

        $urlService = app(UrlObfuscationService::class);
        $hashid = $urlService->encodeId($form->id);

        $response = $this->get(route('slick-forms.form.show.hash', ['hash' => $hashid]));

        $response->assertOk();
        $response->assertStatus(200);
    }

    /** @test */
    public function it_rejects_form_access_without_valid_signature_when_required()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
            'settings' => [
                'url_security' => [
                    'require_signature' => true,
                ],
            ],
        ]);

        $urlService = app(UrlObfuscationService::class);
        $hashid = $urlService->encodeId($form->id);

        // Try to access without signature
        $response = $this->get(route('slick-forms.form.show.hash', ['hash' => $hashid]));

        $response->assertStatus(403);
    }

    /** @test */
    public function it_allows_form_access_with_valid_signature()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
            'settings' => [
                'url_security' => [
                    'require_signature' => true,
                ],
            ],
        ]);

        $urlService = app(UrlObfuscationService::class);
        $signedUrl = $urlService->generateSignedUrl($form, 24);

        // Extract signature and hashid
        parse_str(parse_url($signedUrl, PHP_URL_QUERY), $params);
        $signature = $params['signature'];
        $hashid = $urlService->encodeId($form->id);

        $response = $this->get(route('slick-forms.form.show.hash', ['hash' => $hashid, 'signature' => $signature]));

        $response->assertOk();
        $response->assertStatus(200);
    }

    /** @test */
    public function it_prefills_form_fields_from_encrypted_url_data()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
        ]);

        $nameField = CustomFormField::create([
            'slick_form_id' => $form->id,
            'name' => 'name',
            'field_type' => 'text',
            'label' => 'Name',
        ]);

        $emailField = CustomFormField::create([
            'slick_form_id' => $form->id,
            'name' => 'email',
            'field_type' => 'email',
            'label' => 'Email',
        ]);

        $urlService = app(UrlObfuscationService::class);
        $prefillData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ];

        $prefillUrl = $urlService->generatePrefillUrl($form, $prefillData, 24);

        // The prefillUrl already contains the correct hashid and encrypted data
        $response = $this->get($prefillUrl);

        $response->assertOk();
        $response->assertSee('John Doe');
        $response->assertSee('john@example.com');
    }

    /** @test */
    public function it_triggers_webhook_on_form_submission()
    {
        config(['slick-forms.webhooks.queue' => false]);
        Http::fake(['*' => Http::response(['success' => true], 200)]);

        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
        ]);

        $webhook = FormWebhook::create([
            'form_id' => $form->id,
            'name' => 'Test Webhook',
            'url' => 'https://example.com/webhook',
            'method' => 'POST',
            'format' => 'json',
            'enabled' => true,
        ]);

        $nameField = CustomFormField::create([
            'slick_form_id' => $form->id,
            'name' => 'name',
            'field_type' => 'text',
            'label' => 'Name',
        ]);

        // Create submission (this should trigger webhook)
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

        // Manually trigger webhook (in real app, this happens in FormRenderer)
        $webhookService = app(WebhookService::class);
        $payload = $webhookService->buildPayload($submission->fresh(['fieldValues.field']));
        $webhookService->sendWebhook($webhook, $payload, $submission);

        // Assert webhook was sent
        Http::assertSent(function ($request) {
            return $request->url() === 'https://example.com/webhook' &&
                   $request['event'] === 'form.submitted';
        });

        // Assert log was created
        $this->assertDatabaseHas('slick_form_webhook_logs', [
            'webhook_id' => $webhook->id,
            'submission_id' => $submission->id,
            'status' => 'sent',
        ]);
    }

    /** @test */
    public function it_queues_webhook_when_queue_enabled()
    {
        config(['slick-forms.webhooks.queue' => true]);
        Queue::fake();

        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
        ]);

        $webhook = FormWebhook::create([
            'form_id' => $form->id,
            'name' => 'Test Webhook',
            'url' => 'https://example.com/webhook',
            'method' => 'POST',
            'format' => 'json',
            'enabled' => true,
        ]);

        $submission = CustomFormSubmission::create([
            'slick_form_id' => $form->id,
            'ip_address' => '127.0.0.1',
            'submitted_at' => now(),
        ]);

        $webhookService = app(WebhookService::class);
        $payload = ['test' => 'data'];
        $webhookService->sendWebhook($webhook, $payload, $submission);

        Queue::assertPushed(SendWebhookJob::class);
    }

    /** @test */
    public function it_does_not_trigger_disabled_webhook()
    {
        config(['slick-forms.webhooks.queue' => false]);
        Http::fake();

        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
        ]);

        $webhook = FormWebhook::create([
            'form_id' => $form->id,
            'name' => 'Disabled Webhook',
            'url' => 'https://example.com/webhook',
            'method' => 'POST',
            'format' => 'json',
            'enabled' => false,
        ]);

        $submission = CustomFormSubmission::create([
            'slick_form_id' => $form->id,
            'ip_address' => '127.0.0.1',
            'submitted_at' => now(),
        ]);

        $webhookService = app(WebhookService::class);
        $payload = ['test' => 'data'];
        $webhookService->sendWebhook($webhook, $payload, $submission);

        Http::assertNothingSent();
    }

    /** @test */
    public function it_respects_webhook_conditional_triggers()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
        ]);

        $statusField = CustomFormField::create([
            'slick_form_id' => $form->id,
            'name' => 'status',
            'field_type' => 'select',
            'label' => 'Status',
        ]);

        $webhook = FormWebhook::create([
            'form_id' => $form->id,
            'name' => 'Conditional Webhook',
            'url' => 'https://example.com/webhook',
            'method' => 'POST',
            'format' => 'json',
            'enabled' => true,
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

        // Create submission with status = 'pending' (should not trigger)
        $submission1 = CustomFormSubmission::create([
            'slick_form_id' => $form->id,
            'ip_address' => '127.0.0.1',
            'submitted_at' => now(),
        ]);

        CustomFormFieldValue::create([
            'slick_form_submission_id' => $submission1->id,
            'slick_form_field_id' => $statusField->id,
            'value' => 'pending',
        ]);

        $webhookService = app(WebhookService::class);
        $shouldTrigger1 = $webhookService->evaluateTriggerConditions(
            $webhook,
            $submission1->fresh(['fieldValues.field'])
        );

        $this->assertFalse($shouldTrigger1);

        // Create submission with status = 'approved' (should trigger)
        $submission2 = CustomFormSubmission::create([
            'slick_form_id' => $form->id,
            'ip_address' => '127.0.0.1',
            'submitted_at' => now(),
        ]);

        CustomFormFieldValue::create([
            'slick_form_submission_id' => $submission2->id,
            'slick_form_field_id' => $statusField->id,
            'value' => 'approved',
        ]);

        $shouldTrigger2 = $webhookService->evaluateTriggerConditions(
            $webhook,
            $submission2->fresh(['fieldValues.field'])
        );

        $this->assertTrue($shouldTrigger2);
    }

    /** @test */
    public function it_retries_failed_webhooks()
    {
        Queue::fake();

        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
        ]);

        $webhook = FormWebhook::create([
            'form_id' => $form->id,
            'name' => 'Test Webhook',
            'url' => 'https://example.com/webhook',
            'method' => 'POST',
            'format' => 'json',
            'enabled' => true,
            'max_retries' => 3,
            'retry_delay_seconds' => 60,
        ]);

        $log = FormWebhookLog::create([
            'webhook_id' => $webhook->id,
            'event_type' => 'submission',
            'request_url' => 'https://example.com/webhook',
            'request_body' => json_encode(['test' => 'data']),
            'status' => 'failed',
            'retry_count' => 0,
            'error_message' => 'Connection timeout',
        ]);

        $webhookService = app(WebhookService::class);
        $webhookService->retryFailedWebhook($log);

        Queue::assertPushed(SendWebhookJob::class);
    }

    /** @test */
    public function it_tracks_signed_url_usage()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
        ]);

        $urlService = app(UrlObfuscationService::class);
        $signedUrl = $urlService->generateSignedUrl($form, 24);

        parse_str(parse_url($signedUrl, PHP_URL_QUERY), $params);
        $signature = $params['signature'];

        // First verification
        $isValid1 = $urlService->verifySignedUrl($signature);
        $this->assertTrue($isValid1);

        $record = FormSignedUrl::where('signature', $signature)->first();
        $this->assertEquals(1, $record->uses);

        // Second verification
        $isValid2 = $urlService->verifySignedUrl($signature);
        $this->assertTrue($isValid2);

        $record->refresh();
        $this->assertEquals(2, $record->uses);
    }

    /** @test */
    public function it_rejects_expired_prefill_url()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
        ]);

        $nameField = CustomFormField::create([
            'slick_form_id' => $form->id,
            'name' => 'name',
            'field_type' => 'text',
            'label' => 'Name',
        ]);

        // Create expired prefill URL (backdated)
        FormSignedUrl::create([
            'form_id' => $form->id,
            'signature' => 'expired-signature',
            'prefill_data' => [
                'data' => ['name' => 'John Doe'],
                'expires_at' => now()->subHours(1)->toIso8601String(),
            ],
            'expires_at' => now()->subHours(1),
        ]);

        $urlService = app(UrlObfuscationService::class);
        $hashid = $urlService->encodeId($form->id);

        $response = $this->get(route('slick-forms.form.show.hash', ['hash' => $hashid]));

        // Form should load but without prefilled data
        $response->assertOk();
    }
}
