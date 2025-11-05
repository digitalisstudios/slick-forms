<?php

namespace DigitalisStudios\SlickForms\Tests\Unit;

use DigitalisStudios\SlickForms\FieldTypes\TextField;
use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Services\FieldTypeRegistry;
use DigitalisStudios\SlickForms\Tests\TestCase;

/**
 * Test schema export feature for field types
 */
class SchemaExportTest extends TestCase
{
    /** @test */
    public function field_type_can_export_full_schema()
    {
        $textField = new TextField;

        $schema = $textField->getFullSchema();

        $this->assertNotEmpty($schema);
        $this->assertJson($schema);
    }

    /** @test */
    public function exported_schema_contains_metadata_section()
    {
        $textField = new TextField;

        $schema = json_decode($textField->getFullSchema(), true);

        $this->assertArrayHasKey('metadata', $schema);
        $this->assertArrayHasKey('type', $schema['metadata']);
        $this->assertArrayHasKey('label', $schema['metadata']);
        $this->assertArrayHasKey('icon', $schema['metadata']);
        $this->assertArrayHasKey('description', $schema['metadata']);

        $this->assertEquals('text', $schema['metadata']['type']);
        $this->assertEquals('Text Input', $schema['metadata']['label']);
    }

    /** @test */
    public function exported_schema_contains_usage_section()
    {
        $textField = new TextField;

        $schema = json_decode($textField->getFullSchema(), true);

        $this->assertArrayHasKey('usage', $schema);
        $this->assertArrayHasKey('model', $schema['usage']);
        $this->assertArrayHasKey('method', $schema['usage']);
        $this->assertArrayHasKey('example', $schema['usage']);

        $this->assertEquals('CustomFormField', $schema['usage']['model']);
        $this->assertEquals('create', $schema['usage']['method']);
        $this->assertArrayHasKey('field_type', $schema['usage']['example']);
        $this->assertEquals('text', $schema['usage']['example']['field_type']);
    }

    /** @test */
    public function exported_schema_contains_properties_section()
    {
        $textField = new TextField;

        $schema = json_decode($textField->getFullSchema(), true);

        $this->assertArrayHasKey('properties', $schema);
        $this->assertIsArray($schema['properties']);

        // Check for essential properties
        $this->assertArrayHasKey('slick_form_id', $schema['properties']);
        $this->assertArrayHasKey('field_type', $schema['properties']);
        $this->assertArrayHasKey('name', $schema['properties']);
        $this->assertArrayHasKey('label', $schema['properties']);
        $this->assertArrayHasKey('is_required', $schema['properties']);
        $this->assertArrayHasKey('validation_rules', $schema['properties']);
        $this->assertArrayHasKey('conditional_logic', $schema['properties']);
        $this->assertArrayHasKey('options', $schema['properties']);
    }

    /** @test */
    public function property_definitions_include_storage_information()
    {
        $textField = new TextField;

        $schema = json_decode($textField->getFullSchema(), true);

        // Check column storage
        $this->assertEquals('column', $schema['properties']['name']['storage']);
        $this->assertEquals('column', $schema['properties']['label']['storage']);
        $this->assertEquals('column', $schema['properties']['is_required']['storage']);

        // Check JSON column storage (properties that use target => 'options')
        $this->assertEquals('json_column', $schema['properties']['floating_label']['storage']);
        $this->assertEquals('json_column', $schema['properties']['field_size']['storage']);
        $this->assertEquals('json_column', $schema['properties']['validation_timing']['storage']);
    }

    /** @test */
    public function exported_schema_contains_validation_rules_section()
    {
        $textField = new TextField;

        $schema = json_decode($textField->getFullSchema(), true);

        $this->assertArrayHasKey('validation_rules', $schema);
        $this->assertArrayHasKey('type', $schema['validation_rules']);
        $this->assertArrayHasKey('storage', $schema['validation_rules']);
        $this->assertArrayHasKey('description', $schema['validation_rules']);

        $this->assertEquals('array', $schema['validation_rules']['type']);
        $this->assertEquals('json_column', $schema['validation_rules']['storage']);
    }

    /** @test */
    public function exported_schema_contains_conditional_logic_section()
    {
        $textField = new TextField;

        $schema = json_decode($textField->getFullSchema(), true);

        $this->assertArrayHasKey('conditional_logic', $schema);
        $this->assertArrayHasKey('type', $schema['conditional_logic']);
        $this->assertArrayHasKey('storage', $schema['conditional_logic']);
        $this->assertArrayHasKey('structure', $schema['conditional_logic']);

        // Check structure
        $structure = $schema['conditional_logic']['structure'];
        $this->assertArrayHasKey('action', $structure);
        $this->assertArrayHasKey('match', $structure);
        $this->assertArrayHasKey('conditions', $structure);

        // Check action options
        $this->assertEquals(['show', 'hide'], $structure['action']['options']);

        // Check match options
        $this->assertEquals(['all', 'any'], $structure['match']['options']);
    }

    /** @test */
    public function conditional_logic_includes_field_type_operators()
    {
        $textField = new TextField;

        $schema = json_decode($textField->getFullSchema(), true);

        $operators = $schema['conditional_logic']['structure']['conditions']['structure']['operator']['options'];

        $this->assertIsArray($operators);
        $this->assertNotEmpty($operators);

        // Text fields should have these operators
        $this->assertContains('equals', $operators);
        $this->assertContains('not_equals', $operators);
        $this->assertContains('contains', $operators);
        $this->assertContains('not_contains', $operators);
        $this->assertContains('is_empty', $operators);
        $this->assertContains('is_not_empty', $operators);
    }

    /** @test */
    public function exported_schema_contains_tabs_section()
    {
        $textField = new TextField;

        $schema = json_decode($textField->getFullSchema(), true);

        $this->assertArrayHasKey('tabs', $schema);
        $this->assertIsArray($schema['tabs']);

        // Check for standard tabs
        $this->assertArrayHasKey('basic', $schema['tabs']);
        $this->assertArrayHasKey('options', $schema['tabs']);
        $this->assertArrayHasKey('validation', $schema['tabs']);
        $this->assertArrayHasKey('style', $schema['tabs']);
        $this->assertArrayHasKey('advanced', $schema['tabs']);

        // Check tab structure
        $this->assertArrayHasKey('label', $schema['tabs']['basic']);
        $this->assertArrayHasKey('icon', $schema['tabs']['basic']);
        $this->assertArrayHasKey('order', $schema['tabs']['basic']);
    }

    /** @test */
    public function properties_with_options_include_option_lists()
    {
        $textField = new TextField;

        $schema = json_decode($textField->getFullSchema(), true);

        // Field size should have options
        $this->assertArrayHasKey('field_size', $schema['properties']);
        $this->assertArrayHasKey('options', $schema['properties']['field_size']);

        $options = $schema['properties']['field_size']['options'];
        $this->assertContains('Default', $options);
        $this->assertContains('Small', $options);
        $this->assertContains('Large', $options);
    }

    /** @test */
    public function model_convenience_method_works()
    {
        $form = CustomForm::factory()->create();
        $field = CustomFormField::factory()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'test_field',
            'label' => 'Test Field',
        ]);

        $schema = $field->getFullSchema();

        $this->assertNotEmpty($schema);
        $this->assertJson($schema);

        $decoded = json_decode($schema, true);
        $this->assertEquals('text', $decoded['metadata']['type']);
    }

    /** @test */
    public function different_field_types_have_different_operators()
    {
        $registry = app(FieldTypeRegistry::class);

        // Text field
        $textField = $registry->get('text');
        $textSchema = json_decode($textField->getFullSchema(), true);
        $textOperators = $textSchema['conditional_logic']['structure']['conditions']['structure']['operator']['options'];

        // Checkbox field
        $checkboxField = $registry->get('checkbox');
        $checkboxSchema = json_decode($checkboxField->getFullSchema(), true);
        $checkboxOperators = $checkboxSchema['conditional_logic']['structure']['conditions']['structure']['operator']['options'];

        // Operators should be different
        $this->assertNotEquals($textOperators, $checkboxOperators);

        // Checkbox should have checked/unchecked
        $this->assertContains('checked', $checkboxOperators);
        $this->assertContains('unchecked', $checkboxOperators);

        // Text should not have checked/unchecked
        $this->assertNotContains('checked', $textOperators);
        $this->assertNotContains('unchecked', $textOperators);
    }

    /** @test */
    public function schema_includes_all_fillable_columns()
    {
        $textField = new TextField;
        $schema = json_decode($textField->getFullSchema(), true);

        $model = new CustomFormField;
        $fillable = $model->getFillable();

        // All fillable columns should be in properties
        foreach ($fillable as $column) {
            $this->assertArrayHasKey(
                $column,
                $schema['properties'],
                "Fillable column '{$column}' should be in schema properties"
            );
        }
    }

    /** @test */
    public function schema_example_includes_conditional_logic_structure()
    {
        $textField = new TextField;
        $schema = json_decode($textField->getFullSchema(), true);

        $this->assertArrayHasKey('example', $schema['conditional_logic']);

        $example = $schema['conditional_logic']['example'];
        $this->assertArrayHasKey('action', $example);
        $this->assertArrayHasKey('match', $example);
        $this->assertArrayHasKey('conditions', $example);

        $this->assertIsArray($example['conditions']);
        $this->assertNotEmpty($example['conditions']);

        $condition = $example['conditions'][0];
        $this->assertArrayHasKey('target_field_id', $condition);
        $this->assertArrayHasKey('operator', $condition);
        $this->assertArrayHasKey('value', $condition);
    }
}
