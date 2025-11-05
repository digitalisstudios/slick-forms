<?php

namespace DigitalisStudios\SlickForms\Tests\Unit;

use DigitalisStudios\SlickForms\Services\FormulaEvaluator;
use DigitalisStudios\SlickForms\Tests\TestCase;

class FormulaEvaluatorTest extends TestCase
{
    protected FormulaEvaluator $evaluator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluator = new FormulaEvaluator;
    }

    /** @test */
    public function it_evaluates_basic_addition()
    {
        $formula = '{price} + {tax}';
        $values = ['price' => 100, 'tax' => 8];

        $result = $this->evaluator->evaluate($formula, $values);

        $this->assertEquals(108.0, $result);
    }

    /** @test */
    public function it_evaluates_basic_subtraction()
    {
        $formula = '{total} - {discount}';
        $values = ['total' => 100, 'discount' => 15];

        $result = $this->evaluator->evaluate($formula, $values);

        $this->assertEquals(85.0, $result);
    }

    /** @test */
    public function it_evaluates_basic_multiplication()
    {
        $formula = '{price} * {quantity}';
        $values = ['price' => 25.50, 'quantity' => 3];

        $result = $this->evaluator->evaluate($formula, $values);

        $this->assertEquals(76.5, $result);
    }

    /** @test */
    public function it_evaluates_basic_division()
    {
        $formula = '{total} / {people}';
        $values = ['total' => 100, 'people' => 4];

        $result = $this->evaluator->evaluate($formula, $values);

        $this->assertEquals(25.0, $result);
    }

    /** @test */
    public function it_evaluates_modulo_operation()
    {
        $formula = '{value} % {divisor}';
        $values = ['value' => 17, 'divisor' => 5];

        $result = $this->evaluator->evaluate($formula, $values);

        $this->assertEquals(2.0, $result);
    }

    /** @test */
    public function it_respects_operator_precedence()
    {
        $formula = '{price} + {tax} * {quantity}';
        $values = ['price' => 100, 'tax' => 10, 'quantity' => 2];

        // Should be 100 + (10 * 2) = 120, not (100 + 10) * 2 = 220
        $result = $this->evaluator->evaluate($formula, $values);

        $this->assertEquals(120.0, $result);
    }

    /** @test */
    public function it_evaluates_parentheses_correctly()
    {
        $formula = '({price} + {tax}) * {quantity}';
        $values = ['price' => 100, 'tax' => 10, 'quantity' => 2];

        // Should be (100 + 10) * 2 = 220
        $result = $this->evaluator->evaluate($formula, $values);

        $this->assertEquals(220.0, $result);
    }

    /** @test */
    public function it_evaluates_sum_function()
    {
        $formula = 'SUM({jan}, {feb}, {mar})';
        $values = ['jan' => 100, 'feb' => 150, 'mar' => 200];

        $result = $this->evaluator->evaluate($formula, $values);

        $this->assertEquals(450.0, $result);
    }

    /** @test */
    public function it_evaluates_avg_function()
    {
        $formula = 'AVG({score1}, {score2}, {score3})';
        $values = ['score1' => 80, 'score2' => 90, 'score3' => 100];

        $result = $this->evaluator->evaluate($formula, $values);

        $this->assertEquals(90.0, $result);
    }

    /** @test */
    public function it_evaluates_min_function()
    {
        $formula = 'MIN({budget}, {actual})';
        $values = ['budget' => 5000, 'actual' => 4500];

        $result = $this->evaluator->evaluate($formula, $values);

        $this->assertEquals(4500.0, $result);
    }

    /** @test */
    public function it_evaluates_max_function()
    {
        $formula = 'MAX({jan}, {feb}, {mar})';
        $values = ['jan' => 100, 'feb' => 250, 'mar' => 150];

        $result = $this->evaluator->evaluate($formula, $values);

        $this->assertEquals(250.0, $result);
    }

    /** @test */
    public function it_evaluates_round_function()
    {
        $formula = 'ROUND({price} * {quantity}, 2)';
        $values = ['price' => 10.456, 'quantity' => 3];

        $result = $this->evaluator->evaluate($formula, $values);

        // ROUND applies during function replacement, then result is rounded again
        // 10.456 * 3 = 31.368, ROUND(31.368, 2) = 31.37
        $this->assertEquals(31.37, $result);
    }

    /** @test */
    public function it_evaluates_abs_function()
    {
        $formula = 'ABS({profit_loss})';
        $values = ['profit_loss' => -150.50];

        $result = $this->evaluator->evaluate($formula, $values);

        $this->assertEquals(150.5, $result);
    }

    /** @test */
    public function it_handles_missing_fields_as_zero()
    {
        $formula = '{price} + {discount}';
        $values = ['price' => 100]; // discount is missing

        $result = $this->evaluator->evaluate($formula, $values);

        $this->assertEquals(100.0, $result);
    }

    /** @test */
    public function it_handles_empty_field_values_as_zero()
    {
        $formula = '{price} * {quantity}';
        $values = ['price' => 50, 'quantity' => '']; // empty string

        $result = $this->evaluator->evaluate($formula, $values);

        $this->assertEquals(0.0, $result); // 50 * 0 = 0
    }

    /** @test */
    public function it_handles_division_by_zero()
    {
        $formula = '{total} / {count}';
        $values = ['total' => 100, 'count' => 0];

        $result = $this->evaluator->evaluate($formula, $values);

        $this->assertNull($result);
    }

    /** @test */
    public function it_rounds_to_specified_decimal_places()
    {
        $formula = '{price} / {quantity}';
        $values = ['price' => 100, 'quantity' => 3];

        // 100 / 3 = 33.33333...
        $result = $this->evaluator->evaluate($formula, $values, 2);
        $this->assertEquals(33.33, $result);

        $result = $this->evaluator->evaluate($formula, $values, 0);
        $this->assertEquals(33.0, $result);

        $result = $this->evaluator->evaluate($formula, $values, 4);
        $this->assertEquals(33.3333, $result);
    }

    /** @test */
    public function it_rejects_invalid_expressions()
    {
        $formula = '{price}; DROP TABLE users;'; // SQL injection attempt
        $values = ['price' => 100];

        $result = $this->evaluator->evaluate($formula, $values);

        $this->assertNull($result);
    }

    /** @test */
    public function it_handles_field_prefix_format()
    {
        // FormData stores fields as 'field_<name>'
        $formula = '{price} * {quantity}';
        $values = ['field_price' => 100, 'field_quantity' => 2];

        $result = $this->evaluator->evaluate($formula, $values);

        $this->assertEquals(200.0, $result);
    }

    /** @test */
    public function it_extracts_field_names_from_formula()
    {
        $formula = '{price} * {quantity} + {tax} - {discount}';

        $fieldNames = $this->evaluator->extractFieldNames($formula);

        $this->assertEquals(['price', 'quantity', 'tax', 'discount'], $fieldNames);
    }

    /** @test */
    public function it_extracts_unique_field_names()
    {
        $formula = '{price} + {price} * {tax}';

        $fieldNames = $this->evaluator->extractFieldNames($formula);

        $this->assertEquals(['price', 'tax'], $fieldNames);
    }

    /** @test */
    public function it_formats_value_as_number()
    {
        $formatted = $this->evaluator->formatValue(1234.567, 'number', 2);

        $this->assertEquals('1,234.57', $formatted);
    }

    /** @test */
    public function it_formats_value_as_currency()
    {
        $formatted = $this->evaluator->formatValue(1234.50, 'currency', 2, '$', '');

        $this->assertEquals('$1,234.50', $formatted);
    }

    /** @test */
    public function it_formats_value_as_percentage()
    {
        $formatted = $this->evaluator->formatValue(98.5, 'percentage', 1, '', '%');

        $this->assertEquals('98.5%', $formatted);
    }

    /** @test */
    public function it_formats_value_with_prefix_and_suffix()
    {
        $formatted = $this->evaluator->formatValue(100, 'number', 2, '€ ', ' EUR');

        $this->assertEquals('€ 100.00 EUR', $formatted);
    }

    /** @test */
    public function it_handles_null_value_in_formatting()
    {
        $formatted = $this->evaluator->formatValue(null, 'number', 2);

        $this->assertEquals('', $formatted);
    }

    /** @test */
    public function it_evaluates_complex_e_commerce_formula()
    {
        // Real-world example: total with tax and shipping
        $formula = '({price} * {quantity} + {shipping}) * (1 + {tax_rate} / 100)';
        $values = [
            'price' => 25.99,
            'quantity' => 3,
            'shipping' => 5.00,
            'tax_rate' => 8.5,
        ];

        // (25.99 * 3 + 5.00) * (1 + 8.5 / 100)
        // (77.97 + 5.00) * 1.085
        // 82.97 * 1.085 = 90.02245
        $result = $this->evaluator->evaluate($formula, $values, 2);

        $this->assertEquals(90.02, $result);
    }

    /** @test */
    public function it_evaluates_discount_calculator_formula()
    {
        // Real-world example: discount calculation
        $formula = '{original_price} - ({original_price} * {discount_percent} / 100)';
        $values = [
            'original_price' => 199.99,
            'discount_percent' => 25,
        ];

        // 199.99 - (199.99 * 25 / 100)
        // 199.99 - 49.9975
        // 149.9925
        $result = $this->evaluator->evaluate($formula, $values, 2);

        $this->assertEquals(149.99, $result);
    }

    /** @test */
    public function it_evaluates_bmi_calculator_formula()
    {
        // Real-world example: BMI calculation
        $formula = '{weight_kg} / (({height_cm} / 100) * ({height_cm} / 100))';
        $values = [
            'weight_kg' => 70,
            'height_cm' => 175,
        ];

        // 70 / ((175 / 100) * (175 / 100))
        // 70 / (1.75 * 1.75)
        // 70 / 3.0625
        // 22.857142...
        $result = $this->evaluator->evaluate($formula, $values, 1);

        $this->assertEquals(22.9, $result);
    }

    /** @test */
    public function it_evaluates_nested_functions()
    {
        // Complex example: average of three values, rounded
        $formula = 'ROUND(AVG({val1}, {val2}, {val3}), 1)';
        $values = [
            'val1' => 10.123,
            'val2' => 20.456,
            'val3' => 30.789,
        ];

        // AVG(10.123, 20.456, 30.789) = 20.456
        // ROUND(20.456, 1) = 20.5
        $result = $this->evaluator->evaluate($formula, $values, 1);

        $this->assertEquals(20.5, $result);
    }

    /** @test */
    public function it_handles_numeric_constants()
    {
        $formula = '{price} * 1.08'; // 8% markup
        $values = ['price' => 100];

        $result = $this->evaluator->evaluate($formula, $values);

        $this->assertEquals(108.0, $result);
    }

    /** @test */
    public function it_handles_negative_numbers()
    {
        $formula = '{revenue} - {expenses}';
        $values = ['revenue' => 1000, 'expenses' => 1500];

        $result = $this->evaluator->evaluate($formula, $values);

        $this->assertEquals(-500.0, $result);
    }

    /** @test */
    public function it_handles_case_insensitive_functions()
    {
        $formula1 = 'SUM({a}, {b})';
        $formula2 = 'sum({a}, {b})';
        $formula3 = 'Sum({a}, {b})';
        $values = ['a' => 10, 'b' => 20];

        $result1 = $this->evaluator->evaluate($formula1, $values);
        $result2 = $this->evaluator->evaluate($formula2, $values);
        $result3 = $this->evaluator->evaluate($formula3, $values);

        $this->assertEquals(30.0, $result1);
        $this->assertEquals(30.0, $result2);
        $this->assertEquals(30.0, $result3);
    }
}
