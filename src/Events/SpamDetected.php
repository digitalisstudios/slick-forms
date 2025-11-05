<?php

namespace DigitalisStudios\SlickForms\Events;

use DigitalisStudios\SlickForms\Models\CustomForm;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when spam is detected on a form submission
 */
class SpamDetected
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance
     */
    public function __construct(
        public CustomForm $form,
        public string $method,
        public string $ipAddress
    ) {}
}
