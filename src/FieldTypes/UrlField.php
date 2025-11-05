<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Services\InputMaskHelper;

class UrlField extends BaseFieldType
{
    public function getName(): string
    {
        return 'url';
    }

    public function getLabel(): string
    {
        return 'URL';
    }

    public function getIcon(): string
    {
        return 'bi bi-link-45deg';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $showPreview = $field->options['show_preview'] ?? false;
        $openInNewTab = $field->options['open_in_new_tab'] ?? true;
        $hasMask = InputMaskHelper::hasMask($field);
        $fieldId = 'field_'.$field->id;

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';

        // Support floating or standard labels (avoid input-group)
        $html .= $this->renderLabelWithFloating($field, $fieldId);

        // Plain input (no input-group) so floating labels work
        $input = '<input type="url" class="form-control" '
            .'id="'.$fieldId.'" '
            .($hasMask ? 'wire:model.lazy' : $this->getWireModelAttribute($field)).'="formData.field_'.$field->id.'" '
            .$this->getValidationAttributes($field).' @change="$wire.refreshVisibility()" '
            .'placeholder="https://example.com" '
            .'value="'.htmlspecialchars($value ?? '').'" '
            .($field->is_required ? 'required ' : '')
            .'>';

        $html .= $this->wrapFloatingLabel($field, $input);
        $html .= InputMaskHelper::renderMaskScript($fieldId, $field);

        if ($showPreview) {
            $html .= '<div class="mt-2">';
            $target = $openInNewTab ? "'_blank'" : "'_self'";
            $html .= '<button type="button" class="btn btn-sm btn-outline-primary" '
                .'onclick="var u=document.getElementById(\''.$fieldId.'\').value; if(u){ window.open(u, '.$target.'); } return false;"';
            $html .= '><i class="bi bi-box-arrow-up-right me-1"></i> Preview Link</button>';
            $html .= '</div>';
        }

        $html .= $this->renderInvalidFeedback($field);
        $html .= $this->renderValidFeedback($field);
        $html .= $this->renderHelpText($field);
        $html .= '</div>';

        return $html;
    }

    public function renderBuilder(CustomFormField $field): string
    {
        $showPreview = $field->options['show_preview'] ?? true;

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';

        // Standard label + input (no input-group) for preview
        $html .= $this->renderLabel($field, $elementId ?? '');
        $html .= '<input type="url" class="form-control" placeholder="https://example.com" disabled>';

        if ($showPreview) {
            $html .= '<div class="mt-2">';
            $html .= '<button class="btn btn-sm btn-outline-primary" disabled><i class="bi bi-box-arrow-up-right me-1"></i> Preview Link</button>';
            $html .= '</div>';
        }

        $html .= $this->renderHelpText($field);

        if ($showPreview) {
            $html .= '<div class="form-text small"><i class="bi bi-info-circle me-1"></i>Link preview enabled</div>';
        }

        $html .= '</div>';

        return $html;
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        $rules = parent::validate($field, $value);
        $rules[] = 'url';
        $rules[] = 'max:2048';

        return $rules;
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'show_preview' => [
                'type' => 'switch',
                'label' => 'Show Link Preview Button',
                'tab' => 'options',
                'target' => 'options',
                'default' => false,
                'required' => false,
                'help' => 'Display a button to open and preview the entered URL',
            ],
            'open_in_new_tab' => [
                'type' => 'switch',
                'label' => 'Open Preview in New Tab',
                'tab' => 'options',
                'target' => 'options',
                'default' => true,
                'required' => false,
                'help' => 'Open link preview in a new browser tab',
            ],
            'mask_enabled' => [
                'type' => 'switch',
                'label' => 'Enable Input Mask',
                'tab' => 'options',
                'target' => 'options.mask.enabled',
                'default' => true,
                'required' => false,
                'help' => 'Auto-format the URL as users type',
            ],
            'mask_type' => [
                'type' => 'select',
                'label' => 'Mask Type',
                'tab' => 'options',
                'target' => 'options.mask.type',
                'options' => [
                    'none' => 'None',
                    'slug' => 'Slug (a-z, 0-9, hyphen)',
                    'https_only' => 'HTTPS only (force https://)',
                    'custom' => 'Custom Pattern',
                ],
                'default' => 'https_only',
                'required' => false,
                'help' => 'Use a custom input pattern for the URL format',
                'show_if' => [
                    'field' => 'mask_enabled',
                    'operator' => '==',
                    'value' => true,
                ],
            ],
            'mask_pattern' => [
                'type' => 'text',
                'label' => 'Custom Pattern',
                'tab' => 'options',
                'target' => 'options.mask.custom_pattern',
                'default' => '',
                'required' => false,
                'help' => '# = number, A = letter, * = alphanumeric. Example: https://AAA.***.com/***',
                'placeholder' => 'https://AAA.***.com/***',
                'show_if' => [
                    ['field' => 'mask_enabled', 'operator' => '==', 'value' => true],
                    ['field' => 'mask_type', 'operator' => '==', 'value' => 'custom'],
                ],
            ],
        ]);
    }

    public function getAvailableValidationOptions(): array
    {
        return [
            'active_url' => [
                'type' => 'checkbox',
                'label' => 'Verify Active URL',
                'help' => 'Verify the URL is an active, accessible website',
                'rule_format' => 'active_url',
            ],
        ];
    }
}
