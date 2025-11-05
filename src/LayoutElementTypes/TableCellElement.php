<?php

namespace DigitalisStudios\SlickForms\LayoutElementTypes;

use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;

/**
 * TableCellElement - Individual cells within table rows
 *
 * Represents <th> or <td> HTML elements.
 * Can contain fields and layout elements (like Column).
 */
class TableCellElement extends BaseLayoutElementType
{
    public function getType(): string
    {
        return 'table_cell';
    }

    public function getLabel(): string
    {
        return 'Table Cell';
    }

    public function getIcon(): string
    {
        return 'bi-square';
    }

    public function render(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        $settings = $element->settings ?? [];
        $cellType = $settings['cell_type'] ?? 'td';
        $colspan = $settings['colspan'] ?? 1;
        $rowspan = $settings['rowspan'] ?? 1;
        $width = $settings['width'] ?? '';
        $align = $settings['align'] ?? '';
        $valign = $settings['valign'] ?? '';

        $classes = $this->getElementClasses($element);
        $style = $element->style ? ' style="'.e($element->style).'"' : '';
        $id = $element->element_id ? ' id="'.e($element->element_id).'"' : '';

        $classAttr = $classes ? ' class="'.$classes.'"' : '';
        $colspanAttr = $colspan > 1 ? ' colspan="'.(int) $colspan.'"' : '';
        $rowspanAttr = $rowspan > 1 ? ' rowspan="'.(int) $rowspan.'"' : '';
        $widthAttr = $width ? ' width="'.e($width).'"' : '';

        // Build alignment classes
        $alignClasses = [];
        if ($align) {
            $alignClasses[] = 'text-'.$align;
        }
        if ($valign) {
            $alignClasses[] = $valign;
        }
        $alignClassAttr = $alignClasses ? ' class="'.implode(' ', $alignClasses).'"' : '';

        return "<{$cellType}{$id}{$classAttr}{$alignClassAttr}{$colspanAttr}{$rowspanAttr}{$widthAttr}{$style}>{$childrenHtml}</{$cellType}>";
    }

    public function renderBuilder(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        return view('slick-forms::livewire.components.elements.builder.table-cell', [
            'element' => $element,
            'childrenHtml' => $childrenHtml,
        ])->render();
    }

    public function canContain(string $elementType): bool
    {
        // Cells work like columns - can contain everything except table structural elements
        $disallowed = ['table_header', 'table_body', 'table_footer', 'table_row', 'table_cell'];

        return ! in_array($elementType, $disallowed);
    }

    public function getAllowedChildren(): array
    {
        // Same as Column element - can contain most things
        return ['field', 'row', 'card', 'accordion', 'tabs', 'table', 'container'];
    }

    public function getConfigSchema(): array
    {
        $schema = parent::getConfigSchema();

        // Cell-specific properties
        $schema['cell_type'] = [
            'type' => 'select',
            'label' => 'Cell Type',
            'tab' => 'settings',
            'target' => 'settings',
            'options' => [
                'td' => 'Data Cell (td)',
                'th' => 'Header Cell (th)',
            ],
            'help' => 'Header cells are typically bold and used in thead rows',
        ];

        $schema['colspan'] = [
            'type' => 'number',
            'label' => 'Column Span',
            'tab' => 'settings',
            'target' => 'settings',
            'min' => 1,
            'default' => 1,
            'help' => 'Number of columns this cell spans',
        ];

        $schema['rowspan'] = [
            'type' => 'number',
            'label' => 'Row Span',
            'tab' => 'settings',
            'target' => 'settings',
            'min' => 1,
            'default' => 1,
            'help' => 'Number of rows this cell spans',
        ];

        $schema['width'] = [
            'type' => 'text',
            'label' => 'Width',
            'tab' => 'settings',
            'target' => 'settings',
            'placeholder' => 'e.g., 100px, 25%, auto',
            'help' => 'Custom width for this cell',
        ];

        $schema['align'] = [
            'type' => 'select',
            'label' => 'Horizontal Alignment',
            'tab' => 'style',
            'target' => 'settings',
            'options' => [
                '' => 'Default',
                'start' => 'Start (Left)',
                'center' => 'Center',
                'end' => 'End (Right)',
            ],
        ];

        $schema['valign'] = [
            'type' => 'select',
            'label' => 'Vertical Alignment',
            'tab' => 'style',
            'target' => 'settings',
            'options' => [
                '' => 'Default',
                'align-top' => 'Top',
                'align-middle' => 'Middle',
                'align-bottom' => 'Bottom',
            ],
        ];

        $schema['background_color'] = [
            'type' => 'select',
            'label' => 'Background Color',
            'tab' => 'style',
            'target' => 'settings',
            'options' => [
                '' => 'None',
                'table-primary' => 'Primary',
                'table-secondary' => 'Secondary',
                'table-success' => 'Success',
                'table-danger' => 'Danger',
                'table-warning' => 'Warning',
                'table-info' => 'Info',
                'table-light' => 'Light',
                'table-dark' => 'Dark',
            ],
        ];

        return $schema;
    }
}
