<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;

class HeaderField extends BaseFieldType
{
    public function getName(): string
    {
        return 'header';
    }

    public function getLabel(): string
    {
        return 'Header';
    }

    public function getIcon(): string
    {
        return 'bi bi-type-h1';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $headingLevel = $field->options['heading_level'] ?? 'h3';
        // Handle if heading_level is stored as an array (take first value)
        if (is_array($headingLevel)) {
            $headingLevel = $headingLevel[0] ?? 'h3';
        }

        // Build classes including text alignment
        $classes = [];
        if ($field->class) {
            $classes[] = $field->class;
        }
        $alignmentClasses = $this->buildTextAlignmentClasses($field);
        if ($alignmentClasses) {
            $classes[] = $alignmentClasses;
        }
        $className = implode(' ', $classes);

        $style = $field->style ?? '';

        $html = '<'.$headingLevel;
        if ($className) {
            $html .= ' class="'.htmlspecialchars($className).'"';
        }
        if ($style) {
            $html .= ' style="'.htmlspecialchars($style).'"';
        }
        $html .= '>';
        $html .= htmlspecialchars($field->label);
        $html .= '</'.$headingLevel.'>';

        if ($field->help_text) {
            $html .= '<p class="text-muted">'.htmlspecialchars($field->help_text).'</p>';
        }

        return $html;
    }

    public function renderBuilder(CustomFormField $field): string
    {
        $headingLevel = $field->options['heading_level'] ?? 'h3';
        // Handle if heading_level is stored as an array (take first value)
        if (is_array($headingLevel)) {
            $headingLevel = $headingLevel[0] ?? 'h3';
        }

        // Build classes including text alignment
        $classes = [];
        if ($field->class) {
            $classes[] = $field->class;
        }
        $alignmentClasses = $this->buildTextAlignmentClasses($field);
        if ($alignmentClasses) {
            $classes[] = $alignmentClasses;
        }
        $className = implode(' ', $classes);

        $style = $field->style ?? '';

        $html = '<'.$headingLevel;
        if ($className) {
            $html .= ' class="'.htmlspecialchars($className).'"';
        }
        if ($style) {
            $html .= ' style="'.htmlspecialchars($style).'"';
        }
        $html .= '>';
        $html .= htmlspecialchars($field->label);
        $html .= '</'.$headingLevel.'>';

        if ($field->help_text) {
            $html .= '<p class="text-muted">'.htmlspecialchars($field->help_text).'</p>';
        }

        return $html;
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        // Header fields don't collect data, so no validation needed
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
            'heading_level' => [
                'type' => 'select',
                'label' => 'Heading Level',
                'tab' => 'options',
                'target' => 'options',
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                ],
                'default' => 'h3',
                'required' => false,
            ],
            'class_name' => [
                'type' => 'text',
                'label' => 'CSS Class',
                'tab' => 'options',
                'target' => 'options',
                'required' => false,
                'help' => 'Additional CSS classes for styling',
            ],
        ]);
    }

    public function getAvailableValidationOptions(): array
    {
        // Header fields are display-only and don't collect data
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
