<?php

namespace DigitalisStudios\SlickForms\Tests\Unit;

use DigitalisStudios\SlickForms\FieldTypes\CalculationField;
use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Tests\TestCase;

class CalculationFieldTest extends TestCase
{
    protected CalculationField $field;

    protected function setUp(): void
    {
        parent::setUp();
        $this->field = new CalculationField;
    }

    /** @test */
    public function it_has_correct_metadata()
    {
        $this->assertEquals('calculation', $this->field->getName());
        $this->assertEquals('Calculation', $this->field->getLabel());
        $this->assertEquals('bi bi-calculator', $this->field->getIcon());
    }

    /** @test */
    public function it_renders_visible_field_with_prefix_and_suffix()
    {
        $field = new CustomFormField([
            'name' => 'total',
            'label' => 'Total',
            'options' => [
                'formula' => '{price} * {quantity}',
                'display_mode' => 'visible',
                'prefix' => '$',
                'suffix' => ' USD',
            ],
        ]);
        $field->id = 1; // Set ID after creation to simulate persisted model

        $html = $this->field->render($field, '150.00');

        $this->assertStringContainsString('input-group', $html);
        $this->assertStringContainsString('input-group-text', $html);
        $this->assertStringContainsString('$', $html);
        $this->assertStringContainsString(' USD', $html);
        $this->assertStringContainsString('readonly', $html);
        $this->assertStringContainsString('wire:model="formData.field_1"', $html);
    }

    /** @test */
    public function it_renders_hidden_field()
    {
        $field = new CustomFormField([
            'name' => 'hidden_calc',
            'label' => 'Hidden Calculation',
            'options' => [
                'formula' => '{a} + {b}',
                'display_mode' => 'hidden',
            ],
        ]);
        $field->id = 2; // Set ID after creation to simulate persisted model

        $html = $this->field->render($field);

        $this->assertStringContainsString('type="hidden"', $html);
        $this->assertStringContainsString('wire:model="formData.field_2"', $html);
        $this->assertStringNotContainsString('input-group', $html);
    }

    /** @test */
    public function it_shows_formula_in_help_text_when_no_custom_help_text()
    {
        $field = new CustomFormField([
            'id' => 3,
            'name' => 'result',
            'label' => 'Result',
            'help_text' => null,
            'options' => [
                'formula' => '{x} + {y}',
                'display_mode' => 'visible',
            ],
        ]);

        $html = $this->field->render($field);

        $this->assertStringContainsString('bi-calculator', $html);
        $this->assertStringContainsString('Formula:', $html);
        $this->assertStringContainsString('{x} + {y}', $html);
    }

    /** @test */
    public function it_does_not_show_formula_when_custom_help_text_exists()
    {
        $field = new CustomFormField([
            'id' => 4,
            'name' => 'result',
            'label' => 'Result',
            'help_text' => 'This is custom help text',
            'options' => [
                'formula' => '{x} + {y}',
                'display_mode' => 'visible',
            ],
        ]);

        $html = $this->field->render($field);

        // Custom help text is rendered by BaseFieldType, not the field itself
        $this->assertStringNotContainsString('Formula:', $html);
    }

    /** @test */
    public function it_renders_builder_preview_with_formula()
    {
        $field = new CustomFormField([
            'id' => 5,
            'name' => 'total',
            'label' => 'Total',
            'options' => [
                'formula' => '{price} * {quantity}',
            ],
        ]);

        $html = $this->field->renderBuilder($field);

        $this->assertStringContainsString('[Calculated]', $html);
        $this->assertStringContainsString('bi-calculator', $html);
        $this->assertStringContainsString('Formula:', $html);
        $this->assertStringContainsString('{price} * {quantity}', $html);
    }

    /** @test */
    public function it_shows_warning_in_builder_when_no_formula_set()
    {
        $field = new CustomFormField([
            'id' => 6,
            'name' => 'total',
            'label' => 'Total',
            'options' => [],
        ]);

        $html = $this->field->renderBuilder($field);

        $this->assertStringContainsString('[Calculated]', $html);
        $this->assertStringContainsString('bi-exclamation-triangle', $html);
        $this->assertStringContainsString('No formula set', $html);
    }

    /** @test */
    public function it_is_not_required_by_default()
    {
        $field = new CustomFormField([
            'id' => 7,
            'name' => 'calc',
            'label' => 'Calculation',
            'is_required' => false,
            'options' => [
                'formula' => '{a} + {b}',
            ],
        ]);

        $rules = $this->field->validate($field, '100');

        $this->assertEmpty($rules);
    }

    /** @test */
    public function it_does_not_enforce_required_validation()
    {
        // Calculation fields are read-only, so required validation doesn't make sense
        $field = new CustomFormField([
            'id' => 8,
            'name' => 'calc',
            'label' => 'Calculation',
            'is_required' => true,
            'options' => [
                'formula' => '{a} + {b}',
            ],
        ]);

        $rules = $this->field->validate($field, null);

        // Should not return 'required' rule since field is calculated, not user input
        $this->assertEmpty($rules);
    }

    /** @test */
    public function it_returns_value_as_is_in_process_value()
    {
        // Calculation fields don't need special processing
        $value = '150.50';

        $processed = $this->field->processValue($value);

        $this->assertEquals('150.50', $processed);
    }

    /** @test */
    public function it_handles_null_value_in_process_value()
    {
        $processed = $this->field->processValue(null);

        $this->assertNull($processed);
    }

    /** @test */
    public function it_extracts_field_dependencies_from_formula()
    {
        $field = new CustomFormField([
            'id' => 9,
            'name' => 'total',
            'label' => 'Total',
            'options' => [
                'formula' => '({price} + {tax}) * {quantity}',
            ],
        ]);

        $dependencies = $this->field->extractDependencies($field->options['formula']);

        $this->assertEquals(['price', 'tax', 'quantity'], $dependencies);
    }

    /** @test */
    public function it_handles_formulas_with_functions()
    {
        $field = new CustomFormField([
            'id' => 10,
            'name' => 'average',
            'label' => 'Average',
            'options' => [
                'formula' => 'AVG({score1}, {score2}, {score3})',
            ],
        ]);

        $dependencies = $this->field->extractDependencies($field->options['formula']);

        $this->assertEquals(['score1', 'score2', 'score3'], $dependencies);
    }

    /** @test */
    public function it_returns_no_validation_options()
    {
        // Calculation fields don't have custom validation options
        // (they're read-only, so validation doesn't apply)
        $options = $this->field->getAvailableValidationOptions();

        $this->assertEmpty($options);
    }
}
