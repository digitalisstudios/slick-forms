<?php

namespace DigitalisStudios\SlickForms\Tests\Unit;

use DigitalisStudios\SlickForms\FieldTypes\LocationPickerField;
use DigitalisStudios\SlickForms\FieldTypes\RatingMatrixField;
use DigitalisStudios\SlickForms\FieldTypes\SignaturePadField;
use DigitalisStudios\SlickForms\Models\CustomFormField;
use Orchestra\Testbench\TestCase;

class Phase5NewFieldTypesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function signature_pad_field_has_correct_identifier()
    {
        $field = new SignaturePadField;

        $this->assertEquals('signature', $field->getName());
    }

    /** @test */
    public function signature_pad_field_has_correct_label()
    {
        $field = new SignaturePadField;

        $this->assertEquals('Signature Pad', $field->getLabel());
    }

    /** @test */
    public function signature_pad_field_has_bootstrap_icon()
    {
        $field = new SignaturePadField;

        $this->assertStringContainsString('bi', $field->getIcon());
    }

    /** @test */
    public function signature_pad_field_renders_with_canvas()
    {
        $field = new SignaturePadField;
        $formField = new CustomFormField([
            'field_type' => 'signature',
            'name' => 'signature_field',
            'label' => 'Sign Here',
            'options' => [],
        ]);
        $formField->id = 1;

        $html = $field->render($formField);

        $this->assertStringContainsString('canvas', $html);
        $this->assertStringContainsString('signature_canvas_1', $html);
        $this->assertStringContainsString('signature_pad@4.1.7', $html);
    }

    /** @test */
    public function signature_pad_field_has_config_schema()
    {
        $field = new SignaturePadField;
        $schema = $field->getConfigSchema();

        $this->assertArrayHasKey('canvas_width', $schema);
        $this->assertArrayHasKey('canvas_height', $schema);
        $this->assertArrayHasKey('pen_color', $schema);
        $this->assertArrayHasKey('background_color', $schema);
        $this->assertEquals('number', $schema['canvas_width']['type']);
        $this->assertEquals('color', $schema['pen_color']['type']);
    }

    /** @test */
    public function signature_pad_field_validates_as_string()
    {
        $field = new SignaturePadField;
        $formField = new CustomFormField([
            'validation_rules' => [],
        ]);

        $rules = $field->validate($formField, 'test-value');

        $this->assertContains('string', $rules);
        $this->assertContains('nullable', $rules);
    }

    /** @test */
    public function location_picker_field_has_correct_identifier()
    {
        $field = new LocationPickerField;

        $this->assertEquals('location', $field->getName());
    }

    /** @test */
    public function location_picker_field_has_correct_label()
    {
        $field = new LocationPickerField;

        $this->assertEquals('Location Picker', $field->getLabel());
    }

    /** @test */
    public function location_picker_field_renders_with_map()
    {
        $field = new LocationPickerField;
        $formField = new CustomFormField([
            'field_type' => 'location',
            'name' => 'location_field',
            'label' => 'Select Location',
            'options' => [],
        ]);
        $formField->id = 1;

        $html = $field->render($formField);

        $this->assertStringContainsString('location_map_1', $html);
        $this->assertStringContainsString('leaflet@1.9.4', $html);
        $this->assertStringContainsString('L.map', $html);
    }

    /** @test */
    public function location_picker_field_has_config_schema()
    {
        $field = new LocationPickerField;
        $schema = $field->getConfigSchema();

        $this->assertArrayHasKey('default_lat', $schema);
        $this->assertArrayHasKey('default_lng', $schema);
        $this->assertArrayHasKey('default_zoom', $schema);
        $this->assertArrayHasKey('map_height', $schema);
        $this->assertArrayHasKey('enable_search', $schema);
        $this->assertArrayHasKey('show_coordinates', $schema);
        $this->assertEquals('number', $schema['default_lat']['type']);
        $this->assertEquals('switch', $schema['enable_search']['type']);
    }

    /** @test */
    public function location_picker_field_validates_as_json()
    {
        $field = new LocationPickerField;
        $formField = new CustomFormField([
            'validation_rules' => [],
        ]);

        $rules = $field->validate($formField, 'test-value');

        $this->assertContains('json', $rules);
        $this->assertContains('nullable', $rules);
    }

    /** @test */
    public function location_picker_field_processes_json_value()
    {
        $field = new LocationPickerField;
        $value = ['lat' => 37.7749, 'lng' => -122.4194, 'address' => 'San Francisco'];

        $processed = $field->processValue($value);

        $this->assertJson($processed);
        $decoded = json_decode($processed, true);
        $this->assertEquals(37.7749, $decoded['lat']);
        $this->assertEquals(-122.4194, $decoded['lng']);
    }

    /** @test */
    public function rating_matrix_field_has_correct_identifier()
    {
        $field = new RatingMatrixField;

        $this->assertEquals('rating_matrix', $field->getName());
    }

    /** @test */
    public function rating_matrix_field_has_correct_label()
    {
        $field = new RatingMatrixField;

        $this->assertEquals('Rating Matrix', $field->getLabel());
    }

    /** @test */
    public function rating_matrix_field_renders_with_table()
    {
        $field = new RatingMatrixField;
        $formField = new CustomFormField([
            'field_type' => 'rating_matrix',
            'name' => 'matrix_field',
            'label' => 'Rate Items',
            'options' => [
                'rows' => [
                    ['value' => 'item1', 'label' => 'Item 1'],
                ],
                'columns' => [
                    ['value' => '1', 'label' => '1'],
                    ['value' => '2', 'label' => '2'],
                ],
            ],
        ]);
        $formField->id = 1;

        $html = $field->render($formField);

        $this->assertStringContainsString('<table', $html);
        $this->assertStringContainsString('rating-matrix-table', $html);
        $this->assertStringContainsString('Item 1', $html);
    }

    /** @test */
    public function rating_matrix_field_has_config_schema()
    {
        $field = new RatingMatrixField;
        $schema = $field->getConfigSchema();

        $this->assertArrayHasKey('rows', $schema);
        $this->assertArrayHasKey('columns', $schema);
        $this->assertArrayHasKey('input_type', $schema);
        $this->assertArrayHasKey('allow_na', $schema);
        $this->assertEquals('repeater', $schema['rows']['type']);
        $this->assertEquals('select', $schema['input_type']['type']);
        $this->assertEquals('switch', $schema['allow_na']['type']);
    }

    /** @test */
    public function rating_matrix_field_validates_as_json()
    {
        $field = new RatingMatrixField;
        $formField = new CustomFormField([
            'validation_rules' => [],
        ]);

        $rules = $field->validate($formField, 'test-value');

        $this->assertContains('json', $rules);
        $this->assertContains('nullable', $rules);
    }

    /** @test */
    public function rating_matrix_field_processes_json_value()
    {
        $field = new RatingMatrixField;
        $value = ['row1' => '5', 'row2' => '4'];

        $processed = $field->processValue($value);

        $this->assertJson($processed);
        $decoded = json_decode($processed, true);
        $this->assertEquals('5', $decoded['row1']);
        $this->assertEquals('4', $decoded['row2']);
    }

    /** @test */
    public function all_new_fields_extend_base_field_type()
    {
        $signatureField = new SignaturePadField;
        $locationField = new LocationPickerField;
        $matrixField = new RatingMatrixField;

        $this->assertInstanceOf(\DigitalisStudios\SlickForms\FieldTypes\BaseFieldType::class, $signatureField);
        $this->assertInstanceOf(\DigitalisStudios\SlickForms\FieldTypes\BaseFieldType::class, $locationField);
        $this->assertInstanceOf(\DigitalisStudios\SlickForms\FieldTypes\BaseFieldType::class, $matrixField);
    }

    /** @test */
    public function signature_field_renders_builder_preview()
    {
        $field = new SignaturePadField;
        $formField = new CustomFormField([
            'field_type' => 'signature',
            'name' => 'signature_field',
            'label' => 'Sign Here',
            'options' => ['canvas_width' => 500, 'canvas_height' => 200],
        ]);
        $formField->id = 1;

        $html = $field->renderBuilder($formField);

        $this->assertStringContainsString('500', $html);
        $this->assertStringContainsString('200', $html);
        $this->assertStringContainsString('Signature pad area', $html);
    }

    /** @test */
    public function location_field_renders_builder_with_interactive_map()
    {
        $field = new LocationPickerField;
        $formField = new CustomFormField([
            'field_type' => 'location',
            'name' => 'location_field',
            'label' => 'Select Location',
            'options' => [],
        ]);
        $formField->id = 1;

        $html = $field->renderBuilder($formField);

        $this->assertStringContainsString('location_map_builder_1', $html);
        $this->assertStringContainsString('leaflet@1.9.4', $html);
    }

    /** @test */
    public function rating_matrix_field_renders_builder_preview()
    {
        $field = new RatingMatrixField;
        $formField = new CustomFormField([
            'field_type' => 'rating_matrix',
            'name' => 'matrix_field',
            'label' => 'Rate Items',
            'options' => [
                'rows' => [
                    ['value' => 'item1', 'label' => 'Item 1'],
                ],
                'columns' => [
                    ['value' => '1', 'label' => '1'],
                ],
            ],
        ]);
        $formField->id = 1;

        $html = $field->renderBuilder($formField);

        $this->assertStringContainsString('rating-matrix-preview', $html);
        $this->assertStringContainsString('Item 1', $html);
    }
}
