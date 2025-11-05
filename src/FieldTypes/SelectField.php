<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Services\DynamicOptionsService;

class SelectField extends BaseFieldType
{
    public function getName(): string
    {
        return 'select';
    }

    public function getLabel(): string
    {
        return 'Select';
    }

    public function getIcon(): string
    {
        return 'bi bi-list-ul';
    }

    protected function getCommonAttributes(CustomFormField $field): array
    {
        $attributes = parent::getCommonAttributes($field);

        $formSelectClass = 'form-select';
        $fieldSize = $field->options['field_size'] ?? '';
        if ($fieldSize === 'sm') {
            $formSelectClass .= ' form-select-sm';
        } elseif ($fieldSize === 'lg') {
            $formSelectClass .= ' form-select-lg';
        }

        $attributes['class'] = $formSelectClass;

        return $attributes;
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $attributes = $this->getCommonAttributes($field);
        $selectId = 'field_'.$field->id;
        $attributes['id'] = $selectId;
        $attributes[$this->getWireModelAttribute($field)] = 'formData.field_'.$field->id;
        $attributes['@change'] = '$wire.refreshVisibility()';

        $options = $this->getOptionsForField($field);
        $isMultiple = $field->options['multiple'] ?? false;
        $isSearchable = $field->options['searchable'] ?? false;
        $searchPlaceholder = $field->options['search_placeholder'] ?? 'Search options...';

        // Determine selected values
        if ($value !== null && $value !== '') {
            // Use submitted/saved value
            $selectedValues = is_array($value) ? $value : [$value];
        } else {
            // Use default selections
            $defaultValue = $this->getDefaultValue($field);
            $selectedValues = is_array($defaultValue) ? $defaultValue : ($defaultValue ? [$defaultValue] : []);
        }

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';
        $html .= $this->renderLabelWithFloating($field, $attributes['id']);

        if ($isSearchable) {
            $html .= '<div wire:ignore>';
        }

        $selectHtml = '<select '.$this->renderAttributes($attributes).$this->getValidationAttributes($field);
        if ($isMultiple) {
            $selectHtml .= ' multiple';
        }
        $selectHtml .= '>';

        if (! $isMultiple) {
            $selectHtml .= '<option value="">-- Select --</option>';
        }

        foreach ($options as $option) {
            $optionValue = $option['value'] ?? $option['label'];
            $selected = in_array($optionValue, $selectedValues) ? 'selected' : '';
            $selectHtml .= sprintf(
                '<option value="%s" %s>%s</option>',
                htmlspecialchars($optionValue),
                $selected,
                htmlspecialchars($option['label'])
            );
        }

        $selectHtml .= '</select>';

        if ($isSearchable) {
            // Tom Select doesn't support floating labels, just output the select
            $html .= $selectHtml;
            $html .= '</div>';

            // Initialize Tom Select with vanilla JavaScript
            $html .= '<script>
                (function() {
                    const selectId = "'.$selectId.'";
                    const fieldKey = "formData.field_'.$field->id.'";

                    function initTomSelect() {
                        const selectEl = document.getElementById(selectId);
                        if (!selectEl) return;

                        // Prevent double initialization
                        if (selectEl.tomselect) return;

                        // Wait for TomSelect library to load
                        if (typeof TomSelect === "undefined") {
                            setTimeout(initTomSelect, 100);
                            return;
                        }

                        const tomSelect = new TomSelect(\'#\' + selectId, {
                            plugins: '.($isMultiple ? "['remove_button']" : '[]').',
                            placeholder: \''.addslashes(htmlspecialchars($searchPlaceholder)).'\',
                            allowEmptyOption: '.($isMultiple ? 'false' : 'true').',
                            '.($isMultiple ? 'maxItems: null,' : '').'
                            onChange: function(value) {
                                // Update Livewire when Tom Select value changes
                                const component = Livewire.find(selectEl.closest(\'[wire\\\\:id]\').getAttribute(\'wire:id\'));
                                if (component) {
                                    component.set(fieldKey, value);
                                    component.call("refreshVisibility");
                                }
                            }
                        });

                        // Store instance for cleanup
                        selectEl.tomselect = tomSelect;
                    }

                    // Initialize on page load
                    if (document.readyState === \'loading\') {
                        document.addEventListener(\'DOMContentLoaded\', initTomSelect);
                    } else {
                        initTomSelect();
                    }

                    // Reinitialize after Livewire updates (if needed)
                    document.addEventListener(\'livewire:navigated\', initTomSelect);
                })();
            </script>';
        } else {
            // Standard select - floating labels only work on single selects
            if ($isMultiple) {
                $html .= $selectHtml;
                $html .= '<div class="form-text">Hold Ctrl (Cmd on Mac) to select multiple options</div>';
            } else {
                $html .= $this->wrapFloatingLabel($field, $selectHtml);
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
        $options = $this->getOptionsForField($field);
        $isMultiple = $field->options['multiple'] ?? false;
        $isSearchable = $field->options['searchable'] ?? false;

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';
        $html .= $this->renderLabel($field, $elementId ?? '');

        $html .= '<select class="form-select" disabled';
        if ($isMultiple) {
            $html .= ' multiple size="5"';
        }
        $html .= '>';

        if (! $isMultiple) {
            $html .= '<option value="">-- Select --</option>';
        }

        foreach ($options as $option) {
            $html .= '<option>'.htmlspecialchars($option['label']).'</option>';
        }

        $html .= '</select>';

        // Show info about enabled features
        $features = [];
        if ($isMultiple) {
            $features[] = 'Multiple selection';
        }
        if ($isSearchable) {
            $features[] = 'Searchable';
        }
        if (! empty($features)) {
            $html .= '<div class="form-text small"><i class="bi bi-info-circle me-1"></i>'.implode(', ', $features).' enabled</div>';
        }

        $html .= $this->renderHelpText($field);

        $html .= '</div>';

        return $html;
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        $rules = parent::validate($field, $value);
        $isMultiple = $field->options['multiple'] ?? false;

        if ($isMultiple) {
            $rules[] = 'array';
        } else {
            $rules[] = 'string';
        }

        return $rules;
    }

    public function processValue(mixed $value): mixed
    {
        // If it's an array (multiple selection), encode as JSON
        return is_array($value) ? json_encode($value) : $value;
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
                'help' => 'Define the available dropdown options',
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
            // Dynamic URL Options
            'source_url' => [
                'type' => 'text',
                'label' => 'API URL',
                'tab' => 'options',
                'target' => 'options',
                'required' => false,
                'help' => 'URL to fetch options from (use {parent} for cascading)',
                'placeholder' => 'https://api.example.com/countries',
                'show_if' => [
                    'field' => 'option_source',
                    'operator' => '==',
                    'value' => 'url',
                ],
            ],
            'headers' => [
                'type' => 'textarea',
                'label' => 'HTTP Headers (JSON)',
                'tab' => 'options',
                'target' => 'options',
                'required' => false,
                'help' => 'JSON object of headers to include in request',
                'placeholder' => '{"Authorization": "Bearer token"}',
                'show_if' => [
                    'field' => 'option_source',
                    'operator' => '==',
                    'value' => 'url',
                ],
            ],
            'value_key' => [
                'type' => 'text',
                'label' => 'Value Key',
                'tab' => 'options',
                'target' => 'options',
                'default' => 'value',
                'required' => false,
                'help' => 'JSON path to value field (e.g., "id" or "data.id")',
                'show_if' => [
                    'field' => 'option_source',
                    'operator' => '==',
                    'value' => 'url',
                ],
            ],
            'label_key' => [
                'type' => 'text',
                'label' => 'Label Key',
                'tab' => 'options',
                'target' => 'options',
                'default' => 'label',
                'required' => false,
                'help' => 'JSON path to label field (e.g., "name" or "data.name")',
                'show_if' => [
                    'field' => 'option_source',
                    'operator' => '==',
                    'value' => 'url',
                ],
            ],
            // Dynamic Model Options
            'model_class' => [
                'type' => 'text',
                'label' => 'Model Class',
                'tab' => 'options',
                'target' => 'options',
                'required' => false,
                'help' => 'Fully qualified model class (e.g., App\\Models\\Country)',
                'placeholder' => 'App\\Models\\Country',
                'show_if' => [
                    'field' => 'option_source',
                    'operator' => '==',
                    'value' => 'model',
                ],
            ],
            'value_column' => [
                'type' => 'text',
                'label' => 'Value Column',
                'tab' => 'options',
                'target' => 'options',
                'default' => 'id',
                'required' => false,
                'help' => 'Database column for option value',
                'show_if' => [
                    'field' => 'option_source',
                    'operator' => '==',
                    'value' => 'model',
                ],
            ],
            'label_column' => [
                'type' => 'text',
                'label' => 'Label Column',
                'tab' => 'options',
                'target' => 'options',
                'default' => 'name',
                'required' => false,
                'help' => 'Database column for option label',
                'show_if' => [
                    'field' => 'option_source',
                    'operator' => '==',
                    'value' => 'model',
                ],
            ],
            'scope' => [
                'type' => 'text',
                'label' => 'Query Scope',
                'tab' => 'options',
                'target' => 'options',
                'required' => false,
                'help' => 'Optional scope method name (e.g., "active")',
                'placeholder' => 'active',
                'show_if' => [
                    'field' => 'option_source',
                    'operator' => '==',
                    'value' => 'model',
                ],
            ],
            'where_conditions' => [
                'type' => 'textarea',
                'label' => 'Where Conditions (JSON)',
                'tab' => 'options',
                'target' => 'options',
                'required' => false,
                'help' => 'JSON object of where conditions (e.g., {"status": "active"})',
                'placeholder' => '{"status": "active"}',
                'show_if' => [
                    'field' => 'option_source',
                    'operator' => '==',
                    'value' => 'model',
                ],
            ],
            'multiple' => [
                'type' => 'switch',
                'label' => 'Allow Multiple Selections',
                'tab' => 'options',
                'target' => 'options',
                'default' => false,
                'required' => false,
                'help' => 'Allow users to select multiple options',
            ],
            'searchable' => [
                'type' => 'switch',
                'label' => 'Enable Search/Filter',
                'tab' => 'options',
                'target' => 'options',
                'default' => false,
                'required' => false,
                'help' => 'Add search functionality using Tom Select',
            ],
            'search_placeholder' => [
                'type' => 'text',
                'label' => 'Search Placeholder',
                'tab' => 'options',
                'target' => 'options',
                'default' => 'Search options...',
                'required' => false,
                'help' => 'Placeholder text for the search input',
                'show_if' => [
                    'field' => 'searchable',
                    'operator' => '==',
                    'value' => true,
                ],
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
            'min' => [
                'type' => 'number',
                'label' => 'Minimum Selections',
                'help' => 'For multiple select: minimum number of selections required',
                'rule_format' => 'min:{value}',
            ],
            'max' => [
                'type' => 'number',
                'label' => 'Maximum Selections',
                'help' => 'For multiple select: maximum number of selections allowed',
                'rule_format' => 'max:{value}',
            ],
        ];
    }

    public function getDefaultValue(CustomFormField $field): mixed
    {
        $options = $this->getOptionsForField($field);
        $isMultiple = $field->options['multiple'] ?? false;
        $defaults = [];

        foreach ($options as $option) {
            if (($option['default'] ?? false) === true) {
                $defaults[] = $option['value'] ?? $option['label'];
            }
        }

        if ($isMultiple) {
            return $defaults; // Return array for multiple select
        }

        return $defaults[0] ?? null; // Return single value or null
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
