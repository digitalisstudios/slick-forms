<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;

class StarRatingField extends BaseFieldType
{
    public function getName(): string
    {
        return 'star_rating';
    }

    public function getLabel(): string
    {
        return 'Star Rating';
    }

    public function getIcon(): string
    {
        return 'bi bi-star-fill';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $maxStars = $field->options['max_stars'] ?? 5;
        $currentValue = $value ?? 0;

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';
        // Note: Star rating uses custom icons, floating labels won't work
        $fieldId = 'field_'.$field->id;
        // Explicit label to ensure required asterisk is rendered consistently
        $html .= $this->renderLabel($field, $fieldId, true);

        $html .= '<div x-data="{ rating: '.(int) $currentValue.', hoverRating: 0 }">';
        $html .= '<input type="hidden" '.$this->getWireModelAttribute($field).'="formData.field_'.$field->id.'" '.$this->getValidationAttributes($field).' x-model="rating">';

        $html .= '<div class="d-flex gap-1" style="font-size: 2rem;">';
        for ($i = 1; $i <= $maxStars; $i++) {
            $html .= '<i ';
            $html .= 'class="bi" ';
            $html .= ':class="(hoverRating >= '.$i.' || (hoverRating === 0 && rating >= '.$i.')) ? \'bi-star-fill text-warning\' : \'bi-star text-muted\'" ';
            $html .= 'style="cursor: pointer;" ';
            $html .= '@click="rating = '.$i.'; $wire.set(\'formData.field_'.$field->id.'\', '.$i.')" ';
            $html .= '@mouseenter="hoverRating = '.$i.'" ';
            $html .= '@mouseleave="hoverRating = 0"';
            $html .= '></i>';
        }
        $html .= '</div>';

        $html .= '<div class="mt-2 text-muted small" x-show="rating > 0">';
        $html .= '<span x-text="rating"></span> out of '.$maxStars.' stars';
        $html .= '</div>';

        $html .= '</div>';
        $html .= $this->renderInvalidFeedback($field);
        $html .= $this->renderValidFeedback($field);
        $html .= $this->renderHelpText($field);

        $html .= '</div>';

        return $html;
    }

    public function renderBuilder(CustomFormField $field): string
    {
        $maxStars = $field->options['max_stars'] ?? 5;

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';
        $html .= $this->renderLabel($field, $elementId ?? '');

        $html .= '<div class="d-flex gap-1" style="font-size: 2rem;">';
        for ($i = 1; $i <= $maxStars; $i++) {
            $html .= '<i class="bi bi-star text-muted"></i>';
        }
        $html .= '</div>';
        $html .= $this->renderHelpText($field);

        $html .= '</div>';

        return $html;
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        $rules = parent::validate($field, $value);
        $rules[] = 'integer';

        $maxStars = $field->options['max_stars'] ?? 5;
        // Allow 0 as a valid default that satisfies required
        $rules[] = 'min:0';
        $rules[] = 'max:'.$maxStars;

        return $rules;
    }

    public function getDefaultValue(CustomFormField $field): mixed
    {
        // Default to 0 so required validation passes without user interaction
        return 0;
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'max_stars' => [
                'type' => 'number',
                'label' => 'Maximum Stars',
                'tab' => 'options',
                'target' => 'options',
                'default' => '5',
                'required' => false,
                'help' => 'Number of stars to display (e.g., 5 for 5-star rating)',
            ],
        ]);
    }

    public function getAvailableValidationOptions(): array
    {
        return [
            'min' => [
                'type' => 'number',
                'label' => 'Minimum Rating',
                'help' => 'Minimum star rating required (e.g., 3 for at least 3 stars)',
                'rule_format' => 'min:{value}',
                'placeholder' => '1',
            ],
        ];
    }
}
