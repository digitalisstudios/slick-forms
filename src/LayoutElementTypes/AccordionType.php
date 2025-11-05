<?php

namespace DigitalisStudios\SlickForms\LayoutElementTypes;

use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;

/**
 * AccordionType
 *
 * Bootstrap accordion component for collapsible content sections.
 */
class AccordionType extends BaseLayoutElementType
{
    public function getType(): string
    {
        return 'accordion';
    }

    public function getLabel(): string
    {
        return 'Accordion';
    }

    public function getIcon(): string
    {
        return 'bi-list';
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'flush' => [
                'type' => 'switch',
                'label' => 'Flush Style',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => false,
                'help' => 'Remove borders and rounded corners for edge-to-edge display',
            ],
            'always_open' => [
                'type' => 'switch',
                'label' => 'Allow Multiple Open',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => false,
                'help' => 'Allow multiple accordion items to be open simultaneously',
            ],
            'default_open_item' => [
                'type' => 'number',
                'label' => 'Default Open Item',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => 0,
                'min' => -1,
                'help' => '0 = first item, 1 = second item, -1 = none open',
                'placeholder' => '0',
            ],
        ]);
    }

    public function canContain(string $elementType): bool
    {
        // Accordions contain accordion items
        return $elementType === 'accordion_item';
    }

    public function getAllowedChildren(): array
    {
        return ['accordion_item'];
    }

    public function render(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        $settings = $element->settings ?? [];

        // Build accordion classes
        $accordionClasses = ['accordion'];

        if (! empty($settings['flush'])) {
            $accordionClasses[] = 'accordion-flush';
        }

        $classes = $this->getElementClasses($element, implode(' ', $accordionClasses));

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
        $html = '<div class="layout-element-accordion border border-danger border-dashed rounded p-3 mb-3 bg-danger bg-opacity-10">';
        $html .= '<small class="text-danger fw-bold">';
        $html .= '<i class="bi bi-list me-1"></i> Accordion';
        $html .= '</small>';
        $html .= '<div class="mt-2">';
        $html .= $childrenHtml;
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
}
