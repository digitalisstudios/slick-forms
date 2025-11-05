<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;

class EmailField extends BaseFieldType
{
    public function getName(): string
    {
        return 'email';
    }

    public function getLabel(): string
    {
        return 'Email';
    }

    public function getIcon(): string
    {
        return 'bi bi-envelope';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $attributes = $this->getCommonAttributes($field);
        $attributes['type'] = 'email';
        $attributes['value'] = $value ?? '';
        $attributes[$this->getWireModelAttribute($field)] = 'formData.field_'.$field->id;
        $attributes['@change'] = '$wire.refreshVisibility()';

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';

        $html .= $this->renderLabelWithFloating($field, $attributes['id']);

        $inputHtml = '<input '.$this->renderAttributes($attributes).$this->getValidationAttributes($field).'>';

        $html .= $this->wrapFloatingLabel($field, $inputHtml);
        $html .= $this->renderInvalidFeedback($field);
        $html .= $this->renderValidFeedback($field);
        $html .= $this->renderHelpText($field);

        $html .= '</div>';

        return $html;
    }

    public function renderBuilder(CustomFormField $field): string
    {
        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';
        $html .= $this->renderLabel($field, $elementId ?? '');
        $html .= '<input type="email" class="form-control" placeholder="'.htmlspecialchars($field->placeholder ?? '').'" disabled>';
        $html .= $this->renderHelpText($field);

        $html .= '</div>';

        return $html;
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        $rules = parent::validate($field, $value);
        $rules[] = 'email';
        $rules[] = 'max:255';

        return $rules;
    }

    public function getConfigSchema(): array
    {
        // EmailField inherits all base fields - no additional options needed
        return parent::getConfigSchema();
    }

    public function getAvailableValidationOptions(): array
    {
        return [
            'email:rfc' => [
                'type' => 'checkbox',
                'label' => 'Strict RFC Compliance',
                'help' => 'Enforce strict email format according to RFC standards',
                'rule_format' => 'email:rfc',
            ],
            'email:dns' => [
                'type' => 'checkbox',
                'label' => 'DNS Verification',
                'help' => 'Verify the email domain has valid DNS records',
                'rule_format' => 'email:dns',
            ],
            'confirmed' => [
                'type' => 'checkbox',
                'label' => 'Require Confirmation',
                'help' => 'Require a matching email_confirmation field',
                'rule_format' => 'confirmed',
            ],
        ];
    }
}
