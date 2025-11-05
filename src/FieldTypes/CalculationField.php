<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;

class CalculationField extends BaseFieldType
{
    public function getName(): string
    {
        return 'calculation';
    }

    public function getLabel(): string
    {
        return 'Calculation';
    }

    public function getIcon(): string
    {
        return 'bi bi-calculator';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $formula = $field->options['formula'] ?? '';
        $displayAs = $field->options['display_as'] ?? 'number';
        $decimalPlaces = $field->options['decimal_places'] ?? 2;
        $prefix = $field->options['prefix'] ?? '';
        $suffix = $field->options['suffix'] ?? '';
        $displayMode = $field->options['display_mode'] ?? 'visible';

        // If hidden, render hidden input only
        if ($displayMode === 'hidden') {
            return '<input type="hidden" wire:model="formData.field_'.$field->id.'">';
        }

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';

        // Pass true for $includeAsterisk since CSS can't reach the nested input in input-group
        $fieldId = 'field_'.$field->id;
        $html .= $this->renderLabel($field, $fieldId, true);

        // Display calculated value (read-only)
        $html .= '<div class="input-group">';

        // Prefix
        if ($prefix) {
            $html .= '<span class="input-group-text">'.htmlspecialchars($prefix).'</span>';
        }

        // Read-only input showing calculated value
        $html .= '<input type="text" ';
        $html .= 'class="form-control" ';
        $html .= 'id="'.$fieldId.'" ';
        $html .= 'readonly ';
        $html .= 'wire:model="formData.field_'.$field->id.'" ';
        $html .= 'placeholder="'.($value !== null ? htmlspecialchars($value) : 'Calculated value').'" ';
        $html .= 'style="background-color: #f8f9fa;">';

        // Suffix
        if ($suffix) {
            $html .= '<span class="input-group-text">'.htmlspecialchars($suffix).'</span>';
        }

        $html .= '</div>';

        // Show formula in help text if no custom help text
        if (! $field->help_text && $formula) {
            $html .= '<div class="form-text small text-muted">';
            $html .= '<i class="bi bi-calculator me-1"></i>';
            $html .= 'Formula: '.htmlspecialchars($formula);
            $html .= '</div>';
        } else {
            $html .= $this->renderInvalidFeedback($field);
            $html .= $this->renderValidFeedback($field);
            $html .= $this->renderHelpText($field);
        }

        $html .= $this->renderValidationFeedback($field);

        $html .= '</div>';

        return $html;
    }

    public function renderBuilder(CustomFormField $field): string
    {
        $formula = $field->options['formula'] ?? '';
        $prefix = $field->options['prefix'] ?? '';
        $suffix = $field->options['suffix'] ?? '';

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';

        // Pass true for $includeAsterisk since CSS can't reach the nested input in input-group
        $html .= $this->renderLabel($field, 'field_'.$field->id, true);

        $html .= '<div class="input-group">';

        if ($prefix) {
            $html .= '<span class="input-group-text">'.htmlspecialchars($prefix).'</span>';
        }

        $html .= '<input type="text" class="form-control" value="[Calculated]" readonly disabled style="background-color: #e9ecef;">';

        if ($suffix) {
            $html .= '<span class="input-group-text">'.htmlspecialchars($suffix).'</span>';
        }

        $html .= '</div>';

        // Show formula in builder
        if ($formula) {
            $html .= '<div class="form-text small">';
            $html .= '<i class="bi bi-calculator me-1"></i>';
            $html .= 'Formula: '.htmlspecialchars($formula);
            $html .= '</div>';
        } else {
            $html .= '<div class="form-text text-warning small">';
            $html .= '<i class="bi bi-exclamation-triangle me-1"></i>';
            $html .= 'No formula set - configure in properties panel';
            $html .= '</div>';
        }

        $html .= $this->renderInvalidFeedback($field);
        $html .= $this->renderValidFeedback($field);
        $html .= $this->renderHelpText($field);

        $html .= '</div>';

        return $html;
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        // Calculation fields are read-only and automatically calculated
        // No user input validation needed since the value is system-generated
        return [];
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'formula' => [
                'type' => 'textarea',
                'label' => 'Formula',
                'tab' => 'options',
                'target' => 'options',
                'required' => true,
                'help' => 'Use {field_name} to reference other fields. Example: {price} * {quantity}',
                'placeholder' => '{price} * {quantity}',
            ],
            'display_as' => [
                'type' => 'select',
                'label' => 'Display Format',
                'tab' => 'options',
                'target' => 'options',
                'options' => [
                    'number' => 'Number',
                    'currency' => 'Currency',
                    'percentage' => 'Percentage',
                ],
                'default' => 'number',
                'required' => false,
            ],
            'decimal_places' => [
                'type' => 'number',
                'label' => 'Decimal Places',
                'tab' => 'options',
                'target' => 'options',
                'default' => 2,
                'required' => false,
                'help' => 'Number of decimal places to display',
            ],
            'prefix' => [
                'type' => 'text',
                'label' => 'Prefix',
                'tab' => 'options',
                'target' => 'options',
                'placeholder' => '$',
                'required' => false,
                'help' => 'Text to show before the value (e.g., $)',
            ],
            'suffix' => [
                'type' => 'text',
                'label' => 'Suffix',
                'tab' => 'options',
                'target' => 'options',
                'placeholder' => '%',
                'required' => false,
                'help' => 'Text to show after the value (e.g., %)',
            ],
            'display_mode' => [
                'type' => 'select',
                'label' => 'Display Mode',
                'tab' => 'options',
                'target' => 'options',
                'options' => [
                    'visible' => 'Visible',
                    'hidden' => 'Hidden',
                ],
                'default' => 'visible',
                'required' => false,
                'help' => 'Hidden fields will store the value but not display it',
            ],
        ]);
    }

    public function getAvailableValidationOptions(): array
    {
        // Calculation fields are read-only, no validation options needed
        return [];
    }

    /**
     * Extract field names referenced in the formula
     */
    public function extractDependencies(string $formula): array
    {
        preg_match_all('/\{([a-z0-9_]+)\}/i', $formula, $matches);

        return $matches[1] ?? [];
    }
}
