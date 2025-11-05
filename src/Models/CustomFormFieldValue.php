<?php

namespace DigitalisStudios\SlickForms\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomFormFieldValue extends Model
{
    protected $table = 'slick_form_field_values';

    protected $fillable = [
        'slick_form_submission_id',
        'slick_form_field_id',
        'value',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(CustomFormSubmission::class, 'slick_form_submission_id');
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(CustomFormField::class, 'slick_form_field_id');
    }

    /**
     * Sanitize the value attribute to ensure valid UTF-8 encoding
     */
    protected function value(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($value === null) {
                    return null;
                }

                // Convert to UTF-8 if not already, removing invalid characters
                return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            }
        );
    }
}
