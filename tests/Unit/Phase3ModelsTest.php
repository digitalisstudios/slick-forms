<?php

namespace DigitalisStudios\SlickForms\Tests\Unit;

use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Models\DynamicOptionsCache;
use DigitalisStudios\SlickForms\Models\FormModelBinding;
use DigitalisStudios\SlickForms\Tests\TestCase;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Phase3ModelsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function dynamic_options_cache_belongs_to_field(): void
    {
        $field = CustomFormField::factory()->create();

        $cache = DynamicOptionsCache::create([
            'field_id' => $field->id,
            'cache_key' => 'test-key',
            'options' => [['value' => '1', 'label' => 'Option 1']],
            'ttl_seconds' => 300,
            'cached_at' => now(),
        ]);

        $this->assertInstanceOf(CustomFormField::class, $cache->field);
        $this->assertEquals($field->id, $cache->field->id);
    }

    /** @test */
    public function dynamic_options_cache_casts_options_to_array(): void
    {
        $field = CustomFormField::factory()->create();

        $cache = DynamicOptionsCache::create([
            'field_id' => $field->id,
            'cache_key' => 'test-key',
            'options' => [['value' => '1', 'label' => 'Test']],
            'ttl_seconds' => 300,
            'cached_at' => now(),
        ]);

        $this->assertIsArray($cache->options);
        $this->assertCount(1, $cache->options);
    }

    /** @test */
    public function dynamic_options_cache_is_expired_when_ttl_exceeded(): void
    {
        $field = CustomFormField::factory()->create();

        $cache = DynamicOptionsCache::create([
            'field_id' => $field->id,
            'cache_key' => 'test-key',
            'options' => [['value' => '1', 'label' => 'Test']],
            'ttl_seconds' => 300,
            'cached_at' => now()->subSeconds(400), // 400 seconds ago
        ]);

        $this->assertTrue($cache->isExpired());
    }

    /** @test */
    public function dynamic_options_cache_is_not_expired_when_within_ttl(): void
    {
        $field = CustomFormField::factory()->create();

        $cache = DynamicOptionsCache::create([
            'field_id' => $field->id,
            'cache_key' => 'test-key',
            'options' => [['value' => '1', 'label' => 'Test']],
            'ttl_seconds' => 300,
            'cached_at' => now()->subSeconds(200), // 200 seconds ago
        ]);

        $this->assertFalse($cache->isExpired());
    }

    /** @test */
    public function dynamic_options_cache_is_valid_when_not_expired(): void
    {
        $field = CustomFormField::factory()->create();

        $cache = DynamicOptionsCache::create([
            'field_id' => $field->id,
            'cache_key' => 'test-key',
            'options' => [['value' => '1', 'label' => 'Test']],
            'ttl_seconds' => 300,
            'cached_at' => now(),
        ]);

        $this->assertTrue($cache->isValid());
    }

    /** @test */
    public function dynamic_options_cache_expires_at_returns_correct_datetime(): void
    {
        $field = CustomFormField::factory()->create();
        $cachedAt = now();

        $cache = DynamicOptionsCache::create([
            'field_id' => $field->id,
            'cache_key' => 'test-key',
            'options' => [['value' => '1', 'label' => 'Test']],
            'ttl_seconds' => 300,
            'cached_at' => $cachedAt,
        ]);

        $expiresAt = $cache->expiresAt();

        $this->assertEquals($cachedAt->addSeconds(300)->timestamp, $expiresAt->timestamp, '', 1);
    }

    /** @test */
    public function form_model_binding_belongs_to_form(): void
    {
        $form = CustomForm::factory()->create();

        $binding = FormModelBinding::create([
            'form_id' => $form->id,
            'model_class' => User::class,
            'route_parameter' => 'user',
            'route_key' => 'id',
            'field_mappings' => ['name' => 'name'],
            'relationship_mappings' => [],
            'allow_create' => true,
            'allow_update' => true,
        ]);

        $this->assertInstanceOf(CustomForm::class, $binding->form);
        $this->assertEquals($form->id, $binding->form->id);
    }

    /** @test */
    public function form_model_binding_casts_field_mappings_to_array(): void
    {
        $form = CustomForm::factory()->create();

        $binding = FormModelBinding::create([
            'form_id' => $form->id,
            'model_class' => User::class,
            'field_mappings' => ['name' => 'name', 'email' => 'email'],
            'allow_create' => true,
            'allow_update' => true,
        ]);

        $this->assertIsArray($binding->field_mappings);
        $this->assertCount(2, $binding->field_mappings);
        $this->assertEquals('name', $binding->field_mappings['name']);
    }

    /** @test */
    public function form_model_binding_casts_relationship_mappings_to_array(): void
    {
        $form = CustomForm::factory()->create();

        $binding = FormModelBinding::create([
            'form_id' => $form->id,
            'model_class' => User::class,
            'field_mappings' => ['name' => 'name'],
            'relationship_mappings' => ['roles' => 'roles', 'tags' => 'tags'],
            'allow_create' => true,
            'allow_update' => true,
        ]);

        $this->assertIsArray($binding->relationship_mappings);
        $this->assertCount(2, $binding->relationship_mappings);
    }

    /** @test */
    public function form_model_binding_casts_boolean_flags(): void
    {
        $form = CustomForm::factory()->create();

        $binding = FormModelBinding::create([
            'form_id' => $form->id,
            'model_class' => User::class,
            'field_mappings' => ['name' => 'name'],
            'allow_create' => 1,
            'allow_update' => 0,
        ]);

        $this->assertIsBool($binding->allow_create);
        $this->assertIsBool($binding->allow_update);
        $this->assertTrue($binding->allow_create);
        $this->assertFalse($binding->allow_update);
    }

    /** @test */
    public function form_model_binding_allows_create_helper_method(): void
    {
        $form = CustomForm::factory()->create();

        $binding = FormModelBinding::create([
            'form_id' => $form->id,
            'model_class' => User::class,
            'field_mappings' => ['name' => 'name'],
            'allow_create' => true,
            'allow_update' => false,
        ]);

        $this->assertTrue($binding->allowsCreate());
        $this->assertFalse($binding->allowsUpdate());
    }

    /** @test */
    public function form_model_binding_allows_update_helper_method(): void
    {
        $form = CustomForm::factory()->create();

        $binding = FormModelBinding::create([
            'form_id' => $form->id,
            'model_class' => User::class,
            'field_mappings' => ['name' => 'name'],
            'allow_create' => false,
            'allow_update' => true,
        ]);

        $this->assertFalse($binding->allowsCreate());
        $this->assertTrue($binding->allowsUpdate());
    }

    /** @test */
    public function form_model_binding_get_model_instance_creates_new_model(): void
    {
        $form = CustomForm::factory()->create();

        $binding = FormModelBinding::create([
            'form_id' => $form->id,
            'model_class' => User::class,
            'field_mappings' => ['name' => 'name'],
            'allow_create' => true,
            'allow_update' => false,
        ]);

        $model = $binding->getModelInstance();

        $this->assertInstanceOf(User::class, $model);
        $this->assertFalse($model->exists);
    }

    /** @test */
    public function custom_form_has_model_binding_relationship(): void
    {
        $form = CustomForm::factory()->create();

        $binding = FormModelBinding::create([
            'form_id' => $form->id,
            'model_class' => User::class,
            'field_mappings' => ['name' => 'name'],
            'allow_create' => true,
            'allow_update' => true,
        ]);

        $this->assertInstanceOf(FormModelBinding::class, $form->modelBinding);
        $this->assertEquals($binding->id, $form->modelBinding->id);
    }

    /** @test */
    public function custom_form_field_has_dynamic_options_cache_relationship(): void
    {
        $field = CustomFormField::factory()->create();

        $cache1 = DynamicOptionsCache::create([
            'field_id' => $field->id,
            'cache_key' => 'key1',
            'options' => [['value' => '1']],
            'ttl_seconds' => 300,
            'cached_at' => now(),
        ]);

        $cache2 = DynamicOptionsCache::create([
            'field_id' => $field->id,
            'cache_key' => 'key2',
            'options' => [['value' => '2']],
            'ttl_seconds' => 300,
            'cached_at' => now(),
        ]);

        $field->load('dynamicOptionsCache');

        $this->assertCount(2, $field->dynamicOptionsCache);
        $this->assertInstanceOf(DynamicOptionsCache::class, $field->dynamicOptionsCache->first());
    }
}
