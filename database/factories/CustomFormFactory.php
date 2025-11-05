<?php

namespace DigitalisStudios\SlickForms\Database\Factories;

use DigitalisStudios\SlickForms\Models\CustomForm;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomFormFactory extends Factory
{
    protected $model = CustomForm::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->sentence(10),
            'is_active' => true,
            'is_template' => false,
            'settings' => [],
        ];
    }
}
