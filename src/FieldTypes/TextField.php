<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Services\InputMaskHelper;

class TextField extends BaseFieldType
{
    public function getName(): string
    {
        return 'text';
    }

    public function getLabel(): string
    {
        return 'Text Input';
    }

    public function getIcon(): string
    {
        return 'bi bi-input-cursor-text';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $attributes = $this->getCommonAttributes($field);
        $attributes['type'] = 'text';
        $attributes['value'] = $value ?? '';

        // Use lazy for input masks, otherwise respect validation timing
        if (InputMaskHelper::hasMask($field)) {
            $attributes['wire:model.lazy'] = 'formData.field_'.$field->id;
            $attributes['@change'] = '$wire.refreshVisibility()';

            // Add placeholder if mask type has one
            $maskType = $field->options['mask']['type'] ?? 'none';
            if (! $field->placeholder && $maskType !== 'none') {
                $attributes['placeholder'] = InputMaskHelper::getMaskPlaceholder($maskType);
            }
        } else {
            $attributes[$this->getWireModelAttribute($field)] = 'formData.field_'.$field->id;
            $attributes['@change'] = '$wire.refreshVisibility()';
        }

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';

        $html .= $this->renderLabelWithFloating($field, $attributes['id']);

        $inputHtml = '<input '.$this->renderAttributes($attributes).$this->getValidationAttributes($field).'>';
        $html .= $this->wrapFloatingLabel($field, $inputHtml);

        $html .= $this->renderInvalidFeedback($field);
        $html .= $this->renderValidFeedback($field);
        $html .= $this->renderHelpText($field);

        $html .= InputMaskHelper::renderMaskScript($attributes['id'], $field);

        $html .= '</div>';

        return $html;
    }

    public function renderBuilder(CustomFormField $field): string
    {
        $elementId = $field->element_id ?? 'field_'.$field->id;

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';
        $html .= $this->renderLabel($field, $elementId);
        $html .= '<input type="text" class="form-control" placeholder="'.htmlspecialchars($field->placeholder ?? '').'" disabled>';
        $html .= $this->renderHelpText($field);
        $html .= '</div>';

        return $html;
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        $rules = parent::validate($field, $value);
        $rules[] = 'string';
        $rules[] = 'max:255';

        return $rules;
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'mask_enabled' => [
                'type' => 'switch',
                'label' => 'Enable Input Mask',
                'tab' => 'options',
                'target' => 'options.mask.enabled',
                'default' => false,
                'required' => false,
                'help' => 'Auto-format input as users type',
            ],
            'mask_type' => [
                'type' => 'select',
                'label' => 'Mask Type',
                'tab' => 'options',
                'target' => 'options.mask.type',
                'options' => [
                    'none' => 'None',
                    'phone_us' => 'Phone (US)',
                    'phone_intl' => 'Phone (International)',
                    'creditCard' => 'Credit Card',
                    'date_us' => 'Date (MM/DD/YYYY)',
                    'date_eu' => 'Date (DD/MM/YYYY)',
                    'date_iso' => 'Date (YYYY-MM-DD)',
                    'time_12' => 'Time (12-hour)',
                    'time_24' => 'Time (24-hour)',
                    'ssn' => 'SSN',
                    'zip' => 'ZIP Code',
                    'zip_plus4' => 'ZIP+4',
                    'number_decimal' => 'Number (Decimal)',
                    'number_integer' => 'Number (Integer)',
                    'currency_usd' => 'Currency (USD)',
                    'percentage' => 'Percentage',
                    'custom' => 'Custom Pattern',
                ],
                'default' => 'none',
                'required' => false,
                'help' => 'Select a preset mask or create a custom pattern',
                'show_if' => [
                    'field' => 'mask_enabled',
                    'operator' => '==',
                    'value' => true,
                ],
            ],
            'mask_pattern' => [
                'type' => 'text',
                'label' => 'Custom Pattern',
                'tab' => 'options',
                'target' => 'options.mask.pattern',
                'default' => '',
                'required' => false,
                'help' => '# = number, A = letter, * = alphanumeric. Example: ###-##-#### for SSN',
                'placeholder' => '###-##-####',
                'show_if' => [
                    ['field' => 'mask_enabled', 'operator' => '==', 'value' => true],
                    ['field' => 'mask_type', 'operator' => '==', 'value' => 'custom'],
                ],
            ],
        ]);
    }

    public function getAvailableValidationOptions(): array
    {
        return [
            'min' => [
                'type' => 'number',
                'label' => 'Minimum Length',
                'help' => 'Minimum number of characters required',
                'rule_format' => 'min:{value}',
            ],
            'max' => [
                'type' => 'number',
                'label' => 'Maximum Length',
                'help' => 'Maximum number of characters allowed',
                'rule_format' => 'max:{value}',
            ],
            'regex' => [
                'type' => 'text',
                'label' => 'Pattern (Regex)',
                'help' => 'Regular expression pattern to match',
                'rule_format' => 'regex:{value}',
                'placeholder' => '/^[A-Z][a-z]+$/',
            ],
            'alpha' => [
                'type' => 'checkbox',
                'label' => 'Alphabetic Characters Only',
                'rule_format' => 'alpha',
            ],
            'alpha_num' => [
                'type' => 'checkbox',
                'label' => 'Alphanumeric Characters Only',
                'rule_format' => 'alpha_num',
            ],
            'alpha_dash' => [
                'type' => 'checkbox',
                'label' => 'Allow Letters, Numbers, Dashes, Underscores',
                'rule_format' => 'alpha_dash',
            ],
        ];
    }
}
