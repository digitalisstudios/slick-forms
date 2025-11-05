<?php

namespace DigitalisStudios\SlickForms\Jobs;

use DigitalisStudios\SlickForms\Models\CustomFormSubmission;
use DigitalisStudios\SlickForms\Models\FormEmailTemplate;
use DigitalisStudios\SlickForms\Services\EmailNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Job to send email notification asynchronously
 */
class SendEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job
     */
    public int $backoff = 60;

    /**
     * Create a new job instance
     */
    public function __construct(
        public CustomFormSubmission $submission,
        public FormEmailTemplate $template
    ) {}

    /**
     * Execute the job
     */
    public function handle(EmailNotificationService $emailService): void
    {
        // Use the protected sendEmail method via reflection, or call the public methods
        // Since sendEmail is protected, we'll use a public wrapper approach

        try {
            // Call the service's sendEmail method through a dedicated public method
            $recipients = $this->getRecipients($emailService);
            $subject = $this->renderSubject($emailService);
            $body = $emailService->renderEmailTemplate($this->template, $this->submission);

            \Mail::send([], [], function ($message) use ($recipients, $subject, $body, $emailService) {
                foreach ($recipients as $recipient) {
                    $message->to($recipient)
                        ->subject($subject)
                        ->html($body)
                        ->from(
                            config('slick-forms.email.from_address', config('mail.from.address')),
                            config('slick-forms.email.from_name', config('mail.from.name'))
                        );

                    // Attach PDF if configured
                    if ($this->template->attach_pdf) {
                        $pdfPath = $emailService->attachSubmissionPdf($this->submission);
                        $message->attach($pdfPath, [
                            'as' => 'submission-'.$this->submission->id.'.pdf',
                            'mime' => 'application/pdf',
                        ]);
                    }
                }
            });

            $emailService->logEmailDelivery($this->submission, $this->template, 'sent');
        } catch (\Exception $e) {
            $emailService->logEmailDelivery($this->submission, $this->template, 'failed', $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get recipients using reflection to access protected method
     */
    protected function getRecipients(EmailNotificationService $emailService): array
    {
        $reflection = new \ReflectionClass($emailService);
        $method = $reflection->getMethod('getRecipients');
        $method->setAccessible(true);

        return $method->invoke($emailService, $this->submission, $this->template);
    }

    /**
     * Render subject using reflection to access protected method
     */
    protected function renderSubject(EmailNotificationService $emailService): string
    {
        $reflection = new \ReflectionClass($emailService);
        $method = $reflection->getMethod('renderSubject');
        $method->setAccessible(true);

        return $method->invoke($emailService, $this->template, $this->submission);
    }
}
