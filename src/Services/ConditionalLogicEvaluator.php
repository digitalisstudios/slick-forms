<?php

namespace DigitalisStudios\SlickForms\Services;

use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;
use Illuminate\Support\Collection;

class ConditionalLogicEvaluator
{
    /**
     * Evaluate if a field should be visible based on its conditional logic rules
     *
     * @param  CustomFormField  $field  The field to evaluate
     * @param  array  $formData  Current form data (element_id => value)
     * @param  Collection  $allFields  All form fields for ID lookup
     * @return bool True if field should be visible, false otherwise
     */
    public function shouldShowField(CustomFormField $field, array $formData, Collection $allFields): bool
    {
        // If no conditional logic, always show
        if (! $field->conditional_logic || empty($field->conditional_logic)) {
            return true;
        }

        $logic = $field->conditional_logic;

        if (isset($logic['rule_groups']) && is_array($logic['rule_groups'])) {
            return $this->evaluateRuleGroups($logic, $formData, $allFields);
        }

        // Legacy: Simple condition list (backward compatibility)
        if (! isset($logic['conditions']) || ! is_array($logic['conditions'])) {
            return true;
        }

        // Default action is 'show'
        $action = $logic['action'] ?? 'show';

        // Default match is 'all' (AND logic)
        $match = $logic['match'] ?? 'all';

        // Evaluate all conditions
        $results = [];
        foreach ($logic['conditions'] as $condition) {
            $results[] = $this->evaluateCondition($condition, $formData, $allFields);
        }

        // Apply match logic
        $conditionsMet = $match === 'any'
            ? in_array(true, $results, true)
            : ! in_array(false, $results, true);

        // Apply action
        return $action === 'show' ? $conditionsMet : ! $conditionsMet;
    }

    /**
     * Evaluate rule groups with nested AND/OR logic
     *
     * @param  array  $logic  The complete conditional logic structure
     * @param  array  $formData  Current form data
     * @param  Collection  $allFields  All form fields
     * @return bool True if field should be visible
     */
    protected function evaluateRuleGroups(array $logic, array $formData, Collection $allFields): bool
    {
        $action = $logic['action'] ?? 'show';
        $groupsMatch = $logic['groups_match'] ?? 'all'; // AND/OR between groups

        $groupResults = [];

        foreach ($logic['rule_groups'] as $group) {
            $groupResults[] = $this->evaluateRuleGroup($group, $formData, $allFields);
        }

        // Apply groups match logic (AND/OR between groups)
        $conditionsMet = $groupsMatch === 'any'
            ? in_array(true, $groupResults, true)
            : ! in_array(false, $groupResults, true);

        // Apply action
        return $action === 'show' ? $conditionsMet : ! $conditionsMet;
    }

    /**
     * Evaluate a single rule group
     *
     * @param  array  $group  The rule group to evaluate
     * @param  array  $formData  Current form data
     * @param  Collection  $allFields  All form fields
     * @return bool True if group conditions are met
     */
    protected function evaluateRuleGroup(array $group, array $formData, Collection $allFields): bool
    {
        $match = $group['match'] ?? 'all'; // AND/OR within group
        $conditions = $group['conditions'] ?? [];

        $results = [];

        foreach ($conditions as $condition) {
            $results[] = $this->evaluateCondition($condition, $formData, $allFields);
        }

        // Apply match logic within group
        return $match === 'any'
            ? in_array(true, $results, true)
            : ! in_array(false, $results, true);
    }

    /**
     * Evaluate a single condition
     *
     * @param  array  $condition  The condition to evaluate
     * @param  array  $formData  Current form data (element_id => value)
     * @param  Collection  $allFields  All form fields for ID lookup
     * @return bool True if condition is met
     */
    protected function evaluateCondition(array $condition, array $formData, Collection $allFields): bool
    {
        $operator = $condition['operator'] ?? 'equals';
        $expectedValue = $condition['value'] ?? null;

        // Get the element_id to look up in form data
        // Support both new target_field_id (database ID) and legacy target_element_id
        $elementId = null;

        if (isset($condition['target_field_id'])) {
            // New format: use field database ID, look up element_id
            $targetField = $allFields->firstWhere('id', $condition['target_field_id']);
            $elementId = $targetField?->element_id;
        } elseif (isset($condition['target_element_id'])) {
            // Legacy format: use element_id directly (backward compatibility)
            $elementId = $condition['target_element_id'];
        }

        // If no element_id found, condition is not met
        if (! $elementId || ! isset($formData[$elementId])) {
            return $this->handleMissingValue($operator);
        }

        $actualValue = $formData[$elementId];

        // Evaluate based on operator
        return match ($operator) {
            'equals' => $this->compareEquals($actualValue, $expectedValue),
            'not_equals' => ! $this->compareEquals($actualValue, $expectedValue),
            'contains' => $this->compareContains($actualValue, $expectedValue),
            'not_contains' => ! $this->compareContains($actualValue, $expectedValue),
            'greater_than' => $this->compareGreaterThan($actualValue, $expectedValue),
            'less_than' => $this->compareLessThan($actualValue, $expectedValue),
            'greater_than_or_equal' => $this->compareGreaterThanOrEqual($actualValue, $expectedValue),
            'less_than_or_equal' => $this->compareLessThanOrEqual($actualValue, $expectedValue),
            'is_empty' => $this->compareIsEmpty($actualValue),
            'is_not_empty' => ! $this->compareIsEmpty($actualValue),
            'in' => $this->compareIn($actualValue, $expectedValue),
            'not_in' => ! $this->compareIn($actualValue, $expectedValue),
            'checked' => $this->compareChecked($actualValue),
            'unchecked' => ! $this->compareChecked($actualValue),
            'after' => $this->compareGreaterThan($actualValue, $expectedValue),
            'before' => $this->compareLessThan($actualValue, $expectedValue),
            'after_or_equal' => $this->compareGreaterThanOrEqual($actualValue, $expectedValue),
            'before_or_equal' => $this->compareLessThanOrEqual($actualValue, $expectedValue),
            default => false,
        };
    }

    /**
     * Handle missing value based on operator
     */
    protected function handleMissingValue(string $operator): bool
    {
        return in_array($operator, ['is_empty', 'not_equals', 'not_contains', 'not_in', 'unchecked']);
    }

    /**
     * Compare for equality
     */
    protected function compareEquals(mixed $actual, mixed $expected): bool
    {
        // Handle arrays (multi-select, checkboxes)
        if (is_array($actual)) {
            return in_array($expected, $actual);
        }

        // Loose comparison for flexibility
        return $actual == $expected;
    }

    /**
     * Compare for contains (substring)
     */
    protected function compareContains(mixed $actual, mixed $expected): bool
    {
        if (is_array($actual)) {
            $actual = implode(',', $actual);
        }

        return str_contains((string) $actual, (string) $expected);
    }

    /**
     * Compare for greater than
     */
    protected function compareGreaterThan(mixed $actual, mixed $expected): bool
    {
        // Try to parse as dates first
        if (! is_numeric($actual) || ! is_numeric($expected)) {
            $actualTime = strtotime($actual);
            $expectedTime = strtotime($expected);

            if ($actualTime !== false && $expectedTime !== false) {
                return $actualTime > $expectedTime;
            }

            return false;
        }

        return (float) $actual > (float) $expected;
    }

    /**
     * Compare for less than
     */
    protected function compareLessThan(mixed $actual, mixed $expected): bool
    {
        // Try to parse as dates first
        if (! is_numeric($actual) || ! is_numeric($expected)) {
            $actualTime = strtotime($actual);
            $expectedTime = strtotime($expected);

            if ($actualTime !== false && $expectedTime !== false) {
                return $actualTime < $expectedTime;
            }

            return false;
        }

        return (float) $actual < (float) $expected;
    }

    /**
     * Compare for greater than or equal
     */
    protected function compareGreaterThanOrEqual(mixed $actual, mixed $expected): bool
    {
        // Try to parse as dates first
        if (! is_numeric($actual) || ! is_numeric($expected)) {
            $actualTime = strtotime($actual);
            $expectedTime = strtotime($expected);

            if ($actualTime !== false && $expectedTime !== false) {
                return $actualTime >= $expectedTime;
            }

            return false;
        }

        return (float) $actual >= (float) $expected;
    }

    /**
     * Compare for less than or equal
     */
    protected function compareLessThanOrEqual(mixed $actual, mixed $expected): bool
    {
        // Try to parse as dates first
        if (! is_numeric($actual) || ! is_numeric($expected)) {
            $actualTime = strtotime($actual);
            $expectedTime = strtotime($expected);

            if ($actualTime !== false && $expectedTime !== false) {
                return $actualTime <= $expectedTime;
            }

            return false;
        }

        return (float) $actual <= (float) $expected;
    }

    /**
     * Check if value is empty
     */
    protected function compareIsEmpty(mixed $actual): bool
    {
        if (is_array($actual)) {
            return empty($actual);
        }

        return $actual === '' || $actual === null;
    }

    /**
     * Check if value is in array
     */
    protected function compareIn(mixed $actual, mixed $expected): bool
    {
        // Expected should be comma-separated string or array
        if (is_string($expected)) {
            $expected = array_map('trim', explode(',', $expected));
        }

        if (! is_array($expected)) {
            return false;
        }

        // If actual is array, check if any value is in expected
        if (is_array($actual)) {
            return ! empty(array_intersect($actual, $expected));
        }

        return in_array($actual, $expected);
    }

    /**
     * Get all fields that should be visible based on conditional logic
     *
     * @param  Collection  $fields  Collection of CustomFormField
     * @param  array  $formData  Current form data
     * @return Collection Filtered collection of visible fields
     */
    public function filterVisibleFields(Collection $fields, array $formData): Collection
    {
        return $fields->filter(function (CustomFormField $field) use ($formData, $fields) {
            return $this->shouldShowField($field, $formData, $fields);
        });
    }

    /**
     * Get conditional validation rules that should be applied to a field
     *
     * @param  CustomFormField  $field  The field to evaluate
     * @param  array  $formData  Current form data (element_id => value)
     * @param  Collection  $allFields  All form fields for ID lookup
     * @return array Additional validation rules to apply
     */
    public function getConditionalValidationRules(CustomFormField $field, array $formData, Collection $allFields): array
    {
        // If no conditional logic, return empty
        if (! $field->conditional_logic || empty($field->conditional_logic)) {
            return [];
        }

        $logic = $field->conditional_logic;

        // Check if conditional_validation is defined
        if (! isset($logic['conditional_validation']) || ! is_array($logic['conditional_validation'])) {
            return [];
        }

        $appliedRules = [];

        // Evaluate each conditional validation rule
        foreach ($logic['conditional_validation'] as $conditionalRule) {
            if (! isset($conditionalRule['rule']) || ! isset($conditionalRule['conditions'])) {
                continue;
            }

            $match = $conditionalRule['match'] ?? 'all';

            // Evaluate all conditions for this validation rule
            $results = [];
            foreach ($conditionalRule['conditions'] as $condition) {
                $results[] = $this->evaluateCondition($condition, $formData, $allFields);
            }

            // Apply match logic
            $conditionsMet = $match === 'any'
                ? in_array(true, $results, true)
                : ! in_array(false, $results, true);

            // If conditions are met, add this validation rule
            if ($conditionsMet) {
                $appliedRules[] = $conditionalRule['rule'];
            }
        }

        return $appliedRules;
    }

    /**
     * Check if checkbox value is checked
     */
    protected function compareChecked(mixed $actual): bool
    {
        // Checkbox can be true, 1, "1", "on", "yes"
        if (is_bool($actual)) {
            return $actual === true;
        }

        if (is_numeric($actual)) {
            return (int) $actual === 1;
        }

        if (is_string($actual)) {
            return in_array(strtolower($actual), ['1', 'on', 'yes', 'true']);
        }

        return false;
    }

    /**
     * Evaluate if a layout element should be visible based on its conditional logic rules
     *
     * @param  SlickFormLayoutElement  $element  The element to evaluate
     * @param  array  $formData  Current form data (element_id => value)
     * @param  Collection  $allFields  All form fields for ID lookup
     * @return bool True if element should be visible, false otherwise
     */
    public function shouldShowElement(SlickFormLayoutElement $element, array $formData, Collection $allFields): bool
    {
        // If no conditional logic, always show
        if (! $element->conditional_logic || empty($element->conditional_logic)) {
            return true;
        }

        $logic = $element->conditional_logic;

        if (isset($logic['rule_groups']) && is_array($logic['rule_groups'])) {
            return $this->evaluateRuleGroups($logic, $formData, $allFields);
        }

        // Legacy: Simple condition list (backward compatibility)
        if (! isset($logic['conditions']) || ! is_array($logic['conditions'])) {
            return true;
        }

        // Default action is 'show'
        $action = $logic['action'] ?? 'show';

        // Default match is 'all' (AND logic)
        $match = $logic['match'] ?? 'all';

        // Evaluate all conditions
        $results = [];
        foreach ($logic['conditions'] as $condition) {
            $results[] = $this->evaluateCondition($condition, $formData, $allFields);
        }

        // Apply match logic
        $conditionsMet = $match === 'any'
            ? in_array(true, $results, true)
            : ! in_array(false, $results, true);

        // Apply action
        return $action === 'show' ? $conditionsMet : ! $conditionsMet;
    }

    /**
     * Get all layout elements that should be visible based on conditional logic
     *
     * @param  Collection  $elements  Collection of SlickFormLayoutElement
     * @param  array  $formData  Current form data
     * @param  Collection  $allFields  All form fields for ID lookup
     * @return Collection Filtered collection of visible elements
     */
    public function filterVisibleElements(Collection $elements, array $formData, Collection $allFields): Collection
    {
        return $elements->filter(function (SlickFormLayoutElement $element) use ($formData, $allFields) {
            return $this->shouldShowElement($element, $formData, $allFields);
        });
    }

    /**
     * Evaluate webhook/notification trigger conditions
     *
     * @param  array  $triggerConditions  Trigger conditions from webhook/notification (contains 'logic' and 'conditions')
     * @param  array  $formData  Form submission data (field name => value)
     * @param  \DigitalisStudios\SlickForms\Models\CustomForm  $form  The form instance
     * @return bool True if conditions are met
     */
    public function evaluateConditions(array $triggerConditions, array $formData, $form): bool
    {
        // If no conditions, always pass
        if (empty($triggerConditions) || empty($triggerConditions['conditions'])) {
            return true;
        }

        $logic = $triggerConditions['logic'] ?? 'and';
        $conditions = $triggerConditions['conditions'] ?? [];

        // Load all fields for ID lookup
        $allFields = $form->fields;

        // Evaluate each condition
        $results = [];
        foreach ($conditions as $condition) {
            $results[] = $this->evaluateWebhookCondition($condition, $formData, $allFields);
        }

        // Apply logic (AND/OR)
        return $logic === 'or'
            ? in_array(true, $results, true)
            : ! in_array(false, $results, true);
    }

    /**
     * Evaluate a single webhook condition (uses field_id instead of element_id)
     *
     * @param  array  $condition  The condition with field_id, operator, value
     * @param  array  $formData  Form data with field names as keys
     * @param  Collection  $allFields  All form fields
     * @return bool True if condition is met
     */
    protected function evaluateWebhookCondition(array $condition, array $formData, Collection $allFields): bool
    {
        $operator = $condition['operator'] ?? 'equals';
        $expectedValue = $condition['value'] ?? null;

        // Get field by database ID
        $fieldId = $condition['field_id'] ?? null;
        if (! $fieldId) {
            return false;
        }

        $field = $allFields->firstWhere('id', $fieldId);
        if (! $field) {
            return false;
        }

        // Get actual value from form data using field name
        $fieldName = $field->name;
        if (! isset($formData[$fieldName])) {
            return $this->handleMissingValue($operator);
        }

        $actualValue = $formData[$fieldName];

        // Evaluate based on operator (reuse existing comparison methods)
        return match ($operator) {
            'equals' => $this->compareEquals($actualValue, $expectedValue),
            'not_equals' => ! $this->compareEquals($actualValue, $expectedValue),
            'contains' => $this->compareContains($actualValue, $expectedValue),
            'not_contains' => ! $this->compareContains($actualValue, $expectedValue),
            'greater_than' => $this->compareGreaterThan($actualValue, $expectedValue),
            'less_than' => $this->compareLessThan($actualValue, $expectedValue),
            'greater_than_or_equal' => $this->compareGreaterThanOrEqual($actualValue, $expectedValue),
            'less_than_or_equal' => $this->compareLessThanOrEqual($actualValue, $expectedValue),
            'is_empty' => $this->compareIsEmpty($actualValue),
            'is_not_empty' => ! $this->compareIsEmpty($actualValue),
            'in' => $this->compareIn($actualValue, $expectedValue),
            'not_in' => ! $this->compareIn($actualValue, $expectedValue),
            'checked' => $this->compareChecked($actualValue),
            'unchecked' => ! $this->compareChecked($actualValue),
            'after' => $this->compareGreaterThan($actualValue, $expectedValue),
            'before' => $this->compareLessThan($actualValue, $expectedValue),
            'after_or_equal' => $this->compareGreaterThanOrEqual($actualValue, $expectedValue),
            'before_or_equal' => $this->compareLessThanOrEqual($actualValue, $expectedValue),
            default => false,
        };
    }

    /**
     * Get available operators for a specific field type
     * Returns array of operators that make sense for the given field type
     *
     * @param  string  $fieldType  The field type (e.g., 'text', 'number', 'select')
     * @return array Array of operator values (e.g., ['equals', 'not_equals', 'contains'])
     */
    public function getOperatorsForFieldType(string $fieldType): array
    {
        return match ($fieldType) {
            // Checkbox
            'checkbox' => ['checked', 'unchecked'],

            // Number, Slider, Star Rating
            'number', 'slider', 'star_rating', 'range' => [
                'equals',
                'not_equals',
                'greater_than',
                'less_than',
                'greater_than_or_equal',
                'less_than_or_equal',
                'is_empty',
                'is_not_empty',
            ],

            // Date fields
            'date', 'date_range', 'time' => [
                'equals',
                'not_equals',
                'after',
                'before',
                'after_or_equal',
                'before_or_equal',
                'is_empty',
                'is_not_empty',
            ],

            // Select, Radio
            'select', 'radio' => [
                'equals',
                'not_equals',
                'in',
                'not_in',
                'is_empty',
                'is_not_empty',
            ],

            // Tags
            'tags' => [
                'contains',
                'not_contains',
                'in',
                'not_in',
                'is_empty',
                'is_not_empty',
            ],

            // Switch
            'switch' => ['checked', 'unchecked'],

            // Text-based fields (Text, Textarea, Email, URL, Phone, Password)
            'text', 'textarea', 'email', 'url', 'phone', 'password' => [
                'equals',
                'not_equals',
                'contains',
                'not_contains',
                'starts_with',
                'ends_with',
                'regex',
                'is_empty',
                'is_not_empty',
            ],

            // Color Picker
            'color_picker' => [
                'equals',
                'not_equals',
                'is_empty',
                'is_not_empty',
            ],

            // File, Image, Video
            'file', 'image', 'video' => [
                'is_empty',
                'is_not_empty',
            ],

            // Default operators for unknown field types
            default => [
                'equals',
                'not_equals',
                'is_empty',
                'is_not_empty',
            ],
        };
    }

    /**
     * Get human-readable labels for operators
     *
     * @return array Operator value => label mapping
     */
    public function getOperatorLabels(): array
    {
        return [
            'equals' => 'Equals',
            'not_equals' => 'Does not equal',
            'contains' => 'Contains',
            'not_contains' => 'Does not contain',
            'starts_with' => 'Starts with',
            'ends_with' => 'Ends with',
            'regex' => 'Matches regex',
            'greater_than' => 'Greater than',
            'less_than' => 'Less than',
            'greater_than_or_equal' => 'Greater than or equal to',
            'less_than_or_equal' => 'Less than or equal to',
            'after' => 'Is after',
            'before' => 'Is before',
            'after_or_equal' => 'Is on or after',
            'before_or_equal' => 'Is on or before',
            'in' => 'Is one of',
            'not_in' => 'Is not one of',
            'checked' => 'Is checked',
            'unchecked' => 'Is unchecked',
            'is_empty' => 'Is empty',
            'is_not_empty' => 'Is not empty',
        ];
    }
}
