<?php

namespace DigitalisStudios\SlickForms\Tests\Unit;

use DigitalisStudios\SlickForms\Services\ConditionalLogicEvaluator;
use DigitalisStudios\SlickForms\Tests\TestCase;

/**
 * Test enhanced conditional logic operators for all field types
 */
class ConditionalLogicOperatorsTest extends TestCase
{
    protected ConditionalLogicEvaluator $evaluator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluator = app(ConditionalLogicEvaluator::class);
    }

    /** @test */
    public function checkbox_field_has_checked_unchecked_operators()
    {
        $operators = $this->evaluator->getOperatorsForFieldType('checkbox');

        $this->assertContains('checked', $operators);
        $this->assertContains('unchecked', $operators);
        $this->assertCount(2, $operators);
    }

    /** @test */
    public function switch_field_has_checked_unchecked_operators()
    {
        $operators = $this->evaluator->getOperatorsForFieldType('switch');

        $this->assertContains('checked', $operators);
        $this->assertContains('unchecked', $operators);
    }

    /** @test */
    public function number_field_has_comparison_operators()
    {
        $operators = $this->evaluator->getOperatorsForFieldType('number');

        $this->assertContains('equals', $operators);
        $this->assertContains('not_equals', $operators);
        $this->assertContains('greater_than', $operators);
        $this->assertContains('less_than', $operators);
        $this->assertContains('greater_than_or_equal', $operators);
        $this->assertContains('less_than_or_equal', $operators);
        $this->assertContains('is_empty', $operators);
        $this->assertContains('is_not_empty', $operators);
    }

    /** @test */
    public function slider_and_star_rating_have_same_operators_as_number()
    {
        $numberOps = $this->evaluator->getOperatorsForFieldType('number');
        $sliderOps = $this->evaluator->getOperatorsForFieldType('slider');
        $starOps = $this->evaluator->getOperatorsForFieldType('star_rating');
        $rangeOps = $this->evaluator->getOperatorsForFieldType('range');

        $this->assertEquals($numberOps, $sliderOps);
        $this->assertEquals($numberOps, $starOps);
        $this->assertEquals($numberOps, $rangeOps);
    }

    /** @test */
    public function date_field_has_date_specific_operators()
    {
        $operators = $this->evaluator->getOperatorsForFieldType('date');

        $this->assertContains('equals', $operators);
        $this->assertContains('not_equals', $operators);
        $this->assertContains('after', $operators);
        $this->assertContains('before', $operators);
        $this->assertContains('after_or_equal', $operators);
        $this->assertContains('before_or_equal', $operators);
        $this->assertContains('is_empty', $operators);
        $this->assertContains('is_not_empty', $operators);
    }

    /** @test */
    public function date_range_and_time_have_same_operators_as_date()
    {
        $dateOps = $this->evaluator->getOperatorsForFieldType('date');
        $dateRangeOps = $this->evaluator->getOperatorsForFieldType('date_range');
        $timeOps = $this->evaluator->getOperatorsForFieldType('time');

        $this->assertEquals($dateOps, $dateRangeOps);
        $this->assertEquals($dateOps, $timeOps);
    }

    /** @test */
    public function select_and_radio_have_option_based_operators()
    {
        $operators = $this->evaluator->getOperatorsForFieldType('select');

        $this->assertContains('equals', $operators);
        $this->assertContains('not_equals', $operators);
        $this->assertContains('in', $operators);
        $this->assertContains('not_in', $operators);
        $this->assertContains('is_empty', $operators);
        $this->assertContains('is_not_empty', $operators);

        $radioOps = $this->evaluator->getOperatorsForFieldType('radio');
        $this->assertEquals($operators, $radioOps);
    }

    /** @test */
    public function tags_field_has_array_operators()
    {
        $operators = $this->evaluator->getOperatorsForFieldType('tags');

        $this->assertContains('contains', $operators);
        $this->assertContains('not_contains', $operators);
        $this->assertContains('in', $operators);
        $this->assertContains('not_in', $operators);
        $this->assertContains('is_empty', $operators);
        $this->assertContains('is_not_empty', $operators);
    }

    /** @test */
    public function text_fields_have_string_operators()
    {
        $textTypes = ['text', 'textarea', 'email', 'url', 'phone', 'password'];

        foreach ($textTypes as $type) {
            $operators = $this->evaluator->getOperatorsForFieldType($type);

            $this->assertContains('equals', $operators, "Type: $type");
            $this->assertContains('not_equals', $operators, "Type: $type");
            $this->assertContains('contains', $operators, "Type: $type");
            $this->assertContains('not_contains', $operators, "Type: $type");
            $this->assertContains('starts_with', $operators, "Type: $type");
            $this->assertContains('ends_with', $operators, "Type: $type");
            $this->assertContains('regex', $operators, "Type: $type");
            $this->assertContains('is_empty', $operators, "Type: $type");
            $this->assertContains('is_not_empty', $operators, "Type: $type");
        }
    }

    /** @test */
    public function color_picker_has_basic_operators()
    {
        $operators = $this->evaluator->getOperatorsForFieldType('color_picker');

        $this->assertContains('equals', $operators);
        $this->assertContains('not_equals', $operators);
        $this->assertContains('is_empty', $operators);
        $this->assertContains('is_not_empty', $operators);
    }

    /** @test */
    public function file_fields_only_have_empty_operators()
    {
        $fileTypes = ['file', 'image', 'video'];

        foreach ($fileTypes as $type) {
            $operators = $this->evaluator->getOperatorsForFieldType($type);

            $this->assertContains('is_empty', $operators, "Type: $type");
            $this->assertContains('is_not_empty', $operators, "Type: $type");
            $this->assertCount(2, $operators, "Type: $type should only have empty operators");
        }
    }

    /** @test */
    public function unknown_field_type_gets_default_operators()
    {
        $operators = $this->evaluator->getOperatorsForFieldType('unknown_type');

        $this->assertContains('equals', $operators);
        $this->assertContains('not_equals', $operators);
        $this->assertContains('is_empty', $operators);
        $this->assertContains('is_not_empty', $operators);
    }

    /** @test */
    public function all_operators_are_strings()
    {
        $allTypes = [
            'checkbox', 'switch', 'number', 'slider', 'star_rating', 'range',
            'date', 'date_range', 'time', 'select', 'radio', 'tags',
            'text', 'textarea', 'email', 'url', 'phone', 'password',
            'color_picker', 'file', 'image', 'video',
        ];

        foreach ($allTypes as $type) {
            $operators = $this->evaluator->getOperatorsForFieldType($type);

            foreach ($operators as $operator) {
                $this->assertIsString($operator, "Operator for type $type should be string");
            }
        }
    }
}
