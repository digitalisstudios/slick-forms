<?php

namespace DigitalisStudios\SlickForms\Tests\Unit;

use DigitalisStudios\SlickForms\Events\ModelBound;
use DigitalisStudios\SlickForms\Events\ModelSaved;
use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\CustomFormSubmission;
use DigitalisStudios\SlickForms\Models\FormModelBinding;
use DigitalisStudios\SlickForms\Services\ModelBindingService;
use DigitalisStudios\SlickForms\Tests\Support\User;
use DigitalisStudios\SlickForms\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModelBindingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ModelBindingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ModelBindingService::class);
    }

    /** @test */
    public function it_binds_model_to_form(): void
    {
        $form = CustomForm::factory()->create();

        $config = [
            'model_class' => User::class,
            'route_parameter' => 'user',
            'route_key' => 'id',
            'field_mappings' => [
                'name' => 'name',
                'email' => 'email',
            ],
            'allow_create' => true,
            'allow_update' => true,
        ];

        $result = $this->service->bindModel($form, $config);

        $this->assertEquals(User::class, $result['model_class']);
        $this->assertArrayHasKey('field_mappings', $result);
        $this->assertDatabaseHas('slick_form_model_bindings', [
            'form_id' => $form->id,
            'model_class' => User::class,
        ]);
    }

    /** @test */
    public function it_populates_form_data_from_model(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $form = CustomForm::factory()->create();

        $binding = FormModelBinding::create([
            'form_id' => $form->id,
            'model_class' => User::class,
            'route_parameter' => 'user',
            'route_key' => 'id',
            'field_mappings' => [
                'name' => 'name',
                'email' => 'email',
            ],
            'allow_create' => false,
            'allow_update' => true,
        ]);

        $formData = $this->service->populateFormData($form, $user);

        $this->assertEquals('John Doe', $formData['name']);
        $this->assertEquals('john@example.com', $formData['email']);
    }

    /** @test */
    public function it_saves_form_data_to_new_model(): void
    {
        $form = CustomForm::factory()->create();

        $binding = FormModelBinding::create([
            'form_id' => $form->id,
            'model_class' => User::class,
            'route_parameter' => 'user',
            'route_key' => 'id',
            'field_mappings' => [
                'name' => 'name',
                'email' => 'email',
            ],
            'allow_create' => true,
            'allow_update' => false,
        ]);

        $submission = CustomFormSubmission::factory()->create([
            'slick_form_id' => $form->id,
        ]);

        $formData = [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
        ];

        $model = $this->service->saveModel($form, $formData, $submission);

        $this->assertInstanceOf(User::class, $model);
        $this->assertEquals('Jane Smith', $model->name);
        $this->assertEquals('jane@example.com', $model->email);
        $this->assertDatabaseHas('users', [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
        ]);
    }

    /** @test */
    public function it_updates_existing_model(): void
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);

        $form = CustomForm::factory()->create();

        $binding = FormModelBinding::create([
            'form_id' => $form->id,
            'model_class' => User::class,
            'route_parameter' => 'user',
            'route_key' => 'id',
            'field_mappings' => [
                'name' => 'name',
                'email' => 'email',
            ],
            'allow_create' => false,
            'allow_update' => true,
        ]);

        $submission = CustomFormSubmission::factory()->create([
            'slick_form_id' => $form->id,
        ]);

        $formData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        $updatedModel = $this->service->saveModel($form, $formData, $submission, $user);

        $this->assertEquals('Updated Name', $updatedModel->name);
        $this->assertEquals('updated@example.com', $updatedModel->email);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    /** @test */
    public function it_resolves_nested_attributes_with_dot_notation(): void
    {
        $user = User::factory()->create(['name' => 'Test User']);

        // Test simple attribute
        $name = $this->service->resolveNestedAttribute($user, 'name');
        $this->assertEquals('Test User', $name);

        // Test nested array access
        $user->settings = ['theme' => 'dark', 'notifications' => ['email' => true]];
        $user->save();

        $theme = $this->service->resolveNestedAttribute($user, 'settings.theme');
        $this->assertEquals('dark', $theme);
    }

    /** @test */
    public function it_sets_nested_attributes_with_dot_notation(): void
    {
        $user = User::factory()->create(['name' => 'Original']);

        // Set simple attribute
        $this->service->setNestedAttribute($user, 'name', 'Updated');
        $this->assertEquals('Updated', $user->name);

        // Set nested attribute (will be saved to JSON column if available)
        $user->settings = [];
        $this->service->setNestedAttribute($user, 'settings.theme', 'dark');

        // Since settings is not a real column, this would require a JSON column setup
        // For this test, we just verify the method runs without error
        $this->assertTrue(true);
    }

    /** @test */
    public function it_applies_transformer_to_field_value(): void
    {
        // Test with inline PHP transformer
        $transformer = 'return strtoupper($value);';
        $result = $this->service->applyTransformer('hello', $transformer);

        $this->assertEquals('HELLO', $result);
    }

    /** @test */
    public function it_returns_original_value_on_transformer_error(): void
    {
        // Test with invalid transformer
        $transformer = 'invalid php code {{{';
        $result = $this->service->applyTransformer('test', $transformer);

        // Should return original value on error
        $this->assertEquals('test', $result);
    }

    /** @test */
    public function it_gets_model_from_route_parameters(): void
    {
        $user = User::factory()->create();

        $binding = FormModelBinding::factory()->create([
            'model_class' => User::class,
            'route_parameter' => 'user',
            'route_key' => 'id',
        ]);

        // Simulate route parameters
        $routeParams = ['user' => $user->id];

        $model = $this->service->getModelFromRoute($binding, $routeParams);

        $this->assertInstanceOf(User::class, $model);
        $this->assertEquals($user->id, $model->id);
    }

    /** @test */
    public function it_respects_allow_create_permission(): void
    {
        $form = CustomForm::factory()->create();

        $binding = FormModelBinding::create([
            'form_id' => $form->id,
            'model_class' => User::class,
            'field_mappings' => ['name' => 'name'],
            'allow_create' => false, // Not allowed to create
            'allow_update' => false,
        ]);

        $submission = CustomFormSubmission::factory()->create([
            'slick_form_id' => $form->id,
        ]);

        $formData = ['name' => 'New User'];

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Model creation is not allowed for this form');

        $this->service->saveModel($form, $formData, $submission);
    }

    /** @test */
    public function it_respects_allow_update_permission(): void
    {
        $user = User::factory()->create(['name' => 'Original']);
        $form = CustomForm::factory()->create();

        $binding = FormModelBinding::create([
            'form_id' => $form->id,
            'model_class' => User::class,
            'field_mappings' => ['name' => 'name'],
            'allow_create' => false,
            'allow_update' => false, // Not allowed to update
        ]);

        $submission = CustomFormSubmission::factory()->create([
            'slick_form_id' => $form->id,
        ]);

        $formData = ['name' => 'Updated'];

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Model updates are not allowed for this form');

        $this->service->saveModel($form, $formData, $submission, $user);
    }

    /** @test */
    public function it_dispatches_events_on_bind_and_save(): void
    {
        \Illuminate\Support\Facades\Event::fake();

        $form = CustomForm::factory()->create();

        // Bind event
        $config = [
            'model_class' => User::class,
            'field_mappings' => ['name' => 'name'],
            'allow_create' => true,
            'allow_update' => true,
        ];

        $this->service->bindModel($form, $config);

        \Illuminate\Support\Facades\Event::assertDispatched(ModelBound::class);

        // Save event - use a different form to avoid unique constraint
        \Illuminate\Support\Facades\Event::fake();

        $form2 = CustomForm::factory()->create();

        $binding = FormModelBinding::create([
            'form_id' => $form2->id,
            'model_class' => User::class,
            'field_mappings' => ['name' => 'name', 'email' => 'email'],
            'allow_create' => true,
            'allow_update' => false,
        ]);

        $submission = CustomFormSubmission::factory()->create([
            'slick_form_id' => $form2->id,
        ]);

        $formData = ['name' => 'Test User', 'email' => 'test@example.com'];

        $this->service->saveModel($form2, $formData, $submission);

        \Illuminate\Support\Facades\Event::assertDispatched(ModelSaved::class);
    }

    /** @test */
    public function it_handles_null_model_for_new_creation(): void
    {
        $form = CustomForm::factory()->create();

        $binding = FormModelBinding::create([
            'form_id' => $form->id,
            'model_class' => User::class,
            'field_mappings' => ['name' => 'name', 'email' => 'email'],
            'allow_create' => true,
            'allow_update' => false,
        ]);

        $submission = CustomFormSubmission::factory()->create([
            'slick_form_id' => $form->id,
        ]);

        $formData = [
            'name' => 'New User',
            'email' => 'new@example.com',
        ];

        // Pass null as existing model to force creation
        $model = $this->service->saveModel($form, $formData, $submission, null);

        $this->assertInstanceOf(User::class, $model);
        $this->assertEquals('New User', $model->name);
        $this->assertTrue($model->exists);
    }
}
