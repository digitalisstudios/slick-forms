<?php

namespace DigitalisStudios\SlickForms\Events;

use DigitalisStudios\SlickForms\Models\CustomForm;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when a signed URL is generated for a form
 */
class SignedUrlGenerated
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance
     */
    public function __construct(
        public CustomForm $form,
        public string $url
    ) {}
}
