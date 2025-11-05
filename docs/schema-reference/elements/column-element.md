# Column

## Overview



## Basic Information

- **Type**: `column`
- **Icon**: `bi-layout-three-columns`
- **Model**: `SlickFormLayoutElement`

## Properties Reference

### Database Columns

| Property | Type | Required | Default | Description |
|----------|------|----------|---------|-------------|
| `slick_form_id` | `integer` | Yes | - | The form this element belongs to |
| `slick_form_page_id` | `integer` | No | - | The page this element belongs to (for multi-page forms) |
| `parent_id` | `integer` | No | - | Parent element ID for nesting (null = top-level) |
| `element_type` | `string` | Yes | - | Must be 'column' for this element type |
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
| `width.xs` | `select` | No | - | Width - Extra Small (<576px) |
| `width.sm` | `select` | No | - | Width - Small (≥576px) |
| `width.md` | `select` | No | - | Width - Medium (≥768px) |
| `width.lg` | `select` | No | - | Width - Large (≥992px) |
| `width.xl` | `select` | No | - | Width - Extra Large (≥1200px) |
| `width.xxl` | `select` | No | - | Width - XXL (≥1400px) |
| `offset.xs` | `select` | No | - | Offset - XS |
| `offset.sm` | `select` | No | - | Offset - SM |
| `offset.md` | `select` | No | - | Offset - MD |
| `order.xs` | `select` | No | - | Order - XS |
| `order.sm` | `select` | No | - | Order - SM |
| `order.md` | `select` | No | - | Order - MD |
| `align_self` | `select` | No | `""` | Override row's vertical alignment for this column |

## Properties by Tab

### Basic Tab

**`slick_form_id`** *(required)*
- The form this element belongs to

**`slick_form_page_id`**
- The page this element belongs to (for multi-page forms)

**`parent_id`**
- Parent element ID for nesting (null = top-level)

**`element_type`** *(required)*
- Must be 'column' for this element type

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

### Settings Tab

**`width.xs`**
- Width - Extra Small (<576px)
- Options: 
  - `0`: -- Not Set --
  - `1`: Equal Width
  - `2`: Auto Width
  - `3`: 12 (Full)
  - `4`: 11
  - `5`: 10
  - `6`: 9 (3/4)
  - `7`: 8 (2/3)
  - `8`: 7
  - `9`: 6 (1/2)
  - `10`: 5
  - `11`: 4 (1/3)
  - `12`: 3 (1/4)
  - `13`: 2 (1/6)
  - `14`: 1

**`width.sm`**
- Width - Small (≥576px)
- Options: 
  - `0`: -- Not Set --
  - `1`: Equal Width
  - `2`: Auto Width
  - `3`: 12 (Full)
  - `4`: 11
  - `5`: 10
  - `6`: 9 (3/4)
  - `7`: 8 (2/3)
  - `8`: 7
  - `9`: 6 (1/2)
  - `10`: 5
  - `11`: 4 (1/3)
  - `12`: 3 (1/4)
  - `13`: 2 (1/6)
  - `14`: 1

**`width.md`**
- Width - Medium (≥768px)
- Options: 
  - `0`: -- Not Set --
  - `1`: Equal Width
  - `2`: Auto Width
  - `3`: 12 (Full)
  - `4`: 11
  - `5`: 10
  - `6`: 9 (3/4)
  - `7`: 8 (2/3)
  - `8`: 7
  - `9`: 6 (1/2)
  - `10`: 5
  - `11`: 4 (1/3)
  - `12`: 3 (1/4)
  - `13`: 2 (1/6)
  - `14`: 1

**`width.lg`**
- Width - Large (≥992px)
- Options: 
  - `0`: -- Not Set --
  - `1`: Equal Width
  - `2`: Auto Width
  - `3`: 12 (Full)
  - `4`: 11
  - `5`: 10
  - `6`: 9 (3/4)
  - `7`: 8 (2/3)
  - `8`: 7
  - `9`: 6 (1/2)
  - `10`: 5
  - `11`: 4 (1/3)
  - `12`: 3 (1/4)
  - `13`: 2 (1/6)
  - `14`: 1

**`width.xl`**
- Width - Extra Large (≥1200px)
- Options: 
  - `0`: -- Not Set --
  - `1`: Equal Width
  - `2`: Auto Width
  - `3`: 12 (Full)
  - `4`: 11
  - `5`: 10
  - `6`: 9 (3/4)
  - `7`: 8 (2/3)
  - `8`: 7
  - `9`: 6 (1/2)
  - `10`: 5
  - `11`: 4 (1/3)
  - `12`: 3 (1/4)
  - `13`: 2 (1/6)
  - `14`: 1

**`width.xxl`**
- Width - XXL (≥1400px)
- Options: 
  - `0`: -- Not Set --
  - `1`: Equal Width
  - `2`: Auto Width
  - `3`: 12 (Full)
  - `4`: 11
  - `5`: 10
  - `6`: 9 (3/4)
  - `7`: 8 (2/3)
  - `8`: 7
  - `9`: 6 (1/2)
  - `10`: 5
  - `11`: 4 (1/3)
  - `12`: 3 (1/4)
  - `13`: 2 (1/6)
  - `14`: 1

**`offset.xs`**
- Offset - XS
- Options: 
  - `0`: None
  - `1`: 1
  - `2`: 2
  - `3`: 3
  - `4`: 4
  - `5`: 5
  - `6`: 6
  - `7`: 7
  - `8`: 8
  - `9`: 9
  - `10`: 10
  - `11`: 11

**`offset.sm`**
- Offset - SM
- Options: 
  - `0`: None
  - `1`: 1
  - `2`: 2
  - `3`: 3
  - `4`: 4
  - `5`: 5
  - `6`: 6
  - `7`: 7
  - `8`: 8
  - `9`: 9
  - `10`: 10
  - `11`: 11

**`offset.md`**
- Offset - MD
- Options: 
  - `0`: None
  - `1`: 1
  - `2`: 2
  - `3`: 3
  - `4`: 4
  - `5`: 5
  - `6`: 6
  - `7`: 7
  - `8`: 8
  - `9`: 9
  - `10`: 10
  - `11`: 11

**`order.xs`**
- Order - XS
- Options: 
  - `0`: Default
  - `1`: First
  - `2`: Last
  - `3`: 0
  - `4`: 1
  - `5`: 2
  - `6`: 3
  - `7`: 4
  - `8`: 5

**`order.sm`**
- Order - SM
- Options: 
  - `0`: Default
  - `1`: First
  - `2`: Last
  - `3`: 0
  - `4`: 1
  - `5`: 2
  - `6`: 3
  - `7`: 4
  - `8`: 5

**`order.md`**
- Order - MD
- Options: 
  - `0`: Default
  - `1`: First
  - `2`: Last
  - `3`: 0
  - `4`: 1
  - `5`: 2
  - `6`: 3
  - `7`: 4
  - `8`: 5

**`align_self`**
- Override row's vertical alignment for this column
- Options: 
  - `0`: Default
  - `1`: Top
  - `2`: Center
  - `3`: Bottom
  - `4`: Stretch

## Usage Example

```php
{
    "slick_form_id": 1,
    "element_type": "column",
    "element_id": "left-column",
    "order": 0,
    "parent_id": 1,
    "settings": {
        "width": {
            "xs": "12",
            "md": "6",
            "lg": "4"
        },
        "offset": {
            "lg": "1"
        }
    }
}
```

## JSON Schema

```json
{
    "metadata": {
        "type": "column",
        "label": "Column",
        "icon": "bi-layout-three-columns",
        "description": ""
    },
    "usage": {
        "model": "SlickFormLayoutElement",
        "method": "create",
        "example": {
            "slick_form_id": 1,
            "element_type": "column",
            "element_id": "left-column",
            "order": 0,
            "parent_id": 1,
            "settings": {
                "width": {
                    "xs": "12",
                    "md": "6",
                    "lg": "4"
                },
                "offset": {
                    "lg": "1"
                }
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
            "value": "column",
            "description": "Must be 'column' for this element type"
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
        "width.xs": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Width - Extra Small (<576px)",
            "options": [
                "-- Not Set --",
                "Equal Width",
                "Auto Width",
                "12 (Full)",
                "11",
                "10",
                "9 (3\/4)",
                "8 (2\/3)",
                "7",
                "6 (1\/2)",
                "5",
                "4 (1\/3)",
                "3 (1\/4)",
                "2 (1\/6)",
                "1"
            ],
            "options_labels": {
                "": "-- Not Set --",
                "equal": "Equal Width",
                "auto": "Auto Width",
                "12": "12 (Full)",
                "11": "11",
                "10": "10",
                "9": "9 (3\/4)",
                "8": "8 (2\/3)",
                "7": "7",
                "6": "6 (1\/2)",
                "5": "5",
                "4": "4 (1\/3)",
                "3": "3 (1\/4)",
                "2": "2 (1\/6)",
                "1": "1"
            },
            "tab": "settings"
        },
        "width.sm": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Width - Small (\u2265576px)",
            "options": [
                "-- Not Set --",
                "Equal Width",
                "Auto Width",
                "12 (Full)",
                "11",
                "10",
                "9 (3\/4)",
                "8 (2\/3)",
                "7",
                "6 (1\/2)",
                "5",
                "4 (1\/3)",
                "3 (1\/4)",
                "2 (1\/6)",
                "1"
            ],
            "options_labels": {
                "": "-- Not Set --",
                "equal": "Equal Width",
                "auto": "Auto Width",
                "12": "12 (Full)",
                "11": "11",
                "10": "10",
                "9": "9 (3\/4)",
                "8": "8 (2\/3)",
                "7": "7",
                "6": "6 (1\/2)",
                "5": "5",
                "4": "4 (1\/3)",
                "3": "3 (1\/4)",
                "2": "2 (1\/6)",
                "1": "1"
            },
            "tab": "settings"
        },
        "width.md": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Width - Medium (\u2265768px)",
            "options": [
                "-- Not Set --",
                "Equal Width",
                "Auto Width",
                "12 (Full)",
                "11",
                "10",
                "9 (3\/4)",
                "8 (2\/3)",
                "7",
                "6 (1\/2)",
                "5",
                "4 (1\/3)",
                "3 (1\/4)",
                "2 (1\/6)",
                "1"
            ],
            "options_labels": {
                "": "-- Not Set --",
                "equal": "Equal Width",
                "auto": "Auto Width",
                "12": "12 (Full)",
                "11": "11",
                "10": "10",
                "9": "9 (3\/4)",
                "8": "8 (2\/3)",
                "7": "7",
                "6": "6 (1\/2)",
                "5": "5",
                "4": "4 (1\/3)",
                "3": "3 (1\/4)",
                "2": "2 (1\/6)",
                "1": "1"
            },
            "tab": "settings"
        },
        "width.lg": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Width - Large (\u2265992px)",
            "options": [
                "-- Not Set --",
                "Equal Width",
                "Auto Width",
                "12 (Full)",
                "11",
                "10",
                "9 (3\/4)",
                "8 (2\/3)",
                "7",
                "6 (1\/2)",
                "5",
                "4 (1\/3)",
                "3 (1\/4)",
                "2 (1\/6)",
                "1"
            ],
            "options_labels": {
                "": "-- Not Set --",
                "equal": "Equal Width",
                "auto": "Auto Width",
                "12": "12 (Full)",
                "11": "11",
                "10": "10",
                "9": "9 (3\/4)",
                "8": "8 (2\/3)",
                "7": "7",
                "6": "6 (1\/2)",
                "5": "5",
                "4": "4 (1\/3)",
                "3": "3 (1\/4)",
                "2": "2 (1\/6)",
                "1": "1"
            },
            "tab": "settings"
        },
        "width.xl": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Width - Extra Large (\u22651200px)",
            "options": [
                "-- Not Set --",
                "Equal Width",
                "Auto Width",
                "12 (Full)",
                "11",
                "10",
                "9 (3\/4)",
                "8 (2\/3)",
                "7",
                "6 (1\/2)",
                "5",
                "4 (1\/3)",
                "3 (1\/4)",
                "2 (1\/6)",
                "1"
            ],
            "options_labels": {
                "": "-- Not Set --",
                "equal": "Equal Width",
                "auto": "Auto Width",
                "12": "12 (Full)",
                "11": "11",
                "10": "10",
                "9": "9 (3\/4)",
                "8": "8 (2\/3)",
                "7": "7",
                "6": "6 (1\/2)",
                "5": "5",
                "4": "4 (1\/3)",
                "3": "3 (1\/4)",
                "2": "2 (1\/6)",
                "1": "1"
            },
            "tab": "settings"
        },
        "width.xxl": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Width - XXL (\u22651400px)",
            "options": [
                "-- Not Set --",
                "Equal Width",
                "Auto Width",
                "12 (Full)",
                "11",
                "10",
                "9 (3\/4)",
                "8 (2\/3)",
                "7",
                "6 (1\/2)",
                "5",
                "4 (1\/3)",
                "3 (1\/4)",
                "2 (1\/6)",
                "1"
            ],
            "options_labels": {
                "": "-- Not Set --",
                "equal": "Equal Width",
                "auto": "Auto Width",
                "12": "12 (Full)",
                "11": "11",
                "10": "10",
                "9": "9 (3\/4)",
                "8": "8 (2\/3)",
                "7": "7",
                "6": "6 (1\/2)",
                "5": "5",
                "4": "4 (1\/3)",
                "3": "3 (1\/4)",
                "2": "2 (1\/6)",
                "1": "1"
            },
            "tab": "settings"
        },
        "offset.xs": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Offset - XS",
            "options": [
                "None",
                "1",
                "2",
                "3",
                "4",
                "5",
                "6",
                "7",
                "8",
                "9",
                "10",
                "11"
            ],
            "options_labels": {
                "": "None",
                "1": "1",
                "2": "2",
                "3": "3",
                "4": "4",
                "5": "5",
                "6": "6",
                "7": "7",
                "8": "8",
                "9": "9",
                "10": "10",
                "11": "11"
            },
            "tab": "settings"
        },
        "offset.sm": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Offset - SM",
            "options": [
                "None",
                "1",
                "2",
                "3",
                "4",
                "5",
                "6",
                "7",
                "8",
                "9",
                "10",
                "11"
            ],
            "options_labels": {
                "": "None",
                "1": "1",
                "2": "2",
                "3": "3",
                "4": "4",
                "5": "5",
                "6": "6",
                "7": "7",
                "8": "8",
                "9": "9",
                "10": "10",
                "11": "11"
            },
            "tab": "settings"
        },
        "offset.md": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Offset - MD",
            "options": [
                "None",
                "1",
                "2",
                "3",
                "4",
                "5",
                "6",
                "7",
                "8",
                "9",
                "10",
                "11"
            ],
            "options_labels": {
                "": "None",
                "1": "1",
                "2": "2",
                "3": "3",
                "4": "4",
                "5": "5",
                "6": "6",
                "7": "7",
                "8": "8",
                "9": "9",
                "10": "10",
                "11": "11"
            },
            "tab": "settings"
        },
        "order.xs": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Order - XS",
            "options": [
                "Default",
                "First",
                "Last",
                "0",
                "1",
                "2",
                "3",
                "4",
                "5"
            ],
            "options_labels": {
                "": "Default",
                "first": "First",
                "last": "Last",
                "0": "0",
                "1": "1",
                "2": "2",
                "3": "3",
                "4": "4",
                "5": "5"
            },
            "tab": "settings"
        },
        "order.sm": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Order - SM",
            "options": [
                "Default",
                "First",
                "Last",
                "0",
                "1",
                "2",
                "3",
                "4",
                "5"
            ],
            "options_labels": {
                "": "Default",
                "first": "First",
                "last": "Last",
                "0": "0",
                "1": "1",
                "2": "2",
                "3": "3",
                "4": "4",
                "5": "5"
            },
            "tab": "settings"
        },
        "order.md": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Order - MD",
            "options": [
                "Default",
                "First",
                "Last",
                "0",
                "1",
                "2",
                "3",
                "4",
                "5"
            ],
            "options_labels": {
                "": "Default",
                "first": "First",
                "last": "Last",
                "0": "0",
                "1": "1",
                "2": "2",
                "3": "3",
                "4": "4",
                "5": "5"
            },
            "tab": "settings"
        },
        "align_self": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": "",
            "description": "Override row's vertical alignment for this column",
            "options": [
                "Default",
                "Top",
                "Center",
                "Bottom",
                "Stretch"
            ],
            "options_labels": {
                "": "Default",
                "align-self-start": "Top",
                "align-self-center": "Center",
                "align-self-end": "Bottom",
                "align-self-stretch": "Stretch"
            },
            "tab": "settings"
        }
    },
    "allowed_children": [
        "row",
        "field",
        "card",
        "accordion",
        "tabs",
        "table"
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
