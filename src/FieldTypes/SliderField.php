<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;

class SliderField extends BaseFieldType
{
    public function getName(): string
    {
        return 'slider';
    }

    public function getLabel(): string
    {
        return 'Slider';
    }

    public function getIcon(): string
    {
        return 'bi bi-sliders';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $min = $field->options['min'] ?? 0;
        $max = $field->options['max'] ?? 100;
        $step = $field->options['step'] ?? 1;
        $showValue = $field->options['show_value'] ?? true;
        $currentValue = $value ?? $min;

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';
        // Note: Slider field uses form-range, floating labels won't work
        $fieldId = 'field_'.$field->id;
        // Explicitly include required asterisk as CSS cannot target nested structure reliably here
        $html .= $this->renderLabel($field, $fieldId, true);

        $html .= '<div x-data="{ value: '.$currentValue.' }">';

        if ($showValue) {
            $html .= '<div class="d-flex justify-content-between align-items-center mb-2">';
            $html .= '<span class="text-muted small">Min: '.$min.'</span>';
            $html .= '<span class="badge bg-primary" x-text="value"></span>';
            $html .= '<span class="text-muted small">Max: '.$max.'</span>';
            $html .= '</div>';
        }

        $html .= '<input type="range" ';
        $html .= 'class="form-range" ';
        $html .= 'min="'.$min.'" ';
        $html .= 'max="'.$max.'" ';
        $html .= 'step="'.$step.'" ';
        $html .= 'x-model="value" ';
        $html .= '@input="$wire.set(\'formData.field_'.$field->id.'\', value)" @change="$wire.refreshVisibility()" ';
        // Do not add HTML required attribute to avoid duplicate asterisks from external CSS
        $html .= '>';

        // Hidden input for Livewire
        $html .= '<input type="hidden" wire:model="formData.field_'.$field->id.'">';

        $html .= '</div>';
        $html .= $this->renderInvalidFeedback($field);
        $html .= $this->renderValidFeedback($field);
        $html .= $this->renderHelpText($field);

        $html .= '</div>';

        return $html;
    }

    public function getDefaultValue(CustomFormField $field): mixed
    {
        // Default slider value should be its configured minimum (commonly 0)
        return $field->options['min'] ?? 0;
    }

    public function renderBuilder(CustomFormField $field): string
    {
        $min = $field->options['min'] ?? 0;
        $max = $field->options['max'] ?? 100;
        $step = $field->options['step'] ?? 1;
        $showValue = $field->options['show_value'] ?? true;

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';
        $html .= $this->renderLabel($field, $elementId ?? '');

        if ($showValue) {
            $html .= '<div class="d-flex justify-content-between align-items-center mb-2">';
            $html .= '<span class="text-muted small">Min: '.$min.'</span>';
            $html .= '<span class="badge bg-primary">'.$min.'</span>';
            $html .= '<span class="text-muted small">Max: '.$max.'</span>';
            $html .= '</div>';
        }

        $html .= '<input type="range" class="form-range" min="'.$min.'" max="'.$max.'" step="'.$step.'" disabled>';
        $html .= $this->renderInvalidFeedback($field);
        $html .= $this->renderValidFeedback($field);
        $html .= $this->renderHelpText($field);

        $html .= '</div>';

        return $html;
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        $rules = parent::validate($field, $value);
        $rules[] = 'numeric';

        $min = $field->options['min'] ?? 0;
        $max = $field->options['max'] ?? 100;

        $rules[] = 'min:'.$min;
        $rules[] = 'max:'.$max;

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
                'default' => '0',
                'required' => false,
                'help' => 'Minimum slider value',
            ],
            'max' => [
                'type' => 'number',
                'label' => 'Maximum Value',
                'tab' => 'options',
                'target' => 'options',
                'default' => '100',
                'required' => false,
                'help' => 'Maximum slider value',
            ],
            'step' => [
                'type' => 'number',
                'label' => 'Step',
                'tab' => 'options',
                'target' => 'options',
                'default' => '1',
                'required' => false,
                'help' => 'Slider increment/decrement step',
            ],
            'show_value' => [
                'type' => 'switch',
                'label' => 'Show Current Value',
                'tab' => 'options',
                'target' => 'options',
                'default' => true,
                'required' => false,
                'help' => 'Display the current slider value',
            ],
        ]);
    }

    public function getAvailableValidationOptions(): array
    {
        return [
            'between' => [
                'type' => 'text',
                'label' => 'Must Be Between',
                'help' => 'Format: min,max (e.g., 10,90)',
                'rule_format' => 'between:{value}',
                'placeholder' => '10,90',
            ],
        ];
    }
}
