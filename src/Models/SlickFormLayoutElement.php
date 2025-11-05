<?php

namespace DigitalisStudios\SlickForms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SlickFormLayoutElement extends Model
{
    protected $table = 'slick_form_layout_elements';

    protected $fillable = [
        'slick_form_id',
        'slick_form_page_id',
        'parent_id',
        'parent_field_id',
        'element_type',
        'element_id',
        'order',
        'settings',
        'conditional_logic',
        'class',
        'style',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'conditional_logic' => 'array',
            'order' => 'integer',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(CustomForm::class, 'slick_form_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(SlickFormLayoutElement::class, 'parent_id');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(SlickFormPage::class, 'slick_form_page_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(SlickFormLayoutElement::class, 'parent_id')->orderBy('order');
    }

    public function parentField(): BelongsTo
    {
        return $this->belongsTo(CustomFormField::class, 'parent_field_id');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(CustomFormField::class, 'slick_form_layout_element_id')->orderBy('order');
    }

    public function isContainer(): bool
    {
        return $this->element_type === 'container';
    }

    public function isRow(): bool
    {
        return $this->element_type === 'row';
    }

    public function isColumn(): bool
    {
        return $this->element_type === 'column';
    }

    public function getColumnWidth(): string|int
    {
        // Try new structure first (width.xs is the base/mobile width)
        if (isset($this->settings['width']['xs'])) {
            return $this->settings['width']['xs'];
        }

        // Fall back to legacy single width
        return $this->settings['column_width'] ?? 12;
    }

    public function getColumnClass(): string
    {
        $classes = [];

        // Handle legacy single width setting
        if (isset($this->settings['column_width']) && ! is_array($this->settings['column_width'])) {
            $width = $this->settings['column_width'];

            return match ($width) {
                'auto' => 'col-auto',
                'equal' => 'col',
                default => 'col-md-'.$width,
            };
        }

        // Handle responsive widths (support both 'width' and 'column_width' keys)
        $widths = $this->settings['width'] ?? $this->settings['column_width'] ?? [];

        foreach (['xs', 'sm', 'md', 'lg', 'xl', 'xxl'] as $breakpoint) {
            if (! empty($widths[$breakpoint])) {
                $width = $widths[$breakpoint];
                if ($width === 'auto') {
                    $classes[] = $breakpoint === 'xs' ? 'col-auto' : "col-{$breakpoint}-auto";
                } elseif ($width === 'equal') {
                    $classes[] = $breakpoint === 'xs' ? 'col' : "col-{$breakpoint}";
                } else {
                    $classes[] = $breakpoint === 'xs' ? "col-{$width}" : "col-{$breakpoint}-{$width}";
                }
            }
        }

        // Handle offsets (support both 'offset' and 'column_offset' keys)
        $offsets = $this->settings['offset'] ?? $this->settings['column_offset'] ?? [];
        foreach (['xs', 'sm', 'md', 'lg', 'xl', 'xxl'] as $breakpoint) {
            if (! empty($offsets[$breakpoint])) {
                $offset = $offsets[$breakpoint];
                $classes[] = $breakpoint === 'xs' ? "offset-{$offset}" : "offset-{$breakpoint}-{$offset}";
            }
        }

        // Handle order (support both 'order' and 'column_order' keys)
        $orders = $this->settings['order'] ?? $this->settings['column_order'] ?? [];
        foreach (['xs', 'sm', 'md', 'lg', 'xl', 'xxl'] as $breakpoint) {
            if (! empty($orders[$breakpoint])) {
                $order = $orders[$breakpoint];
                $classes[] = $breakpoint === 'xs' ? "order-{$order}" : "order-{$breakpoint}-{$order}";
            }
        }

        if (! empty($this->settings['align_self'])) {
            $classes[] = $this->settings['align_self'];
        }

        return ! empty($classes) ? implode(' ', $classes) : 'col';
    }

    public function getColumnWidthLabel(): string
    {
        $width = $this->getColumnWidth();

        return match ($width) {
            'auto' => 'Auto',
            'equal' => 'Equal',
            default => $width.'/12',
        };
    }

    public function canBeParentOf(string $childType): bool
    {
        return match ($this->element_type) {
            'container' => in_array($childType, ['row', 'field', 'card', 'accordion', 'tabs', 'table', 'carousel']),
            'row' => in_array($childType, ['column', 'field']),
            'column' => in_array($childType, ['row', 'field', 'card', 'accordion', 'tabs', 'table', 'carousel']),
            'card' => in_array($childType, ['row', 'field', 'table']),
            'accordion' => in_array($childType, ['accordion_item']),
            'accordion_item' => in_array($childType, ['row', 'field', 'table']),
            'tabs' => in_array($childType, ['tab']),
            'tab' => in_array($childType, ['row', 'field', 'table']),
            'carousel' => $childType === 'container', // Carousel slides are containers with isSlide setting
            'table_cell' => in_array($childType, ['field', 'card']),
            default => false,
        };
    }

    public function getTabLabel(): string
    {
        return $this->settings['tab_label'] ?? 'Tab '.($this->order + 1);
    }

    public function getAccordionItemLabel(): string
    {
        return $this->settings['accordion_item_label'] ?? 'Section '.($this->order + 1);
    }

    public function getContainerLabel(): string
    {
        return $this->settings['container_label'] ?? 'Container '.($this->order + 1);
    }

    public function isContainerFluid(): bool
    {
        return $this->settings['container_fluid'] ?? false;
    }

    public function getContainerClass(): string
    {
        $breakpoint = $this->settings['container_breakpoint'] ?? '';

        if ($this->isContainerFluid()) {
            return 'container-fluid';
        }

        if ($breakpoint && $breakpoint !== 'fixed') {
            return 'container-'.$breakpoint;
        }

        return 'container';
    }

    /**
     * Get row classes including gutter and alignment
     */
    public function getRowClass(): string
    {
        $classes = ['row'];

        // Gutter control
        if (! empty($this->settings['gutter'])) {
            $classes[] = $this->settings['gutter'];
        }

        // Horizontal alignment
        if (! empty($this->settings['horizontal_alignment'])) {
            $classes[] = $this->settings['horizontal_alignment'];
        }

        // Vertical alignment
        if (! empty($this->settings['vertical_alignment'])) {
            $classes[] = $this->settings['vertical_alignment'];
        }

        return implode(' ', $classes);
    }

    /**
     * Get card classes including background, border, shadow
     */
    public function getCardClass(): string
    {
        $classes = ['card'];

        // Background color
        if (! empty($this->settings['card_background'])) {
            $classes[] = 'bg-'.$this->settings['card_background'];
        }

        // Border color
        if (! empty($this->settings['card_border_color'])) {
            $classes[] = 'border-'.$this->settings['card_border_color'];
        }

        // Shadow
        if (! empty($this->settings['card_shadow'])) {
            $classes[] = $this->settings['card_shadow'];
        }

        // Text color (for dark backgrounds)
        if (! empty($this->settings['card_text_color'])) {
            $classes[] = 'text-'.$this->settings['card_text_color'];
        }

        return implode(' ', $classes);
    }

    /**
     * Check if card has header
     */
    public function hasCardHeader(): bool
    {
        // Show header if explicit header text is set or legacy show flag is true
        return ! empty($this->settings['header']) || ! empty($this->settings['card_header_show']);
    }

    /**
     * Get card header text
     */
    public function getCardHeaderText(): string
    {
        // Prefer new 'header' key; fallback to legacy
        return $this->settings['header'] ?? ($this->settings['card_header_text'] ?? '');
    }

    /**
     * Check if card has footer
     */
    public function hasCardFooter(): bool
    {
        return ! empty($this->settings['footer']) || ! empty($this->settings['card_footer_show']);
    }

    /**
     * Get card footer text
     */
    public function getCardFooterText(): string
    {
        return $this->settings['footer'] ?? ($this->settings['card_footer_text'] ?? '');
    }

    /**
     * Get card title
     */
    public function getCardTitle(): string
    {
        // Prefer new 'title' key; fallback to legacy 'card_title'
        return $this->settings['title'] ?? ($this->settings['card_title'] ?? '');
    }

    /**
     * Get card subtitle
     */
    public function getCardSubtitle(): string
    {
        return $this->settings['subtitle'] ?? ($this->settings['card_subtitle'] ?? '');
    }

    /**
     * Get tabs classes including style and alignment
     */
    public function getTabsClass(): string
    {
        $classes = ['nav'];

        // Tab style (tabs, pills, underline)
        $style = $this->settings['tab_style'] ?? 'nav-tabs';
        $classes[] = $style;

        // Tab alignment
        if (! empty($this->settings['tab_alignment'])) {
            $classes[] = $this->settings['tab_alignment'];
        }

        // Tab fill/justified
        if (! empty($this->settings['tab_fill'])) {
            $classes[] = $this->settings['tab_fill'];
        }

        return implode(' ', $classes);
    }

    /**
     * Check if tabs are vertical
     */
    public function isVerticalTabs(): bool
    {
        return ! empty($this->settings['vertical_tabs']);
    }

    /**
     * Get default active tab index
     */
    public function getDefaultActiveTab(): int
    {
        return (int) ($this->settings['default_active_tab'] ?? 0);
    }

    /**
     * Check if fade animation is enabled for tabs
     */
    public function hasFadeAnimation(): bool
    {
        return $this->settings['fade_animation'] ?? true;
    }

    /**
     * Get tab icon
     */
    public function getTabIcon(): string
    {
        return $this->settings['tab_icon'] ?? '';
    }

    /**
     * Get accordion classes including flush style
     */
    public function getAccordionClass(): string
    {
        $classes = ['accordion'];

        // Flush style
        if (! empty($this->settings['accordion_flush'])) {
            $classes[] = 'accordion-flush';
        }

        return implode(' ', $classes);
    }

    /**
     * Check if accordion allows multiple items open
     */
    public function isAccordionAlwaysOpen(): bool
    {
        return ! empty($this->settings['always_open']);
    }

    /**
     * Get default open accordion item index
     */
    public function getDefaultOpenItem(): int
    {
        return (int) ($this->settings['default_open_item'] ?? 0);
    }

    /**
     * Check if this accordion item should be initially open
     */
    public function isInitiallyOpen(): bool
    {
        return ! empty($this->settings['initially_open']);
    }

    /**
     * Get accordion item icon
     */
    public function getAccordionItemIcon(): string
    {
        return $this->settings['accordion_item_icon'] ?? '';
    }

    public function requiresParent(): bool
    {
        return in_array($this->element_type, ['column', 'tab', 'accordion_item']);
    }

    public function validateParent(): bool
    {
        // Columns must be in a row
        if ($this->element_type === 'column') {
            return $this->parent && $this->parent->element_type === 'row';
        }

        // Containers can have parents if the parent allows it via canBeParentOf()
        if ($this->element_type === 'container' && $this->parent) {
            return $this->parent->canBeParentOf('container');
        }

        // Rows can be anywhere except directly in another row
        if ($this->element_type === 'row' && $this->parent) {
            return in_array($this->parent->element_type, ['container', 'column']);
        }

        return true;
    }

    /**
     * Build utility classes from element settings
     * Combines spacing, display, and text alignment utilities
     */
    public function buildUtilityClasses(): string
    {
        $classes = [];

        // Add spacing utilities
        $classes[] = $this->buildSpacingClasses();

        // Add display utilities
        $classes[] = $this->buildDisplayClasses();

        // Add text alignment utilities
        $classes[] = $this->buildTextAlignmentClasses();

        // Filter out empty values and join
        return implode(' ', array_filter($classes));
    }

    /**
     * Build spacing utility classes (margin and padding)
     */
    protected function buildSpacingClasses(): string
    {
        $classes = [];
        $spacing = $this->settings['spacing'] ?? [];

        // Margin utilities
        if (! empty($spacing['margin'])) {
            $classes[] = 'm-'.$spacing['margin'];
        }
        if (! empty($spacing['margin_top'])) {
            $classes[] = 'mt-'.$spacing['margin_top'];
        }
        if (! empty($spacing['margin_bottom'])) {
            $classes[] = 'mb-'.$spacing['margin_bottom'];
        }
        if (! empty($spacing['margin_start'])) {
            $classes[] = 'ms-'.$spacing['margin_start'];
        }
        if (! empty($spacing['margin_end'])) {
            $classes[] = 'me-'.$spacing['margin_end'];
        }
        if (! empty($spacing['margin_x'])) {
            $classes[] = 'mx-'.$spacing['margin_x'];
        }
        if (! empty($spacing['margin_y'])) {
            $classes[] = 'my-'.$spacing['margin_y'];
        }

        // Padding utilities
        if (! empty($spacing['padding'])) {
            $classes[] = 'p-'.$spacing['padding'];
        }
        if (! empty($spacing['padding_top'])) {
            $classes[] = 'pt-'.$spacing['padding_top'];
        }
        if (! empty($spacing['padding_bottom'])) {
            $classes[] = 'pb-'.$spacing['padding_bottom'];
        }
        if (! empty($spacing['padding_start'])) {
            $classes[] = 'ps-'.$spacing['padding_start'];
        }
        if (! empty($spacing['padding_end'])) {
            $classes[] = 'pe-'.$spacing['padding_end'];
        }
        if (! empty($spacing['padding_x'])) {
            $classes[] = 'px-'.$spacing['padding_x'];
        }
        if (! empty($spacing['padding_y'])) {
            $classes[] = 'py-'.$spacing['padding_y'];
        }

        return implode(' ', $classes);
    }

    /**
     * Build display utility classes
     */
    protected function buildDisplayClasses(): string
    {
        $classes = [];
        $display = $this->settings['display'] ?? [];

        // Responsive display utilities
        foreach (['xs', 'sm', 'md', 'lg', 'xl', 'xxl'] as $breakpoint) {
            $key = $breakpoint === 'xs' ? 'display' : 'display_'.$breakpoint;
            if (! empty($display[$key])) {
                $prefix = $breakpoint === 'xs' ? 'd' : 'd-'.$breakpoint;
                $classes[] = $prefix.'-'.$display[$key];
            }
        }

        return implode(' ', $classes);
    }

    /**
     * Build text alignment utility classes
     */
    protected function buildTextAlignmentClasses(): string
    {
        $classes = [];
        $alignment = $this->settings['text_alignment'] ?? [];

        // Responsive text alignment utilities
        foreach (['xs', 'sm', 'md', 'lg', 'xl', 'xxl'] as $breakpoint) {
            $key = $breakpoint === 'xs' ? 'align' : 'align_'.$breakpoint;
            if (! empty($alignment[$key])) {
                $prefix = $breakpoint === 'xs' ? 'text' : 'text-'.$breakpoint;
                $classes[] = $prefix.'-'.$alignment[$key];
            }
        }

        return implode(' ', $classes);
    }

    /**
     * Get all classes for this element including utility classes
     */
    public function getAllClasses(): string
    {
        $classes = [];

        // Add custom classes from field
        if ($this->class) {
            $classes[] = $this->class;
        }

        // Add utility classes
        $utilityClasses = $this->buildUtilityClasses();
        if ($utilityClasses) {
            $classes[] = $utilityClasses;
        }

        return implode(' ', array_filter($classes));
    }

    /**
     * Get complete schema documentation for this element's type
     *
     * @return string JSON schema documentation
     */
    public function getFullSchema(): string
    {
        $registry = app(\DigitalisStudios\SlickForms\Services\LayoutElementRegistry::class);
        $elementType = $registry->get($this->element_type);

        return $elementType->getFullSchema();
    }
}
