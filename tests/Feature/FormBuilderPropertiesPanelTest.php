<?php

namespace DigitalisStudios\SlickForms\Tests\Feature;

use DigitalisStudios\SlickForms\Livewire\FormBuilder;
use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;
use DigitalisStudios\SlickForms\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

/**
 * Test that all properties panel tabs correctly save field data
 */
class FormBuilderPropertiesPanelTest extends TestCase
{
    use RefreshDatabase;

    protected CustomForm $form;

    protected SlickFormLayoutElement $container;

    protected CustomFormField $field;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a form with a field
        $this->form = CustomForm::create([
            'name' => 'Test Form',
            'description' => 'Test form for properties panel',
            'is_active' => true,
        ]);

        // Create container
        $this->container = SlickFormLayoutElement::create([
            'slick_form_id' => $this->form->id,
            'element_type' => 'container',
            'order' => 1,
        ]);

        // Create a text field for testing
        $this->field = CustomFormField::create([
            'slick_form_id' => $this->form->id,
            'slick_form_layout_element_id' => $this->container->id,
            'field_type' => 'text',
            'name' => 'test_field',
            'label' => 'Test Field',
            'order' => 1,
        ]);
    }

    /** @test */
    public function basic_tab_saves_basic_information_fields()
    {
        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('editField', $this->field->id)
            ->set('properties.label', 'Updated Label')
            ->set('properties.name', 'updated_field')
            ->set('properties.element_id', 'updated-field-123')
            ->call('saveField');

        $this->field->refresh();
        $this->assertEquals('Updated Label', $this->field->label);
        $this->assertEquals('updated_field', $this->field->name);
        $this->assertEquals('updated-field-123', $this->field->element_id);
    }

    /** @test */
    public function basic_tab_saves_show_label_toggle()
    {
        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('editField', $this->field->id)
            ->set('properties.show_label', false)
            ->call('saveField');

        $this->field->refresh();
        $this->assertFalse($this->field->show_label);
    }

    /** @test */
    public function basic_tab_saves_placeholder()
    {
        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('editField', $this->field->id)
            ->set('properties.placeholder', 'Enter your text here')
            ->call('saveField');

        $this->field->refresh();
        $this->assertEquals('Enter your text here', $this->field->placeholder);
    }

    /** @test */
    public function basic_tab_saves_help_text()
    {
        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('editField', $this->field->id)
            ->set('properties.help_text', 'This is helpful information')
            ->call('saveField');

        $this->field->refresh();
        $this->assertEquals('This is helpful information', $this->field->help_text);
    }

    /** @test */
    public function basic_tab_saves_help_text_as_popover()
    {
        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('editField', $this->field->id)
            ->set('properties.help_text_as_popover', true)
            ->call('saveField');

        $this->field->refresh();
        $this->assertTrue($this->field->help_text_as_popover);
    }

    /** @test */
    public function basic_tab_saves_floating_label()
    {
        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('editField', $this->field->id)
            ->set('properties.floating_label', true)
            ->call('saveField');

        $this->field->refresh();
        $this->assertTrue($this->field->options['floating_label'] ?? false);
    }

    /** @test */
    public function basic_tab_saves_field_size()
    {
        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('editField', $this->field->id)
            ->set('properties.field_size', 'lg')
            ->call('saveField');

        $this->field->refresh();
        $this->assertEquals('lg', $this->field->options['field_size'] ?? null);
    }

    /** @test */
    public function options_tab_saves_switch_field_labels()
    {
        $switchField = CustomFormField::create([
            'slick_form_id' => $this->form->id,
            'slick_form_layout_element_id' => $this->container->id,
            'field_type' => 'switch',
            'name' => 'switch_field',
            'label' => 'Switch Field',
            'order' => 2,
        ]);

        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('editField', $switchField->id)
            ->set('properties.show_labels', true)
            ->set('properties.on_label', 'Active')
            ->set('properties.off_label', 'Inactive')
            ->call('saveField');

        $switchField->refresh();
        $this->assertTrue($switchField->options['show_labels'] ?? false);
        $this->assertEquals('Active', $switchField->options['on_label'] ?? null);
        $this->assertEquals('Inactive', $switchField->options['off_label'] ?? null);
    }

    /** @test */
    public function options_tab_saves_password_field_strength_options()
    {
        $passwordField = CustomFormField::create([
            'slick_form_id' => $this->form->id,
            'slick_form_layout_element_id' => $this->container->id,
            'field_type' => 'password',
            'name' => 'password_field',
            'label' => 'Password',
            'order' => 3,
        ]);

        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('editField', $passwordField->id)
            ->set('properties.show_toggle', true)
            ->set('properties.show_strength', true)
            ->set('properties.minimum_strength', 4)
            ->call('saveField');

        $passwordField->refresh();
        $this->assertTrue($passwordField->options['show_toggle'] ?? false);
        $this->assertTrue($passwordField->options['show_strength'] ?? false);
        $this->assertEquals(4, $passwordField->options['minimum_strength'] ?? null);
    }

    /** @test */
    public function options_tab_saves_select_field_options()
    {
        $selectField = CustomFormField::create([
            'slick_form_id' => $this->form->id,
            'slick_form_layout_element_id' => $this->container->id,
            'field_type' => 'select',
            'name' => 'select_field',
            'label' => 'Select Field',
            'order' => 4,
        ]);

        $options = [
            'values' => [
                ['label' => 'Option 1', 'value' => '1'],
                ['label' => 'Option 2', 'value' => '2'],
            ],
        ];

        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('editField', $selectField->id)
            ->set('properties.values', $options['values'])
            ->call('saveField');

        $selectField->refresh();
        $this->assertCount(2, $selectField->options['values'] ?? []);
        $this->assertEquals('Option 1', $selectField->options['values'][0]['label']);
    }

    /** @test */
    public function validation_tab_saves_required_field()
    {
        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('editField', $this->field->id)
            ->set('fieldIsRequired', true)
            ->call('saveField');

        $this->field->refresh();
        $this->assertTrue($this->field->is_required);
    }

    /** @test */
    public function validation_tab_saves_validation_rules()
    {
        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('editField', $this->field->id)
            ->set('fieldValidationOptions', ['email' => true, 'max' => 255])
            ->call('saveField');

        $this->field->refresh();
        $this->assertContains('email', $this->field->validation_rules);
        $this->assertContains('max:255', $this->field->validation_rules);
    }

    /** @test */
    public function validation_tab_saves_custom_validation_messages()
    {
        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('editField', $this->field->id)
            ->set('properties.custom_invalid_feedback', 'Please enter a valid value')
            ->set('properties.custom_valid_feedback', 'Looks good!')
            ->call('saveField');

        $this->field->refresh();
        $this->assertEquals('Please enter a valid value', $this->field->options['custom_invalid_feedback'] ?? null);
        $this->assertEquals('Looks good!', $this->field->options['custom_valid_feedback'] ?? null);
    }

    /** @test */
    public function validation_tab_saves_conditional_validation()
    {
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

        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('editField', $this->field->id)
            ->set('fieldConditionalLogic', $conditionalLogic)
            ->call('saveField');

        $this->field->refresh();
        $this->assertNotNull($this->field->conditional_logic);
        $this->assertArrayHasKey('conditional_validation', $this->field->conditional_logic);
    }

    /** @test */
    public function style_tab_saves_bootstrap_spacing()
    {
        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('editField', $this->field->id)
            ->set('properties.spacing.margin_top', '3')
            ->set('properties.spacing.margin_bottom', '4')
            ->set('properties.spacing.padding', '2')
            ->call('saveField');

        $this->field->refresh();
        $this->assertEquals('3', $this->field->options['spacing']['margin_top'] ?? null);
        $this->assertEquals('4', $this->field->options['spacing']['margin_bottom'] ?? null);
        $this->assertEquals('2', $this->field->options['spacing']['padding'] ?? null);
    }

    /** @test */
    public function style_tab_saves_css_classes()
    {
        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('editField', $this->field->id)
            ->set('properties.class', 'custom-class another-class')
            ->call('saveField');

        $this->field->refresh();
        $this->assertEquals('custom-class another-class', $this->field->class);
    }

    /** @test */
    public function style_tab_saves_inline_styles()
    {
        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('editField', $this->field->id)
            ->set('properties.style', 'color: red; font-weight: bold;')
            ->call('saveField');

        $this->field->refresh();
        $this->assertEquals('color: red; font-weight: bold;', $this->field->style);
    }

    /** @test */
    public function advanced_tab_saves_responsive_visibility()
    {
        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('editField', $this->field->id)
            ->set('properties.display.display', 'block')
            ->set('properties.display.display_sm', 'none')
            ->set('properties.display.display_md', 'block')
            ->call('saveField');

        $this->field->refresh();
        $this->assertEquals('block', $this->field->options['display']['display'] ?? null);
        $this->assertEquals('none', $this->field->options['display']['display_sm'] ?? null);
        $this->assertEquals('block', $this->field->options['display']['display_md'] ?? null);
    }

    /** @test */
    public function advanced_tab_saves_conditional_logic()
    {
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

        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('editField', $this->field->id)
            ->set('fieldConditionalLogic', $conditionalLogic)
            ->call('saveField');

        $this->field->refresh();
        $this->assertNotNull($this->field->conditional_logic);
        $this->assertEquals('show', $this->field->conditional_logic['action'] ?? null);
        $this->assertEquals('all', $this->field->conditional_logic['match'] ?? null);
        $this->assertCount(1, $this->field->conditional_logic['conditions'] ?? []);
    }

    /** @test */
    public function all_tabs_data_persists_after_save()
    {
        // Set data from all tabs
        $component = Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('editField', $this->field->id)
            // Basic tab
            ->set('properties.label', 'Comprehensive Test Field')
            ->set('properties.name', 'comprehensive_field')
            ->set('properties.placeholder', 'Enter value')
            ->set('properties.help_text', 'Help text')
            ->set('properties.show_label', true)
            // Validation tab
            ->set('fieldIsRequired', true)
            ->set('fieldValidationOptions', ['email' => true])
            // Style tab
            ->set('properties.class', 'custom-class')
            ->set('properties.style', 'color: blue;')
            // Options tab
            ->set('properties.field_size', 'lg')
            ->call('saveField');

        $this->field->refresh();

        // Verify all data was saved correctly
        $this->assertEquals('Comprehensive Test Field', $this->field->label);
        $this->assertEquals('comprehensive_field', $this->field->name);
        $this->assertEquals('Enter value', $this->field->placeholder);
        $this->assertEquals('Help text', $this->field->help_text);
        $this->assertTrue($this->field->show_label);
        $this->assertTrue($this->field->is_required);
        $this->assertContains('email', $this->field->validation_rules);
        $this->assertEquals('custom-class', $this->field->class);
        $this->assertEquals('color: blue;', $this->field->style);
        $this->assertEquals('lg', $this->field->options['field_size'] ?? null);
    }
}
