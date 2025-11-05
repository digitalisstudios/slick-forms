<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Services\InputMaskHelper;

class NumberField extends BaseFieldType
{
    public function getName(): string
    {
        return 'number';
    }

    public function getLabel(): string
    {
        return 'Number';
    }

    public function getIcon(): string
    {
        return 'bi bi-123';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $attributes = $this->getCommonAttributes($field);

        if (InputMaskHelper::hasMask($field)) {
            $attributes['type'] = 'text';
            $attributes['wire:model.lazy'] = 'formData.field_'.$field->id;

            // Add placeholder if mask type has one
            $maskType = $field->options['mask']['type'] ?? 'none';
            if (! $field->placeholder && $maskType !== 'none') {
                $attributes['placeholder'] = InputMaskHelper::getMaskPlaceholder($maskType);
            }
        } else {
            $attributes['type'] = 'number';
            $attributes[$this->getWireModelAttribute($field)] = 'formData.field_'.$field->id;
            $attributes['@change'] = '$wire.refreshVisibility()';

            // Add min/max/step attributes from options
            if (isset($field->options['min']) && $field->options['min'] !== '') {
                $attributes['min'] = $field->options['min'];
            }
            if (isset($field->options['max']) && $field->options['max'] !== '') {
                $attributes['max'] = $field->options['max'];
            }
            if (isset($field->options['step']) && $field->options['step'] !== '') {
                $attributes['step'] = $field->options['step'];
            }
        }

        $attributes['value'] = $value ?? '';

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
        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';
        $html .= $this->renderLabel($field, $elementId ?? '');
        $html .= '<input type="number" class="form-control" placeholder="'.htmlspecialchars($field->placeholder ?? '').'" disabled>';
        $html .= $this->renderHelpText($field);

        $html .= '</div>';

        return $html;
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        $rules = parent::validate($field, $value);
        $rules[] = 'numeric';

        return $rules;
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'min' => [
                'type' => 'number',
                'label' => 'Minimum Value',
                'tab' => 'options',
                'target' => 'options',
                'required' => false,
                'help' => 'Minimum value allowed (HTML5 validation)',
            ],
            'max' => [
                'type' => 'number',
                'label' => 'Maximum Value',
                'tab' => 'options',
                'target' => 'options',
                'required' => false,
                'help' => 'Maximum value allowed (HTML5 validation)',
            ],
            'step' => [
                'type' => 'number',
                'label' => 'Step',
                'tab' => 'options',
                'target' => 'options',
                'default' => 1,
                'required' => false,
                'help' => 'Increment step (e.g., 0.01 for decimals, 1 for integers)',
            ],
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
                'help' => '# = number, A = letter, * = alphanumeric',
                'placeholder' => '###.##',
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
                'label' => 'Minimum Value',
                'help' => 'Minimum numeric value allowed',
                'rule_format' => 'min:{value}',
            ],
            'max' => [
                'type' => 'number',
                'label' => 'Maximum Value',
                'help' => 'Maximum numeric value allowed',
                'rule_format' => 'max:{value}',
            ],
            'integer' => [
                'type' => 'checkbox',
                'label' => 'Must be Integer (No Decimals)',
                'rule_format' => 'integer',
            ],
            'decimal' => [
                'type' => 'text',
                'label' => 'Decimal Places',
                'help' => 'Format: min,max (e.g., 0,2 for up to 2 decimals)',
                'rule_format' => 'decimal:{value}',
                'placeholder' => '0,2',
            ],
        ];
    }
}
