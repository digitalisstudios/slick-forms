<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;

class SwitchField extends BaseFieldType
{
    public function getName(): string
    {
        return 'switch';
    }

    public function getLabel(): string
    {
        return 'Switch/Toggle';
    }

    public function getIcon(): string
    {
        return 'bi bi-toggle-on';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $fieldId = 'field_'.$field->id;
        $checked = $value ? 'checked' : '';
        $onLabel = $field->options['on_label'] ?? 'On';
        $offLabel = $field->options['off_label'] ?? 'Off';
        $showLabels = $field->options['show_labels'] ?? true;

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';

        $html .= '<div class="form-check form-switch">';
        $html .= '<input class="form-check-input" type="checkbox" role="switch" ';
        $html .= 'id="'.$fieldId.'" ';
        $html .= $this->getWireModelAttribute($field).'="formData.field_'.$field->id.'" ';
        $html .= $this->getValidationAttributes($field).' @change="$wire.refreshVisibility()" ';
        if ($field->is_required) {
            $html .= 'required ';
        }
        $html .= $checked.'>';

        $html .= '<label class="form-check-label" for="'.$fieldId.'">';
        $html .= htmlspecialchars($field->label);
        if ($field->is_required) {
            $html .= ' <span class="text-danger">*</span>';
        }
        $html .= '</label>';
        $html .= '</div>';

        if ($showLabels) {
            $html .= '<div class="form-text small" x-data="{ checked: '.($value ? 'true' : 'false').' }" @change.window="if ($event.target.id === \''.$fieldId.'\') checked = $event.target.checked">';
            $html .= '<span x-show="checked" class="text-success"><i class="bi bi-check-circle me-1"></i>'.htmlspecialchars($onLabel).'</span>';
            $html .= '<span x-show="!checked" class="text-muted"><i class="bi bi-circle me-1"></i>'.htmlspecialchars($offLabel).'</span>';
            $html .= '</div>';
        }

        $html .= $this->renderInvalidFeedback($field);
        $html .= $this->renderValidFeedback($field);
        $html .= $this->renderHelpText($field);
        $html .= '</div>';

        return $html;
    }

    public function renderBuilder(CustomFormField $field): string
    {
        $showLabels = $field->options['show_labels'] ?? true;
        $onLabel = $field->options['on_label'] ?? 'On';

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';

        $html .= '<div class="form-check form-switch">';
        $html .= '<input class="form-check-input" type="checkbox" role="switch" disabled>';
        $html .= '<label class="form-check-label">'.htmlspecialchars($field->label).'</label>';
        $html .= '</div>';

        if ($showLabels) {
            $html .= '<div class="form-text small text-muted"><i class="bi bi-info-circle me-1"></i>Shows "'.htmlspecialchars($onLabel).'" when enabled</div>';
        }

        $html .= $this->renderHelpText($field);
        $html .= '</div>';

        return $html;
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        $rules = parent::validate($field, $value);

        // For switches, "required" means "must be checked" (accepted)
        // Remove 'required' and add 'accepted' instead
        $rules = array_filter($rules, fn ($rule) => $rule !== 'required');

        if ($field->is_required) {
            $rules[] = 'accepted';
        } else {
            $rules[] = 'boolean';
        }

        return $rules;
    }

    public function processValue(mixed $value): mixed
    {
        return $value ? 1 : 0;
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'show_labels' => [
                'type' => 'switch',
                'label' => 'Show On/Off Labels',
                'tab' => 'options',
                'target' => 'options',
                'default' => true,
                'required' => false,
                'help' => 'Display text labels next to the switch',
            ],
            'on_label' => [
                'type' => 'text',
                'label' => 'On Label',
                'tab' => 'options',
                'target' => 'options',
                'default' => 'On',
                'required' => false,
                'help' => 'Text shown when switch is enabled',
            ],
            'off_label' => [
                'type' => 'text',
                'label' => 'Off Label',
                'tab' => 'options',
                'target' => 'options',
                'default' => 'Off',
                'required' => false,
                'help' => 'Text shown when switch is disabled',
            ],
        ]);
    }

    public function getAvailableValidationOptions(): array
    {
        // No additional validation options needed
        // "Required Field" checkbox now means "must be checked" for switches
        return [];
    }
}
