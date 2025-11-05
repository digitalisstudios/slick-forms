<?php

namespace DigitalisStudios\SlickForms\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomFormSubmission extends Model
{
    use HasFactory;

    protected $table = 'slick_form_submissions';

    protected static function newFactory()
    {
        return \DigitalisStudios\SlickForms\Database\Factories\CustomFormSubmissionFactory::new();
    }

    protected $fillable = [
        'slick_form_id',
        'user_id',
        'ip_address',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(CustomForm::class, 'slick_form_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fieldValues(): HasMany
    {
        return $this->hasMany(CustomFormFieldValue::class, 'slick_form_submission_id');
    }

    /**
     * Sanitize the ip_address attribute to ensure valid UTF-8 encoding
     */
    protected function ipAddress(): Attribute
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
