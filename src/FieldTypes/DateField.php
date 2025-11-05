<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;

class DateField extends BaseFieldType
{
    public function getName(): string
    {
        return 'date';
    }

    public function getLabel(): string
    {
        return 'Date';
    }

    public function getIcon(): string
    {
        return 'bi bi-calendar';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $enableFlatpickr = $field->options['enable_flatpickr'] ?? true;
        $dateFormat = $field->options['date_format'] ?? 'm/d/Y';
        $minDate = $field->options['min_date'] ?? '';
        $maxDate = $field->options['max_date'] ?? '';
        $enableTime = $field->options['enable_time'] ?? false;
        $fieldId = 'field_'.$field->id;

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';
        $useFloating = $this->useFloatingLabel($field);

        if ($enableFlatpickr) {
            // Use Flatpickr with Alpine.js
            $flatpickrOptions = [
                'dateFormat' => $dateFormat,
                'allowInput' => true,
                'enableTime' => $enableTime,
            ];

            if ($minDate) {
                $flatpickrOptions['minDate'] = $minDate;
            }
            if ($maxDate) {
                $flatpickrOptions['maxDate'] = $maxDate;
            }

            $flatpickrJson = htmlspecialchars(json_encode($flatpickrOptions), ENT_QUOTES, 'UTF-8');

            $flatpickrHtml = '<div x-data="{
                picker: null,
                init() {
                    const options = JSON.parse(this.$el.querySelector(\'input\').dataset.flatpickr);
                    this.picker = flatpickr(this.$refs.datepicker, {
                        ...options,
                        onChange: (selectedDates, dateStr) => {
                            $wire.set(\'formData.field_'.$field->id.'\', dateStr);
                            $wire.refreshVisibility();
                        }
                    });
                }
            }" wire:ignore>';

            $flatpickrHtml .= '<input
                type="text"
                class="form-control"
                id="'.$fieldId.'"
                x-ref="datepicker"
                data-flatpickr="'.$flatpickrJson.'"';

            if ($value) {
                $flatpickrHtml .= ' value="'.htmlspecialchars($value).'"';
            }
            if ($field->placeholder) {
                $flatpickrHtml .= ' placeholder="'.htmlspecialchars($field->placeholder).'"';
            }
            if ($field->is_required) {
                $flatpickrHtml .= ' required';
            }

            $flatpickrHtml .= '>';
            $flatpickrHtml .= '</div>';

            // Enhanced picker: render standard label, no floating
            $html .= $this->renderLabel($field, $fieldId);
            $html .= $flatpickrHtml;
        } else {
            // Standard HTML5 date input
            $attributes = $this->getCommonAttributes($field);
            $attributes['type'] = $enableTime ? 'datetime-local' : 'date';
            $attributes[$this->getWireModelAttribute($field)] = 'formData.field_'.$field->id;
            $attributes['@change'] = '$wire.refreshVisibility()';

            if ($value) {
                $attributes['value'] = $value;
            }
            if ($minDate && $minDate !== 'today') {
                $attributes['min'] = $minDate;
            }
            if ($maxDate && $maxDate !== 'today') {
                $attributes['max'] = $maxDate;
            }

            $inputHtml = '<input '.$this->renderAttributes($attributes).$this->getValidationAttributes($field).'>';
            if ($useFloating) {
                $html .= $this->renderLabelWithFloating($field, $fieldId);
                $html .= $this->wrapFloatingLabel($field, $inputHtml);
            } else {
                $html .= $this->renderLabel($field, $fieldId);
                $html .= $inputHtml;
            }
        }

        $html .= $this->renderInvalidFeedback($field);
        $html .= $this->renderValidFeedback($field);
        $html .= $this->renderHelpText($field);

        $html .= '</div>';

        return $html;
    }

    public function renderBuilder(CustomFormField $field): string
    {
        $enableFlatpickr = $field->options['enable_flatpickr'] ?? true;
        $dateFormat = $field->options['date_format'] ?? 'm/d/Y';
        $enableTime = $field->options['enable_time'] ?? false;
        $minDate = $field->options['min_date'] ?? '';
        $maxDate = $field->options['max_date'] ?? '';

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';
        $html .= $this->renderLabel($field, $elementId ?? '');
        $html .= '<input type="'.($enableTime ? 'datetime-local' : 'date').'" class="form-control" disabled>';
        $html .= $this->renderHelpText($field);

        // Show info about enabled features
        $features = [];
        if ($enableFlatpickr) {
            $features[] = 'Enhanced picker';
        }
        $features[] = 'Format: '.$dateFormat;
        if ($enableTime) {
            $features[] = 'Time enabled';
        }
        if ($minDate) {
            $features[] = 'Min: '.$minDate;
        }
        if ($maxDate) {
            $features[] = 'Max: '.$maxDate;
        }

        if (! empty($features)) {
            $html .= '<div class="form-text small"><i class="bi bi-info-circle me-1"></i>'.implode(' | ', $features).'</div>';
        }

        $html .= '</div>';

        return $html;
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        $rules = parent::validate($field, $value);
        $rules[] = 'date';

        return $rules;
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'enable_flatpickr' => [
                'type' => 'switch',
                'label' => 'Enable Enhanced Date Picker',
                'tab' => 'options',
                'target' => 'options',
                'default' => true,
                'required' => false,
                'help' => 'Use Flatpickr for enhanced date selection',
            ],
            'date_format' => [
                'type' => 'select',
                'label' => 'Display Format',
                'tab' => 'options',
                'target' => 'options',
                'options' => [
                    'm/d/Y' => 'MM/DD/YYYY',
                    'd/m/Y' => 'DD/MM/YYYY',
                    'Y-m-d' => 'YYYY-MM-DD',
                    'F d, Y' => 'Month DD, YYYY',
                ],
                'default' => 'm/d/Y',
                'required' => false,
            ],
            'min_date' => [
                'type' => 'text',
                'label' => 'Minimum Date',
                'tab' => 'options',
                'target' => 'options',
                'placeholder' => 'YYYY-MM-DD or "today"',
                'required' => false,
                'help' => 'Earliest selectable date',
            ],
            'max_date' => [
                'type' => 'text',
                'label' => 'Maximum Date',
                'tab' => 'options',
                'target' => 'options',
                'placeholder' => 'YYYY-MM-DD or "today"',
                'required' => false,
                'help' => 'Latest selectable date',
            ],
            'enable_time' => [
                'type' => 'switch',
                'label' => 'Include Time Selection',
                'tab' => 'options',
                'target' => 'options',
                'default' => false,
                'required' => false,
                'help' => 'Allow time selection in addition to date',
            ],
        ]);
    }

    public function getAvailableValidationOptions(): array
    {
        return [
            'after' => [
                'type' => 'date',
                'label' => 'Must Be After',
                'help' => 'Date must be after this date',
                'rule_format' => 'after:{value}',
                'placeholder' => 'YYYY-MM-DD',
            ],
            'after_or_equal' => [
                'type' => 'date',
                'label' => 'Must Be After Or Equal',
                'help' => 'Date must be after or equal to this date',
                'rule_format' => 'after_or_equal:{value}',
                'placeholder' => 'YYYY-MM-DD',
            ],
            'before' => [
                'type' => 'date',
                'label' => 'Must Be Before',
                'help' => 'Date must be before this date',
                'rule_format' => 'before:{value}',
                'placeholder' => 'YYYY-MM-DD',
            ],
            'before_or_equal' => [
                'type' => 'date',
                'label' => 'Must Be Before Or Equal',
                'help' => 'Date must be before or equal to this date',
                'rule_format' => 'before_or_equal:{value}',
                'placeholder' => 'YYYY-MM-DD',
            ],
            'date_format' => [
                'type' => 'text',
                'label' => 'Date Format',
                'help' => 'Required date format (e.g., Y-m-d, m/d/Y)',
                'rule_format' => 'date_format:{value}',
                'placeholder' => 'Y-m-d',
            ],
        ];
    }
}
