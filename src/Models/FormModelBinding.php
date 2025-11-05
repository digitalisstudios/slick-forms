<?php

namespace DigitalisStudios\SlickForms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Form Model Binding
 *
 * Defines how a form binds to an Eloquent model for pre-filling
 * and saving data directly to database models.
 */
class FormModelBinding extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'slick_form_model_bindings';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'form_id',
        'model_class',
        'route_parameter',
        'route_key',
        'field_mappings',
        'relationship_mappings',
        'allow_create',
        'allow_update',
        'custom_population_logic',
        'custom_save_logic',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'field_mappings' => 'array',
            'relationship_mappings' => 'array',
            'allow_create' => 'boolean',
            'allow_update' => 'boolean',
        ];
    }

    /**
     * Get the form that owns this model binding.
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(CustomForm::class, 'form_id');
    }

    /**
     * Get the mapped model class instance.
     */
    public function getModelInstance(): ?object
    {
        if (! class_exists($this->model_class)) {
            return null;
        }

        return new $this->model_class;
    }

    /**
     * Check if model binding allows creating new records.
     */
    public function allowsCreate(): bool
    {
        return $this->allow_create === true;
    }

    /**
     * Check if model binding allows updating existing records.
     */
    public function allowsUpdate(): bool
    {
        return $this->allow_update === true;
    }

    protected static function newFactory()
    {
        return \DigitalisStudios\SlickForms\Database\Factories\FormModelBindingFactory::new();
    }
}
