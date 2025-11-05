<?php

namespace DigitalisStudios\SlickForms\Events;

use DigitalisStudios\SlickForms\Models\CustomForm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when a model is bound to a form
 */
class ModelBound
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance
     */
    public function __construct(
        public CustomForm $form,
        public ?Model $model = null
    ) {}
}
