# Textarea

## Overview



## Basic Information

- **Type**: `textarea`
- **Icon**: `bi bi-textarea-t`
- **Model**: `CustomFormField`
- **Category**: Input

## Properties Reference

### Database Columns

| Property | Type | Required | Default | Description |
|--|------|--|-|-----|
| `slick_form_id` | `integer` | Yes | - | The form this field belongs to |
| `slick_form_page_id` | `integer` | No | - | The page this field belongs to (for multi-page forms) |
| `slick_form_layout_element_id` | `integer` | No | - | The layout element this field belongs to |
| `parent_field_id` | `integer` | No | - | Parent field ID (for nested fields like repeater children) |
| `field_type` | `string` | Yes | - | Must be 'textarea' for this field type |
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
| `use_wysiwyg` | `switch` | No | `false` | Render this textarea as a rich text editor |
| `rows` | `number` | No | `4` | Number of visible text rows |

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
- Must be 'textarea' for this field type

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

**`use_wysiwyg`**
- Render this textarea as a rich text editor

**`rows`**
- Number of visible text rows

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
                        "contains",
                        "not_contains",
                        "starts_with",
                        "ends_with",
                        "regex",
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
    "field_type": "textarea",
    "name": "description",
    "label": "Description",
    "placeholder": "Provide a detailed description...",
    "help_text": null,
    "is_required": false,
    "order": 0,
    "validation_rules": [
        "required",
        "min:10",
        "max:1000"
    ],
    "options": {
        "floating_label": true,
        "field_size": "lg",
        "rows": 4,
        "max_length": 1000,
        "show_character_count": true
    }
}
```

## JSON Schema

```json
{
    "metadata": {
        "type": "textarea",
        "label": "Textarea",
        "icon": "bi bi-textarea-t",
        "description": ""
    },
    "usage": {
        "model": "CustomFormField",
        "method": "create",
        "example": {
            "slick_form_id": 1,
            "field_type": "textarea",
            "name": "description",
            "label": "Description",
            "placeholder": "Provide a detailed description...",
            "help_text": null,
            "is_required": false,
            "order": 0,
            "validation_rules": [
                "required",
                "min:10",
                "max:1000"
            ],
            "options": {
                "floating_label": true,
                "field_size": "lg",
                "rows": 4,
                "max_length": 1000,
                "show_character_count": true
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
            "value": "textarea",
            "description": "Must be 'textarea' for this field type"
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
        "use_wysiwyg": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Render this textarea as a rich text editor",
            "tab": "options"
        },
        "rows": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": 4,
            "description": "Number of visible text rows",
            "tab": "options"
        }
    },
    "validation_rules": {
        "type": "array",
        "required": false,
        "storage": "json_column",
        "description": "Laravel validation rules",
        "example": [
            "min",
            "max"
        ],
        "available_rules": {
            "min": {
                "format": "min:{value}",
                "description": "Minimum Length",
                "help": "Minimum number of characters required",
                "value_type": "number",
                "placeholder": null
            },
            "max": {
                "format": "max:{value}",
                "description": "Maximum Length",
                "help": "Maximum number of characters allowed",
                "value_type": "number",
                "placeholder": null
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
                            "contains",
                            "not_contains",
                            "starts_with",
                            "ends_with",
                            "regex",
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
