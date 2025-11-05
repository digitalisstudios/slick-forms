<?php

namespace DigitalisStudios\SlickForms\Tests\Unit;

use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;
use DigitalisStudios\SlickForms\Tests\TestCase;

/**
 * Test that all properties panel tabs correctly store field data
 * This tests the model-level data storage without Livewire
 */
class PropertiesPanelTabsTest extends TestCase
{
    /** @test */
    public function basic_tab_fields_can_be_stored()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
        ]);

        $container = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'container',
            'order' => 1,
        ]);

        $field = CustomFormField::create([
            'slick_form_id' => $form->id,
            'slick_form_layout_element_id' => $container->id,
            'field_type' => 'text',
            'name' => 'test_field',
            'label' => 'Test Field',
            'element_id' => 'test-field-1',
            'placeholder' => 'Enter text',
            'help_text' => 'Help text here',
            'help_text_as_popover' => true,
            'show_label' => true,
            'order' => 1,
        ]);

        // Assert basic information fields are stored
        $this->assertEquals('Test Field', $field->label);
        $this->assertEquals('test_field', $field->name);
        $this->assertEquals('test-field-1', $field->element_id);
        $this->assertEquals('Enter text', $field->placeholder);
        $this->assertEquals('Help text here', $field->help_text);
        $this->assertTrue($field->help_text_as_popover);
        $this->assertTrue($field->show_label);
    }

    /** @test */
    public function options_tab_fields_can_be_stored_in_options_json()
    {
        $form = CustomForm::create(['name' => 'Test Form', 'is_active' => true]);
        $container = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'container',
            'order' => 1,
        ]);

        $field = CustomFormField::create([
            'slick_form_id' => $form->id,
            'slick_form_layout_element_id' => $container->id,
            'field_type' => 'switch',
            'name' => 'switch_field',
            'label' => 'Switch',
            'options' => [
                'show_labels' => true,
                'on_label' => 'Active',
                'off_label' => 'Inactive',
                'field_size' => 'lg',
                'floating_label' => true,
            ],
            'order' => 1,
        ]);

        // Assert options are stored in JSON column
        $this->assertTrue($field->options['show_labels'] ?? false);
        $this->assertEquals('Active', $field->options['on_label'] ?? null);
        $this->assertEquals('Inactive', $field->options['off_label'] ?? null);
        $this->assertEquals('lg', $field->options['field_size'] ?? null);
        $this->assertTrue($field->options['floating_label'] ?? false);
    }

    /** @test */
    public function validation_tab_fields_can_be_stored()
    {
        $form = CustomForm::create(['name' => 'Test Form', 'is_active' => true]);
        $container = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'container',
            'order' => 1,
        ]);

        $field = CustomFormField::create([
            'slick_form_id' => $form->id,
            'slick_form_layout_element_id' => $container->id,
            'field_type' => 'email',
            'name' => 'email_field',
            'label' => 'Email',
            'is_required' => true,
            'validation_rules' => ['email', 'max:255'],
            'options' => [
                'custom_invalid_feedback' => 'Please enter a valid email',
                'custom_valid_feedback' => 'Email looks good!',
            ],
            'order' => 1,
        ]);

        // Assert validation fields are stored
        $this->assertTrue($field->is_required);
        $this->assertContains('email', $field->validation_rules);
        $this->assertContains('max:255', $field->validation_rules);
        $this->assertEquals('Please enter a valid email', $field->options['custom_invalid_feedback'] ?? null);
        $this->assertEquals('Email looks good!', $field->options['custom_valid_feedback'] ?? null);
    }

    /** @test */
    public function validation_tab_conditional_validation_can_be_stored()
    {
        $form = CustomForm::create(['name' => 'Test Form', 'is_active' => true]);
        $container = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'container',
            'order' => 1,
        ]);

        $conditionalLogic = [
            'conditional_validation' => [
                [
                    'rules' => ['max:100'],
                    'conditions' => [
                        [
                            'target_field' => 'other_field',
                            'operator' => 'equals',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ],
        ];

        $field = CustomFormField::create([
            'slick_form_id' => $form->id,
            'slick_form_layout_element_id' => $container->id,
            'field_type' => 'text',
            'name' => 'conditional_field',
            'label' => 'Conditional Field',
            'conditional_logic' => $conditionalLogic,
            'order' => 1,
        ]);

        // Assert conditional validation is stored
        $this->assertArrayHasKey('conditional_validation', $field->conditional_logic);
        $this->assertCount(1, $field->conditional_logic['conditional_validation']);
        $this->assertEquals(['max:100'], $field->conditional_logic['conditional_validation'][0]['rules']);
    }

    /** @test */
    public function style_tab_fields_can_be_stored()
    {
        $form = CustomForm::create(['name' => 'Test Form', 'is_active' => true]);
        $container = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'container',
            'order' => 1,
        ]);

        $field = CustomFormField::create([
            'slick_form_id' => $form->id,
            'slick_form_layout_element_id' => $container->id,
            'field_type' => 'text',
            'name' => 'styled_field',
            'label' => 'Styled Field',
            'class' => 'custom-class another-class',
            'style' => 'color: red; font-weight: bold;',
            'options' => [
                'margin_top' => 'mt-3',
                'margin_bottom' => 'mb-4',
                'padding' => 'p-2',
            ],
            'order' => 1,
        ]);

        // Assert style fields are stored
        $this->assertEquals('custom-class another-class', $field->class);
        $this->assertEquals('color: red; font-weight: bold;', $field->style);
        $this->assertEquals('mt-3', $field->options['margin_top'] ?? null);
        $this->assertEquals('mb-4', $field->options['margin_bottom'] ?? null);
        $this->assertEquals('p-2', $field->options['padding'] ?? null);
    }

    /** @test */
    public function advanced_tab_visibility_fields_can_be_stored()
    {
        $form = CustomForm::create(['name' => 'Test Form', 'is_active' => true]);
        $container = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'container',
            'order' => 1,
        ]);

        $field = CustomFormField::create([
            'slick_form_id' => $form->id,
            'slick_form_layout_element_id' => $container->id,
            'field_type' => 'text',
            'name' => 'responsive_field',
            'label' => 'Responsive Field',
            'options' => [
                'display_xs' => 'block',
                'display_sm' => 'none',
                'display_md' => 'block',
            ],
            'order' => 1,
        ]);

        // Assert responsive visibility is stored
        $this->assertEquals('block', $field->options['display_xs'] ?? null);
        $this->assertEquals('none', $field->options['display_sm'] ?? null);
        $this->assertEquals('block', $field->options['display_md'] ?? null);
    }

    /** @test */
    public function advanced_tab_conditional_logic_can_be_stored()
    {
        $form = CustomForm::create(['name' => 'Test Form', 'is_active' => true]);
        $container = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'container',
            'order' => 1,
        ]);

        $conditionalLogic = [
            'action' => 'show',
            'match' => 'all',
            'conditions' => [
                [
                    'target_field' => 'country',
                    'operator' => 'equals',
                    'value' => 'USA',
                ],
            ],
        ];

        $field = CustomFormField::create([
            'slick_form_id' => $form->id,
            'slick_form_layout_element_id' => $container->id,
            'field_type' => 'text',
            'name' => 'conditional_field',
            'label' => 'Conditional Field',
            'conditional_logic' => $conditionalLogic,
            'order' => 1,
        ]);

        // Assert conditional logic is stored
        $this->assertEquals('show', $field->conditional_logic['action'] ?? null);
        $this->assertEquals('all', $field->conditional_logic['match'] ?? null);
        $this->assertCount(1, $field->conditional_logic['conditions'] ?? []);
        $this->assertEquals('country', $field->conditional_logic['conditions'][0]['target_field'] ?? null);
    }

    /** @test */
    public function all_tabs_data_can_be_stored_together()
    {
        $form = CustomForm::create(['name' => 'Test Form', 'is_active' => true]);
        $container = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'container',
            'order' => 1,
        ]);

        $field = CustomFormField::create([
            'slick_form_id' => $form->id,
            'slick_form_layout_element_id' => $container->id,
            'field_type' => 'email',
            'name' => 'comprehensive_field',
            'label' => 'Comprehensive Test Field',
            'element_id' => 'comprehensive-1',
            'placeholder' => 'Enter value',
            'help_text' => 'Help text',
            'show_label' => true,
            'help_text_as_popover' => false,
            'is_required' => true,
            'validation_rules' => ['email', 'max:255'],
            'class' => 'custom-class',
            'style' => 'color: blue;',
            'options' => [
                'field_size' => 'lg',
                'margin_top' => 'mt-3',
                'display_xs' => 'block',
            ],
            'conditional_logic' => [
                'action' => 'show',
                'match' => 'all',
                'conditions' => [
                    ['target_field' => 'country', 'operator' => 'equals', 'value' => 'USA'],
                ],
            ],
            'order' => 1,
        ]);

        // Assert all data was stored correctly
        // Basic tab
        $this->assertEquals('Comprehensive Test Field', $field->label);
        $this->assertEquals('comprehensive_field', $field->name);
        $this->assertEquals('comprehensive-1', $field->element_id);
        $this->assertEquals('Enter value', $field->placeholder);
        $this->assertEquals('Help text', $field->help_text);
        $this->assertTrue($field->show_label);
        $this->assertFalse($field->help_text_as_popover);

        // Validation tab
        $this->assertTrue($field->is_required);
        $this->assertContains('email', $field->validation_rules);
        $this->assertContains('max:255', $field->validation_rules);

        // Style tab
        $this->assertEquals('custom-class', $field->class);
        $this->assertEquals('color: blue;', $field->style);
        $this->assertEquals('mt-3', $field->options['margin_top'] ?? null);

        // Options tab
        $this->assertEquals('lg', $field->options['field_size'] ?? null);

        // Advanced/Visibility tab
        $this->assertEquals('block', $field->options['display_xs'] ?? null);
        $this->assertEquals('show', $field->conditional_logic['action'] ?? null);
    }
}
