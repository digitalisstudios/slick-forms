<?php

namespace DigitalisStudios\SlickForms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormEmailTemplate extends Model
{
    protected $table = 'slick_form_email_templates';

    protected $fillable = [
        'form_id',
        'type',
        'enabled',
        'recipients',
        'subject',
        'body_template',
        'attach_pdf',
        'conditional_rules',
        'priority',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'recipients' => 'array',
            'attach_pdf' => 'boolean',
            'conditional_rules' => 'array',
            'priority' => 'integer',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(CustomForm::class, 'form_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(FormEmailLog::class, 'template_id');
    }

    /**
     * Check if this template is for admin notifications
     */
    public function isAdminTemplate(): bool
    {
        return $this->type === 'admin';
    }

    /**
     * Check if this template is for user confirmations
     */
    public function isUserTemplate(): bool
    {
        return $this->type === 'user_confirmation';
    }
}
