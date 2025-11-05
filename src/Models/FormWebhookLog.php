<?php

namespace DigitalisStudios\SlickForms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormWebhookLog extends Model
{
    protected $table = 'slick_form_webhook_logs';

    protected $fillable = [
        'webhook_id',
        'submission_id',
        'event_type',
        'request_url',
        'request_headers',
        'request_body',
        'response_status',
        'response_headers',
        'response_body',
        'status',
        'retry_count',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'request_headers' => 'array',
        'response_headers' => 'array',
        'response_status' => 'integer',
        'retry_count' => 'integer',
        'sent_at' => 'datetime',
    ];

    /**
     * Get the webhook this log belongs to
     */
    public function webhook(): BelongsTo
    {
        return $this->belongsTo(FormWebhook::class, 'webhook_id');
    }

    /**
     * Get the submission that triggered this webhook (optional)
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(CustomFormSubmission::class, 'submission_id');
    }

    /**
     * Check if webhook was sent successfully
     */
    public function wasSent(): bool
    {
        return $this->status === 'sent';
    }

    /**
     * Check if webhook failed
     */
    public function failed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if webhook is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if webhook can be retried
     */
    public function canRetry(): bool
    {
        return $this->failed() && $this->retry_count < $this->webhook->max_retries;
    }
}
