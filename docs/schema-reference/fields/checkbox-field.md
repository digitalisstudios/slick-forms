# Checkbox Group

## Overview



## Basic Information

- **Type**: `checkbox`
- **Icon**: `bi bi-check-square`
- **Model**: `CustomFormField`
- **Category**: Selection

## Properties Reference

### Database Columns

| Property | Type | Required | Default | Description |
|--|------|--|-|-----|
| `slick_form_id` | `integer` | Yes | - | The form this field belongs to |
| `slick_form_page_id` | `integer` | No | - | The page this field belongs to (for multi-page forms) |
| `slick_form_layout_element_id` | `integer` | No | - | The layout element this field belongs to |
| `parent_field_id` | `integer` | No | - | Parent field ID (for nested fields like repeater children) |
| `field_type` | `string` | Yes | - | Must be 'checkbox' for this field type |
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
| `option_source` | `select` | Yes | `"static"` | Choose where options come from |
| `values` | `options` | Yes | `[{"label":"Option 1","value":"1","default":false},{"label":"Option 2","value":"2","default":false},{"label":"Option 3","value":"3","default":false}]` | Define the available checkbox options |
| `source_url` | `text` | No | - | URL to fetch options from |
| `headers` | `textarea` | No | - | JSON object of headers |
| `value_key` | `text` | No | `"value"` | JSON path to value field |
| `label_key` | `text` | No | `"label"` | JSON path to label field |
| `model_class` | `text` | No | - | Fully qualified model class |
| `value_column` | `text` | No | `"id"` | Database column for option value |
| `label_column` | `text` | No | `"name"` | Database column for option label |
| `scope` | `text` | No | - | Optional scope method name |
| `where_conditions` | `textarea` | No | - | JSON object of where conditions |
| `layout` | `select` | No | `"vertical"` | Layout |

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
- Must be 'checkbox' for this field type

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

**`option_source`** *(required)*
- Choose where options come from
- Options: 
  - `0`: Array
  - `1`: Array
  - `2`: Array

**`values`** *(required)*
- Define the available checkbox options

**`source_url`**
- URL to fetch options from

**`headers`**
- JSON object of headers

**`value_key`**
- JSON path to value field

**`label_key`**
- JSON path to label field

**`model_class`**
- Fully qualified model class

**`value_column`**
- Database column for option value

**`label_column`**
- Database column for option label

**`scope`**
- Optional scope method name

**`where_conditions`**
- JSON object of where conditions

**`layout`**
- Layout
- Options: 
  - `0`: Vertical (Stacked)
  - `1`: Horizontal (Inline)

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
                        "checked",
                        "unchecked"
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
                "operator": "checked",
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
    "field_type": "checkbox",
    "name": "interests",
    "label": "Interests",
    "placeholder": null,
    "help_text": null,
    "is_required": false,
    "order": 0,
    "validation_rules": [],
    "options": {
        "floating_label": true,
        "field_size": "lg",
        "values": [
            {
                "label": "Web Development",
                "value": "web_dev"
            },
            {
                "label": "Mobile Development",
                "value": "mobile_dev"
            },
            {
                "label": "UI\/UX Design",
                "value": "design"
            },
            {
                "label": "Data Science",
                "value": "data_science"
            }
        ]
    }
}
```

## JSON Schema

```json
{
    "metadata": {
        "type": "checkbox",
        "label": "Checkbox Group",
        "icon": "bi bi-check-square",
        "description": ""
    },
    "usage": {
        "model": "CustomFormField",
        "method": "create",
        "example": {
            "slick_form_id": 1,
            "field_type": "checkbox",
            "name": "interests",
            "label": "Interests",
            "placeholder": null,
            "help_text": null,
            "is_required": false,
            "order": 0,
            "validation_rules": [],
            "options": {
                "floating_label": true,
                "field_size": "lg",
                "values": [
                    {
                        "label": "Web Development",
                        "value": "web_dev"
                    },
                    {
                        "label": "Mobile Development",
                        "value": "mobile_dev"
                    },
                    {
                        "label": "UI\/UX Design",
                        "value": "design"
                    },
                    {
                        "label": "Data Science",
                        "value": "data_science"
                    }
                ]
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
            "value": "checkbox",
            "description": "Must be 'checkbox' for this field type"
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
        "option_source": {
            "type": "select",
            "required": true,
            "storage": "json_column",
            "default": "static",
            "description": "Choose where options come from",
            "options": [
                {
                    "label": "Static (Manual Entry)",
                    "value": "static"
                },
                {
                    "label": "Dynamic (URL\/API)",
                    "value": "url"
                },
                {
                    "label": "Dynamic (Database Model)",
                    "value": "model"
                }
            ],
            "options_labels": [
                {
                    "label": "Static (Manual Entry)",
                    "value": "static"
                },
                {
                    "label": "Dynamic (URL\/API)",
                    "value": "url"
                },
                {
                    "label": "Dynamic (Database Model)",
                    "value": "model"
                }
            ],
            "tab": "options"
        },
        "values": {
            "type": "options",
            "required": true,
            "storage": "json_column",
            "default": [
                {
                    "label": "Option 1",
                    "value": "1",
                    "default": false
                },
                {
                    "label": "Option 2",
                    "value": "2",
                    "default": false
                },
                {
                    "label": "Option 3",
                    "value": "3",
                    "default": false
                }
            ],
            "description": "Define the available checkbox options",
            "tab": "options"
        },
        "source_url": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "URL to fetch options from",
            "tab": "options"
        },
        "headers": {
            "type": "textarea",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "JSON object of headers",
            "tab": "options"
        },
        "value_key": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": "value",
            "description": "JSON path to value field",
            "tab": "options"
        },
        "label_key": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": "label",
            "description": "JSON path to label field",
            "tab": "options"
        },
        "model_class": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Fully qualified model class",
            "tab": "options"
        },
        "value_column": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": "id",
            "description": "Database column for option value",
            "tab": "options"
        },
        "label_column": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": "name",
            "description": "Database column for option label",
            "tab": "options"
        },
        "scope": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Optional scope method name",
            "tab": "options"
        },
        "where_conditions": {
            "type": "textarea",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "JSON object of where conditions",
            "tab": "options"
        },
        "layout": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": "vertical",
            "description": "Layout",
            "options": [
                "Vertical (Stacked)",
                "Horizontal (Inline)"
            ],
            "options_labels": {
                "vertical": "Vertical (Stacked)",
                "horizontal": "Horizontal (Inline)"
            },
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
                "description": "Minimum Selections",
                "help": "Minimum number of checkboxes that must be selected",
                "value_type": "number",
                "placeholder": null
            },
            "max": {
                "format": "max:{value}",
                "description": "Maximum Selections",
                "help": "Maximum number of checkboxes that can be selected",
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
                            "checked",
                            "unchecked"
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
                    "operator": "checked",
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
