<?php

namespace DigitalisStudios\SlickForms\Tests\Unit;

use DigitalisStudios\SlickForms\FieldTypes\EmailField;
use DigitalisStudios\SlickForms\FieldTypes\PasswordField;
use DigitalisStudios\SlickForms\FieldTypes\TextField;
use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Tests\TestCase;

/**
 * Test custom validation messages for all field types
 */
class ValidationMessagesTest extends TestCase
{
    /** @test */
    public function base_field_type_has_validation_message_config_schema()
    {
        $field = new TextField;
        $schema = $field->getConfigSchema();

        $this->assertArrayHasKey('custom_invalid_feedback', $schema);
        $this->assertArrayHasKey('custom_valid_feedback', $schema);
        $this->assertEquals('text', $schema['custom_invalid_feedback']['type']);
        $this->assertEquals('text', $schema['custom_valid_feedback']['type']);
    }

    /** @test */
    public function text_field_renders_custom_invalid_feedback()
    {
        $field = new TextField;
        $formField = new CustomFormField([
            'label' => 'Username',
            'name' => 'username',
            'field_type' => 'text',
            'options' => [
                'custom_invalid_feedback' => 'Username must be at least 5 characters',
            ],
        ]);

        $html = $field->render($formField);

        $this->assertStringContainsString('invalid-feedback', $html);
        $this->assertStringContainsString('Username must be at least 5 characters', $html);
    }

    /** @test */
    public function text_field_renders_custom_valid_feedback()
    {
        $field = new TextField;
        $formField = new CustomFormField([
            'label' => 'Username',
            'name' => 'username',
            'field_type' => 'text',
            'options' => [
                'custom_valid_feedback' => 'Username is available!',
            ],
        ]);

        $html = $field->render($formField);

        $this->assertStringContainsString('valid-feedback', $html);
        $this->assertStringContainsString('Username is available!', $html);
    }

    /** @test */
    public function email_field_renders_validation_feedback()
    {
        $field = new EmailField;
        $formField = new CustomFormField([
            'label' => 'Email',
            'name' => 'email',
            'field_type' => 'email',
            'options' => [
                'custom_invalid_feedback' => 'Please enter a valid email address',
            ],
        ]);

        $html = $field->render($formField);

        $this->assertStringContainsString('invalid-feedback', $html);
        $this->assertStringContainsString('Please enter a valid email address', $html);
    }

    /** @test */
    public function password_field_renders_validation_feedback()
    {
        $field = new PasswordField;
        $formField = new CustomFormField([
            'label' => 'Password',
            'name' => 'password',
            'field_type' => 'password',
            'options' => [
                'custom_invalid_feedback' => 'Password must contain at least 8 characters, one uppercase, one lowercase, and one number',
                'custom_valid_feedback' => 'Strong password!',
            ],
        ]);

        $html = $field->render($formField);

        $this->assertStringContainsString('invalid-feedback', $html);
        $this->assertStringContainsString('Password must contain at least 8 characters', $html);
        $this->assertStringContainsString('valid-feedback', $html);
        $this->assertStringContainsString('Strong password!', $html);
    }

    /** @test */
    public function field_without_custom_messages_renders_default_feedback()
    {
        $field = new TextField;
        $formField = new CustomFormField([
            'label' => 'Name',
            'name' => 'name',
            'field_type' => 'text',
        ]);

        $html = $field->render($formField);

        $this->assertStringContainsString('invalid-feedback', $html);
        $this->assertStringContainsString('Please provide a valid value', $html);
    }

    /** @test */
    public function validation_feedback_escapes_html_to_prevent_xss()
    {
        $field = new TextField;
        $formField = new CustomFormField([
            'label' => 'Test',
            'name' => 'test',
            'field_type' => 'text',
            'options' => [
                'custom_invalid_feedback' => '<script>alert("XSS")</script>Malicious',
            ],
        ]);

        $html = $field->render($formField);

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    /** @test */
    public function all_26_field_types_support_validation_feedback()
    {
        $registry = app(\DigitalisStudios\SlickForms\Services\FieldTypeRegistry::class);
        $allFieldTypes = $registry->all();

        // Fields that don't need validation feedback (display-only, hidden, file uploads, or require Livewire context)
        $skipFields = ['hidden', 'header', 'paragraph', 'code', 'image', 'video', 'repeater', 'pdf_embed'];

        foreach ($allFieldTypes as $fieldType) {
            // Skip display-only and hidden fields before rendering
            if (in_array($fieldType->getName(), $skipFields)) {
                continue;
            }

            $formField = new CustomFormField([
                'label' => 'Test Field',
                'name' => 'test_field',
                'field_type' => $fieldType->getName(),
                'options' => [
                    'custom_invalid_feedback' => 'Custom error message',
                ],
            ]);

            $html = $fieldType->render($formField);

            $this->assertStringContainsString(
                'invalid-feedback',
                $html,
                "Field type {$fieldType->getName()} should render invalid-feedback"
            );
        }
    }
}
