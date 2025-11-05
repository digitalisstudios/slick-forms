<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;

class TimeField extends BaseFieldType
{
    public function getName(): string
    {
        return 'time';
    }

    public function getLabel(): string
    {
        return 'Time';
    }

    public function getIcon(): string
    {
        return 'bi bi-clock';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $enable24Hour = $field->options['enable_24_hour'] ?? false;
        $showSeconds = $field->options['show_seconds'] ?? false;
        $fieldId = 'field_'.$field->id;

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';

        // Support floating labels by avoiding input-group wrappers
        $html .= $this->renderLabelWithFloating($field, $fieldId);
        $input = '<input type="time" class="form-control" '
               .'id="'.$fieldId.'" '
               .$this->getWireModelAttribute($field).'=\'formData.field_'.$field->id.'\' '
               .$this->getValidationAttributes($field).' @change=\'$wire.refreshVisibility()\' '
               .($showSeconds ? 'step=\'1\' ' : '')
               .($field->is_required ? 'required ' : '')
               .'value=\''.htmlspecialchars($value ?? '').'\'>';
        $html .= $this->wrapFloatingLabel($field, $input);

        if (! $enable24Hour) {
            $html .= '<div class="form-text small"><i class="bi bi-info-circle me-1"></i>12-hour format (browser default)</div>';
        }

        $html .= $this->renderInvalidFeedback($field);
        $html .= $this->renderValidFeedback($field);
        $html .= $this->renderHelpText($field);
        $html .= '</div>';

        return $html;
    }

    public function renderBuilder(CustomFormField $field): string
    {
        $enable24Hour = $field->options['enable_24_hour'] ?? false;
        $showSeconds = $field->options['show_seconds'] ?? false;

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';

        $html .= $this->renderLabel($field, $elementId ?? '');
        $html .= '<input type="time" class="form-control" disabled>';

        $features = [];
        if ($enable24Hour) {
            $features[] = '24-hour format';
        }
        if ($showSeconds) {
            $features[] = 'Seconds enabled';
        }

        if (! empty($features)) {
            $html .= '<div class="form-text small"><i class="bi bi-info-circle me-1"></i>'.implode(' | ', $features).'</div>';
        }

        $html .= $this->renderHelpText($field);
        $html .= '</div>';

        return $html;
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        $rules = parent::validate($field, $value);
        $rules[] = 'date_format:H:i'.($field->options['show_seconds'] ?? false ? ':s' : '');

        return $rules;
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'enable_24_hour' => [
                'type' => 'switch',
                'label' => '24-Hour Format',
                'tab' => 'options',
                'target' => 'options',
                'default' => false,
                'required' => false,
                'help' => 'Use 24-hour time format (13:00) instead of 12-hour (1:00 PM)',
            ],
            'show_seconds' => [
                'type' => 'switch',
                'label' => 'Show Seconds',
                'tab' => 'options',
                'target' => 'options',
                'default' => false,
                'required' => false,
                'help' => 'Allow selection of seconds in addition to hours and minutes',
            ],
        ]);
    }

    public function getAvailableValidationOptions(): array
    {
        return [
            'after' => [
                'type' => 'text',
                'label' => 'Must Be After',
                'help' => 'Time must be after this time (HH:MM format)',
                'rule_format' => 'after:{value}',
                'placeholder' => '09:00',
            ],
            'before' => [
                'type' => 'text',
                'label' => 'Must Be Before',
                'help' => 'Time must be before this time (HH:MM format)',
                'rule_format' => 'before:{value}',
                'placeholder' => '17:00',
            ],
        ];
    }
}
