<?php

namespace DigitalisStudios\SlickForms\Database\Factories;

use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\CustomFormSubmission;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomFormSubmissionFactory extends Factory
{
    protected $model = CustomFormSubmission::class;

    public function definition(): array
    {
        return [
            'slick_form_id' => CustomForm::factory(),
            'user_id' => null,
            'ip_address' => $this->faker->ipv4(),
            'submitted_at' => now(),
        ];
    }
}
