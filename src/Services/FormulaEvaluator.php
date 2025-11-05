<?php

namespace DigitalisStudios\SlickForms\Services;

use Exception;

class FormulaEvaluator
{
    /**
     * Evaluate a formula with given field values
     *
     * @param  string  $formula  The formula to evaluate (e.g., "{price} * {quantity}")
     * @param  array  $fieldValues  Array of field values keyed by field name
     * @param  int  $decimalPlaces  Number of decimal places for result
     */
    public function evaluate(string $formula, array $fieldValues, int $decimalPlaces = 2): ?float
    {
        try {
            // Step 1: Replace field references with values
            $expression = $this->replaceFieldReferences($formula, $fieldValues);

            // Step 2: Replace function calls
            $expression = $this->replaceFunctions($expression, $fieldValues);

            // Step 3: Validate expression (security check)
            if (! $this->isValidExpression($expression)) {
                return null;
            }

            // Step 4: Evaluate the mathematical expression
            $result = $this->evaluateExpression($expression);

            // Step 5: Round to specified decimal places
            if ($result !== null) {
                $result = round($result, $decimalPlaces);
            }

            return $result;
        } catch (Exception $e) {
            // Log error in production
            // \Log::error('Formula evaluation error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Replace field references like {field_name} with actual values
     */
    protected function replaceFieldReferences(string $formula, array $fieldValues): string
    {
        return preg_replace_callback('/\{([a-z0-9_]+)\}/i', function ($matches) use ($fieldValues) {
            $fieldName = $matches[1];

            // Try with 'field_' prefix (for formData keys)
            if (isset($fieldValues['field_'.$fieldName])) {
                $value = $fieldValues['field_'.$fieldName];
            } elseif (isset($fieldValues[$fieldName])) {
                $value = $fieldValues[$fieldName];
            } else {
                // Field not found or empty, return 0
                return '0';
            }

            // Convert to number, handle empty strings
            $numericValue = is_numeric($value) ? $value : 0;

            return (string) $numericValue;
        }, $formula);
    }

    /**
     * Replace function calls with their evaluated results
     */
    protected function replaceFunctions(string $expression, array $fieldValues): string
    {
        // SUM function: SUM({field1}, {field2}, {field3})
        $expression = preg_replace_callback('/SUM\(([^)]+)\)/i', function ($matches) use ($fieldValues) {
            $args = $this->parseArguments($matches[1]);
            $sum = 0;

            foreach ($args as $arg) {
                $value = $this->replaceFieldReferences($arg, $fieldValues);
                $sum += is_numeric($value) ? (float) $value : 0;
            }

            return (string) $sum;
        }, $expression);

        // AVG function: AVG({field1}, {field2}, {field3})
        $expression = preg_replace_callback('/AVG\(([^)]+)\)/i', function ($matches) use ($fieldValues) {
            $args = $this->parseArguments($matches[1]);
            $sum = 0;
            $count = count($args);

            foreach ($args as $arg) {
                $value = $this->replaceFieldReferences($arg, $fieldValues);
                $sum += is_numeric($value) ? (float) $value : 0;
            }

            return $count > 0 ? (string) ($sum / $count) : '0';
        }, $expression);

        // MIN function: MIN({field1}, {field2})
        $expression = preg_replace_callback('/MIN\(([^)]+)\)/i', function ($matches) use ($fieldValues) {
            $args = $this->parseArguments($matches[1]);
            $values = [];

            foreach ($args as $arg) {
                $value = $this->replaceFieldReferences($arg, $fieldValues);
                if (is_numeric($value)) {
                    $values[] = (float) $value;
                }
            }

            return ! empty($values) ? (string) min($values) : '0';
        }, $expression);

        // MAX function: MAX({field1}, {field2})
        $expression = preg_replace_callback('/MAX\(([^)]+)\)/i', function ($matches) use ($fieldValues) {
            $args = $this->parseArguments($matches[1]);
            $values = [];

            foreach ($args as $arg) {
                $value = $this->replaceFieldReferences($arg, $fieldValues);
                if (is_numeric($value)) {
                    $values[] = (float) $value;
                }
            }

            return ! empty($values) ? (string) max($values) : '0';
        }, $expression);

        // ROUND function: ROUND({field} or expression, decimals)
        $expression = preg_replace_callback('/ROUND\(([^,]+),\s*(\d+)\)/i', function ($matches) use ($fieldValues) {
            $innerExpression = trim($matches[1]);
            $decimals = (int) $matches[2];

            // If it's a simple field reference, replace it
            if (preg_match('/^\{[a-z0-9_]+\}$/i', $innerExpression)) {
                $value = $this->replaceFieldReferences($innerExpression, $fieldValues);
            } else {
                // It's already been processed (field references replaced with values)
                // Evaluate the expression
                $value = $this->evaluateExpression($innerExpression);
            }

            return is_numeric($value) ? (string) round((float) $value, $decimals) : '0';
        }, $expression);

        // ABS function: ABS({field})
        $expression = preg_replace_callback('/ABS\(([^)]+)\)/i', function ($matches) use ($fieldValues) {
            $value = $this->replaceFieldReferences($matches[1], $fieldValues);

            return is_numeric($value) ? (string) abs((float) $value) : '0';
        }, $expression);

        return $expression;
    }

    /**
     * Parse comma-separated arguments
     */
    protected function parseArguments(string $argumentString): array
    {
        return array_map('trim', explode(',', $argumentString));
    }

    /**
     * Validate that expression only contains safe characters
     */
    protected function isValidExpression(string $expression): bool
    {
        // Only allow: numbers, operators, parentheses, decimal points, whitespace, minus sign
        return preg_match('/^[\d+\-*\/%().\s]+$/', $expression) === 1;
    }

    /**
     * Safely evaluate a mathematical expression
     */
    protected function evaluateExpression(string $expression): ?float
    {
        // Remove whitespace
        $expression = str_replace(' ', '', $expression);

        // Handle empty expression
        if ($expression === '') {
            return null;
        }

        try {
            // Use a safe evaluation method
            // We've already validated the expression contains only safe characters

            // Handle division by zero
            if (preg_match('/\/\s*0(?![.\d])/', $expression)) {
                return null; // Division by zero
            }

            // Evaluate using PHP's built-in eval (safe because we validated the expression)
            // Alternative: use a math expression parser library for production
            $result = @eval('return '.$expression.';');

            return is_numeric($result) ? (float) $result : null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Extract field names from a formula
     */
    public function extractFieldNames(string $formula): array
    {
        preg_match_all('/\{([a-z0-9_]+)\}/i', $formula, $matches);

        return array_values(array_unique($matches[1] ?? []));
    }

    /**
     * Format a calculated value for display
     */
    public function formatValue(?float $value, string $displayAs, int $decimalPlaces = 2, string $prefix = '', string $suffix = ''): string
    {
        if ($value === null) {
            return '';
        }

        // Format based on display type
        switch ($displayAs) {
            case 'currency':
                $formatted = number_format($value, $decimalPlaces);
                break;
            case 'percentage':
                $formatted = number_format($value, $decimalPlaces);
                break;
            default:
                $formatted = number_format($value, $decimalPlaces);
        }

        // Add prefix and suffix
        return $prefix.$formatted.$suffix;
    }
}
