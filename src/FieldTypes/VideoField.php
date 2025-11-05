<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;

class VideoField extends BaseFieldType
{
    public function getName(): string
    {
        return 'video';
    }

    public function getLabel(): string
    {
        return 'Video';
    }

    public function getIcon(): string
    {
        return 'bi bi-camera-video';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $videoUrl = $field->options['video_url'] ?? '';
        $videoType = $field->options['video_type'] ?? 'url'; // 'url' or 'embed'
        $width = $field->options['width'] ?? '100%';
        $height = $field->options['height'] ?? '400px';
        $className = $field->class ?? '';
        $style = $field->style ?? '';

        if (! $videoUrl) {
            return '';
        }

        if ($videoType === 'embed') {
            // Embed code (YouTube, Vimeo iframe, etc.)
            $html = '<div class="ratio ratio-16x9';
            if ($className) {
                $html .= ' '.htmlspecialchars($className);
            }
            $html .= '"';
            if ($style) {
                $html .= ' style="'.htmlspecialchars($style).'"';
            }
            $html .= '>';
            $html .= $videoUrl; // Raw embed code
            $html .= '</div>';
        } else {
            // Direct video URL
            $html = '<video controls';
            if ($className) {
                $html .= ' class="'.htmlspecialchars($className).'"';
            }

            // Build style attribute
            $styles = [];
            $styles[] = 'width: '.htmlspecialchars($width);
            $styles[] = 'height: '.htmlspecialchars($height);
            if ($style) {
                $styles[] = $style;
            }
            $html .= ' style="'.htmlspecialchars(implode('; ', $styles)).'"';

            $html .= '>';
            $html .= '<source src="'.htmlspecialchars($videoUrl).'" type="video/mp4">';
            $html .= 'Your browser does not support the video tag.';
            $html .= '</video>';
        }

        if ($field->help_text) {
            $html .= '<p class="text-muted small mt-2">'.htmlspecialchars($field->help_text).'</p>';
        }

        return $html;
    }

    public function renderBuilder(CustomFormField $field): string
    {
        $videoUrl = $field->options['video_url'] ?? '';

        if (! $videoUrl) {
            // Show placeholder when no video is configured
            $html = '<div class="border rounded p-3 bg-dark text-center" style="min-height: 200px; display: flex; align-items: center; justify-content: center;">';
            $html .= '<div>';
            $html .= '<i class="bi bi-camera-video" style="font-size: 3rem; color: #adb5bd;"></i>';
            $html .= '<div class="mt-2 text-light small">Configure video in Field Properties</div>';
            $html .= '</div>';
            $html .= '</div>';

            return $html;
        }

        // Show actual video preview
        return $this->render($field);
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        // Video fields are display-only and don't collect data
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
            'video_type' => [
                'type' => 'select',
                'label' => 'Video Type',
                'tab' => 'basic',
                'target' => 'options',
                'options' => [
                    'url' => 'Direct Video URL',
                    'embed' => 'Embed Code (YouTube, Vimeo, etc.)',
                ],
                'default' => 'url',
                'required' => false,
            ],
            'video_url' => [
                'type' => 'textarea',
                'label' => 'Video URL or Embed Code',
                'tab' => 'basic',
                'target' => 'options',
                'placeholder' => 'For URL: https://example.com/video.mp4\nFor Embed: <iframe src="..."></iframe>',
                'help' => 'Enter a direct video URL or paste embed code from YouTube/Vimeo',
                'required' => false,
            ],
            // Re-add basic tab fields in desired order
            'help_text_as_popover' => $helpTextAsPopover,
            'help_text' => $helpText,
            'width' => [
                'type' => 'text',
                'label' => 'Width',
                'tab' => 'options',
                'target' => 'options',
                'placeholder' => 'e.g., 100%, 600px',
                'help' => 'Only applies to direct video URLs',
                'default' => '100%',
                'required' => false,
            ],
            'height' => [
                'type' => 'text',
                'label' => 'Height',
                'tab' => 'options',
                'target' => 'options',
                'placeholder' => 'e.g., 400px, auto',
                'help' => 'Only applies to direct video URLs',
                'default' => '400px',
                'required' => false,
            ],
        ]);

        return $schema;
    }

    public function getAvailableValidationOptions(): array
    {
        // Video fields are display-only and don't collect data
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
