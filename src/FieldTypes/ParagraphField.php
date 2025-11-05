<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;

class ParagraphField extends BaseFieldType
{
    public function getName(): string
    {
        return 'paragraph';
    }

    public function getLabel(): string
    {
        return 'Paragraph';
    }

    public function getIcon(): string
    {
        return 'bi bi-paragraph';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $content = $field->options['content'] ?? '';
        $className = $field->class ?? '';
        $style = $field->style ?? '';

        $html = '';

        if ($field->show_label && $field->label) {
            $html .= '<p class="fw-bold">'.htmlspecialchars($field->label).'</p>';
        }

        if ($content) {
            $html .= '<div';
            if ($className) {
                $html .= ' class="'.htmlspecialchars($className).'"';
            }
            if ($style) {
                $html .= ' style="'.htmlspecialchars($style).'"';
            }
            $html .= '>';
            // Content is HTML from Quill editor, so we don't escape it
            $html .= $content;
            $html .= '</div>';
        }

        return $html;
    }

    public function renderBuilder(CustomFormField $field): string
    {
        $content = $field->options['content'] ?? '';
        $className = $field->class ?? '';
        $style = $field->style ?? '';

        $html = '';

        if ($field->show_label && $field->label) {
            $html .= '<p class="fw-bold">'.htmlspecialchars($field->label).'</p>';
        }

        if ($content) {
            $html .= '<div';
            if ($className) {
                $html .= ' class="'.htmlspecialchars($className).'"';
            }
            if ($style) {
                $html .= ' style="'.htmlspecialchars($style).'"';
            }
            $html .= '>';
            // Content is HTML from Quill editor, so we don't escape it
            $html .= $content;
            $html .= '</div>';
        } else {
            $html .= '<p class="text-muted fst-italic">No content specified</p>';
        }

        return $html;
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        // Paragraph fields don't collect data, so no validation needed
        return [];
    }

    public function getConfigSchema(): array
    {
        $schema = parent::getConfigSchema();

        // Remove input-related options (this is a display-only field)
        unset($schema['floating_label']);
        unset($schema['field_size']);
        unset($schema['placeholder']);

        return array_merge($schema, [
            'content' => [
                'type' => 'wysiwyg',
                'label' => 'Paragraph Content',
                'tab' => 'basic',
                'target' => 'options',
                'required' => false,
                'help' => 'Use the WYSIWYG editor to format your paragraph content',
            ],
        ]);
    }

    public function getAvailableValidationOptions(): array
    {
        // Paragraph fields are display-only and don't collect data
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
