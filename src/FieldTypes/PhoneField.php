<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Services\InputMaskHelper;

class PhoneField extends BaseFieldType
{
    public function getName(): string
    {
        return 'phone';
    }

    public function getLabel(): string
    {
        return 'Phone Number';
    }

    public function getIcon(): string
    {
        return 'bi bi-telephone';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        // Country selector disabled globally
        $showCountryCode = false;
        $defaultCountry = 'US';
        $format = $field->options['format'] ?? 'international';
        $useFloating = $this->useFloatingLabel($field);

        $attributes = $this->getCommonAttributes($field);
        // Use text type to ensure proper behavior with floating labels
        $attributes['type'] = 'text';
        $attributes['value'] = $value ?? '';

        // Use lazy for input masks, otherwise respect validation timing
        if (InputMaskHelper::hasMask($field)) {
            $attributes['wire:model.lazy'] = 'formData.field_'.$field->id;

            // Add placeholder if mask type has one
            $maskType = $field->options['mask']['type'] ?? 'none';
            if (! $field->placeholder && $maskType !== 'none') {
                $attributes['placeholder'] = InputMaskHelper::getMaskPlaceholder($maskType);
            }
        } else {
            $attributes[$this->getWireModelAttribute($field)] = 'formData.field_'.$field->id;
            // Set default placeholder based on format if no placeholder set
            if (! isset($attributes['placeholder'])) {
                $attributes['placeholder'] = $format === 'us' ? '(555) 123-4567' : '+1 555 123 4567';
            }
        }

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';

        // Render label (handles floating vs standard)
        $html .= $this->renderLabelWithFloating($field, $attributes['id']);

        // Build input HTML (input-group only when not using floating labels)
        $attributes['@change'] = '$wire.refreshVisibility()';
        $inputElement = '<input '.$this->renderAttributes($attributes).$this->getValidationAttributes($field).'>';

        if ($useFloating) {
            // Floating labels are not compatible with input-group; render plain input inside form-floating
            $html .= $this->wrapFloatingLabel($field, $inputElement);
        } else {
            $inputHtml = $inputElement;
            if ($showCountryCode) {
                $inputHtml = '<div class="input-group">'
                    .'<select class="form-select" style="max-width: 120px;" wire:model="formData.field_'.$field->id.'_country" @change="$wire.refreshVisibility()">'
                    .'<option value="US" '.($defaultCountry === 'US' ? 'selected' : '').'>🇺🇸 +1</option>'
                    .'<option value="GB" '.($defaultCountry === 'GB' ? 'selected' : '').'>🇬🇧 +44</option>'
                    .'<option value="CA" '.($defaultCountry === 'CA' ? 'selected' : '').'>🇨🇦 +1</option>'
                    .'<option value="AU" '.($defaultCountry === 'AU' ? 'selected' : '').'>🇦🇺 +61</option>'
                    .'<option value="FR" '.($defaultCountry === 'FR' ? 'selected' : '').'>🇫🇷 +33</option>'
                    .'<option value="DE" '.($defaultCountry === 'DE' ? 'selected' : '').'>🇩🇪 +49</option>'
                    .'<option value="IT" '.($defaultCountry === 'IT' ? 'selected' : '').'>🇮🇹 +39</option>'
                    .'<option value="ES" '.($defaultCountry === 'ES' ? 'selected' : '').'>🇪🇸 +34</option>'
                    .'<option value="MX" '.($defaultCountry === 'MX' ? 'selected' : '').'>🇲🇽 +52</option>'
                    .'<option value="BR" '.($defaultCountry === 'BR' ? 'selected' : '').'>🇧🇷 +55</option>'
                    .'<option value="IN" '.($defaultCountry === 'IN' ? 'selected' : '').'>🇮🇳 +91</option>'
                    .'<option value="CN" '.($defaultCountry === 'CN' ? 'selected' : '').'>🇨🇳 +86</option>'
                    .'<option value="JP" '.($defaultCountry === 'JP' ? 'selected' : '').'>🇯🇵 +81</option>'
                    .'</select>'
                    .$inputElement
                    .'</div>';
            }
            $html .= $inputHtml;
        }

        // Only show invalid feedback when invalid (no forced display)
        $html .= $this->renderInvalidFeedback($field);
        $html .= $this->renderValidFeedback($field);
        $html .= $this->renderHelpText($field);

        $html .= InputMaskHelper::renderMaskScript($attributes['id'], $field);

        $html .= '</div>';

        return $html;
    }

    public function renderBuilder(CustomFormField $field): string
    {
        // Country selector disabled in builder preview
        $showCountryCode = false;
        $format = $field->options['format'] ?? 'international';

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';

        // Pass true for $includeAsterisk since CSS can't reach the nested input in input-group
        $html .= $this->renderLabel($field, $elementId ?? '', true);

        // Builder preview uses text type
        $html .= '<input type="text" class="form-control" ';
        $html .= 'placeholder="'.($format === 'us' ? '(555) 123-4567' : '+1 555 123 4567').'" disabled>';

        $html .= $this->renderHelpText($field);

        if ($showCountryCode) {
            $html .= '<div class="form-text small"><i class="bi bi-info-circle me-1"></i>Country code selector enabled</div>';
        }

        $html .= '</div>';

        return $html;
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        $rules = parent::validate($field, $value);
        $rules[] = 'string';

        return $rules;
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'format' => [
                'type' => 'select',
                'label' => 'Display Format',
                'tab' => 'options',
                'target' => 'options',
                'options' => [
                    'international' => 'International (+1 555 123 4567)',
                    'us' => 'US Format ((555) 123-4567)',
                ],
                'default' => 'international',
                'required' => false,
            ],
            'mask_enabled' => [
                'type' => 'switch',
                'label' => 'Enable Input Mask',
                'tab' => 'options',
                'target' => 'options.mask.enabled',
                'default' => false,
                'required' => false,
                'help' => 'Auto-format input as users type',
            ],
            'mask_type' => [
                'type' => 'select',
                'label' => 'Mask Type',
                'tab' => 'options',
                'target' => 'options.mask.type',
                'options' => [
                    'none' => 'None',
                    'phone_us' => 'Phone (US)',
                    'phone_intl' => 'Phone (International)',
                    'custom' => 'Custom Pattern',
                ],
                'default' => 'none',
                'required' => false,
                'help' => 'Select a preset mask or create a custom pattern',
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
                'target' => 'options.mask.pattern',
                'default' => '',
                'required' => false,
                'help' => '# = number, A = letter, * = alphanumeric',
                'placeholder' => '(###) ###-####',
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
            'regex' => [
                'type' => 'text',
                'label' => 'Phone Pattern',
                'help' => 'Regular expression for phone validation',
                'rule_format' => 'regex:{value}',
                'placeholder' => '/^[0-9\s\-\+\(\)]+$/',
            ],
            'min' => [
                'type' => 'number',
                'label' => 'Minimum Length',
                'help' => 'Minimum number of characters',
                'rule_format' => 'min:{value}',
            ],
            'max' => [
                'type' => 'number',
                'label' => 'Maximum Length',
                'help' => 'Maximum number of characters',
                'rule_format' => 'max:{value}',
            ],
        ];
    }
}
