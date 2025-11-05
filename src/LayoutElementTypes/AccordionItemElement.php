<?php

namespace DigitalisStudios\SlickForms\LayoutElementTypes;

use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;

/**
 * AccordionItemElement - Individual accordion item within Accordion
 *
 * Represents a single accordion item with header and content.
 * Used as children of AccordionType elements.
 */
class AccordionItemElement extends BaseLayoutElementType
{
    public function getType(): string
    {
        return 'accordion_item';
    }

    public function getLabel(): string
    {
        return 'Accordion Item';
    }

    public function getIcon(): string
    {
        return 'bi-chevron-down';
    }

    public function render(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        // Accordion item rendering is handled by the parent AccordionType element
        // This should not be called directly
        return $childrenHtml;
    }

    public function renderBuilder(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        // Accordion item rendering is handled by the parent AccordionType element
        // This should not be called directly
        return $childrenHtml;
    }

    public function canContain(string $elementType): bool
    {
        // Accordion items can contain any element except other accordion items
        return $elementType !== 'accordion_item';
    }

    public function getAllowedChildren(): array
    {
        // Accordion items can contain most elements
        return ['container', 'row', 'column', 'card', 'tabs', 'carousel', 'table'];
    }

    public function getConfigSchema(): array
    {
        $schema = parent::getConfigSchema();

        // Add accordion item-specific properties
        $schema['item_title'] = [
            'type' => 'text',
            'label' => 'Item Title',
            'tab' => 'basic',
            'target' => 'settings',
            'placeholder' => 'Enter accordion item title',
            'required' => false,
        ];

        $schema['item_icon'] = [
            'type' => 'icon_picker',
            'label' => 'Item Icon',
            'tab' => 'basic',
            'target' => 'settings',
            'placeholder' => 'Optional icon',
            'required' => false,
        ];

        return $schema;
    }

    public function getPropertyTabs(): array
    {
        $tabs = parent::getPropertyTabs();

        // Remove children tab for accordion items (not useful in hybrid UI)
        unset($tabs['children']);

        return $tabs;
    }
}
