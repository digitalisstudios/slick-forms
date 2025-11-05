<?php

namespace DigitalisStudios\SlickForms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormWebhook extends Model
{
    protected $table = 'slick_form_webhooks';

    protected $fillable = [
        'form_id',
        'name',
        'url',
        'method',
        'headers',
        'format',
        'trigger_conditions',
        'enabled',
        'max_retries',
        'retry_delay_seconds',
    ];

    protected $casts = [
        'headers' => 'array',
        'trigger_conditions' => 'array',
        'enabled' => 'boolean',
        'max_retries' => 'integer',
        'retry_delay_seconds' => 'integer',
    ];

    /**
     * Get the form this webhook belongs to
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(CustomForm::class, 'form_id');
    }

    /**
     * Get all webhook delivery logs
     */
    public function logs(): HasMany
    {
        return $this->hasMany(FormWebhookLog::class, 'webhook_id');
    }

    /**
     * Check if webhook is enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled === true;
    }

    /**
     * Get success rate percentage
     */
    public function getSuccessRate(): float
    {
        $total = $this->logs()->count();

        if ($total === 0) {
            return 0.0;
        }

        $successful = $this->logs()->where('status', 'sent')->count();

        return round(($successful / $total) * 100, 2);
    }

    /**
     * Get headers formatted for HTTP client
     * Converts from [['key' => 'Header', 'value' => 'value']] to ['Header' => 'value']
     */
    public function getFormattedHeaders(): array
    {
        $headers = $this->headers ?? [];

        // If already in correct format (associative array), return as-is
        if (empty($headers) || ! isset($headers[0])) {
            return $headers;
        }

        // Convert from array of objects to associative array
        $formatted = [];
        foreach ($headers as $header) {
            if (isset($header['key']) && isset($header['value'])) {
                $formatted[$header['key']] = $header['value'];
            }
        }

        return $formatted;
    }
}
