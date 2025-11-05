# Carousel

## Overview



## Basic Information

- **Type**: `carousel`
- **Icon**: `bi-collection-play`
- **Model**: `SlickFormLayoutElement`

## Properties Reference

### Database Columns

| Property | Type | Required | Default | Description |
|----------|------|----------|---------|-------------|
| `slick_form_id` | `integer` | Yes | - | The form this element belongs to |
| `slick_form_page_id` | `integer` | No | - | The page this element belongs to (for multi-page forms) |
| `parent_id` | `integer` | No | - | Parent element ID for nesting (null = top-level) |
| `element_type` | `string` | Yes | - | Must be 'carousel' for this element type |
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
| `preset` | `custom` | No | `""` | ⚠️ Applying a preset will replace ALL current settings and slides. |
| `direction` | `select` | No | `"horizontal"` | Slider direction |
| `speed` | `number` | No | `300` | Duration of transition between slides in milliseconds |
| `loop` | `switch` | No | `false` | Set to true to enable continuous loop mode (infinite carousel) |
| `rewind` | `switch` | No | `false` | When reaching the end, rewind to beginning (alternative to loop mode) |
| `slidesPerView` | `text` | No | `"1"` | Number of slides visible at once. Use number (1, 2, 3...) or "auto" for automatic sizing |
| `initialSlide` | `number` | No | `0` | Index number of slide to display on load (0 = first slide, 1 = second, etc.) |
| `spaceBetween` | `number` | No | `0` | Distance between slides in pixels |
| `slidesPerGroup` | `number` | No | `1` | Number of slides to skip when navigating (e.g., 3 = skip 3 slides per click) |
| `centeredSlides` | `switch` | No | `false` | If true, active slide will be centered, not always at the left edge |
| `autoHeight` | `switch` | No | `false` | Automatically adapt carousel height to active slide content |
| `grabCursor` | `switch` | No | `false` | Show hand/grab cursor when hovering over carousel |
| `effect` | `select` | No | `"slide"` | Choose transition effect between slides |
| `coverflow_tip` | `html` | No | - |  |
| `fadeEffect_crossFade` | `switch` | No | `false` | Enable cross-fade transition (overlapping slides) |
| `cubeEffect_shadow` | `switch` | No | `true` | Enable main cube shadow effect |
| `cubeEffect_shadowOffset` | `number` | No | `20` | Main shadow offset in pixels |
| `cubeEffect_shadowScale` | `number` | No | `0.94` | Main shadow scale ratio |
| `cubeEffect_slideShadows` | `switch` | No | `true` | Enable individual slide shadows |
| `coverflowEffect_rotate` | `number` | No | `50` | Slide rotation angle in degrees |
| `coverflowEffect_stretch` | `number` | No | `0` | Stretch space between slides (px) |
| `coverflowEffect_depth` | `number` | No | `100` | Depth offset in pixels (slides translate in Z axis) |
| `coverflowEffect_modifier` | `number` | No | `1` | Effect multiplier |
| `coverflowEffect_scale` | `number` | No | `1` | Slide scale effect |
| `coverflowEffect_slideShadows` | `switch` | No | `true` | Enable individual slide shadows |
| `allowSlideNext` | `switch` | No | `true` | Enable forward navigation (swipe right/click next button) |
| `allowSlidePrev` | `switch` | No | `true` | Enable backward navigation (swipe left/click prev button) |
| `allowTouchMove` | `switch` | No | `true` | Enable touch/mouse drag to navigate slides |
| `slideToClickedSlide` | `switch` | No | `false` | Click any visible slide to navigate to it (useful with multiple slides per view) |
| `navigation_enabled` | `switch` | No | `false` | Show previous/next navigation arrows |
| `navigation_hideOnClick` | `switch` | No | `false` | Toggle navigation visibility when carousel is clicked |
| `pagination_enabled` | `switch` | No | `false` | Show pagination indicators |
| `pagination_type` | `select` | No | `"bullets"` | Type of pagination indicator to display |
| `pagination_clickable` | `switch` | No | `true` | Make pagination bullets clickable to navigate |
| `pagination_dynamicBullets` | `switch` | No | `false` | Show limited number of pagination bullets with active highlighted |
| `autoplay_enabled` | `switch` | No | `false` | Automatically transition between slides |
| `autoplay_delay` | `number` | No | `3000` | Time between slide transitions in milliseconds |
| `autoplay_pauseOnMouseEnter` | `switch` | No | `false` | Pause autoplay when mouse hovers over carousel |
| `autoplay_disableOnInteraction` | `switch` | No | `true` | Disable autoplay after user interacts with carousel |
| `scrollbar_enabled` | `switch` | No | `false` | Show scrollbar indicator |
| `scrollbar_draggable` | `switch` | No | `false` | Allow scrollbar to be draggable |
| `scrollbar_hide` | `switch` | No | `true` | Automatically hide scrollbar after interaction |
| `a11y_enabled` | `switch` | No | `true` | Add ARIA labels and keyboard support for screen readers |
| `a11y_prevSlideMessage` | `text` | No | `"Previous slide"` | ARIA label for previous button |
| `a11y_nextSlideMessage` | `text` | No | `"Next slide"` | ARIA label for next button |
| `a11y_firstSlideMessage` | `text` | No | `"This is the first slide"` | Screen reader message when reaching first slide |
| `a11y_lastSlideMessage` | `text` | No | `"This is the last slide"` | Screen reader message when reaching last slide |
| `keyboard_enabled` | `switch` | No | `false` | Enable keyboard control (arrow keys) |
| `keyboard_onlyInViewport` | `switch` | No | `true` | Keyboard control only works when carousel is visible in viewport |
| `mousewheel_enabled` | `switch` | No | `false` | Enable navigation by mouse wheel scrolling |
| `mousewheel_invert` | `switch` | No | `false` | Reverse mousewheel scroll direction |
| `mousewheel_sensitivity` | `number` | No | `1` | Mousewheel scroll sensitivity multiplier |
| `zoom_enabled` | `switch` | No | `false` | Enable pinch-to-zoom on slides (mobile/desktop) |
| `zoom_maxRatio` | `number` | No | `3` | Maximum zoom scale |
| `zoom_minRatio` | `number` | No | `1` | Minimum zoom scale |
| `zoom_toggle` | `switch` | No | `true` | Enable double-tap/click to toggle zoom |
| `parallax_enabled` | `switch` | No | `false` | Enable parallax effects with a single background image that slides behind content |
| `parallax_bg_mode` | `select` | No | `""` | Background image for parallax effect |
| `parallax_bg_url` | `text` | No | - | URL of the parallax background image |
| `parallax_bg_upload` | `file` | No | - | Upload a background image for parallax effect |
| `parallax_bg_preview` | `html` | No | - | Preview |
| `freeMode_enabled` | `switch` | No | `false` | Slides will not snap to positions, continuous scrolling |
| `freeMode_momentum` | `switch` | No | `true` | Enable momentum scrolling |
| `freeMode_sticky` | `switch` | No | `false` | Snap to slides after free mode scroll ends |
| `grid_rows` | `number` | No | `1` | Number of slides rows (for grid layout) |
| `grid_fill` | `select` | No | `"row"` | How grid slides are filled |
| `breakpoints_enabled` | `switch` | No | `false` | Override settings at specific screen widths |
| `breakpoint_640_slidesPerView` | `text` | No | `""` | Number of slides on mobile (640px and up) |
| `breakpoint_640_spaceBetween` | `number` | No | - | Space between slides on mobile |
| `breakpoint_768_slidesPerView` | `text` | No | `""` | Number of slides on tablet (768px and up) |
| `breakpoint_768_spaceBetween` | `number` | No | - | Space between slides on tablet |
| `breakpoint_1024_slidesPerView` | `text` | No | `""` | Number of slides on desktop (1024px and up) |
| `breakpoint_1024_spaceBetween` | `number` | No | - | Space between slides on desktop |
| `breakpoint_1280_slidesPerView` | `text` | No | `""` | Number of slides on large desktop (1280px and up) |
| `breakpoint_1280_spaceBetween` | `number` | No | - | Space between slides on large desktop |
| `lazy` | `switch` | No | `false` | Load images only when slides become visible (improves performance for image-heavy carousels) |
| `builderMode` | `select` | No | `"preview"` | Toggle between editable list view and live preview mode in the form builder |

## Properties by Tab

### Basic Tab

**`slick_form_id`** *(required)*
- The form this element belongs to

**`slick_form_page_id`**
- The page this element belongs to (for multi-page forms)

**`parent_id`**
- Parent element ID for nesting (null = top-level)

**`element_type`** *(required)*
- Must be 'carousel' for this element type

**`order`** *(required)*
- Display order among siblings

**`settings`**
- 

**`conditional_logic`**
- Visibility rules (same structure as field conditional logic)

**`element_id`** *(required)*
- Unique HTML id attribute (letters, numbers, hyphens, underscores)

**`preset`**
- ⚠️ Applying a preset will replace ALL current settings and slides.
- Options: 
  - `0`: Custom (No Preset)
  - `1`: Image Gallery
  - `2`: Testimonials
  - `3`: Product Showcase
  - `4`: Hero Slider
  - `5`: Album Gallery (Coverflow)
  - `6`: Portfolio Showcase (Cube)
  - `7`: Thumbnail Grid
  - `8`: Content Cards
  - `9`: Timeline
  - `10`: Before/After Comparison

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

**`direction`**
- Slider direction
- Options: 
  - `0`: Horizontal
  - `1`: Vertical

**`speed`**
- Duration of transition between slides in milliseconds

**`loop`**
- Set to true to enable continuous loop mode (infinite carousel)

**`rewind`**
- When reaching the end, rewind to beginning (alternative to loop mode)

**`slidesPerView`**
- Number of slides visible at once. Use number (1, 2, 3...) or "auto" for automatic sizing

**`initialSlide`**
- Index number of slide to display on load (0 = first slide, 1 = second, etc.)

**`spaceBetween`**
- Distance between slides in pixels

**`slidesPerGroup`**
- Number of slides to skip when navigating (e.g., 3 = skip 3 slides per click)

**`centeredSlides`**
- If true, active slide will be centered, not always at the left edge

**`autoHeight`**
- Automatically adapt carousel height to active slide content

**`grabCursor`**
- Show hand/grab cursor when hovering over carousel

### Effects Tab

**`effect`**
- Choose transition effect between slides
- Options: 
  - `0`: Slide (Default)
  - `1`: Fade
  - `2`: Cube (3D)
  - `3`: Coverflow (3D)
  - `4`: Flip (3D)
  - `5`: Cards
  - `6`: Creative (Custom)

**`coverflow_tip`**
- 

**`fadeEffect_crossFade`**
- Enable cross-fade transition (overlapping slides)

**`cubeEffect_shadow`**
- Enable main cube shadow effect

**`cubeEffect_shadowOffset`**
- Main shadow offset in pixels

**`cubeEffect_shadowScale`**
- Main shadow scale ratio

**`cubeEffect_slideShadows`**
- Enable individual slide shadows

**`coverflowEffect_rotate`**
- Slide rotation angle in degrees

**`coverflowEffect_stretch`**
- Stretch space between slides (px)

**`coverflowEffect_depth`**
- Depth offset in pixels (slides translate in Z axis)

**`coverflowEffect_modifier`**
- Effect multiplier

**`coverflowEffect_scale`**
- Slide scale effect

**`coverflowEffect_slideShadows`**
- Enable individual slide shadows

### Interaction Tab

**`allowSlideNext`**
- Enable forward navigation (swipe right/click next button)

**`allowSlidePrev`**
- Enable backward navigation (swipe left/click prev button)

**`allowTouchMove`**
- Enable touch/mouse drag to navigate slides

**`slideToClickedSlide`**
- Click any visible slide to navigate to it (useful with multiple slides per view)

### Navigation Tab

**`navigation_enabled`**
- Show previous/next navigation arrows

**`navigation_hideOnClick`**
- Toggle navigation visibility when carousel is clicked

### Pagination Tab

**`pagination_enabled`**
- Show pagination indicators

**`pagination_type`**
- Type of pagination indicator to display
- Options: 
  - `0`: Bullets
  - `1`: Fraction (1 / 5)
  - `2`: Progress Bar

**`pagination_clickable`**
- Make pagination bullets clickable to navigate

**`pagination_dynamicBullets`**
- Show limited number of pagination bullets with active highlighted

### Autoplay Tab

**`autoplay_enabled`**
- Automatically transition between slides

**`autoplay_delay`**
- Time between slide transitions in milliseconds

**`autoplay_pauseOnMouseEnter`**
- Pause autoplay when mouse hovers over carousel

**`autoplay_disableOnInteraction`**
- Disable autoplay after user interacts with carousel

### Scrollbar Tab

**`scrollbar_enabled`**
- Show scrollbar indicator

**`scrollbar_draggable`**
- Allow scrollbar to be draggable

**`scrollbar_hide`**
- Automatically hide scrollbar after interaction

### Accessibility Tab

**`a11y_enabled`**
- Add ARIA labels and keyboard support for screen readers

**`a11y_prevSlideMessage`**
- ARIA label for previous button

**`a11y_nextSlideMessage`**
- ARIA label for next button

**`a11y_firstSlideMessage`**
- Screen reader message when reaching first slide

**`a11y_lastSlideMessage`**
- Screen reader message when reaching last slide

### Advanced Tab

**`keyboard_enabled`**
- Enable keyboard control (arrow keys)

**`keyboard_onlyInViewport`**
- Keyboard control only works when carousel is visible in viewport

**`mousewheel_enabled`**
- Enable navigation by mouse wheel scrolling

**`mousewheel_invert`**
- Reverse mousewheel scroll direction

**`mousewheel_sensitivity`**
- Mousewheel scroll sensitivity multiplier

**`zoom_enabled`**
- Enable pinch-to-zoom on slides (mobile/desktop)

**`zoom_maxRatio`**
- Maximum zoom scale

**`zoom_minRatio`**
- Minimum zoom scale

**`zoom_toggle`**
- Enable double-tap/click to toggle zoom

**`parallax_enabled`**
- Enable parallax effects with a single background image that slides behind content

**`parallax_bg_mode`**
- Background image for parallax effect
- Options: 
  - `0`: None
  - `1`: Image URL
  - `2`: File Upload

**`parallax_bg_url`**
- URL of the parallax background image

**`parallax_bg_upload`**
- Upload a background image for parallax effect

**`parallax_bg_preview`**
- Preview

**`freeMode_enabled`**
- Slides will not snap to positions, continuous scrolling

**`freeMode_momentum`**
- Enable momentum scrolling

**`freeMode_sticky`**
- Snap to slides after free mode scroll ends

**`grid_rows`**
- Number of slides rows (for grid layout)

**`grid_fill`**
- How grid slides are filled
- Options: 
  - `0`: Row
  - `1`: Column

**`breakpoints_enabled`**
- Override settings at specific screen widths

**`breakpoint_640_slidesPerView`**
- Number of slides on mobile (640px and up)

**`breakpoint_640_spaceBetween`**
- Space between slides on mobile

**`breakpoint_768_slidesPerView`**
- Number of slides on tablet (768px and up)

**`breakpoint_768_spaceBetween`**
- Space between slides on tablet

**`breakpoint_1024_slidesPerView`**
- Number of slides on desktop (1024px and up)

**`breakpoint_1024_spaceBetween`**
- Space between slides on desktop

**`breakpoint_1280_slidesPerView`**
- Number of slides on large desktop (1280px and up)

**`breakpoint_1280_spaceBetween`**
- Space between slides on large desktop

**`lazy`**
- Load images only when slides become visible (improves performance for image-heavy carousels)

**`builderMode`**
- Toggle between editable list view and live preview mode in the form builder
- Options: 
  - `0`: List View (Edit)
  - `1`: Preview Mode (Read-only)

## Usage Example

```php
{
    "slick_form_id": 1,
    "element_type": "carousel",
    "element_id": "hero-carousel",
    "order": 0,
    "settings": {
        "direction": "horizontal",
        "speed": 300,
        "loop": true,
        "slides_per_view": 1,
        "space_between": 30,
        "autoplay": {
            "enabled": true,
            "delay": 3000
        },
        "navigation": {
            "enabled": true
        },
        "pagination": {
            "enabled": true,
            "type": "bullets",
            "clickable": true
        }
    }
}
```

## JSON Schema

```json
{
    "metadata": {
        "type": "carousel",
        "label": "Carousel",
        "icon": "bi-collection-play",
        "description": ""
    },
    "usage": {
        "model": "SlickFormLayoutElement",
        "method": "create",
        "example": {
            "slick_form_id": 1,
            "element_type": "carousel",
            "element_id": "hero-carousel",
            "order": 0,
            "settings": {
                "direction": "horizontal",
                "speed": 300,
                "loop": true,
                "slides_per_view": 1,
                "space_between": 30,
                "autoplay": {
                    "enabled": true,
                    "delay": 3000
                },
                "navigation": {
                    "enabled": true
                },
                "pagination": {
                    "enabled": true,
                    "type": "bullets",
                    "clickable": true
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
            "value": "carousel",
            "description": "Must be 'carousel' for this element type"
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
        "preset": {
            "type": "custom",
            "required": false,
            "storage": "json_column",
            "default": "",
            "description": "\u26a0\ufe0f Applying a preset will replace ALL current settings and slides.",
            "options": [
                "Custom (No Preset)",
                "Image Gallery",
                "Testimonials",
                "Product Showcase",
                "Hero Slider",
                "Album Gallery (Coverflow)",
                "Portfolio Showcase (Cube)",
                "Thumbnail Grid",
                "Content Cards",
                "Timeline",
                "Before\/After Comparison"
            ],
            "options_labels": {
                "": "Custom (No Preset)",
                "image_gallery": "Image Gallery",
                "testimonials": "Testimonials",
                "product_showcase": "Product Showcase",
                "hero_slider": "Hero Slider",
                "album_gallery": "Album Gallery (Coverflow)",
                "portfolio_showcase": "Portfolio Showcase (Cube)",
                "thumbnail_gallery": "Thumbnail Grid",
                "content_cards": "Content Cards",
                "timeline": "Timeline",
                "before_after": "Before\/After Comparison"
            },
            "tab": "basic"
        },
        "direction": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": "horizontal",
            "description": "Slider direction",
            "options": [
                "Horizontal",
                "Vertical"
            ],
            "options_labels": {
                "horizontal": "Horizontal",
                "vertical": "Vertical"
            },
            "tab": "settings"
        },
        "speed": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": 300,
            "description": "Duration of transition between slides in milliseconds",
            "tab": "settings"
        },
        "loop": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Set to true to enable continuous loop mode (infinite carousel)",
            "tab": "settings"
        },
        "rewind": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "When reaching the end, rewind to beginning (alternative to loop mode)",
            "tab": "settings"
        },
        "slidesPerView": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": "1",
            "description": "Number of slides visible at once. Use number (1, 2, 3...) or \"auto\" for automatic sizing",
            "tab": "settings"
        },
        "initialSlide": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": 0,
            "description": "Index number of slide to display on load (0 = first slide, 1 = second, etc.)",
            "tab": "settings"
        },
        "spaceBetween": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": 0,
            "description": "Distance between slides in pixels",
            "tab": "settings"
        },
        "slidesPerGroup": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": 1,
            "description": "Number of slides to skip when navigating (e.g., 3 = skip 3 slides per click)",
            "tab": "settings"
        },
        "centeredSlides": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "If true, active slide will be centered, not always at the left edge",
            "tab": "settings"
        },
        "autoHeight": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Automatically adapt carousel height to active slide content",
            "tab": "settings"
        },
        "grabCursor": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Show hand\/grab cursor when hovering over carousel",
            "tab": "settings"
        },
        "effect": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": "slide",
            "description": "Choose transition effect between slides",
            "options": [
                "Slide (Default)",
                "Fade",
                "Cube (3D)",
                "Coverflow (3D)",
                "Flip (3D)",
                "Cards",
                "Creative (Custom)"
            ],
            "options_labels": {
                "slide": "Slide (Default)",
                "fade": "Fade",
                "cube": "Cube (3D)",
                "coverflow": "Coverflow (3D)",
                "flip": "Flip (3D)",
                "cards": "Cards",
                "creative": "Creative (Custom)"
            },
            "tab": "effects"
        },
        "coverflow_tip": {
            "type": "html",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "",
            "tab": "effects"
        },
        "fadeEffect_crossFade": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Enable cross-fade transition (overlapping slides)",
            "tab": "effects"
        },
        "cubeEffect_shadow": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": true,
            "description": "Enable main cube shadow effect",
            "tab": "effects"
        },
        "cubeEffect_shadowOffset": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": 20,
            "description": "Main shadow offset in pixels",
            "tab": "effects"
        },
        "cubeEffect_shadowScale": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": 0.94,
            "description": "Main shadow scale ratio",
            "tab": "effects"
        },
        "cubeEffect_slideShadows": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": true,
            "description": "Enable individual slide shadows",
            "tab": "effects"
        },
        "coverflowEffect_rotate": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": 50,
            "description": "Slide rotation angle in degrees",
            "tab": "effects"
        },
        "coverflowEffect_stretch": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": 0,
            "description": "Stretch space between slides (px)",
            "tab": "effects"
        },
        "coverflowEffect_depth": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": 100,
            "description": "Depth offset in pixels (slides translate in Z axis)",
            "tab": "effects"
        },
        "coverflowEffect_modifier": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": 1,
            "description": "Effect multiplier",
            "tab": "effects"
        },
        "coverflowEffect_scale": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": 1,
            "description": "Slide scale effect",
            "tab": "effects"
        },
        "coverflowEffect_slideShadows": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": true,
            "description": "Enable individual slide shadows",
            "tab": "effects"
        },
        "allowSlideNext": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": true,
            "description": "Enable forward navigation (swipe right\/click next button)",
            "tab": "interaction"
        },
        "allowSlidePrev": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": true,
            "description": "Enable backward navigation (swipe left\/click prev button)",
            "tab": "interaction"
        },
        "allowTouchMove": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": true,
            "description": "Enable touch\/mouse drag to navigate slides",
            "tab": "interaction"
        },
        "slideToClickedSlide": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Click any visible slide to navigate to it (useful with multiple slides per view)",
            "tab": "interaction"
        },
        "navigation_enabled": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Show previous\/next navigation arrows",
            "tab": "navigation"
        },
        "navigation_hideOnClick": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Toggle navigation visibility when carousel is clicked",
            "tab": "navigation"
        },
        "pagination_enabled": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Show pagination indicators",
            "tab": "pagination"
        },
        "pagination_type": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": "bullets",
            "description": "Type of pagination indicator to display",
            "options": [
                "Bullets",
                "Fraction (1 \/ 5)",
                "Progress Bar"
            ],
            "options_labels": {
                "bullets": "Bullets",
                "fraction": "Fraction (1 \/ 5)",
                "progressbar": "Progress Bar"
            },
            "tab": "pagination"
        },
        "pagination_clickable": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": true,
            "description": "Make pagination bullets clickable to navigate",
            "tab": "pagination"
        },
        "pagination_dynamicBullets": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Show limited number of pagination bullets with active highlighted",
            "tab": "pagination"
        },
        "autoplay_enabled": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Automatically transition between slides",
            "tab": "autoplay"
        },
        "autoplay_delay": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": 3000,
            "description": "Time between slide transitions in milliseconds",
            "tab": "autoplay"
        },
        "autoplay_pauseOnMouseEnter": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Pause autoplay when mouse hovers over carousel",
            "tab": "autoplay"
        },
        "autoplay_disableOnInteraction": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": true,
            "description": "Disable autoplay after user interacts with carousel",
            "tab": "autoplay"
        },
        "scrollbar_enabled": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Show scrollbar indicator",
            "tab": "scrollbar"
        },
        "scrollbar_draggable": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Allow scrollbar to be draggable",
            "tab": "scrollbar"
        },
        "scrollbar_hide": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": true,
            "description": "Automatically hide scrollbar after interaction",
            "tab": "scrollbar"
        },
        "a11y_enabled": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": true,
            "description": "Add ARIA labels and keyboard support for screen readers",
            "tab": "accessibility"
        },
        "a11y_prevSlideMessage": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": "Previous slide",
            "description": "ARIA label for previous button",
            "tab": "accessibility"
        },
        "a11y_nextSlideMessage": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": "Next slide",
            "description": "ARIA label for next button",
            "tab": "accessibility"
        },
        "a11y_firstSlideMessage": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": "This is the first slide",
            "description": "Screen reader message when reaching first slide",
            "tab": "accessibility"
        },
        "a11y_lastSlideMessage": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": "This is the last slide",
            "description": "Screen reader message when reaching last slide",
            "tab": "accessibility"
        },
        "keyboard_enabled": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Enable keyboard control (arrow keys)",
            "tab": "advanced"
        },
        "keyboard_onlyInViewport": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": true,
            "description": "Keyboard control only works when carousel is visible in viewport",
            "tab": "advanced"
        },
        "mousewheel_enabled": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Enable navigation by mouse wheel scrolling",
            "tab": "advanced"
        },
        "mousewheel_invert": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Reverse mousewheel scroll direction",
            "tab": "advanced"
        },
        "mousewheel_sensitivity": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": 1,
            "description": "Mousewheel scroll sensitivity multiplier",
            "tab": "advanced"
        },
        "zoom_enabled": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Enable pinch-to-zoom on slides (mobile\/desktop)",
            "tab": "advanced"
        },
        "zoom_maxRatio": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": 3,
            "description": "Maximum zoom scale",
            "tab": "advanced"
        },
        "zoom_minRatio": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": 1,
            "description": "Minimum zoom scale",
            "tab": "advanced"
        },
        "zoom_toggle": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": true,
            "description": "Enable double-tap\/click to toggle zoom",
            "tab": "advanced"
        },
        "parallax_enabled": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Enable parallax effects with a single background image that slides behind content",
            "tab": "advanced"
        },
        "parallax_bg_mode": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": "",
            "description": "Background image for parallax effect",
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
            "tab": "advanced"
        },
        "parallax_bg_url": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "URL of the parallax background image",
            "tab": "advanced"
        },
        "parallax_bg_upload": {
            "type": "file",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Upload a background image for parallax effect",
            "tab": "advanced"
        },
        "parallax_bg_preview": {
            "type": "html",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Preview",
            "tab": "advanced"
        },
        "freeMode_enabled": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Slides will not snap to positions, continuous scrolling",
            "tab": "advanced"
        },
        "freeMode_momentum": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": true,
            "description": "Enable momentum scrolling",
            "tab": "advanced"
        },
        "freeMode_sticky": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Snap to slides after free mode scroll ends",
            "tab": "advanced"
        },
        "grid_rows": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": 1,
            "description": "Number of slides rows (for grid layout)",
            "tab": "advanced"
        },
        "grid_fill": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": "row",
            "description": "How grid slides are filled",
            "options": [
                "Row",
                "Column"
            ],
            "options_labels": {
                "row": "Row",
                "column": "Column"
            },
            "tab": "advanced"
        },
        "breakpoints_enabled": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Override settings at specific screen widths",
            "tab": "advanced"
        },
        "breakpoint_640_slidesPerView": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": "",
            "description": "Number of slides on mobile (640px and up)",
            "tab": "advanced"
        },
        "breakpoint_640_spaceBetween": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Space between slides on mobile",
            "tab": "advanced"
        },
        "breakpoint_768_slidesPerView": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": "",
            "description": "Number of slides on tablet (768px and up)",
            "tab": "advanced"
        },
        "breakpoint_768_spaceBetween": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Space between slides on tablet",
            "tab": "advanced"
        },
        "breakpoint_1024_slidesPerView": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": "",
            "description": "Number of slides on desktop (1024px and up)",
            "tab": "advanced"
        },
        "breakpoint_1024_spaceBetween": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Space between slides on desktop",
            "tab": "advanced"
        },
        "breakpoint_1280_slidesPerView": {
            "type": "text",
            "required": false,
            "storage": "json_column",
            "default": "",
            "description": "Number of slides on large desktop (1280px and up)",
            "tab": "advanced"
        },
        "breakpoint_1280_spaceBetween": {
            "type": "number",
            "required": false,
            "storage": "json_column",
            "default": null,
            "description": "Space between slides on large desktop",
            "tab": "advanced"
        },
        "lazy": {
            "type": "switch",
            "required": false,
            "storage": "json_column",
            "default": false,
            "description": "Load images only when slides become visible (improves performance for image-heavy carousels)",
            "tab": "advanced"
        },
        "builderMode": {
            "type": "select",
            "required": false,
            "storage": "json_column",
            "default": "preview",
            "description": "Toggle between editable list view and live preview mode in the form builder",
            "options": [
                "List View (Edit)",
                "Preview Mode (Read-only)"
            ],
            "options_labels": {
                "list": "List View (Edit)",
                "preview": "Preview Mode (Read-only)"
            },
            "tab": "advanced"
        }
    },
    "allowed_children": [
        "*"
    ],
    "tabs": {
        "navigation": {
            "label": "Navigation",
            "icon": "bi-arrow-left-right",
            "order": 36,
            "view": "auto-generated from schema"
        },
        "pagination": {
            "label": "Pagination",
            "icon": "bi-circle-fill",
            "order": 37,
            "view": "auto-generated from schema"
        },
        "effects": {
            "label": "Effects",
            "icon": "bi-magic",
            "order": 38,
            "view": "auto-generated from schema"
        },
        "autoplay": {
            "label": "Autoplay",
            "icon": "bi-play-circle",
            "order": 39,
            "view": "auto-generated from schema"
        },
        "interaction": {
            "label": "Interaction",
            "icon": "bi-hand-index",
            "order": 40,
            "view": "auto-generated from schema"
        },
        "scrollbar": {
            "label": "Scrollbar",
            "icon": "bi-arrows-expand",
            "order": 41,
            "view": "auto-generated from schema"
        },
        "accessibility": {
            "label": "Accessibility",
            "icon": "bi-universal-access",
            "order": 42,
            "view": "auto-generated from schema"
        }
    }
}
```
