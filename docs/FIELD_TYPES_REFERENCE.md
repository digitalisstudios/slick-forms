# Field Types Reference

**Complete catalog of all 32 field types in Slick Forms**

This guide provides comprehensive details on every field type available in Slick Forms, including configuration options, use cases, and examples.

---

## Table of Contents

- [Input Fields](#input-fields)
  - [Text](#text-field)
  - [Textarea](#textarea-field)
  - [Email](#email-field)
  - [Number](#number-field)
  - [Password](#password-field)
  - [Phone](#phone-field)
  - [URL](#url-field)
- [Selection Fields](#selection-fields)
  - [Select](#select-field)
  - [Radio](#radio-field)
  - [Checkbox](#checkbox-field)
  - [Switch](#switch-field)
  - [Tags](#tags-field)
- [Date & Time Fields](#date--time-fields)
  - [Date](#date-field)
  - [Time](#time-field)
  - [Date Range](#date-range-field)
- [Interactive Fields](#interactive-fields)
  - [Star Rating](#star-rating-field)
  - [Slider](#slider-field)
  - [Range](#range-field)
  - [Color Picker](#color-picker-field)
  - [File](#file-field)
- [Content Fields](#content-fields)
  - [Header](#header-field)
  - [Paragraph](#paragraph-field)
  - [Code](#code-field)
  - [Image](#image-field)
  - [Video](#video-field)
- [Advanced Fields](#advanced-fields)
  - [Calculation](#calculation-field)
  - [Repeater](#repeater-field)
  - [Hidden](#hidden-field)
  - [Signature Pad](#signature-pad-field) 🆕
  - [Location Picker](#location-picker-field) 🆕
  - [Rating Matrix](#rating-matrix-field) 🆕
  - [PDF Embed](#pdf-embed-field) 🆕

---

## Common Field Properties

All field types share the following database properties from the `CustomFormField` model. These properties apply to every field regardless of type:

### Database Fields

| Property | Type | Required | Default | Description |
|----------|------|----------|---------|-------------|
| `slick_form_id` | integer | Yes | - | ID of the form this field belongs to |
| `slick_form_page_id` | integer | No | `null` | ID of the form page this field belongs to (for multi-page forms) |
| `slick_form_layout_element_id` | integer | No | `null` | ID of the layout element this field is placed within (for structured layouts) |
| `parent_field_id` | integer | No | `null` | ID of the parent field (for nested fields like repeater children) |
| `field_type` | string | Yes | - | Type of field (e.g., 'text', 'email', 'select', 'checkbox') |
| `name` | string | Yes | - | Unique field identifier used as key in form submissions (alphanumeric and underscores only, e.g., 'email_address', 'phone_number') |
| `label` | string | No | - | Display label shown above the field |
| `placeholder` | string | No | `null` | Placeholder text shown inside empty input fields |
| `help_text` | string | No | `null` | Help text displayed below the field to guide users |
| `is_required` | boolean | No | `false` | Whether this field must be filled out before form submission |
| `element_id` | string | No | Auto-generated | HTML id attribute for the field (auto-generated as "fieldtype-field-123" if not provided) |
| `class` | string | No | `null` | Custom CSS classes to apply to the field wrapper (space-separated) |
| `style` | string | No | `null` | Custom inline CSS styles for the field wrapper (e.g., "margin-top: 20px;") |
| `show_label` | boolean | No | `true` | Whether to display the field label |
| `help_text_as_popover` | boolean | No | `false` | Display help text as a "?" popover icon instead of below the field |
| `order` | integer | No | Auto-generated | Display order of the field within its parent container |
| `validation_rules` | array (JSON) | No | `[]` | Array of Laravel validation rules (e.g., `['required', 'email', 'max:255']`) |
| `conditional_logic` | array (JSON) | No | `[]` | Conditional visibility and validation rules based on other field values |
| `options` | array (JSON) | No | `[]` | Field-type-specific configuration options (varies by field type - see individual field documentation below) |

**Example:**
```php
$form->fields()->create([
    'slick_form_id' => 1,
    'field_type' => 'text',
    'name' => 'company_name',
    'label' => 'Company Name',
    'placeholder' => 'Acme Corporation',
    'help_text' => 'Enter your registered business name',
    'is_required' => true,
    'show_label' => true,
    'validation_rules' => ['required', 'string', 'max:255'],
    'options' => [
        'field_size' => 'lg',
        'floating_label' => true,
    ],
]);
```

### Validation Rules

The `validation_rules` property accepts an array of Laravel validation rules that will be applied to the field's input value. Each field type supports different validation options based on the type of data it collects.

**Common validation rules include:**
- `required` - Field must be filled out
- `email` - Must be a valid email address
- `numeric` - Must be a number
- `min:value` - Minimum value/length
- `max:value` - Maximum value/length
- `regex:pattern` - Must match a regular expression
- `unique:table,column` - Must be unique in database
- And many more...

Each field type in this document lists its available validation options. For complete details on validation rules, conditional validation, custom error messages, and advanced validation techniques, see the [Validation Documentation](./CONDITIONAL_LOGIC.md#validation).

### Conditional Logic

The `conditional_logic` property allows you to show/hide fields or apply conditional validation based on the values of other fields in the form. This enables dynamic, intelligent forms that adapt to user input.

**Conditional logic supports:**
- **Field visibility** - Show/hide fields based on conditions
- **Conditional validation** - Apply validation rules only when conditions are met
- **Multiple conditions** - Combine conditions with AND/OR logic
- **Field-specific operators** - Different comparison operators for each field type

**Example:**
```php
'conditional_logic' => [
    'show_if' => [
        'conditions' => [
            [
                'target_field_id' => 'field_123',
                'operator' => 'equals',
                'value' => 'yes'
            ]
        ],
        'logic' => 'all' // 'all' (AND) or 'any' (OR)
    ]
]
```

For complete details on conditional logic, supported operators, field picker functionality, and advanced examples, see the [Conditional Logic Documentation](./CONDITIONAL_LOGIC.md).

---

## Input Fields

### Text Field

**Field Type:** `text`
**Icon:** 📝
**Category:** Basic input

**Description:**
Single-line text input for collecting short textual information. The most commonly used field type.

**Use Cases:**
- Names (first, last, full)
- Addresses
- Job titles
- Short descriptions
- Any single-line text data

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `mask_enabled` | string | No | ``false`` | Enable input masking to auto-format values as user types |
| `mask_type` | string | No | ``"none"`` | Preset mask format (phone, SSN, credit card, etc.) or "custom" for pattern |
| `mask_pattern` | string | No | ``""`` | Custom mask pattern: # = digit, A = letter, * = alphanumeric (e.g., "###-##-####") |

**Validation Options:**
- Required
- Min length
- Max length
- Pattern (regex)
- Unique

**Example:**
```php
$form->fields()->create([
    'field_type' => 'text',
    'label' => 'Company Name',
    'name' => 'company_name',
    'placeholder' => 'Acme Corporation',
    'is_required' => true,
    'validation_rules' => ['required', 'string', 'max:255'],
    'options' => [
        'floating_label' => true,
        'field_size' => 'lg',
        'mask_enabled' => true,
        'mask_type' => 'phone_us',
    ],
]);
```

---

### Textarea Field

**Field Type:** `textarea`
**Icon:** 📄
**Category:** Basic input

**Description:**
Multi-line text input for collecting longer textual content. Auto-resizes or has fixed height.

**Use Cases:**
- Comments
- Messages
- Descriptions
- Feedback
- Notes
- Long-form text

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `use_wysiwyg` | string | No | ``false`` | Render this textarea as a rich text editor |
| `rows` | string | No | ``4`` | Array of items to be rated (each row in the matrix) |

**Validation Options:**
- Required
- Min length
- Max length
- Pattern (regex)

**Example:**
```php
$form->fields()->create([
    'field_type' => 'textarea',
    'label' => 'Additional Comments',
    'name' => 'comments',
    'validation_rules' => ['required', 'string', 'max:500'],
    'options' => [
        'rows' => 5,
        'use_wysiwyg' => false,
    ],
]);
```

---

### Email Field

**Field Type:** `email`
**Icon:** ✉️
**Category:** Basic input

**Description:**
Email address input with built-in browser validation and server-side validation.

**Use Cases:**
- Contact forms
- Registration forms
- Newsletter signups
- Lead capture

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |

**Validation Options:**
- Required
- Email format
- Unique

**Example:**
```php
$form->fields()->create([
    'field_type' => 'email',
    'label' => 'Email Address',
    'name' => 'email',
    'placeholder' => 'you@example.com',
    'is_required' => true,
    'validation_rules' => ['required', 'email'],
    'options' => [
        'floating_label' => true,
    ],
]);
```

---

### Number Field

**Field Type:** `number`
**Icon:** 🔢
**Category:** Basic input

**Description:**
Numeric input with optional min/max values and step increment.

**Use Cases:**
- Age
- Quantity
- Price
- Score
- Any numeric data

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `min` | string | No | - | Minimum value |
| `max` | string | No | - | Maximum value |
| `step` | string | No | ``1`` | Increment step between values |
| `mask_enabled` | string | No | ``false`` | Enable input masking to auto-format values as user types |
| `mask_type` | string | No | ``"none"`` | Preset mask format (phone, SSN, credit card, etc.) or "custom" for pattern |
| `mask_pattern` | string | No | ``""`` | Custom mask pattern: # = digit, A = letter, * = alphanumeric (e.g., "###-##-####") |

**Validation Options:**
- Required
- Min value
- Max value
- Integer
- Numeric

**Example:**
```php
$form->fields()->create([
    'field_type' => 'number',
    'label' => 'Quantity',
    'name' => 'quantity',
    'validation_rules' => ['required', 'integer', 'min:1', 'max:100'],
    'options' => [
        'min' => 1,
        'max' => 100,
        'step' => 1,
        'field_size' => 'default',
    ],
]);
```

---

### Password Field

**Field Type:** `password`
**Icon:** 🔒
**Category:** Basic input

**Description:**
Password input with **real-time strength indicator** and show/hide toggle. Displays password strength (weak, medium, strong) as user types.

**Use Cases:**
- User registration
- Account creation
- Password reset
- Security settings

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `show_toggle` | string | No | ``true`` | Show eye icon to toggle password visibility |
| `show_strength` | string | No | ``true`` | Display real-time password strength indicator |
| `minimum_strength` | string | No | ``4`` | Minimum required password strength (0-5 scale, 0 = no requirement) |

**Validation Options:**
- Required
- Min length
- Max length
- Confirmed (with confirmation field)
- Pattern (regex)

**Example:**
```php
$form->fields()->create([
    'field_type' => 'password',
    'label' => 'Password',
    'name' => 'password',
    'validation_rules' => ['required', 'string', 'min:8', 'confirmed'],
    'options' => [
        'show_toggle' => true,
        'show_strength' => true,
        'minimum_strength' => 3,
    ],
]);
```

**Features:**
- ✅ Show/hide password toggle
- ✅ Real-time strength indicator (weak, medium, strong)
- ✅ Visual feedback with colors
- ✅ Works with browser autofill

---

### Phone Field

**Field Type:** `phone`
**Icon:** 📞
**Category:** Basic input

**Description:**
Phone number input with **country code selector**. Supports international phone numbers with flags and dial codes.

**Use Cases:**
- Contact information
- Emergency contacts
- Phone verification
- Support tickets

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `show_country_code` | string | No | ``true`` | Display country code dropdown with flags |
| `default_country` | string | No | ``"US"`` | Default country code for phone number formatting |
| `format` | string | No | ``"international"`` | Color format: "hex", "rgb", or "hsl" |
| `mask_enabled` | string | No | ``false`` | Enable input masking to auto-format values as user types |
| `mask_type` | string | No | ``"none"`` | Preset mask format (phone, SSN, credit card, etc.) or "custom" for pattern |
| `mask_pattern` | string | No | ``""`` | Custom mask pattern: # = digit, A = letter, * = alphanumeric (e.g., "###-##-####") |

**Supported Countries (13):**
- United States (+1)
- Canada (+1)
- United Kingdom (+44)
- Australia (+61)
- France (+33)
- Germany (+49)
- India (+91)
- Japan (+81)
- China (+86)
- Brazil (+55)
- Mexico (+52)
- Spain (+34)
- Italy (+39)

**Validation Options:**
- Required
- Phone format
- Min length
- Max length

**Example:**
```php
$form->fields()->create([
    'field_type' => 'phone',
    'label' => 'Phone Number',
    'name' => 'phone',
    'validation_rules' => ['required', 'string'],
    'options' => [
        'default_country' => 'us',
    ],
]);
```

---

### URL Field

**Field Type:** `url`
**Icon:** 🔗
**Category:** Basic input

**Description:**
URL input with **preview button** and automatic validation. Shows link icon and validates URL format.

**Use Cases:**
- Website links
- Social media profiles
- Portfolio URLs
- External resources

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `show_preview` | string | No | ``false`` | Display preview button to open and preview the entered URL |
| `open_in_new_tab` | string | No | ``true`` | Whether preview button opens URL in new tab (vs same tab) |
| `mask_enabled` | string | No | ``true`` | Enable input masking to auto-format values as user types |
| `mask_type` | string | No | ``"https_only"`` | Preset mask format (phone, SSN, credit card, etc.) or "custom" for pattern |
| `mask_pattern` | string | No | ``""`` | Custom mask pattern: # = digit, A = letter, * = alphanumeric (e.g., "###-##-####") |

**Validation Options:**
- Required
- URL format
- Active URL (checks if URL is reachable)

**Example:**
```php
$form->fields()->create([
    'field_type' => 'url',
    'label' => 'Portfolio Website',
    'name' => 'portfolio_url',
    'validation_rules' => ['url'],
    'options' => [
        'show_preview' => true,
        'open_in_new_tab' => true,
    ],
]);
```

**Features:**
- ✅ Preview button to open URL in new tab
- ✅ Auto-validation of URL format
- ✅ Link icon indicator

---

## Selection Fields

### Select Field

**Field Type:** `select`
**Icon:** 📋
**Category:** Selection

**Description:**
Dropdown select field with **Tom Select v2.3.1** integration. Supports single or multiple selection, searchable options, and keyboard navigation.

**Use Cases:**
- Country/state selection
- Category selection
- Multi-select tags
- Dropdowns with many options

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `option_source` | string | Yes | ``"static"`` | Source for select options: "static", "url" (API endpoint), or "model" (Eloquent query) |
| `values` | string | Yes | ``[{"label":"Option 1","value":"1","default":false},{"label":"Option 2","value":"2","default":false},{"label":"Option 3","value":"3","default":false}]`` | Array of option values and labels for selection |
| `source_url` | string | No | - | API endpoint URL to fetch options from |
| `headers` | string | No | - | HTTP headers to send with API request (JSON object) |
| `value_key` | string | No | ``"value"`` | JSON property name to use as option value |
| `label_key` | string | No | ``"label"`` | JSON property name to use as option label |
| `model_class` | string | No | - | Fully qualified Eloquent model class name (e.g., "App\Models\Country") |
| `value_column` | string | No | ``"id"`` | Database column to use as option value |
| `label_column` | string | No | ``"name"`` | Database column to use as option label |
| `scope` | string | No | - | Eloquent query scope to apply when fetching options |
| `where_conditions` | string | No | - | Additional where conditions for Eloquent query (JSON object) |
| `multiple` | string | No | ``false`` | Allow selecting multiple options (multi-select) |
| `searchable` | string | No | ``false`` | Enable search/filter functionality in select dropdown |
| `search_placeholder` | string | No | ``"Search options..."`` | Placeholder text for the search input |

**Validation Options:**
- Required
- In (must be one of the options)
- Array (for multiple select)

**Example:**
```php
$form->fields()->create([
    'field_type' => 'select',
    'label' => 'Country',
    'name' => 'country',
    'validation_rules' => ['required', 'in:US,CA,UK'],
    'options' => [
        'values' => [
            ['label' => 'Option 1', 'value' => '1'],
            ['label' => 'Option 2', 'value' => '2']
        ],
        'multiple' => false,
        'searchable' => true,
    ],
]);
```

**Tom Select Features:**
- ✅ Keyboard navigation (arrow keys, Enter, Escape)
- ✅ Search/filter options
- ✅ Multi-select with tags
- ✅ Custom styling
- ✅ Dropdown positioning

---

### Radio Field

**Field Type:** `radio`
**Icon:** ⚪
**Category:** Selection

**Description:**
Radio button group for single selection. Supports inline or stacked layout.

**Use Cases:**
- Yes/No questions
- Gender selection
- Single choice from 2-7 options
- Preference selection

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `option_source` | string | Yes | ``"static"`` | Source for select options: "static", "url" (API endpoint), or "model" (Eloquent query) |
| `values` | string | Yes | ``[{"label":"Option 1","value":"1","default":false},{"label":"Option 2","value":"2","default":false},{"label":"Option 3","value":"3","default":false}]`` | Array of option values and labels for selection |
| `source_url` | string | No | - | API endpoint URL to fetch options from |
| `headers` | string | No | - | HTTP headers to send with API request (JSON object) |
| `value_key` | string | No | ``"value"`` | JSON property name to use as option value |
| `label_key` | string | No | ``"label"`` | JSON property name to use as option label |
| `model_class` | string | No | - | Fully qualified Eloquent model class name (e.g., "App\Models\Country") |
| `value_column` | string | No | ``"id"`` | Database column to use as option value |
| `label_column` | string | No | ``"name"`` | Database column to use as option label |
| `scope` | string | No | - | Eloquent query scope to apply when fetching options |
| `where_conditions` | string | No | - | Additional where conditions for Eloquent query (JSON object) |
| `layout` | string | No | ``"vertical"`` | Layout orientation: "vertical" (stacked) or "horizontal" (inline) |

**Validation Options:**
- Required
- In (must be one of the options)

**Example:**
```php
$form->fields()->create([
    'field_type' => 'radio',
    'label' => 'Preferred Contact Method',
    'name' => 'contact_method',
    'validation_rules' => ['required', 'in:email,phone'],
    'options' => [
        'values' => [
            ['label' => 'Yes', 'value' => 'yes'],
            ['label' => 'No', 'value' => 'no']
        ],
        'layout' => 'horizontal',
    ],
]);
```

---

### Checkbox Field

**Field Type:** `checkbox`
**Icon:** ☑️
**Category:** Selection

**Description:**
Checkbox group for multiple selection. Supports inline or stacked layout.

**Use Cases:**
- Multiple selections
- Interests/hobbies
- Terms & conditions
- Feature selection

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `option_source` | string | Yes | ``"static"`` | Source for select options: "static", "url" (API endpoint), or "model" (Eloquent query) |
| `values` | string | Yes | ``[{"label":"Option 1","value":"1","default":false},{"label":"Option 2","value":"2","default":false},{"label":"Option 3","value":"3","default":false}]`` | Array of option values and labels for selection |
| `source_url` | string | No | - | API endpoint URL to fetch options from |
| `headers` | string | No | - | HTTP headers to send with API request (JSON object) |
| `value_key` | string | No | ``"value"`` | JSON property name to use as option value |
| `label_key` | string | No | ``"label"`` | JSON property name to use as option label |
| `model_class` | string | No | - | Fully qualified Eloquent model class name (e.g., "App\Models\Country") |
| `value_column` | string | No | ``"id"`` | Database column to use as option value |
| `label_column` | string | No | ``"name"`` | Database column to use as option label |
| `scope` | string | No | - | Eloquent query scope to apply when fetching options |
| `where_conditions` | string | No | - | Additional where conditions for Eloquent query (JSON object) |
| `layout` | string | No | ``"vertical"`` | Layout orientation: "vertical" (stacked) or "horizontal" (inline) |

**Validation Options:**
- Required
- Array
- Min (minimum selections)
- Max (maximum selections)

**Example:**
```php
$form->fields()->create([
    'field_type' => 'checkbox',
    'label' => 'Select All That Apply',
    'name' => 'interests',
    'validation_rules' => ['array', 'min:1'],
    'options' => [
        'values' => [
            ['label' => 'Newsletter', 'value' => 'newsletter'],
            ['label' => 'Updates', 'value' => 'updates']
        ],
        'layout' => 'vertical',
    ],
]);
```

---

### Switch Field

**Field Type:** `switch`
**Icon:** 🎚️
**Category:** Selection

**Description:**
Bootstrap 5 toggle switch with customizable On/Off labels. Modern alternative to checkbox for boolean values.

**Use Cases:**
- Enable/disable features
- Yes/No questions
- Settings toggles
- Opt-in/opt-out

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `show_labels` | string | No | ``true`` | Display on/off labels next to the switch toggle |
| `on_label` | string | No | ``"On"`` | Label text shown when switch is ON |
| `off_label` | string | No | ``"Off"`` | Label text shown when switch is OFF |

**Validation Options:**
- Required
- Boolean
- Accepted (must be true)

**Example:**
```php
$form->fields()->create([
    'field_type' => 'switch',
    'label' => 'Subscribe to Newsletter',
    'name' => 'newsletter_subscribe',
    'options' => [
        'on_label' => 'Enabled',
        'off_label' => 'Disabled',
    ],
]);
```

---

### Tags Field

**Field Type:** `tags`
**Icon:** 🏷️
**Category:** Selection

**Description:**
Dynamic tag input allowing users to add multiple text values. Great for flexible multi-value inputs.

**Use Cases:**
- Skills
- Keywords
- Tags/categories
- Free-form multiple values

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `max_tags` | string | No | - | Maximum number of tags that can be added |

**Validation Options:**
- Required
- Array
- Min (minimum tags)
- Max (maximum tags)

**Example:**
```php
$form->fields()->create([
    'field_type' => 'tags',
    'label' => 'Skills',
    'name' => 'skills',
    'placeholder' => 'Type and press Enter',
    'validation_rules' => ['array', 'min:3', 'max:10'],
    'options' => [
        'max_tags' => 5,
    ],
]);
```

---

## Date & Time Fields

### Date Field

**Field Type:** `date`
**Icon:** 📅
**Category:** Date/Time

**Description:**
Date picker with **Flatpickr v4.6.13** integration. Supports multiple date formats, min/max dates, and inline calendar.

**Use Cases:**
- Birth dates
- Event dates
- Deadlines
- Appointments
- Any date selection

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `enable_flatpickr` | string | No | ``true`` | Use Flatpickr date picker library for enhanced UI |
| `date_format` | string | No | ``"m\/d\/Y"`` | Date display format (e.g., "Y-m-d", "m/d/Y") |
| `min_date` | string | No | - | Earliest selectable date |
| `max_date` | string | No | - | Latest selectable date |
| `enable_time` | string | No | ``false`` | Include time picker in addition to date picker |

**Supported Formats:**
- `Y-m-d` - 2024-01-27
- `m/d/Y` - 01/27/2024
- `d-m-Y` - 27-01-2024
- Custom formats via Flatpickr

**Validation Options:**
- Required
- Date format
- Before date
- After date

**Example:**
```php
$form->fields()->create([
    'field_type' => 'date',
    'label' => 'Start Date',
    'name' => 'start_date',
    'validation_rules' => ['required', 'date', 'after:today'],
    'options' => [
        'date_format' => 'Y-m-d',
        'enable_flatpickr' => true,
    ],
]);
```

**Flatpickr Features:**
- ✅ Keyboard navigation
- ✅ Multiple date formats
- ✅ Min/max date restrictions
- ✅ Inline calendar mode
- ✅ Localization support

---

### Time Field

**Field Type:** `time`
**Icon:** ⏰
**Category:** Date/Time

**Description:**
Time picker with 12/24-hour format support and step intervals.

**Use Cases:**
- Appointment times
- Meeting schedules
- Time tracking
- Business hours

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `enable_24_hour` | string | No | ``false`` | Use 24-hour time format instead of 12-hour AM/PM |
| `show_seconds` | string | No | ``false`` | Include seconds in time picker |

**Validation Options:**
- Required
- Date format
- Before time
- After time

**Example:**
```php
$form->fields()->create([
    'field_type' => 'time',
    'label' => 'Meeting Time',
    'name' => 'meeting_time',
    'validation_rules' => ['required'],
    'options' => [
        'enable_24_hour' => true,
        'show_seconds' => false,
    ],
]);
```

---

### Date Range Field

**Field Type:** `date_range`
**Icon:** 📅📅
**Category:** Date/Time

**Description:**
Select start and end dates with Flatpickr. Perfect for date ranges like trip dates, event duration, etc.

**Use Cases:**
- Trip dates
- Event duration
- Date range filters
- Booking periods

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |

**Validation Options:**
- Required
- Array (returns ['start' => date, 'end' => date])

**Example:**
```php
$form->fields()->create([
    'field_type' => 'date_range',
    'label' => 'Vacation Dates',
    'name' => 'vacation_dates',
    'validation_rules' => ['required', 'array'],
    'options' => [
        'date_format' => 'Y-m-d',
    ],
]);
```

---

## Interactive Fields

### Star Rating Field

**Field Type:** `star_rating`
**Icon:** ⭐
**Category:** Interactive

**Description:**
Star rating input with configurable number of stars. Visual feedback with filled/empty stars.

**Use Cases:**
- Product reviews
- Service ratings
- Satisfaction surveys
- Experience feedback

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `max_stars` | string | No | ``"5"`` | Maximum number of stars (1-10) |

**Validation Options:**
- Required
- Min value
- Max value
- Numeric

**Example:**
```php
$form->fields()->create([
    'field_type' => 'star_rating',
    'label' => 'How would you rate your experience?',
    'name' => 'experience_rating',
    'validation_rules' => ['required', 'numeric', 'min:1', 'max:5'],
    'options' => [
        'max_stars' => 5,
        'allow_half' => true,
    ],
]);
```

---

### Slider Field

**Field Type:** `slider`
**Icon:** 🎚️
**Category:** Interactive

**Description:**
Horizontal slider for selecting numeric values with visual feedback.

**Use Cases:**
- Budget selection
- Age ranges
- Satisfaction levels
- Numeric preferences

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `min` | string | No | ``"0"`` | Minimum value |
| `max` | string | No | ``"100"`` | Maximum value |
| `step` | string | No | ``"1"`` | Increment step between values |
| `show_value` | string | No | ``true`` | Display current value next to slider |

**Validation Options:**
- Required
- Numeric
- Min value
- Max value

**Example:**
```php
$form->fields()->create([
    'field_type' => 'slider',
    'label' => 'Project Budget ($)',
    'name' => 'project_budget',
    'validation_rules' => ['required', 'numeric', 'min:1000', 'max:50000'],
    'options' => [
        'min' => 0,
        'max' => 100,
        'step' => 5,
        'show_value' => true,
    ],
]);
```

---

### Range Field

**Field Type:** `range`
**Icon:** ↔️
**Category:** Interactive

**Description:**
**Dual slider** for selecting min-max range with step increment configuration. Two handles for start and end values.

**Use Cases:**
- Price ranges
- Date ranges (numeric)
- Age ranges
- Min/max selections

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `min` | string | No | ``"0"`` | Minimum value |
| `max` | string | No | ``"100"`` | Maximum value |
| `step` | string | No | ``"1"`` | Increment step between values |

**Validation Options:**
- Required
- Array (returns ['min' => value, 'max' => value])

**Example:**
```php
$form->fields()->create([
    'field_type' => 'range',
    'label' => 'Price Range ($)',
    'name' => 'price_range',
    'validation_rules' => ['required', 'array'],
    'options' => [
        'min' => 0,
        'max' => 100,
        'step' => 10,
    ],
]);
```

**Features:**
- ✅ Two handles (min and max)
- ✅ Visual range indicator
- ✅ Live value display
- ✅ Step increment control

---

### Color Picker Field

**Field Type:** `color_picker`
**Icon:** 🎨
**Category:** Interactive

**Description:**
Color picker with hex value input and visual color palette.

**Use Cases:**
- Theme customization
- Design preferences
- Brand colors
- UI customization

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `default_color` | string | No | ``"#000000"`` | Pre-selected color value |
| `show_hex` | string | No | ``true`` | Display text input for hex color codes |

**Validation Options:**
- Required
- Hex color format

**Example:**
```php
$form->fields()->create([
    'field_type' => 'color_picker',
    'label' => 'Accent Color',
    'name' => 'accent_color',
    'validation_rules' => ['required', 'regex:/^#[0-9A-Fa-f],
    'options' => [
        'format' => 'hex',
    ],
]);
```

---

### File Field

**Field Type:** `file`
**Icon:** 📎
**Category:** Interactive

**Description:**
File upload with **drag-and-drop interface**, visual file preview cards, and progress indicators.

**Use Cases:**
- Document uploads
- Resume/CV
- Certificates
- Any file attachments

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `accepted_types` | string | No | - | File extensions or MIME types (comma-separated) |
| `max_size` | string | No | ``"10240"`` | Maximum file size in kilobytes |
| `multiple` | string | No | ``false`` | Allow selecting multiple options (multi-select) |
| `enable_drag_drop` | string | No | ``true`` | Allow drag and drop file uploads |
| `show_preview` | string | No | ``true`` | Display preview button to open and preview the entered URL |

**Validation Options:**
- Required
- File
- Mimes (file types)
- Max size

**Example:**
```php
$form->fields()->create([
    'field_type' => 'file',
    'label' => 'Resume',
    'name' => 'resume',
    'validation_rules' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
    'options' => [
        'max_size' => 5120,
        'allowed_extensions' => 'pdf,doc,docx',
    ],
]);
```

**Features:**
- ✅ Drag-and-drop upload
- ✅ File preview cards
- ✅ Progress indicators
- ✅ Multiple file type support
- ✅ File size validation

---

## Content Fields

### Header Field

**Field Type:** `header`
**Icon:** #️⃣
**Category:** Content (Display-only)

**Description:**
Display heading text with configurable heading level (h1-h6). Used for form section headers.

**Use Cases:**
- Section dividers
- Form organization
- Visual hierarchy
- Instructions headers

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `heading_level` | string | No | ``"h3"`` | Heading Level |
| `class_name` | string | No | - | Additional CSS classes for styling |

**Example:**
```php
$form->fields()->create([
    'field_type' => 'header',
    'label' => 'Contact Details',
    'name' => 'header_field',
    'options' => [
        'header_size' => 'h2',
        'text_alignment' => 'left',
    ],
]);
```

**Note:** Header fields don't collect data - they're display-only.

---

### Paragraph Field

**Field Type:** `paragraph`
**Icon:** 📄
**Category:** Content (Display-only)

**Description:**
Display formatted text content with **Quill v1.3.6 WYSIWYG editor** for rich text editing in builder.

**Use Cases:**
- Instructions
- Disclaimers
- Descriptions
- Help text blocks
- Terms & conditions

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `content` | string | No | - | Use the WYSIWYG editor to format your paragraph content |

**Example:**
```php
$form->fields()->create([
    'field_type' => 'paragraph',
    'label' => 'Privacy Notice',
    'name' => 'paragraph_field',
    'options' => [
        'text_alignment' => 'left',
    ],
]);
```

**Quill Editor Features:**
- ✅ Bold, italic, underline
- ✅ Lists (ordered, unordered)
- ✅ Links
- ✅ Clean HTML output

**Note:** Paragraph fields don't collect data - they're display-only.

---

### Code Field

**Field Type:** `code`
**Icon:** 💻
**Category:** Content (Display-only)

**Description:**
Display or execute code (HTML, CSS, JavaScript) with **Ace Editor v1.32.2** for syntax highlighting in builder.

**Use Cases:**
- Custom HTML blocks
- Embedded widgets
- JavaScript snippets
- CSS styling
- Tracking codes

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `code_content` | string | No | - | Enter HTML, CSS, or JavaScript code to display/execute |

**Example:**
```php
$form->fields()->create([
    'field_type' => 'code',
    'label' => 'Tracking Code',
    'name' => 'code_field',
    'options' => [
        'language' => 'php',
        'show_line_numbers' => true,
    ],
]);
```

**Ace Editor Features:**
- ✅ Syntax highlighting
- ✅ Autocomplete (basic, live, snippets)
- ✅ HTML, CSS, JavaScript support
- ✅ Code folding
- ✅ Line numbers

**Note:** Code fields don't collect data - they render/execute the code content.

---

### Image Field

**Field Type:** `image`
**Icon:** 🖼️
**Category:** Content

**Description:**
Display-only field that shows images in forms. Supports both URL-based images and file uploads. Does not collect user input.

**Use Cases:**
- Display logos or branding
- Show instructional diagrams
- Add visual context to forms
- Display reference images

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `input_mode` | string | No | ``"url"`` | Input Mode |
| `image_url` | string | No | - | Image URL |
| `image_upload` | string | No | - | Upload Image |
| `image_preview` | string | No | - | Preview |
| `alt_text` | string | No | - | Alternative text for accessibility |
| `width` | string | No | - | Canvas width in pixels |
| `height` | string | No | - | Canvas height in pixels |
| `aspect_ratio` | string | No | ``""`` | Force a specific aspect ratio |
| `object_fit` | string | No | ``""`` | How the image should fit within its container |

**Validation Options:**
- Required
- Image
- Mimes (image types)
- Max size
- Dimensions

**Example:**
```php
$form->fields()->create([
    'field_type' => 'image',
    'label' => 'Profile Picture',
    'name' => 'avatar',
    'validation_rules' => ['required', 'image', 'max:2048', 'dimensions:max_width=1000,max_height=1000'],
    'options' => [
        'max_size' => 2048,
        'allowed_extensions' => 'jpg,png,gif',
    ],
]);
```

**Features:**
- ✅ Image preview
- ✅ Drag-and-drop
- ✅ Dimension validation
- ✅ Supported formats: jpg, png, gif, webp

---

### Video Field

**Field Type:** `video`
**Icon:** 🎥
**Category:** Content

**Description:**
Display-only field that embeds videos in forms. Supports direct video URLs and embed codes (YouTube, Vimeo, etc.). Does not collect user input.

**Use Cases:**
- Display instructional videos
- Show product demonstrations
- Embed tutorial content
- Add video context to forms

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `video_type` | string | No | ``"url"`` | Video Type |
| `video_url` | string | No | - | Enter a direct video URL or paste embed code from YouTube/Vimeo |
| `width` | string | No | ``"100%"`` | Canvas width in pixels |
| `height` | string | No | ``"400px"`` | Canvas height in pixels |

**Validation Options:**
- Required
- File
- Mimes (video types)
- Max size

**Example:**
```php
$form->fields()->create([
    'field_type' => 'video',
    'label' => 'Demo Video',
    'name' => 'demo_video',
    'validation_rules' => ['file', 'mimes:mp4,mov', 'max:51200'],
    'options' => [
        'max_size' => 10240,
        'allowed_extensions' => 'mp4,webm',
    ],
]);
```

---

## Advanced Fields

### Calculation Field

**Field Type:** `calculation`
**Icon:** 🧮
**Category:** Advanced

**Description:**
Automatically calculate values based on other field values using formulas. Supports arithmetic operations and references to other fields.

**Use Cases:**
- Total price calculations
- Subtotals
- Percentage calculations
- Derived values

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `formula` | string | Yes | - | JavaScript expression to calculate value from other fields |
| `display_as` | string | No | ``"number"`` | Display Format |
| `decimal_places` | string | No | ``2`` | Number of decimal places to display |
| `prefix` | string | No | - | Text to show before the value (e.g., $) |
| `suffix` | string | No | - | Text to show after the value (e.g., %) |
| `display_mode` | string | No | ``"visible"`` | Hidden fields will store the value but not display it |

**Supported Operators:**
- Addition: `+`
- Subtraction: `-`
- Multiplication: `*`
- Division: `/`
- Parentheses: `()`

**Example:**
```php
$form->fields()->create([
    'field_type' => 'calculation',
    'label' => 'Grand Total',
    'name' => 'grand_total',
    'options' => [
        'formula' => 'field_1 + field_2',
        'precision' => 2,
    ],
]);
```

**Features:**
- ✅ Real-time calculation
- ✅ Reference other fields by name
- ✅ Complex formulas with parentheses
- ✅ Formatted output with prefix/suffix

---

### Repeater Field

**Field Type:** `repeater`
**Icon:** 🔁
**Category:** Advanced

**Description:**
Repeatable group of fields. Users can add/remove field groups dynamically. Perfect for multiple entries of the same data structure.

**Use Cases:**
- Multiple addresses
- Family members
- Work experience entries
- Product variations
- Line items

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `min_instances` | string | No | ``1`` | Minimum number of instances required |
| `max_instances` | string | No | ``10`` | Maximum number of instances allowed |
| `initial_instances` | string | No | ``1`` | Number of instances to show initially |
| `add_button_text` | string | No | ``"Add Another"`` | Text for "Add Row" button |
| `remove_button_text` | string | No | ``"Remove"`` | Text for "Remove Row" button |
| `allow_reorder` | string | No | ``true`` | Allow users to drag-and-drop to reorder instances |
| `layout_style` | string | No | ``"card"`` | Visual style for instances |
| `show_instance_number` | string | No | ``true`` | Display instance number in header (e.g., "Contact #1") |

### How Repeater Fields Work

Repeater fields are containers that allow users to dynamically add/remove instances of a group of fields. Child fields and layout elements must be attached to the repeater using the `parent_field_id` property.

**Key Concepts:**
- The repeater field itself doesn't collect data - it's a container
- Child fields are created separately and linked via `parent_field_id`
- All child fields will be repeated for each instance
- Submitted data is an array of arrays, with each instance containing the child field values

**Example:**
```php
// Step 1: Create the repeater field
$repeater = $form->fields()->create([
    'field_type' => 'repeater',
    'label' => 'Emergency Contacts',
    'name' => 'emergency_contacts',
    'options' => [
        'min_instances' => 1,
        'max_instances' => 5,
        'initial_instances' => 1,
        'add_button_text' => 'Add Another Contact',
        'remove_button_text' => 'Remove Contact',
    ],
]);

// Step 2: Create child fields and attach them to the repeater
$form->fields()->create([
    'field_type' => 'text',
    'label' => 'Contact Name',
    'name' => 'contact_name',
    'parent_field_id' => $repeater->id, // Link to parent repeater
    'is_required' => true,
]);

$form->fields()->create([
    'field_type' => 'phone',
    'label' => 'Contact Phone',
    'name' => 'contact_phone',
    'parent_field_id' => $repeater->id, // Link to parent repeater
    'is_required' => true,
]);

$form->fields()->create([
    'field_type' => 'select',
    'label' => 'Relationship',
    'name' => 'relationship',
    'parent_field_id' => $repeater->id, // Link to parent repeater
    'options' => [
        'values' => [
            ['label' => 'Spouse', 'value' => 'spouse'],
            ['label' => 'Parent', 'value' => 'parent'],
            ['label' => 'Sibling', 'value' => 'sibling'],
            ['label' => 'Friend', 'value' => 'friend'],
        ],
    ],
]);
```

**Submitted Data Structure:**
```json
{
  "emergency_contacts": [
    {
      "contact_name": "John Doe",
      "contact_phone": "+1 (555) 123-4567",
      "relationship": "spouse"
    },
    {
      "contact_name": "Jane Smith",
      "contact_phone": "+1 (555) 987-6543",
      "relationship": "parent"
    }
  ]
}
```

**Features:**
- ✅ Add/remove instances dynamically
- ✅ Min/max instance limits
- ✅ Support any field type or layout element as children
- ✅ Drag-and-drop reordering (optional)
- ✅ Nested data structure in submissions

---

### Hidden Field

**Field Type:** `hidden`
**Icon:** 👁️‍🗨️
**Category:** Advanced

**Description:**
Hidden input field for passing data not visible to users. Useful for tracking, UTM parameters, or pre-filled values.

**Use Cases:**
- UTM parameters
- Referral codes
- Tracking IDs
- Hidden metadata

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `default_value` | string | No | - | Value to submit with form (not visible to users) |

**Example:**
```php
$form->fields()->create([
    'field_type' => 'hidden',
    'label' => 'Product Comparison',
    'name' => 'referral_code',
    'options' => [
        'default_value' => 'hidden_tracking_value',
    ],
]);
```

---

### Signature Pad Field 🆕

**Field Type:** `signature`
**Icon:** ✍️
**Category:** Advanced input
**Added:** v2.0.0

**Description:**
Canvas-based signature capture field powered by Signature Pad v4.1.7. Allows users to draw their signature using mouse, touch, or stylus.

**Use Cases:**
- Legal agreements and contracts
- Consent forms
- Authorization documents
- Digital signature collection
- Artist signatures

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `canvas_width` | string | No | ``"500"`` | Width of the signature canvas (supports px, %, rem, em, etc.) |
| `canvas_height` | string | No | ``"200"`` | Height of the signature canvas (supports px, %, rem, em, etc.) |
| `pen_color` | string | No | ``"#000000"`` | Color of signature pen stroke |
| `background_color` | string | No | ``"#ffffff"`` | Background color of signature canvas |

**Validation Options:**
- Required
- String (validates base64 data)

**Stored Value:**
The signature is stored as a base64-encoded data URL:
```
data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...
```

**Example:**
```php
$form->fields()->create([
    'field_type' => 'signature',
    'label' => 'Sign Below',
    'name' => 'user_signature',
    'validation_rules' => ['required', 'string'],
    'options' => [
        'canvas_width' => '500',
        'canvas_height' => '200',
        'pen_color' => '#000000',
        'background_color' => '#ffffff',
    ],
]);
```

**Features:**
- ✅ Clear signature button
- ✅ Responsive canvas sizing
- ✅ Touch and mouse support
- ✅ Retina display support
- ✅ Stores as PNG image data
- ✅ CDN-loaded library (no local dependencies)

**Rendering Signature:**
To display a saved signature:
```blade
<img src="{{ $submission->getFieldValue('user_signature') }}" alt="Signature" />
```

---

### Location Picker Field 🆕

**Field Type:** `location`
**Icon:** 📍
**Category:** Advanced input
**Added:** v2.0.0

**Description:**
Interactive map field powered by Leaflet v1.9.4 and OpenStreetMap. Users can click on a map to select a location, drag the marker for fine-tuning, or search for addresses using the Nominatim geocoding API.

**Use Cases:**
- Store locations
- Event venues
- Delivery addresses
- Meeting points
- Service areas
- Property locations

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `default_lat` | string | No | ``37.7749`` | Default latitude for map center |
| `default_lng` | string | No | ``-122.4194`` | Default longitude for map center |
| `default_zoom` | string | No | ``13`` | Map zoom level (1 = world view, 18 = street level) |
| `map_height` | string | No | ``400`` | Height of the map in pixels |
| `enable_search` | string | No | ``true`` | Show search box to find locations by address |
| `show_coordinates` | string | No | ``true`` | Display latitude and longitude below the map |

**Validation Options:**
- Required
- JSON (validates location data structure)

**Stored Value:**
Location data is stored as JSON:
```json
{
  "lat": 37.7749,
  "lng": -122.4194,
  "address": "San Francisco, CA, USA"
}
```

**Example:**
```php
$form->fields()->create([
    'field_type' => 'location',
    'label' => 'Select Meeting Location',
    'name' => 'meeting_location',
    'validation_rules' => ['required', 'json'],
    'options' => [
        'default_lat' => 37.7749,
        'default_lng' => -122.4194,
        'zoom' => 12,
        'search_enabled' => true,
    ],
]);
```

**Features:**
- ✅ Click-to-place marker
- ✅ Draggable marker for fine-tuning
- ✅ Address search with autocomplete
- ✅ Displays latitude/longitude coordinates
- ✅ OpenStreetMap tiles (free, no API key required)
- ✅ Responsive map container
- ✅ CDN-loaded library (no local dependencies)

**Accessing Location Data:**
```php
$location = json_decode($submission->getFieldValue('meeting_location'), true);
$lat = $location['lat'];
$lng = $location['lng'];
$address = $location['address'];

// Generate Google Maps link
$mapsUrl = "https://www.google.com/maps?q={$lat},{$lng}";
```

---

### Rating Matrix Field 🆕

**Field Type:** `rating_matrix`
**Icon:** ⭐
**Category:** Advanced input
**Added:** v2.0.0

**Description:**
Multi-item rating field that allows users to rate multiple items on the same scale. Perfect for surveys, product comparisons, and feedback forms.

**Use Cases:**
- Customer satisfaction surveys
- Product feature ratings
- Employee performance reviews
- Course evaluations
- Service quality assessments

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `rows` | string | No | ``[{"value":"row1","label":"Item 1"},{"value":"row2","label":"Item 2"},{"value":"row3","label":"Item 3"}]`` | Array of items to be rated (each row in the matrix) |
| `columns` | string | No | ``[{"value":"1","label":"1"},{"value":"2","label":"2"},{"value":"3","label":"3"},{"value":"4","label":"4"},{"value":"5","label":"5"}]`` | Array of rating scale options (each column in the matrix) |
| `input_type` | string | No | ``"radio"`` | How users select ratings: "radio" (one per row) or "checkbox" (multiple per row) |
| `allow_na` | string | No | ``false`` | Add "Not Applicable" option for each row |

**Validation Options:**
- Required
- JSON (validates matrix data structure)

**Stored Value:**
Ratings are stored as JSON object:
```json
{
  "support": "4",
  "quality": "3",
  "price": "2"
}
```

**Example:**
```php
$form->fields()->create([
    'field_type' => 'rating_matrix',
    'label' => 'Rate Your Experience',
    'name' => 'experience_ratings',
    'validation_rules' => ['required', 'json'],
    'options' => [
        'rows' => [
            ['label' => 'Quality', 'value' => 'quality'],
            ['label' => 'Service', 'value' => 'service'],
            ['label' => 'Value', 'value' => 'value']
        ],
        'columns' => [
            ['label' => 'Poor', 'value' => '1'],
            ['label' => 'Fair', 'value' => '2'],
            ['label' => 'Good', 'value' => '3'],
            ['label' => 'Very Good', 'value' => '4'],
            ['label' => 'Excellent', 'value' => '5']
        ],
        'input_type' => 'radio',
    ],
]);
```

**Features:**
- ✅ Radio button or dropdown input types
- ✅ Optional "N/A" column
- ✅ Responsive table layout
- ✅ Custom row and column labels
- ✅ Validation per row
- ✅ JSON storage for easy processing

**Accessing Matrix Data:**
```php
$ratings = json_decode($submission->getFieldValue('experience_ratings'), true);

foreach ($ratings as $item => $rating) {
    echo "$item: $rating stars\n";
}

// Calculate average rating
$average = array_sum($ratings) / count($ratings);
```

---

### PDF Embed Field 🆕

**Field Type:** `pdf_embed`
**Icon:** 📄
**Category:** Content (display-only)
**Added:** v2.0.0

**Description:**
Display-only field that embeds PDF documents inline within forms. Does not collect user input - used for showing terms of service, contracts, or informational documents that users should review.

**Use Cases:**
- Terms of service agreements
- Privacy policies
- Contracts and legal documents
- Informational brochures
- Product specifications
- User manuals

**Field-Specific Options** (stored in `options` JSON column):

| Option | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| `label_icon` | string | No | - | Bootstrap icon class to display before the label (e.g., "bi-envelope") |
| `floating_label` | string | No | ``false`` | Enable Bootstrap 5 floating label style (label animates inside input) |
| `field_size` | string | No | - | Bootstrap input size: empty for default, "sm" for small, "lg" for large |
| `text_alignment` | string | No | - | Text alignment for the input content (left, center, right) |
| `spacing` | string | No | - | Margin and padding configuration for the field wrapper |
| `display` | string | No | - | Visibility and display settings (show/hide conditions) |
| `validation_timing` | string | No | ``"live"`` | When to trigger validation: "live" (on input), "blur" (on focus loss), or "submit" (on form submit) |
| `custom_invalid_feedback` | string | No | - | Custom error message to show when validation fails |
| `custom_valid_feedback` | string | No | - | Custom success message to show when validation passes |
| `input_mode` | string | No | ``"url"`` | Source |
| `pdf_url` | string | No | - | PDF URL |
| `pdf_upload` | string | No | - | Upload PDF |
| `pdf_preview` | string | No | - | Preview |
| `width` | string | No | ``"100%"`` | Canvas width in pixels |
| `height` | string | No | ``"600px"`` | Canvas height in pixels |

**Validation Options:**
None - this is a display-only field that doesn't collect input.

**Example:**
```php
$form->fields()->create([
    'field_type' => 'pdf_embed',
    'label' => 'Please Review Our Terms',
    'name' => 'agree_to_terms',
    'validation_rules' => ['required', 'accepted'],
    'options' => [
        'pdf_url' => 'https://example.com/document.pdf',
        'height' => '600px',
        'show_download' => true,
    ],
]
```

For complete validation rules, see [Laravel Validation Documentation](https://laravel.com/docs/validation#available-validation-rules).

---

## Next Steps

- **Conditional Logic:** Learn how to show/hide fields based on values → [Conditional Logic Guide](CONDITIONAL_LOGIC.md)
- **Custom Field Types:** Create your own field types → [Custom Field Types Guide](CUSTOM_FIELD_TYPES.md)
- **Layout System:** Build complex layouts → [Layout Guide](LAYOUT_GUIDE.md)
- **Working with Submissions:** Process form data → [Submissions Guide](WORKING_WITH_SUBMISSIONS.md)

---

**Need help?** See our [Troubleshooting Guide](TROUBLESHOOTING.md) or [open an issue](https://bitbucket.org/bmooredigitalisstudios/slick-forms/issues).
