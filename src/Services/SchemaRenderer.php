<?php

namespace DigitalisStudios\SlickForms\Services;

use Illuminate\Support\Str;

/**
 * SchemaRenderer Service
 *
 * Auto-generates property panel UI from field/element configuration schemas.
 * Supports multiple input types: text, textarea, number, select, switch, color, date, repeater, wysiwyg, ace_editor.
 */
class SchemaRenderer
{
    /**
     * Render a single schema field as HTML
     *
     * @param  string  $fieldKey  The property key (e.g., 'searchable', 'label')
     * @param  array  $fieldConfig  Schema configuration for this field
     * @param  string  $wireModelPrefix  Wire model prefix (e.g., 'fieldOptions', 'elementSettings')
     * @param  mixed  $currentValue  Current value of the field
     * @return string HTML markup
     */
    public function renderField(string $fieldKey, array $fieldConfig, string $wireModelPrefix, mixed $currentValue = null): string
    {
        $type = $fieldConfig['type'] ?? 'text';
        $label = $fieldConfig['label'] ?? Str::title(str_replace('_', ' ', $fieldKey));
        $help = $fieldConfig['help'] ?? null;
        $placeholder = $fieldConfig['placeholder'] ?? '';
        $required = $fieldConfig['required'] ?? false;

        // Use dot notation for wire:model (works with nested property structure)
        $wireModel = "{$wireModelPrefix}.{$fieldKey}";

        // Build Alpine.js x-show directive for conditional visibility
        $xShow = $this->buildConditionalVisibility($fieldConfig, $wireModelPrefix);

        $html = '<div class="mb-3"'.($xShow ? ' '.$xShow : '').'>';

        switch ($type) {
            case 'text':
                $html .= $this->renderTextField($label, $wireModel, $placeholder, $help, $required);
                break;

            case 'textarea':
                $rows = $fieldConfig['rows'] ?? 3;
                $html .= $this->renderTextareaField($label, $wireModel, $placeholder, $help, $required, $rows);
                break;

            case 'number':
                $min = $fieldConfig['min'] ?? null;
                $max = $fieldConfig['max'] ?? null;
                $step = $fieldConfig['step'] ?? null;
                $html .= $this->renderNumberField($label, $wireModel, $placeholder, $help, $required, $min, $max, $step);
                break;

            case 'select':
                $options = $fieldConfig['options'] ?? [];
                $html .= $this->renderSelectField($label, $wireModel, $options, $help, $required);
                break;

            case 'switch':
            case 'checkbox':
                $html .= $this->renderSwitchField($label, $wireModel, $help);
                break;

            case 'color':
                $html .= $this->renderColorField($label, $wireModel, $help);
                break;

            case 'date':
                $html .= $this->renderDateField($label, $wireModel, $help, $required);
                break;

            case 'file':
                $accept = $fieldConfig['accept'] ?? '';
                $html .= $this->renderFileField($label, $wireModel, $accept, $help, $required);
                break;

            case 'options':
                $html .= $this->renderOptionsField($label, $wireModel, $help, $required, $wireModelPrefix);
                break;

            case 'repeater':
                $subfields = $fieldConfig['fields'] ?? [];
                $html .= $this->renderRepeaterField($label, $wireModel, $subfields, $help);
                break;

            case 'wysiwyg':
                $html .= $this->renderWysiwygField($label, $wireModel, $help, $required);
                break;

            case 'ace_editor':
                $mode = $fieldConfig['mode'] ?? 'html';
                $html .= $this->renderAceEditorField($label, $wireModel, $help, $required, $mode);
                break;

            case 'icon_picker':
                $html .= $this->renderIconPickerField($label, $wireModel, $help, $required, $currentValue);
                break;

            case 'divider':
                $html .= '<hr class="my-3">';
                break;

            case 'heading':
                $size = $fieldConfig['size'] ?? 'h6';
                $html .= "<{$size} class=\"text-uppercase text-muted small fw-bold mb-3\">";
                $html .= htmlspecialchars($label);
                $html .= "</{$size}>";
                break;

            case 'custom':
                // Custom type: field is in schema for saving but UI is rendered in custom view
                // Return empty string to prevent duplicate rendering
                return '';

            case 'html':
                // Raw HTML output (useful for previews, instructions, etc.)
                $content = $fieldConfig['content'] ?? '';
                $html .= $content;
                break;

            default:
                // Fallback to text input for unknown types
                $html .= $this->renderTextField($label, $wireModel, $placeholder, $help, $required);
                break;
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Render multiple schema fields for a specific tab
     *
     * @param  array  $schema  Full schema array
     * @param  string  $tab  Tab name to filter by
     * @param  string  $wireModelPrefix  Wire model prefix
     * @param  array  $currentValues  Current values keyed by field key
     * @return string HTML markup
     */
    public function renderTab(array $schema, string $tab, string $wireModelPrefix, array $currentValues = []): string
    {
        $html = '';

        foreach ($schema as $fieldKey => $fieldConfig) {
            $fieldTab = $fieldConfig['tab'] ?? 'basic';

            if ($fieldTab === $tab) {
                $currentValue = $currentValues[$fieldKey] ?? ($fieldConfig['default'] ?? null);
                $html .= $this->renderField($fieldKey, $fieldConfig, $wireModelPrefix, $currentValue);
            }
        }

        return $html;
    }

    /**
     * Render text input field
     */
    protected function renderTextField(string $label, string $wireModel, string $placeholder, ?string $help, bool $required): string
    {
        $html = '<label class="form-label">';
        $html .= htmlspecialchars($label);
        if ($required) {
            $html .= ' <span class="text-danger">*</span>';
        }
        $html .= '</label>';

        $html .= '<input type="text" class="form-control" ';
        $html .= 'wire:model="'.$wireModel.'" ';
        if ($placeholder) {
            $html .= 'placeholder="'.htmlspecialchars($placeholder).'" ';
        }
        if ($required) {
            $html .= 'required ';
        }
        $html .= '>';

        if ($help) {
            $html .= '<div class="form-text small">'.htmlspecialchars($help).'</div>';
        }

        return $html;
    }

    /**
     * Render textarea field
     */
    protected function renderTextareaField(string $label, string $wireModel, string $placeholder, ?string $help, bool $required, int $rows): string
    {
        $html = '<label class="form-label">';
        $html .= htmlspecialchars($label);
        if ($required) {
            $html .= ' <span class="text-danger">*</span>';
        }
        $html .= '</label>';

        $html .= '<textarea class="form-control" ';
        $html .= 'wire:model="'.$wireModel.'" ';
        $html .= 'rows="'.$rows.'" ';
        if ($placeholder) {
            $html .= 'placeholder="'.htmlspecialchars($placeholder).'" ';
        }
        if ($required) {
            $html .= 'required ';
        }
        $html .= '></textarea>';

        if ($help) {
            $html .= '<div class="form-text small">'.htmlspecialchars($help).'</div>';
        }

        return $html;
    }

    /**
     * Render number input field
     */
    protected function renderNumberField(string $label, string $wireModel, string $placeholder, ?string $help, bool $required, ?int $min, ?int $max, ?float $step): string
    {
        $html = '<label class="form-label">';
        $html .= htmlspecialchars($label);
        if ($required) {
            $html .= ' <span class="text-danger">*</span>';
        }
        $html .= '</label>';

        $html .= '<input type="number" class="form-control" ';
        $html .= 'wire:model="'.$wireModel.'" ';
        if ($placeholder) {
            $html .= 'placeholder="'.htmlspecialchars($placeholder).'" ';
        }
        if ($min !== null) {
            $html .= 'min="'.$min.'" ';
        }
        if ($max !== null) {
            $html .= 'max="'.$max.'" ';
        }
        if ($step !== null) {
            $html .= 'step="'.$step.'" ';
        }
        if ($required) {
            $html .= 'required ';
        }
        $html .= '>';

        if ($help) {
            $html .= '<div class="form-text small">'.htmlspecialchars($help).'</div>';
        }

        return $html;
    }

    /**
     * Render select dropdown field
     */
    protected function renderSelectField(string $label, string $wireModel, array $options, ?string $help, bool $required): string
    {
        $html = '<label class="form-label">';
        $html .= htmlspecialchars($label);
        if ($required) {
            $html .= ' <span class="text-danger">*</span>';
        }
        $html .= '</label>';

        $html .= '<select class="form-select" wire:model="'.$wireModel.'" ';
        if ($required) {
            $html .= 'required ';
        }
        $html .= '>';

        foreach ($options as $key => $option) {
            // Handle both array format ['value' => 'x', 'label' => 'y'] and simple key => value format
            if (is_array($option)) {
                $optionValue = $option['value'] ?? $key;
                $optionLabel = $option['label'] ?? $optionValue;
            } else {
                $optionValue = $key;
                $optionLabel = $option;
            }

            $html .= '<option value="'.htmlspecialchars((string) $optionValue).'">';
            $html .= htmlspecialchars((string) $optionLabel);
            $html .= '</option>';
        }

        $html .= '</select>';

        if ($help) {
            $html .= '<div class="form-text small">'.htmlspecialchars($help).'</div>';
        }

        return $html;
    }

    /**
     * Render switch/checkbox field
     */
    protected function renderSwitchField(string $label, string $wireModel, ?string $help): string
    {
        $fieldId = 'switch_'.Str::slug($wireModel);

        $html = '<div class="form-check form-switch">';
        $html .= '<input type="checkbox" class="form-check-input" ';
        $html .= 'id="'.$fieldId.'" ';
        $html .= 'wire:model="'.$wireModel.'" ';
        $html .= 'role="switch">';
        $html .= '<label class="form-check-label" for="'.$fieldId.'">';
        $html .= htmlspecialchars($label);
        $html .= '</label>';
        $html .= '</div>';

        if ($help) {
            $html .= '<div class="form-text small">'.htmlspecialchars($help).'</div>';
        }

        return $html;
    }

    /**
     * Render color picker field
     */
    protected function renderColorField(string $label, string $wireModel, ?string $help): string
    {
        $html = '<label class="form-label">'.htmlspecialchars($label).'</label>';

        $html .= '<input type="color" class="form-control form-control-color" ';
        $html .= 'wire:model="'.$wireModel.'" ';
        $html .= '>';

        if ($help) {
            $html .= '<div class="form-text small">'.htmlspecialchars($help).'</div>';
        }

        return $html;
    }

    /**
     * Render date picker field
     */
    protected function renderDateField(string $label, string $wireModel, ?string $help, bool $required): string
    {
        $html = '<label class="form-label">';
        $html .= htmlspecialchars($label);
        if ($required) {
            $html .= ' <span class="text-danger">*</span>';
        }
        $html .= '</label>';

        $html .= '<input type="date" class="form-control" ';
        $html .= 'wire:model="'.$wireModel.'" ';
        if ($required) {
            $html .= 'required ';
        }
        $html .= '>';

        if ($help) {
            $html .= '<div class="form-text small">'.htmlspecialchars($help).'</div>';
        }

        return $html;
    }

    /**
     * Render file upload field
     */
    protected function renderFileField(string $label, string $wireModel, string $accept, ?string $help, bool $required): string
    {
        $html = '<label class="form-label">';
        $html .= htmlspecialchars($label);
        if ($required) {
            $html .= ' <span class="text-danger">*</span>';
        }
        $html .= '</label>';

        $html .= '<input type="file" class="form-control" ';
        $html .= 'wire:model="'.$wireModel.'" ';
        if ($accept) {
            $html .= 'accept="'.htmlspecialchars($accept).'" ';
        }
        if ($required) {
            $html .= 'required ';
        }
        $html .= '>';

        if ($help) {
            $html .= '<div class="form-text small">'.htmlspecialchars($help).'</div>';
        }

        return $html;
    }

    /**
     * Render repeater field (array of items)
     *
     * Note: This is a simplified implementation. Complex repeaters may need custom blade views.
     */
    protected function renderRepeaterField(string $label, string $wireModel, array $subfields, ?string $help): string
    {
        $html = '<div class="border rounded p-3 bg-light">';
        $html .= '<label class="form-label fw-bold">'.htmlspecialchars($label).'</label>';

        if ($help) {
            $html .= '<div class="form-text small mb-2">'.htmlspecialchars($help).'</div>';
        }

        $html .= '<div class="alert alert-info small mb-2">';
        $html .= 'Repeater fields require custom implementation in blade views for full functionality.';
        $html .= '</div>';

        // Button to add new item
        $html .= '<button type="button" class="btn btn-sm btn-outline-primary" ';
        $html .= 'wire:click="addRepeaterItem(\''.$wireModel.'\')">';
        $html .= '<i class="bi bi-plus-circle me-1"></i> Add Item';
        $html .= '</button>';

        $html .= '</div>';

        return $html;
    }

    /**
     * Render Quill WYSIWYG editor field
     */
    protected function renderWysiwygField(string $label, string $wireModel, ?string $help, bool $required): string
    {
        $editorId = 'quill_'.Str::slug($wireModel);
        $wireKey = 'quill-'.Str::slug($wireModel);

        $html = '<div wire:key="'.$wireKey.'">';
        $html .= '<label class="form-label">';
        $html .= htmlspecialchars($label);
        if ($required) {
            $html .= ' <span class="text-danger">*</span>';
        }
        $html .= '</label>';

        if ($help) {
            $html .= '<div class="form-text small mb-2">'.htmlspecialchars($help).'</div>';
        }

        // Quill editor container
        $html .= '<div id="'.$editorId.'" style="height: 200px; background: white;"></div>';

        // Alpine.js initialization with visibility check and event listener for lazy loading
        $html .= '<div x-data="{ editorContent: \'\', quillInstance: null }" x-init="';
        $html .= 'const initQuill = () => {';
        $html .= '  const el = document.getElementById(\''.$editorId.'\');';
        $html .= '  if (typeof Quill !== \'undefined\' && el && el.offsetParent !== null && !quillInstance) {';
        $html .= '    const quill = new Quill(\'#'.$editorId.'\', {';
        $html .= '      theme: \'snow\',';
        $html .= '      modules: { toolbar: [[\'bold\', \'italic\', \'underline\'], [\'link\'], [{\'list\': \'ordered\'}, {\'list\': \'bullet\'}]] }';
        $html .= '    });';
        $html .= '    quillInstance = quill;';
        $html .= '    const initialContent = $wire.get(\''.$wireModel.'\') || \'\';';
        $html .= '    if (initialContent) { quill.root.innerHTML = initialContent; }';
        $html .= '    editorContent = quill.root.innerHTML;';
        $html .= '    quill.on(\'text-change\', () => {';
        $html .= '      editorContent = quill.root.innerHTML;';
        $html .= '    });';
        $html .= '    $watch(\'editorContent\', value => { $wire.set(\''.$wireModel.'\', value, false); });';
        $html .= '  }';
        $html .= '};';
        $html .= '$nextTick(() => {';
        $html .= '  setTimeout(initQuill, 100);';
        $html .= '  window.addEventListener(\'quill-loaded\', initQuill);';
        $html .= '});';
        $html .= '"></div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Render Ace Editor field
     */
    protected function renderAceEditorField(string $label, string $wireModel, ?string $help, bool $required, string $mode): string
    {
        $editorId = 'ace_'.Str::slug($wireModel);
        $wireKey = 'ace-'.Str::slug($wireModel);

        $html = '<div wire:key="'.$wireKey.'">';
        $html .= '<label class="form-label">';
        $html .= htmlspecialchars($label);
        if ($required) {
            $html .= ' <span class="text-danger">*</span>';
        }
        $html .= '</label>';

        if ($help) {
            $html .= '<div class="form-text small mb-2">'.htmlspecialchars($help).'</div>';
        }

        // Ace editor container
        $html .= '<div id="'.$editorId.'" class="ace-editor-container"></div>';

        // Alpine.js initialization with visibility check and event listener for lazy loading
        $html .= '<div x-data="{ editorContent: \'\', aceInstance: null }" x-init="';
        $html .= 'const initAce = () => {';
        $html .= '  const el = document.getElementById(\''.$editorId.'\');';
        $html .= '  if (typeof ace !== \'undefined\' && el && el.offsetParent !== null && !aceInstance) {';
        $html .= '    const editor = ace.edit(\''.$editorId.'\');';
        $html .= '    aceInstance = editor;';
        $html .= '    editor.setTheme(\'ace/theme/monokai\');';
        $html .= '    editor.session.setMode(\'ace/mode/'.htmlspecialchars($mode).'\');';
        $html .= '    editor.setOptions({';
        $html .= '      fontSize: \'14px\',';
        $html .= '      showPrintMargin: false,';
        $html .= '      enableBasicAutocompletion: true,';
        $html .= '      enableLiveAutocompletion: true,';
        $html .= '      enableSnippets: true';
        $html .= '    });';
        $html .= '    const initialContent = $wire.get(\''.$wireModel.'\') || \'\';';
        $html .= '    if (initialContent) { editor.setValue(initialContent, -1); }';
        $html .= '    editorContent = editor.getValue();';
        $html .= '    editor.session.on(\'change\', () => {';
        $html .= '      editorContent = editor.getValue();';
        $html .= '    });';
        $html .= '    $watch(\'editorContent\', value => { $wire.set(\''.$wireModel.'\', value, false); });';
        $html .= '  }';
        $html .= '};';
        $html .= '$nextTick(() => {';
        $html .= '  setTimeout(initAce, 100);';
        $html .= '  window.addEventListener(\'ace-loaded\', initAce);';
        $html .= '});';
        $html .= '"></div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Render Icon Picker field
     */
    protected function renderIconPickerField(string $label, string $wireModel, ?string $help, bool $required, mixed $currentValue): string
    {
        $pickerId = 'icon_picker_'.Str::slug($wireModel);
        $inputId = 'icon_input_'.Str::slug($wireModel);
        $wireKey = 'icon-picker-'.Str::slug($wireModel);
        $currentIcon = $currentValue ?: '';
        $displayValue = $currentIcon ?: 'None';

        $html = '<div wire:key="'.$wireKey.'" wire:ignore>';
        $html .= '<label for="'.$inputId.'" class="form-label">';
        $html .= htmlspecialchars($label);
        if ($required) {
            $html .= ' <span class="text-danger">*</span>';
        }
        $html .= '</label>';

        if ($help) {
            $html .= '<div class="form-text small mb-2">'.htmlspecialchars($help).'</div>';
        }

        $html .= '<div class="input-group">';
        $html .= '<button id="'.$pickerId.'" class="btn btn-outline-secondary icon-picker-btn" type="button" style="min-width: 60px;" data-wire-model="'.$wireModel.'">';
        if ($currentIcon) {
            $html .= '<i class="'.htmlspecialchars($currentIcon).'"></i>';
        } else {
            $html .= '<span class="text-muted">—</span>';
        }
        $html .= '</button>';
        $html .= '<input type="text" class="form-control icon-picker-input" id="'.$inputId.'" value="'.htmlspecialchars($displayValue).'" placeholder="None" readonly>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Check if a schema has any fields for a specific tab
     *
     * @param  array  $schema  Full schema array
     * @param  string  $tab  Tab name to check
     */
    public function hasFieldsForTab(array $schema, string $tab): bool
    {
        foreach ($schema as $fieldConfig) {
            if (($fieldConfig['tab'] ?? 'basic') === $tab) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all unique tabs from a schema
     *
     * @param  array  $schema  Full schema array
     * @return array Array of unique tab names
     */
    public function getTabsFromSchema(array $schema): array
    {
        $tabs = [];

        foreach ($schema as $fieldConfig) {
            $tab = $fieldConfig['tab'] ?? 'basic';
            if (! in_array($tab, $tabs)) {
                $tabs[] = $tab;
            }
        }

        return $tabs;
    }

    /**
     * Build Alpine.js x-show directive for conditional visibility
     *
     * Supports 'show_if' configuration in schema:
     * - Single condition: ['field' => 'mask_enabled', 'operator' => '==', 'value' => true]
     * - Multiple conditions (AND): [['field' => 'a', ...], ['field' => 'b', ...]]
     *
     * @param  array  $fieldConfig  Schema configuration
     * @param  string  $wireModelPrefix  Wire model prefix for field references
     * @return string Alpine.js x-show attribute or empty string
     */
    protected function buildConditionalVisibility(array $fieldConfig, string $wireModelPrefix): string
    {
        if (! isset($fieldConfig['show_if'])) {
            return '';
        }

        $showIf = $fieldConfig['show_if'];

        // Normalize show_if to standard format
        // Support shorthand: ['input_mode' => 'url'] → ['field' => 'input_mode', 'value' => 'url']
        // Support full: ['field' => 'input_mode', 'operator' => '==', 'value' => 'url']
        $conditions = $this->normalizeShowIfConditions($showIf);

        // Build Alpine.js expression (AND all conditions)
        $expressions = [];

        foreach ($conditions as $condition) {
            $field = $condition['field'] ?? null;
            $operator = $condition['operator'] ?? '==';
            $value = $condition['value'] ?? null;

            if (! $field) {
                continue;
            }

            // Build Alpine expression
            $alpineField = "\$wire.{$wireModelPrefix}.{$field}";

            // Format value for Alpine
            if (is_bool($value)) {
                $alpineValue = $value ? 'true' : 'false';
                $expressions[] = "{$alpineField} {$operator} {$alpineValue}";
            } elseif (is_null($value)) {
                $alpineValue = 'null';
                $expressions[] = "{$alpineField} {$operator} {$alpineValue}";
            } elseif (is_array($value)) {
                // For array values, create OR condition for each value
                $orConditions = [];
                foreach ($value as $v) {
                    $alpineValue = is_string($v) ? "'".addslashes($v)."'" : $v;
                    $orConditions[] = "{$alpineField} {$operator} {$alpineValue}";
                }
                $expressions[] = '('.implode(' || ', $orConditions).')';
            } elseif (is_string($value)) {
                $alpineValue = "'".addslashes($value)."'";
                $expressions[] = "{$alpineField} {$operator} {$alpineValue}";
            } else {
                $alpineValue = $value;
                $expressions[] = "{$alpineField} {$operator} {$alpineValue}";
            }
        }

        if (empty($expressions)) {
            return '';
        }

        // Join conditions with AND (&&)
        $expression = implode(' && ', $expressions);

        return 'x-show="'.htmlspecialchars($expression).'"';
    }

    /**
     * Normalize show_if conditions to standard format
     *
     * Supports multiple formats:
     * 1. Shorthand: ['input_mode' => 'url'] → single condition
     * 2. Full single: ['field' => 'input_mode', 'value' => 'url']
     * 3. Multiple: [['field' => 'a', ...], ['field' => 'b', ...]]
     *
     * @param  array  $showIf  Raw show_if configuration
     * @return array Normalized array of conditions
     */
    protected function normalizeShowIfConditions(array $showIf): array
    {
        // Check if it's already in full format (has 'field' key)
        if (isset($showIf['field'])) {
            return [$showIf];
        }

        // Check if it's an array of conditions (first element is array with 'field' key)
        if (isset($showIf[0]) && is_array($showIf[0]) && isset($showIf[0]['field'])) {
            return $showIf;
        }

        // Otherwise, it's shorthand format: ['input_mode' => 'url', 'another_field' => 'value']
        // Convert each key-value pair to a condition
        $conditions = [];
        foreach ($showIf as $field => $value) {
            $conditions[] = [
                'field' => $field,
                'operator' => '==',
                'value' => $value,
            ];
        }

        return $conditions;
    }

    /**
     * Render options management field for select, radio, and checkbox fields
     */
    protected function renderOptionsField(string $label, string $wireModel, ?string $help, bool $required, string $wireModelPrefix): string
    {
        // The wireModel is already "properties.values" from the schema
        $valuesModel = $wireModel;

        $html = '<label class="form-label">';
        $html .= htmlspecialchars($label);
        if ($required) {
            $html .= ' <span class="text-danger">*</span>';
        }
        $html .= '</label>';

        if ($help) {
            $html .= '<div class="form-text small mb-2">'.htmlspecialchars($help).'</div>';
        }

        // Options list container with Alpine data
        $html .= '<div class="border rounded p-3 bg-light" x-data="{
            newLabel: \'\',
            newValue: \'\',
            addOption() {
                if (this.newLabel && this.newValue) {
                    $wire.call(\'addOption\', \''.$valuesModel.'\', this.newLabel, this.newValue, false);
                    this.newLabel = \'\';
                    this.newValue = \'\';
                }
            },
            get allowMultipleDefaults() {
                const fieldType = $wire.selectedField?.field_type;
                const multiple = $wire.'.$wireModelPrefix.'.multiple;
                return fieldType === \'checkbox\' || (fieldType === \'select\' && multiple);
            },
            setDefault(index) {
                const values = $wire.'.$valuesModel.';
                if (!this.allowMultipleDefaults) {
                    // Clear all other defaults for radio/single-select
                    values.forEach((opt, i) => {
                        if (i !== index) {
                            opt.default = false;
                        }
                    });
                }
                // Toggle the clicked option
                values[index].default = !values[index].default;
            }
        }">';

        // Header row
        $html .= '<div class="d-flex gap-2 mb-2">';
        $html .= '<div style="width: 40px;"><small class="text-muted fw-semibold">Sort</small></div>';
        $html .= '<div style="width: 60px;"><small class="text-muted fw-semibold">Default</small></div>';
        $html .= '<div class="flex-fill"><small class="text-muted fw-semibold">Label</small></div>';
        $html .= '<div class="flex-fill"><small class="text-muted fw-semibold">Value</small></div>';
        $html .= '<div style="width: 40px;"></div>'; // Trash column
        $html .= '</div>';

        // Existing options list (sortable container)
        $html .= '<div class="mb-3" x-init="
            if (typeof Sortable !== \'undefined\') {
                new Sortable($el, {
                    animation: 150,
                    handle: \'.options-drag-handle\',
                    ghostClass: \'bg-primary-subtle\',
                    onEnd: function(evt) {
                        if (evt.oldIndex !== evt.newIndex) {
                            // Reorder the array directly in the reactive state
                            const values = $wire.'.$valuesModel.';
                            const item = values.splice(evt.oldIndex, 1)[0];
                            values.splice(evt.newIndex, 0, item);
                        }
                    }
                });
            }
        ">';
        $html .= '<template x-for="(option, index) in $wire.'.$valuesModel.'" :key="option.value + \'-\' + index">';
        $html .= '<div class="d-flex gap-2 mb-2 align-items-center" style="cursor: move;" :wire:key="\'option-\' + option.value + \'-\' + index">';

        // Drag handle
        $html .= '<div class="text-center" style="width: 40px;">';
        $html .= '<i class="bi bi-grip-vertical text-muted options-drag-handle" style="cursor: grab;"></i>';
        $html .= '</div>';

        // Default selection control
        $html .= '<div class="text-center" style="width: 60px;">';
        $html .= '<input type="checkbox" class="form-check-input" ';
        $html .= 'x-bind:checked="option.default || false" ';
        $html .= '@click="setDefault(index)" ';
        $html .= 'title="Set as default">';
        $html .= '</div>';

        // Label input
        $html .= '<div class="flex-fill">';
        $html .= '<input type="text" class="form-control form-control-sm" ';
        $html .= 'x-model="option.label" ';
        $html .= 'placeholder="Label" ';
        $html .= '@change="$wire.set(\''.$valuesModel.'\' + \'.\' + index + \'.label\', $event.target.value)">';
        $html .= '</div>';

        // Value input
        $html .= '<div class="flex-fill">';
        $html .= '<input type="text" class="form-control form-control-sm" ';
        $html .= 'x-model="option.value" ';
        $html .= 'placeholder="Value" ';
        $html .= '@change="$wire.set(\''.$valuesModel.'\' + \'.\' + index + \'.value\', $event.target.value)">';
        $html .= '</div>';

        // Delete button
        $html .= '<div class="text-center" style="width: 40px;">';
        $html .= '<button type="button" class="btn btn-sm btn-outline-danger" ';
        $html .= '@click="$wire.call(\'removeOption\', \''.$valuesModel.'\', index)" ';
        $html .= 'title="Delete">';
        $html .= '<i class="bi bi-trash"></i>';
        $html .= '</button>';
        $html .= '</div>';

        $html .= '</div>'; // d-flex
        $html .= '</template>';
        $html .= '</div>'; // mb-3

        // Add new option form
        $html .= '<div class="border-top pt-3">';
        $html .= '<div class="row g-2">';

        // New label input
        $html .= '<div class="col-md-5">';
        $html .= '<input type="text" class="form-control form-control-sm" ';
        $html .= 'x-model="newLabel" ';
        $html .= 'placeholder="New option label" ';
        $html .= '@keydown.enter.prevent="addOption()">';
        $html .= '</div>';

        // New value input
        $html .= '<div class="col-md-5">';
        $html .= '<input type="text" class="form-control form-control-sm" ';
        $html .= 'x-model="newValue" ';
        $html .= 'placeholder="New option value" ';
        $html .= '@keydown.enter.prevent="addOption()">';
        $html .= '</div>';

        // Add button
        $html .= '<div class="col-md-2">';
        $html .= '<button type="button" class="btn btn-sm btn-primary w-100" ';
        $html .= '@click="addOption()">';
        $html .= '<i class="bi bi-plus-circle me-1"></i> Add';
        $html .= '</button>';
        $html .= '</div>';

        $html .= '</div>'; // row
        $html .= '</div>'; // border-top

        $html .= '</div>'; // bg-light container

        return $html;
    }
}
