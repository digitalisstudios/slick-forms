<?php

namespace DigitalisStudios\SlickForms\LayoutElementTypes;

use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;

/**
 * CardType
 *
 * Bootstrap card component for visual grouping.
 * Supports headers, footers, titles, subtitles, and styling options.
 */
class CardType extends BaseLayoutElementType
{
    public function getType(): string
    {
        return 'card';
    }

    public function getLabel(): string
    {
        return 'Card';
    }

    public function getIcon(): string
    {
        return 'bi-card-text';
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'header' => [
                'type' => 'text',
                'label' => 'Card Header',
                'tab' => 'settings',
                'target' => 'settings',
                'placeholder' => 'Optional header text',
                'help' => 'Leave empty for no header',
            ],
            'title' => [
                'type' => 'text',
                'label' => 'Card Title',
                'tab' => 'settings',
                'target' => 'settings',
                'placeholder' => 'Optional title',
            ],
            'subtitle' => [
                'type' => 'text',
                'label' => 'Card Subtitle',
                'tab' => 'settings',
                'target' => 'settings',
                'placeholder' => 'Optional subtitle',
            ],
            'footer' => [
                'type' => 'text',
                'label' => 'Card Footer',
                'tab' => 'settings',
                'target' => 'settings',
                'placeholder' => 'Optional footer text',
                'help' => 'Leave empty for no footer',
            ],
            'background' => [
                'type' => 'select',
                'label' => 'Background Color',
                'tab' => 'style',
                'target' => 'settings',
                'default' => '',
                'options' => [
                    '' => 'White (default)',
                    'primary' => 'Primary',
                    'secondary' => 'Secondary',
                    'success' => 'Success',
                    'danger' => 'Danger',
                    'warning' => 'Warning',
                    'info' => 'Info',
                    'light' => 'Light',
                    'dark' => 'Dark',
                ],
            ],
            'border' => [
                'type' => 'select',
                'label' => 'Border Color',
                'tab' => 'style',
                'target' => 'settings',
                'default' => '',
                'options' => [
                    '' => 'Default',
                    'primary' => 'Primary',
                    'secondary' => 'Secondary',
                    'success' => 'Success',
                    'danger' => 'Danger',
                    'warning' => 'Warning',
                    'info' => 'Info',
                ],
            ],
            'shadow' => [
                'type' => 'select',
                'label' => 'Shadow',
                'tab' => 'style',
                'target' => 'settings',
                'default' => '',
                'options' => [
                    '' => 'None',
                    'shadow-sm' => 'Small',
                    'shadow' => 'Medium',
                    'shadow-lg' => 'Large',
                ],
            ],
        ]);
    }

    public function canContain(string $elementType): bool
    {
        return in_array($elementType, ['row', 'field']);
    }

    public function getAllowedChildren(): array
    {
        return ['row', 'field'];
    }

    public function render(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        $settings = $element->settings ?? [];

        // Build card classes
        $cardClasses = ['card'];

        if (! empty($settings['background'])) {
            $cardClasses[] = 'bg-'.$settings['background'];
        }
        if (! empty($settings['border'])) {
            $cardClasses[] = 'border-'.$settings['border'];
        }
        if (! empty($settings['shadow'])) {
            $cardClasses[] = $settings['shadow'];
        }

        $classes = $this->getElementClasses($element, implode(' ', $cardClasses));

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

        // Header
        if (! empty($settings['header'])) {
            $html .= '<div class="card-header">'.htmlspecialchars($settings['header']).'</div>';
        }

        // Body
        $html .= '<div class="card-body">';

        // Title
        if (! empty($settings['title'])) {
            $html .= '<h5 class="card-title">'.htmlspecialchars($settings['title']).'</h5>';
        }

        // Subtitle
        if (! empty($settings['subtitle'])) {
            $html .= '<h6 class="card-subtitle mb-2 text-muted">'.htmlspecialchars($settings['subtitle']).'</h6>';
        }

        $html .= $childrenHtml;

        $html .= '</div>'; // card-body

        // Footer
        if (! empty($settings['footer'])) {
            $html .= '<div class="card-footer">'.htmlspecialchars($settings['footer']).'</div>';
        }

        $html .= '</div>'; // card

        return $html;
    }

    public function renderBuilder(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        $settings = $element->settings ?? [];
        $title = $settings['title'] ?? 'Card';

        $html = '<div class="layout-element-card border border-warning rounded p-3 mb-3 bg-warning bg-opacity-10">';
        $html .= '<small class="text-warning fw-bold">';
        $html .= '<i class="bi bi-card-text me-1"></i> '.htmlspecialchars($title);
        $html .= '</small>';
        $html .= '<div class="mt-2">';
        $html .= $childrenHtml;
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
}
