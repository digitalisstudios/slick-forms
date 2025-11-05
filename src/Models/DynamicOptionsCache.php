<?php

namespace DigitalisStudios\SlickForms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Dynamic Options Cache Model
 *
 * Stores cached options from remote URLs or database queries
 * to improve performance and reduce external API calls.
 */
class DynamicOptionsCache extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'slick_dynamic_options_cache';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'field_id',
        'cache_key',
        'options',
        'cached_at',
        'ttl_seconds',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'options' => 'array',
            'cached_at' => 'datetime',
            'ttl_seconds' => 'integer',
        ];
    }

    /**
     * Get the field that owns this cache entry.
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(CustomFormField::class, 'field_id');
    }

    /**
     * Check if the cached options have expired.
     */
    public function isExpired(): bool
    {
        return $this->cached_at->addSeconds($this->ttl_seconds)->isPast();
    }

    /**
     * Check if the cached options are still valid.
     */
    public function isValid(): bool
    {
        return ! $this->isExpired();
    }

    /**
     * Get the expiration timestamp.
     */
    public function expiresAt(): \Illuminate\Support\Carbon
    {
        return $this->cached_at->addSeconds($this->ttl_seconds);
    }
}
