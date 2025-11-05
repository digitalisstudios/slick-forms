<?php

namespace DigitalisStudios\SlickForms\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use DigitalisStudios\SlickForms\Events\EmailNotificationFailed;
use DigitalisStudios\SlickForms\Events\EmailNotificationSending;
use DigitalisStudios\SlickForms\Events\EmailNotificationSent;
use DigitalisStudios\SlickForms\Jobs\SendEmailNotification;
use DigitalisStudios\SlickForms\Models\CustomFormSubmission;
use DigitalisStudios\SlickForms\Models\FormEmailLog;
use DigitalisStudios\SlickForms\Models\FormEmailTemplate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Mail;

/**
 * Service for handling email notifications
 */
class EmailNotificationService
{
    /**
     * Send admin notification email for a form submission
     */
    public function sendAdminNotification(CustomFormSubmission $submission): void
    {
        $form = $submission->form;

        // Get all admin email templates for this form
        $templates = FormEmailTemplate::where('form_id', $form->id)
            ->where('type', 'admin')
            ->where('enabled', true)
            ->orderBy('priority')
            ->get();

        foreach ($templates as $template) {
            // Check if conditional rules allow sending
            if (! $this->evaluateConditionalRules($template, $submission)) {
                continue;
            }

            // Queue or send email based on configuration
            if (config('slick-forms.email.queue', true)) {
                SendEmailNotification::dispatch($submission, $template);
                $this->logEmailDelivery($submission, $template, 'queued');
            } else {
                $this->sendEmail($submission, $template);
            }
        }
    }

    /**
     * Send user confirmation email for a form submission
     */
    public function sendUserConfirmation(CustomFormSubmission $submission): void
    {
        $form = $submission->form;

        // Get user confirmation email templates
        $templates = FormEmailTemplate::where('form_id', $form->id)
            ->where('type', 'user_confirmation')
            ->where('enabled', true)
            ->orderBy('priority')
            ->get();

        foreach ($templates as $template) {
            // Check if conditional rules allow sending
            if (! $this->evaluateConditionalRules($template, $submission)) {
                continue;
            }

            // Get user email from form data
            $userEmail = $this->getUserEmail($submission, $template);

            if (! $userEmail) {
                continue;
            }

            // Queue or send email based on configuration
            if (config('slick-forms.email.queue', true)) {
                SendEmailNotification::dispatch($submission, $template);
                $this->logEmailDelivery($submission, $template, 'queued');
            } else {
                $this->sendEmail($submission, $template);
            }
        }
    }

    /**
     * Render email template with submission data
     *
     * @return string Rendered HTML content
     */
    public function renderEmailTemplate(FormEmailTemplate $template, CustomFormSubmission $submission): string
    {
        // Prepare template variables
        $data = [
            'submission' => $submission,
            'form' => $submission->form,
            'field_values' => $submission->fieldValues->keyBy('field.name'),
            'submitted_at' => $submission->created_at,
            'ip_address' => $submission->ip_address,
        ];

        // Render template using Blade
        try {
            return Blade::render($template->body_template, $data);
        } catch (\Exception $e) {
            // Fallback to simple string replacement if Blade fails
            return $this->simpleTemplateRender($template->body_template, $data);
        }
    }

    /**
     * Evaluate conditional rules to determine if email should be sent
     *
     * @return bool True if email should be sent
     */
    public function evaluateConditionalRules(FormEmailTemplate $template, CustomFormSubmission $submission): bool
    {
        // If no conditional rules, always send
        if (empty($template->conditional_rules)) {
            return true;
        }

        // Get submitted form data
        $formData = $submission->fieldValues->pluck('value', 'field.name')->toArray();

        // Evaluate each rule (AND logic - all must pass)
        foreach ($template->conditional_rules as $rule) {
            $fieldName = $rule['field'] ?? null;
            $operator = $rule['operator'] ?? 'equals';
            $value = $rule['value'] ?? null;

            if (! $fieldName || ! isset($formData[$fieldName])) {
                return false;
            }

            $fieldValue = $formData[$fieldName];

            // Evaluate based on operator
            $passes = match ($operator) {
                'equals' => $fieldValue == $value,
                'not_equals' => $fieldValue != $value,
                'contains' => str_contains($fieldValue, $value),
                'not_contains' => ! str_contains($fieldValue, $value),
                'greater_than' => $fieldValue > $value,
                'less_than' => $fieldValue < $value,
                'is_empty' => empty($fieldValue),
                'is_not_empty' => ! empty($fieldValue),
                default => false,
            };

            if (! $passes) {
                return false;
            }
        }

        return true;
    }

    /**
     * Generate PDF attachment of submission
     *
     * @return string Path to generated PDF file
     */
    public function attachSubmissionPdf(CustomFormSubmission $submission): string
    {
        $form = $submission->form;

        // Prepare data for PDF
        $data = [
            'submission' => $submission,
            'form' => $form,
            'field_values' => $submission->fieldValues,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('slick-forms::emails.submission-pdf', $data);

        // Save to temporary location
        $filename = 'submission-'.$submission->id.'-'.time().'.pdf';
        $path = storage_path('app/temp/'.$filename);

        // Ensure directory exists
        if (! file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $pdf->save($path);

        return $path;
    }

    /**
     * Log email delivery attempt
     *
     * @param  string  $status  Status: 'sent', 'failed', 'queued'
     * @param  string|null  $error  Optional error message
     */
    public function logEmailDelivery(
        CustomFormSubmission $submission,
        FormEmailTemplate $template,
        string $status,
        ?string $error = null
    ): void {
        $recipients = $this->getRecipients($submission, $template);

        foreach ($recipients as $recipient) {
            FormEmailLog::create([
                'submission_id' => $submission->id,
                'template_id' => $template->id,
                'to' => $recipient,
                'subject' => $this->renderSubject($template, $submission),
                'body' => $status === 'sent' ? $this->renderEmailTemplate($template, $submission) : null,
                'status' => $status,
                'error_message' => $error,
                'sent_at' => $status === 'sent' ? now() : null,
            ]);
        }
    }

    /**
     * Send email immediately (not queued)
     */
    protected function sendEmail(CustomFormSubmission $submission, FormEmailTemplate $template): void
    {
        try {
            event(new EmailNotificationSending($submission, $template));

            $recipients = $this->getRecipients($submission, $template);
            $subject = $this->renderSubject($template, $submission);
            $body = $this->renderEmailTemplate($template, $submission);

            foreach ($recipients as $recipient) {
                Mail::send([], [], function ($message) use ($recipient, $subject, $body, $template, $submission) {
                    $message->to($recipient)
                        ->subject($subject)
                        ->html($body)
                        ->from(
                            config('slick-forms.email.from_address', config('mail.from.address')),
                            config('slick-forms.email.from_name', config('mail.from.name'))
                        );

                    // Attach PDF if configured
                    if ($template->attach_pdf) {
                        $pdfPath = $this->attachSubmissionPdf($submission);
                        $message->attach($pdfPath, [
                            'as' => 'submission-'.$submission->id.'.pdf',
                            'mime' => 'application/pdf',
                        ]);
                    }
                });
            }

            $this->logEmailDelivery($submission, $template, 'sent');
            event(new EmailNotificationSent($submission, $template));
        } catch (\Exception $e) {
            $this->logEmailDelivery($submission, $template, 'failed', $e->getMessage());
            event(new EmailNotificationFailed($submission, $template, $e->getMessage()));
        }
    }

    /**
     * Get recipient email addresses
     */
    protected function getRecipients(CustomFormSubmission $submission, FormEmailTemplate $template): array
    {
        $recipients = [];

        foreach ($template->recipients as $recipient) {
            // Check if recipient is a field reference (e.g., "field:email")
            if (str_starts_with($recipient, 'field:')) {
                $fieldName = substr($recipient, 6);
                $fieldValue = $submission->fieldValues()
                    ->whereHas('field', fn ($q) => $q->where('name', $fieldName))
                    ->first();

                if ($fieldValue && filter_var($fieldValue->value, FILTER_VALIDATE_EMAIL)) {
                    $recipients[] = $fieldValue->value;
                }
            } elseif (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                // Direct email address
                $recipients[] = $recipient;
            }
        }

        return array_unique($recipients);
    }

    /**
     * Get user email from submission
     */
    protected function getUserEmail(CustomFormSubmission $submission, FormEmailTemplate $template): ?string
    {
        $recipients = $this->getRecipients($submission, $template);

        return $recipients[0] ?? null;
    }

    /**
     * Render email subject with template variables
     */
    protected function renderSubject(FormEmailTemplate $template, CustomFormSubmission $submission): string
    {
        $data = [
            'form_name' => $submission->form->name,
            'submission_id' => $submission->id,
            'submitted_at' => $submission->created_at->format('Y-m-d H:i:s'),
        ];

        $subject = $template->subject;

        foreach ($data as $key => $value) {
            $subject = str_replace('{{'.$key.'}}', $value, $subject);
        }

        return $subject;
    }

    /**
     * Simple template rendering fallback
     */
    protected function simpleTemplateRender(string $template, array $data): string
    {
        // Extract field values for easy replacement
        $replacements = [];

        if (isset($data['field_values'])) {
            foreach ($data['field_values'] as $fieldName => $fieldValue) {
                $replacements['{{ $field_values[\''.$fieldName.'\'] }}'] = $fieldValue->value ?? '';
            }
        }

        $replacements['{{ $form->name }}'] = $data['form']->name ?? '';
        $replacements['{{ $submission->id }}'] = $data['submission']->id ?? '';
        $replacements['{{ $submitted_at }}'] = $data['submitted_at']->format('Y-m-d H:i:s') ?? '';

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }
}
