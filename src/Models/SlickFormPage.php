<?php

namespace DigitalisStudios\SlickForms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SlickFormPage extends Model
{
    protected $fillable = [
        'slick_form_id',
        'title',
        'description',
        'order',
        'icon',
        'show_in_progress',
        'settings',
    ];

    protected $casts = [
        'show_in_progress' => 'boolean',
        'settings' => 'array',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(CustomForm::class, 'slick_form_id');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(CustomFormField::class, 'slick_form_page_id')->orderBy('order');
    }

    public function layoutElements(): HasMany
    {
        return $this->hasMany(SlickFormLayoutElement::class, 'slick_form_page_id')
            ->whereNull('parent_id')
            ->orderBy('order');
    }

    /**
     * Get all fields and layout elements for this page in order
     */
    public function getStructure(): array
    {
        $fields = $this->fields()
            ->whereNull('slick_form_layout_element_id')
            ->whereNull('parent_field_id')
            ->get();

        $elements = $this->layoutElements()->with('children')->get();

        // Combine and sort by order
        $combined = collect([])
            ->concat($fields->map(fn ($f) => ['type' => 'field', 'data' => $f, 'order' => $f->order]))
            ->concat($elements->map(fn ($e) => ['type' => 'element', 'data' => $e, 'order' => $e->order]))
            ->sortBy('order')
            ->values()
            ->all();

        return $combined;
    }

    /**
     * Check if this page has any content
     */
    public function hasContent(): bool
    {
        return $this->fields()->exists() || $this->layoutElements()->exists();
    }

    /**
     * Get the next page in sequence
     */
    public function nextPage(): ?self
    {
        return self::where('slick_form_id', $this->slick_form_id)
            ->where('order', '>', $this->order)
            ->orderBy('order')
            ->first();
    }

    /**
     * Get the previous page in sequence
     */
    public function previousPage(): ?self
    {
        return self::where('slick_form_id', $this->slick_form_id)
            ->where('order', '<', $this->order)
            ->orderBy('order', 'desc')
            ->first();
    }

    /**
     * Check if this is the first page
     */
    public function isFirstPage(): bool
    {
        return ! $this->previousPage();
    }

    /**
     * Check if this is the last page
     */
    public function isLastPage(): bool
    {
        return ! $this->nextPage();
    }
}
