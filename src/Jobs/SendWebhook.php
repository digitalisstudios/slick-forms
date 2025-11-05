<?php

namespace DigitalisStudios\SlickForms\Jobs;

use DigitalisStudios\SlickForms\Models\FormWebhook;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Job to send webhook HTTP request asynchronously
 *
 * Phase 1: Skeleton class
 * Phase 2: Full implementation scheduled
 */
class SendWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job
     * Exponential backoff: 60, 120, 240 seconds
     */
    public int $backoff = 60;

    /**
     * Create a new job instance
     */
    public function __construct(
        public FormWebhook $webhook,
        public array $payload,
        public ?int $submissionId = null
    ) {}

    /**
     * Execute the job
     */
    public function handle(): void
    {
        $webhookService = app(\DigitalisStudios\SlickForms\Services\WebhookService::class);

        // Get submission if ID provided
        $submission = $this->submissionId
            ? \DigitalisStudios\SlickForms\Models\CustomFormSubmission::find($this->submissionId)
            : null;

        // Use reflection to call protected executeWebhook method
        $reflection = new \ReflectionClass($webhookService);
        $method = $reflection->getMethod('executeWebhook');
        $method->setAccessible(true);
        $method->invoke($webhookService, $this->webhook, $this->payload, $submission);
    }
}
