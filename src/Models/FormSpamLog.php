<?php

namespace DigitalisStudios\SlickForms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormSpamLog extends Model
{
    protected $table = 'slick_form_spam_logs';

    protected $fillable = [
        'form_id',
        'ip_address',
        'detection_method',
        'details',
    ];

    protected function casts(): array
    {
        return [
            'details' => 'array',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(CustomForm::class, 'form_id');
    }

    /**
     * Check if this was detected by honeypot
     */
    public function isHoneypot(): bool
    {
        return $this->detection_method === 'honeypot';
    }

    /**
     * Check if this was detected by reCAPTCHA
     */
    public function isRecaptcha(): bool
    {
        return $this->detection_method === 'recaptcha';
    }

    /**
     * Check if this was detected by hCaptcha
     */
    public function isHcaptcha(): bool
    {
        return $this->detection_method === 'hcaptcha';
    }

    /**
     * Check if this was detected by rate limiting
     */
    public function isRateLimit(): bool
    {
        return $this->detection_method === 'rate_limit';
    }
}
