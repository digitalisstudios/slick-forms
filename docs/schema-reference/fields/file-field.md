# File Upload

## Overview



## Basic Information

- **Type**: `file`
- **Icon**: `bi bi-file-earmark-arrow-up`
- **Model**: `CustomFormField`
- **Category**: File upload

## Properties Reference

### Database Columns

| Property | Type | Required | Default | Description |
|--|------|--|-|-----|
| `slick_form_id` | `integer` | Yes | - | The form this field belongs to |
| `slick_form_page_id` | `integer` | No | - | The page this field belongs to (for multi-page forms) |
| `slick_form_layout_element_id` | `integer` | No | - | The layout element this field belongs to |
| `parent_field_id` | `integer` | No | - | Parent field ID (for nested fields like repeater children) |
| `field_type` | `string` | Yes | - | Must be 'file' for this field type |
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
| `accepted_types` | `text` | No | - | File extensions or MIME types (comma-separated) |
| `max_size` | `number` | No | `"10240"` | Maximum file size in kilobytes (default: 10MB) |
| `multiple` | `switch` | No | `false` | Allow users to upload multiple files at once |
| `enable_drag_drop` | `switch` | No | `true` | Allow drag and drop file uploads |
| `show_preview` | `switch` | No | `true` | Display preview of uploaded files |

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
- Must be 'file' for this field type

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

**`accepted_types`**
- File extensions or MIME types (comma-separated)

**`max_size`**
- Maximum file size in kilobytes (default: 10MB)

**`multiple`**
- Allow users to upload multiple files at once

**`enable_drag_drop`**
- Allow drag and drop file uploads

**`show_preview`**
- Display preview of uploaded files

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
                "operator": "is_empty",
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
    "field_type": "file",
    "name": "resume",
    "label": "Upload Resume",
    "placeholder": null,
    "help_text": "Accepted formats: PDF, DOC, DOCX (max 5MB)",
    "is_required": false,
    "order": 0,
    "validation_rules": [
        "required",
        "file",
        "max:5120"
    ],
    "options": {
        "floating_label": true,
        "field_size": "lg"
    }
}
```

## JSON Schema

```json
{
    "metadata": {
        "type": "file",
        "label": "File Upload",
        "icon": "bi bi-file-earmark-arrow-up",
        "description": ""
    },
    "usage": {
        "model": "CustomFormField",
        "method": "create",
        "example": {
            "slick_form_id": 1,
            "field_type": "file",
            "name": "resume",
            "label": "Upload Resume",
            "placeholder": null,
            "help_text": "Accepted formats: PDF, DOC, DOCX (max 5MB)",
            "is_required": false,
            "order": 0,
            "validation_rules": [
                "required",
                "file",
                "max:5120"
            ],
            "options": {
                "floating_label": true,
                "field_size": "lg"
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
            "value": "file",
            "description": "Must be 'file' for this field type"
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
        "accepted_types": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "File extensions or MIME types (comma-separated)",
            "tab": "options"
        },
        "max_size": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": "10240",
            "description": "Maximum file size in kilobytes (default: 10MB)",
            "tab": "options"
        },
        "multiple": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Allow users to upload multiple files at once",
            "tab": "options"
        },
        "enable_drag_drop": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": true,
            "description": "Allow drag and drop file uploads",
            "tab": "options"
        },
        "show_preview": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": true,
            "description": "Display preview of uploaded files",
            "tab": "options"
        }
    },
    "validation_rules": {
        "type": "array",
        "required": false,
        "storage": "json_column",
        "description": "Laravel validation rules",
        "example": [
            "mimes",
            "max",
            "dimensions"
        ],
        "available_rules": {
            "mimes": {
                "format": "mimes:{value}",
                "description": "Allowed MIME Types",
                "help": "Comma-separated list of allowed file extensions (e.g., pdf,jpg,png)",
                "value_type": "text",
                "placeholder": "pdf,doc,docx"
            },
            "max": {
                "format": "max:{value}",
                "description": "Maximum File Size (KB)",
                "help": "Maximum file size in kilobytes",
                "value_type": "number",
                "placeholder": "10240"
            },
            "dimensions": {
                "format": "dimensions:{value}",
                "description": "Image Dimensions",
                "help": "For images only. Format: min_width=100,max_width=1000,min_height=100,max_height=1000",
                "value_type": "text",
                "placeholder": "min_width=100,max_width=1000"
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
                    "operator": "is_empty",
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
