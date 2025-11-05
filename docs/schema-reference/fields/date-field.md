# Date

## Overview



## Basic Information

- **Type**: `date`
- **Icon**: `bi bi-calendar`
- **Model**: `CustomFormField`
- **Category**: Date time

## Properties Reference

### Database Columns

| Property | Type | Required | Default | Description |
|--|------|--|-|-----|
| `slick_form_id` | `integer` | Yes | - | The form this field belongs to |
| `slick_form_page_id` | `integer` | No | - | The page this field belongs to (for multi-page forms) |
| `slick_form_layout_element_id` | `integer` | No | - | The layout element this field belongs to |
| `parent_field_id` | `integer` | No | - | Parent field ID (for nested fields like repeater children) |
| `field_type` | `string` | Yes | - | Must be 'date' for this field type |
| `validation_rules` | `mixed` | No | - | Array of Laravel validation rules (e.g., ['required', 'min:3', 'max:255']) |
| `conditional_logic` | `mixed` | No | - | JSON object defining visibility conditions and conditional validation rules |
| `options` | `mixed` | No | - | JSON object storing field-specific configuration options (see JSON OPTIONS table below) |
| `order` | `integer` | Yes | - | Display order among siblings |
| `show_label` | `switch` | No | `true` | Show Label |
| `label` | `text` | Yes | - | Label |
| `name` | `text` | Yes | - | Technical identifier for this field (letters, numbers, underscores) |
| `element_id` | `text` | Yes | - | Unique HTML id attribute for this field |
| `placeholder` | `text` | No | - | Placeholder |
| `help_text_as_popover` | `switch` | No | `false` | Display help text as a "?" popover instead of below the field |
| `help_text` | `textarea` | No | - | Optional help text displayed below the field |
| `is_required` | `switch` | No | `false` | Required |
| `class` | `text` | No | - | Custom CSS classes to apply to the field wrapper |
| `style` | `textarea` | No | - | Custom inline CSS styles (e.g., "color: red; font-weight: bold;") |

### JSON Options (`options` column)

| Property | Type | Required | Default | Description |
|--|------|--|-|-----|
| `label_icon` | `icon_picker` | No | - | Optional icon to display before the label |
| `floating_label` | `switch` | No | `false` | Modern floating label style (Bootstrap 5) |
| `field_size` | `select` | No | - | Control the size of the input field |
| `text_alignment` | `custom` | No | - | UI rendered in custom view, stored in options |
| `spacing` | `custom` | No | - | UI rendered in custom view, stored in options |
| `display` | `custom` | No | - | UI rendered in custom view, stored in options |
| `validation_timing` | `select` | No | `"live"` | When should this field be validated? |
| `custom_invalid_feedback` | `text` | No | - | Custom message to show when validation fails |
| `custom_valid_feedback` | `text` | No | - | Optional message to show when validation passes |
| `enable_flatpickr` | `switch` | No | `true` | Use Flatpickr for enhanced date selection |
| `date_format` | `select` | No | `"m\/d\/Y"` | Display Format |
| `min_date` | `text` | No | - | Earliest selectable date |
| `max_date` | `text` | No | - | Latest selectable date |
| `enable_time` | `switch` | No | `false` | Allow time selection in addition to date |

## Properties by Tab

### Basic Tab

**`slick_form_id`** *(required)*
- The form this field belongs to

**`slick_form_page_id`**
- The page this field belongs to (for multi-page forms)

**`slick_form_layout_element_id`**
- The layout element this field belongs to

**`parent_field_id`**
- Parent field ID (for nested fields like repeater children)

**`field_type`** *(required)*
- Must be 'date' for this field type

**`validation_rules`**
- Array of Laravel validation rules (e.g., ['required', 'min:3', 'max:255'])

**`conditional_logic`**
- JSON object defining visibility conditions and conditional validation rules

**`options`**
- JSON object storing field-specific configuration options (see JSON OPTIONS table below)

**`order`** *(required)*
- Display order among siblings

**`show_label`**
- Show Label

**`label`** *(required)*
- Label

**`label_icon`**
- Optional icon to display before the label

**`name`** *(required)*
- Technical identifier for this field (letters, numbers, underscores)

**`element_id`** *(required)*
- Unique HTML id attribute for this field

**`placeholder`**
- Placeholder

**`help_text_as_popover`**
- Display help text as a "?" popover instead of below the field

**`help_text`**
- Optional help text displayed below the field

### Validation Tab

**`is_required`**
- Required

**`validation_timing`**
- When should this field be validated?
- Options: 
  - `0`: Real-time (as you type)
  - `1`: On blur (when leaving field)
  - `2`: On submit only

**`custom_invalid_feedback`**
- Custom message to show when validation fails

**`custom_valid_feedback`**
- Optional message to show when validation passes

### Style Tab

**`class`**
- Custom CSS classes to apply to the field wrapper

**`style`**
- Custom inline CSS styles (e.g., "color: red; font-weight: bold;")

**`text_alignment`**
- UI rendered in custom view, stored in options

**`spacing`**
- UI rendered in custom view, stored in options

### Options Tab

**`floating_label`**
- Modern floating label style (Bootstrap 5)

**`field_size`**
- Control the size of the input field
- Options: 
  - `0`: Default
  - `1`: Small
  - `2`: Large

**`enable_flatpickr`**
- Use Flatpickr for enhanced date selection

**`date_format`**
- Display Format
- Options: 
  - `0`: MM/DD/YYYY
  - `1`: DD/MM/YYYY
  - `2`: YYYY-MM-DD
  - `3`: Month DD, YYYY

**`min_date`**
- Earliest selectable date

**`max_date`**
- Latest selectable date

**`enable_time`**
- Allow time selection in addition to date

### Advanced Tab

**`display`**
- UI rendered in custom view, stored in options

## Validation Rules

## Conditional Logic

### Structure

```json
{
    "type": "object",
    "required": false,
    "storage": "json_column",
    "description": "Visibility and conditional validation rules",
    "structure": {
        "action": {
            "type": "string",
            "required": true,
            "options": [
                "show",
                "hide"
            ],
            "description": "Whether to show or hide when conditions are met"
        },
        "match": {
            "type": "string",
            "required": true,
            "options": [
                "all",
                "any"
            ],
            "description": "all = AND logic (all conditions must be met), any = OR logic (any condition can be met)"
        },
        "conditions": {
            "type": "array",
            "description": "Array of condition objects",
            "structure": {
                "target_field_id": {
                    "type": "integer",
                    "description": "Database ID of the field to check"
                },
                "operator": {
                    "type": "string",
                    "description": "Comparison operator for this field type",
                    "options": [
                        "equals",
                        "not_equals",
                        "after",
                        "before",
                        "after_or_equal",
                        "before_or_equal",
                        "is_empty",
                        "is_not_empty"
                    ]
                },
                "value": {
                    "type": "mixed",
                    "description": "Value to compare against (type depends on operator)"
                }
            }
        }
    },
    "example": {
        "action": "show",
        "match": "all",
        "conditions": [
            {
                "target_field_id": 123,
                "operator": "equals",
                "value": "example_value"
            }
        ]
    }
}
```

## Usage Example

```php
{
    "slick_form_id": 1,
    "field_type": "date",
    "name": "birth_date",
    "label": "Date of Birth",
    "placeholder": null,
    "help_text": null,
    "is_required": false,
    "order": 0,
    "validation_rules": [
        "required",
        "date"
    ],
    "options": {
        "floating_label": true,
        "field_size": "lg",
        "format": "Y-m-d",
        "min_date": "1900-01-01"
    }
}
```

## JSON Schema

```json
{
    "metadata": {
        "type": "date",
        "label": "Date",
        "icon": "bi bi-calendar",
        "description": ""
    },
    "usage": {
        "model": "CustomFormField",
        "method": "create",
        "example": {
            "slick_form_id": 1,
            "field_type": "date",
            "name": "birth_date",
            "label": "Date of Birth",
            "placeholder": null,
            "help_text": null,
            "is_required": false,
            "order": 0,
            "validation_rules": [
                "required",
                "date"
            ],
            "options": {
                "floating_label": true,
                "field_size": "lg",
                "format": "Y-m-d",
                "min_date": "1900-01-01"
            }
        }
    },
    "properties": {
        "slick_form_id": {
            "type": "integer",
            "required": true,
            "storage": "column",
            "description": "The form this field belongs to"
        },
        "slick_form_page_id": {
            "type": "integer",
            "required": false,
            "storage": "column",
            "description": "The page this field belongs to (for multi-page forms)"
        },
        "slick_form_layout_element_id": {
            "type": "integer",
            "required": false,
            "storage": "column",
            "description": "The layout element this field belongs to"
        },
        "parent_field_id": {
            "type": "integer",
            "required": false,
            "storage": "column",
            "description": "Parent field ID (for nested fields like repeater children)"
        },
        "field_type": {
            "type": "string",
            "required": true,
            "storage": "column",
            "value": "date",
            "description": "Must be 'date' for this field type"
        },
        "validation_rules": {
            "type": "mixed",
            "storage": "column",
            "description": "Array of Laravel validation rules (e.g., ['required', 'min:3', 'max:255'])"
        },
        "conditional_logic": {
            "type": "mixed",
            "storage": "column",
            "description": "JSON object defining visibility conditions and conditional validation rules"
        },
        "options": {
            "type": "mixed",
            "storage": "column",
            "description": ""
        },
        "order": {
            "type": "integer",
            "required": true,
            "storage": "column",
            "description": "Display order among siblings"
        },
        "show_label": {
            "type": "switch",
            "required": false,
            "storage": "column",
            "default": true,
            "description": "Show Label",
            "tab": "basic"
        },
        "label": {
            "type": "text",
            "required": true,
            "storage": "column",
            "default": null,
            "description": "Label",
            "tab": "basic"
        },
        "label_icon": {
            "type": "icon_picker",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Optional icon to display before the label",
            "tab": "basic"
        },
        "name": {
            "type": "text",
            "required": true,
            "storage": "column",
            "default": null,
            "description": "Technical identifier for this field (letters, numbers, underscores)",
            "tab": "basic"
        },
        "element_id": {
            "type": "text",
            "required": true,
            "storage": "column",
            "default": null,
            "description": "Unique HTML id attribute for this field",
            "tab": "basic"
        },
        "placeholder": {
            "type": "text",
            "required": false,
            "storage": "column",
            "default": null,
            "description": "Placeholder",
            "tab": "basic"
        },
        "help_text_as_popover": {
            "type": "switch",
            "required": false,
            "storage": "column",
            "default": false,
            "description": "Display help text as a \"?\" popover instead of below the field",
            "tab": "basic"
        },
        "help_text": {
            "type": "textarea",
            "required": false,
            "storage": "column",
            "default": null,
            "description": "Optional help text displayed below the field",
            "tab": "basic"
        },
        "is_required": {
            "type": "switch",
            "required": false,
            "storage": "column",
            "default": false,
            "description": "Required",
            "tab": "validation"
        },
        "class": {
            "type": "text",
            "required": false,
            "storage": "column",
            "default": null,
            "description": "Custom CSS classes to apply to the field wrapper",
            "tab": "style"
        },
        "style": {
            "type": "textarea",
            "required": false,
            "storage": "column",
            "default": null,
            "description": "Custom inline CSS styles (e.g., \"color: red; font-weight: bold;\")",
            "tab": "style"
        },
        "floating_label": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Modern floating label style (Bootstrap 5)",
            "tab": "options"
        },
        "field_size": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Control the size of the input field",
            "options": [
                "Default",
                "Small",
                "Large"
            ],
            "options_labels": {
                "": "Default",
                "sm": "Small",
                "lg": "Large"
            },
            "tab": "options"
        },
        "text_alignment": {
            "type": "custom",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "UI rendered in custom view, stored in options",
            "tab": "style"
        },
        "spacing": {
            "type": "custom",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "UI rendered in custom view, stored in options",
            "tab": "style"
        },
        "display": {
            "type": "custom",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "UI rendered in custom view, stored in options",
            "tab": "advanced"
        },
        "validation_timing": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": "live",
            "description": "When should this field be validated?",
            "options": [
                "Real-time (as you type)",
                "On blur (when leaving field)",
                "On submit only"
            ],
            "options_labels": {
                "live": "Real-time (as you type)",
                "blur": "On blur (when leaving field)",
                "submit": "On submit only"
            },
            "tab": "validation"
        },
        "custom_invalid_feedback": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Custom message to show when validation fails",
            "tab": "validation"
        },
        "custom_valid_feedback": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Optional message to show when validation passes",
            "tab": "validation"
        },
        "enable_flatpickr": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": true,
            "description": "Use Flatpickr for enhanced date selection",
            "tab": "options"
        },
        "date_format": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": "m\/d\/Y",
            "description": "Display Format",
            "options": [
                "MM\/DD\/YYYY",
                "DD\/MM\/YYYY",
                "YYYY-MM-DD",
                "Month DD, YYYY"
            ],
            "options_labels": {
                "m\/d\/Y": "MM\/DD\/YYYY",
                "d\/m\/Y": "DD\/MM\/YYYY",
                "Y-m-d": "YYYY-MM-DD",
                "F d, Y": "Month DD, YYYY"
            },
            "tab": "options"
        },
        "min_date": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Earliest selectable date",
            "tab": "options"
        },
        "max_date": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Latest selectable date",
            "tab": "options"
        },
        "enable_time": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Allow time selection in addition to date",
            "tab": "options"
        }
    },
    "validation_rules": {
        "type": "array",
        "required": false,
        "storage": "json_column",
        "description": "Laravel validation rules",
        "example": [
            "after",
            "after_or_equal",
            "before",
            "before_or_equal",
            "date_format"
        ],
        "available_rules": {
            "after": {
                "format": "after:{value}",
                "description": "Must Be After",
                "help": "Date must be after this date",
                "value_type": "date",
                "placeholder": "YYYY-MM-DD"
            },
            "after_or_equal": {
                "format": "after_or_equal:{value}",
                "description": "Must Be After Or Equal",
                "help": "Date must be after or equal to this date",
                "value_type": "date",
                "placeholder": "YYYY-MM-DD"
            },
            "before": {
                "format": "before:{value}",
                "description": "Must Be Before",
                "help": "Date must be before this date",
                "value_type": "date",
                "placeholder": "YYYY-MM-DD"
            },
            "before_or_equal": {
                "format": "before_or_equal:{value}",
                "description": "Must Be Before Or Equal",
                "help": "Date must be before or equal to this date",
                "value_type": "date",
                "placeholder": "YYYY-MM-DD"
            },
            "date_format": {
                "format": "date_format:{value}",
                "description": "Date Format",
                "help": "Required date format (e.g., Y-m-d, m\/d\/Y)",
                "value_type": "text",
                "placeholder": "Y-m-d"
            }
        }
    },
    "conditional_logic": {
        "type": "object",
        "required": false,
        "storage": "json_column",
        "description": "Visibility and conditional validation rules",
        "structure": {
            "action": {
                "type": "string",
                "required": true,
                "options": [
                    "show",
                    "hide"
                ],
                "description": "Whether to show or hide when conditions are met"
            },
            "match": {
                "type": "string",
                "required": true,
                "options": [
                    "all",
                    "any"
                ],
                "description": "all = AND logic (all conditions must be met), any = OR logic (any condition can be met)"
            },
            "conditions": {
                "type": "array",
                "description": "Array of condition objects",
                "structure": {
                    "target_field_id": {
                        "type": "integer",
                        "description": "Database ID of the field to check"
                    },
                    "operator": {
                        "type": "string",
                        "description": "Comparison operator for this field type",
                        "options": [
                            "equals",
                            "not_equals",
                            "after",
                            "before",
                            "after_or_equal",
                            "before_or_equal",
                            "is_empty",
                            "is_not_empty"
                        ]
                    },
                    "value": {
                        "type": "mixed",
                        "description": "Value to compare against (type depends on operator)"
                    }
                }
            }
        },
        "example": {
            "action": "show",
            "match": "all",
            "conditions": [
                {
                    "target_field_id": 123,
                    "operator": "equals",
                    "value": "example_value"
                }
            ]
        }
    },
    "tabs": {
        "basic": {
            "label": "Basic",
            "icon": "bi-info-circle",
            "order": 10,
            "view": "auto-generated from schema"
        },
        "options": {
            "label": "Options",
            "icon": "bi-sliders",
            "order": 20,
            "view": "auto-generated from schema"
        },
        "validation": {
            "label": "Validation",
            "icon": "bi-check-circle",
            "order": 30,
            "view": "slick-forms::livewire.partials.properties-panel.tabs.validation"
        },
        "style": {
            "label": "Style",
            "icon": "bi-palette",
            "order": 40,
            "view": "slick-forms::livewire.partials.properties-panel.tabs.style"
        },
        "advanced": {
            "label": "Visibility",
            "icon": "bi-eye",
            "order": 50,
            "view": "slick-forms::livewire.partials.properties-panel.tabs.advanced"
        }
    }
}
```
