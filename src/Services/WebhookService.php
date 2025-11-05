<?php

namespace DigitalisStudios\SlickForms\Services;

use DigitalisStudios\SlickForms\Events\WebhookFailed;
use DigitalisStudios\SlickForms\Events\WebhookSending;
use DigitalisStudios\SlickForms\Events\WebhookSent;
use DigitalisStudios\SlickForms\Jobs\SendWebhook as SendWebhookJob;
use DigitalisStudios\SlickForms\Models\CustomFormSubmission;
use DigitalisStudios\SlickForms\Models\FormWebhook;
use DigitalisStudios\SlickForms\Models\FormWebhookLog;
use Illuminate\Support\Facades\Http;

/**
 * Service for handling webhook integrations
 */
class WebhookService
{
    /**
     * Send webhook HTTP request for a submission
     *
     * @param  FormWebhook  $webhook  The webhook configuration
     * @param  array  $payload  Data to send in webhook request
     * @param  CustomFormSubmission|null  $submission  Optional submission context
     */
    public function sendWebhook(FormWebhook $webhook, array $payload, ?CustomFormSubmission $submission = null): void
    {
        // Check if webhook is enabled
        if (! $webhook->isEnabled()) {
            return;
        }

        // Check if webhooks are enabled globally
        if (! config('slick-forms.webhooks.enabled', true)) {
            return;
        }

        // Dispatch event before sending
        event(new WebhookSending($webhook, $payload));

        // Queue the webhook if configured
        if (config('slick-forms.webhooks.queue', true)) {
            SendWebhookJob::dispatch($webhook, $payload, $submission?->id);

            return;
        }

        // Send synchronously
        $this->executeWebhook($webhook, $payload, $submission);
    }

    /**
     * Execute webhook HTTP request (called by job or synchronously)
     *
     * @param  FormWebhook  $webhook  The webhook configuration
     * @param  array  $payload  Data to send
     * @param  CustomFormSubmission|null  $submission  Optional submission
     */
    protected function executeWebhook(FormWebhook $webhook, array $payload, ?CustomFormSubmission $submission = null): void
    {
        $startTime = microtime(true);

        try {
            // Build HTTP request
            $request = Http::timeout(config('slick-forms.webhooks.timeout', 30))
                ->withHeaders($webhook->getFormattedHeaders());

            // Send based on format
            $response = match ($webhook->format) {
                'form_data' => $request->asForm()->{strtolower($webhook->method)}($webhook->url, $payload),
                'xml' => $request->withBody(array_to_xml($payload), 'application/xml')->send($webhook->method, $webhook->url),
                default => $request->asJson()->{strtolower($webhook->method)}($webhook->url, $payload),
            };

            $duration = microtime(true) - $startTime;

            // Check if response was successful (2xx status code)
            if (! $response->successful()) {
                throw new \Exception("Webhook failed with status {$response->status()}: {$response->body()}");
            }

            // Log successful delivery
            $this->logWebhookAttempt($webhook, [
                'url' => $webhook->url,
                'method' => $webhook->method,
                'headers' => $webhook->headers,
                'body' => $payload,
            ], [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body(),
                'duration' => $duration,
            ], 'sent', $submission?->id);

            // Dispatch success event
            event(new WebhookSent($webhook, $response->json() ?? []));
        } catch (\Exception $e) {
            $duration = microtime(true) - $startTime;

            // Log failed delivery
            $this->logWebhookAttempt($webhook, [
                'url' => $webhook->url,
                'method' => $webhook->method,
                'headers' => $webhook->headers,
                'body' => $payload,
            ], [
                'error' => $e->getMessage(),
                'duration' => $duration,
            ], 'failed', $submission?->id, $e->getMessage());

            // Dispatch failure event
            event(new WebhookFailed($webhook, $e->getMessage()));
        }
    }

    /**
     * Retry a failed webhook delivery
     *
     * @param  FormWebhookLog  $log  Previous failed webhook log entry
     */
    public function retryFailedWebhook(FormWebhookLog $log): void
    {
        $webhook = $log->webhook;

        // Check if webhook can be retried
        if (! $log->canRetry()) {
            return;
        }

        // Calculate exponential backoff delay
        $retryDelay = $webhook->retry_delay_seconds * pow(2, $log->retry_count);

        // Parse payload from request body
        $payload = is_string($log->request_body) ? json_decode($log->request_body, true) : [];

        // Queue retry with delay
        SendWebhookJob::dispatch($webhook, $payload, $log->submission_id)
            ->delay(now()->addSeconds($retryDelay));
    }

    /**
     * Test webhook configuration with sample data
     *
     * @param  FormWebhook  $webhook  The webhook to test
     * @return array Test results including status, response, timing
     */
    public function testWebhook(FormWebhook $webhook): array
    {
        $startTime = microtime(true);

        // Build test payload
        $testPayload = [
            'test' => true,
            'webhook_name' => $webhook->name,
            'form_id' => $webhook->form_id,
            'form_name' => $webhook->form->name,
            'timestamp' => now()->toIso8601String(),
            'sample_data' => [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'message' => 'This is a test webhook delivery',
            ],
        ];

        try {
            // Send test request
            $request = Http::timeout(config('slick-forms.webhooks.timeout', 30))
                ->withHeaders($webhook->headers ?? []);

            $response = match ($webhook->format) {
                'form_data' => $request->asForm()->{strtolower($webhook->method)}($webhook->url, $testPayload),
                'xml' => $request->withBody(array_to_xml($testPayload), 'application/xml')->send($webhook->method, $webhook->url),
                default => $request->asJson()->{strtolower($webhook->method)}($webhook->url, $testPayload),
            };

            $duration = microtime(true) - $startTime;

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'response_body' => $response->body(),
                'response_headers' => $response->headers(),
                'duration' => round($duration, 3),
                'error' => null,
            ];
        } catch (\Exception $e) {
            $duration = microtime(true) - $startTime;

            return [
                'success' => false,
                'status' => null,
                'response_body' => null,
                'response_headers' => null,
                'duration' => round($duration, 3),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Build webhook payload from submission data
     *
     * @param  CustomFormSubmission  $submission  The form submission
     * @return array Formatted payload data
     */
    public function buildPayload(CustomFormSubmission $submission): array
    {
        $form = $submission->form;
        $values = $submission->fieldValues;

        // Build field data array
        $fieldData = [];
        foreach ($values as $value) {
            $field = $value->field;
            $fieldData[$field->name] = [
                'value' => $value->value,
                'label' => $field->label,
                'type' => $field->field_type,
            ];
        }

        return [
            'event' => 'form.submitted',
            'submission_id' => $submission->id,
            'form' => [
                'id' => $form->id,
                'name' => $form->name,
                'description' => $form->description,
            ],
            'submitted_at' => $submission->created_at->toIso8601String(),
            'ip_address' => $submission->ip_address,
            'fields' => $fieldData,
        ];
    }

    /**
     * Evaluate trigger conditions to determine if webhook should fire
     *
     * @param  FormWebhook  $webhook  The webhook configuration
     * @param  CustomFormSubmission  $submission  The submission to evaluate
     * @return bool True if webhook should be triggered
     */
    public function evaluateTriggerConditions(FormWebhook $webhook, CustomFormSubmission $submission): bool
    {
        // If no conditions set, always trigger
        $conditions = $webhook->trigger_conditions;
        if (empty($conditions)) {
            return true;
        }

        // Use ConditionalLogicEvaluator for consistent condition evaluation
        $evaluator = app(\DigitalisStudios\SlickForms\Services\ConditionalLogicEvaluator::class);

        // Build submission data array
        $submissionData = [];
        foreach ($submission->fieldValues ?? [] as $value) {
            $submissionData[$value->field->name] = $value->value;
        }

        // Evaluate conditions
        return $evaluator->evaluateConditions($conditions, $submissionData, $submission->form);
    }

    /**
     * Log webhook delivery attempt
     *
     * @param  FormWebhook  $webhook  The webhook
     * @param  array  $request  Request data sent
     * @param  array  $response  Response received
     * @param  string  $status  Status: 'sent', 'failed', 'pending'
     * @param  int|null  $submissionId  Optional submission ID
     * @param  string|null  $errorMessage  Optional error message
     */
    public function logWebhookAttempt(
        FormWebhook $webhook,
        array $request,
        array $response,
        string $status,
        ?int $submissionId = null,
        ?string $errorMessage = null
    ): void {
        FormWebhookLog::create([
            'webhook_id' => $webhook->id,
            'submission_id' => $submissionId,
            'event_type' => 'submission',
            'request_url' => $request['url'] ?? $webhook->url,
            'request_headers' => $request['headers'] ?? null,
            'request_body' => is_array($request['body'] ?? null) ? json_encode($request['body']) : null,
            'response_status' => $response['status'] ?? null,
            'response_headers' => $response['headers'] ?? null,
            'response_body' => is_array($response['body'] ?? null) ? json_encode($response['body']) : ($response['body'] ?? null),
            'status' => $status,
            'retry_count' => 0,
            'error_message' => $errorMessage,
            'sent_at' => $status === 'sent' ? now() : null,
        ]);
    }
}
