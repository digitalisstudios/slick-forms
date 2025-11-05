# Table Cell

## Overview



## Basic Information

- **Type**: `table_cell`
- **Icon**: `bi-square`
- **Model**: `SlickFormLayoutElement`

## Properties Reference

### Database Columns

| Property | Type | Required | Default | Description |
|----------|------|----------|---------|-------------|
| `slick_form_id` | `integer` | Yes | - | The form this element belongs to |
| `slick_form_page_id` | `integer` | No | - | The page this element belongs to (for multi-page forms) |
| `parent_id` | `integer` | No | - | Parent element ID for nesting (null = top-level) |
| `element_type` | `string` | Yes | - | Must be 'table_cell' for this element type |
| `order` | `integer` | Yes | - | Display order among siblings |
| `settings` | `mixed` | No | - |  |
| `element_id` | `text` | Yes | - | Unique HTML id attribute (letters, numbers, hyphens, underscores) |
| `class` | `text` | No | - | Space-separated CSS class names |
| `style` | `textarea` | No | - | Custom CSS styles for this element |

### JSON Options (`options` column)

| Property | Type | Required | Default | Description |
|----------|------|----------|---------|-------------|
| `conditional_logic` | `object` | No | - | Visibility rules (same structure as field conditional logic) |
| `spacing.margin_top` | `select` | No | - | Margin Top |
| `spacing.margin_bottom` | `select` | No | - | Margin Bottom |
| `spacing.padding_top` | `select` | No | - | Padding Top |
| `spacing.padding_bottom` | `select` | No | - | Padding Bottom |
| `display.display` | `select` | No | - | Display (XS) |
| `display.display_sm` | `select` | No | - | Display (SM) |
| `display.display_md` | `select` | No | - | Display (MD) |
| `text_alignment.align` | `select` | No | - | Text Alignment |
| `cell_type` | `select` | No | - | Header cells are typically bold and used in thead rows |
| `colspan` | `number` | No | `1` | Number of columns this cell spans |
| `rowspan` | `number` | No | `1` | Number of rows this cell spans |
| `width` | `text` | No | - | Custom width for this cell |
| `align` | `select` | No | - | Horizontal Alignment |
| `valign` | `select` | No | - | Vertical Alignment |
| `background_color` | `select` | No | - | Background Color |

## Properties by Tab

### Basic Tab

**`slick_form_id`** *(required)*
- The form this element belongs to

**`slick_form_page_id`**
- The page this element belongs to (for multi-page forms)

**`parent_id`**
- Parent element ID for nesting (null = top-level)

**`element_type`** *(required)*
- Must be 'table_cell' for this element type

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

**`style`**
- Custom CSS styles for this element

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

**`align`**
- Horizontal Alignment
- Options: 
  - `0`: Default
  - `1`: Start (Left)
  - `2`: Center
  - `3`: End (Right)

**`valign`**
- Vertical Alignment
- Options: 
  - `0`: Default
  - `1`: Top
  - `2`: Middle
  - `3`: Bottom

**`background_color`**
- Background Color
- Options: 
  - `0`: None
  - `1`: Primary
  - `2`: Secondary
  - `3`: Success
  - `4`: Danger
  - `5`: Warning
  - `6`: Info
  - `7`: Light
  - `8`: Dark

### Settings Tab

**`cell_type`**
- Header cells are typically bold and used in thead rows
- Options: 
  - `0`: Data Cell (td)
  - `1`: Header Cell (th)

**`colspan`**
- Number of columns this cell spans

**`rowspan`**
- Number of rows this cell spans

**`width`**
- Custom width for this cell

## Usage Example

```php
{
    "slick_form_id": 1,
    "element_type": "table_cell",
    "element_id": "table_cell-element",
    "order": 0,
    "parent_id": 6
}
```

## JSON Schema

```json
{
    "metadata": {
        "type": "table_cell",
        "label": "Table Cell",
        "icon": "bi-square",
        "description": ""
    },
    "usage": {
        "model": "SlickFormLayoutElement",
        "method": "create",
        "example": {
            "slick_form_id": 1,
            "element_type": "table_cell",
            "element_id": "table_cell-element",
            "order": 0,
            "parent_id": 6
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
            "value": "table_cell",
            "description": "Must be 'table_cell' for this element type"
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
            "type": "textarea",
            "required": false,
            "storage": "column",
            "default": null,
            "description": "Custom CSS styles for this element",
            "tab": "style"
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
        "cell_type": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Header cells are typically bold and used in thead rows",
            "options": [
                "Data Cell (td)",
                "Header Cell (th)"
            ],
            "options_labels": {
                "td": "Data Cell (td)",
                "th": "Header Cell (th)"
            },
            "tab": "settings"
        },
        "colspan": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": 1,
            "description": "Number of columns this cell spans",
            "tab": "settings"
        },
        "rowspan": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": 1,
            "description": "Number of rows this cell spans",
            "tab": "settings"
        },
        "width": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Custom width for this cell",
            "tab": "settings"
        },
        "align": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Horizontal Alignment",
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
        "valign": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Vertical Alignment",
            "options": [
                "Default",
                "Top",
                "Middle",
                "Bottom"
            ],
            "options_labels": {
                "": "Default",
                "align-top": "Top",
                "align-middle": "Middle",
                "align-bottom": "Bottom"
            },
            "tab": "style"
        },
        "background_color": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Background Color",
            "options": [
                "None",
                "Primary",
                "Secondary",
                "Success",
                "Danger",
                "Warning",
                "Info",
                "Light",
                "Dark"
            ],
            "options_labels": {
                "": "None",
                "table-primary": "Primary",
                "table-secondary": "Secondary",
                "table-success": "Success",
                "table-danger": "Danger",
                "table-warning": "Warning",
                "table-info": "Info",
                "table-light": "Light",
                "table-dark": "Dark"
            },
            "tab": "style"
        }
    },
    "allowed_children": [
        "field",
        "row",
        "card",
        "accordion",
        "tabs",
        "table",
        "container"
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
