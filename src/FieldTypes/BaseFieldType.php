<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;

abstract class BaseFieldType
{
    abstract public function getName(): string;

    abstract public function getLabel(): string;

    abstract public function getIcon(): string;

    abstract public function render(CustomFormField $field, mixed $value = null): string;

    abstract public function renderBuilder(CustomFormField $field): string;

    public function validate(CustomFormField $field, mixed $value): array
    {
        $rules = $field->validation_rules ?? [];

        if ($field->is_required) {
            $rules[] = 'required';
        } else {
            // Add nullable for optional fields to prevent type validation errors on empty values
            $rules[] = 'nullable';
        }

        return $rules;
    }

    public function processValue(mixed $value): mixed
    {
        return $value;
    }

    public function getConfigSchema(): array
    {
        return [
            // Basic Information (Database Columns)
            'show_label' => [
                'type' => 'switch',
                'label' => 'Show Label',
                'tab' => 'basic',
                'target' => 'column',
                'default' => true,
                'required' => false,
            ],
            'label' => [
                'type' => 'text',
                'label' => 'Label',
                'tab' => 'basic',
                'target' => 'column',
                'required' => true,
            ],
            'label_icon' => [
                'type' => 'icon_picker',
                'label' => 'Label Icon',
                'tab' => 'basic',
                'target' => 'options',
                'required' => false,
                'help' => 'Optional icon to display before the label',
            ],
            'name' => [
                'type' => 'text',
                'label' => 'Field Name',
                'tab' => 'basic',
                'target' => 'column',
                'required' => true,
                'help' => 'Technical identifier for this field (letters, numbers, underscores)',
            ],
            'element_id' => [
                'type' => 'text',
                'label' => 'Element ID',
                'tab' => 'basic',
                'target' => 'column',
                'required' => true,
                'help' => 'Unique HTML id attribute for this field',
            ],
            'placeholder' => [
                'type' => 'text',
                'label' => 'Placeholder',
                'tab' => 'basic',
                'target' => 'column',
                'required' => false,
            ],
            'help_text_as_popover' => [
                'type' => 'switch',
                'label' => 'Help Text as Popover',
                'tab' => 'basic',
                'target' => 'column',
                'default' => false,
                'required' => false,
                'help' => 'Display help text as a "?" popover instead of below the field',
            ],
            'help_text' => [
                'type' => 'textarea',
                'label' => 'Help Text',
                'tab' => 'basic',
                'target' => 'column',
                'required' => false,
                'help' => 'Optional help text displayed below the field',
            ],
            'is_required' => [
                'type' => 'switch',
                'label' => 'Required',
                'tab' => 'validation',
                'target' => 'column',
                'default' => false,
                'required' => false,
            ],
            'class' => [
                'type' => 'text',
                'label' => 'CSS Classes',
                'tab' => 'style',
                'target' => 'column',
                'required' => false,
                'help' => 'Custom CSS classes to apply to the field wrapper',
            ],
            'style' => [
                'type' => 'textarea',
                'label' => 'Inline Styles',
                'tab' => 'style',
                'target' => 'column',
                'required' => false,
                'help' => 'Custom inline CSS styles (e.g., "color: red; font-weight: bold;")',
            ],

            // Field Options (JSON Storage)
            'floating_label' => [
                'type' => 'switch',
                'label' => 'Use Floating Label',
                'tab' => 'options',
                'target' => 'options',
                'default' => false,
                'required' => false,
                'help' => 'Modern floating label style (Bootstrap 5)',
            ],
            'field_size' => [
                'type' => 'select',
                'label' => 'Field Size',
                'tab' => 'options',
                'target' => 'options',
                'required' => false,
                'options' => [
                    '' => 'Default',
                    'sm' => 'Small',
                    'lg' => 'Large',
                ],
                'help' => 'Control the size of the input field',
            ],

            // Style Options (Custom UI in style.blade.php)
            'text_alignment' => [
                'type' => 'custom',
                'label' => 'Text Alignment',
                'tab' => 'style',
                'target' => 'options',
                'required' => false,
                'help' => 'UI rendered in custom view, stored in options',
            ],
            'spacing' => [
                'type' => 'custom',
                'label' => 'Spacing',
                'tab' => 'style',
                'target' => 'options',
                'required' => false,
                'help' => 'UI rendered in custom view, stored in options',
            ],

            // Advanced Options (Custom UI in advanced.blade.php)
            'display' => [
                'type' => 'custom',
                'label' => 'Display Utilities',
                'tab' => 'advanced',
                'target' => 'options',
                'required' => false,
                'help' => 'UI rendered in custom view, stored in options',
            ],

            // Validation Options
            'validation_timing' => [
                'type' => 'select',
                'label' => 'Validation Timing',
                'tab' => 'validation',
                'target' => 'options',
                'required' => false,
                'default' => 'live',
                'options' => [
                    'live' => 'Real-time (as you type)',
                    'blur' => 'On blur (when leaving field)',
                    'submit' => 'On submit only',
                ],
                'help' => 'When should this field be validated?',
            ],
            'custom_invalid_feedback' => [
                'type' => 'text',
                'label' => 'Custom Error Message',
                'tab' => 'validation',
                'target' => 'options',
                'required' => false,
                'help' => 'Custom message to show when validation fails',
            ],
            'custom_valid_feedback' => [
                'type' => 'text',
                'label' => 'Custom Success Message',
                'tab' => 'validation',
                'target' => 'options',
                'required' => false,
                'help' => 'Optional message to show when validation passes',
            ],
        ];
    }

    /**
     * Get available validation options for this field type
     * Returns array of validation options that can be configured
     */
    public function getAvailableValidationOptions(): array
    {
        return [];
    }

    /**
     * Get the appropriate wire:model attribute key based on validation timing
     *
     * @param  CustomFormField  $field  The field to generate wire:model for
     * @return string The wire:model attribute key (e.g., 'wire:model.live', 'wire:model.blur', 'wire:model')
     */
    protected function getWireModelAttribute(CustomFormField $field): string
    {
        $validationTiming = $field->options['validation_timing'] ?? 'live';

        return match ($validationTiming) {
            'blur' => 'wire:model.blur',
            'submit' => 'wire:model',
            default => 'wire:model.live',
        };
    }

    /**
     * Get additional wire attributes for validation (e.g., wire:blur for blur validation)
     *
     * @param  CustomFormField  $field  The field to generate validation attributes for
     * @return string Additional wire attributes to add to the input (empty string if none needed)
     */
    protected function getValidationAttributes(CustomFormField $field): string
    {
        $validationTiming = $field->options['validation_timing'] ?? 'live';

        // For blur validation, add wire:blur to trigger validation
        // Note: wire:blur automatically prefixes with $wire., so just use method name
        if ($validationTiming === 'blur') {
            return ' wire:blur="validateField(\'formData.field_'.$field->id.'\')"';
        }

        // For live validation, also add wire:blur for required fields
        // This ensures validation runs when focusing and leaving an empty required field
        if ($validationTiming === 'live' && $field->is_required) {
            return ' wire:blur="validateField(\'formData.field_'.$field->id.'\')"';
        }

        return '';
    }

    /**
     * Get property panel tabs for this field type
     *
     * Returns array of tabs to display in the properties panel.
     * Field types can override this to add custom tabs or modify existing ones.
     *
     * @return array Array of tab configurations keyed by tab key
     */
    public function getPropertyTabs(): array
    {
        return [
            'basic' => [
                'label' => 'Basic',
                'icon' => 'bi-info-circle',
                'order' => 10,
                'view' => null, // null = auto-generate from schema
            ],
            'options' => [
                'label' => 'Options',
                'icon' => 'bi-sliders',
                'order' => 20,
                'view' => null, // null = auto-generate from schema
            ],
            'validation' => [
                'label' => 'Validation',
                'icon' => 'bi-check-circle',
                'order' => 30,
                'view' => 'slick-forms::livewire.partials.properties-panel.tabs.validation',
            ],
            'style' => [
                'label' => 'Style',
                'icon' => 'bi-palette',
                'order' => 40,
                'view' => 'slick-forms::livewire.partials.properties-panel.tabs.style',
            ],
            'advanced' => [
                'label' => 'Visibility',
                'icon' => 'bi-eye',
                'order' => 50,
                'view' => 'slick-forms::livewire.partials.properties-panel.tabs.advanced',
            ],
        ];
    }

    protected function getCommonAttributes(CustomFormField $field): array
    {
        $formControlClass = 'form-control';
        $fieldSize = $field->options['field_size'] ?? '';
        if ($fieldSize === 'sm') {
            $formControlClass .= ' form-control-sm';
        } elseif ($fieldSize === 'lg') {
            $formControlClass .= ' form-control-lg';
        }

        // Add is-invalid class if validation errors exist
        $errors = view()->shared('errors') ?? session()->get('errors');
        if ($errors && $errors->has('formData.field_'.$field->id)) {
            $formControlClass .= ' is-invalid';
        }

        $attributes = [
            'id' => $field->element_id ?? 'field_'.$field->id,
            'name' => 'field_'.$field->id,
            'class' => $formControlClass,
        ];

        $useFloatingLabel = $field->options['floating_label'] ?? false;
        if ($useFloatingLabel && ! $field->placeholder) {
            $attributes['placeholder'] = $field->label;
        } elseif ($field->placeholder) {
            $attributes['placeholder'] = $field->placeholder;
        }

        if ($field->is_required) {
            $attributes['required'] = true;
        }

        return $attributes;
    }

    protected function renderAttributes(array $attributes): string
    {
        $html = [];
        foreach ($attributes as $key => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $html[] = $key;
                }
            } else {
                $html[] = sprintf('%s="%s"', $key, htmlspecialchars($value));
            }
        }

        return implode(' ', $html);
    }

    /**
     * Render label with optional help text popover
     * Always renders a red asterisk when the field is required.
     */
    protected function renderLabel(CustomFormField $field, string $forId = '', bool $includeAsterisk = false): string
    {
        $showLabel = $field->show_label ?? true;
        $hasPopover = $field->help_text && ($field->help_text_as_popover ?? false);

        // If label is hidden but we have a popover, just show the "?" icon
        if (! $showLabel && $hasPopover) {
            $html = '<div class="mb-2">';
            $html .= '<i class="bi bi-question-circle text-muted" ';
            $html .= 'style="cursor: help;" ';
            $html .= 'data-bs-toggle="popover" ';
            $html .= 'data-bs-trigger="hover focus" ';
            $html .= 'data-bs-content="'.htmlspecialchars($field->help_text).'"></i>';
            $html .= '</div>';

            return $html;
        }

        // If label is hidden and no popover, return nothing
        if (! $showLabel) {
            return '';
        }

        // Render full label with optional icon and asterisk
        $html = '<label';
        if ($forId) {
            $html .= ' for="'.$forId.'"';
        }
        $html .= ' class="form-label">';

        // Add label icon if set
        $labelIcon = $field->options['label_icon'] ?? null;
        if ($labelIcon) {
            $html .= '<i class="'.htmlspecialchars($labelIcon).' me-2"></i>';
        }

        $html .= htmlspecialchars($field->label);

        // Add required asterisk when field is required (rendered via PHP, not CSS)
        if ($field->is_required) {
            $html .= ' <span class="text-danger">*</span>';
        }

        // Add help text popover icon if enabled
        if ($hasPopover) {
            $html .= ' <i class="bi bi-question-circle text-muted" ';
            $html .= 'style="cursor: help;" ';
            $html .= 'data-bs-toggle="popover" ';
            $html .= 'data-bs-trigger="hover focus" ';
            $html .= 'data-bs-content="'.htmlspecialchars($field->help_text).'"></i>';
        }

        $html .= '</label>';

        return $html;
    }

    /**
     * Render help text below field (only if not using popover)
     */
    protected function renderHelpText(CustomFormField $field): string
    {
        if (! $field->help_text || ($field->help_text_as_popover ?? false)) {
            return '';
        }

        return '<div class="form-text">'.htmlspecialchars($field->help_text).'</div>';
    }

    /**
     * Build utility classes from field options
     * Combines spacing, display, and text alignment utilities
     */
    protected function buildUtilityClasses(CustomFormField $field): string
    {
        $classes = [];

        // Add spacing utilities
        $classes[] = $this->buildSpacingClasses($field);

        // Add display utilities
        $classes[] = $this->buildDisplayClasses($field);

        // Add text alignment utilities
        $classes[] = $this->buildTextAlignmentClasses($field);

        // Filter out empty values and join
        return implode(' ', array_filter($classes));
    }

    /**
     * Build spacing utility classes (margin and padding)
     */
    protected function buildSpacingClasses(CustomFormField $field): string
    {
        $classes = [];
        $spacing = $field->options['spacing'] ?? [];

        // Helper to extract value and handle arrays
        $getValue = function ($value) {
            if (is_array($value)) {
                return collect($value)->filter()->first();
            }

            return $value;
        };

        // Margin utilities
        if (! empty($spacing['margin'])) {
            $value = $getValue($spacing['margin']);
            if ($value) {
                $classes[] = 'm-'.$value;
            }
        }
        if (! empty($spacing['margin_top'])) {
            $value = $getValue($spacing['margin_top']);
            if ($value) {
                $classes[] = 'mt-'.$value;
            }
        }
        if (! empty($spacing['margin_bottom'])) {
            $value = $getValue($spacing['margin_bottom']);
            if ($value) {
                $classes[] = 'mb-'.$value;
            }
        }
        if (! empty($spacing['margin_start'])) {
            $value = $getValue($spacing['margin_start']);
            if ($value) {
                $classes[] = 'ms-'.$value;
            }
        }
        if (! empty($spacing['margin_end'])) {
            $value = $getValue($spacing['margin_end']);
            if ($value) {
                $classes[] = 'me-'.$value;
            }
        }
        if (! empty($spacing['margin_x'])) {
            $value = $getValue($spacing['margin_x']);
            if ($value) {
                $classes[] = 'mx-'.$value;
            }
        }
        if (! empty($spacing['margin_y'])) {
            $value = $getValue($spacing['margin_y']);
            if ($value) {
                $classes[] = 'my-'.$value;
            }
        }

        // Padding utilities
        if (! empty($spacing['padding'])) {
            $value = $getValue($spacing['padding']);
            if ($value) {
                $classes[] = 'p-'.$value;
            }
        }
        if (! empty($spacing['padding_top'])) {
            $value = $getValue($spacing['padding_top']);
            if ($value) {
                $classes[] = 'pt-'.$value;
            }
        }
        if (! empty($spacing['padding_bottom'])) {
            $value = $getValue($spacing['padding_bottom']);
            if ($value) {
                $classes[] = 'pb-'.$value;
            }
        }
        if (! empty($spacing['padding_start'])) {
            $value = $getValue($spacing['padding_start']);
            if ($value) {
                $classes[] = 'ps-'.$value;
            }
        }
        if (! empty($spacing['padding_end'])) {
            $value = $getValue($spacing['padding_end']);
            if ($value) {
                $classes[] = 'pe-'.$value;
            }
        }
        if (! empty($spacing['padding_x'])) {
            $value = $getValue($spacing['padding_x']);
            if ($value) {
                $classes[] = 'px-'.$value;
            }
        }
        if (! empty($spacing['padding_y'])) {
            $value = $getValue($spacing['padding_y']);
            if ($value) {
                $classes[] = 'py-'.$value;
            }
        }

        return implode(' ', $classes);
    }

    /**
     * Build display utility classes
     */
    protected function buildDisplayClasses(CustomFormField $field): string
    {
        $classes = [];
        $display = $field->options['display'] ?? [];

        // Responsive display utilities
        foreach (['xs', 'sm', 'md', 'lg', 'xl', 'xxl'] as $breakpoint) {
            $key = $breakpoint === 'xs' ? 'display' : 'display_'.$breakpoint;
            if (! empty($display[$key])) {
                $value = $display[$key];

                // Handle if value is stored as an array (legacy data from array_merge_recursive)
                if (is_array($value)) {
                    $value = collect($value)->filter()->first();
                }

                if ($value) {
                    $prefix = $breakpoint === 'xs' ? 'd' : 'd-'.$breakpoint;
                    $classes[] = $prefix.'-'.$value;
                }
            }
        }

        return implode(' ', $classes);
    }

    /**
     * Build text alignment utility classes
     */
    protected function buildTextAlignmentClasses(CustomFormField $field): string
    {
        $classes = [];
        $alignment = $field->options['text_alignment'] ?? [];

        // Responsive text alignment utilities
        foreach (['xs', 'sm', 'md', 'lg', 'xl', 'xxl'] as $breakpoint) {
            $key = $breakpoint === 'xs' ? 'align' : 'align_'.$breakpoint;
            if (! empty($alignment[$key])) {
                $value = $alignment[$key];

                // Handle if value is stored as an array (legacy data from array_merge_recursive)
                if (is_array($value)) {
                    // Get first non-empty value
                    $value = collect($value)->filter()->first();
                }

                if ($value) {
                    $prefix = $breakpoint === 'xs' ? 'text' : 'text-'.$breakpoint;
                    $classes[] = $prefix.'-'.$value;
                }
            }
        }

        return implode(' ', $classes);
    }

    /**
     * Merge field classes with utility classes
     */
    protected function getFieldClasses(CustomFormField $field, string $baseClasses = ''): string
    {
        $classes = [];

        // Add base classes
        if ($baseClasses) {
            $classes[] = $baseClasses;
        }

        // Add custom classes from field
        if ($field->class) {
            $classes[] = $field->class;
        }

        // Add utility classes
        $utilityClasses = $this->buildUtilityClasses($field);
        if ($utilityClasses) {
            $classes[] = $utilityClasses;
        }

        return implode(' ', array_filter($classes));
    }

    /**
     * Render custom validation feedback messages
     * Returns HTML for invalid and valid feedback divs
     */
    protected function renderValidationFeedback(CustomFormField $field): string
    {
        $html = '';

        // Invalid feedback (custom or default)
        $invalidMessage = $field->options['custom_invalid_feedback'] ?? null;
        if ($invalidMessage) {
            $html .= '<div class="invalid-feedback">'.htmlspecialchars($invalidMessage).'</div>';
        } else {
            // Default invalid feedback will be shown by Laravel validation
            $html .= '<div class="invalid-feedback">Please provide a valid value.</div>';
        }

        // Valid feedback (optional)
        $validMessage = $field->options['custom_valid_feedback'] ?? null;
        if ($validMessage) {
            $html .= '<div class="valid-feedback">'.htmlspecialchars($validMessage).'</div>';
        }

        return $html;
    }

    /**
     * Check if field should use floating label
     */
    protected function useFloatingLabel(CustomFormField $field): bool
    {
        return ($field->options['floating_label'] ?? false) === true;
    }

    /**
     * Wrap field content in floating label container
     * Usage: return $this->wrapFloatingLabel($field, $inputHtml);
     */
    protected function wrapFloatingLabel(CustomFormField $field, string $inputHtml): string
    {
        if (! $this->useFloatingLabel($field)) {
            return $inputHtml;
        }

        // Floating label structure
        $html = '<div class="form-floating">';
        $html .= $inputHtml;
        $html .= '<label for="'.($field->element_id ?? 'field_'.$field->id).'">';
        $html .= htmlspecialchars($field->label);
        if ($field->is_required) {
            $html .= ' <span class="text-danger">*</span>';
        }
        $html .= '</label>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Render required asterisk for fields where CSS can't reach the input
     * (e.g., input groups where input is nested too deep)
     */
    protected function renderRequiredAsterisk(CustomFormField $field): string
    {
        if ($field->is_required) {
            return ' <span class="text-danger">*</span>';
        }

        return '';
    }

    /**
     * Render label for floating label fields
     * Returns empty string if floating label is enabled (label is rendered inside form-floating)
     */
    protected function renderLabelWithFloating(CustomFormField $field, string $forId = ''): string
    {
        if ($this->useFloatingLabel($field)) {
            return ''; // Label will be rendered inside form-floating div
        }

        return $this->renderLabel($field, $forId);
    }

    /**
     * Get the default value for this field type
     * Override this method in field type classes that support default values
     *
     * @param  CustomFormField  $field  The field model with options
     * @return mixed The default value (null, string, array, etc.)
     */
    public function getDefaultValue(CustomFormField $field): mixed
    {
        return null;
    }

    /**
     * Render Bootstrap invalid-feedback div with custom or default message
     * Always renders the div - Bootstrap CSS will show/hide based on .is-invalid class
     */
    protected function renderInvalidFeedback(CustomFormField $field, bool $forceDisplay = false): string
    {
        // Get custom message from field options or use default
        $message = $field->options['custom_invalid_feedback'] ?? 'Please provide a valid value';

        // HTML escape to prevent XSS
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        // Use d-block for input-groups or when forced, otherwise rely on Bootstrap's CSS sibling selector
        $class = $forceDisplay ? 'invalid-feedback d-block' : 'invalid-feedback';

        return '<div class="'.$class.'">'.$message.'</div>';
    }

    /**
     * Render Bootstrap valid-feedback div if custom message is provided
     * Always renders the div if message exists - Bootstrap CSS will show/hide based on .is-valid class
     */
    protected function renderValidFeedback(CustomFormField $field, bool $forceDisplay = false): string
    {
        // Only render if custom valid feedback message is provided
        if (empty($field->options['custom_valid_feedback'])) {
            return '';
        }

        $message = $field->options['custom_valid_feedback'];

        // HTML escape to prevent XSS
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        // Use d-block for input-groups or when forced, otherwise rely on Bootstrap's CSS sibling selector
        $class = $forceDisplay ? 'valid-feedback d-block' : 'valid-feedback';

        return '<div class="'.$class.'">'.$message.'</div>';
    }

    /**
     * Get complete schema documentation for this field type
     *
     * Dynamically generates JSON schema by reading from:
     * - getConfigSchema() for properties
     * - getAvailableValidationOptions() for validation rules
     * - getPropertyTabs() for tab structure
     * - ConditionalLogicEvaluator for operators by field type
     * - CustomFormField $fillable for available columns
     *
     * @return string JSON schema documentation
     */
    public function getFullSchema(): string
    {
        // Get configuration schemas
        $baseSchema = (new \ReflectionClass(self::class))->getMethod('getConfigSchema')->invoke($this);
        $configSchema = $this->getConfigSchema();
        $mergedSchema = array_merge($baseSchema, $configSchema);

        // Get validation options
        $validationOptions = $this->getAvailableValidationOptions();

        // Get tabs
        $tabs = $this->getPropertyTabs();

        // Get operators for this field type
        $operators = $this->getAvailableOperatorsForFieldType($this->getName());

        // Build the full schema
        $schema = [
            'metadata' => [
                'type' => $this->getName(),
                'label' => $this->getLabel(),
                'icon' => $this->getIcon(),
                'description' => $this->getDescription(),
            ],
            'usage' => $this->buildUsageExample(),
            'properties' => $this->buildPropertiesFromSchema($mergedSchema),
            'validation_rules' => $this->buildValidationRules($validationOptions),
            'conditional_logic' => $this->buildConditionalLogicSchema($operators),
            'tabs' => $this->formatTabs($tabs),
        ];

        return json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Get description for this field type
     * Override in child classes to provide specific descriptions
     */
    protected function getDescription(): string
    {
        return '';
    }

    /**
     * Build usage example showing how to create this field type
     */
    protected function buildUsageExample(): array
    {
        // Build a realistic example with common options
        $example = [
            'slick_form_id' => 1,
            'field_type' => $this->getName(),
            'name' => $this->getExampleFieldName(),
            'label' => $this->getExampleLabel(),
            'placeholder' => $this->getExamplePlaceholder(),
            'help_text' => $this->getExampleHelpText(),
            'is_required' => $this->getExampleRequired(),
            'order' => 0,
        ];

        // Add validation rules if available
        $validationOptions = $this->getAvailableValidationOptions();
        if (! empty($validationOptions)) {
            $example['validation_rules'] = $this->getExampleValidationRules($validationOptions);
        }

        // Add field-specific options
        $exampleOptions = $this->getExampleOptions();
        if (! empty($exampleOptions)) {
            $example['options'] = $exampleOptions;
        }

        return [
            'model' => 'CustomFormField',
            'method' => 'create',
            'example' => $example,
        ];
    }

    /**
     * Get example field name
     * Override in child classes for field-specific names
     */
    protected function getExampleFieldName(): string
    {
        return match ($this->getName()) {
            'text' => 'first_name',
            'email' => 'email_address',
            'phone' => 'phone_number',
            'url' => 'website_url',
            'password' => 'user_password',
            'textarea' => 'description',
            'number' => 'age',
            'date' => 'birth_date',
            'time' => 'appointment_time',
            'date_range' => 'event_dates',
            'select' => 'country',
            'radio' => 'gender',
            'checkbox' => 'interests',
            'switch' => 'newsletter_opt_in',
            'file' => 'resume',
            'image' => 'profile_photo',
            'video' => 'introduction_video',
            'color_picker' => 'brand_color',
            'tags' => 'skills',
            'slider' => 'satisfaction_rating',
            'range' => 'price_range',
            'star_rating' => 'product_rating',
            'signature' => 'agreement_signature',
            'location' => 'office_location',
            default => 'example_field',
        };
    }

    /**
     * Get example label
     * Override in child classes for field-specific labels
     */
    protected function getExampleLabel(): string
    {
        return match ($this->getName()) {
            'text' => 'First Name',
            'email' => 'Email Address',
            'phone' => 'Phone Number',
            'url' => 'Website URL',
            'password' => 'Password',
            'textarea' => 'Description',
            'number' => 'Age',
            'date' => 'Date of Birth',
            'time' => 'Appointment Time',
            'date_range' => 'Event Dates',
            'select' => 'Country',
            'radio' => 'Gender',
            'checkbox' => 'Interests',
            'switch' => 'Subscribe to Newsletter',
            'file' => 'Upload Resume',
            'image' => 'Profile Photo',
            'video' => 'Introduction Video',
            'color_picker' => 'Brand Color',
            'tags' => 'Skills',
            'slider' => 'Satisfaction Rating',
            'range' => 'Price Range',
            'star_rating' => 'Rate this Product',
            'signature' => 'Sign Agreement',
            'location' => 'Office Location',
            default => 'Example Field',
        };
    }

    /**
     * Get example placeholder
     * Override in child classes for field-specific placeholders
     */
    protected function getExamplePlaceholder(): ?string
    {
        return match ($this->getName()) {
            'text' => 'Enter your first name',
            'email' => 'you@example.com',
            'phone' => '+1 (555) 123-4567',
            'url' => 'https://example.com',
            'password' => 'Enter a secure password',
            'textarea' => 'Provide a detailed description...',
            'number' => '18',
            'tags' => 'Type and press Enter',
            default => null,
        };
    }

    /**
     * Get example help text
     * Override in child classes for field-specific help text
     */
    protected function getExampleHelpText(): ?string
    {
        return match ($this->getName()) {
            'password' => 'Must be at least 8 characters with uppercase, lowercase, and numbers',
            'email' => 'We\'ll never share your email with anyone else',
            'file' => 'Accepted formats: PDF, DOC, DOCX (max 5MB)',
            'image' => 'Recommended size: 800x800px (max 2MB)',
            default => null,
        };
    }

    /**
     * Get example required status
     */
    protected function getExampleRequired(): bool
    {
        return in_array($this->getName(), ['text', 'email', 'password', 'select', 'radio']);
    }

    /**
     * Get example validation rules
     */
    protected function getExampleValidationRules(array $validationOptions): array
    {
        $rules = [];

        // Add common rules based on field type
        if ($this->getExampleRequired()) {
            $rules[] = 'required';
        }

        return match ($this->getName()) {
            'text' => ['required', 'min:2', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'min:8', 'max:255'],
            'phone' => ['required', 'regex:/^[\d\s\+\-\(\)]+$/'],
            'url' => ['required', 'url', 'max:255'],
            'textarea' => ['required', 'min:10', 'max:1000'],
            'number' => ['required', 'numeric', 'min:1', 'max:120'],
            'date' => ['required', 'date'],
            'file' => ['required', 'file', 'max:5120'],
            'image' => ['required', 'image', 'max:2048'],
            default => $rules,
        };
    }

    /**
     * Get example options for this field type
     * Override in child classes for field-specific options
     */
    protected function getExampleOptions(): array
    {
        $options = [];

        // Add common options
        $schema = $this->getConfigSchema();

        // Add floating label if supported
        if (isset($schema['floating_label'])) {
            $options['floating_label'] = true;
        }

        // Add field size if supported
        if (isset($schema['field_size'])) {
            $options['field_size'] = 'lg';
        }

        // Add field-specific options
        $options = array_merge($options, match ($this->getName()) {
            'select' => [
                'values' => [
                    ['label' => 'United States', 'value' => 'us'],
                    ['label' => 'Canada', 'value' => 'ca'],
                    ['label' => 'United Kingdom', 'value' => 'uk'],
                    ['label' => 'Australia', 'value' => 'au'],
                ],
                'placeholder' => 'Choose a country...',
            ],
            'radio' => [
                'values' => [
                    ['label' => 'Male', 'value' => 'male'],
                    ['label' => 'Female', 'value' => 'female'],
                    ['label' => 'Other', 'value' => 'other'],
                    ['label' => 'Prefer not to say', 'value' => 'not_specified'],
                ],
                'inline' => false,
            ],
            'checkbox' => [
                'values' => [
                    ['label' => 'Web Development', 'value' => 'web_dev'],
                    ['label' => 'Mobile Development', 'value' => 'mobile_dev'],
                    ['label' => 'UI/UX Design', 'value' => 'design'],
                    ['label' => 'Data Science', 'value' => 'data_science'],
                ],
                'inline' => false,
            ],
            'slider' => [
                'min' => 0,
                'max' => 100,
                'step' => 1,
                'show_value' => true,
            ],
            'range' => [
                'min' => 0,
                'max' => 1000,
                'step' => 10,
                'show_values' => true,
            ],
            'star_rating' => [
                'max_stars' => 5,
                'show_labels' => true,
            ],
            'number' => [
                'min' => 1,
                'max' => 120,
                'step' => 1,
            ],
            'textarea' => [
                'rows' => 4,
                'max_length' => 1000,
                'show_character_count' => true,
            ],
            'password' => [
                'show_toggle' => true,
                'show_strength_indicator' => true,
            ],
            'date' => [
                'format' => 'Y-m-d',
                'min_date' => '1900-01-01',
                'max_date' => null,
            ],
            'tags' => [
                'max_tags' => 10,
                'allow_duplicates' => false,
            ],
            default => [],
        });

        return array_filter($options);
    }

    /**
     * Build properties documentation from config schema
     *
     * @param  array  $schema  Merged config schema from parent and child
     * @return array Properties documentation
     */
    protected function buildPropertiesFromSchema(array $schema): array
    {
        $properties = [];

        // Add model fillable columns first
        $model = new CustomFormField;
        foreach ($model->getFillable() as $column) {
            // Skip if already in schema (will be added below with more detail)
            if (isset($schema[$column])) {
                continue;
            }

            // Add basic documentation for fillable columns not in schema
            $properties[$column] = $this->getColumnDocumentation($column);
        }

        // Add schema-defined properties
        foreach ($schema as $key => $config) {
            $property = [
                'type' => $config['type'] ?? 'string',
                'required' => $config['required'] ?? false,
                'storage' => $this->determineStorage($config['target'] ?? 'options'),
                'default' => $config['default'] ?? null,
                'description' => $config['help'] ?? $config['label'] ?? '',
            ];

            // Extract options if defined
            if (isset($config['options']) && is_array($config['options'])) {
                $property['options'] = array_values($config['options']);
                $property['options_labels'] = $config['options'];
            }

            // Extract validation patterns
            if (isset($config['pattern'])) {
                $property['pattern'] = $config['pattern'];
            }

            // Add tab information
            if (isset($config['tab'])) {
                $property['tab'] = $config['tab'];
            }

            $properties[$key] = $property;
        }

        return $properties;
    }

    /**
     * Get documentation for a database column
     */
    protected function getColumnDocumentation(string $column): array
    {
        $docs = [
            'slick_form_id' => [
                'type' => 'integer',
                'required' => true,
                'storage' => 'column',
                'description' => 'The form this field belongs to',
            ],
            'slick_form_page_id' => [
                'type' => 'integer',
                'required' => false,
                'storage' => 'column',
                'description' => 'The page this field belongs to (for multi-page forms)',
            ],
            'slick_form_layout_element_id' => [
                'type' => 'integer',
                'required' => false,
                'storage' => 'column',
                'description' => 'The layout element this field belongs to',
            ],
            'parent_field_id' => [
                'type' => 'integer',
                'required' => false,
                'storage' => 'column',
                'description' => 'Parent field ID (for nested fields like repeater children)',
            ],
            'field_type' => [
                'type' => 'string',
                'required' => true,
                'storage' => 'column',
                'value' => $this->getName(),
                'description' => "Must be '{$this->getName()}' for this field type",
            ],
            'order' => [
                'type' => 'integer',
                'required' => true,
                'storage' => 'column',
                'description' => 'Display order among siblings',
            ],
        ];

        return $docs[$column] ?? [
            'type' => 'mixed',
            'storage' => 'column',
            'description' => '',
        ];
    }

    /**
     * Determine storage location from schema target
     */
    protected function determineStorage(string $target): string
    {
        return match ($target) {
            'column' => 'column',
            'options' => 'json_column',
            'validation_rules' => 'json_column',
            'conditional_logic' => 'json_column',
            'settings' => 'json_column',
            default => 'json_column',
        };
    }

    /**
     * Build validation rules documentation
     */
    protected function buildValidationRules(array $validationOptions): array
    {
        if (empty($validationOptions)) {
            return [
                'type' => 'array',
                'required' => false,
                'storage' => 'json_column',
                'description' => 'Laravel validation rules',
                'example' => ['required', 'string'],
                'available_rules' => [],
            ];
        }

        $rules = [];
        foreach ($validationOptions as $ruleName => $option) {
            $rules[$ruleName] = [
                'format' => $option['rule_format'] ?? $ruleName,
                'description' => $option['label'] ?? $ruleName,
                'help' => $option['help'] ?? '',
                'value_type' => $option['type'] ?? 'string',
                'placeholder' => $option['placeholder'] ?? null,
            ];
        }

        return [
            'type' => 'array',
            'required' => false,
            'storage' => 'json_column',
            'description' => 'Laravel validation rules',
            'example' => array_keys($rules),
            'available_rules' => $rules,
        ];
    }

    /**
     * Build conditional logic schema documentation
     */
    protected function buildConditionalLogicSchema(array $operators): array
    {
        return [
            'type' => 'object',
            'required' => false,
            'storage' => 'json_column',
            'description' => 'Visibility and conditional validation rules',
            'structure' => [
                'action' => [
                    'type' => 'string',
                    'required' => true,
                    'options' => ['show', 'hide'],
                    'description' => 'Whether to show or hide when conditions are met',
                ],
                'match' => [
                    'type' => 'string',
                    'required' => true,
                    'options' => ['all', 'any'],
                    'description' => 'all = AND logic (all conditions must be met), any = OR logic (any condition can be met)',
                ],
                'conditions' => [
                    'type' => 'array',
                    'description' => 'Array of condition objects',
                    'structure' => [
                        'target_field_id' => [
                            'type' => 'integer',
                            'description' => 'Database ID of the field to check',
                        ],
                        'operator' => [
                            'type' => 'string',
                            'description' => 'Comparison operator for this field type',
                            'options' => $operators,
                        ],
                        'value' => [
                            'type' => 'mixed',
                            'description' => 'Value to compare against (type depends on operator)',
                        ],
                    ],
                ],
            ],
            'example' => [
                'action' => 'show',
                'match' => 'all',
                'conditions' => [
                    [
                        'target_field_id' => 123,
                        'operator' => $operators[0] ?? 'equals',
                        'value' => 'example_value',
                    ],
                ],
            ],
        ];
    }

    /**
     * Format tabs documentation
     */
    protected function formatTabs(array $tabs): array
    {
        $formatted = [];

        foreach ($tabs as $key => $config) {
            // Skip disabled tabs (false values)
            if ($config === false) {
                continue;
            }

            // Handle array config
            if (is_array($config)) {
                $formatted[$key] = [
                    'label' => $config['label'] ?? $key,
                    'icon' => $config['icon'] ?? null,
                    'order' => $config['order'] ?? 0,
                    'view' => $config['view'] ?? 'auto-generated from schema',
                ];
            }
        }

        return $formatted;
    }

    /**
     * Get available operators for this field type
     * Queries ConditionalLogicEvaluator service
     */
    protected function getAvailableOperatorsForFieldType(string $fieldType): array
    {
        $evaluator = app(\DigitalisStudios\SlickForms\Services\ConditionalLogicEvaluator::class);

        return $evaluator->getOperatorsForFieldType($fieldType);
    }
}
