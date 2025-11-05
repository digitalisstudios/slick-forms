<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;

class DateRangeField extends BaseFieldType
{
    public function getName(): string
    {
        return 'date_range';
    }

    public function getLabel(): string
    {
        return 'Date Range';
    }

    public function getIcon(): string
    {
        return 'bi bi-calendar-range';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $valueArray = is_array($value) ? $value : ($value ? json_decode($value, true) : ['start' => '', 'end' => '']);
        $startDate = $valueArray['start'] ?? '';
        $endDate = $valueArray['end'] ?? '';

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';
        $useFloating = $this->useFloatingLabel($field);
        $fieldId = 'field_'.$field->id;
        if (! $useFloating) {
            $html .= $this->renderLabel($field, $fieldId);
        }

        $html .= '<div x-data="{
            startDate: \''.htmlspecialchars($startDate).'\',
            endDate: \''.htmlspecialchars($endDate).'\',
            updateDates() {
                const data = { start: this.startDate, end: this.endDate };
                $wire.set(\'formData.field_'.$field->id.'\', data);
                $wire.refreshVisibility();
            }
        }">';

        $html .= '<div class="row g-2">';

        // Start Date
        $html .= '<div class="col-md-6">';
        if ($useFloating) {
            $html .= '<div class="form-floating">';
            $html .= '<input type="date" class="form-control" placeholder="Start Date" x-model="startDate" @change="updateDates()" ';
            if ($field->is_required) {
                $html .= 'required ';
            }
            $html .= '>';
            $html .= '<label>Start Date</label>';
            $html .= '</div>';
        } else {
            $html .= '<label class="form-label small">Start Date</label>';
            $html .= '<input type="date" class="form-control" x-model="startDate" @change="updateDates()" ';
            if ($field->is_required) {
                $html .= 'required ';
            }
            $html .= '>';
        }
        $html .= '</div>';

        // End Date
        $html .= '<div class="col-md-6">';
        if ($useFloating) {
            $html .= '<div class="form-floating">';
            $html .= '<input type="date" class="form-control" placeholder="End Date" x-model="endDate" @change="updateDates()" :min="startDate" ';
            if ($field->is_required) {
                $html .= 'required ';
            }
            $html .= '>';
            $html .= '<label>End Date</label>';
            $html .= '</div>';
        } else {
            $html .= '<label class="form-label small">End Date</label>';
            $html .= '<input type="date" class="form-control" x-model="endDate" @change="updateDates()" :min="startDate" ';
            if ($field->is_required) {
                $html .= 'required ';
            }
            $html .= '>';
        }
        $html .= '</div>';

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
        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';
        $html .= $this->renderLabel($field, $elementId ?? '');

        $html .= '<div class="row g-2">';

        $html .= '<div class="col-md-6">';
        $html .= '<label class="form-label small">Start Date</label>';
        $html .= '<input type="date" class="form-control" disabled>';
        $html .= '</div>';

        $html .= '<div class="col-md-6">';
        $html .= '<label class="form-label small">End Date</label>';
        $html .= '<input type="date" class="form-control" disabled>';
        $html .= '</div>';

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
        $rules[] = 'required_array_keys:start,end';

        return $rules;
    }

    public function processValue(mixed $value): mixed
    {
        return is_array($value) ? json_encode($value) : $value;
    }

    public function getConfigSchema(): array
    {
        return parent::getConfigSchema();
    }

    public function getAvailableValidationOptions(): array
    {
        return [
            'start_after' => [
                'type' => 'date',
                'label' => 'Start Date Must Be After',
                'help' => 'Start date must be after this date',
                'rule_format' => 'start_after:{value}',
                'placeholder' => 'YYYY-MM-DD',
            ],
            'start_before' => [
                'type' => 'date',
                'label' => 'Start Date Must Be Before',
                'help' => 'Start date must be before this date',
                'rule_format' => 'start_before:{value}',
                'placeholder' => 'YYYY-MM-DD',
            ],
            'max_duration_days' => [
                'type' => 'number',
                'label' => 'Maximum Duration (Days)',
                'help' => 'Maximum number of days between start and end date',
                'rule_format' => 'max_duration_days:{value}',
                'placeholder' => '30',
            ],
            'min_duration_days' => [
                'type' => 'number',
                'label' => 'Minimum Duration (Days)',
                'help' => 'Minimum number of days between start and end date',
                'rule_format' => 'min_duration_days:{value}',
                'placeholder' => '1',
            ],
        ];
    }
}
