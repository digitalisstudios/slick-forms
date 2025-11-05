<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;

class RangeField extends BaseFieldType
{
    public function getName(): string
    {
        return 'range';
    }

    public function getLabel(): string
    {
        return 'Range (Min-Max)';
    }

    public function getIcon(): string
    {
        return 'bi bi-arrows-expand';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $min = $field->options['min'] ?? 0;
        $max = $field->options['max'] ?? 100;
        $step = $field->options['step'] ?? 1;
        $fieldId = 'field_'.$field->id;
        $sliderId = 'range_slider_'.$field->id;

        // Parse existing value
        $valueArray = is_array($value) ? $value : ($value ? json_decode($value, true) : ['min' => $min, 'max' => $max]);
        $minValue = $valueArray['min'] ?? $min;
        $maxValue = $valueArray['max'] ?? $max;

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';

        // Add scoped styles for this slider
        $html .= '<style>
            #'.$sliderId.' input[type=range]::-webkit-slider-thumb {
                -webkit-appearance: none;
                pointer-events: all;
                width: 20px;
                height: 20px;
                background: #0d6efd;
                border: 2px solid white;
                border-radius: 50%;
                box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.2);
                cursor: pointer;
            }

            #'.$sliderId.' input[type=range]::-moz-range-thumb {
                pointer-events: all;
                width: 20px;
                height: 20px;
                background: #0d6efd;
                border: 2px solid white;
                border-radius: 50%;
                box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.2);
                cursor: pointer;
            }
        </style>';

        // Label
        $html .= $this->renderLabelWithFloating($field, $fieldId);

        // Custom dual-handle slider with Alpine.js
        $html .= '<div x-data="{
            minVal: '.$minValue.',
            maxVal: '.$maxValue.',
            min: '.$min.',
            max: '.$max.',
            step: '.$step.',

            init() {
                this.updateWire();
            },

            updateMin() {
                if (parseFloat(this.minVal) > parseFloat(this.maxVal)) {
                    this.minVal = this.maxVal;
                }
                this.updateWire();
            },

            updateMax() {
                if (parseFloat(this.maxVal) < parseFloat(this.minVal)) {
                    this.maxVal = this.minVal;
                }
                this.updateWire();
            },

            updateWire() {
                $wire.set(\'formData.field_'.$field->id.'\', { min: parseFloat(this.minVal), max: parseFloat(this.maxVal) });
                $wire.refreshVisibility();
            }
        }">';

        // Value display
        $html .= '<div class="d-flex justify-content-between align-items-center mb-3">';
        $html .= '<div class="badge bg-primary px-3 py-2">';
        $html .= '<small>Min:</small> <strong x-text="minVal"></strong>';
        $html .= '</div>';
        $html .= '<div class="badge bg-primary px-3 py-2">';
        $html .= '<small>Max:</small> <strong x-text="maxVal"></strong>';
        $html .= '</div>';
        $html .= '</div>';

        // Slider inputs wrapper
        $html .= '<div id="'.$sliderId.'" class="position-relative" style="height: 40px;">';

        // Min range input
        $html .= '<input type="range" ';
        $html .= 'class="form-range position-absolute w-100" ';
        $html .= 'style="pointer-events: none; -webkit-appearance: none; appearance: none; background: transparent; height: 40px; z-index: 2;" ';
        $html .= 'min="'.$min.'" max="'.$max.'" step="'.$step.'" ';
        $html .= 'x-model.number="minVal" ';
        $html .= '@input="updateMin()">';

        // Max range input
        $html .= '<input type="range" ';
        $html .= 'class="form-range position-absolute w-100" ';
        $html .= 'style="pointer-events: none; -webkit-appearance: none; appearance: none; background: transparent; height: 40px; z-index: 2;" ';
        $html .= 'min="'.$min.'" max="'.$max.'" step="'.$step.'" ';
        $html .= 'x-model.number="maxVal" ';
        $html .= '@input="updateMax()">';

        // Track (non-interactive visual)
        $html .= '<div class="position-absolute w-100" style="top: 50%; transform: translateY(-50%); height: 6px; background: #dee2e6; border-radius: 3px; z-index: 1;"></div>';

        // Active range highlight (non-interactive visual)
        $html .= '<div class="position-absolute" ';
        $html .= 'style="top: 50%; transform: translateY(-50%); height: 6px; background: #0d6efd; border-radius: 3px; z-index: 1;" ';
        $html .= 'x-bind:style="{
            left: ((minVal - min) / (max - min)) * 100 + \'%\',
            right: (1 - (maxVal - min) / (max - min)) * 100 + \'%\'
        }"></div>';

        $html .= '</div>';

        $html .= '</div>';

        // Range labels
        $html .= '<div class="d-flex justify-content-between mt-2">';
        $html .= '<small class="text-muted">'.$min.'</small>';
        $html .= '<small class="text-muted">'.$max.'</small>';
        $html .= '</div>';

        // Hidden input for Livewire
        $html .= '<input type="hidden" wire:model="formData.field_'.$field->id.'">';

        $html .= $this->renderInvalidFeedback($field);
        $html .= $this->renderValidFeedback($field);
        $html .= $this->renderHelpText($field);

        $html .= '</div>';

        return $html;
    }

    public function renderBuilder(CustomFormField $field): string
    {
        $min = $field->options['min'] ?? 0;
        $max = $field->options['max'] ?? 100;
        $midPoint = ($min + $max) / 2;
        $minValue = $min + (($max - $min) * 0.25); // 25% from min
        $maxValue = $min + (($max - $min) * 0.75); // 75% from max

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';

        $html .= $this->renderLabel($field, $elementId ?? '');

        // Value display
        $html .= '<div class="d-flex justify-content-between align-items-center mb-3">';
        $html .= '<div class="badge bg-secondary px-3 py-2">';
        $html .= '<small>Min:</small> <strong>'.$minValue.'</strong>';
        $html .= '</div>';
        $html .= '<div class="badge bg-secondary px-3 py-2">';
        $html .= '<small>Max:</small> <strong>'.$maxValue.'</strong>';
        $html .= '</div>';
        $html .= '</div>';

        // Dual slider preview
        $html .= '<div class="position-relative" style="height: 50px; padding-top: 15px;">';

        // Track background
        $html .= '<div class="position-absolute w-100 rounded" style="height: 6px; background: #dee2e6; top: 50%; transform: translateY(-50%);"></div>';

        // Active range highlight (25% to 75%)
        $html .= '<div class="position-absolute rounded" style="height: 6px; background: #6c757d; top: 50%; transform: translateY(-50%); left: 25%; right: 25%;"></div>';

        // Min thumb (at 25%)
        $html .= '<div class="position-absolute rounded-circle bg-secondary border border-2 border-white shadow" style="width: 20px; height: 20px; top: 50%; left: 25%; transform: translate(-50%, -50%);"></div>';

        // Max thumb (at 75%)
        $html .= '<div class="position-absolute rounded-circle bg-secondary border border-2 border-white shadow" style="width: 20px; height: 20px; top: 50%; left: 75%; transform: translate(-50%, -50%);"></div>';

        $html .= '</div>';

        // Range labels
        $html .= '<div class="d-flex justify-content-between mt-2">';
        $html .= '<small class="text-muted">'.$min.'</small>';
        $html .= '<small class="text-muted">'.$max.'</small>';
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
        $rules[] = 'array';
        $rules[] = 'required_array_keys:min,max';

        return $rules;
    }

    public function processValue(mixed $value): mixed
    {
        return is_array($value) ? json_encode($value) : $value;
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
                'help' => 'Minimum value for the range',
            ],
            'max' => [
                'type' => 'number',
                'label' => 'Maximum Value',
                'tab' => 'options',
                'target' => 'options',
                'default' => '100',
                'required' => false,
                'help' => 'Maximum value for the range',
            ],
            'step' => [
                'type' => 'number',
                'label' => 'Step',
                'tab' => 'options',
                'target' => 'options',
                'default' => '1',
                'required' => false,
                'help' => 'Increment/decrement step for both sliders',
            ],
        ]);
    }

    public function getAvailableValidationOptions(): array
    {
        return [];
    }
}
