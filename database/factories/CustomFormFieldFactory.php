<?php

namespace DigitalisStudios\SlickForms\Database\Factories;

use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\CustomFormField;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomFormFieldFactory extends Factory
{
    protected $model = CustomFormField::class;

    public function definition(): array
    {
        return [
            'slick_form_id' => CustomForm::factory(),
            'field_type' => 'text',
            'name' => $this->faker->unique()->word(),
            'label' => $this->faker->words(3, true),
            'placeholder' => $this->faker->sentence(3),
            'help_text' => $this->faker->sentence(),
            'is_required' => false,
            'validation_rules' => [],
            'options' => [],
            'conditional_logic' => [],
            'order' => $this->faker->numberBetween(1, 100),
            'show_label' => true,
            'help_text_as_popover' => false,
        ];
    }
}
