# Creating Custom Layout Elements

**Status:** Complete Guide for Slick Forms v2.1+
**Last Updated:** 2025-10-27
**Prerequisites:** Understanding of Laravel, Bootstrap 5, and Blade templating

---

## Table of Contents

1. [QuickStart: Sidebar Layout Element](#quickstart-sidebar-layout-element)
2. [BaseLayoutElementType Overview](#baselayoutelementtype-overview)
3. [Schema Configuration](#schema-configuration)
4. [Advanced Examples](#advanced-examples)
5. [Best Practices](#best-practices)
6. [Testing Custom Layout Elements](#testing-custom-layout-elements)
7. [Resources](#resources)

---

## QuickStart: Sidebar Layout Element

Let's create a custom **Sidebar** layout element that displays content with a left or right sidebar.

### Step 1: Create the Class

Create a new file: `app/SlickForms/LayoutElements/SidebarType.php`

```php
<?php

namespace App\SlickForms\LayoutElements;

use DigitalisStudios\SlickForms\LayoutElementTypes\BaseLayoutElementType;
use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;

class SidebarType extends BaseLayoutElementType
{
    /**
     * Unique identifier for this layout element type
     */
    public function getType(): string
    {
        return 'sidebar';
    }

    /**
     * Display name shown in the builder palette
     */
    public function getLabel(): string
    {
        return 'Sidebar';
    }

    /**
     * Bootstrap icon class for builder palette
     */
    public function getIcon(): string
    {
        return 'bi-layout-sidebar';
    }

    /**
     * Description shown in builder palette
     */
    public function getDescription(): string
    {
        return 'Two-column layout with a sidebar';
    }

    /**
     * Which element types can be placed inside this element
     *
     * @return array ['*'] for all types, or specific types like ['field', 'card', 'row']
     */
    public function getAllowedChildren(): array
    {
        return ['*']; // Allow any child element
    }

    /**
     * Configuration schema for properties panel
     */
    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'sidebar_position' => [
                'type' => 'select',
                'label' => 'Sidebar Position',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => 'left',
                'options' => [
                    'left' => 'Left Sidebar',
                    'right' => 'Right Sidebar',
                ],
                'help' => 'Position of the sidebar column',
            ],
            'sidebar_width' => [
                'type' => 'select',
                'label' => 'Sidebar Width',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => '3',
                'options' => [
                    '2' => 'Narrow (2 columns)',
                    '3' => 'Medium (3 columns)',
                    '4' => 'Wide (4 columns)',
                ],
                'help' => 'Width of sidebar (out of 12-column grid)',
            ],
            'sidebar_background' => [
                'type' => 'select',
                'label' => 'Sidebar Background',
                'tab' => 'style',
                'target' => 'settings',
                'default' => 'light',
                'options' => [
                    'light' => 'Light',
                    'dark' => 'Dark',
                    'primary' => 'Primary',
                    'secondary' => 'Secondary',
                ],
            ],
        ]);
    }

    /**
     * Render the element for user-facing forms
     */
    public function render(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        $settings = $element->settings ?? [];
        $position = $settings['sidebar_position'] ?? 'left';
        $sidebarWidth = $settings['sidebar_width'] ?? '3';
        $mainWidth = 12 - (int)$sidebarWidth;
        $background = $settings['sidebar_background'] ?? 'light';

        // Separate children into sidebar (first child) and main content (remaining children)
        $children = $element->children()->orderBy('order')->get();
        $sidebarChild = $children->first();
        $mainChildren = $children->slice(1);

        // Render sidebar child
        $sidebarHtml = $sidebarChild
            ? app(\DigitalisStudios\SlickForms\Services\LayoutElementRegistry::class)
                ->get($sidebarChild->element_type)
                ->render($sidebarChild, '')
            : '<div class="text-muted p-3">Sidebar content</div>';

        // Render main content children
        $mainHtml = '';
        foreach ($mainChildren as $child) {
            $mainHtml .= app(\DigitalisStudios\SlickForms\Services\LayoutElementRegistry::class)
                ->get($child->element_type)
                ->render($child, '');
        }

        $sidebarColumn = <<<HTML
            <div class="col-md-{$sidebarWidth} bg-{$background} p-3">
                {$sidebarHtml}
            </div>
        HTML;

        $mainColumn = <<<HTML
            <div class="col-md-{$mainWidth}">
                {$mainHtml}
            </div>
        HTML;

        $columns = $position === 'left'
            ? $sidebarColumn . $mainColumn
            : $mainColumn . $sidebarColumn;

        return <<<HTML
            <div class="row g-0" {$this->getAttributesString($element)}>
                {$columns}
            </div>
        HTML;
    }

    /**
     * Render the element for the builder interface
     */
    public function renderBuilder(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        $settings = $element->settings ?? [];
        $position = $settings['sidebar_position'] ?? 'left';
        $sidebarWidth = $settings['sidebar_width'] ?? '3';
        $mainWidth = 12 - (int)$sidebarWidth;
        $background = $settings['sidebar_background'] ?? 'light';

        $sidebarColumn = <<<HTML
            <div class="col-md-{$sidebarWidth} bg-{$background}-subtle border border-{$background} p-2">
                <small class="text-muted">Sidebar ({$sidebarWidth} cols)</small>
                <div class="mt-2">
                    {$childrenHtml}
                </div>
            </div>
        HTML;

        $mainColumn = <<<HTML
            <div class="col-md-{$mainWidth} border p-2">
                <small class="text-muted">Main Content ({$mainWidth} cols)</small>
                <div class="mt-2">
                    {$childrenHtml}
                </div>
            </div>
        HTML;

        $columns = $position === 'left'
            ? $sidebarColumn . $mainColumn
            : $mainColumn . $sidebarColumn;

        return <<<HTML
            <div class="row g-2 mb-3" {$this->getAttributesString($element)}>
                {$columns}
            </div>
        HTML;
    }
}
```

### Step 2: Register the Layout Element

Add to `config/slick-forms.php`:

```php
'layout_element_types' => [
    // ... existing types
    'sidebar' => \App\SlickForms\LayoutElements\SidebarType::class,
],
```

### Step 3: Register in Service Provider

In `app/Providers/AppServiceProvider.php` (or create a dedicated service provider):

```php
public function boot(): void
{
    $registry = app(\DigitalisStudios\SlickForms\Services\LayoutElementRegistry::class);

    // Register custom layout element
    $registry->register('sidebar', \App\SlickForms\LayoutElements\SidebarType::class);
}
```

### Step 4: Use in Form Builder

The **Sidebar** element will now appear in the builder's layout element palette. Users can drag it onto the canvas, configure sidebar position and width in the properties panel, and add content to both sidebar and main areas.

---

## BaseLayoutElementType Overview

All layout element types must extend `BaseLayoutElementType` and implement these **required abstract methods**:

### Required Methods

#### `getType(): string`
Returns unique identifier for the element type.

```php
public function getType(): string
{
    return 'sidebar'; // Must be unique across all layout element types
}
```

**Rules:**
- Lowercase letters, numbers, underscores only
- Must match the key used in config registration
- Cannot conflict with existing types

#### `getLabel(): string`
Returns human-readable display name for builder palette.

```php
public function getLabel(): string
{
    return 'Sidebar Layout'; // Shown in palette
}
```

#### `getIcon(): string`
Returns Bootstrap icon class for builder palette.

```php
public function getIcon(): string
{
    return 'bi-layout-sidebar'; // Bootstrap Icons 1.10+
}
```

**Common Icons:**
- `bi-grid` - Grid layouts
- `bi-layout-split` - Split layouts
- `bi-layout-sidebar` - Sidebar layouts
- `bi-layout-three-columns` - Multi-column layouts
- `bi-box` - Container/wrapper elements

#### `render(SlickFormLayoutElement $element, string $childrenHtml): string`
Renders the element for **user-facing forms**.

```php
public function render(SlickFormLayoutElement $element, string $childrenHtml): string
{
    $settings = $element->settings ?? [];

    // Build HTML using element settings and children
    return <<<HTML
        <div class="custom-element">
            {$childrenHtml}
        </div>
    HTML;
}
```

**Parameters:**
- `$element` - The database model instance with settings, relationships
- `$childrenHtml` - Pre-rendered HTML of all child elements

**Return:**
- HTML string with proper Bootstrap classes and structure

#### `renderBuilder(SlickFormLayoutElement $element, string $childrenHtml): string`
Renders the element for **builder interface** with visual indicators.

```php
public function renderBuilder(SlickFormLayoutElement $element, string $childrenHtml): string
{
    $settings = $element->settings ?? [];

    // Add visual indicators for builder
    return <<<HTML
        <div class="border border-primary p-3">
            <small class="text-muted">Custom Element</small>
            {$childrenHtml}
        </div>
    HTML;
}
```

**Best Practices:**
- Add borders, background colors, or labels to distinguish from rendered view
- Show configuration hints (e.g., "3-column layout", "Sidebar: left")
- Keep markup similar to `render()` but with builder-specific styling

### Optional Methods

#### `getDescription(): string`
Returns description shown in builder palette.

```php
public function getDescription(): string
{
    return 'Two-column layout with configurable sidebar';
}
```

#### `getAllowedChildren(): array`
Specifies which element types can be placed inside this element.

```php
public function getAllowedChildren(): array
{
    return ['*']; // Allow all types
    // OR
    return ['field', 'card', 'row']; // Specific types only
}
```

**Default:** `['*']` (allows all types)

#### `canContain(string $elementType): bool`
Validates if a specific element type can be added as a child.

```php
public function canContain(string $elementType): bool
{
    $allowed = $this->getAllowedChildren();
    return in_array('*', $allowed) || in_array($elementType, $allowed);
}
```

**Override** only if you need custom validation logic beyond simple type checking.

#### `getConfigSchema(): array`
Returns schema for auto-generating properties panel.

```php
public function getConfigSchema(): array
{
    return array_merge(parent::getConfigSchema(), [
        // Your custom settings
    ]);
}
```

**ALWAYS call `parent::getConfigSchema()`** to include base settings like `element_id`, `class`, `style`, spacing utilities.

#### `getPropertyTabs(): array`
Returns available tabs for properties panel.

```php
public function getPropertyTabs(): array
{
    $tabs = parent::getPropertyTabs(); // basic, settings, style, advanced

    // Add custom tab
    $tabs['layout'] = [
        'label' => 'Layout Options',
        'icon' => 'bi-grid',
        'order' => 25,
        'view' => null, // null = auto-generate from schema
    ];

    return $tabs;
}
```

**Default Tabs:** basic, settings, style, advanced

---

## Schema Configuration

### Schema Field Types

The schema system supports these field types for auto-generating the properties panel:

#### Text Input
```php
'option_name' => [
    'type' => 'text',
    'label' => 'Option Label',
    'tab' => 'settings',
    'target' => 'settings',
    'default' => '',
    'placeholder' => 'Enter value...',
    'help' => 'Optional help text',
]
```

#### Textarea
```php
'description' => [
    'type' => 'textarea',
    'label' => 'Description',
    'tab' => 'settings',
    'target' => 'settings',
    'rows' => 3,
]
```

#### Number Input
```php
'columns' => [
    'type' => 'number',
    'label' => 'Number of Columns',
    'tab' => 'settings',
    'target' => 'settings',
    'min' => 1,
    'max' => 12,
    'default' => 3,
]
```

#### Select Dropdown
```php
'alignment' => [
    'type' => 'select',
    'label' => 'Alignment',
    'tab' => 'settings',
    'target' => 'settings',
    'options' => [
        'left' => 'Left',
        'center' => 'Center',
        'right' => 'Right',
    ],
    'default' => 'left',
]
```

#### Switch (Toggle)
```php
'show_border' => [
    'type' => 'switch',
    'label' => 'Show Border',
    'tab' => 'style',
    'target' => 'settings',
    'default' => false,
]
```

#### Color Picker
```php
'background_color' => [
    'type' => 'color',
    'label' => 'Background Color',
    'tab' => 'style',
    'target' => 'settings',
    'default' => '#ffffff',
]
```

#### Date Picker
```php
'effective_date' => [
    'type' => 'date',
    'label' => 'Effective Date',
    'tab' => 'advanced',
    'target' => 'settings',
]
```

#### Divider (Visual Separator)
```php
'divider_1' => [
    'type' => 'divider',
    'tab' => 'settings',
]
```

#### Heading (Section Label)
```php
'heading_layout' => [
    'type' => 'heading',
    'label' => 'Layout Options',
    'tab' => 'settings',
]
```

### Target Metadata

The `target` key specifies where the value should be saved:

- `'target' => 'column'` - Save to database column (e.g., `element_id`, `class`, `style`)
- `'target' => 'settings'` - Save to `settings` JSON column (most common for custom options)

**Example:**
```php
'element_id' => [
    'type' => 'text',
    'label' => 'Element ID',
    'tab' => 'basic',
    'target' => 'column', // Saves to slick_form_layout_elements.element_id
],
'sidebar_width' => [
    'type' => 'select',
    'label' => 'Sidebar Width',
    'tab' => 'settings',
    'target' => 'settings', // Saves to settings JSON: { "sidebar_width": "3" }
    'options' => [...],
]
```

### Nested Settings with Dot Notation

Use dot notation for nested settings:

```php
'width.xs' => [
    'type' => 'select',
    'label' => 'Width - Extra Small',
    'tab' => 'settings',
    'target' => 'settings',
    'options' => [...],
],
'width.md' => [
    'type' => 'select',
    'label' => 'Width - Medium',
    'tab' => 'settings',
    'target' => 'settings',
    'options' => [...],
]
```

**Result in JSON:**
```json
{
  "width": {
    "xs": "12",
    "md": "6"
  }
}
```

### Base Schema Fields

`BaseLayoutElementType` provides these default schema fields:

- `element_id` - HTML id attribute
- `class` - Custom CSS classes
- `style` - Inline styles
- `spacing.margin_top` - Top margin (0-5)
- `spacing.margin_bottom` - Bottom margin (0-5)
- `spacing.padding` - Padding (0-5)
- Display utilities (d-none, d-block, etc.)
- Text alignment utilities

**Always call `parent::getConfigSchema()`** to include these base fields.

---

## Advanced Examples

### Example 1: Multi-Column Grid

A flexible grid system with configurable column count and responsive behavior.

```php
<?php

namespace App\SlickForms\LayoutElements;

use DigitalisStudios\SlickForms\LayoutElementTypes\BaseLayoutElementType;
use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;

class GridType extends BaseLayoutElementType
{
    public function getType(): string
    {
        return 'grid';
    }

    public function getLabel(): string
    {
        return 'Multi-Column Grid';
    }

    public function getIcon(): string
    {
        return 'bi-grid-3x3';
    }

    public function getDescription(): string
    {
        return 'Responsive grid with configurable columns';
    }

    public function getAllowedChildren(): array
    {
        return ['field', 'card']; // Only fields and cards
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'columns_xs' => [
                'type' => 'select',
                'label' => 'Columns (Mobile)',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => '1',
                'options' => [
                    '1' => '1 Column',
                    '2' => '2 Columns',
                ],
            ],
            'columns_md' => [
                'type' => 'select',
                'label' => 'Columns (Tablet)',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => '2',
                'options' => [
                    '1' => '1 Column',
                    '2' => '2 Columns',
                    '3' => '3 Columns',
                ],
            ],
            'columns_lg' => [
                'type' => 'select',
                'label' => 'Columns (Desktop)',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => '3',
                'options' => [
                    '1' => '1 Column',
                    '2' => '2 Columns',
                    '3' => '3 Columns',
                    '4' => '4 Columns',
                ],
            ],
            'gap' => [
                'type' => 'select',
                'label' => 'Gap Between Items',
                'tab' => 'style',
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
        ]);
    }

    public function render(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        $settings = $element->settings ?? [];
        $columnsXs = $settings['columns_xs'] ?? '1';
        $columnsMd = $settings['columns_md'] ?? '2';
        $columnsLg = $settings['columns_lg'] ?? '3';
        $gap = $settings['gap'] ?? '3';

        // Calculate column widths for Bootstrap grid
        $colXs = 12 / (int)$columnsXs;
        $colMd = 12 / (int)$columnsMd;
        $colLg = 12 / (int)$columnsLg;

        // Wrap each child in a column
        $children = $element->children()->orderBy('order')->get();
        $childrenHtml = '';

        foreach ($children as $child) {
            $childHtml = app(\DigitalisStudios\SlickForms\Services\LayoutElementRegistry::class)
                ->get($child->element_type)
                ->render($child, '');

            $childrenHtml .= <<<HTML
                <div class="col-{$colXs} col-md-{$colMd} col-lg-{$colLg}">
                    {$childHtml}
                </div>
            HTML;
        }

        return <<<HTML
            <div class="row g-{$gap}" {$this->getAttributesString($element)}>
                {$childrenHtml}
            </div>
        HTML;
    }

    public function renderBuilder(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        $settings = $element->settings ?? [];
        $columnsLg = $settings['columns_lg'] ?? '3';
        $gap = $settings['gap'] ?? '3';

        return <<<HTML
            <div class="border border-info p-3 mb-3 bg-info-subtle" {$this->getAttributesString($element)}>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted">
                        <i class="bi bi-grid-3x3 me-1"></i>Grid: {$columnsLg} columns, gap: {$gap}
                    </small>
                </div>
                <div class="row g-{$gap}">
                    {$childrenHtml}
                </div>
            </div>
        HTML;
    }
}
```

### Example 2: Collapsible Section with Custom Tab

A collapsible section with JavaScript behavior and custom properties tab.

```php
<?php

namespace App\SlickForms\LayoutElements;

use DigitalisStudios\SlickForms\LayoutElementTypes\BaseLayoutElementType;
use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;

class CollapsibleType extends BaseLayoutElementType
{
    public function getType(): string
    {
        return 'collapsible';
    }

    public function getLabel(): string
    {
        return 'Collapsible Section';
    }

    public function getIcon(): string
    {
        return 'bi-arrows-collapse';
    }

    public function getDescription(): string
    {
        return 'Section that can be expanded/collapsed';
    }

    /**
     * Add custom "Behavior" tab
     */
    public function getPropertyTabs(): array
    {
        $tabs = parent::getPropertyTabs();

        $tabs['behavior'] = [
            'label' => 'Behavior',
            'icon' => 'bi-gear',
            'order' => 25,
            'view' => null, // Auto-generate from schema
        ];

        return $tabs;
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'title' => [
                'type' => 'text',
                'label' => 'Section Title',
                'tab' => 'settings',
                'target' => 'settings',
                'required' => true,
                'default' => 'Click to expand',
            ],
            'icon' => [
                'type' => 'text',
                'label' => 'Icon Class',
                'tab' => 'settings',
                'target' => 'settings',
                'placeholder' => 'bi-info-circle',
                'help' => 'Bootstrap icon class',
            ],
            'default_open' => [
                'type' => 'switch',
                'label' => 'Default Open',
                'tab' => 'behavior',
                'target' => 'settings',
                'default' => false,
                'help' => 'Start with section expanded',
            ],
            'animate' => [
                'type' => 'switch',
                'label' => 'Animate Toggle',
                'tab' => 'behavior',
                'target' => 'settings',
                'default' => true,
            ],
            'save_state' => [
                'type' => 'switch',
                'label' => 'Remember State',
                'tab' => 'behavior',
                'target' => 'settings',
                'default' => false,
                'help' => 'Save open/closed state in localStorage',
            ],
        ]);
    }

    public function render(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        $settings = $element->settings ?? [];
        $title = $settings['title'] ?? 'Click to expand';
        $icon = $settings['icon'] ?? 'bi-chevron-down';
        $defaultOpen = $settings['default_open'] ?? false;
        $animate = $settings['animate'] ?? true;
        $saveState = $settings['save_state'] ?? false;

        $collapseId = 'collapsible-' . $element->id;
        $showClass = $defaultOpen ? 'show' : '';
        $collapseClass = $animate ? 'collapse' : '';

        $dataAttrs = '';
        if ($saveState) {
            $dataAttrs = 'data-save-state="true" data-storage-key="' . $collapseId . '"';
        }

        return <<<HTML
            <div class="collapsible-section" {$this->getAttributesString($element)} {$dataAttrs}>
                <div class="card">
                    <div class="card-header">
                        <button
                            class="btn btn-link text-decoration-none w-100 text-start d-flex justify-content-between align-items-center"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#{$collapseId}"
                            aria-expanded="{($defaultOpen ? 'true' : 'false')}"
                            aria-controls="{$collapseId}"
                        >
                            <span>
                                <i class="{$icon} me-2"></i>
                                {$title}
                            </span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    </div>
                    <div id="{$collapseId}" class="{$collapseClass} {$showClass}">
                        <div class="card-body">
                            {$childrenHtml}
                        </div>
                    </div>
                </div>
            </div>
        HTML;
    }

    public function renderBuilder(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        $settings = $element->settings ?? [];
        $title = $settings['title'] ?? 'Click to expand';
        $icon = $settings['icon'] ?? 'bi-chevron-down';

        return <<<HTML
            <div class="border border-warning p-3 mb-3 bg-warning-subtle" {$this->getAttributesString($element)}>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>
                        <i class="{$icon} me-1"></i>
                        {$title}
                    </strong>
                    <small class="text-muted">Collapsible Section</small>
                </div>
                <div class="mt-2">
                    {$childrenHtml}
                </div>
            </div>
        HTML;
    }
}
```

**JavaScript for localStorage support** (add to your form renderer):

```javascript
document.addEventListener('DOMContentLoaded', function() {
    // Restore saved state
    document.querySelectorAll('[data-save-state="true"]').forEach(function(element) {
        const storageKey = element.dataset.storageKey;
        const collapse = element.querySelector('.collapse');
        const savedState = localStorage.getItem(storageKey);

        if (savedState === 'open') {
            collapse.classList.add('show');
        }
    });

    // Save state on toggle
    document.querySelectorAll('[data-save-state="true"] [data-bs-toggle="collapse"]').forEach(function(button) {
        button.addEventListener('click', function() {
            const storageKey = this.closest('[data-save-state]').dataset.storageKey;
            const target = document.querySelector(this.dataset.bsTarget);
            const isOpen = target.classList.contains('show');

            localStorage.setItem(storageKey, isOpen ? 'closed' : 'open');
        });
    });
});
```

---

## Best Practices

### ✅ DO: Call Parent Methods

**Always** extend parent functionality rather than replacing it:

```php
// ✅ GOOD
public function getConfigSchema(): array
{
    return array_merge(parent::getConfigSchema(), [
        'my_option' => [...],
    ]);
}

// ❌ BAD - Loses base functionality
public function getConfigSchema(): array
{
    return [
        'my_option' => [...],
    ];
}
```

### ✅ DO: Use Bootstrap Classes

Stay consistent with Bootstrap 5 grid and utilities:

```php
// ✅ GOOD - Bootstrap grid
return '<div class="row g-3"><div class="col-md-6">...</div></div>';

// ❌ BAD - Custom CSS that may conflict
return '<div class="my-custom-grid">...</div>';
```

### ✅ DO: Validate Child Elements

Use `getAllowedChildren()` to restrict nesting:

```php
public function getAllowedChildren(): array
{
    return ['field', 'card']; // Only specific types
}
```

### ✅ DO: Provide Builder Visual Feedback

Make builder preview clearly different from rendered output:

```php
public function renderBuilder(SlickFormLayoutElement $element, string $childrenHtml): string
{
    return <<<HTML
        <div class="border border-primary bg-primary-subtle p-3 mb-3">
            <small class="text-muted">My Custom Element</small>
            {$childrenHtml}
        </div>
    HTML;
}
```

### ✅ DO: Use Proper Attribute Handling

Use `getAttributesString()` for consistent attribute rendering:

```php
return <<<HTML
    <div {$this->getAttributesString($element)}>
        {$childrenHtml}
    </div>
HTML;
```

This includes `element_id`, `class`, and `style` attributes automatically.

### ✅ DO: Document Complex Options

Add `help` text for non-obvious settings:

```php
'advanced_option' => [
    'type' => 'select',
    'label' => 'Advanced Option',
    'tab' => 'settings',
    'target' => 'settings',
    'options' => [...],
    'help' => 'This controls the XYZ behavior. Use "Auto" unless you have specific requirements.',
]
```

### ❌ DON'T: Hardcode IDs Without Uniqueness

```php
// ❌ BAD - Multiple instances will have same ID
return '<div id="my-section">{$childrenHtml}</div>';

// ✅ GOOD - Use element ID for uniqueness
$sectionId = 'section-' . $element->id;
return '<div id="{$sectionId}">{$childrenHtml}</div>';
```

### ❌ DON'T: Access Children Manually in render()

The `$childrenHtml` parameter contains pre-rendered children. Don't query children manually unless you need custom ordering/filtering:

```php
// ❌ BAD - Unnecessary query
public function render(SlickFormLayoutElement $element, string $childrenHtml): string
{
    $children = $element->children; // Don't do this
    // ...
}

// ✅ GOOD - Use provided HTML
public function render(SlickFormLayoutElement $element, string $childrenHtml): string
{
    return "<div>{$childrenHtml}</div>";
}
```

**Exception:** Custom ordering/filtering (like Sidebar example separating first child from rest).

### ❌ DON'T: Forget Responsive Design

Always consider mobile-first responsive behavior:

```php
// ❌ BAD - Only desktop
return '<div class="col-3">...</div>';

// ✅ GOOD - Responsive
return '<div class="col-12 col-md-6 col-lg-3">...</div>';
```

---

## Testing Custom Layout Elements

### Manual Testing Checklist

1. **Builder Palette** - Element appears with correct icon and label
2. **Drag & Drop** - Can drag element onto canvas
3. **Nesting** - Only allowed child types can be added
4. **Properties Panel** - All schema fields render correctly
5. **Save & Reload** - Settings persist after saving form
6. **Rendering** - Element displays correctly on user-facing form
7. **Responsive** - Layout works on mobile, tablet, desktop
8. **Multiple Instances** - Can add multiple instances without conflicts

### Unit Testing Example

Create `tests/Unit/CustomLayoutElementTest.php`:

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\SlickForms\LayoutElements\SidebarType;
use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;

class CustomLayoutElementTest extends TestCase
{
    protected SidebarType $sidebarType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sidebarType = new SidebarType;
    }

    public function test_has_correct_metadata()
    {
        $this->assertEquals('sidebar', $this->sidebarType->getType());
        $this->assertEquals('Sidebar', $this->sidebarType->getLabel());
        $this->assertEquals('bi-layout-sidebar', $this->sidebarType->getIcon());
    }

    public function test_config_schema_includes_custom_fields()
    {
        $schema = $this->sidebarType->getConfigSchema();

        $this->assertArrayHasKey('sidebar_position', $schema);
        $this->assertArrayHasKey('sidebar_width', $schema);
        $this->assertEquals('select', $schema['sidebar_position']['type']);
    }

    public function test_config_schema_includes_base_fields()
    {
        $schema = $this->sidebarType->getConfigSchema();

        // From BaseLayoutElementType
        $this->assertArrayHasKey('element_id', $schema);
        $this->assertArrayHasKey('class', $schema);
        $this->assertArrayHasKey('style', $schema);
    }

    public function test_allows_all_children_by_default()
    {
        $this->assertEquals(['*'], $this->sidebarType->getAllowedChildren());
        $this->assertTrue($this->sidebarType->canContain('field'));
        $this->assertTrue($this->sidebarType->canContain('card'));
    }

    public function test_render_includes_settings()
    {
        $element = SlickFormLayoutElement::factory()->create([
            'element_type' => 'sidebar',
            'settings' => [
                'sidebar_position' => 'right',
                'sidebar_width' => '4',
            ],
        ]);

        $html = $this->sidebarType->render($element, '<p>Child content</p>');

        $this->assertStringContainsString('col-md-4', $html);
        $this->assertStringContainsString('col-md-8', $html);
    }

    public function test_render_builder_has_visual_indicators()
    {
        $element = SlickFormLayoutElement::factory()->create([
            'element_type' => 'sidebar',
        ]);

        $html = $this->sidebarType->renderBuilder($element, '<p>Child</p>');

        $this->assertStringContainsString('border', $html);
        $this->assertStringContainsString('Sidebar', $html);
    }
}
```

### Feature Testing Example

Test the complete flow in builder:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;
use Livewire\Livewire;
use DigitalisStudios\SlickForms\Livewire\FormBuilder;

class SidebarLayoutElementTest extends TestCase
{
    public function test_can_add_sidebar_element_in_builder()
    {
        $form = CustomForm::factory()->create();

        Livewire::test(FormBuilder::class, ['form' => $form])
            ->call('addLayoutElement', 'sidebar')
            ->assertSet('showElementEditor', true)
            ->assertSee('Sidebar Position')
            ->assertSee('Sidebar Width');
    }

    public function test_can_configure_sidebar_settings()
    {
        $form = CustomForm::factory()->create();
        $element = SlickFormLayoutElement::factory()->create([
            'form_id' => $form->id,
            'element_type' => 'sidebar',
            'settings' => ['sidebar_position' => 'left'],
        ]);

        Livewire::test(FormBuilder::class, ['form' => $form])
            ->call('editElement', $element->id)
            ->set('elementProperties.sidebar_position', 'right')
            ->set('elementProperties.sidebar_width', '4')
            ->call('saveElement');

        $element->refresh();
        $this->assertEquals('right', $element->settings['sidebar_position']);
        $this->assertEquals('4', $element->settings['sidebar_width']);
    }
}
```

---

## Resources

### Official Documentation

- **Slick Forms** - Main package documentation (see project README)
- **BaseLayoutElementType Source** - `src/LayoutElementTypes/BaseLayoutElementType.php`
- **LayoutElementRegistry Source** - `src/Services/LayoutElementRegistry.php`
- **TabRegistry Source** - `src/Services/TabRegistry.php`

### Built-in Examples

Study these built-in layout elements for reference:

1. **ContainerType** (`src/LayoutElementTypes/ContainerType.php`)
   - Simple example with fluid/breakpoint options
   - Good starting point for beginners

2. **ColumnType** (`src/LayoutElementTypes/ColumnType.php`)
   - Complex responsive configuration with nested settings
   - Advanced use of dot notation (width.xs, width.md)

3. **CardType** (`src/LayoutElementTypes/CardType.php`)
   - Multiple visual options (header, footer, colors)
   - Good example of style-focused element

4. **TabsType** (`src/LayoutElementTypes/TabsType.php`)
   - JavaScript-enhanced component
   - Shows how to integrate Bootstrap JS components

5. **TableType** (`src/LayoutElementTypes/TableType.php`)
   - Field-focused layout
   - Custom child rendering logic

### Bootstrap 5 Resources

- **Bootstrap Grid** - https://getbootstrap.com/docs/5.3/layout/grid/
- **Bootstrap Components** - https://getbootstrap.com/docs/5.3/components/
- **Bootstrap Utilities** - https://getbootstrap.com/docs/5.3/utilities/
- **Bootstrap Icons** - https://icons.getbootstrap.com/

### Related Guides

- **CUSTOM_FIELD_TYPES.md** - Creating custom field types (companion guide)
- **SCHEMA_REFERENCE.md** - Complete schema options reference
- **INTEGRATION_GUIDE.md** - Integrating the new properties panel system

---

## Next Steps

1. **Try the QuickStart** - Create the Sidebar layout element
2. **Study Built-in Types** - Read ContainerType, RowType, ColumnType source
3. **Experiment** - Create your own layout element (wizard, timeline, masonry)
4. **Test Thoroughly** - Use manual and automated tests
5. **Share** - Contribute back to the community!

---

**Questions or Issues?**

- Check `IMPLEMENTATION_COMPLETE.md` for system overview
- Review `INTEGRATION_GUIDE.md` for setup steps
- Examine built-in layout element types in `src/LayoutElementTypes/`

Happy building! 🎉
