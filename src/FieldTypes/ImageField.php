<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;

class ImageField extends BaseFieldType
{
    public function getName(): string
    {
        return 'image';
    }

    public function getLabel(): string
    {
        return 'Image';
    }

    public function getIcon(): string
    {
        return 'bi bi-image';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $imageUrl = $field->options['image_url'] ?? '';
        $altText = $field->options['alt_text'] ?? $field->label;
        $width = $field->options['width'] ?? '';
        $height = $field->options['height'] ?? '';
        $aspectRatio = $field->options['aspect_ratio'] ?? '';
        $objectFit = $field->options['object_fit'] ?? '';
        $className = $field->class ?? '';
        $style = $field->style ?? '';

        if (! $imageUrl) {
            return '';
        }

        $html = '<img src="'.htmlspecialchars($imageUrl).'" ';
        $html .= 'alt="'.htmlspecialchars($altText).'" ';

        // Build class attribute
        $classes = ['img-fluid']; // Bootstrap responsive image
        if ($className) {
            $classes[] = $className;
        }
        $html .= 'class="'.htmlspecialchars(implode(' ', $classes)).'" ';

        // Build style attribute
        $styles = [];
        if ($width) {
            $styles[] = 'width: '.htmlspecialchars($width);
        }
        if ($height) {
            $styles[] = 'height: '.htmlspecialchars($height);
        }
        if ($aspectRatio) {
            $styles[] = 'aspect-ratio: '.htmlspecialchars($aspectRatio);
        }
        if ($objectFit) {
            $styles[] = 'object-fit: '.htmlspecialchars($objectFit);
        }
        if ($style) {
            $styles[] = $style;
        }
        if (! empty($styles)) {
            $html .= 'style="'.htmlspecialchars(implode('; ', $styles)).'" ';
        }

        $html .= '>';

        if ($field->help_text) {
            $html .= '<p class="text-muted small mt-2">'.htmlspecialchars($field->help_text).'</p>';
        }

        return $html;
    }

    public function renderBuilder(CustomFormField $field): string
    {
        $imageUrl = $field->options['image_url'] ?? '';
        $altText = $field->options['alt_text'] ?? $field->label;

        if (! $imageUrl) {
            // Show placeholder when no image is configured
            $html = '<div class="border rounded p-3 bg-light text-center" style="min-height: 150px; display: flex; align-items: center; justify-content: center;">';
            $html .= '<div>';
            $html .= '<i class="bi bi-image" style="font-size: 3rem; color: #6c757d;"></i>';
            $html .= '<div class="mt-2 text-muted small">Configure image in Field Properties</div>';
            $html .= '</div>';
            $html .= '</div>';

            return $html;
        }

        // Show actual image preview
        return $this->render($field);
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        // Image fields are display-only and don't collect data
        return [];
    }

    public function getConfigSchema(): array
    {
        $schema = parent::getConfigSchema();

        // Remove input-related options (this is a display-only field)
        unset($schema['floating_label']);
        unset($schema['field_size']);
        unset($schema['placeholder']);

        // Extract fields that should come after content settings
        $helpTextAsPopover = $schema['help_text_as_popover'];
        $helpText = $schema['help_text'];
        unset($schema['help_text_as_popover'], $schema['help_text']);

        // Add content fields in desired order (after element_id)
        $schema = array_merge($schema, [
            'input_mode' => [
                'type' => 'select',
                'label' => 'Input Mode',
                'tab' => 'basic',
                'target' => 'options',
                'options' => [
                    'url' => 'Image URL',
                    'upload' => 'File Upload',
                ],
                'default' => 'url',
                'required' => false,
            ],
            'image_url' => [
                'type' => 'text',
                'label' => 'Image URL',
                'tab' => 'basic',
                'target' => 'options',
                'placeholder' => 'https://example.com/image.jpg',
                'required' => false,
                'show_if' => ['input_mode' => 'url'],
            ],
            'image_upload' => [
                'type' => 'file',
                'label' => 'Upload Image',
                'tab' => 'basic',
                'target' => 'options',
                'accept' => 'image/*',
                'required' => false,
                'show_if' => ['input_mode' => 'upload'],
            ],
            'image_preview' => [
                'type' => 'html',
                'label' => 'Preview',
                'tab' => 'basic',
                'target' => 'options', // Won't be saved, just for display
                'content' => '
                    <div x-show="$wire.properties.image_url" class="border rounded p-2 bg-light text-center">
                        <img :src="$wire.properties.image_url" class="img-fluid" style="max-height: 200px;" alt="Preview">
                        <div class="small text-success mt-2">
                            <i class="bi bi-check-circle me-1"></i> Image uploaded successfully
                        </div>
                    </div>
                ',
                'required' => false,
            ],
            // Re-add basic tab fields in desired order
            'help_text_as_popover' => $helpTextAsPopover,
            'help_text' => $helpText,
            'alt_text' => [
                'type' => 'text',
                'label' => 'Alt Text',
                'tab' => 'options',
                'target' => 'options',
                'help' => 'Alternative text for accessibility',
                'required' => false,
            ],
            'width' => [
                'type' => 'text',
                'label' => 'Width',
                'tab' => 'options',
                'target' => 'options',
                'placeholder' => 'e.g., 300px, 50%, auto',
                'required' => false,
            ],
            'height' => [
                'type' => 'text',
                'label' => 'Height',
                'tab' => 'options',
                'target' => 'options',
                'placeholder' => 'e.g., 200px, auto',
                'required' => false,
            ],
            'aspect_ratio' => [
                'type' => 'select',
                'label' => 'Aspect Ratio',
                'tab' => 'options',
                'target' => 'options',
                'options' => [
                    '' => 'Original',
                    '1/1' => 'Square (1:1)',
                    '4/3' => 'Standard (4:3)',
                    '3/2' => 'Classic (3:2)',
                    '16/9' => 'Widescreen (16:9)',
                    '21/9' => 'Ultrawide (21:9)',
                    '3/4' => 'Portrait (3:4)',
                    '2/3' => 'Tall (2:3)',
                    '9/16' => 'Vertical (9:16)',
                ],
                'default' => '',
                'required' => false,
                'help' => 'Force a specific aspect ratio',
            ],
            'object_fit' => [
                'type' => 'select',
                'label' => 'Object Fit',
                'tab' => 'options',
                'target' => 'options',
                'options' => [
                    '' => 'Default',
                    'contain' => 'Contain',
                    'cover' => 'Cover',
                    'fill' => 'Fill',
                    'none' => 'None',
                    'scale-down' => 'Scale Down',
                ],
                'default' => '',
                'required' => false,
                'help' => 'How the image should fit within its container',
            ],
        ]);

        return $schema;
    }

    public function getAvailableValidationOptions(): array
    {
        // Image fields are display-only and don't collect data
        return [];
    }

    public function getPropertyTabs(): array
    {
        // Content fields don't need validation tab
        return [
            'validation' => false, // Hide validation tab
        ];
    }
}
