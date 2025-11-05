<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;

class HiddenField extends BaseFieldType
{
    public function getName(): string
    {
        return 'hidden';
    }

    public function getLabel(): string
    {
        return 'Hidden';
    }

    public function getIcon(): string
    {
        return 'bi bi-eye-slash';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $defaultValue = $field->options['default_value'] ?? '';
        $actualValue = $value ?? $defaultValue;

        $html = '<input type="hidden" ';
        $html .= 'id="field_'.$field->id.'" ';
        $html .= 'name="field_'.$field->id.'" ';
        $html .= 'value="'.htmlspecialchars($actualValue).'" ';
        $html .= 'wire:model="formData.field_'.$field->id.'">';

        return $html;
    }

    public function renderBuilder(CustomFormField $field): string
    {
        $defaultValue = $field->options['default_value'] ?? '';

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';
        $html .= '<div class="alert alert-secondary d-flex align-items-center">';
        $html .= '<i class="bi bi-eye-slash me-2"></i>';
        $html .= '<div>';
        $html .= '<strong>'.htmlspecialchars($field->label).'</strong> (Hidden Field)';
        if ($defaultValue) {
            $html .= '<div class="small mt-1">Default value: <code>'.htmlspecialchars($defaultValue).'</code></div>';
        }
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        // Hidden fields typically don't require validation, but we'll include the parent rules
        return parent::validate($field, $value);
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'default_value' => [
                'type' => 'text',
                'label' => 'Default Value',
                'tab' => 'options',
                'target' => 'options',
                'required' => false,
                'help' => 'Pre-filled value for this hidden field',
            ],
        ]);
    }

    public function getAvailableValidationOptions(): array
    {
        return [
            'in' => [
                'type' => 'text',
                'label' => 'Allowed Values',
                'help' => 'Comma-separated list of allowed values',
                'rule_format' => 'in:{value}',
                'placeholder' => 'value1,value2,value3',
            ],
            'regex' => [
                'type' => 'text',
                'label' => 'Pattern (Regex)',
                'help' => 'Regular expression pattern to match',
                'rule_format' => 'regex:{value}',
                'placeholder' => '/^[A-Z0-9]+$/',
            ],
        ];
    }
}
