<?php

namespace DigitalisStudios\SlickForms\LayoutElementTypes;

use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;

/**
 * TabsType
 *
 * Bootstrap tabs component for organizing content into tabbed interface.
 */
class TabsType extends BaseLayoutElementType
{
    public function getType(): string
    {
        return 'tabs';
    }

    public function getLabel(): string
    {
        return 'Tabs';
    }

    public function getIcon(): string
    {
        return 'bi-folder';
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'style' => [
                'type' => 'select',
                'label' => 'Tab Style',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => 'nav-tabs',
                'options' => [
                    'nav-tabs' => 'Tabs (default)',
                    'nav-pills' => 'Pills',
                    'nav-underline' => 'Underline',
                ],
            ],
            'alignment' => [
                'type' => 'select',
                'label' => 'Tab Alignment',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => '',
                'options' => [
                    '' => 'Left (default)',
                    'justify-content-center' => 'Center',
                    'justify-content-end' => 'Right',
                ],
            ],
            'fill' => [
                'type' => 'select',
                'label' => 'Tab Width',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => 'false',
                'options' => [
                    'false' => 'Auto',
                    'nav-fill' => 'Fill (proportional)',
                    'nav-justified' => 'Justified (equal width)',
                ],
            ],
            'default_active_tab' => [
                'type' => 'number',
                'label' => 'Default Active Tab',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => 0,
                'min' => 0,
                'help' => '0 = first tab, 1 = second tab, etc.',
                'placeholder' => '0',
            ],
            'fade' => [
                'type' => 'switch',
                'label' => 'Enable Fade Animation',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => false,
            ],
        ]);
    }

    public function canContain(string $elementType): bool
    {
        // Tabs contain tab items
        return $elementType === 'tab';
    }

    public function getAllowedChildren(): array
    {
        return ['tab'];
    }

    public function render(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        // Note: Actual rendering would need access to children to build tab navigation
        // This is a simplified version
        $settings = $element->settings ?? [];

        $classes = $this->getElementClasses($element);

        $html = '<div';
        if ($element->element_id) {
            $html .= ' id="'.htmlspecialchars($element->element_id).'"';
        }
        if ($classes) {
            $html .= ' class="'.$classes.'"';
        }
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
        $html = '<div class="layout-element-tabs border border-primary rounded p-3 mb-3 bg-primary bg-opacity-10">';
        $html .= '<small class="text-primary fw-bold">';
        $html .= '<i class="bi bi-folder me-1"></i> Tabs';
        $html .= '</small>';
        $html .= '<div class="mt-2">';
        $html .= $childrenHtml;
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
}
