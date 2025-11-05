<?php

namespace DigitalisStudios\SlickForms\LayoutElementTypes;

use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;

/**
 * TableType
 *
 * Bootstrap table layout for displaying fields in a tabular format.
 * Introduced in v2.5.0 for structured field organization.
 */
class TableType extends BaseLayoutElementType
{
    public function getType(): string
    {
        return 'table';
    }

    public function getLabel(): string
    {
        return 'Table';
    }

    public function getIcon(): string
    {
        return 'bi-table';
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'label' => [
                'type' => 'text',
                'label' => 'Table Label',
                'tab' => 'basic',
                'target' => 'settings',
                'help' => 'Optional label shown above the table',
                'placeholder' => 'e.g., Item Details, Pricing Information',
            ],
            'columns' => [
                'type' => 'number',
                'label' => 'Number of Columns',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => 2,
                'min' => 1,
                'max' => 6,
                'help' => 'Number of columns in the table',
            ],
            'striped' => [
                'type' => 'switch',
                'label' => 'Striped Rows',
                'tab' => 'style',
                'target' => 'settings',
                'default' => false,
                'help' => 'Alternate row colors for easier reading',
            ],
            'bordered' => [
                'type' => 'switch',
                'label' => 'Bordered',
                'tab' => 'style',
                'target' => 'settings',
                'default' => true,
                'help' => 'Add borders around table cells',
            ],
            'borderless' => [
                'type' => 'switch',
                'label' => 'Borderless',
                'tab' => 'style',
                'target' => 'settings',
                'default' => false,
                'help' => 'Remove all borders (overrides bordered)',
            ],
            'hover' => [
                'type' => 'switch',
                'label' => 'Hover Effect',
                'tab' => 'style',
                'target' => 'settings',
                'default' => false,
                'help' => 'Highlight rows on hover',
            ],
            'size' => [
                'type' => 'select',
                'label' => 'Table Size',
                'tab' => 'style',
                'target' => 'settings',
                'default' => '',
                'options' => [
                    '' => 'Default',
                    'table-sm' => 'Small (compact)',
                ],
            ],
            'responsive' => [
                'type' => 'switch',
                'label' => 'Responsive',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => true,
                'help' => 'Enable horizontal scrolling on small screens',
            ],
        ]);
    }

    public function canContain(string $elementType): bool
    {
        // Tables can only contain table sections (header, body, footer)
        return in_array($elementType, ['table_header', 'table_body', 'table_footer']);
    }

    public function getAllowedChildren(): array
    {
        return ['table_header', 'table_body', 'table_footer'];
    }

    public function render(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        $settings = $element->settings ?? [];

        // Build table classes
        $tableClasses = ['table'];

        if (! empty($settings['striped'])) {
            $tableClasses[] = 'table-striped';
        }
        if (! empty($settings['borderless'])) {
            $tableClasses[] = 'table-borderless';
        } elseif (! empty($settings['bordered'])) {
            $tableClasses[] = 'table-bordered';
        }
        if (! empty($settings['hover'])) {
            $tableClasses[] = 'table-hover';
        }
        if (! empty($settings['size'])) {
            $tableClasses[] = $settings['size'];
        }

        $tableClassStr = implode(' ', $tableClasses);
        $responsive = $settings['responsive'] ?? true;

        // Build HTML
        $html = '';

        // Optional label
        if (! empty($settings['label'])) {
            $html .= '<h6 class="mb-2">'.htmlspecialchars($settings['label']).'</h6>';
        }

        // Responsive wrapper
        if ($responsive) {
            $html .= '<div class="table-responsive">';
        }

        // Table
        $html .= '<table';
        if ($element->element_id) {
            $html .= ' id="'.htmlspecialchars($element->element_id).'"';
        }

        $classes = $this->getElementClasses($element, $tableClassStr);
        $html .= ' class="'.$classes.'"';

        if ($element->style) {
            $html .= ' style="'.htmlspecialchars($element->style).'"';
        }
        $html .= '>';

        // Children render as thead, tbody, tfoot
        $html .= $childrenHtml;

        $html .= '</table>';

        if ($responsive) {
            $html .= '</div>';
        }

        return $html;
    }

    public function renderBuilder(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        return view('slick-forms::livewire.components.elements.builder.table', [
            'element' => $element,
            'childrenHtml' => $childrenHtml,
        ])->render();
    }
}
