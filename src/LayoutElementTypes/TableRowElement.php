<?php

namespace DigitalisStudios\SlickForms\LayoutElementTypes;

use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;

/**
 * TableRowElement - Rows within table sections
 *
 * Represents <tr> HTML elements.
 * Can only contain TableCellElement children.
 */
class TableRowElement extends BaseLayoutElementType
{
    public function getType(): string
    {
        return 'table_row';
    }

    public function getLabel(): string
    {
        return 'Table Row';
    }

    public function getIcon(): string
    {
        return 'bi-dash-lg';
    }

    public function render(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        $classes = $this->getElementClasses($element);
        $style = $element->style ? ' style="'.e($element->style).'"' : '';
        $id = $element->element_id ? ' id="'.e($element->element_id).'"' : '';

        $classAttr = $classes ? ' class="'.$classes.'"' : '';

        return "<tr{$id}{$classAttr}{$style}>{$childrenHtml}</tr>";
    }

    public function renderBuilder(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        return view('slick-forms::livewire.components.elements.builder.table-row', [
            'element' => $element,
            'childrenHtml' => $childrenHtml,
        ])->render();
    }

    public function canContain(string $elementType): bool
    {
        return $elementType === 'table_cell';
    }

    public function getAllowedChildren(): array
    {
        return ['table_cell'];
    }

    public function getConfigSchema(): array
    {
        $schema = parent::getConfigSchema();

        // Add row-specific properties
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

        $schema['vertical_alignment'] = [
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

        return $schema;
    }

    public function getPropertyTabs(): array
    {
        $tabs = parent::getPropertyTabs();

        // Remove children tab for rows (not useful in hybrid UI)
        unset($tabs['children']);

        return $tabs;
    }
}
