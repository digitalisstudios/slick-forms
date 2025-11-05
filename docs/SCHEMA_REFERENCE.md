# Schema Reference - Complete Guide

**Status:** Complete Reference for Slick Forms v2.1+
**Last Updated:** 2025-10-27

---

## Table of Contents

1. [Overview](#overview)
2. [Schema Field Types](#schema-field-types)
3. [Common Schema Properties](#common-schema-properties)
4. [Target Metadata](#target-metadata)
5. [Tab Configuration](#tab-configuration)
6. [Advanced Features](#advanced-features)
7. [Complete Examples](#complete-examples)

---

## Overview

The schema system in Slick Forms allows you to define property panel configurations declaratively. Instead of creating manual Blade templates, you define a simple array that describes your fields, and the `SchemaRenderer` service automatically generates the UI.

### Basic Schema Structure

```php
public function getConfigSchema(): array
{
    return array_merge(parent::getConfigSchema(), [
        'field_key' => [
            'type' => 'text',           // Field type (required)
            'label' => 'Field Label',   // Display label (required)
            'tab' => 'options',         // Which tab to show on
            'target' => 'options',      // Where to save value
            'default' => '',            // Default value
            'placeholder' => 'Enter...', // Placeholder text
            'help' => 'Help text here', // Help tooltip
            'required' => false,        // Is field required
        ],
    ]);
}
```

---

## Schema Field Types

### Text Input

Simple single-line text input.

```php
'field_name' => [
    'type' => 'text',
    'label' => 'Field Name',
    'tab' => 'basic',
    'target' => 'column',
    'default' => '',
    'placeholder' => 'Enter field name...',
    'help' => 'Technical identifier for this field',
    'required' => true,
]
```

**Supported Properties:**
- `placeholder` (string) - Placeholder text
- `maxlength` (int) - Maximum character length
- `pattern` (string) - HTML5 pattern validation
- `required` (bool) - Mark as required field

**Rendered As:**
```html
<input type="text" class="form-control" wire:model="properties.field_name" placeholder="Enter field name..." />
```

---

### Textarea

Multi-line text input.

```php
'description' => [
    'type' => 'textarea',
    'label' => 'Description',
    'tab' => 'basic',
    'target' => 'column',
    'default' => '',
    'placeholder' => 'Enter description...',
    'rows' => 3,
    'help' => 'Optional field description',
]
```

**Supported Properties:**
- `rows` (int) - Number of visible rows (default: 3)
- `placeholder` (string) - Placeholder text
- `maxlength` (int) - Maximum character length
- `required` (bool) - Mark as required field

**Rendered As:**
```html
<textarea class="form-control" wire:model="properties.description" rows="3"></textarea>
```

---

### Number Input

Numeric input with optional min/max constraints.

```php
'max_length' => [
    'type' => 'number',
    'label' => 'Maximum Length',
    'tab' => 'validation',
    'target' => 'options',
    'default' => 255,
    'min' => 1,
    'max' => 10000,
    'step' => 1,
    'help' => 'Maximum characters allowed',
]
```

**Supported Properties:**
- `min` (int|float) - Minimum value
- `max` (int|float) - Maximum value
- `step` (int|float) - Increment step (default: 1)
- `placeholder` (string) - Placeholder text
- `required` (bool) - Mark as required field

**Rendered As:**
```html
<input type="number" class="form-control" wire:model="properties.max_length" min="1" max="10000" step="1" />
```

---

### Select Dropdown

Single-select dropdown with predefined options.

```php
'field_size' => [
    'type' => 'select',
    'label' => 'Field Size',
    'tab' => 'options',
    'target' => 'options',
    'default' => '',
    'options' => [
        '' => 'Default',
        'sm' => 'Small',
        'lg' => 'Large',
    ],
    'help' => 'Bootstrap form control size',
]
```

**Supported Properties:**
- `options` (array) - Key-value pairs for dropdown options (required)
- `placeholder` (string) - Empty option text
- `required` (bool) - Mark as required field

**Options Format:**
```php
'options' => [
    'value1' => 'Label 1',
    'value2' => 'Label 2',
    'value3' => 'Label 3',
]
```

**Rendered As:**
```html
<select class="form-select" wire:model="properties.field_size">
    <option value="">Default</option>
    <option value="sm">Small</option>
    <option value="lg">Large</option>
</select>
```

---

### Switch (Toggle)

Boolean toggle switch using Bootstrap's form-switch.

```php
'show_label' => [
    'type' => 'switch',
    'label' => 'Show Label',
    'tab' => 'basic',
    'target' => 'column',
    'default' => true,
    'help' => 'Display field label above input',
]
```

**Supported Properties:**
- `default` (bool) - Default state (true/false)
- `help` (string) - Help text

**Rendered As:**
```html
<div class="form-check form-switch">
    <input type="checkbox" class="form-check-input" wire:model="properties.show_label" />
    <label class="form-check-label">Show Label</label>
</div>
```

**Value:** Returns boolean `true` or `false`

---

### Color Picker

HTML5 color picker input.

```php
'background_color' => [
    'type' => 'color',
    'label' => 'Background Color',
    'tab' => 'style',
    'target' => 'options',
    'default' => '#ffffff',
    'help' => 'Choose background color',
]
```

**Supported Properties:**
- `default` (string) - Default color hex code (e.g., '#ffffff')

**Rendered As:**
```html
<input type="color" class="form-control form-control-color" wire:model="properties.background_color" />
```

**Value:** Returns hex color string (e.g., '#ff5733')

---

### Date Picker

HTML5 date picker input.

```php
'effective_date' => [
    'type' => 'date',
    'label' => 'Effective Date',
    'tab' => 'advanced',
    'target' => 'options',
    'default' => '',
    'min' => '2024-01-01',
    'max' => '2025-12-31',
    'help' => 'Date when this takes effect',
]
```

**Supported Properties:**
- `min` (string) - Minimum date (YYYY-MM-DD format)
- `max` (string) - Maximum date (YYYY-MM-DD format)
- `default` (string) - Default date (YYYY-MM-DD format)

**Rendered As:**
```html
<input type="date" class="form-control" wire:model="properties.effective_date" min="2024-01-01" max="2025-12-31" />
```

**Value:** Returns date string in YYYY-MM-DD format

---

### Repeater (Advanced)

Multi-row repeatable fields (e.g., for select options, table rows).

```php
'values' => [
    'type' => 'repeater',
    'label' => 'Options',
    'tab' => 'options',
    'target' => 'options',
    'help' => 'Define the available options',
    'view' => 'slick-forms::livewire.partials.properties-panel.field-options.select-radio-checkbox',
]
```

**Note:** Repeater fields currently require a custom `view` property pointing to a Blade template. Auto-generation for repeaters is a future enhancement.

**Supported Properties:**
- `view` (string) - Path to custom Blade template (required for now)
- `help` (string) - Help text

**Custom View:** Must implement Livewire wire:model bindings for array data.

---

### Divider

Visual separator between form sections (renders horizontal line).

```php
'divider_1' => [
    'type' => 'divider',
    'tab' => 'options',
]
```

**Supported Properties:**
- None (purely visual)

**Rendered As:**
```html
<hr class="my-3" />
```

**Use Case:** Group related fields within a tab.

---

### Heading

Section heading within a tab (renders bold text label).

```php
'heading_appearance' => [
    'type' => 'heading',
    'label' => 'Appearance Settings',
    'tab' => 'style',
]
```

**Supported Properties:**
- `label` (string) - Heading text (required)

**Rendered As:**
```html
<h6 class="fw-bold mb-2 mt-3">Appearance Settings</h6>
```

**Use Case:** Organize complex tabs with multiple sections.

---

## Common Schema Properties

These properties are supported across all field types (unless noted otherwise).

### Required Properties

#### `type` (string)
The field type to render. Must be one of:
- `text`, `textarea`, `number`, `select`, `switch`, `color`, `date`, `repeater`, `divider`, `heading`

**Required:** Yes

---

#### `label` (string)
Display label shown above the field.

**Required:** Yes (except for `divider`)

**Example:**
```php
'label' => 'Field Name'
```

---

### Optional Common Properties

#### `tab` (string)
Which property panel tab this field appears on.

**Default:** `'basic'`

**Common Values:**
- `'basic'` - Basic settings
- `'options'` - Field-specific options
- `'validation'` - Validation rules
- `'style'` - Styling options
- `'advanced'` - Advanced settings
- Custom tab names (if registered via `getPropertyTabs()`)

**Example:**
```php
'tab' => 'options'
```

---

#### `target` (string)
Where to save the field value in the database.

**Default:** `'options'`

**Allowed Values:**
- `'column'` - Save to database column (e.g., `label`, `name`, `placeholder`)
- `'options'` - Save to `options` JSON column (for field-specific settings)
- `'settings'` - Save to `settings` JSON column (for layout element settings)
- `'validation_rules'` - Save to `validation_rules` column
- `'conditional_logic'` - Save to `conditional_logic` column

**Example:**
```php
'target' => 'column'
```

**See:** [Target Metadata](#target-metadata) section for details.

---

#### `default` (mixed)
Default value when creating new field/element or when value is empty.

**Type:** Depends on field type
- `text`, `textarea`, `select`, `color`, `date`: string
- `number`: int or float
- `switch`: bool

**Example:**
```php
'default' => true  // For switch
'default' => ''    // For text
'default' => 255   // For number
```

---

#### `placeholder` (string)
Placeholder text shown in empty input fields.

**Applicable To:** `text`, `textarea`, `number`, `select`

**Example:**
```php
'placeholder' => 'Enter field name...'
```

---

#### `help` (string)
Help text shown below the field (or as tooltip).

**Example:**
```php
'help' => 'Technical identifier used in form submissions'
```

---

#### `required` (bool)
Mark field as required (adds asterisk to label).

**Default:** `false`

**Note:** Only affects UI indication, not validation. Implement validation separately.

**Example:**
```php
'required' => true
```

---

### Field-Type-Specific Properties

#### For `textarea`

##### `rows` (int)
Number of visible text rows.

**Default:** 3

**Example:**
```php
'rows' => 5
```

---

#### For `number`

##### `min` (int|float)
Minimum allowed value.

**Example:**
```php
'min' => 0
```

##### `max` (int|float)
Maximum allowed value.

**Example:**
```php
'max' => 100
```

##### `step` (int|float)
Increment/decrement step value.

**Default:** 1

**Example:**
```php
'step' => 0.01  // For decimals
```

---

#### For `select`

##### `options` (array)
Key-value pairs for dropdown choices.

**Required:** Yes

**Format:**
```php
'options' => [
    'value1' => 'Display Label 1',
    'value2' => 'Display Label 2',
]
```

---

#### For `date`

##### `min` (string)
Minimum selectable date (YYYY-MM-DD format).

**Example:**
```php
'min' => '2024-01-01'
```

##### `max` (string)
Maximum selectable date (YYYY-MM-DD format).

**Example:**
```php
'max' => '2025-12-31'
```

---

#### For `repeater`

##### `view` (string)
Path to custom Blade template for rendering repeater rows.

**Required:** Yes (for now)

**Example:**
```php
'view' => 'slick-forms::livewire.partials.properties-panel.field-options.select-radio-checkbox'
```

---

## Target Metadata

The `target` property specifies where a field's value should be saved in the database.

### Target: `column`

Save directly to a database column.

**Use For:**
- Core field properties: `name`, `label`, `placeholder`, `help_text`, `element_id`
- Boolean flags: `show_label`, `is_required`, `help_text_as_popover`
- Text properties: `class`, `style`

**Database Tables:**
- `slick_form_fields` table for field types
- `slick_form_layout_elements` table for layout elements

**Example:**
```php
'label' => [
    'type' => 'text',
    'label' => 'Label',
    'tab' => 'basic',
    'target' => 'column',  // Saves to slick_form_fields.label column
]
```

**Saving Logic:**
```php
// In ManagesSchemaProperties trait
$this->selectedField->label = $this->properties['label'];
$this->selectedField->save();
```

---

### Target: `options`

Save to the `options` JSON column (for field types).

**Use For:**
- Field-specific settings: `searchable`, `multiple`, `show_toggle`, `show_strength`
- Custom validation messages: `custom_invalid_feedback`, `custom_valid_feedback`
- Field behavior options: `floating_label`, `field_size`

**Database Column:** `slick_form_fields.options` (JSON)

**Example:**
```php
'searchable' => [
    'type' => 'switch',
    'label' => 'Searchable',
    'tab' => 'options',
    'target' => 'options',  // Saves to options JSON
    'default' => false,
]
```

**Saving Logic:**
```php
// In ManagesSchemaProperties trait
$options = $this->selectedField->options ?? [];
$options['searchable'] = $this->properties['searchable'];
$this->selectedField->options = $options;
$this->selectedField->save();
```

**Resulting JSON:**
```json
{
  "searchable": true,
  "multiple": false,
  "field_size": "lg"
}
```

---

### Target: `settings`

Save to the `settings` JSON column (for layout elements).

**Use For:**
- Layout element configuration: `fluid`, `breakpoint`, `gutter`
- Responsive settings: `width.xs`, `width.md`, `width.lg`
- Component options: `tabs_style`, `accordion_flush`, `card_background`

**Database Column:** `slick_form_layout_elements.settings` (JSON)

**Example:**
```php
'fluid' => [
    'type' => 'switch',
    'label' => 'Fluid Container',
    'tab' => 'settings',
    'target' => 'settings',  // Saves to settings JSON
    'default' => false,
]
```

**Supports Nested Keys** (dot notation):
```php
'width.xs' => [
    'type' => 'select',
    'label' => 'Width - XS',
    'tab' => 'settings',
    'target' => 'settings',
    'options' => [...],
]
```

**Resulting JSON:**
```json
{
  "fluid": true,
  "width": {
    "xs": "12",
    "md": "6",
    "lg": "4"
  }
}
```

---

### Target: `validation_rules`

Save to the `validation_rules` column (for field types).

**Use For:**
- Laravel validation rules array

**Database Column:** `slick_form_fields.validation_rules` (JSON array)

**Example:**
```php
'rules' => [
    'type' => 'repeater',  // Custom UI
    'label' => 'Validation Rules',
    'tab' => 'validation',
    'target' => 'validation_rules',
]
```

**Resulting JSON:**
```json
["required", "email", "max:255"]
```

---

### Target: `conditional_logic`

Save to the `conditional_logic` column (for field types).

**Use For:**
- Conditional visibility rules
- Conditional validation rules

**Database Column:** `slick_form_fields.conditional_logic` (JSON object)

**Example:**
```php
'conditional_visibility' => [
    'type' => 'repeater',  // Custom UI
    'label' => 'Conditional Logic',
    'tab' => 'advanced',
    'target' => 'conditional_logic',
]
```

**Resulting JSON:**
```json
{
  "enabled": true,
  "show_when": "all",
  "conditions": [
    {
      "target_field": "country",
      "operator": "equals",
      "value": "US"
    }
  ]
}
```

---

## Tab Configuration

Tabs organize properties into logical groups in the properties panel.

### Default Tabs

#### For Field Types (from BaseFieldType)

```php
[
    'basic' => [
        'label' => 'Basic',
        'icon' => 'bi-info-circle',
        'order' => 10,
        'view' => null,  // Auto-generate from schema
    ],
    'options' => [
        'label' => 'Options',
        'icon' => 'bi-sliders',
        'order' => 20,
        'view' => null,
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
        'label' => 'Advanced',
        'icon' => 'bi-gear',
        'order' => 50,
        'view' => 'slick-forms::livewire.partials.properties-panel.tabs.advanced',
    ],
]
```

#### For Layout Elements (from BaseLayoutElementType)

```php
[
    'basic' => [
        'label' => 'Basic',
        'icon' => 'bi-info-circle',
        'order' => 10,
        'view' => null,
    ],
    'settings' => [
        'label' => 'Settings',
        'icon' => 'bi-sliders',
        'order' => 20,
        'view' => null,
    ],
    'style' => [
        'label' => 'Style',
        'icon' => 'bi-palette',
        'order' => 30,
        'view' => null,
    ],
    'advanced' => [
        'label' => 'Advanced',
        'icon' => 'bi-gear',
        'order' => 40,
        'view' => null,
    ],
]
```

---

### Custom Tabs

Add custom tabs by overriding `getPropertyTabs()`:

```php
public function getPropertyTabs(): array
{
    $tabs = parent::getPropertyTabs();

    // Add custom tab
    $tabs['data_source'] = [
        'label' => 'Data Source',
        'icon' => 'bi-database',
        'order' => 25,  // Position between options (20) and validation (30)
        'view' => null,  // Auto-generate from schema
    ];

    return $tabs;
}
```

**Then use in schema:**
```php
'api_endpoint' => [
    'type' => 'text',
    'label' => 'API Endpoint',
    'tab' => 'data_source',  // Your custom tab
    'target' => 'options',
]
```

---

### Custom Tab Views

For tabs with complex UI that can't be auto-generated, specify a custom view:

```php
'validation' => [
    'label' => 'Validation',
    'icon' => 'bi-check-circle',
    'order' => 30,
    'view' => 'slick-forms::livewire.partials.properties-panel.tabs.validation',
]
```

**Custom View Must:**
- Access properties via `$properties` (for fields) or `$elementProperties` (for elements)
- Use Livewire `wire:model` bindings
- Follow existing tab conventions

---

## Advanced Features

### Nested Settings (Dot Notation)

Use dot notation for nested JSON structures:

```php
'width.xs' => [
    'type' => 'select',
    'label' => 'Width - XS',
    'tab' => 'settings',
    'target' => 'settings',
    'options' => ['6' => 'Half', '12' => 'Full'],
],
'width.md' => [
    'type' => 'select',
    'label' => 'Width - MD',
    'tab' => 'settings',
    'target' => 'settings',
    'options' => ['4' => 'Third', '6' => 'Half', '12' => 'Full'],
]
```

**Result:**
```json
{
  "width": {
    "xs": "12",
    "md": "6"
  }
}
```

**Access in Code:**
```php
$width = $element->settings['width']['xs'] ?? '12';
```

---

### Conditional Field Visibility (Future)

Not yet implemented, but planned:

```php
'show_strength_min' => [
    'type' => 'number',
    'label' => 'Minimum Strength',
    'tab' => 'options',
    'target' => 'options',
    'show_if' => [
        'field' => 'show_strength',
        'operator' => 'equals',
        'value' => true,
    ],
]
```

---

### Field Groups (Future)

Not yet implemented, but planned for grouping related fields:

```php
'responsive_widths' => [
    'type' => 'group',
    'label' => 'Responsive Widths',
    'tab' => 'settings',
    'fields' => [
        'width.xs' => [...],
        'width.md' => [...],
        'width.lg' => [...],
    ],
]
```

---

## Complete Examples

### Example 1: Custom Field Type with Comprehensive Schema

```php
<?php

namespace App\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\FieldTypes\BaseFieldType;

class SignatureField extends BaseFieldType
{
    public function getName(): string
    {
        return 'signature';
    }

    public function getLabel(): string
    {
        return 'Signature Pad';
    }

    public function getIcon(): string
    {
        return 'bi-pen';
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            // Settings tab
            'heading_canvas' => [
                'type' => 'heading',
                'label' => 'Canvas Settings',
                'tab' => 'settings',
            ],
            'canvas_width' => [
                'type' => 'number',
                'label' => 'Canvas Width (px)',
                'tab' => 'settings',
                'target' => 'options',
                'default' => 400,
                'min' => 200,
                'max' => 1000,
                'help' => 'Width of signature canvas',
            ],
            'canvas_height' => [
                'type' => 'number',
                'label' => 'Canvas Height (px)',
                'tab' => 'settings',
                'target' => 'options',
                'default' => 200,
                'min' => 100,
                'max' => 500,
            ],
            'divider_1' => [
                'type' => 'divider',
                'tab' => 'settings',
            ],
            'heading_pen' => [
                'type' => 'heading',
                'label' => 'Pen Settings',
                'tab' => 'settings',
            ],
            'pen_color' => [
                'type' => 'color',
                'label' => 'Pen Color',
                'tab' => 'settings',
                'target' => 'options',
                'default' => '#000000',
            ],
            'pen_width' => [
                'type' => 'number',
                'label' => 'Pen Width (px)',
                'tab' => 'settings',
                'target' => 'options',
                'default' => 2,
                'min' => 1,
                'max' => 10,
            ],
            'background_color' => [
                'type' => 'color',
                'label' => 'Background Color',
                'tab' => 'settings',
                'target' => 'options',
                'default' => '#ffffff',
            ],
            'show_clear_button' => [
                'type' => 'switch',
                'label' => 'Show Clear Button',
                'tab' => 'settings',
                'target' => 'options',
                'default' => true,
                'help' => 'Allow users to clear and re-sign',
            ],

            // Style tab
            'border_style' => [
                'type' => 'select',
                'label' => 'Border Style',
                'tab' => 'style',
                'target' => 'options',
                'default' => 'solid',
                'options' => [
                    'none' => 'No Border',
                    'solid' => 'Solid',
                    'dashed' => 'Dashed',
                    'dotted' => 'Dotted',
                ],
            ],
        ]);
    }

    public function render($field, $value = null): string
    {
        $options = $field->options ?? [];
        $width = $options['canvas_width'] ?? 400;
        $height = $options['canvas_height'] ?? 200;
        $penColor = $options['pen_color'] ?? '#000000';
        $penWidth = $options['pen_width'] ?? 2;
        $bgColor = $options['background_color'] ?? '#ffffff';
        $showClear = $options['show_clear_button'] ?? true;
        $borderStyle = $options['border_style'] ?? 'solid';

        // Render signature pad HTML
        // ...
    }
}
```

---

### Example 2: Custom Layout Element with Nested Settings

```php
<?php

namespace App\SlickForms\LayoutElements;

use DigitalisStudios\SlickForms\LayoutElementTypes\BaseLayoutElementType;

class ResponsiveGridType extends BaseLayoutElementType
{
    public function getType(): string
    {
        return 'responsive_grid';
    }

    public function getLabel(): string
    {
        return 'Responsive Grid';
    }

    public function getIcon(): string
    {
        return 'bi-grid-3x3-gap';
    }

    public function getConfigSchema(): array
    {
        $columnOptions = [
            '1' => '1 Column',
            '2' => '2 Columns',
            '3' => '3 Columns',
            '4' => '4 Columns',
        ];

        return array_merge(parent::getConfigSchema(), [
            // Nested columns configuration
            'columns.xs' => [
                'type' => 'select',
                'label' => 'Columns - Mobile (<576px)',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => '1',
                'options' => $columnOptions,
            ],
            'columns.sm' => [
                'type' => 'select',
                'label' => 'Columns - Tablet (≥576px)',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => '2',
                'options' => $columnOptions,
            ],
            'columns.md' => [
                'type' => 'select',
                'label' => 'Columns - Desktop (≥768px)',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => '3',
                'options' => $columnOptions,
            ],
            'columns.lg' => [
                'type' => 'select',
                'label' => 'Columns - Large (≥992px)',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => '4',
                'options' => $columnOptions,
            ],
            'divider_1' => [
                'type' => 'divider',
                'tab' => 'settings',
            ],
            'gap' => [
                'type' => 'select',
                'label' => 'Gap Between Items',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => '3',
                'options' => [
                    '0' => 'None',
                    '1' => 'Small',
                    '2' => 'Medium',
                    '3' => 'Default',
                    '4' => 'Large',
                    '5' => 'Extra Large',
                ],
            ],
            'equal_height' => [
                'type' => 'switch',
                'label' => 'Equal Height Columns',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => false,
                'help' => 'Make all columns the same height',
            ],
        ]);
    }

    public function render($element, $childrenHtml): string
    {
        $settings = $element->settings ?? [];
        $columns = $settings['columns'] ?? ['xs' => '1', 'sm' => '2', 'md' => '3', 'lg' => '4'];
        $gap = $settings['gap'] ?? '3';

        // Build responsive column classes
        // ...
    }
}
```

---

## Summary

This schema reference provides all the information needed to define property panel configurations for custom field types and layout elements. Key takeaways:

1. **Use `type` to choose field type** - text, textarea, number, select, switch, color, date, repeater, divider, heading
2. **Use `tab` to organize fields** - basic, options, validation, style, advanced, or custom tabs
3. **Use `target` to specify storage** - column, options, settings, validation_rules, conditional_logic
4. **Always call `parent::getConfigSchema()`** - To include base fields
5. **Use dot notation for nested settings** - e.g., `width.xs`, `columns.md`
6. **Provide help text** - Make your custom types user-friendly

For working examples, see:
- **CUSTOM_FIELD_TYPES.md** - Complete field type examples
- **CUSTOM_LAYOUT_ELEMENTS.md** - Complete layout element examples
- **Built-in types** in `src/FieldTypes/` and `src/LayoutElementTypes/`

---

**Questions?** Check the implementation files or other documentation guides!
