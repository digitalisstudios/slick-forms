<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;

class ColorPickerField extends BaseFieldType
{
    public function getName(): string
    {
        return 'color_picker';
    }

    public function getLabel(): string
    {
        return 'Color Picker';
    }

    public function getIcon(): string
    {
        return 'bi bi-palette';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $defaultColor = $field->options['default_color'] ?? '#000000';
        $showHex = $field->options['show_hex'] ?? true;
        $currentValue = $value ?? $defaultColor;

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';
        $fieldId = 'field_'.$field->id;
        $html .= $this->renderLabelWithFloating($field, $fieldId);

        $html .= '<div x-data="{ color: \''.htmlspecialchars($currentValue).'\' }">';

        $html .= '<div class="d-flex gap-2 align-items-center">';

        // Color input
        $html .= '<input type="color" ';
        $html .= 'class="form-control form-control-color" ';
        $html .= 'x-model="color" ';
        $html .= '@input="$wire.set(\'formData.field_'.$field->id.'\', color)" ';
        $html .= 'style="width: 60px; height: 40px;" ';
        if ($field->is_required) {
            $html .= 'required ';
        }
        $html .= '>';

        if ($showHex) {
            // Text input for hex value
            $html .= '<input type="text" ';
            $html .= 'class="form-control" ';
            $html .= 'x-model="color" ';
            $html .= '@input="$wire.set(\'formData.field_'.$field->id.'\', color)" ';
            $html .= 'placeholder="#000000" ';
            $html .= 'pattern="^#[0-9A-Fa-f]{6}$" ';
            $html .= 'style="max-width: 120px;"';
            $html .= '>';

            // Color preview
            $html .= '<div class="border rounded p-3" :style="{backgroundColor: color}" style="min-width: 60px; min-height: 40px;"></div>';
        }

        $html .= '</div>';

        // Hidden input for Livewire
        $html .= '<input type="hidden" wire:model="formData.field_'.$field->id.'">';

        $html .= '</div>';
        $html .= $this->renderInvalidFeedback($field);
        $html .= $this->renderValidFeedback($field);
        $html .= $this->renderHelpText($field);

        $html .= '</div>';

        return $html;
    }

    public function renderBuilder(CustomFormField $field): string
    {
        $defaultColor = $field->options['default_color'] ?? '#000000';
        $showHex = $field->options['show_hex'] ?? true;

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';
        $html .= $this->renderLabel($field, $elementId ?? '');

        $html .= '<div class="d-flex gap-2 align-items-center">';
        $html .= '<input type="color" class="form-control form-control-color" value="'.htmlspecialchars($defaultColor).'" style="width: 60px; height: 40px;" disabled>';

        if ($showHex) {
            $html .= '<input type="text" class="form-control" value="'.htmlspecialchars($defaultColor).'" style="max-width: 120px;" disabled>';
            $html .= '<div class="border rounded p-3" style="background-color: '.htmlspecialchars($defaultColor).'; min-width: 60px; min-height: 40px;"></div>';
        }

        $html .= '</div>';
        $html .= $this->renderInvalidFeedback($field);
        $html .= $this->renderValidFeedback($field);
        $html .= $this->renderHelpText($field);

        $html .= '</div>';

        return $html;
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        $rules = parent::validate($field, $value);
        $rules[] = 'string';
        $rules[] = 'regex:/^#[0-9A-Fa-f]{6}$/';

        return $rules;
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'default_color' => [
                'type' => 'color',
                'label' => 'Default Color',
                'tab' => 'options',
                'target' => 'options',
                'default' => '#000000',
                'required' => false,
                'help' => 'Pre-selected color value',
            ],
            'show_hex' => [
                'type' => 'switch',
                'label' => 'Show Hex Input',
                'tab' => 'options',
                'target' => 'options',
                'default' => true,
                'required' => false,
                'help' => 'Display text input for hex color codes',
            ],
        ]);
    }

    public function getAvailableValidationOptions(): array
    {
        // Color picker already has strict hex validation built-in
        // Additional options could be added here if needed
        return [];
    }
}
