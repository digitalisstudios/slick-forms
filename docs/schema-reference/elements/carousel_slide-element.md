# Carousel Slide

## Overview



## Basic Information

- **Type**: `carousel_slide`
- **Icon**: `bi bi-image`
- **Model**: `SlickFormLayoutElement`

## Properties Reference

### Database Columns

| Property | Type | Required | Default | Description |
|----------|------|----------|---------|-------------|
| `slick_form_id` | `integer` | Yes | - | The form this element belongs to |
| `slick_form_page_id` | `integer` | No | - | The page this element belongs to (for multi-page forms) |
| `parent_id` | `integer` | No | - | Parent element ID for nesting (null = top-level) |
| `element_type` | `string` | Yes | - | Must be 'carousel_slide' for this element type |
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
| `slide_title` | `text` | No | - | Slide Title |
| `slide_icon` | `icon_picker` | No | - | Slide Icon |
| `autoplay_delay` | `number` | No | - | Autoplay Delay (ms) |
| `hash` | `text` | No | - | Hash Navigation ID |
| `history` | `text` | No | - | History Navigation Path |
| `zoom_max_ratio` | `number` | No | - | Zoom Max Ratio |
| `background_color` | `color` | No | - | Background Color |
| `background_image_mode` | `select` | No | `""` | Background Image |
| `background_image_url` | `text` | No | - | Background Image URL |
| `background_image_upload` | `file` | No | - | Upload Background Image |
| `background_image_preview` | `html` | No | - | Preview |
| `text_alignment` | `select` | No | - | Text Alignment |
| `vertical_alignment` | `select` | No | - | Vertical Alignment |
| `min_height` | `text` | No | - | Minimum Height |
| `padding` | `select` | No | - | Padding |
| `custom_class` | `text` | No | - | Custom CSS Classes |
| `custom_style` | `textarea` | No | - | Custom Inline Styles |

## Properties by Tab

### Basic Tab

**`slick_form_id`** *(required)*
- The form this element belongs to

**`slick_form_page_id`**
- The page this element belongs to (for multi-page forms)

**`parent_id`**
- Parent element ID for nesting (null = top-level)

**`element_type`** *(required)*
- Must be 'carousel_slide' for this element type

**`order`** *(required)*
- Display order among siblings

**`settings`**
- 

**`conditional_logic`**
- Visibility rules (same structure as field conditional logic)

**`element_id`** *(required)*
- Unique HTML id attribute (letters, numbers, hyphens, underscores)

**`slide_title`**
- Slide Title

**`slide_icon`**
- Slide Icon

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

**`background_color`**
- Background Color

**`background_image_mode`**
- Background Image
- Options: 
  - `0`: None
  - `1`: Image URL
  - `2`: File Upload

**`background_image_url`**
- Background Image URL

**`background_image_upload`**
- Upload Background Image

**`background_image_preview`**
- Preview

**`text_alignment`**
- Text Alignment
- Options: 
  - `0`: Default
  - `1`: Start
  - `2`: Center
  - `3`: End

**`vertical_alignment`**
- Vertical Alignment
- Options: 
  - `0`: Default
  - `1`: Top
  - `2`: Center
  - `3`: Bottom
  - `4`: Stretch

**`min_height`**
- Minimum Height

### Settings Tab

**`autoplay_delay`**
- Autoplay Delay (ms)

**`hash`**
- Hash Navigation ID

**`history`**
- History Navigation Path

**`zoom_max_ratio`**
- Zoom Max Ratio

### Advanced Tab

**`padding`**
- Padding
- Options: 
  - `0`: Default
  - `1`: None
  - `2`: Extra Small
  - `3`: Small
  - `4`: Medium
  - `5`: Large
  - `6`: Extra Large

**`custom_class`**
- Custom CSS Classes

**`custom_style`**
- Custom Inline Styles

## Usage Example

```php
{
    "slick_form_id": 1,
    "element_type": "carousel_slide",
    "element_id": "slide-1",
    "order": 0,
    "parent_id": 4,
    "settings": {
        "container_label": "Hero Image"
    }
}
```

## JSON Schema

```json
{
    "metadata": {
        "type": "carousel_slide",
        "label": "Carousel Slide",
        "icon": "bi bi-image",
        "description": ""
    },
    "usage": {
        "model": "SlickFormLayoutElement",
        "method": "create",
        "example": {
            "slick_form_id": 1,
            "element_type": "carousel_slide",
            "element_id": "slide-1",
            "order": 0,
            "parent_id": 4,
            "settings": {
                "container_label": "Hero Image"
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
            "value": "carousel_slide",
            "description": "Must be 'carousel_slide' for this element type"
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
        "slide_title": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Slide Title",
            "tab": "basic"
        },
        "slide_icon": {
            "type": "icon_picker",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Slide Icon",
            "tab": "basic"
        },
        "autoplay_delay": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Autoplay Delay (ms)",
            "tab": "settings"
        },
        "hash": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Hash Navigation ID",
            "tab": "settings"
        },
        "history": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "History Navigation Path",
            "tab": "settings"
        },
        "zoom_max_ratio": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Zoom Max Ratio",
            "tab": "settings"
        },
        "background_color": {
            "type": "color",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Background Color",
            "tab": "style"
        },
        "background_image_mode": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": "",
            "description": "Background Image",
            "options": [
                "None",
                "Image URL",
                "File Upload"
            ],
            "options_labels": {
                "": "None",
                "url": "Image URL",
                "upload": "File Upload"
            },
            "tab": "style"
        },
        "background_image_url": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Background Image URL",
            "tab": "style"
        },
        "background_image_upload": {
            "type": "file",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Upload Background Image",
            "tab": "style"
        },
        "background_image_preview": {
            "type": "html",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Preview",
            "tab": "style"
        },
        "text_alignment": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Text Alignment",
            "options": [
                "Default",
                "Start",
                "Center",
                "End"
            ],
            "options_labels": {
                "": "Default",
                "start": "Start",
                "center": "Center",
                "end": "End"
            },
            "tab": "style"
        },
        "vertical_alignment": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Vertical Alignment",
            "options": [
                "Default",
                "Top",
                "Center",
                "Bottom",
                "Stretch"
            ],
            "options_labels": {
                "": "Default",
                "start": "Top",
                "center": "Center",
                "end": "Bottom",
                "stretch": "Stretch"
            },
            "tab": "style"
        },
        "min_height": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Minimum Height",
            "tab": "style"
        },
        "padding": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Padding",
            "options": [
                "Default",
                "None",
                "Extra Small",
                "Small",
                "Medium",
                "Large",
                "Extra Large"
            ],
            "options_labels": {
                "": "Default",
                "p-0": "None",
                "p-1": "Extra Small",
                "p-2": "Small",
                "p-3": "Medium",
                "p-4": "Large",
                "p-5": "Extra Large"
            },
            "tab": "advanced"
        },
        "custom_class": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Custom CSS Classes",
            "tab": "advanced"
        },
        "custom_style": {
            "type": "textarea",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Custom Inline Styles",
            "tab": "advanced"
        }
    },
    "allowed_children": [
        "row",
        "column",
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
