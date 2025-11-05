# Slick Forms Schema Documentation

This directory contains automatically generated JSON schema documentation for all field types and layout element types in Slick Forms.

## Directory Structure

```
docs/schema/
├── fields/          # Field type schemas (32 files)
└── elements/        # Layout element type schemas (16 files)
```

## What Are These Schemas?

Each JSON file contains complete documentation for a specific field type or layout element type, including:

- **Metadata** - Type identifier, label, icon, description
- **Usage** - Model name, method, and example code showing how to create instances
- **Properties** - All available properties with type, storage location, defaults, and descriptions
- **Validation Rules** - Available Laravel validation rules (fields only)
- **Conditional Logic** - Supported operators and structure (fields only)
- **Allowed Children** - Nesting rules (layout elements only)
- **Tabs** - Properties panel tab organization

## Field Type Schemas (32 types)

### Input Fields
- `text.json` - Single-line text input
- `textarea.json` - Multi-line text input
- `email.json` - Email address input with validation
- `number.json` - Numeric input with min/max
- `password.json` - Password input with visibility toggle
- `phone.json` - Phone number with country selector
- `url.json` - URL input with link preview
- `hidden.json` - Hidden field for storing data

### Selection Fields
- `select.json` - Dropdown select
- `radio.json` - Radio button group
- `checkbox.json` - Checkbox group
- `switch.json` - Toggle switch
- `tags.json` - Multi-value tag input

### Date & Time Fields
- `date.json` - Date picker
- `time.json` - Time picker
- `date_range.json` - Start and end date selection

### File Upload Fields
- `file.json` - Generic file upload
- `image.json` - Image upload with preview
- `video.json` - Video upload with preview
- `pdf_embed.json` - PDF file upload with viewer

### Interactive Fields
- `star_rating.json` - 5-star rating input
- `slider.json` - Single value slider
- `range.json` - Min-max range slider
- `color_picker.json` - Color selection
- `signature.json` - Canvas signature pad
- `location.json` - Map-based location picker
- `rating_matrix.json` - Multiple items rated on same scale

### Advanced Fields
- `calculation.json` - Formula-based calculated field
- `repeater.json` - Repeatable field group

### Content Fields (Display Only)
- `code.json` - Syntax-highlighted code block
- `header.json` - Heading text
- `paragraph.json` - Paragraph text

## Layout Element Schemas (16 types)

### Structure Elements
- `container.json` - Top-level wrapper (fluid or fixed-width)
- `row.json` - Bootstrap grid row with gutter and alignment
- `column.json` - Responsive column with breakpoint widths

### Component Elements
- `card.json` - Visual grouping with header/footer
- `tabs.json` - Tabbed interface container
- `tab.json` - Individual tab within tabs
- `accordion.json` - Collapsible sections container
- `accordion_item.json` - Individual accordion section
- `carousel.json` - Swiper.js carousel/slider
- `carousel_slide.json` - Individual carousel slide

### Table Elements
- `table.json` - Semantic HTML table
- `table_header.json` - Table header section (thead)
- `table_body.json` - Table body section (tbody)
- `table_footer.json` - Table footer section (tfoot)
- `table_row.json` - Table row (tr)
- `table_cell.json` - Table cell (td)

## Schema Structure

### Field Type Schema Example

```json
{
  "metadata": {
    "type": "text",
    "label": "Text Input",
    "icon": "bi bi-input-cursor-text",
    "description": "Single-line text input field"
  },
  "usage": {
    "model": "CustomFormField",
    "method": "create",
    "example": {
      "slick_form_id": 1,
      "field_type": "text",
      "name": "email_address",
      "label": "Email Address",
      "is_required": true,
      "order": 0
    }
  },
  "properties": { ... },
  "validation_rules": { ... },
  "conditional_logic": { ... },
  "tabs": { ... }
}
```

### Layout Element Schema Example

```json
{
  "metadata": {
    "type": "container",
    "label": "Container",
    "icon": "bi-box",
    "description": "Top-level wrapper"
  },
  "usage": {
    "model": "SlickFormLayoutElement",
    "method": "create",
    "example": {
      "slick_form_id": 1,
      "element_type": "container",
      "order": 0,
      "settings": {}
    }
  },
  "properties": { ... },
  "allowed_children": ["row", "field", "card", ...],
  "tabs": { ... }
}
```

## How To Use These Schemas

### 1. API Integration

Use these schemas to understand what properties are available when creating forms programmatically:

```php
// Example: Creating a text field
CustomFormField::create([
    'slick_form_id' => 1,
    'field_type' => 'text',
    'name' => 'first_name',
    'label' => 'First Name',
    'placeholder' => 'Enter your first name',
    'is_required' => true,
    'order' => 0,
    'validation_rules' => ['required', 'min:2', 'max:50'],
    'options' => [
        'floating_label' => true,
        'field_size' => 'lg'
    ]
]);
```

### 2. Documentation Generation

Parse these JSON files to generate documentation for your application or API consumers.

### 3. Type Generation

Use schemas to generate TypeScript interfaces or other type definitions for frontend applications.

### 4. Validation

Validate form configuration payloads against these schemas.

## Regenerating Schemas

Schemas are automatically generated from the current codebase using:

```bash
php artisan slick-forms:export-schemas
```

Run this command whenever:
- New field types or layout elements are added
- Existing types are modified (new properties, validation rules, etc.)
- Configuration schemas change

The command reads directly from:
- `getConfigSchema()` methods
- `getAvailableValidationOptions()` methods
- `getPropertyTabs()` methods
- `ConditionalLogicEvaluator` service
- Model `$fillable` arrays

## Schema Benefits

✅ **Always Up-to-Date** - Generated from actual code, impossible to drift from implementation
✅ **Complete** - Includes all properties, options, validation rules, and examples
✅ **Developer-Friendly** - Clear structure showing exactly how to use `::create()` methods
✅ **Type-Safe** - Reflects actual implementation, not outdated documentation
✅ **Versionable** - Track changes in git to see what's been added/modified
✅ **API-Ready** - Perfect for external integrations and API documentation

## Property Storage Locations

Properties are stored in different locations depending on their `storage` value:

- **`column`** - Direct database column in `slick_form_fields` or `slick_form_layout_elements`
- **`json_column`** - Stored in JSON columns:
  - `options` - Field-specific configuration
  - `settings` - Element-specific configuration
  - `validation_rules` - Laravel validation rules
  - `conditional_logic` - Visibility and conditional validation

## Conditional Logic Operators

Each field type supports different operators based on its data type:

- **Text fields**: `equals`, `not_equals`, `contains`, `not_contains`, `starts_with`, `ends_with`, `regex`, `is_empty`, `is_not_empty`
- **Number fields**: `equals`, `not_equals`, `greater_than`, `less_than`, `>=`, `<=`, `is_empty`, `is_not_empty`
- **Checkbox/Switch**: `checked`, `unchecked`
- **Select/Radio**: `equals`, `not_equals`, `in`, `not_in`, `is_empty`, `is_not_empty`
- **Date fields**: `equals`, `not_equals`, `after`, `before`, `after_or_equal`, `before_or_equal`, `is_empty`, `is_not_empty`

See individual field schemas for exact operator lists.

## Layout Nesting Rules

Layout elements have specific nesting rules defined in `allowed_children`:

- **Container** → Row, Field, Card, Accordion, Tabs, Carousel, Table
- **Row** → Column, Field
- **Column** → Row, Field, Card, Accordion, Tabs, Carousel, Table
- **Card** → Row, Field, Table
- **Tabs** → Tab (children)
- **Accordion** → Accordion Item (children)
- **Carousel** → Container (as slides)
- **Table** → Table Header, Table Body, Table Footer (children)

See individual element schemas for complete nesting rules.

## Field Type Categories

### Input Collection
Fields that accept user input and store values.

### Display Only
Fields that display content but don't accept input (Code, Header, Paragraph, Image, Video).

### Calculation
Fields that compute values from other fields (Calculation).

### Advanced
Fields with complex behavior (Repeater, Rating Matrix, Signature, Location).

## Contributing

When adding new field types or layout elements:

1. Extend `BaseFieldType` or `BaseLayoutElementType`
2. Implement required abstract methods
3. Define `getConfigSchema()` for properties
4. Define `getAvailableValidationOptions()` for validation (fields only)
5. Register in config file
6. Run `php artisan slick-forms:export-schemas` to generate schema

The schema will be automatically generated from your implementation!

## Questions?

For more information about Slick Forms, see the main package documentation.
