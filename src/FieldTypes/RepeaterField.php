<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;

class RepeaterField extends BaseFieldType
{
    public function getName(): string
    {
        return 'repeater';
    }

    public function getLabel(): string
    {
        return 'Repeater Group';
    }

    public function getIcon(): string
    {
        return 'bi bi-collection';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        // Rendering is complex - delegate to Blade partial
        // Value is array of instances, each instance is array of field values
        return view('slick-forms::partials.repeater-render', [
            'field' => $field,
            'value' => $value,
        ])->render();
    }

    public function renderBuilder(CustomFormField $field): string
    {
        // Builder preview showing child fields
        return view('slick-forms::partials.repeater-builder', [
            'field' => $field,
        ])->render();
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        // Validate array structure and instance count
        $minInstances = $field->options['min_instances'] ?? 1;
        $maxInstances = $field->options['max_instances'] ?? 10;

        $rules = [
            'required',
            'array',
            'min:'.$minInstances,
            'max:'.$maxInstances,
        ];

        return $rules;
    }

    public function processValue(mixed $value): mixed
    {
        // Value is array of instances
        // Each instance is array of field_id => value
        // Store as JSON
        return is_array($value) ? json_encode($value) : $value;
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'min_instances' => [
                'type' => 'number',
                'label' => 'Minimum Instances',
                'tab' => 'options',
                'target' => 'options',
                'default' => 1,
                'required' => false,
                'help' => 'Minimum number of instances required',
                'min' => 0,
                'max' => 100,
            ],
            'max_instances' => [
                'type' => 'number',
                'label' => 'Maximum Instances',
                'tab' => 'options',
                'target' => 'options',
                'default' => 10,
                'required' => false,
                'help' => 'Maximum number of instances allowed',
                'min' => 1,
                'max' => 100,
            ],
            'initial_instances' => [
                'type' => 'number',
                'label' => 'Initial Instances',
                'tab' => 'options',
                'target' => 'options',
                'default' => 1,
                'required' => false,
                'help' => 'Number of instances to show initially',
                'min' => 0,
                'max' => 100,
            ],
            'add_button_text' => [
                'type' => 'text',
                'label' => 'Add Button Text',
                'tab' => 'options',
                'target' => 'options',
                'default' => 'Add Another',
                'required' => false,
                'placeholder' => 'Add Another',
            ],
            'remove_button_text' => [
                'type' => 'text',
                'label' => 'Remove Button Text',
                'tab' => 'options',
                'target' => 'options',
                'default' => 'Remove',
                'required' => false,
                'placeholder' => 'Remove',
            ],
            'allow_reorder' => [
                'type' => 'switch',
                'label' => 'Allow Reorder',
                'tab' => 'options',
                'target' => 'options',
                'default' => true,
                'required' => false,
                'help' => 'Allow users to drag-and-drop to reorder instances',
            ],
            'layout_style' => [
                'type' => 'select',
                'label' => 'Layout Style',
                'tab' => 'options',
                'target' => 'options',
                'options' => [
                    'list' => 'List Items',
                    'card' => 'Card',
                    'accordion' => 'Accordion',
                    'plain' => 'Plain',
                ],
                'default' => 'list',
                'required' => false,
                'help' => 'Visual style for instances',
            ],
            'show_instance_number' => [
                'type' => 'switch',
                'label' => 'Show Instance Number',
                'tab' => 'options',
                'target' => 'options',
                'default' => true,
                'required' => false,
                'help' => 'Display instance number in header (e.g., "Contact #1")',
            ],
        ]);
    }

    public function getAvailableValidationOptions(): array
    {
        // Repeater validation is handled separately
        // Child fields have their own validation
        return [];
    }

    public function getPropertyTabs(): array
    {
        // Customize tabs for repeater fields
        return [
            // Hide validation tab (children have their own)
            'validation' => false,
            // Add a Children tab to manage nested fields in the properties panel
            'children' => [
                'label' => 'Children',
                'icon' => 'bi-diagram-3',
                'order' => 25,
                'view' => 'slick-forms::livewire.partials.tabs.repeater-children-tab',
            ],
        ];
    }
}
