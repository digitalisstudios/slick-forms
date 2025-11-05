<?php

namespace DigitalisStudios\SlickForms\Livewire\Concerns;

use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;
use DigitalisStudios\SlickForms\Services\FieldTypeRegistry;
use DigitalisStudios\SlickForms\Services\LayoutElementRegistry;

/**
 * ManagesSchemaProperties Trait
 *
 * Provides schema-driven property management for FormBuilder.
 * Replaces 50+ individual Livewire properties with dynamic $properties array.
 *
 * Usage:
 * 1. Add to FormBuilder: use ManagesSchemaProperties;
 * 2. Replace individual properties with: public array $properties = [];
 * 3. Replace editField() with: editFieldV3()
 * 4. Replace saveField() with: saveFieldV3()
 */
trait ManagesSchemaProperties
{
    /**
     * Dynamic properties array for fields
     * Replaces: $fieldLabel, $fieldName, $fieldPlaceholder, etc.
     */
    public array $properties = [];

    /**
     * Dynamic properties array for layout elements
     * Replaces: $elementElementId, $elementColumnWidths, etc.
     */
    public array $elementProperties = [];

    /**
     * Load field properties from schema (V3 Refactored Version)
     *
     * This method replaces the hardcoded editField() method.
     * It dynamically loads properties based on the field type's schema.
     */
    public function editFieldV3(int $fieldId): void
    {
        $this->selectedField = CustomFormField::findOrFail($fieldId);
        $this->showFieldEditor = true;
        $this->anyPanelOpen = true;
        $this->showElementEditor = false;
        $this->showFormEditor = false;
        $this->activePropertiesTab = 'basic';

        // Get field type and schema
        $registry = app(FieldTypeRegistry::class);
        $fieldType = $registry->get($this->selectedField->field_type);
        $schema = $fieldType->getConfigSchema();

        // Initialize properties array from schema
        $this->properties = [];

        foreach ($schema as $key => $config) {
            $target = $config['target'] ?? 'options';
            $default = $config['default'] ?? null;
            $type = $config['type'] ?? 'text';

            // Load value based on target
            if ($target === 'column') {
                // Load from database column
                $this->properties[$key] = $this->selectedField->{$key} ?? $default;
            } elseif ($target === 'options') {
                // Load from options JSON (flat key)
                $value = $this->selectedField->options[$key] ?? $default;

                // For custom type fields with nested wire:model bindings, ensure array structure
                if ($type === 'custom' && $value === null) {
                    $value = [];
                }

                // For options type fields, ensure array structure
                if ($type === 'options' && ($value === null || ! is_array($value))) {
                    $value = $default ?? [];
                }

                $this->properties[$key] = $value;
            } elseif (str_starts_with($target, 'options.')) {
                // Load from nested options path (e.g., 'options.mask.enabled')
                $path = substr($target, 8); // Remove 'options.' prefix
                $value = $this->selectedField->options ?? [];
                foreach (explode('.', $path) as $k) {
                    $value = $value[$k] ?? null;
                    if ($value === null) {
                        $value = $default;
                        break;
                    }
                }
                $this->properties[$key] = $value;
            } elseif ($target === 'validation_rules') {
                // Load from validation_rules column
                $this->properties[$key] = $this->selectedField->validation_rules ?? [];
            } elseif ($target === 'conditional_logic') {
                // Load from conditional_logic column
                $this->properties[$key] = $this->selectedField->conditional_logic ?? [];
            } else {
                // Unknown target, try options
                $this->properties[$key] = $this->selectedField->options[$key] ?? $default;
            }
        }

        // Load special properties (not schema-driven due to complexity)
        $this->loadSpecialFieldProperties();

        // Emit event for any JS hooks
        $this->dispatch('field-editor-opened', fieldId: $fieldId);
    }

    /**
     * Save field properties from schema (V3 Refactored Version)
     *
     * This method replaces the hardcoded saveField() method.
     * It dynamically saves properties based on the field type's schema target specification.
     */
    public function saveFieldV3(): void
    {
        if (! $this->selectedField) {
            $this->errorMessage = 'No field selected.';

            return;
        }

        // Get field type and schema
        $registry = app(FieldTypeRegistry::class);
        $fieldType = $registry->get($this->selectedField->field_type);
        $schema = $fieldType->getConfigSchema();

        // Prepare data structures
        $columnUpdates = [];
        $optionsUpdates = [];
        $validationRules = [];
        $conditionalLogic = [];

        // Map properties back to model based on schema
        foreach ($schema as $key => $config) {
            $type = $config['type'] ?? 'text';

            // Skip display-only fields (don't save to database)
            if (in_array($type, ['html', 'divider', 'heading'])) {
                continue;
            }

            $target = $config['target'] ?? 'options';
            $value = $this->properties[$key] ?? ($config['default'] ?? null);

            if ($target === 'column') {
                // Save to database column
                $columnUpdates[$key] = $value;
            } elseif ($target === 'options') {
                // Save to options JSON (flat key)
                $optionsUpdates[$key] = $value;
            } elseif (str_starts_with($target, 'options.')) {
                // Save to nested options path (e.g., 'options.mask.enabled')
                $path = substr($target, 8); // Remove 'options.' prefix
                $keys = explode('.', $path);
                $this->setNestedValue($optionsUpdates, $keys, $value);
            } elseif ($target === 'validation_rules') {
                // Save to validation_rules column
                $validationRules = $value;
            } elseif ($target === 'conditional_logic') {
                // Save to conditional_logic column
                $conditionalLogic = $value;
            }
        }

        // Apply column updates
        foreach ($columnUpdates as $column => $value) {
            $this->selectedField->{$column} = $value;
        }

        // Apply options updates (replace to avoid array conversion on duplicate keys)
        $currentOptions = $this->selectedField->options ?? [];
        $this->selectedField->options = array_replace_recursive($currentOptions, $optionsUpdates);

        // Apply validation rules
        if (! empty($validationRules)) {
            $this->selectedField->validation_rules = $validationRules;
        }

        // Apply conditional logic
        if (! empty($conditionalLogic)) {
            $this->selectedField->conditional_logic = $conditionalLogic;
        }

        // Save special properties (not schema-driven)
        $this->saveSpecialFieldProperties();

        // Validate element_id uniqueness
        if (isset($columnUpdates['element_id']) && $columnUpdates['element_id']) {
            $elementId = $columnUpdates['element_id'];

            // Check if element_id is used by another field
            $existingField = \DigitalisStudios\SlickForms\Models\CustomFormField::where('slick_form_id', $this->selectedField->slick_form_id)
                ->where('element_id', $elementId)
                ->where('id', '!=', $this->selectedField->id)
                ->first();

            if ($existingField) {
                $this->errorMessage = "Element ID '{$elementId}' is already used by another field.";

                return;
            }

            // Check if element_id is used by a layout element
            $existingElement = \DigitalisStudios\SlickForms\Models\SlickFormLayoutElement::where('slick_form_id', $this->selectedField->slick_form_id)
                ->where('element_id', $elementId)
                ->first();

            if ($existingElement) {
                $this->errorMessage = "Element ID '{$elementId}' is already used by a layout element.";

                return;
            }
        }

        // Save the model
        try {
            $this->selectedField->save();

            // Refresh structure
            $this->refreshFormStructure();

            // Keep editor open and field selected after save
            // $this->showFieldEditor = false;
            // $this->selectedField = null;
            // $this->properties = [];

            // Emit success event
            $this->dispatch('field-saved');
        } catch (\Exception $e) {
            $this->errorMessage = 'Error saving field: '.$e->getMessage();
        }
    }

    /**
     * Load element properties from schema (V3 Refactored Version)
     *
     * This method replaces the hardcoded editElement() method.
     */
    public function editElementV3(int $elementId): void
    {
        $this->selectedElement = SlickFormLayoutElement::findOrFail($elementId);
        $this->showElementEditor = true;
        $this->showFieldEditor = false;
        $this->showFormEditor = false;
        $this->activePropertiesTab = 'basic';

        // Get element type and schema
        $registry = app(LayoutElementRegistry::class);

        if (! $registry->has($this->selectedElement->element_type)) {
            $this->errorMessage = 'Unknown element type: '.$this->selectedElement->element_type;

            return;
        }

        $elementType = $registry->get($this->selectedElement->element_type);
        $schema = $elementType->getConfigSchema();

        // Initialize element properties array from schema
        $this->elementProperties = [];

        foreach ($schema as $key => $config) {
            $target = $config['target'] ?? 'settings';
            $default = $config['default'] ?? null;

            // Handle nested keys (e.g., 'width.xs', 'spacing.margin_top')
            $keys = explode('.', $key);

            if ($target === 'column') {
                // Load from database column
                $this->setNestedValue($this->elementProperties, $keys, $this->selectedElement->{$key} ?? $default);
            } elseif ($target === 'settings') {
                // Load from settings JSON (nested) - use key path
                $value = $this->selectedElement->settings ?? [];
                foreach ($keys as $k) {
                    $value = $value[$k] ?? null;
                    if ($value === null) {
                        $value = $default;
                        break;
                    }
                }
                // Store in nested structure to match wire:model dotted notation
                $this->setNestedValue($this->elementProperties, $keys, $value);
            } elseif (str_starts_with($target, 'settings.')) {
                // Load from nested settings path (e.g., 'settings.navigation.enabled')
                $path = substr($target, 9); // Remove 'settings.' prefix
                $targetKeys = explode('.', $path);
                $value = $this->selectedElement->settings ?? [];
                foreach ($targetKeys as $k) {
                    $value = $value[$k] ?? null;
                    if ($value === null) {
                        $value = $default;
                        break;
                    }
                }
                // Store using schema key (not target path) to match wire:model
                $this->setNestedValue($this->elementProperties, $keys, $value);
            } else {
                // Unknown target, try settings using key path
                $value = $this->selectedElement->settings ?? [];
                foreach ($keys as $k) {
                    $value = $value[$k] ?? null;
                    if ($value === null) {
                        $value = $default;
                        break;
                    }
                }
                // Store in nested structure to match wire:model dotted notation
                $this->setNestedValue($this->elementProperties, $keys, $value);
            }
        }

        // Emit event
        $this->dispatch('element-editor-opened', elementId: $elementId);
    }

    /**
     * Save element properties from schema (V3 Refactored Version)
     *
     * This method replaces the hardcoded saveElement() method.
     */
    public function saveElementV3(): void
    {
        if (! $this->selectedElement) {
            $this->errorMessage = 'No element selected.';

            return;
        }

        // Get element type and schema
        $registry = app(LayoutElementRegistry::class);

        if (! $registry->has($this->selectedElement->element_type)) {
            $this->errorMessage = 'Unknown element type: '.$this->selectedElement->element_type;

            return;
        }

        $elementType = $registry->get($this->selectedElement->element_type);
        $schema = $elementType->getConfigSchema();

        // Prepare data structures
        $columnUpdates = [];
        $settingsUpdates = [];

        // Map properties back to model based on schema
        foreach ($schema as $key => $config) {
            $type = $config['type'] ?? 'text';

            // Skip display-only fields (don't save to database)
            if (in_array($type, ['html', 'divider', 'heading'])) {
                continue;
            }

            $target = $config['target'] ?? 'settings';

            // Handle nested keys (e.g., 'width.xs', 'spacing.margin_top')
            $keys = explode('.', $key);

            // Get value from nested elementProperties structure (using schema key)
            $value = $this->elementProperties;
            foreach ($keys as $k) {
                $value = $value[$k] ?? null;
                if ($value === null) {
                    $value = $config['default'] ?? null;
                    break;
                }
            }

            if ($target === 'column') {
                // Save to database column
                $columnUpdates[$key] = $value;
            } elseif ($target === 'settings') {
                // Save to settings JSON (nested) using schema key as path
                $this->setNestedValue($settingsUpdates, $keys, $value);
            } elseif (str_starts_with($target, 'settings.')) {
                // Save to nested settings path (e.g., 'settings.navigation.enabled')
                $path = substr($target, 9); // Remove 'settings.' prefix
                $targetKeys = explode('.', $path);
                $this->setNestedValue($settingsUpdates, $targetKeys, $value);
            }
        }

        // Apply column updates
        foreach ($columnUpdates as $column => $value) {
            $this->selectedElement->{$column} = $value;
        }

        // Apply settings updates
        $currentSettings = $this->selectedElement->settings ?? [];
        $this->selectedElement->settings = array_replace_recursive($currentSettings, $settingsUpdates);

        // Validate element_id uniqueness
        if (isset($columnUpdates['element_id']) && $columnUpdates['element_id']) {
            $elementId = $columnUpdates['element_id'];

            // Check if element_id is used by another layout element
            $existingElement = \DigitalisStudios\SlickForms\Models\SlickFormLayoutElement::where('slick_form_id', $this->selectedElement->slick_form_id)
                ->where('element_id', $elementId)
                ->where('id', '!=', $this->selectedElement->id)
                ->first();

            if ($existingElement) {
                $this->errorMessage = "Element ID '{$elementId}' is already used by another layout element.";

                return;
            }

            // Check if element_id is used by a field
            $existingField = \DigitalisStudios\SlickForms\Models\CustomFormField::where('slick_form_id', $this->selectedElement->slick_form_id)
                ->where('element_id', $elementId)
                ->first();

            if ($existingField) {
                $this->errorMessage = "Element ID '{$elementId}' is already used by a field.";

                return;
            }
        }

        // Save the model
        try {
            $this->selectedElement->save();

            // Refresh structure
            $this->refreshFormStructure();

            // Keep editor open and element selected after save
            // $this->showElementEditor = false;
            // $this->selectedElement = null;
            // $this->elementProperties = [];

            // Emit success event
            $this->dispatch('element-saved');
        } catch (\Exception $e) {
            $this->errorMessage = 'Error saving element: '.$e->getMessage();
        }
    }

    /**
     * Helper: Set nested array value from dot notation
     *
     * @param  mixed  $value
     */
    protected function setNestedValue(array &$array, array $keys, $value): void
    {
        $current = &$array;

        foreach ($keys as $i => $key) {
            if ($i === count($keys) - 1) {
                // Last key, set value
                $current[$key] = $value;
            } else {
                // Intermediate key, ensure array exists
                if (! isset($current[$key]) || ! is_array($current[$key])) {
                    $current[$key] = [];
                }
                $current = &$current[$key];
            }
        }
    }

    /**
     * Helper: Refresh form structure
     * (Assumes FormBuilder has this method)
     */
    protected function refreshFormStructure(): void
    {
        if (method_exists($this, 'loadFormStructure')) {
            $this->loadFormStructure();
        }
    }

    /**
     * Load special field properties that are not schema-driven
     * Handles: is_required, validation_rules, conditional_logic
     */
    protected function loadSpecialFieldProperties(): void
    {
        if (! $this->selectedField) {
            return;
        }

        // Load is_required
        if (property_exists($this, 'fieldIsRequired')) {
            $this->fieldIsRequired = $this->selectedField->is_required ?? false;
        }

        // Load validation rules and parse into fieldValidationOptions
        if (property_exists($this, 'fieldValidationOptions')) {
            $this->fieldValidationOptions = $this->parseValidationRules($this->selectedField->validation_rules ?? []);
        }

        // Load conditional logic
        if (property_exists($this, 'fieldConditionalLogic')) {
            $this->fieldConditionalLogic = $this->selectedField->conditional_logic ?? [];
        }
    }

    /**
     * Save special field properties that are not schema-driven
     * Handles: is_required, validation_rules, conditional_logic
     */
    protected function saveSpecialFieldProperties(): void
    {
        if (! $this->selectedField) {
            return;
        }

        // Save is_required
        if (property_exists($this, 'fieldIsRequired')) {
            $this->selectedField->is_required = $this->fieldIsRequired;
        }

        // Build and save validation rules from fieldValidationOptions
        if (property_exists($this, 'fieldValidationOptions')) {
            $rules = $this->buildValidationRules();
            if (! empty($rules)) {
                $this->selectedField->validation_rules = $rules;
            }
        }

        // Save conditional logic
        if (property_exists($this, 'fieldConditionalLogic')) {
            if (! empty($this->fieldConditionalLogic)) {
                $this->selectedField->conditional_logic = $this->fieldConditionalLogic;
            }
        }
    }

    /**
     * Parse validation_rules array into fieldValidationOptions format
     * Converts: ['max:255', 'min:3'] → ['max' => 255, 'min' => 3]
     */
    protected function parseValidationRules(array $rules): array
    {
        $options = [];

        foreach ($rules as $rule) {
            if (is_string($rule) && str_contains($rule, ':')) {
                [$key, $value] = explode(':', $rule, 2);
                $options[$key] = $value;
            }
        }

        return $options;
    }

    /**
     * Build validation_rules array from fieldValidationOptions
     * Converts: ['max' => 255, 'min' => 3] → ['max:255', 'min:3']
     */
    protected function buildValidationRules(): array
    {
        $rules = [];

        foreach ($this->fieldValidationOptions as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            // Handle boolean values (checkboxes)
            if (is_bool($value)) {
                if ($value) {
                    $rules[] = $key;
                }
            } else {
                $rules[] = $key.':'.$value;
            }
        }

        return $rules;
    }
}
