<?php

namespace DigitalisStudios\SlickForms\Tests\Unit;

use DigitalisStudios\SlickForms\FieldTypes\PasswordField;
use DigitalisStudios\SlickForms\FieldTypes\PhoneField;
use DigitalisStudios\SlickForms\FieldTypes\RangeField;
use DigitalisStudios\SlickForms\FieldTypes\SwitchField;
use DigitalisStudios\SlickForms\FieldTypes\TimeField;
use DigitalisStudios\SlickForms\FieldTypes\UrlField;
use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Tests\TestCase;

/**
 * Test all 6 additional field types (Switch, Range, Phone, URL, Time, Password)
 */
class NewFieldTypesTest extends TestCase
{
    /** @test */
    public function switch_field_exists_and_has_correct_properties()
    {
        $field = new SwitchField;

        $this->assertEquals('switch', $field->getName());
        $this->assertEquals('Switch/Toggle', $field->getLabel());
        $this->assertStringContainsString('bi', $field->getIcon());
    }

    /** @test */
    public function switch_field_renders_bootstrap_switch()
    {
        $field = new SwitchField;
        $formField = new CustomFormField([
            'label' => 'Enable Notifications',
            'name' => 'notifications',
            'field_type' => 'switch',
        ]);

        $html = $field->render($formField);

        $this->assertStringContainsString('form-check-input', $html);
        $this->assertStringContainsString('form-switch', $html);
        $this->assertStringContainsString('Enable Notifications', $html);
    }

    /** @test */
    public function range_field_exists_and_has_correct_properties()
    {
        $field = new RangeField;

        $this->assertEquals('range', $field->getName());
        $this->assertEquals('Range (Min-Max)', $field->getLabel());
        $this->assertStringContainsString('bi', $field->getIcon());
    }

    /** @test */
    public function range_field_renders_dual_slider()
    {
        $field = new RangeField;
        $formField = new CustomFormField([
            'label' => 'Price Range',
            'name' => 'price_range',
            'field_type' => 'range',
            'options' => [
                'min' => 0,
                'max' => 1000,
            ],
        ]);

        $html = $field->render($formField);

        $this->assertStringContainsString('Price Range', $html);
        $this->assertStringContainsString('form-range', $html);
    }

    /** @test */
    public function phone_field_exists_and_has_correct_properties()
    {
        $field = new PhoneField;

        $this->assertEquals('phone', $field->getName());
        $this->assertEquals('Phone Number', $field->getLabel());
        $this->assertStringContainsString('bi', $field->getIcon());
    }

    /** @test */
    public function url_field_exists_and_has_correct_properties()
    {
        $field = new UrlField;

        $this->assertEquals('url', $field->getName());
        $this->assertEquals('URL', $field->getLabel());
        $this->assertStringContainsString('bi', $field->getIcon());
    }

    /** @test */
    public function url_field_renders_with_preview_button()
    {
        $field = new UrlField;
        $formField = new CustomFormField([
            'label' => 'Website',
            'name' => 'website',
            'field_type' => 'url',
        ]);

        $html = $field->render($formField);

        $this->assertStringContainsString('Website', $html);
        $this->assertStringContainsString('type="url"', $html);
    }

    /** @test */
    public function time_field_exists_and_has_correct_properties()
    {
        $field = new TimeField;

        $this->assertEquals('time', $field->getName());
        $this->assertEquals('Time', $field->getLabel());
        $this->assertStringContainsString('bi', $field->getIcon());
    }

    /** @test */
    public function time_field_renders_time_input()
    {
        $field = new TimeField;
        $formField = new CustomFormField([
            'label' => 'Appointment Time',
            'name' => 'appointment_time',
            'field_type' => 'time',
        ]);

        $html = $field->render($formField);

        $this->assertStringContainsString('Appointment Time', $html);
        $this->assertStringContainsString('type="time"', $html);
    }

    /** @test */
    public function password_field_exists_and_has_correct_properties()
    {
        $field = new PasswordField;

        $this->assertEquals('password', $field->getName());
        $this->assertEquals('Password', $field->getLabel());
        $this->assertStringContainsString('bi', $field->getIcon());
    }

    /** @test */
    public function password_field_renders_with_toggle_and_strength_indicator()
    {
        $field = new PasswordField;
        $formField = new CustomFormField([
            'label' => 'Password',
            'name' => 'password',
            'field_type' => 'password',
        ]);

        $html = $field->render($formField);

        $this->assertStringContainsString('Password', $html);
        $this->assertStringContainsString(':type="showPassword', $html); // Alpine.js dynamic type
        $this->assertStringContainsString('strength', $html);
    }

    /** @test */
    public function all_six_new_field_types_are_registered()
    {
        $registry = app(\DigitalisStudios\SlickForms\Services\FieldTypeRegistry::class);

        $this->assertTrue($registry->has('switch'));
        $this->assertTrue($registry->has('range'));
        $this->assertTrue($registry->has('phone'));
        $this->assertTrue($registry->has('url'));
        $this->assertTrue($registry->has('time'));
        $this->assertTrue($registry->has('password'));
    }

    /** @test */
    public function total_field_types_is_32()
    {
        $registry = app(\DigitalisStudios\SlickForms\Services\FieldTypeRegistry::class);
        $allFields = $registry->all();

        $this->assertCount(32, $allFields, 'Should have 32 total field types (28 v1 + signature + location + rating_matrix + pdf_embed)');
    }
}
