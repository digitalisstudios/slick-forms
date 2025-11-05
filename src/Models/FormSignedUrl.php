<?php

namespace DigitalisStudios\SlickForms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormSignedUrl extends Model
{
    protected $table = 'slick_form_signed_urls';

    protected $fillable = [
        'form_id',
        'signature',
        'prefill_data',
        'expires_at',
        'max_uses',
        'uses',
    ];

    protected $casts = [
        'prefill_data' => 'array',
        'expires_at' => 'datetime',
        'max_uses' => 'integer',
        'uses' => 'integer',
    ];

    /**
     * Get the form this signed URL belongs to
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(CustomForm::class, 'form_id');
    }

    /**
     * Check if the signed URL has expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if the signed URL has reached max uses
     */
    public function hasReachedMaxUses(): bool
    {
        return $this->max_uses && $this->uses >= $this->max_uses;
    }

    /**
     * Check if the signed URL is still valid
     */
    public function isValid(): bool
    {
        return ! $this->isExpired() && ! $this->hasReachedMaxUses();
    }

    /**
     * Increment the usage counter
     */
    public function incrementUses(): void
    {
        $this->increment('uses');
    }
}
