<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;

class CodeField extends BaseFieldType
{
    public function getName(): string
    {
        return 'code';
    }

    public function getLabel(): string
    {
        return 'Code';
    }

    public function getIcon(): string
    {
        return 'bi bi-code-slash';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $codeContent = $field->options['code_content'] ?? '';
        $className = $field->class ?? '';
        $style = $field->style ?? '';

        $html = '';

        if ($field->show_label && $field->label) {
            $html .= '<p class="fw-bold">'.htmlspecialchars($field->label).'</p>';
        }

        if ($codeContent) {
            // Wrap in a div with optional class/style
            if ($className || $style) {
                $html .= '<div';
                if ($className) {
                    $html .= ' class="'.htmlspecialchars($className).'"';
                }
                if ($style) {
                    $html .= ' style="'.htmlspecialchars($style).'"';
                }
                $html .= '>';
            }

            // Render the code content as actual HTML (not escaped)
            $html .= $codeContent;

            if ($className || $style) {
                $html .= '</div>';
            }
        }

        return $html;
    }

    public function renderBuilder(CustomFormField $field): string
    {
        $codeContent = $field->options['code_content'] ?? '';

        if (! $codeContent) {
            // Show placeholder when no code is configured
            $html = '<div class="border rounded p-3 bg-light text-center" style="min-height: 100px; display: flex; align-items: center; justify-content: center;">';
            $html .= '<div>';
            $html .= '<i class="bi bi-code-slash" style="font-size: 3rem; color: #6c757d;"></i>';
            $html .= '<div class="mt-2 text-muted small">Configure code in Field Properties</div>';
            $html .= '</div>';
            $html .= '</div>';

            return $html;
        }

        // Check if the code contains only non-visual content (style or script tags)
        $trimmedContent = trim($codeContent);
        $isStyleOnly = preg_match('/^<style[^>]*>.*<\/style>$/s', $trimmedContent);
        $isScriptOnly = preg_match('/^<script[^>]*>.*<\/script>$/s', $trimmedContent);

        // If it's only style/script tags, show an icon instead
        if ($isStyleOnly || $isScriptOnly) {
            $iconClass = $isStyleOnly ? 'bi-filetype-css' : 'bi-filetype-js';
            $iconColor = $isStyleOnly ? '#563d7c' : '#f7df1e';
            $labelText = $isStyleOnly ? 'CSS Code' : 'JavaScript Code';

            $html = '<div class="border rounded p-3 text-center" style="min-height: 80px; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">';
            $html .= '<div>';
            $html .= '<i class="bi '.$iconClass.'" style="font-size: 2.5rem; color: '.$iconColor.';"></i>';
            $html .= '<div class="mt-2 small fw-semibold" style="color: #6c757d;">'.$labelText.'</div>';
            $html .= '</div>';
            $html .= '</div>';

            return $html;
        }

        // Render the actual HTML in the builder canvas
        return $this->render($field);
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        // Code fields are display-only and don't collect data
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
            'code_content' => [
                'type' => 'ace_editor',
                'label' => 'Code Content (HTML/CSS/JavaScript)',
                'tab' => 'basic',
                'target' => 'options',
                'mode' => 'html',
                'required' => false,
                'help' => 'Enter HTML, CSS, or JavaScript code to display/execute',
            ],
        ]);
    }

    public function getAvailableValidationOptions(): array
    {
        // Code fields are display-only and don't collect data
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
