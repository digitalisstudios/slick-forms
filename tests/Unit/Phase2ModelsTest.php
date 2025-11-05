<?php

namespace DigitalisStudios\SlickForms\Tests\Unit;

use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\CustomFormSubmission;
use DigitalisStudios\SlickForms\Models\FormEmailLog;
use DigitalisStudios\SlickForms\Models\FormEmailTemplate;
use DigitalisStudios\SlickForms\Models\FormSpamLog;
use DigitalisStudios\SlickForms\Tests\TestCase;

class Phase2ModelsTest extends TestCase
{
    /** @test */
    public function form_email_template_belongs_to_form()
    {
        $form = CustomForm::create(['name' => 'Test Form', 'is_active' => true]);

        $template = FormEmailTemplate::create([
            'form_id' => $form->id,
            'type' => 'admin',
            'enabled' => true,
            'recipients' => ['admin@example.com'],
            'subject' => 'Test',
            'body_template' => 'Test body',
        ]);

        $this->assertInstanceOf(CustomForm::class, $template->form);
        $this->assertEquals($form->id, $template->form->id);
    }

    /** @test */
    public function form_email_template_has_many_logs()
    {
        $form = CustomForm::create(['name' => 'Test Form', 'is_active' => true]);

        $template = FormEmailTemplate::create([
            'form_id' => $form->id,
            'type' => 'admin',
            'enabled' => true,
            'recipients' => ['admin@example.com'],
            'subject' => 'Test',
            'body_template' => 'Test body',
        ]);

        $submission = CustomFormSubmission::create([
            'slick_form_id' => $form->id,
            'ip_address' => '127.0.0.1',
            'submitted_at' => now(),
        ]);

        FormEmailLog::create([
            'submission_id' => $submission->id,
            'template_id' => $template->id,
            'to' => 'admin@example.com',
            'subject' => 'Test',
            'status' => 'sent',
        ]);

        $this->assertCount(1, $template->logs);
    }

    /** @test */
    public function form_email_template_is_admin_template_check()
    {
        $template = new FormEmailTemplate(['type' => 'admin']);
        $this->assertTrue($template->isAdminTemplate());
        $this->assertFalse($template->isUserTemplate());
    }

    /** @test */
    public function form_email_template_is_user_template_check()
    {
        $template = new FormEmailTemplate(['type' => 'user_confirmation']);
        $this->assertTrue($template->isUserTemplate());
        $this->assertFalse($template->isAdminTemplate());
    }

    /** @test */
    public function form_email_template_casts_arrays_correctly()
    {
        $template = FormEmailTemplate::create([
            'form_id' => CustomForm::create(['name' => 'Test', 'is_active' => true])->id,
            'type' => 'admin',
            'enabled' => true,
            'recipients' => ['admin@example.com', 'support@example.com'],
            'subject' => 'Test',
            'body_template' => 'Test body',
            'conditional_rules' => [
                ['field' => 'status', 'operator' => 'equals', 'value' => 'approved'],
            ],
        ]);

        $this->assertIsArray($template->recipients);
        $this->assertIsArray($template->conditional_rules);
        $this->assertIsBool($template->enabled);
    }

    /** @test */
    public function form_email_log_belongs_to_submission()
    {
        $form = CustomForm::create(['name' => 'Test Form', 'is_active' => true]);

        $submission = CustomFormSubmission::create([
            'slick_form_id' => $form->id,
            'ip_address' => '127.0.0.1',
            'submitted_at' => now(),
        ]);

        $log = FormEmailLog::create([
            'submission_id' => $submission->id,
            'to' => 'user@example.com',
            'subject' => 'Test',
            'status' => 'sent',
        ]);

        $this->assertInstanceOf(CustomFormSubmission::class, $log->submission);
        $this->assertEquals($submission->id, $log->submission->id);
    }

    /** @test */
    public function form_email_log_belongs_to_template()
    {
        $form = CustomForm::create(['name' => 'Test Form', 'is_active' => true]);

        $template = FormEmailTemplate::create([
            'form_id' => $form->id,
            'type' => 'admin',
            'enabled' => true,
            'recipients' => ['admin@example.com'],
            'subject' => 'Test',
            'body_template' => 'Test body',
        ]);

        $submission = CustomFormSubmission::create([
            'slick_form_id' => $form->id,
            'ip_address' => '127.0.0.1',
            'submitted_at' => now(),
        ]);

        $log = FormEmailLog::create([
            'submission_id' => $submission->id,
            'template_id' => $template->id,
            'to' => 'admin@example.com',
            'subject' => 'Test',
            'status' => 'sent',
        ]);

        $this->assertInstanceOf(FormEmailTemplate::class, $log->template);
        $this->assertEquals($template->id, $log->template->id);
    }

    /** @test */
    public function form_email_log_status_helpers_work()
    {
        $sentLog = new FormEmailLog(['status' => 'sent']);
        $failedLog = new FormEmailLog(['status' => 'failed']);
        $queuedLog = new FormEmailLog(['status' => 'queued']);

        $this->assertTrue($sentLog->wasSent());
        $this->assertFalse($sentLog->failed());
        $this->assertFalse($sentLog->isQueued());

        $this->assertFalse($failedLog->wasSent());
        $this->assertTrue($failedLog->failed());
        $this->assertFalse($failedLog->isQueued());

        $this->assertFalse($queuedLog->wasSent());
        $this->assertFalse($queuedLog->failed());
        $this->assertTrue($queuedLog->isQueued());
    }

    /** @test */
    public function form_spam_log_belongs_to_form()
    {
        $form = CustomForm::create(['name' => 'Test Form', 'is_active' => true]);

        $log = FormSpamLog::create([
            'form_id' => $form->id,
            'ip_address' => '192.168.1.1',
            'detection_method' => 'honeypot',
            'details' => ['field_filled' => true],
        ]);

        $this->assertInstanceOf(CustomForm::class, $log->form);
        $this->assertEquals($form->id, $log->form->id);
    }

    /** @test */
    public function form_spam_log_detection_method_helpers_work()
    {
        $honeypotLog = new FormSpamLog(['detection_method' => 'honeypot']);
        $recaptchaLog = new FormSpamLog(['detection_method' => 'recaptcha']);
        $hcaptchaLog = new FormSpamLog(['detection_method' => 'hcaptcha']);
        $rateLimitLog = new FormSpamLog(['detection_method' => 'rate_limit']);

        $this->assertTrue($honeypotLog->isHoneypot());
        $this->assertFalse($honeypotLog->isRecaptcha());

        $this->assertTrue($recaptchaLog->isRecaptcha());
        $this->assertFalse($recaptchaLog->isHcaptcha());

        $this->assertTrue($hcaptchaLog->isHcaptcha());
        $this->assertFalse($hcaptchaLog->isRateLimit());

        $this->assertTrue($rateLimitLog->isRateLimit());
        $this->assertFalse($rateLimitLog->isHoneypot());
    }

    /** @test */
    public function form_spam_log_casts_details_as_array()
    {
        $log = FormSpamLog::create([
            'form_id' => CustomForm::create(['name' => 'Test', 'is_active' => true])->id,
            'ip_address' => '192.168.1.1',
            'detection_method' => 'honeypot',
            'details' => ['field_filled' => true, 'submission_time' => 12345],
        ]);

        $this->assertIsArray($log->details);
        $this->assertTrue($log->details['field_filled']);
        $this->assertEquals(12345, $log->details['submission_time']);
    }
}
