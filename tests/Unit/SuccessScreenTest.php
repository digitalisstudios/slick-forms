<?php

namespace DigitalisStudios\SlickForms\Tests\Unit;

use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Models\CustomFormSubmission;
use DigitalisStudios\SlickForms\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class SuccessScreenTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_renders_success_message_with_default_settings(): void
    {
        $form = CustomForm::factory()->create([
            'name' => 'Test Form',
            'settings' => [],
        ]);

        $submission = CustomFormSubmission::factory()->create([
            'slick_form_id' => $form->id,
        ]);

        $component = Livewire::test(\DigitalisStudios\SlickForms\Livewire\FormRenderer::class, ['formId' => $form->id])
            ->set('submitted', true)
            ->set('lastSubmission', $submission);

        $component->assertSee('Thank you');
        $component->assertSee('Your submission has been received');
    }

    /** @test */
    public function it_renders_custom_success_message(): void
    {
        $form = CustomForm::factory()->create([
            'name' => 'Test Form',
            'settings' => [
                'success_screen' => [
                    'action_type' => 'message',
                    'message_title' => 'Custom Thank You!',
                    'message_body' => 'Your custom message here.',
                ],
            ],
        ]);

        $submission = CustomFormSubmission::factory()->create([
            'slick_form_id' => $form->id,
        ]);

        $component = Livewire::test(\DigitalisStudios\SlickForms\Livewire\FormRenderer::class, ['formId' => $form->id])
            ->set('submitted', true)
            ->set('lastSubmission', $submission);

        $component->assertSee('Custom Thank You!');
        $component->assertSee('Your custom message here.');
    }

    /** @test */
    public function it_replaces_submission_variables_in_message(): void
    {
        $form = CustomForm::factory()->create([
            'name' => 'Test Form',
            'settings' => [
                'success_screen' => [
                    'action_type' => 'message',
                    'message_title' => 'Thank You',
                    'message_body' => 'Your submission ID is: {{submission.id}}',
                ],
            ],
        ]);

        $submission = CustomFormSubmission::factory()->create([
            'slick_form_id' => $form->id,
        ]);

        $component = Livewire::test(\DigitalisStudios\SlickForms\Livewire\FormRenderer::class, ['formId' => $form->id])
            ->set('submitted', true)
            ->set('lastSubmission', $submission);

        $component->assertSee("Your submission ID is: {$submission->id}");
    }

    /** @test */
    public function it_replaces_field_variables_in_message(): void
    {
        $form = CustomForm::factory()->create([
            'name' => 'Test Form',
            'settings' => [
                'success_screen' => [
                    'action_type' => 'message',
                    'message_title' => 'Thank You',
                    'message_body' => 'Hello {{name}}!',
                ],
            ],
        ]);

        $field = CustomFormField::factory()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'name',
            'label' => 'Name',
        ]);

        $submission = CustomFormSubmission::factory()->create([
            'slick_form_id' => $form->id,
        ]);

        // Create field value
        \DigitalisStudios\SlickForms\Models\CustomFormFieldValue::create([
            'slick_form_submission_id' => $submission->id,
            'slick_form_field_id' => $field->id,
            'value' => 'John Doe',
        ]);

        $component = Livewire::test(\DigitalisStudios\SlickForms\Livewire\FormRenderer::class, ['formId' => $form->id])
            ->set('submitted', true)
            ->set('lastSubmission', $submission);

        $component->assertSee('Hello John Doe!');
    }

    /** @test */
    public function it_evaluates_conditional_redirect_rules(): void
    {
        $form = CustomForm::factory()->create([
            'name' => 'Test Form',
            'settings' => [
                'success_screen' => [
                    'action_type' => 'redirect',
                    'redirect_url' => 'https://default.com',
                    'conditional_redirects' => [
                        [
                            'url' => 'https://premium.com',
                            'priority' => 1,
                            'conditions' => [
                                [
                                    'target_field' => 'plan',
                                    'operator' => 'equals',
                                    'value' => 'premium',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $field = CustomFormField::factory()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'select',
            'name' => 'plan',
            'label' => 'Plan',
        ]);

        $submission = CustomFormSubmission::factory()->create([
            'slick_form_id' => $form->id,
        ]);

        \DigitalisStudios\SlickForms\Models\CustomFormFieldValue::create([
            'slick_form_submission_id' => $submission->id,
            'slick_form_field_id' => $field->id,
            'value' => 'premium',
        ]);

        $component = Livewire::test(\DigitalisStudios\SlickForms\Livewire\FormRenderer::class, ['formId' => $form->id])
            ->set('submitted', true)
            ->set('lastSubmission', $submission);

        // Manually call determineSuccessAction and check result
        $formRenderer = $component->instance();
        $action = $formRenderer->determineSuccessAction($submission);

        $this->assertEquals('redirect', $action['type']);
        $this->assertEquals('https://premium.com', $action['url']);
    }

    /** @test */
    public function it_falls_back_to_default_redirect_when_conditions_not_met(): void
    {
        $form = CustomForm::factory()->create([
            'name' => 'Test Form',
            'settings' => [
                'success_screen' => [
                    'action_type' => 'redirect',
                    'redirect_url' => 'https://default.com',
                    'conditional_redirects' => [
                        [
                            'url' => 'https://premium.com',
                            'priority' => 1,
                            'conditions' => [
                                [
                                    'target_field' => 'plan',
                                    'operator' => 'equals',
                                    'value' => 'premium',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $field = CustomFormField::factory()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'select',
            'name' => 'plan',
            'label' => 'Plan',
        ]);

        $submission = CustomFormSubmission::factory()->create([
            'slick_form_id' => $form->id,
        ]);

        \DigitalisStudios\SlickForms\Models\CustomFormFieldValue::create([
            'slick_form_submission_id' => $submission->id,
            'slick_form_field_id' => $field->id,
            'value' => 'basic', // Not premium
        ]);

        $component = Livewire::test(\DigitalisStudios\SlickForms\Livewire\FormRenderer::class, ['formId' => $form->id])
            ->set('submitted', true)
            ->set('lastSubmission', $submission);

        // Manually call determineSuccessAction and check result
        $formRenderer = $component->instance();
        $action = $formRenderer->determineSuccessAction($submission);

        $this->assertEquals('redirect', $action['type']);
        $this->assertEquals('https://default.com', $action['url']);
    }

    /** @test */
    public function it_shows_submission_data_when_enabled(): void
    {
        $form = CustomForm::factory()->create([
            'name' => 'Test Form',
            'settings' => [
                'success_screen' => [
                    'action_type' => 'message',
                    'show_submission_data' => true,
                ],
            ],
        ]);

        $field = CustomFormField::factory()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'name',
            'label' => 'Name',
        ]);

        $submission = CustomFormSubmission::factory()->create([
            'slick_form_id' => $form->id,
        ]);

        \DigitalisStudios\SlickForms\Models\CustomFormFieldValue::create([
            'slick_form_submission_id' => $submission->id,
            'slick_form_field_id' => $field->id,
            'value' => 'John Doe',
        ]);

        $component = Livewire::test(\DigitalisStudios\SlickForms\Livewire\FormRenderer::class, ['formId' => $form->id])
            ->set('submitted', true)
            ->set('lastSubmission', $submission);

        $component->assertSee('Name');
        $component->assertSee('John Doe');
    }

    /** @test */
    public function it_hides_sensitive_fields_in_submission_data(): void
    {
        $form = CustomForm::factory()->create([
            'name' => 'Test Form',
            'settings' => [
                'success_screen' => [
                    'action_type' => 'message',
                    'show_submission_data' => true,
                    'hidden_fields' => ['password'],
                ],
            ],
        ]);

        $passwordField = CustomFormField::factory()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'password',
            'name' => 'password',
            'label' => 'Password',
        ]);

        $nameField = CustomFormField::factory()->create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'name',
            'label' => 'Name',
        ]);

        $submission = CustomFormSubmission::factory()->create([
            'slick_form_id' => $form->id,
        ]);

        \DigitalisStudios\SlickForms\Models\CustomFormFieldValue::create([
            'slick_form_submission_id' => $submission->id,
            'slick_form_field_id' => $passwordField->id,
            'value' => 'secret123',
        ]);

        \DigitalisStudios\SlickForms\Models\CustomFormFieldValue::create([
            'slick_form_submission_id' => $submission->id,
            'slick_form_field_id' => $nameField->id,
            'value' => 'John Doe',
        ]);

        $component = Livewire::test(\DigitalisStudios\SlickForms\Livewire\FormRenderer::class, ['formId' => $form->id])
            ->set('submitted', true)
            ->set('lastSubmission', $submission);

        $component->assertSee('Name');
        $component->assertSee('John Doe');
        $component->assertDontSee('secret123');
        $component->assertDontSee('Password');
    }

    /** @test */
    public function it_shows_download_buttons_when_enabled(): void
    {
        $form = CustomForm::factory()->create([
            'name' => 'Test Form',
            'settings' => [
                'success_screen' => [
                    'action_type' => 'message',
                    'enable_pdf_download' => true,
                    'pdf_button_text' => 'Download PDF',
                    'enable_csv_download' => true,
                    'csv_button_text' => 'Download CSV',
                ],
            ],
        ]);

        $submission = CustomFormSubmission::factory()->create([
            'slick_form_id' => $form->id,
        ]);

        $component = Livewire::test(\DigitalisStudios\SlickForms\Livewire\FormRenderer::class, ['formId' => $form->id])
            ->set('submitted', true)
            ->set('lastSubmission', $submission);

        $component->assertSee('Download PDF');
        $component->assertSee('Download CSV');
    }

    /** @test */
    public function it_shows_edit_submission_link_when_enabled(): void
    {
        $form = CustomForm::factory()->create([
            'name' => 'Test Form',
            'settings' => [
                'success_screen' => [
                    'action_type' => 'message',
                    'enable_edit_link' => true,
                    'edit_link_text' => 'Edit Your Submission',
                ],
            ],
        ]);

        $submission = CustomFormSubmission::factory()->create([
            'slick_form_id' => $form->id,
        ]);

        $component = Livewire::test(\DigitalisStudios\SlickForms\Livewire\FormRenderer::class, ['formId' => $form->id])
            ->set('submitted', true)
            ->set('lastSubmission', $submission);

        $component->assertSee('Edit Your Submission');
    }

    /** @test */
    public function it_adds_submission_id_to_redirect_url_when_enabled(): void
    {
        $form = CustomForm::factory()->create([
            'name' => 'Test Form',
            'settings' => [
                'success_screen' => [
                    'action_type' => 'redirect',
                    'redirect_url' => 'https://example.com/thank-you',
                    'pass_submission_id' => true,
                ],
            ],
        ]);

        $submission = CustomFormSubmission::factory()->create([
            'slick_form_id' => $form->id,
        ]);

        $component = Livewire::test(\DigitalisStudios\SlickForms\Livewire\FormRenderer::class, ['formId' => $form->id])
            ->set('submitted', true)
            ->set('lastSubmission', $submission);

        // Manually call determineSuccessAction and check result
        $formRenderer = $component->instance();
        $action = $formRenderer->determineSuccessAction($submission);

        $this->assertEquals('redirect', $action['type']);
        $this->assertEquals("https://example.com/thank-you?submission_id={$submission->id}", $action['url']);
    }

    /** @test */
    public function it_handles_message_then_redirect_action_type(): void
    {
        $form = CustomForm::factory()->create([
            'name' => 'Test Form',
            'settings' => [
                'success_screen' => [
                    'action_type' => 'message_then_redirect',
                    'message_title' => 'Processing...',
                    'message_body' => 'You will be redirected shortly.',
                    'redirect_url' => 'https://example.com',
                    'redirect_delay' => 5,
                ],
            ],
        ]);

        $submission = CustomFormSubmission::factory()->create([
            'slick_form_id' => $form->id,
        ]);

        $component = Livewire::test(\DigitalisStudios\SlickForms\Livewire\FormRenderer::class, ['formId' => $form->id])
            ->set('submitted', true)
            ->set('lastSubmission', $submission);

        $component->assertSee('Processing...');
        $component->assertSee('You will be redirected shortly.');
        $component->assertSee('https://example.com');
    }
}
