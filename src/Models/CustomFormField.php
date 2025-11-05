<?php

namespace DigitalisStudios\SlickForms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomFormField extends Model
{
    use HasFactory;

    protected $table = 'slick_form_fields';

    protected static function boot()
    {
        parent::boot();

        // Auto-generate element_id if empty when model is retrieved
        static::retrieved(function ($field) {
            if (empty($field->element_id)) {
                $field->element_id = $field->field_type.'-field-'.$field->id;
                $field->saveQuietly();
            }
        });
    }

    protected $fillable = [
        'slick_form_id',
        'slick_form_page_id',
        'slick_form_layout_element_id',
        'parent_field_id',
        'field_type',
        'name',
        'element_id',
        'class',
        'style',
        'label',
        'show_label',
        'placeholder',
        'help_text',
        'help_text_as_popover',
        'validation_rules',
        'conditional_logic',
        'options',
        'order',
        'is_required',
    ];

    protected function casts(): array
    {
        return [
            'validation_rules' => 'array',
            'conditional_logic' => 'array',
            'options' => 'array',
            'is_required' => 'boolean',
            'show_label' => 'boolean',
            'help_text_as_popover' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(CustomForm::class, 'slick_form_id');
    }

    public function layoutElement(): BelongsTo
    {
        return $this->belongsTo(SlickFormLayoutElement::class, 'slick_form_layout_element_id');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(SlickFormPage::class, 'slick_form_page_id');
    }

    public function values(): HasMany
    {
        return $this->hasMany(CustomFormFieldValue::class, 'slick_form_field_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CustomFormField::class, 'parent_field_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(CustomFormField::class, 'parent_field_id')->orderBy('order');
    }

    protected static function newFactory()
    {
        return \DigitalisStudios\SlickForms\Database\Factories\CustomFormFieldFactory::new();
    }

    /**
     * Get complete schema documentation for this field's type
     *
     * @return string JSON schema documentation
     */
    public function getFullSchema(): string
    {
        $registry = app(\DigitalisStudios\SlickForms\Services\FieldTypeRegistry::class);
        $fieldType = $registry->get($this->field_type);

        return $fieldType->getFullSchema();
    }
}
