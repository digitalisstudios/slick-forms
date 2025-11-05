<?php

namespace DigitalisStudios\SlickForms\Events;

use DigitalisStudios\SlickForms\Models\CustomFormSubmission;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when an email notification fails to send
 */
class EmailNotificationFailed
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance
     */
    public function __construct(
        public CustomFormSubmission $submission,
        public string $error
    ) {}
}
