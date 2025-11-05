<?php

namespace DigitalisStudios\SlickForms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormVersion extends Model
{
    protected $table = 'slick_form_versions';

    protected $fillable = [
        'form_id',
        'version_number',
        'version_name',
        'form_snapshot',
        'published_by',
        'change_summary',
        'published_at',
    ];

    protected $casts = [
        'form_snapshot' => 'array',
        'published_at' => 'datetime',
    ];

    /**
     * Get the form that owns this version.
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(CustomForm::class, 'form_id');
    }

    /**
     * Get the user who published this version.
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'published_by');
    }

    /**
     * Get the submissions that used this version.
     */
    public function submissions()
    {
        return $this->hasMany(CustomFormSubmission::class, 'form_version_id');
    }
}
