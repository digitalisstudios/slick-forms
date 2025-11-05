<?php

namespace DigitalisStudios\SlickForms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SlickFormAnalyticsSession extends Model
{
    protected $table = 'slick_form_analytics_sessions';

    protected $fillable = [
        'slick_form_id',
        'slick_form_submission_id',
        'session_id',
        'user_id',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'platform',
        'started_at',
        'submitted_at',
        'abandoned_at',
        'time_spent_seconds',
        'current_page_index',
        'referrer_url',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'abandoned_at' => 'datetime',
            'time_spent_seconds' => 'integer',
            'current_page_index' => 'integer',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(CustomForm::class, 'slick_form_id');
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(CustomFormSubmission::class, 'slick_form_submission_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(SlickFormAnalyticsEvent::class, 'slick_form_analytics_session_id');
    }

    public function isCompleted(): bool
    {
        return $this->submitted_at !== null;
    }

    public function isAbandoned(): bool
    {
        return $this->abandoned_at !== null && $this->submitted_at === null;
    }

    public function isInProgress(): bool
    {
        return $this->started_at !== null && $this->submitted_at === null && $this->abandoned_at === null;
    }

    public function getCompletionRate(): ?float
    {
        if (! $this->started_at || ! $this->submitted_at) {
            return null;
        }

        return 100.0;
    }
}
