<?php

namespace DigitalisStudios\SlickForms\LayoutElementTypes;

use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;

/**
 * RowType
 *
 * Bootstrap grid row element.
 * Provides gutter spacing, alignment, and responsive layout options.
 */
class RowType extends BaseLayoutElementType
{
    public function getType(): string
    {
        return 'row';
    }

    public function getLabel(): string
    {
        return 'Row';
    }

    public function getIcon(): string
    {
        return 'bi-distribute-vertical';
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'gutter' => [
                'type' => 'select',
                'label' => 'Gutter Size',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => '',
                'options' => [
                    '' => 'Default',
                    'g-0' => 'No gutter',
                    'g-1' => 'Extra small',
                    'g-2' => 'Small',
                    'g-3' => 'Medium',
                    'g-4' => 'Large',
                    'g-5' => 'Extra large',
                ],
                'help' => 'Space between columns',
            ],
            'horizontal_alignment' => [
                'type' => 'select',
                'label' => 'Horizontal Alignment',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => '',
                'options' => [
                    '' => 'Start (default)',
                    'justify-content-center' => 'Center',
                    'justify-content-end' => 'End',
                    'justify-content-around' => 'Around',
                    'justify-content-between' => 'Between',
                    'justify-content-evenly' => 'Evenly',
                ],
                'help' => 'Horizontal distribution of columns',
            ],
            'vertical_alignment' => [
                'type' => 'select',
                'label' => 'Vertical Alignment',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => '',
                'options' => [
                    '' => 'Stretch (default)',
                    'align-items-start' => 'Top',
                    'align-items-center' => 'Middle',
                    'align-items-end' => 'Bottom',
                ],
                'help' => 'Vertical alignment of columns',
            ],
        ]);
    }

    public function canContain(string $elementType): bool
    {
        // Rows can contain columns and fields (fields outside columns still work)
        return in_array($elementType, ['column', 'field']);
    }

    public function getAllowedChildren(): array
    {
        return ['column', 'field'];
    }

    public function render(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        $settings = $element->settings ?? [];

        // Build row classes
        $rowClasses = ['row'];

        if (! empty($settings['gutter'])) {
            $rowClasses[] = $settings['gutter'];
        }
        if (! empty($settings['horizontal_alignment'])) {
            $rowClasses[] = $settings['horizontal_alignment'];
        }
        if (! empty($settings['vertical_alignment'])) {
            $rowClasses[] = $settings['vertical_alignment'];
        }

        $classes = $this->getElementClasses($element, implode(' ', $rowClasses));

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
        // Builder preview with visual row indicator
        $html = '<div class="layout-element-row border border-info border-dashed rounded p-2 mb-2 bg-info bg-opacity-10">';
        $html .= '<small class="text-info fw-bold">';
        $html .= '<i class="bi bi-distribute-vertical me-1"></i> Row';
        $html .= '</small>';
        $html .= '<div class="mt-2">';
        $html .= $childrenHtml;
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
}
