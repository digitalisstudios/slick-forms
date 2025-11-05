<?php

namespace DigitalisStudios\SlickForms\LayoutElementTypes;

use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;

/**
 * TableSectionElement - Header/Body/Footer sections within tables
 *
 * Represents thead, tbody, and tfoot HTML elements.
 * Can only contain TableRowElement children.
 */
class TableSectionElement extends BaseLayoutElementType
{
    public function getType(): string
    {
        // This is a generic type - actual type is stored in element_type column
        // Values: 'table_header', 'table_body', 'table_footer'
        return 'table_section';
    }

    public function getLabel(): string
    {
        return 'Table Section';
    }

    public function getIcon(): string
    {
        return 'bi-layout-three-columns';
    }

    public function render(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        $sectionType = $element->element_type;
        $classes = $this->getElementClasses($element);
        $style = $element->style ? ' style="'.e($element->style).'"' : '';
        $id = $element->element_id ? ' id="'.e($element->element_id).'"' : '';

        $tag = match ($sectionType) {
            'table_header' => 'thead',
            'table_footer' => 'tfoot',
            default => 'tbody',
        };

        $classAttr = $classes ? ' class="'.$classes.'"' : '';

        return "<{$tag}{$id}{$classAttr}{$style}>{$childrenHtml}</{$tag}>";
    }

    public function renderBuilder(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        $sectionType = $element->element_type;

        $label = match ($sectionType) {
            'table_header' => 'Header',
            'table_footer' => 'Footer',
            default => 'Body',
        };

        $badgeColor = match ($sectionType) {
            'table_header' => 'primary',
            'table_footer' => 'secondary',
            default => 'success',
        };

        return view('slick-forms::livewire.components.elements.builder.table-section', [
            'element' => $element,
            'label' => $label,
            'badgeColor' => $badgeColor,
            'childrenHtml' => $childrenHtml,
        ])->render();
    }

    public function canContain(string $elementType): bool
    {
        return $elementType === 'table_row';
    }

    public function getAllowedChildren(): array
    {
        return ['table_row'];
    }

    public function getConfigSchema(): array
    {
        $schema = parent::getConfigSchema();

        // Add section-specific properties
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

    public function getPropertyTabs(): array
    {
        $tabs = parent::getPropertyTabs();

        // Remove children tab for sections (not useful in hybrid UI)
        unset($tabs['children']);

        return $tabs;
    }
}
