<?php

namespace DigitalisStudios\SlickForms\LayoutElementTypes;

use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;

/**
 * ColumnType
 *
 * Bootstrap grid column element.
 * Supports responsive widths, offsets, ordering, and self-alignment.
 */
class ColumnType extends BaseLayoutElementType
{
    public function getType(): string
    {
        return 'column';
    }

    public function getLabel(): string
    {
        return 'Column';
    }

    public function getIcon(): string
    {
        return 'bi-layout-three-columns';
    }

    public function getConfigSchema(): array
    {
        $widthOptions = [
            '' => '-- Not Set --',
            'equal' => 'Equal Width',
            'auto' => 'Auto Width',
            '12' => '12 (Full)',
            '11' => '11',
            '10' => '10',
            '9' => '9 (3/4)',
            '8' => '8 (2/3)',
            '7' => '7',
            '6' => '6 (1/2)',
            '5' => '5',
            '4' => '4 (1/3)',
            '3' => '3 (1/4)',
            '2' => '2 (1/6)',
            '1' => '1',
        ];

        $offsetOptions = ['' => 'None'];
        for ($i = 1; $i <= 11; $i++) {
            $offsetOptions[(string) $i] = (string) $i;
        }

        $orderOptions = [
            '' => 'Default',
            'first' => 'First',
            'last' => 'Last',
            '0' => '0',
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
        ];

        return array_merge(parent::getConfigSchema(), [
            // Responsive Column Widths
            'width.xs' => [
                'type' => 'select',
                'label' => 'Width - Extra Small (<576px)',
                'tab' => 'settings',
                'target' => 'settings',
                'options' => $widthOptions,
            ],
            'width.sm' => [
                'type' => 'select',
                'label' => 'Width - Small (≥576px)',
                'tab' => 'settings',
                'target' => 'settings',
                'options' => $widthOptions,
            ],
            'width.md' => [
                'type' => 'select',
                'label' => 'Width - Medium (≥768px)',
                'tab' => 'settings',
                'target' => 'settings',
                'options' => $widthOptions,
            ],
            'width.lg' => [
                'type' => 'select',
                'label' => 'Width - Large (≥992px)',
                'tab' => 'settings',
                'target' => 'settings',
                'options' => $widthOptions,
            ],
            'width.xl' => [
                'type' => 'select',
                'label' => 'Width - Extra Large (≥1200px)',
                'tab' => 'settings',
                'target' => 'settings',
                'options' => $widthOptions,
            ],
            'width.xxl' => [
                'type' => 'select',
                'label' => 'Width - XXL (≥1400px)',
                'tab' => 'settings',
                'target' => 'settings',
                'options' => $widthOptions,
            ],
            // Column Offsets
            'offset.xs' => [
                'type' => 'select',
                'label' => 'Offset - XS',
                'tab' => 'settings',
                'target' => 'settings',
                'options' => $offsetOptions,
            ],
            'offset.sm' => [
                'type' => 'select',
                'label' => 'Offset - SM',
                'tab' => 'settings',
                'target' => 'settings',
                'options' => $offsetOptions,
            ],
            'offset.md' => [
                'type' => 'select',
                'label' => 'Offset - MD',
                'tab' => 'settings',
                'target' => 'settings',
                'options' => $offsetOptions,
            ],
            // Column Order
            'order.xs' => [
                'type' => 'select',
                'label' => 'Order - XS',
                'tab' => 'settings',
                'target' => 'settings',
                'options' => $orderOptions,
            ],
            'order.sm' => [
                'type' => 'select',
                'label' => 'Order - SM',
                'tab' => 'settings',
                'target' => 'settings',
                'options' => $orderOptions,
            ],
            'order.md' => [
                'type' => 'select',
                'label' => 'Order - MD',
                'tab' => 'settings',
                'target' => 'settings',
                'options' => $orderOptions,
            ],
            // Self Alignment
            'align_self' => [
                'type' => 'select',
                'label' => 'Vertical Alignment (Self)',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => '',
                'options' => [
                    '' => 'Default',
                    'align-self-start' => 'Top',
                    'align-self-center' => 'Center',
                    'align-self-end' => 'Bottom',
                    'align-self-stretch' => 'Stretch',
                ],
                'help' => "Override row's vertical alignment for this column",
            ],
        ]);
    }

    public function canContain(string $elementType): bool
    {
        // Columns can contain rows (nested), fields, cards, accordions, tabs, tables
        return in_array($elementType, ['row', 'field', 'card', 'accordion', 'tabs', 'table']);
    }

    public function getAllowedChildren(): array
    {
        return ['row', 'field', 'card', 'accordion', 'tabs', 'table'];
    }

    public function render(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        $settings = $element->settings ?? [];

        // Build column classes
        $columnClasses = $this->buildColumnClasses($settings);

        $classes = $this->getElementClasses($element, implode(' ', $columnClasses));

        // Build HTML
        $html = '<div';
        if ($element->element_id) {
            $html .= ' id="'.htmlspecialchars($element->element_id).'"';
        }
        $html .= ' class="'.$classes.'"';
        if ($element->style) {
            $html .= ' style="'.htmlspecialchars($element->style).'"';
        }
        $html .= '>';

        $html .= $childrenHtml;

        $html .= '</div>';

        return $html;
    }

    public function renderBuilder(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        $label = $this->getColumnWidthLabel($element);

        $html = '<div class="layout-element-column border border-success border-dashed rounded p-2 mb-2 bg-success bg-opacity-10">';
        $html .= '<small class="text-success fw-bold">';
        $html .= '<i class="bi bi-layout-three-columns me-1"></i> '.htmlspecialchars($label);
        $html .= '</small>';
        $html .= '<div class="mt-2">';
        $html .= $childrenHtml;
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Build column classes from settings
     */
    protected function buildColumnClasses(array $settings): array
    {
        $classes = [];
        $widths = $settings['width'] ?? [];
        $offsets = $settings['offset'] ?? [];
        $orders = $settings['order'] ?? [];

        // Column widths for each breakpoint
        foreach (['xs', 'sm', 'md', 'lg', 'xl', 'xxl'] as $breakpoint) {
            $width = $widths[$breakpoint] ?? '';

            if ($width) {
                if ($width === 'equal') {
                    $classes[] = $breakpoint === 'xs' ? 'col' : 'col-'.$breakpoint;
                } elseif ($width === 'auto') {
                    $classes[] = $breakpoint === 'xs' ? 'col-auto' : 'col-'.$breakpoint.'-auto';
                } else {
                    $classes[] = $breakpoint === 'xs' ? 'col-'.$width : 'col-'.$breakpoint.'-'.$width;
                }
            }
        }

        // Offsets
        foreach (['xs', 'sm', 'md', 'lg', 'xl', 'xxl'] as $breakpoint) {
            $offset = $offsets[$breakpoint] ?? '';

            if ($offset) {
                $classes[] = $breakpoint === 'xs' ? 'offset-'.$offset : 'offset-'.$breakpoint.'-'.$offset;
            }
        }

        // Order
        foreach (['xs', 'sm', 'md', 'lg', 'xl', 'xxl'] as $breakpoint) {
            $order = $orders[$breakpoint] ?? '';

            if ($order) {
                $classes[] = $breakpoint === 'xs' ? 'order-'.$order : 'order-'.$breakpoint.'-'.$order;
            }
        }

        // Self alignment
        if (! empty($settings['align_self'])) {
            $classes[] = $settings['align_self'];
        }

        // If no width classes, default to col
        if (empty($classes)) {
            $classes[] = 'col';
        }

        return $classes;
    }

    /**
     * Get human-readable label for column width
     */
    protected function getColumnWidthLabel(SlickFormLayoutElement $element): string
    {
        $settings = $element->settings ?? [];
        $widths = $settings['width'] ?? [];

        // Find first defined width
        foreach (['xs', 'sm', 'md', 'lg', 'xl', 'xxl'] as $breakpoint) {
            if (! empty($widths[$breakpoint])) {
                $width = $widths[$breakpoint];

                if ($width === 'equal') {
                    return 'Column (Equal)';
                } elseif ($width === 'auto') {
                    return 'Column (Auto)';
                } else {
                    return 'Column ('.$width.'/12)';
                }
            }
        }

        return 'Column';
    }
}
