<?php

namespace DigitalisStudios\SlickForms\Tests\Unit;

use DigitalisStudios\SlickForms\Events\EmailNotificationSending;
use DigitalisStudios\SlickForms\Events\EmailNotificationSent;
use DigitalisStudios\SlickForms\Jobs\SendEmailNotification;
use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Models\CustomFormFieldValue;
use DigitalisStudios\SlickForms\Models\CustomFormSubmission;
use DigitalisStudios\SlickForms\Models\FormEmailLog;
use DigitalisStudios\SlickForms\Models\FormEmailTemplate;
use DigitalisStudios\SlickForms\Services\EmailNotificationService;
use DigitalisStudios\SlickForms\Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

class EmailNotificationServiceTest extends TestCase
{
    protected EmailNotificationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(EmailNotificationService::class);
    }

    /** @test */
    public function it_sends_admin_notification_immediately_when_queue_disabled()
    {
        config(['slick-forms.email.queue' => false]);
        Mail::fake();
        Event::fake();

        $form = $this->createFormWithEmailTemplate('admin');
        $submission = $this->createSubmission($form);

        $this->service->sendAdminNotification($submission);

        Event::assertDispatched(EmailNotificationSending::class);
        Event::assertDispatched(EmailNotificationSent::class);

        $this->assertDatabaseHas('slick_form_email_logs', [
            'submission_id' => $submission->id,
            'status' => 'sent',
        ]);
    }

    /** @test */
    public function it_queues_admin_notification_when_queue_enabled()
    {
        config(['slick-forms.email.queue' => true]);
        Queue::fake();

        $form = $this->createFormWithEmailTemplate('admin');
        $submission = $this->createSubmission($form);

        $this->service->sendAdminNotification($submission);

        Queue::assertPushed(SendEmailNotification::class);

        $this->assertDatabaseHas('slick_form_email_logs', [
            'submission_id' => $submission->id,
            'status' => 'queued',
        ]);
    }

    /** @test */
    public function it_sends_user_confirmation_email()
    {
        config(['slick-forms.email.queue' => false]);
        Mail::fake();

        $form = $this->createFormWithEmailTemplate('user_confirmation');
        $submission = $this->createSubmissionWithEmail($form, 'user@example.com');

        $this->service->sendUserConfirmation($submission);

        $this->assertDatabaseHas('slick_form_email_logs', [
            'submission_id' => $submission->id,
            'to' => 'user@example.com',
            'status' => 'sent',
        ]);
    }

    /** @test */
    public function it_evaluates_conditional_rules_correctly()
    {
        $form = CustomForm::create(['name' => 'Test Form', 'is_active' => true]);

        $emailField = CustomFormField::create([
            'slick_form_id' => $form->id,
            'name' => 'email',
            'field_type' => 'email',
            'label' => 'Email',
        ]);

        $statusField = CustomFormField::create([
            'slick_form_id' => $form->id,
            'name' => 'status',
            'field_type' => 'select',
            'label' => 'Status',
        ]);

        $template = FormEmailTemplate::create([
            'form_id' => $form->id,
            'type' => 'admin',
            'enabled' => true,
            'recipients' => ['admin@example.com'],
            'subject' => 'Test',
            'body_template' => 'Test body',
            'conditional_rules' => [
                ['field' => 'status', 'operator' => 'equals', 'value' => 'approved'],
            ],
        ]);

        $submission = CustomFormSubmission::create([
            'slick_form_id' => $form->id,
            'ip_address' => '127.0.0.1',
            'submitted_at' => now(),
        ]);

        CustomFormFieldValue::create([
            'slick_form_submission_id' => $submission->id,
            'slick_form_field_id' => $statusField->id,
            'value' => 'approved',
        ]);

        $result = $this->service->evaluateConditionalRules($template, $submission);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_fails_conditional_rules_when_condition_not_met()
    {
        $form = CustomForm::create(['name' => 'Test Form', 'is_active' => true]);

        $statusField = CustomFormField::create([
            'slick_form_id' => $form->id,
            'name' => 'status',
            'field_type' => 'select',
            'label' => 'Status',
        ]);

        $template = FormEmailTemplate::create([
            'form_id' => $form->id,
            'type' => 'admin',
            'enabled' => true,
            'recipients' => ['admin@example.com'],
            'subject' => 'Test',
            'body_template' => 'Test body',
            'conditional_rules' => [
                ['field' => 'status', 'operator' => 'equals', 'value' => 'approved'],
            ],
        ]);

        $submission = CustomFormSubmission::create([
            'slick_form_id' => $form->id,
            'ip_address' => '127.0.0.1',
            'submitted_at' => now(),
        ]);

        CustomFormFieldValue::create([
            'slick_form_submission_id' => $submission->id,
            'slick_form_field_id' => $statusField->id,
            'value' => 'pending',
        ]);

        $result = $this->service->evaluateConditionalRules($template, $submission);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_renders_email_template_with_submission_data()
    {
        $form = CustomForm::create(['name' => 'Contact Form', 'is_active' => true]);

        $nameField = CustomFormField::create([
            'slick_form_id' => $form->id,
            'name' => 'name',
            'field_type' => 'text',
            'label' => 'Name',
        ]);

        $template = FormEmailTemplate::create([
            'form_id' => $form->id,
            'type' => 'admin',
            'enabled' => true,
            'recipients' => ['admin@example.com'],
            'subject' => 'New submission',
            'body_template' => 'Hello, {{ $form->name }}',
        ]);

        $submission = CustomFormSubmission::create([
            'slick_form_id' => $form->id,
            'ip_address' => '127.0.0.1',
            'submitted_at' => now(),
        ]);

        CustomFormFieldValue::create([
            'slick_form_submission_id' => $submission->id,
            'slick_form_field_id' => $nameField->id,
            'value' => 'John Doe',
        ]);

        $rendered = $this->service->renderEmailTemplate($template, $submission);

        $this->assertStringContainsString('Contact Form', $rendered);
    }

    /** @test */
    public function it_logs_email_delivery_attempts()
    {
        $form = $this->createFormWithEmailTemplate('admin');
        $submission = $this->createSubmission($form);
        $template = $form->fresh()->load('emailTemplates')->emailTemplates->first();

        $this->service->logEmailDelivery($submission, $template, 'sent');

        $this->assertDatabaseHas('slick_form_email_logs', [
            'submission_id' => $submission->id,
            'template_id' => $template->id,
            'status' => 'sent',
        ]);
    }

    /** @test */
    public function it_logs_failed_email_with_error_message()
    {
        $form = $this->createFormWithEmailTemplate('admin');
        $submission = $this->createSubmission($form);
        $template = $form->fresh()->load('emailTemplates')->emailTemplates->first();

        $this->service->logEmailDelivery($submission, $template, 'failed', 'SMTP connection error');

        $log = FormEmailLog::where('submission_id', $submission->id)->first();

        $this->assertEquals('failed', $log->status);
        $this->assertEquals('SMTP connection error', $log->error_message);
    }

    /** @test */
    public function it_supports_field_reference_recipients()
    {
        $form = CustomForm::create(['name' => 'Test Form', 'is_active' => true]);

        $emailField = CustomFormField::create([
            'slick_form_id' => $form->id,
            'name' => 'email',
            'field_type' => 'email',
            'label' => 'Email',
        ]);

        $template = FormEmailTemplate::create([
            'form_id' => $form->id,
            'type' => 'user_confirmation',
            'enabled' => true,
            'recipients' => ['field:email'],
            'subject' => 'Test',
            'body_template' => 'Test body',
        ]);

        $submission = CustomFormSubmission::create([
            'slick_form_id' => $form->id,
            'ip_address' => '127.0.0.1',
            'submitted_at' => now(),
        ]);

        CustomFormFieldValue::create([
            'slick_form_submission_id' => $submission->id,
            'slick_form_field_id' => $emailField->id,
            'value' => 'user@example.com',
        ]);

        config(['slick-forms.email.queue' => false]);
        Mail::fake();

        $this->service->sendUserConfirmation($submission);

        $this->assertDatabaseHas('slick_form_email_logs', [
            'to' => 'user@example.com',
        ]);
    }

    protected function createFormWithEmailTemplate(string $type): CustomForm
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'is_active' => true,
        ]);

        FormEmailTemplate::create([
            'form_id' => $form->id,
            'type' => $type,
            'enabled' => true,
            'recipients' => $type === 'admin' ? ['admin@example.com'] : ['field:email'],
            'subject' => 'Test Subject',
            'body_template' => 'Test body content',
            'attach_pdf' => false,
        ]);

        return $form;
    }

    protected function createSubmission(CustomForm $form): CustomFormSubmission
    {
        return CustomFormSubmission::create([
            'slick_form_id' => $form->id,
            'ip_address' => '127.0.0.1',
            'submitted_at' => now(),
        ]);
    }

    protected function createSubmissionWithEmail(CustomForm $form, string $email): CustomFormSubmission
    {
        $emailField = CustomFormField::create([
            'slick_form_id' => $form->id,
            'name' => 'email',
            'field_type' => 'email',
            'label' => 'Email',
        ]);

        $submission = CustomFormSubmission::create([
            'slick_form_id' => $form->id,
            'ip_address' => '127.0.0.1',
            'submitted_at' => now(),
        ]);

        CustomFormFieldValue::create([
            'slick_form_submission_id' => $submission->id,
            'slick_form_field_id' => $emailField->id,
            'value' => $email,
        ]);

        return $submission;
    }
}
