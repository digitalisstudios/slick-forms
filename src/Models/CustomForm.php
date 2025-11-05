<?php

namespace DigitalisStudios\SlickForms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'slick_forms';

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'is_template',
        'is_public',
        'expires_at',
        'time_limited',
        'template_category',
        'template_description',
        'preview_image',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_template' => 'boolean',
            'is_public' => 'boolean',
            'expires_at' => 'datetime',
            'time_limited' => 'integer',
            'settings' => 'array',
        ];
    }

    /**
     * Get the route key value for the model (hashid instead of numeric ID)
     */
    public function getRouteKey(): string
    {
        $urlService = app(\DigitalisStudios\SlickForms\Services\UrlObfuscationService::class);

        return $urlService->encodeId($this->id, $this->hashid_salt);
    }

    /**
     * Get the route key name for the model
     */
    public function getRouteKeyName(): string
    {
        return 'hash';
    }

    public function fields(): HasMany
    {
        return $this->hasMany(CustomFormField::class, 'slick_form_id')->orderBy('order');
    }

    public function layoutElements(): HasMany
    {
        return $this->hasMany(SlickFormLayoutElement::class, 'slick_form_id')->whereNull('parent_id')->orderBy('order');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(CustomFormSubmission::class, 'slick_form_id');
    }

    public function pages(): HasMany
    {
        return $this->hasMany(SlickFormPage::class, 'slick_form_id')->orderBy('order');
    }

    public function emailTemplates(): HasMany
    {
        return $this->hasMany(FormEmailTemplate::class, 'form_id');
    }

    public function spamLogs(): HasMany
    {
        return $this->hasMany(FormSpamLog::class, 'form_id');
    }

    public function modelBinding(): HasOne
    {
        return $this->hasOne(FormModelBinding::class, 'form_id');
    }

    public function signedUrls(): HasMany
    {
        return $this->hasMany(FormSignedUrl::class, 'form_id');
    }

    public function webhooks(): HasMany
    {
        return $this->hasMany(FormWebhook::class, 'form_id');
    }

    /**
     * Check if this form uses multi-page mode
     */
    public function isMultiPage(): bool
    {
        return ($this->settings['multi_page_enabled'] ?? false) && $this->pages()->count() > 0;
    }

    /**
     * Get progress indicator style from settings
     */
    public function getProgressStyle(): string
    {
        return $this->settings['progress_style'] ?? 'steps'; // steps, bar, breadcrumbs
    }

    /**
     * Check if back button is enabled
     */
    public function allowBackNavigation(): bool
    {
        return $this->settings['allow_back_navigation'] ?? true;
    }

    /**
     * Check if form has expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if form is available (active and not expired)
     */
    public function isAvailable(): bool
    {
        return $this->is_active && ! $this->isExpired();
    }

    /**
     * Check if form requires authentication
     */
    public function requiresAuth(): bool
    {
        return ! $this->is_public;
    }

    /**
     * Check if form has a time limit
     */
    public function hasTimeLimit(): bool
    {
        return $this->time_limited > 0;
    }

    /**
     * Get time limit in minutes for display
     */
    public function getTimeLimitInMinutes(): int
    {
        return (int) ceil($this->time_limited / 60);
    }

    /**
     * Get time limit in a human-readable format
     */
    public function getTimeLimitFormatted(): string
    {
        if (! $this->hasTimeLimit()) {
            return 'Unlimited';
        }

        $minutes = $this->getTimeLimitInMinutes();

        if ($minutes < 60) {
            return "{$minutes} minute".($minutes !== 1 ? 's' : '');
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes === 0) {
            return "{$hours} hour".($hours !== 1 ? 's' : '');
        }

        return "{$hours}h {$remainingMinutes}m";
    }

    protected static function newFactory()
    {
        return \DigitalisStudios\SlickForms\Database\Factories\CustomFormFactory::new();
    }
}
