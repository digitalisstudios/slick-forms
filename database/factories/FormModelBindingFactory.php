<?php

namespace DigitalisStudios\SlickForms\Database\Factories;

use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\FormModelBinding;
use DigitalisStudios\SlickForms\Tests\Support\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormModelBindingFactory extends Factory
{
    protected $model = FormModelBinding::class;

    public function definition(): array
    {
        return [
            'form_id' => CustomForm::factory(),
            'model_class' => User::class,
            'route_parameter' => 'model',
            'route_key' => 'id',
            'field_mappings' => [
                'name' => 'name',
                'email' => 'email',
            ],
            'relationship_mappings' => [],
            'allow_create' => true,
            'allow_update' => true,
        ];
    }
}
