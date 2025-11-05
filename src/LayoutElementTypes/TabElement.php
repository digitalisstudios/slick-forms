<?php

namespace DigitalisStudios\SlickForms\LayoutElementTypes;

use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;

/**
 * TabElement - Individual tab pane within Tabs
 *
 * Represents a single tab content pane.
 * Used as children of TabsType elements.
 */
class TabElement extends BaseLayoutElementType
{
    public function getType(): string
    {
        return 'tab';
    }

    public function getLabel(): string
    {
        return 'Tab';
    }

    public function getIcon(): string
    {
        return 'bi-square';
    }

    public function render(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        // Tab rendering is handled by the parent TabsType element
        // This should not be called directly
        return $childrenHtml;
    }

    public function renderBuilder(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        // Tab rendering is handled by the parent TabsType element
        // This should not be called directly
        return $childrenHtml;
    }

    public function canContain(string $elementType): bool
    {
        // Tabs can contain any element except other tabs
        return $elementType !== 'tab';
    }

    public function getAllowedChildren(): array
    {
        // Tabs can contain most elements
        return ['container', 'row', 'column', 'card', 'accordion', 'carousel', 'table'];
    }

    public function getConfigSchema(): array
    {
        $schema = parent::getConfigSchema();

        // Add tab-specific properties
        $schema['tab_title'] = [
            'type' => 'text',
            'label' => 'Tab Title',
            'tab' => 'basic',
            'target' => 'settings',
            'placeholder' => 'Enter tab title',
            'required' => false,
        ];

        $schema['tab_icon'] = [
            'type' => 'icon_picker',
            'label' => 'Tab Icon',
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

        // Remove children tab for tabs (not useful in hybrid UI)
        unset($tabs['children']);

        return $tabs;
    }
}
