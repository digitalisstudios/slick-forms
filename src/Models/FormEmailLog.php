<?php

namespace DigitalisStudios\SlickForms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormEmailLog extends Model
{
    protected $table = 'slick_form_email_logs';

    protected $fillable = [
        'submission_id',
        'template_id',
        'to',
        'subject',
        'body',
        'status',
        'error_message',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(CustomFormSubmission::class, 'submission_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(FormEmailTemplate::class, 'template_id');
    }

    /**
     * Check if email was sent successfully
     */
    public function wasSent(): bool
    {
        return $this->status === 'sent';
    }

    /**
     * Check if email failed to send
     */
    public function failed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if email is queued
     */
    public function isQueued(): bool
    {
        return $this->status === 'queued';
    }
}
