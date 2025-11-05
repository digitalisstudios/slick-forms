<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;

class PasswordField extends BaseFieldType
{
    public function getName(): string
    {
        return 'password';
    }

    public function getLabel(): string
    {
        return 'Password';
    }

    public function getIcon(): string
    {
        return 'bi bi-key';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $showToggle = $field->options['show_toggle'] ?? true;
        $showStrength = $field->options['show_strength'] ?? true;
        $minimumStrength = $field->options['minimum_strength'] ?? 4;
        $fieldId = 'field_'.$field->id;

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';

        // Pass true for $includeAsterisk since CSS can't reach the nested input in input-group
        $html .= $this->renderLabel($field, $fieldId, true);

        if ($showToggle || $showStrength) {
            $html .= '<div x-data="{
                password: \'\',
                showPassword: false,
                strength: 0,
                strengthText: \'Weak\',
                strengthClass: \'danger\',
                minimumStrength: '.$minimumStrength.',
                checkStrength() {
                    const pass = this.password;
                    let score = 0;
                    if (pass.length >= 8) score++;
                    if (pass.length >= 12) score++;
                    if (/[a-z]/.test(pass) && /[A-Z]/.test(pass)) score++;
                    if (/[0-9]/.test(pass)) score++;
                    if (/[^a-zA-Z0-9]/.test(pass)) score++;

                    this.strength = score;
                    if (score <= 2) {
                        this.strengthText = \'Weak\';
                        this.strengthClass = \'danger\';
                    } else if (score === 3) {
                        this.strengthText = \'Fair\';
                        this.strengthClass = \'warning\';
                    } else if (score === 4) {
                        this.strengthText = \'Good\';
                        this.strengthClass = \'info\';
                    } else {
                        this.strengthText = \'Strong\';
                        this.strengthClass = \'success\';
                    }
                },
                meetsMinimum() {
                    return this.minimumStrength === 0 || this.strength >= this.minimumStrength;
                }
            }">';

            $html .= '<div class="input-group">';
            $html .= '<span class="input-group-text"><i class="bi bi-key"></i></span>';
            $html .= '<input :type="showPassword ? \'text\' : \'password\'" class="form-control" ';
            $html .= 'id="'.$fieldId.'" ';
            $html .= 'x-model="password" ';
            if ($showStrength) {
                $html .= '@input="checkStrength()" ';
            }
            $html .= $this->getWireModelAttribute($field).'="formData.field_'.$field->id.'" ';
            $html .= $this->getValidationAttributes($field).' @change="$wire.refreshVisibility()" ';
            $html .= 'placeholder="Enter password" ';
            if ($field->is_required) {
                $html .= 'required ';
            }
            $html .= '>';

            if ($showToggle) {
                $html .= '<button type="button" class="btn btn-outline-secondary" @click="showPassword = !showPassword">';
                $html .= '<i class="bi" :class="showPassword ? \'bi-eye-slash\' : \'bi-eye\'"></i>';
                $html .= '</button>';
            }

            $html .= '</div>';

            // Password strength indicator
            if ($showStrength) {
                $html .= '<div x-show="password.length > 0" class="mt-2">';
                $html .= '<div class="d-flex align-items-center gap-2">';
                $html .= '<div class="progress flex-grow-1" style="height: 6px;">';
                $html .= '<div class="progress-bar" :class="\'bg-\' + strengthClass" :style="{width: (strength * 20) + \'%\'}"></div>';
                $html .= '</div>';
                $html .= '<span class="small" :class="\'text-\' + strengthClass" x-text="strengthText"></span>';
                $html .= '</div>';

                // Show minimum strength warning if not met
                if ($minimumStrength > 0) {
                    $strengthNames = [1 => 'very weak', 2 => 'weak', 3 => 'fair', 4 => 'good', 5 => 'strong'];
                    $requiredStrength = $strengthNames[$minimumStrength] ?? 'unknown';
                    $html .= '<div x-show="password.length > 0 && !meetsMinimum()" class="small text-danger mt-1">';
                    $html .= '<i class="bi bi-exclamation-circle me-1"></i>Password must be at least '.htmlspecialchars($requiredStrength).' strength';
                    $html .= '</div>';
                }

                $html .= '</div>';
            }

            $html .= '</div>';
        } else {
            // Simple password input
            $html .= '<div class="input-group">';
            $html .= '<span class="input-group-text"><i class="bi bi-key"></i></span>';
            $html .= '<input type="password" class="form-control" ';
            $html .= 'id="'.$fieldId.'" ';
            $html .= $this->getWireModelAttribute($field).'="formData.field_'.$field->id.'" ';
            $html .= $this->getValidationAttributes($field).' @change="$wire.refreshVisibility()" ';
            $html .= 'placeholder="Enter password" ';
            if ($field->is_required) {
                $html .= 'required ';
            }
            $html .= '>';
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
        $showToggle = $field->options['show_toggle'] ?? true;
        $showStrength = $field->options['show_strength'] ?? true;
        $minimumStrength = $field->options['minimum_strength'] ?? 4;

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';

        // Pass true for $includeAsterisk since CSS can't reach the nested input in input-group
        $html .= $this->renderLabel($field, $elementId ?? '', true);

        $html .= '<div class="input-group">';
        $html .= '<span class="input-group-text"><i class="bi bi-key"></i></span>';
        $html .= '<input type="password" class="form-control" placeholder="••••••••" disabled>';
        if ($showToggle) {
            $html .= '<button type="button" class="btn btn-outline-secondary" disabled><i class="bi bi-eye"></i></button>';
        }
        $html .= '</div>';

        if ($showStrength) {
            $html .= '<div class="mt-2">';
            $html .= '<div class="progress" style="height: 6px;"><div class="progress-bar bg-success" style="width: 60%;"></div></div>';
            $html .= '</div>';
        }

        $features = [];
        if ($showToggle) {
            $features[] = 'Show/hide toggle';
        }
        if ($showStrength) {
            $strengthText = 'Strength indicator';
            if ($minimumStrength > 0) {
                $strengthNames = [1 => 'Very Weak', 2 => 'Weak', 3 => 'Fair', 4 => 'Good', 5 => 'Strong'];
                $strengthText .= ' (min: '.$strengthNames[$minimumStrength].')';
            }
            $features[] = $strengthText;
        }

        if (! empty($features)) {
            $html .= '<div class="form-text small"><i class="bi bi-info-circle me-1"></i>'.implode(' | ', $features).'</div>';
        }

        $html .= $this->renderHelpText($field);
        $html .= '</div>';

        return $html;
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        $rules = parent::validate($field, $value);
        $rules[] = 'string';

        // Add minimum strength validation if enabled
        $minimumStrength = $field->options['minimum_strength'] ?? 4;
        $showStrength = $field->options['show_strength'] ?? true;

        if ($showStrength && $minimumStrength > 0) {
            $rules[] = function ($attribute, $value, $fail) use ($minimumStrength) {
                if (empty($value)) {
                    return; // Let 'required' rule handle empty values
                }

                $strength = $this->calculatePasswordStrength($value);

                if ($strength < $minimumStrength) {
                    $strengthNames = [
                        1 => 'very weak',
                        2 => 'weak',
                        3 => 'fair',
                        4 => 'good',
                        5 => 'strong',
                    ];

                    $requiredStrength = $strengthNames[$minimumStrength] ?? 'unknown';
                    $fail("The password must be at least {$requiredStrength} strength.");
                }
            };
        }

        return $rules;
    }

    protected function calculatePasswordStrength(string $password): int
    {
        $score = 0;

        if (strlen($password) >= 8) {
            $score++;
        }
        if (strlen($password) >= 12) {
            $score++;
        }
        if (preg_match('/[a-z]/', $password) && preg_match('/[A-Z]/', $password)) {
            $score++;
        }
        if (preg_match('/[0-9]/', $password)) {
            $score++;
        }
        if (preg_match('/[^a-zA-Z0-9]/', $password)) {
            $score++;
        }

        return $score;
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'show_toggle' => [
                'type' => 'switch',
                'label' => 'Show/Hide Toggle Button',
                'tab' => 'options',
                'target' => 'options',
                'default' => true,
                'required' => false,
                'help' => 'Allow users to toggle password visibility',
            ],
            'show_strength' => [
                'type' => 'switch',
                'label' => 'Show Password Strength Indicator',
                'tab' => 'options',
                'target' => 'options',
                'default' => true,
                'required' => false,
                'help' => 'Display real-time password strength meter',
            ],
            'minimum_strength' => [
                'type' => 'number',
                'label' => 'Minimum Strength Required',
                'tab' => 'options',
                'target' => 'options',
                'default' => 4,
                'min' => 0,
                'max' => 5,
                'required' => false,
                'help' => 'Minimum password strength (0 = no requirement, 1 = very weak, 5 = strong)',
            ],
        ]);
    }

    public function getAvailableValidationOptions(): array
    {
        return [
            'min' => [
                'type' => 'number',
                'label' => 'Minimum Length',
                'help' => 'Minimum number of characters',
                'rule_format' => 'min:{value}',
                'placeholder' => '8',
            ],
            'confirmed' => [
                'type' => 'checkbox',
                'label' => 'Require Confirmation',
                'help' => 'Require a matching password confirmation field',
                'rule_format' => 'confirmed',
            ],
            'regex' => [
                'type' => 'text',
                'label' => 'Password Pattern',
                'help' => 'Regular expression for password requirements',
                'rule_format' => 'regex:{value}',
                'placeholder' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
            ],
        ];
    }
}
