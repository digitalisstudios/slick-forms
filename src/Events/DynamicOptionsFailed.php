<?php

namespace DigitalisStudios\SlickForms\Events;

use DigitalisStudios\SlickForms\Models\CustomFormField;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when dynamic options fail to load
 */
class DynamicOptionsFailed
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance
     */
    public function __construct(
        public CustomFormField $field,
        public \Exception $exception
    ) {}
}
