<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Services\DynamicOptionsService;

class RadioField extends BaseFieldType
{
    public function getName(): string
    {
        return 'radio';
    }

    public function getLabel(): string
    {
        return 'Radio Group';
    }

    public function getIcon(): string
    {
        return 'bi bi-record-circle';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $options = $this->getOptionsForField($field);
        $layout = $field->options['layout'] ?? 'vertical';
        $isInline = $layout === 'horizontal';

        // Determine default value if not set
        if ($value === null || $value === '') {
            $value = $this->getDefaultValue($field);
        }

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';
        // Note: Radio groups don't support floating labels, will render as normal label
        $fieldId = 'field_'.$field->id;
        $html .= $this->renderLabelWithFloating($field, $fieldId);

        // Wrap in flex container for inline layout
        if ($isInline) {
            $html .= '<div class="d-flex flex-wrap gap-3">';
        }

        foreach ($options as $index => $option) {
            $optionValue = $option['value'] ?? $option['label'];
            $checked = ($value == $optionValue) ? 'checked' : '';
            $radioId = 'field_'.$field->id.'_'.$index;

            $checkClasses = $isInline ? 'form-check form-check-inline' : 'form-check';

            $html .= '<div class="'.$checkClasses.'">';
            $html .= sprintf(
                '<input class="form-check-input" type="radio" name="field_%d" id="%s" value="%s" wire:model="formData.field_%d" @change="$wire.refreshVisibility()" %s>',
                $field->id,
                $radioId,
                htmlspecialchars($optionValue),
                $field->id,
                $checked
            );
            $html .= sprintf(
                '<label class="form-check-label" for="%s">%s</label>',
                $radioId,
                htmlspecialchars($option['label'])
            );
            $html .= '</div>';
        }

        if ($isInline) {
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
        $options = $this->getOptionsForField($field);
        $layout = $field->options['layout'] ?? 'vertical';
        $isInline = $layout === 'horizontal';

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';
        $html .= $this->renderLabel($field, $elementId ?? '');

        // Wrap in flex container for inline layout
        if ($isInline) {
            $html .= '<div class="d-flex flex-wrap gap-3">';
        }

        foreach ($options as $option) {
            $checkClasses = $isInline ? 'form-check form-check-inline' : 'form-check';

            $html .= '<div class="'.$checkClasses.'">';
            $html .= '<input class="form-check-input" type="radio" disabled>';
            $html .= '<label class="form-check-label">'.htmlspecialchars($option['label']).'</label>';
            $html .= '</div>';
        }

        if ($isInline) {
            $html .= '</div>';
        }

        $html .= $this->renderInvalidFeedback($field);
        $html .= $this->renderValidFeedback($field);
        $html .= $this->renderHelpText($field);

        // Show layout info
        if ($isInline) {
            $html .= '<div class="form-text small"><i class="bi bi-info-circle me-1"></i>Horizontal layout</div>';
        }

        $html .= '</div>';

        return $html;
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        $rules = parent::validate($field, $value);
        $rules[] = 'string';

        return $rules;
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'option_source' => [
                'type' => 'select',
                'label' => 'Option Source',
                'tab' => 'options',
                'target' => 'options',
                'default' => 'static',
                'required' => true,
                'help' => 'Choose where options come from',
                'options' => [
                    ['label' => 'Static (Manual Entry)', 'value' => 'static'],
                    ['label' => 'Dynamic (URL/API)', 'value' => 'url'],
                    ['label' => 'Dynamic (Database Model)', 'value' => 'model'],
                ],
            ],
            'values' => [
                'type' => 'options',
                'label' => 'Options',
                'tab' => 'options',
                'target' => 'options',
                'required' => true,
                'help' => 'Define the available radio button options',
                'default' => [
                    ['label' => 'Option 1', 'value' => '1', 'default' => false],
                    ['label' => 'Option 2', 'value' => '2', 'default' => false],
                    ['label' => 'Option 3', 'value' => '3', 'default' => false],
                ],
                'show_if' => [
                    'field' => 'option_source',
                    'operator' => '==',
                    'value' => 'static',
                ],
            ],
            // Dynamic URL Options (same as SelectField)
            'source_url' => [
                'type' => 'text',
                'label' => 'API URL',
                'tab' => 'options',
                'target' => 'options',
                'required' => false,
                'help' => 'URL to fetch options from (use {parent} for cascading)',
                'placeholder' => 'https://api.example.com/countries',
                'show_if' => ['field' => 'option_source', 'operator' => '==', 'value' => 'url'],
            ],
            'headers' => [
                'type' => 'textarea',
                'label' => 'HTTP Headers (JSON)',
                'tab' => 'options',
                'target' => 'options',
                'required' => false,
                'help' => 'JSON object of headers to include in request',
                'placeholder' => '{"Authorization": "Bearer token"}',
                'show_if' => ['field' => 'option_source', 'operator' => '==', 'value' => 'url'],
            ],
            'value_key' => [
                'type' => 'text',
                'label' => 'Value Key',
                'tab' => 'options',
                'target' => 'options',
                'default' => 'value',
                'required' => false,
                'help' => 'JSON path to value field',
                'show_if' => ['field' => 'option_source', 'operator' => '==', 'value' => 'url'],
            ],
            'label_key' => [
                'type' => 'text',
                'label' => 'Label Key',
                'tab' => 'options',
                'target' => 'options',
                'default' => 'label',
                'required' => false,
                'help' => 'JSON path to label field',
                'show_if' => ['field' => 'option_source', 'operator' => '==', 'value' => 'url'],
            ],
            // Dynamic Model Options
            'model_class' => [
                'type' => 'text',
                'label' => 'Model Class',
                'tab' => 'options',
                'target' => 'options',
                'required' => false,
                'help' => 'Fully qualified model class',
                'placeholder' => 'App\\Models\\Country',
                'show_if' => ['field' => 'option_source', 'operator' => '==', 'value' => 'model'],
            ],
            'value_column' => [
                'type' => 'text',
                'label' => 'Value Column',
                'tab' => 'options',
                'target' => 'options',
                'default' => 'id',
                'required' => false,
                'help' => 'Database column for option value',
                'show_if' => ['field' => 'option_source', 'operator' => '==', 'value' => 'model'],
            ],
            'label_column' => [
                'type' => 'text',
                'label' => 'Label Column',
                'tab' => 'options',
                'target' => 'options',
                'default' => 'name',
                'required' => false,
                'help' => 'Database column for option label',
                'show_if' => ['field' => 'option_source', 'operator' => '==', 'value' => 'model'],
            ],
            'scope' => [
                'type' => 'text',
                'label' => 'Query Scope',
                'tab' => 'options',
                'target' => 'options',
                'required' => false,
                'help' => 'Optional scope method name',
                'placeholder' => 'active',
                'show_if' => ['field' => 'option_source', 'operator' => '==', 'value' => 'model'],
            ],
            'where_conditions' => [
                'type' => 'textarea',
                'label' => 'Where Conditions (JSON)',
                'tab' => 'options',
                'target' => 'options',
                'required' => false,
                'help' => 'JSON object of where conditions',
                'placeholder' => '{"status": "active"}',
                'show_if' => ['field' => 'option_source', 'operator' => '==', 'value' => 'model'],
            ],
            'layout' => [
                'type' => 'select',
                'label' => 'Layout',
                'tab' => 'options',
                'target' => 'options',
                'options' => [
                    'vertical' => 'Vertical (Stacked)',
                    'horizontal' => 'Horizontal (Inline)',
                ],
                'default' => 'vertical',
                'required' => false,
            ],
        ]);
    }

    public function getAvailableValidationOptions(): array
    {
        return [
            'in' => [
                'type' => 'text',
                'label' => 'Allowed Values',
                'help' => 'Comma-separated list of allowed values',
                'rule_format' => 'in:{value}',
                'placeholder' => 'value1,value2,value3',
            ],
        ];
    }

    public function getDefaultValue(CustomFormField $field): mixed
    {
        $options = $this->getOptionsForField($field);

        foreach ($options as $option) {
            if (($option['default'] ?? false) === true) {
                return $option['value'] ?? $option['label'];
            }
        }

        return null;
    }

    /**
     * Get options for the field (static or dynamic)
     *
     * @param  string|null  $parentValue  Parent field value for cascading dropdowns
     * @return array Options array with 'value' and 'label' keys
     */
    protected function getOptionsForField(CustomFormField $field, ?string $parentValue = null): array
    {
        $fieldOptions = $field->options ?? [];

        // Check option source (from UI configuration)
        $optionSource = $fieldOptions['option_source'] ?? 'static';

        // Check if dynamic options are configured
        if ($optionSource !== 'static') {
            // Normalize configuration for DynamicOptionsService
            // Must get the entire options array, modify it, and reassign (can't modify JSON cast properties directly)
            $options = $field->options ?? [];
            $options['dynamic_source'] = $optionSource; // 'url' or 'model'

            // Parse JSON strings from textarea inputs
            if (isset($fieldOptions['headers']) && is_string($fieldOptions['headers'])) {
                $options['headers'] = json_decode($fieldOptions['headers'], true) ?? [];
            }

            if (isset($fieldOptions['where_conditions']) && is_string($fieldOptions['where_conditions'])) {
                $options['where'] = json_decode($fieldOptions['where_conditions'], true) ?? [];
            }

            // Assign the modified options back to the field
            $field->options = $options;

            /** @var DynamicOptionsService $service */
            $service = app(DynamicOptionsService::class);

            return $service->loadOptions($field, $parentValue);
        }

        // Return static options
        return $fieldOptions['values'] ?? [];
    }
}
