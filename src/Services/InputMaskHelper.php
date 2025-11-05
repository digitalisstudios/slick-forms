<?php

namespace DigitalisStudios\SlickForms\Services;

class InputMaskHelper
{
    /**
     * Get available mask types for selection
     */
    public static function getAvailableMasks(): array
    {
        return [
            ['label' => 'None', 'value' => 'none'],
            ['label' => 'Phone (US)', 'value' => 'phone_us'],
            ['label' => 'Phone (International)', 'value' => 'phone_international'],
            ['label' => 'Credit Card', 'value' => 'credit_card'],
            ['label' => 'Date (MM/DD/YYYY)', 'value' => 'date_mmddyyyy'],
            ['label' => 'Date (DD/MM/YYYY)', 'value' => 'date_ddmmyyyy'],
            ['label' => 'Date (YYYY-MM-DD)', 'value' => 'date_yyyymmdd'],
            ['label' => 'Time (12-hour)', 'value' => 'time_12hour'],
            ['label' => 'Time (24-hour)', 'value' => 'time_24hour'],
            ['label' => 'SSN', 'value' => 'ssn'],
            ['label' => 'ZIP Code', 'value' => 'zip_code'],
            ['label' => 'ZIP+4', 'value' => 'zip_code_plus4'],
            ['label' => 'Number (Decimal)', 'value' => 'number_decimal'],
            ['label' => 'Number (Integer)', 'value' => 'number_integer'],
            ['label' => 'Currency (USD)', 'value' => 'currency_usd'],
            ['label' => 'Percentage', 'value' => 'percentage'],
            ['label' => 'Custom Pattern', 'value' => 'custom'],
        ];
    }

    /**
     * Get Cleave.js configuration for a mask type
     */
    public static function getMaskConfig(string $maskType, ?string $customPattern = null): string
    {
        $configs = [
            // Phone Masks
            'phone_us' => json_encode([
                'numericOnly' => true,
                'blocks' => [0, 3, 3, 4],
                'delimiters' => ['(', ') ', '-'],
            ]),
            'phone_international' => json_encode([
                'numericOnly' => true,
                'blocks' => [0, 1, 3, 3, 4],
                'delimiters' => ['+', ' ', ' ', ' '],
            ]),

            // Credit Card
            'credit_card' => json_encode([
                'creditCard' => true,
            ]),

            // Date Masks
            'date_mmddyyyy' => json_encode([
                'date' => true,
                'datePattern' => ['m', 'd', 'Y'],
                'delimiter' => '/',
            ]),
            'date_ddmmyyyy' => json_encode([
                'date' => true,
                'datePattern' => ['d', 'm', 'Y'],
                'delimiter' => '/',
            ]),
            'date_yyyymmdd' => json_encode([
                'date' => true,
                'datePattern' => ['Y', 'm', 'd'],
                'delimiter' => '-',
            ]),

            // Time Masks
            'time_12hour' => json_encode([
                'blocks' => [2, 2],
                'delimiter' => ':',
                'numericOnly' => true,
            ]),
            'time_24hour' => json_encode([
                'blocks' => [2, 2],
                'delimiter' => ':',
                'numericOnly' => true,
            ]),

            // SSN and ZIP
            'ssn' => json_encode([
                'blocks' => [3, 2, 4],
                'delimiters' => ['-', '-'],
                'numericOnly' => true,
            ]),
            'zip_code' => json_encode([
                'blocks' => [5],
                'numericOnly' => true,
            ]),
            'zip_code_plus4' => json_encode([
                'blocks' => [5, 4],
                'delimiters' => ['-'],
                'numericOnly' => true,
            ]),

            // Number Masks
            'number_decimal' => json_encode([
                'numeral' => true,
                'numeralThousandsGroupStyle' => 'thousand',
                'numeralDecimalScale' => 2,
            ]),
            'number_integer' => json_encode([
                'numeral' => true,
                'numeralDecimalScale' => 0,
            ]),
            'currency_usd' => json_encode([
                'numeral' => true,
                'numeralThousandsGroupStyle' => 'thousand',
                'numeralDecimalScale' => 2,
                'prefix' => '$',
            ]),
            'percentage' => json_encode([
                'numeral' => true,
                'numeralDecimalScale' => 1,
                'numeralPositiveOnly' => true,
                'suffix' => '%',
            ]),
            'slug' => json_encode(new \stdClass),
            'https_only' => json_encode(new \stdClass),
        ];

        // Handle custom pattern
        if ($maskType === 'custom' && $customPattern) {
            return self::parseCustomPattern($customPattern);
        }

        return $configs[$maskType] ?? '{}';
    }

    /**
     * Parse custom pattern into Cleave.js config
     *
     * Pattern syntax:
     * # = numeric
     * A = alphabetic
     * * = alphanumeric
     * Example: "###-##-####" becomes blocks:[3,2,4], delimiter:"-"
     */
    protected static function parseCustomPattern(string $pattern): string
    {
        $blocks = [];
        $delimiters = [];
        $currentBlock = 0;
        $numericOnly = true;
        $uppercase = false;

        $chars = str_split($pattern);
        foreach ($chars as $char) {
            if ($char === '#') {
                $currentBlock++;
            } elseif ($char === 'A' || $char === '*') {
                $currentBlock++;
                $numericOnly = false;
                if ($char === 'A') {
                    $uppercase = true;
                }
            } elseif ($char === '-' || $char === ' ' || $char === '/' || $char === ':') {
                if ($currentBlock > 0) {
                    $blocks[] = $currentBlock;
                    $delimiters[] = $char;
                    $currentBlock = 0;
                }
            }
        }

        // Add final block
        if ($currentBlock > 0) {
            $blocks[] = $currentBlock;
        }

        $config = [
            'blocks' => $blocks,
        ];

        if (! empty($delimiters)) {
            $config['delimiters'] = $delimiters;
        }

        if ($numericOnly) {
            $config['numericOnly'] = true;
        }

        if ($uppercase) {
            $config['uppercase'] = true;
        }

        return json_encode($config);
    }

    /**
     * Get mask example/placeholder for a mask type
     */
    public static function getMaskPlaceholder(string $maskType): string
    {
        $placeholders = [
            'phone_us' => '(555) 123-4567',
            'phone_international' => '+1 555 123 4567',
            'credit_card' => '4111 1111 1111 1111',
            'date_mmddyyyy' => 'MM/DD/YYYY',
            'date_ddmmyyyy' => 'DD/MM/YYYY',
            'date_yyyymmdd' => 'YYYY-MM-DD',
            'time_12hour' => 'HH:MM',
            'time_24hour' => 'HH:MM',
            'ssn' => '123-45-6789',
            'zip_code' => '12345',
            'zip_code_plus4' => '12345-6789',
            'number_decimal' => '1,234.56',
            'number_integer' => '1,234',
            'currency_usd' => '$1,234.56',
            'percentage' => '98.5%',
            'slug' => 'my-page',
            'https_only' => 'https://example.com',
        ];

        return $placeholders[$maskType] ?? '';
    }

    /**
     * Check if field has mask enabled
     */
    public static function hasMask($field): bool
    {
        return isset($field->options['mask']['enabled'])
            && $field->options['mask']['enabled'] === true
            && ! empty($field->options['mask']['type'])
            && $field->options['mask']['type'] !== 'none';
    }

    /**
     * Render mask initialization script using Alpine.js
     */
    public static function renderMaskScript(string $fieldId, $field): string
    {
        if (! self::hasMask($field)) {
            return '';
        }

        $maskType = $field->options['mask']['type'] ?? 'none';
        $customPattern = $field->options['mask']['custom_pattern'] ?? null;
        $maskConfig = self::getMaskConfig($maskType, $customPattern);

        // Escape single quotes in the config for use in Alpine attribute
        $escapedConfig = str_replace("'", "\\'", $maskConfig);

        // Use Alpine.js x-init to initialize the mask
        // Use single quotes for x-init attribute to avoid JSON quote conflicts
        $jsExtra = '';
        $type = $field->options['mask']['type'] ?? 'none';
        if ($type === 'slug') {
            $jsExtra = "\n                const formatSlug = (s) => s.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').replace(/-{2,}/g, '-');\\n                const onInput = () => {\\n                    const val = input.value;\\n                    const next = formatSlug(val);\\n                    if (next !== val) {\\n                        input.value = next;\\n                        input.dispatchEvent(new Event('input', { bubbles: true }));\\n                    }\\n                };\\n                input.addEventListener('input', onInput);\\n            ";
        } elseif ($type === 'https_only') {
            $jsExtra = "\n                const ensureHttps = () => {\\n                    let v = (input.value || '').trim();\\n                    if (!v) return;\\n                    if (v.startsWith('http://')) {\\n                        v = 'https://' + v.substring(7);\\n                    } else if (!v.startsWith('https://')) {\\n                        v = 'https://' + v.replace(/^\\\\/+/, '');\\n                    }\\n                    if (v !== input.value) {\\n                        input.value = v;\\n                        input.dispatchEvent(new Event('input', { bubbles: true }));\\n                    }\\n                };\\n                input.addEventListener('blur', ensureHttps);\\n                input.addEventListener('change', ensureHttps);\\n            ";
        }

        return <<<HTML
        <div x-data x-init='
            let input = document.getElementById("{$fieldId}");
            if (input && typeof Cleave !== "undefined") {
                if (input.cleaveInstance) {
                    input.cleaveInstance.destroy();
                }
                input.cleaveInstance = new Cleave(input, {$escapedConfig});{$jsExtra}
            } else if (input) {
                let retries = 0;
                let interval = setInterval(() => {
                    if (typeof Cleave !== "undefined") {
                        clearInterval(interval);
                        if (input.cleaveInstance) {
                            input.cleaveInstance.destroy();
                        }
                        input.cleaveInstance = new Cleave(input, {$escapedConfig});{$jsExtra}
                    } else if (++retries > 20) {
                        clearInterval(interval);
                    }
                }, 100);
            }
        ' style="display: none;"></div>
        HTML;
    }
}
