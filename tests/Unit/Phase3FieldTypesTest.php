<?php

namespace DigitalisStudios\SlickForms\Tests\Unit;

use DigitalisStudios\SlickForms\FieldTypes\CheckboxField;
use DigitalisStudios\SlickForms\FieldTypes\RadioField;
use DigitalisStudios\SlickForms\FieldTypes\SelectField;
use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Tests\Support\User;
use DigitalisStudios\SlickForms\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

class Phase3FieldTypesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function select_field_supports_static_options(): void
    {
        $field = CustomFormField::factory()->create([
            'field_type' => 'select',
            'name' => 'country',
            'options' => [
                'option_source' => 'static',
                'values' => [
                    ['value' => 'us', 'label' => 'United States'],
                    ['value' => 'uk', 'label' => 'United Kingdom'],
                ],
            ],
        ]);

        $fieldType = new SelectField;
        $html = $fieldType->render($field);

        $this->assertStringContainsString('United States', $html);
        $this->assertStringContainsString('United Kingdom', $html);
        $this->assertStringContainsString('value="us"', $html);
    }

    /** @test */
    public function select_field_supports_url_based_options(): void
    {
        Http::fake([
            'https://api.example.com/countries' => Http::response([
                ['value' => 'us', 'label' => 'United States'],
                ['value' => 'ca', 'label' => 'Canada'],
            ], 200),
        ]);

        $field = CustomFormField::factory()->create([
            'field_type' => 'select',
            'name' => 'country',
            'options' => [
                'option_source' => 'url',
                'source_url' => 'https://api.example.com/countries',
                'value_key' => 'value',
                'label_key' => 'label',
            ],
        ]);

        $fieldType = new SelectField;
        $html = $fieldType->render($field);

        $this->assertStringContainsString('United States', $html);
        $this->assertStringContainsString('Canada', $html);
    }

    /** @test */
    public function select_field_supports_model_based_options(): void
    {
        // Create test users
        User::factory()->create(['name' => 'Alice', 'email' => 'alice@example.com']);
        User::factory()->create(['name' => 'Bob', 'email' => 'bob@example.com']);

        $field = CustomFormField::factory()->create([
            'field_type' => 'select',
            'name' => 'user',
            'options' => [
                'option_source' => 'model',
                'model_class' => User::class,
                'value_column' => 'id',
                'label_column' => 'name',
            ],
        ]);

        $fieldType = new SelectField;
        $html = $fieldType->render($field);

        $this->assertStringContainsString('Alice', $html);
        $this->assertStringContainsString('Bob', $html);
    }

    /** @test */
    public function radio_field_supports_static_options(): void
    {
        $field = CustomFormField::factory()->create([
            'field_type' => 'radio',
            'name' => 'gender',
            'options' => [
                'option_source' => 'static',
                'values' => [
                    ['value' => 'male', 'label' => 'Male'],
                    ['value' => 'female', 'label' => 'Female'],
                ],
            ],
        ]);

        $fieldType = new RadioField;
        $html = $fieldType->render($field);

        $this->assertStringContainsString('Male', $html);
        $this->assertStringContainsString('Female', $html);
        $this->assertStringContainsString('type="radio"', $html);
    }

    /** @test */
    public function radio_field_supports_url_based_options(): void
    {
        Http::fake([
            'https://api.example.com/options' => Http::response([
                ['value' => '1', 'label' => 'Option 1'],
                ['value' => '2', 'label' => 'Option 2'],
            ], 200),
        ]);

        $field = CustomFormField::factory()->create([
            'field_type' => 'radio',
            'name' => 'choice',
            'options' => [
                'option_source' => 'url',
                'source_url' => 'https://api.example.com/options',
                'value_key' => 'value',
                'label_key' => 'label',
            ],
        ]);

        $fieldType = new RadioField;
        $html = $fieldType->render($field);

        $this->assertStringContainsString('Option 1', $html);
        $this->assertStringContainsString('Option 2', $html);
    }

    /** @test */
    public function radio_field_supports_model_based_options(): void
    {
        User::factory()->create(['name' => 'Alice']);
        User::factory()->create(['name' => 'Bob']);

        $field = CustomFormField::factory()->create([
            'field_type' => 'radio',
            'name' => 'user',
            'options' => [
                'option_source' => 'model',
                'model_class' => User::class,
                'value_column' => 'id',
                'label_column' => 'name',
            ],
        ]);

        $fieldType = new RadioField;
        $html = $fieldType->render($field);

        $this->assertStringContainsString('Alice', $html);
        $this->assertStringContainsString('Bob', $html);
    }

    /** @test */
    public function checkbox_field_supports_static_options(): void
    {
        $field = CustomFormField::factory()->create([
            'field_type' => 'checkbox',
            'name' => 'interests',
            'options' => [
                'option_source' => 'static',
                'values' => [
                    ['value' => 'sports', 'label' => 'Sports'],
                    ['value' => 'music', 'label' => 'Music'],
                ],
            ],
        ]);

        $fieldType = new CheckboxField;
        $html = $fieldType->render($field);

        $this->assertStringContainsString('Sports', $html);
        $this->assertStringContainsString('Music', $html);
        $this->assertStringContainsString('type="checkbox"', $html);
    }

    /** @test */
    public function checkbox_field_supports_url_based_options(): void
    {
        Http::fake([
            'https://api.example.com/tags' => Http::response([
                ['value' => 'tag1', 'label' => 'Tag 1'],
                ['value' => 'tag2', 'label' => 'Tag 2'],
            ], 200),
        ]);

        $field = CustomFormField::factory()->create([
            'field_type' => 'checkbox',
            'name' => 'tags',
            'options' => [
                'option_source' => 'url',
                'source_url' => 'https://api.example.com/tags',
                'value_key' => 'value',
                'label_key' => 'label',
            ],
        ]);

        $fieldType = new CheckboxField;
        $html = $fieldType->render($field);

        $this->assertStringContainsString('Tag 1', $html);
        $this->assertStringContainsString('Tag 2', $html);
    }

    /** @test */
    public function checkbox_field_supports_model_based_options(): void
    {
        User::factory()->create(['name' => 'Alice']);
        User::factory()->create(['name' => 'Bob']);

        $field = CustomFormField::factory()->create([
            'field_type' => 'checkbox',
            'name' => 'users',
            'options' => [
                'option_source' => 'model',
                'model_class' => User::class,
                'value_column' => 'id',
                'label_column' => 'name',
            ],
        ]);

        $fieldType = new CheckboxField;
        $html = $fieldType->render($field);

        $this->assertStringContainsString('Alice', $html);
        $this->assertStringContainsString('Bob', $html);
    }

    /** @test */
    public function select_field_renders_correctly_in_builder(): void
    {
        $field = CustomFormField::factory()->create([
            'field_type' => 'select',
            'name' => 'test',
            'label' => 'Test Field',
            'options' => [
                'option_source' => 'static',
                'values' => [['value' => '1', 'label' => 'Option 1']],
            ],
        ]);

        $fieldType = new SelectField;
        $html = $fieldType->renderBuilder($field);

        $this->assertStringContainsString('Test Field', $html);
        $this->assertStringContainsString('Option 1', $html);
    }

    /** @test */
    public function field_types_have_get_options_for_field_method(): void
    {
        $field = CustomFormField::factory()->create([
            'options' => [
                'option_source' => 'static',
                'values' => [['value' => '1', 'label' => 'Test']],
            ],
        ]);

        $selectField = new SelectField;
        $radioField = new RadioField;
        $checkboxField = new CheckboxField;

        $this->assertTrue(method_exists($selectField, 'getOptionsForField'));
        $this->assertTrue(method_exists($radioField, 'getOptionsForField'));
        $this->assertTrue(method_exists($checkboxField, 'getOptionsForField'));
    }

    /** @test */
    public function field_types_fallback_to_empty_array_for_missing_options(): void
    {
        $field = CustomFormField::factory()->create([
            'field_type' => 'select',
            'name' => 'test',
            'options' => [], // No options configured
        ]);

        $fieldType = new SelectField;
        $html = $fieldType->render($field);

        // Should not throw error, just render empty select
        $this->assertStringContainsString('<select', $html);
    }

    /** @test */
    public function field_types_handle_url_fetch_failures_gracefully(): void
    {
        Http::fake([
            'https://api.example.com/fail' => Http::response(null, 500),
        ]);

        $field = CustomFormField::factory()->create([
            'field_type' => 'select',
            'name' => 'test',
            'options' => [
                'option_source' => 'url',
                'source_url' => 'https://api.example.com/fail',
            ],
        ]);

        $fieldType = new SelectField;
        $html = $fieldType->render($field);

        // Should not throw error, just render empty select
        $this->assertStringContainsString('<select', $html);
    }
}
