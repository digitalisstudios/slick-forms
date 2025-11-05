<?php

namespace DigitalisStudios\SlickForms\LayoutElementTypes;

use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;

/**
 * ContainerType
 *
 * Top-level wrapper element that provides Bootstrap container functionality.
 * Can be fluid or fixed-width with breakpoint-specific containers.
 */
class ContainerType extends BaseLayoutElementType
{
    public function getType(): string
    {
        return 'container';
    }

    public function getLabel(): string
    {
        return 'Container';
    }

    public function getIcon(): string
    {
        return 'bi-box';
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'label' => [
                'type' => 'text',
                'label' => 'Container Label',
                'tab' => 'basic',
                'target' => 'settings',
                'help' => 'Give this section a name to help organize fields in conditional logic dropdowns (optional)',
                'placeholder' => 'e.g., Personal Information, Contact Details, Address',
            ],
            'fluid' => [
                'type' => 'switch',
                'label' => 'Use Fluid Container',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => false,
                'help' => 'Fluid containers span the full width of the viewport',
            ],
            'breakpoint' => [
                'type' => 'select',
                'label' => 'Container Type',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => '',
                'options' => [
                    '' => 'Fixed (default)',
                    'sm' => 'Small (≥576px)',
                    'md' => 'Medium (≥768px)',
                    'lg' => 'Large (≥992px)',
                    'xl' => 'Extra Large (≥1200px)',
                    'xxl' => 'Extra Extra Large (≥1400px)',
                ],
                'help' => 'Maximum width for the container',
            ],
        ]);
    }

    public function canContain(string $elementType): bool
    {
        // Containers can hold rows, fields, cards, accordions, tabs, and tables
        return in_array($elementType, ['row', 'field', 'card', 'accordion', 'tabs', 'table']);
    }

    public function getAllowedChildren(): array
    {
        return ['row', 'field', 'card', 'accordion', 'tabs', 'table'];
    }

    public function render(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        $settings = $element->settings ?? [];
        $fluid = $settings['fluid'] ?? false;
        $breakpoint = $settings['breakpoint'] ?? '';

        // Determine container class
        if ($fluid) {
            $containerClass = 'container-fluid';
        } elseif ($breakpoint) {
            $containerClass = 'container-'.$breakpoint;
        } else {
            $containerClass = 'container';
        }

        // Build full class list
        $classes = $this->getElementClasses($element, $containerClass);

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
        $settings = $element->settings ?? [];
        $label = $settings['label'] ?? 'Container';

        // Builder uses a visual wrapper to show container boundaries
        $html = '<div class="layout-element-container border border-2 border-secondary rounded p-3 mb-3 bg-light bg-opacity-50">';
        $html .= '<div class="d-flex justify-content-between align-items-center mb-2">';
        $html .= '<small class="text-muted fw-bold">';
        $html .= '<i class="bi bi-box me-1"></i> '.htmlspecialchars($label);
        $html .= '</small>';
        $html .= '</div>';

        $html .= $childrenHtml;

        $html .= '</div>';

        return $html;
    }
}
