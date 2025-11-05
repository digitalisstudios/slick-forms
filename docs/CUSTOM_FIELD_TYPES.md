# Creating Custom Field Types

**Slick Forms v1.0+** - Complete guide for creating custom form field types

---

## Overview

Custom field types allow you to extend Slick Forms with any type of input field you need. Examples include:
- Signature pads
- Rich text editors
- Date range pickers with time
- Rating systems
- Drawing canvases
- Code editors
- Anything you can imagine!

---

## Quick Start

### Step 1: Create Your Field Type Class

Create a new class that extends `BaseFieldType`:

```php
<?php

namespace App\CustomFields;

use DigitalisStudios\SlickForms\FieldTypes\BaseFieldType;
use DigitalisStudios\SlickForms\Models\CustomFormField;

class SignatureField extends BaseFieldType
{
    /**
     * Unique identifier for this field type
     */
    public function getName(): string
    {
        return 'signature';
    }

    /**
     * Display label shown in form builder palette
     */
    public function getLabel(): string
    {
        return 'Signature Pad';
    }

    /**
     * Bootstrap icon class for form builder
     */
    public function getIcon(): string
    {
        return 'bi-pen';
    }

    /**
     * Render the field for user-facing forms
     */
    public function render(CustomFormField $field, mixed $value = null): string
    {
        $attributes = $this->getCommonAttributes($field);
        $elementId = $field->element_id ?? 'field_' . $field->id;

        // Build HTML
        $html = $this->renderLabelWithFloating($field, $elementId);

        $html .= '<div class="signature-pad-wrapper">';
        $html .= '<canvas id="' . $elementId . '" class="signature-canvas" ';
        $html .= 'data-pen-color="' . ($field->options['pen_color'] ?? '#000000') . '" ';
        $html .= 'data-background="' . ($field->options['background_color'] ?? '#ffffff') . '" ';
        $html .= '></canvas>';
        $html .= '<button type="button" class="btn btn-sm btn-secondary mt-2" ';
        $html .= 'onclick="clearSignature(\'' . $elementId . '\')">Clear</button>';
        $html .= '</div>';

        // Hidden input to store signature data
        $html .= '<input type="hidden" name="' . $field->name . '" id="' . $elementId . '_data" ';
        $html .= 'value="' . htmlspecialchars($value ?? '') . '">';

        $html .= $this->renderHelpText($field);
        $html .= $this->renderValidationFeedback($field);

        return $html;
    }

    /**
     * Render preview in form builder
     */
    public function renderBuilder(CustomFormField $field): string
    {
        return '
            <div class="border rounded p-3 bg-light text-center">
                <i class="bi bi-pen fs-1 text-muted"></i>
                <p class="small text-muted mb-0">Signature Pad</p>
                <p class="small text-muted mb-0">' . ($field->label ?? 'Untitled') . '</p>
            </div>
        ';
    }

    /**
     * Validation rules for this field type
     */
    public function validate(CustomFormField $field, mixed $value): array
    {
        $rules = parent::validate($field, $value);

        // Add signature-specific validation
        if ($field->is_required) {
            $rules[] = 'required';
        }

        return $rules;
    }

    /**
     * Process submitted value before storage
     */
    public function processValue(mixed $value): mixed
    {
        // Signature is stored as base64 data URL
        // You might want to save to file storage instead
        return $value;
    }

    /**
     * Configuration schema for properties panel
     */
    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'pen_color' => [
                'type' => 'color',
                'label' => 'Pen Color',
                'tab' => 'options',
                'target' => 'options',
                'default' => '#000000',
                'help' => 'Color of the signature pen',
            ],
            'background_color' => [
                'type' => 'color',
                'label' => 'Background Color',
                'tab' => 'options',
                'target' => 'options',
                'default' => '#ffffff',
                'help' => 'Background color of the canvas',
            ],
            'width' => [
                'type' => 'number',
                'label' => 'Canvas Width (px)',
                'tab' => 'options',
                'target' => 'options',
                'default' => 600,
                'min' => 200,
                'max' => 1200,
                'help' => 'Width of the signature canvas',
            ],
            'height' => [
                'type' => 'number',
                'label' => 'Canvas Height (px)',
                'tab' => 'options',
                'target' => 'options',
                'default' => 200,
                'min' => 100,
                'max' => 600,
                'help' => 'Height of the signature canvas',
            ],
            'save_as_file' => [
                'type' => 'switch',
                'label' => 'Save as Image File',
                'tab' => 'options',
                'target' => 'options',
                'default' => false,
                'help' => 'Convert signature to PNG and save to storage',
            ],
        ]);
    }

    /**
     * Validation options available for this field
     */
    public function getAvailableValidationOptions(): array
    {
        return [
            'require_min_strokes' => [
                'type' => 'number',
                'label' => 'Minimum Strokes Required',
                'placeholder' => '3',
                'help' => 'Require at least this many pen strokes',
            ],
        ];
    }

    /**
     * Custom tabs (optional)
     */
    public function getPropertyTabs(): array
    {
        $tabs = parent::getPropertyTabs();

        // Add custom tab for advanced signature settings
        $tabs['signature'] = [
            'label' => 'Signature',
            'icon' => 'bi-pen',
            'order' => 25,  // Between options (20) and validation (30)
            'view' => null,  // Auto-generate from schema
        ];

        return $tabs;
    }
}
```

### Step 2: Register Your Field Type

In your application's service provider:

```php
<?php

namespace App\Providers;

use App\CustomFields\SignatureField;
use Illuminate\Support\ServiceProvider;
use DigitalisStudios\SlickForms\Services\FieldTypeRegistry;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register custom field type
        app(FieldTypeRegistry::class)->register('signature', SignatureField::class);
    }
}
```

### Step 3: Create Supporting Assets (Optional)

If your field requires JavaScript:

```js
// resources/js/signature-field.js

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all signature canvases
    document.querySelectorAll('.signature-canvas').forEach(canvas => {
        const ctx = canvas.getContext('2d');
        const penColor = canvas.dataset.penColor || '#000000';
        const bgColor = canvas.dataset.background || '#ffffff';

        // Set canvas background
        ctx.fillStyle = bgColor;
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Drawing logic
        let drawing = false;
        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);

        function startDrawing(e) {
            drawing = true;
            ctx.beginPath();
            ctx.moveTo(e.offsetX, e.offsetY);
        }

        function draw(e) {
            if (!drawing) return;
            ctx.strokeStyle = penColor;
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.lineTo(e.offsetX, e.offsetY);
            ctx.stroke();

            // Save to hidden input
            const dataUrl = canvas.toDataURL();
            document.getElementById(canvas.id + '_data').value = dataUrl;
        }

        function stopDrawing() {
            drawing = false;
        }
    });
});

function clearSignature(canvasId) {
    const canvas = document.getElementById(canvasId);
    const ctx = canvas.getContext('2d');
    const bgColor = canvas.dataset.background || '#ffffff';

    ctx.fillStyle = bgColor;
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    document.getElementById(canvasId + '_data').value = '';
}
```

Include in your layout:

```blade
@push('scripts')
    <script src="{{ asset('js/signature-field.js') }}"></script>
@endpush
```

---

## Schema Configuration

The `getConfigSchema()` method defines what properties appear in the properties panel.

### Schema Field Types

| Type | Description | Example |
|------|-------------|---------|
| `text` | Single-line text input | Name, placeholder |
| `textarea` | Multi-line text | Help text, descriptions |
| `number` | Numeric input | Min, max, step values |
| `select` | Dropdown selection | Predefined options |
| `switch` | Toggle switch | Boolean options |
| `color` | Color picker | Theme colors |
| `date` | Date picker | Date restrictions |
| `repeater` | Array of items | Option lists |

### Schema Options

| Option | Type | Description | Required |
|--------|------|-------------|----------|
| `type` | string | Field type (see above) | ✅ Yes |
| `label` | string | Display label | ✅ Yes |
| `tab` | string | Which tab to show in | ✅ Yes |
| `target` | string | Where to save (`column` or `options`) | ✅ Yes |
| `default` | mixed | Default value | No |
| `help` | string | Helper text below field | No |
| `placeholder` | string | Placeholder text | No |
| `required` | bool | Is this option required? | No |
| `min` | number | Minimum value (numbers) | No |
| `max` | number | Maximum value (numbers) | No |
| `step` | number | Step increment (numbers) | No |
| `options` | array | Options for select fields | Conditional |
| `rows` | number | Rows for textarea | No |

### Tab Names

Standard tabs (automatically available):
- `basic` - Label, name, placeholder, help text
- `options` - Field-specific configuration
- `validation` - Validation rules and messages
- `style` - CSS classes and styles
- `advanced` - Visibility, conditional logic

You can create custom tabs by overriding `getPropertyTabs()`.

### Target Specification

**IMPORTANT:** Every schema field must specify where the value should be saved:

- `'target' => 'column'` - Save to database column (e.g., `label`, `name`, `is_required`)
- `'target' => 'options'` - Save to `options` JSON column (field-specific settings)

Example:

```php
'label' => [
    'type' => 'text',
    'label' => 'Label',
    'tab' => 'basic',
    'target' => 'column',  // Saved to custom_form_fields.label column
],
'searchable' => [
    'type' => 'switch',
    'label' => 'Searchable',
    'tab' => 'options',
    'target' => 'options',  // Saved to custom_form_fields.options['searchable']
],
```

---

## Advanced Examples

### Example 1: Rich Text Editor Field

```php
class WysiwygField extends BaseFieldType
{
    public function getName(): string { return 'wysiwyg'; }
    public function getLabel(): string { return 'Rich Text Editor'; }
    public function getIcon(): string { return 'bi-paragraph'; }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'editor_height' => [
                'type' => 'number',
                'label' => 'Editor Height (px)',
                'tab' => 'options',
                'target' => 'options',
                'default' => 300,
                'min' => 150,
                'max' => 1000,
            ],
            'toolbar_mode' => [
                'type' => 'select',
                'label' => 'Toolbar Mode',
                'tab' => 'options',
                'target' => 'options',
                'options' => [
                    'full' => 'Full Toolbar',
                    'basic' => 'Basic Toolbar',
                    'minimal' => 'Minimal',
                ],
                'default' => 'basic',
            ],
            'allow_images' => [
                'type' => 'switch',
                'label' => 'Allow Image Upload',
                'tab' => 'options',
                'target' => 'options',
                'default' => true,
            ],
            'max_characters' => [
                'type' => 'number',
                'label' => 'Maximum Characters',
                'tab' => 'validation',
                'target' => 'options',
                'min' => 0,
                'help' => 'Leave 0 for unlimited',
            ],
        ]);
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $height = $field->options['editor_height'] ?? 300;
        $toolbar = $field->options['toolbar_mode'] ?? 'basic';

        return view('custom-fields::wysiwyg', [
            'field' => $field,
            'value' => $value,
            'height' => $height,
            'toolbar' => $toolbar,
        ])->render();
    }
}
```

### Example 2: Star Rating Field with Custom Tab

```php
class AdvancedStarRatingField extends BaseFieldType
{
    public function getName(): string { return 'advanced_star_rating'; }
    public function getLabel(): string { return 'Advanced Star Rating'; }
    public function getIcon(): string { return 'bi-star-fill'; }

    public function getPropertyTabs(): array
    {
        $tabs = parent::getPropertyTabs();

        // Add custom "Rating" tab
        $tabs['rating'] = [
            'label' => 'Rating Settings',
            'icon' => 'bi-stars',
            'order' => 25,
            'view' => null,  // Auto-generate from schema
        ];

        return $tabs;
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            // Rating tab fields
            'max_stars' => [
                'type' => 'number',
                'label' => 'Maximum Stars',
                'tab' => 'rating',  // Custom tab
                'target' => 'options',
                'default' => 5,
                'min' => 3,
                'max' => 10,
            ],
            'allow_half_stars' => [
                'type' => 'switch',
                'label' => 'Allow Half Stars',
                'tab' => 'rating',
                'target' => 'options',
                'default' => false,
            ],
            'star_color' => [
                'type' => 'color',
                'label' => 'Star Color',
                'tab' => 'rating',
                'target' => 'options',
                'default' => '#FFD700',
            ],
            'show_value_text' => [
                'type' => 'switch',
                'label' => 'Show Numeric Value',
                'tab' => 'rating',
                'target' => 'options',
                'default' => true,
                'help' => 'Display "3.5 out of 5 stars" text',
            ],
        ]);
    }
}
```

---

## Best Practices

### 1. Always Extend BaseFieldType
Never create field types from scratch. Always extend `BaseFieldType` to get:
- Standard methods and helpers
- Consistent behavior across all fields
- Automatic Bootstrap integration

### 2. Use Schema for Everything
Put ALL configurable options in `getConfigSchema()`:
- ✅ Auto-generated UI in properties panel
- ✅ Consistent user experience
- ✅ No manual blade files needed

### 3. Leverage Base Methods
`BaseFieldType` provides many helpers:
- `getCommonAttributes()` - Standard field attributes
- `renderLabel()` - Label with help text
- `renderHelpText()` - Help text below field
- `renderValidationFeedback()` - Bootstrap validation messages
- `buildUtilityClasses()` - Bootstrap spacing/display utilities

### 4. Handle Both Render Methods
- `render()` - User-facing form (must work perfectly)
- `renderBuilder()` - Form builder preview (can be simplified)

### 5. Process Values Appropriately
Override `processValue()` to transform submitted data:
- Convert base64 to files
- Parse JSON
- Format dates
- Sanitize input

### 6. Add Validation Options
Provide field-specific validation in `getAvailableValidationOptions()`:

```php
public function getAvailableValidationOptions(): array
{
    return [
        'min_file_size' => [
            'type' => 'number',
            'label' => 'Minimum File Size (KB)',
            'help' => 'Minimum file size in kilobytes',
        ],
        'allowed_extensions' => [
            'type' => 'text',
            'label' => 'Allowed Extensions',
            'placeholder' => 'jpg,png,pdf',
            'help' => 'Comma-separated list of allowed file extensions',
        ],
    ];
}
```

---

## Common Pitfalls

### ❌ Don't: Hardcode Properties
```php
// BAD - Hardcoded configuration
public function render($field, $value = null): string
{
    $color = '#ff0000';  // Hardcoded!
    return '<input type="color" value="' . $color . '">';
}
```

### ✅ Do: Use Schema + Options
```php
// GOOD - Schema-driven configuration
public function getConfigSchema(): array
{
    return array_merge(parent::getConfigSchema(), [
        'default_color' => [
            'type' => 'color',
            'label' => 'Default Color',
            'tab' => 'options',
            'target' => 'options',
            'default' => '#ff0000',
        ],
    ]);
}

public function render($field, $value = null): string
{
    $defaultColor = $field->options['default_color'] ?? '#ff0000';
    return '<input type="color" value="' . ($value ?? $defaultColor) . '">';
}
```

### ❌ Don't: Create Manual Blade Files for Options
```php
// BAD - Manual blade file for options
// resources/views/field-options/my-field.blade.php
// (This defeats the purpose of schema-driven architecture)
```

### ✅ Do: Define Everything in Schema
```php
// GOOD - Everything in schema
public function getConfigSchema(): array
{
    return array_merge(parent::getConfigSchema(), [
        // All options defined here, auto-generated UI
    ]);
}
```

---

## Testing Your Custom Field

### Unit Test Example

```php
use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\CustomFormField;
use Tests\TestCase;

class SignatureFieldTest extends TestCase
{
    /** @test */
    public function signature_field_renders_correctly()
    {
        $form = CustomForm::create(['name' => 'Test Form', 'is_active' => true]);

        $field = CustomFormField::create([
            'slick_form_id' => $form->id,
            'field_type' => 'signature',
            'name' => 'signature',
            'label' => 'Your Signature',
            'options' => [
                'pen_color' => '#0000ff',
                'background_color' => '#ffffff',
            ],
        ]);

        $registry = app(FieldTypeRegistry::class);
        $fieldType = $registry->get('signature');
        $html = $fieldType->render($field);

        $this->assertStringContainsString('signature-canvas', $html);
        $this->assertStringContainsString('data-pen-color="#0000ff"', $html);
    }

    /** @test */
    public function signature_field_has_correct_schema()
    {
        $registry = app(FieldTypeRegistry::class);
        $fieldType = $registry->get('signature');
        $schema = $fieldType->getConfigSchema();

        $this->assertArrayHasKey('pen_color', $schema);
        $this->assertEquals('color', $schema['pen_color']['type']);
        $this->assertEquals('options', $schema['pen_color']['target']);
    }
}
```

---

## Resources

- **Schema Reference:** See `docs/SCHEMA_REFERENCE.md` for complete schema options
- **Migration Guide:** See `docs/MIGRATION_GUIDE.md` for upgrading existing custom types
- **Base Class:** Review `src/FieldTypes/BaseFieldType.php` for all available methods
- **Examples:** Check `src/FieldTypes/` for 20+ built-in field type examples

---

## Next Steps

1. ✅ Create your custom field type class
2. ✅ Define comprehensive `getConfigSchema()`
3. ✅ Implement `render()` and `renderBuilder()`
4. ✅ Register in service provider
5. ✅ Test in form builder
6. ✅ Add JavaScript/CSS if needed
7. ✅ Write unit tests
8. ✅ Document usage for your team

Happy coding! 🚀
