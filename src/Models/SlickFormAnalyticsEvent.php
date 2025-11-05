<?php

namespace DigitalisStudios\SlickForms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SlickFormAnalyticsEvent extends Model
{
    protected $table = 'slick_form_analytics_events';

    public $timestamps = false;

    protected $fillable = [
        'slick_form_analytics_session_id',
        'event_type',
        'slick_form_field_id',
        'page_index',
        'event_data',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'event_data' => 'array',
            'created_at' => 'datetime',
            'page_index' => 'integer',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(SlickFormAnalyticsSession::class, 'slick_form_analytics_session_id');
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(CustomFormField::class, 'slick_form_field_id');
    }
}
