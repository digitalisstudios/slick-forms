<?php

namespace DigitalisStudios\SlickForms\LayoutElementTypes;

class CarouselSlideElement extends BaseLayoutElementType
{
    public function getType(): string
    {
        return 'carousel_slide';
    }

    public function getLabel(): string
    {
        return 'Carousel Slide';
    }

    public function getIcon(): string
    {
        return 'bi bi-image';
    }

    public function getAllowedChildren(): array
    {
        // Slides can contain most elements except nested carousels
        return ['row', 'column', 'field', 'card', 'accordion', 'tabs', 'table'];
    }

    public function getConfigSchema(): array
    {
        return [
            // Basic Tab - Slide Identification
            'slide_title' => [
                'type' => 'text',
                'label' => 'Slide Title',
                'help_text' => 'Title shown in builder (not visible on form)',
                'placeholder' => 'e.g., "Introduction" or "Features"',
                'tab' => 'basic',
            ],
            'slide_icon' => [
                'type' => 'icon_picker',
                'label' => 'Slide Icon',
                'help_text' => 'Icon shown in list mode',
                'tab' => 'basic',
            ],

            // Settings Tab - Swiper Per-Slide Attributes
            'autoplay_delay' => [
                'type' => 'number',
                'label' => 'Autoplay Delay (ms)',
                'help_text' => 'Override carousel autoplay delay for this slide',
                'placeholder' => 'e.g., 5000',
                'tab' => 'settings',
            ],
            'hash' => [
                'type' => 'text',
                'label' => 'Hash Navigation ID',
                'help_text' => 'Identifier for deep-linking (e.g., "intro", "features")',
                'placeholder' => 'e.g., slide-1',
                'tab' => 'settings',
            ],
            'history' => [
                'type' => 'text',
                'label' => 'History Navigation Path',
                'help_text' => 'URL path segment for browser history',
                'placeholder' => 'e.g., introduction',
                'tab' => 'settings',
            ],
            'zoom_max_ratio' => [
                'type' => 'number',
                'label' => 'Zoom Max Ratio',
                'help_text' => 'Maximum zoom level (1-10)',
                'placeholder' => 'e.g., 3',
                'min' => 1,
                'max' => 10,
                'tab' => 'settings',
            ],

            // Style Tab - Visual Properties
            'background_color' => [
                'type' => 'color',
                'label' => 'Background Color',
                'help_text' => 'Slide background color',
                'tab' => 'style',
            ],
            'background_image_mode' => [
                'type' => 'select',
                'label' => 'Background Image',
                'help_text' => 'Choose how to set the background image',
                'options' => [
                    '' => 'None',
                    'url' => 'Image URL',
                    'upload' => 'File Upload',
                ],
                'default' => '',
                'tab' => 'style',
            ],
            'background_image_url' => [
                'type' => 'text',
                'label' => 'Background Image URL',
                'help_text' => 'URL of background image (will cover slide area)',
                'placeholder' => 'https://example.com/image.jpg',
                'tab' => 'style',
                'show_if' => ['background_image_mode' => 'url'],
            ],
            'background_image_upload' => [
                'type' => 'file',
                'label' => 'Upload Background Image',
                'help_text' => 'Upload a background image',
                'accept' => 'image/*',
                'tab' => 'style',
                'show_if' => ['background_image_mode' => 'upload'],
            ],
            'background_image_preview' => [
                'type' => 'html',
                'label' => 'Preview',
                'tab' => 'style',
                'content' => '
                    <div x-show="$wire.properties.background_image_url" class="border rounded p-2 bg-light text-center">
                        <img :src="$wire.properties.background_image_url" class="img-fluid" style="max-height: 200px;" alt="Background Preview">
                        <div class="small text-muted mt-2">
                            <i class="bi bi-image me-1"></i> Background image preview
                        </div>
                    </div>
                ',
                'show_if' => ['background_image_mode' => ['url', 'upload']],
            ],
            'text_alignment' => [
                'type' => 'select',
                'label' => 'Text Alignment',
                'help_text' => 'Horizontal text alignment',
                'options' => [
                    '' => 'Default',
                    'start' => 'Start',
                    'center' => 'Center',
                    'end' => 'End',
                ],
                'tab' => 'style',
            ],
            'vertical_alignment' => [
                'type' => 'select',
                'label' => 'Vertical Alignment',
                'help_text' => 'Vertical content alignment',
                'options' => [
                    '' => 'Default',
                    'start' => 'Top',
                    'center' => 'Center',
                    'end' => 'Bottom',
                    'stretch' => 'Stretch',
                ],
                'tab' => 'style',
            ],
            'min_height' => [
                'type' => 'text',
                'label' => 'Minimum Height',
                'help_text' => 'Minimum slide height (e.g., "400px", "50vh")',
                'placeholder' => 'e.g., 400px or 50vh',
                'tab' => 'style',
            ],

            // Advanced Tab - Layout Properties
            'padding' => [
                'type' => 'select',
                'label' => 'Padding',
                'help_text' => 'Inner spacing around content',
                'options' => [
                    '' => 'Default',
                    'p-0' => 'None',
                    'p-1' => 'Extra Small',
                    'p-2' => 'Small',
                    'p-3' => 'Medium',
                    'p-4' => 'Large',
                    'p-5' => 'Extra Large',
                ],
                'tab' => 'advanced',
            ],
            'custom_class' => [
                'type' => 'text',
                'label' => 'Custom CSS Classes',
                'help_text' => 'Additional CSS classes (space-separated)',
                'placeholder' => 'e.g., my-custom-class',
                'tab' => 'advanced',
            ],
            'custom_style' => [
                'type' => 'textarea',
                'label' => 'Custom Inline Styles',
                'help_text' => 'Custom CSS styles (use CSS syntax)',
                'placeholder' => 'e.g., border: 2px solid red;',
                'rows' => 3,
                'tab' => 'advanced',
            ],
        ];
    }

    /**
     * Render the carousel slide for user-facing forms.
     * Note: Slides are actually rendered by the parent carousel component,
     * so this method is not typically called directly.
     */
    public function render(\DigitalisStudios\SlickForms\Models\SlickFormLayoutElement $element, string $childrenHtml): string
    {
        // Carousel slides are rendered by the carousel component itself
        // This method exists to satisfy the abstract requirement
        return $childrenHtml;
    }

    /**
     * Render the carousel slide for the form builder.
     * Note: Slides are rendered by the carousel builder component,
     * so this method is not typically called directly.
     */
    public function renderBuilder(\DigitalisStudios\SlickForms\Models\SlickFormLayoutElement $element, string $childrenHtml): string
    {
        // Carousel slides are rendered by the carousel builder component
        // This method exists to satisfy the abstract requirement
        return $childrenHtml;
    }
}
