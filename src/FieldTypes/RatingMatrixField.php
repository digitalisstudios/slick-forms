<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;

class RatingMatrixField extends BaseFieldType
{
    public function getName(): string
    {
        return 'rating_matrix';
    }

    public function getLabel(): string
    {
        return 'Rating Matrix';
    }

    public function getIcon(): string
    {
        return 'bi bi-grid-3x3';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $attributes = $this->getCommonAttributes($field);
        $wireModel = 'formData.field_'.$field->id;

        // Get configuration
        $rows = $field->options['rows'] ?? [['value' => 'row1', 'label' => 'Item 1']];
        $columns = $field->options['columns'] ?? [['value' => '1', 'label' => '1'], ['value' => '2', 'label' => '2'], ['value' => '3', 'label' => '3'], ['value' => '4', 'label' => '4'], ['value' => '5', 'label' => '5']];
        $inputType = $field->options['input_type'] ?? 'radio'; // 'radio' or 'select'
        $allowNA = $field->options['allow_na'] ?? false;

        // Parse existing value
        $matrixData = is_string($value) ? json_decode($value, true) : ($value ?? []);

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';

        // Render label
        $html .= $this->renderLabel($field, $attributes['id']);

        // Matrix wrapper with Alpine.js
        $html .= '<div x-data="{';
        $html .= '  matrixData: $wire.entangle(\''.$wireModel.'\'),';
        $html .= '  ratings: '.json_encode($matrixData);
        $html .= '}" x-init="';
        $html .= 'if (typeof matrixData === \'string\') { ratings = JSON.parse(matrixData); }';
        $html .= 'else if (matrixData) { ratings = matrixData; }';
        $html .= '$watch(\'ratings\', value => { matrixData = JSON.stringify(value); });';
        $html .= '">';

        // Responsive table wrapper
        $html .= '<div class="table-responsive">';
        $html .= '<table class="table table-bordered rating-matrix-table">';

        // Table header
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th></th>'; // Empty corner cell
        foreach ($columns as $column) {
            $html .= '<th class="text-center">'.htmlspecialchars($column['label']).'</th>';
        }
        if ($allowNA) {
            $html .= '<th class="text-center">N/A</th>';
        }
        $html .= '</tr>';
        $html .= '</thead>';

        // Table body
        $html .= '<tbody>';
        foreach ($rows as $index => $row) {
            $rowValue = $row['value'];
            $html .= '<tr>';
            $html .= '<td><strong>'.htmlspecialchars($row['label']).'</strong></td>';

            if ($inputType === 'radio') {
                // Radio button for each column
                foreach ($columns as $column) {
                    $colValue = $column['value'];
                    $inputId = $attributes['id'].'_'.$rowValue.'_'.$colValue;
                    $isChecked = isset($matrixData[$rowValue]) && $matrixData[$rowValue] == $colValue;

                    $html .= '<td class="text-center">';
                    $html .= '<input type="radio" ';
                    $html .= 'id="'.$inputId.'" ';
                    $html .= 'name="'.$attributes['id'].'_'.$rowValue.'" ';
                    $html .= 'value="'.$colValue.'" ';
                    $html .= 'x-model="ratings[\''.$rowValue.'\']" ';
                    if ($isChecked) {
                        $html .= 'checked ';
                    }
                    $html .= 'class="form-check-input">';
                    $html .= '</td>';
                }

                // N/A option
                if ($allowNA) {
                    $inputId = $attributes['id'].'_'.$rowValue.'_na';
                    $isChecked = isset($matrixData[$rowValue]) && $matrixData[$rowValue] === 'na';

                    $html .= '<td class="text-center">';
                    $html .= '<input type="radio" ';
                    $html .= 'id="'.$inputId.'" ';
                    $html .= 'name="'.$attributes['id'].'_'.$rowValue.'" ';
                    $html .= 'value="na" ';
                    $html .= 'x-model="ratings[\''.$rowValue.'\']" ';
                    if ($isChecked) {
                        $html .= 'checked ';
                    }
                    $html .= 'class="form-check-input">';
                    $html .= '</td>';
                }
            } else {
                // Select dropdown (spans all columns)
                $html .= '<td colspan="'.(count($columns) + ($allowNA ? 1 : 0)).'">';
                $html .= '<select class="form-select" x-model="ratings[\''.$rowValue.'\']">';
                $html .= '<option value="">-- Select --</option>';
                foreach ($columns as $column) {
                    $isSelected = isset($matrixData[$rowValue]) && $matrixData[$rowValue] == $column['value'];
                    $html .= '<option value="'.$column['value'].'"'.($isSelected ? ' selected' : '').'>';
                    $html .= htmlspecialchars($column['label']);
                    $html .= '</option>';
                }
                if ($allowNA) {
                    $isSelected = isset($matrixData[$rowValue]) && $matrixData[$rowValue] === 'na';
                    $html .= '<option value="na"'.($isSelected ? ' selected' : '').'>N/A</option>';
                }
                $html .= '</select>';
                $html .= '</td>';
            }

            $html .= '</tr>';
        }
        $html .= '</tbody>';

        $html .= '</table>';
        $html .= '</div>'; // End table-responsive

        $html .= '</div>'; // End Alpine wrapper

        // Hidden input for validation
        $html .= '<input type="hidden" '.$this->getWireModelAttribute($field).'="'.$wireModel.'" '.$this->getValidationAttributes($field).'>';

        $html .= $this->renderInvalidFeedback($field);
        $html .= $this->renderValidFeedback($field);
        $html .= $this->renderHelpText($field);

        $html .= '</div>';

        return $html;
    }

    public function renderBuilder(CustomFormField $field): string
    {
        $rows = $field->options['rows'] ?? [['value' => 'row1', 'label' => 'Item 1']];
        $columns = $field->options['columns'] ?? [['value' => '1', 'label' => '1'], ['value' => '2', 'label' => '2'], ['value' => '3', 'label' => '3'], ['value' => '4', 'label' => '4'], ['value' => '5', 'label' => '5']];
        $inputType = $field->options['input_type'] ?? 'radio';
        $allowNA = $field->options['allow_na'] ?? false;

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';
        $html .= $this->renderLabel($field, '');

        // Preview table
        $html .= '<div class="table-responsive">';
        $html .= '<table class="table table-bordered table-sm rating-matrix-preview">';

        // Header
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th></th>';
        foreach ($columns as $column) {
            $html .= '<th class="text-center">'.htmlspecialchars($column['label']).'</th>';
        }
        if ($allowNA) {
            $html .= '<th class="text-center">N/A</th>';
        }
        $html .= '</tr>';
        $html .= '</thead>';

        // Body
        $html .= '<tbody>';
        foreach ($rows as $row) {
            $html .= '<tr>';
            $html .= '<td><strong>'.htmlspecialchars($row['label']).'</strong></td>';

            if ($inputType === 'radio') {
                foreach ($columns as $column) {
                    $html .= '<td class="text-center"><input type="radio" disabled class="form-check-input"></td>';
                }
                if ($allowNA) {
                    $html .= '<td class="text-center"><input type="radio" disabled class="form-check-input"></td>';
                }
            } else {
                $html .= '<td colspan="'.(count($columns) + ($allowNA ? 1 : 0)).'"><select class="form-select form-select-sm" disabled><option>-- Select --</option></select></td>';
            }

            $html .= '</tr>';
        }
        $html .= '</tbody>';

        $html .= '</table>';
        $html .= '</div>';

        $html .= $this->renderHelpText($field);
        $html .= '</div>';

        return $html;
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        $rules = parent::validate($field, $value);
        $rules[] = 'json';
        $rules[] = 'nullable';

        return $rules;
    }

    public function processValue(mixed $value): mixed
    {
        // Store as JSON with row=>rating mapping
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $value;
            }
        }

        return json_encode($value);
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'rows' => [
                'type' => 'repeater',
                'label' => 'Matrix Rows (Items)',
                'tab' => 'options',
                'target' => 'options',
                'fields' => [
                    [
                        'name' => 'value',
                        'label' => 'Value',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'name' => 'label',
                        'label' => 'Label',
                        'type' => 'text',
                        'required' => true,
                    ],
                ],
                'default' => [
                    ['value' => 'row1', 'label' => 'Item 1'],
                    ['value' => 'row2', 'label' => 'Item 2'],
                    ['value' => 'row3', 'label' => 'Item 3'],
                ],
                'help' => 'Items to be rated (rows in the matrix)',
            ],
            'columns' => [
                'type' => 'repeater',
                'label' => 'Rating Scale (Columns)',
                'tab' => 'options',
                'target' => 'options',
                'fields' => [
                    [
                        'name' => 'value',
                        'label' => 'Value',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'name' => 'label',
                        'label' => 'Label',
                        'type' => 'text',
                        'required' => true,
                    ],
                ],
                'default' => [
                    ['value' => '1', 'label' => '1'],
                    ['value' => '2', 'label' => '2'],
                    ['value' => '3', 'label' => '3'],
                    ['value' => '4', 'label' => '4'],
                    ['value' => '5', 'label' => '5'],
                ],
                'help' => 'Rating scale options (columns in the matrix)',
            ],
            'input_type' => [
                'type' => 'select',
                'label' => 'Input Type',
                'tab' => 'options',
                'target' => 'options',
                'options' => [
                    'radio' => 'Radio Buttons (Traditional Matrix)',
                    'select' => 'Dropdown Select (Mobile Friendly)',
                ],
                'default' => 'radio',
                'help' => 'How users select their ratings',
            ],
            'allow_na' => [
                'type' => 'switch',
                'label' => 'Allow N/A Option',
                'tab' => 'options',
                'target' => 'options',
                'default' => false,
                'help' => 'Add a "Not Applicable" option for each row',
            ],
        ]);
    }

    public function getAvailableValidationOptions(): array
    {
        return [
            // Could add validation to require all rows be answered
            // For now, just use required to ensure the field has at least one rating
        ];
    }
}
