# Tabs

## Overview



## Basic Information

- **Type**: `tabs`
- **Icon**: `bi-folder`
- **Model**: `SlickFormLayoutElement`

## Properties Reference

### Database Columns

| Property | Type | Required | Default | Description |
|----------|------|----------|---------|-------------|
| `slick_form_id` | `integer` | Yes | - | The form this element belongs to |
| `slick_form_page_id` | `integer` | No | - | The page this element belongs to (for multi-page forms) |
| `parent_id` | `integer` | No | - | Parent element ID for nesting (null = top-level) |
| `element_type` | `string` | Yes | - | Must be 'tabs' for this element type |
| `order` | `integer` | Yes | - | Display order among siblings |
| `settings` | `mixed` | No | - |  |
| `element_id` | `text` | Yes | - | Unique HTML id attribute (letters, numbers, hyphens, underscores) |
| `class` | `text` | No | - | Space-separated CSS class names |

### JSON Options (`options` column)

| Property | Type | Required | Default | Description |
|----------|------|----------|---------|-------------|
| `conditional_logic` | `object` | No | - | Visibility rules (same structure as field conditional logic) |
| `style` | `select` | No | `"nav-tabs"` | Tab Style |
| `spacing.margin_top` | `select` | No | - | Margin Top |
| `spacing.margin_bottom` | `select` | No | - | Margin Bottom |
| `spacing.padding_top` | `select` | No | - | Padding Top |
| `spacing.padding_bottom` | `select` | No | - | Padding Bottom |
| `display.display` | `select` | No | - | Display (XS) |
| `display.display_sm` | `select` | No | - | Display (SM) |
| `display.display_md` | `select` | No | - | Display (MD) |
| `text_alignment.align` | `select` | No | - | Text Alignment |
| `alignment` | `select` | No | `""` | Tab Alignment |
| `fill` | `select` | No | `"false"` | Tab Width |
| `default_active_tab` | `number` | No | `0` | 0 = first tab, 1 = second tab, etc. |
| `fade` | `switch` | No | `false` | Enable Fade Animation |

## Properties by Tab

### Basic Tab

**`slick_form_id`** *(required)*
- The form this element belongs to

**`slick_form_page_id`**
- The page this element belongs to (for multi-page forms)

**`parent_id`**
- Parent element ID for nesting (null = top-level)

**`element_type`** *(required)*
- Must be 'tabs' for this element type

**`order`** *(required)*
- Display order among siblings

**`settings`**
- 

**`conditional_logic`**
- Visibility rules (same structure as field conditional logic)

**`element_id`** *(required)*
- Unique HTML id attribute (letters, numbers, hyphens, underscores)

### Style Tab

**`class`**
- Space-separated CSS class names

**`spacing.margin_top`**
- Margin Top
- Options: 
  - `0`: None
  - `1`: 0
  - `2`: 1
  - `3`: 2
  - `4`: 3
  - `5`: 4
  - `6`: 5
  - `7`: Auto

**`spacing.margin_bottom`**
- Margin Bottom
- Options: 
  - `0`: None
  - `1`: 0
  - `2`: 1
  - `3`: 2
  - `4`: 3
  - `5`: 4
  - `6`: 5
  - `7`: Auto

**`spacing.padding_top`**
- Padding Top
- Options: 
  - `0`: None
  - `1`: 0
  - `2`: 1
  - `3`: 2
  - `4`: 3
  - `5`: 4
  - `6`: 5

**`spacing.padding_bottom`**
- Padding Bottom
- Options: 
  - `0`: None
  - `1`: 0
  - `2`: 1
  - `3`: 2
  - `4`: 3
  - `5`: 4
  - `6`: 5

**`display.display`**
- Display (XS)
- Options: 
  - `0`: Default
  - `1`: Hide
  - `2`: Block
  - `3`: Inline
  - `4`: Inline Block
  - `5`: Flex

**`display.display_sm`**
- Display (SM)
- Options: 
  - `0`: Default
  - `1`: Hide
  - `2`: Block
  - `3`: Inline
  - `4`: Inline Block
  - `5`: Flex

**`display.display_md`**
- Display (MD)
- Options: 
  - `0`: Default
  - `1`: Hide
  - `2`: Block
  - `3`: Inline
  - `4`: Inline Block
  - `5`: Flex

**`text_alignment.align`**
- Text Alignment
- Options: 
  - `0`: Default
  - `1`: Start (Left)
  - `2`: Center
  - `3`: End (Right)

### Settings Tab

**`style`**
- Tab Style
- Options: 
  - `0`: Tabs (default)
  - `1`: Pills
  - `2`: Underline

**`alignment`**
- Tab Alignment
- Options: 
  - `0`: Left (default)
  - `1`: Center
  - `2`: Right

**`fill`**
- Tab Width
- Options: 
  - `0`: Auto
  - `1`: Fill (proportional)
  - `2`: Justified (equal width)

**`default_active_tab`**
- 0 = first tab, 1 = second tab, etc.

**`fade`**
- Enable Fade Animation

## Usage Example

```php
{
    "slick_form_id": 1,
    "element_type": "tabs",
    "element_id": "profile-tabs",
    "order": 0,
    "settings": {
        "tab_style": "nav-tabs",
        "tab_alignment": "justify-content-center",
        "tab_fill": false,
        "default_active_tab": 0,
        "fade_animation": true
    }
}
```

## JSON Schema

```json
{
    "metadata": {
        "type": "tabs",
        "label": "Tabs",
        "icon": "bi-folder",
        "description": ""
    },
    "usage": {
        "model": "SlickFormLayoutElement",
        "method": "create",
        "example": {
            "slick_form_id": 1,
            "element_type": "tabs",
            "element_id": "profile-tabs",
            "order": 0,
            "settings": {
                "tab_style": "nav-tabs",
                "tab_alignment": "justify-content-center",
                "tab_fill": false,
                "default_active_tab": 0,
                "fade_animation": true
            }
        }
    },
    "properties": {
        "slick_form_id": {
            "type": "integer",
            "required": true,
            "storage": "column",
            "description": "The form this element belongs to"
        },
        "slick_form_page_id": {
            "type": "integer",
            "required": false,
            "storage": "column",
            "description": "The page this element belongs to (for multi-page forms)"
        },
        "parent_id": {
            "type": "integer",
            "required": false,
            "storage": "column",
            "description": "Parent element ID for nesting (null = top-level)"
        },
        "element_type": {
            "type": "string",
            "required": true,
            "storage": "column",
            "value": "tabs",
            "description": "Must be 'tabs' for this element type"
        },
        "order": {
            "type": "integer",
            "required": true,
            "storage": "column",
            "description": "Display order among siblings"
        },
        "settings": {
            "type": "mixed",
            "storage": "column",
            "description": ""
        },
        "conditional_logic": {
            "type": "object",
            "required": false,
            "storage": "json_column",
            "description": "Visibility rules (same structure as field conditional logic)"
        },
        "element_id": {
            "type": "text",
            "required": true,
            "storage": "column",
            "default": null,
            "description": "Unique HTML id attribute (letters, numbers, hyphens, underscores)",
            "tab": "basic"
        },
        "class": {
            "type": "text",
            "required": false,
            "storage": "column",
            "default": null,
            "description": "Space-separated CSS class names",
            "tab": "style"
        },
        "style": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": "nav-tabs",
            "description": "Tab Style",
            "options": [
                "Tabs (default)",
                "Pills",
                "Underline"
            ],
            "options_labels": {
                "nav-tabs": "Tabs (default)",
                "nav-pills": "Pills",
                "nav-underline": "Underline"
            },
            "tab": "settings"
        },
        "spacing.margin_top": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Margin Top",
            "options": [
                "None",
                "0",
                "1",
                "2",
                "3",
                "4",
                "5",
                "Auto"
            ],
            "options_labels": {
                "": "None",
                "0": "0",
                "1": "1",
                "2": "2",
                "3": "3",
                "4": "4",
                "5": "5",
                "auto": "Auto"
            },
            "tab": "style"
        },
        "spacing.margin_bottom": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Margin Bottom",
            "options": [
                "None",
                "0",
                "1",
                "2",
                "3",
                "4",
                "5",
                "Auto"
            ],
            "options_labels": {
                "": "None",
                "0": "0",
                "1": "1",
                "2": "2",
                "3": "3",
                "4": "4",
                "5": "5",
                "auto": "Auto"
            },
            "tab": "style"
        },
        "spacing.padding_top": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Padding Top",
            "options": [
                "None",
                "0",
                "1",
                "2",
                "3",
                "4",
                "5"
            ],
            "options_labels": {
                "": "None",
                "0": "0",
                "1": "1",
                "2": "2",
                "3": "3",
                "4": "4",
                "5": "5"
            },
            "tab": "style"
        },
        "spacing.padding_bottom": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Padding Bottom",
            "options": [
                "None",
                "0",
                "1",
                "2",
                "3",
                "4",
                "5"
            ],
            "options_labels": {
                "": "None",
                "0": "0",
                "1": "1",
                "2": "2",
                "3": "3",
                "4": "4",
                "5": "5"
            },
            "tab": "style"
        },
        "display.display": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Display (XS)",
            "options": [
                "Default",
                "Hide",
                "Block",
                "Inline",
                "Inline Block",
                "Flex"
            ],
            "options_labels": {
                "": "Default",
                "none": "Hide",
                "block": "Block",
                "inline": "Inline",
                "inline-block": "Inline Block",
                "flex": "Flex"
            },
            "tab": "style"
        },
        "display.display_sm": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Display (SM)",
            "options": [
                "Default",
                "Hide",
                "Block",
                "Inline",
                "Inline Block",
                "Flex"
            ],
            "options_labels": {
                "": "Default",
                "none": "Hide",
                "block": "Block",
                "inline": "Inline",
                "inline-block": "Inline Block",
                "flex": "Flex"
            },
            "tab": "style"
        },
        "display.display_md": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Display (MD)",
            "options": [
                "Default",
                "Hide",
                "Block",
                "Inline",
                "Inline Block",
                "Flex"
            ],
            "options_labels": {
                "": "Default",
                "none": "Hide",
                "block": "Block",
                "inline": "Inline",
                "inline-block": "Inline Block",
                "flex": "Flex"
            },
            "tab": "style"
        },
        "text_alignment.align": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Text Alignment",
            "options": [
                "Default",
                "Start (Left)",
                "Center",
                "End (Right)"
            ],
            "options_labels": {
                "": "Default",
                "start": "Start (Left)",
                "center": "Center",
                "end": "End (Right)"
            },
            "tab": "style"
        },
        "alignment": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": "",
            "description": "Tab Alignment",
            "options": [
                "Left (default)",
                "Center",
                "Right"
            ],
            "options_labels": {
                "": "Left (default)",
                "justify-content-center": "Center",
                "justify-content-end": "Right"
            },
            "tab": "settings"
        },
        "fill": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": "false",
            "description": "Tab Width",
            "options": [
                "Auto",
                "Fill (proportional)",
                "Justified (equal width)"
            ],
            "options_labels": {
                "false": "Auto",
                "nav-fill": "Fill (proportional)",
                "nav-justified": "Justified (equal width)"
            },
            "tab": "settings"
        },
        "default_active_tab": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": 0,
            "description": "0 = first tab, 1 = second tab, etc.",
            "tab": "settings"
        },
        "fade": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Enable Fade Animation",
            "tab": "settings"
        }
    },
    "allowed_children": [
        "tab"
    ],
    "tabs": {
        "basic": {
            "label": "Basic",
            "icon": "bi-info-circle",
            "order": 10,
            "view": "auto-generated from schema"
        },
        "settings": {
            "label": "Settings",
            "icon": "bi-sliders",
            "order": 20,
            "view": "auto-generated from schema"
        },
        "style": {
            "label": "Style",
            "icon": "bi-palette",
            "order": 30,
            "view": "auto-generated from schema"
        },
        "advanced": {
            "label": "Visibility",
            "icon": "bi-eye",
            "order": 40,
            "view": "slick-forms::livewire.partials.properties-panel.tabs.advanced"
        }
    }
}
```
