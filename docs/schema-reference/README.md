# Schema Reference Documentation

Complete reference documentation for all Slick Forms field types and layout elements, auto-generated from JSON schemas.

## Field Types (32)

### Input Fields

- [Text Field](fields/text-field.md) - Single-line text input
- [Textarea Field](fields/textarea-field.md) - Multi-line text input
- [Email Field](fields/email-field.md) - Email input with validation
- [Phone Field](fields/phone-field.md) - Phone number input with country selector
- [Number Field](fields/number-field.md) - Numeric input with min/max/step
- [URL Field](fields/url-field.md) - URL input with preview
- [Password Field](fields/password-field.md) - Password input with visibility toggle
- [Hidden Field](fields/hidden-field.md) - Hidden field for storing values

### Selection Fields

- [Select Field](fields/select-field.md) - Dropdown selection (single/multiple)
- [Radio Field](fields/radio-field.md) - Radio button group
- [Checkbox Field](fields/checkbox-field.md) - Multiple checkboxes
- [Switch Field](fields/switch-field.md) - Toggle switch (on/off)
- [Tags Field](fields/tags-field.md) - Tag input with suggestions

### Date & Time Fields

- [Date Field](fields/date-field.md) - Date picker
- [Time Field](fields/time-field.md) - Time picker (12/24 hour)
- [Date Range Field](fields/date_range-field.md) - Start and end date selection

### File Upload Fields

- [File Field](fields/file-field.md) - Generic file upload
- [Image Field](fields/image-field.md) - Image upload/URL with preview
- [Video Field](fields/video-field.md) - Video upload/URL
- [PDF Embed Field](fields/pdf_embed-field.md) - Embedded PDF viewer

### Interactive Fields

- [Star Rating Field](fields/star_rating-field.md) - Star-based rating input
- [Slider Field](fields/slider-field.md) - Single-value slider
- [Range Field](fields/range-field.md) - Dual-handle range slider
- [Color Picker Field](fields/color_picker-field.md) - Color selection
- [Signature Field](fields/signature-field.md) - Canvas-based signature capture
- [Location Picker Field](fields/location-field.md) - Map-based location selection

### Advanced Fields

- [Calculation Field](fields/calculation-field.md) - Formula-based calculations
- [Repeater Field](fields/repeater-field.md) - Repeatable field groups
- [Rating Matrix Field](fields/rating_matrix-field.md) - Matrix-style rating grid

### Content & Display Fields

- [Header Field](fields/header-field.md) - Display heading text (H1-H6)
- [Paragraph Field](fields/paragraph-field.md) - Display HTML content
- [Code Field](fields/code-field.md) - Display formatted code

## Layout Elements (16)

### Layout Structure

- [Container Element](elements/container-element.md) - Top-level wrapper (fluid/fixed)
- [Row Element](elements/row-element.md) - Bootstrap grid row
- [Column Element](elements/column-element.md) - Responsive grid column

### Component Elements

- [Card Element](elements/card-element.md) - Visual grouping container
- [Accordion Element](elements/accordion-element.md) - Collapsible sections container
- [Accordion Item Element](elements/accordion_item-element.md) - Individual accordion section
- [Tabs Element](elements/tabs-element.md) - Tabbed interface container
- [Tab Element](elements/tab-element.md) - Individual tab pane
- [Carousel Element](elements/carousel-element.md) - Swiper.js carousel/slider (86+ options)
- [Carousel Slide Element](elements/carousel_slide-element.md) - Individual carousel slide

### Table Elements

- [Table Element](elements/table-element.md) - Semantic HTML table
- [Table Header Element](elements/table_header-element.md) - Table header (`<thead>`)
- [Table Body Element](elements/table_body-element.md) - Table body (`<tbody>`)
- [Table Footer Element](elements/table_footer-element.md) - Table footer (`<tfoot>`)
- [Table Row Element](elements/table_row-element.md) - Table row (`<tr>`)
- [Table Cell Element](elements/table_cell-element.md) - Table cell (`<td>` or `<th>`)

## Schema Structure

Each schema document includes:

1. **Overview** - Description and use cases
2. **Basic Information** - Type, icon, model, category
3. **Properties Reference** - All configurable properties organized by:
   - Database columns (stored in main table)
   - JSON options (stored in `options` column)
4. **Properties by Tab** - Organized by Properties Panel tabs:
   - Basic Tab - Core settings
   - Options Tab - Field/element-specific options
   - Validation Tab - Validation rules and messages
   - Style Tab - Visual styling options
   - Advanced Tab - Conditional logic and advanced features
5. **Validation Rules** - Available Laravel validation rules (fields only)
6. **Conditional Logic** - Supported operators and structure
7. **Usage Example** - PHP code examples
8. **JSON Schema** - Complete schema definition

## Property Storage

Properties are stored in two locations:

### Database Columns
Common properties stored directly in the table:
- `name` - Technical identifier
- `label` - Display label
- `placeholder` - Input placeholder text
- `help_text` - Help text description
- `is_required` - Whether field is required
- `validation_rules` - Array of Laravel validation rules
- `conditional_logic` - Visibility and validation conditions
- `element_id` - Optional HTML ID attribute
- `class` - Custom CSS classes
- `style` - Custom inline styles

### JSON Options Column
Field/element-specific settings stored in `options` JSON column:
- Configuration options (min, max, step, format, etc.)
- Display settings (rows, columns, colors, etc.)
- Feature toggles (show_preview, allow_custom, etc.)
- Complex configurations (formulas, templates, etc.)

## Conditional Logic

All fields support conditional logic for:
- **Visibility** - Show/hide based on other field values
- **Validation** - Conditionally apply validation rules

Supported operators vary by field type:
- **Comparison**: `equals`, `not_equals`, `greater_than`, `less_than`, `>=`, `<=`
- **String**: `contains`, `not_contains`, `starts_with`, `ends_with`, `regex`
- **Array**: `in`, `not_in`, `contains`, `not_contains`
- **Date**: `after`, `before`, `after_or_equal`, `before_or_equal`
- **Boolean**: `checked`, `unchecked`
- **General**: `is_empty`, `is_not_empty`

## Validation Rules

Fields support Laravel validation rules with customizable error messages:
- **Format**: Rule format string (e.g., `required`, `max:{value}`)
- **Value Type**: Input type for dynamic values (text, number, checkbox)
- **Custom Messages**: Override default validation messages

## Notes

- Documentation auto-generated from `/docs/schema/` JSON schemas
- Schemas define the Properties Panel configuration
- All properties have default values when not specified
- Tab organization determines UI layout in form builder
- Storage location (`column` vs `json_column`) determines database persistence

## Related Documentation

- [Field Type System](/docs/FIELD_TYPE_SYSTEM.md) - How to create custom field types
- [Layout Elements](/docs/LAYOUT_ELEMENTS.md) - Understanding layout structure
- [Conditional Logic](/docs/CONDITIONAL_LOGIC.md) - Advanced visibility rules
- [Validation](/docs/VALIDATION.md) - Form validation system
