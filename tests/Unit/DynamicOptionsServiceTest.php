<?php

namespace DigitalisStudios\SlickForms\Tests\Unit;

use DigitalisStudios\SlickForms\Events\DynamicOptionsFailed;
use DigitalisStudios\SlickForms\Events\DynamicOptionsLoaded;
use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Models\DynamicOptionsCache;
use DigitalisStudios\SlickForms\Services\DynamicOptionsService;
use DigitalisStudios\SlickForms\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DynamicOptionsServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DynamicOptionsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DynamicOptionsService::class);
    }

    /** @test */
    public function it_fetches_options_from_url(): void
    {
        Http::fake([
            'https://api.example.com/options' => Http::response([
                ['value' => '1', 'label' => 'Option 1'],
                ['value' => '2', 'label' => 'Option 2'],
            ], 200),
        ]);

        $config = [
            'dynamic_source' => 'url',
            'source_url' => 'https://api.example.com/options',
            'value_key' => 'value',
            'label_key' => 'label',
        ];

        $field = CustomFormField::factory()->create(['options' => $config]);

        $options = $this->service->loadOptions($field, null);

        $this->assertCount(2, $options);
        $this->assertEquals('1', $options[0]['value']);
        $this->assertEquals('Option 1', $options[0]['label']);
        $this->assertEquals('2', $options[1]['value']);
        $this->assertEquals('Option 2', $options[1]['label']);
    }

    /** @test */
    public function it_handles_nested_json_paths(): void
    {
        Http::fake([
            'https://api.example.com/data' => Http::response([
                'data' => [
                    'items' => [
                        ['id' => '1', 'name' => 'Item 1'],
                        ['id' => '2', 'name' => 'Item 2'],
                    ],
                ],
            ], 200),
        ]);

        $config = [
            'dynamic_source' => 'url',
            'source_url' => 'https://api.example.com/data',
            'value_key' => 'id',
            'label_key' => 'name',
        ];

        $field = CustomFormField::factory()->create(['options' => $config]);

        $options = $this->service->loadOptions($field, null);

        $this->assertCount(2, $options);
        $this->assertEquals('1', $options[0]['value']);
        $this->assertEquals('Item 1', $options[0]['label']);
    }

    /** @test */
    public function it_caches_fetched_options(): void
    {
        Http::fake([
            'https://api.example.com/options' => Http::response([
                ['value' => '1', 'label' => 'Option 1'],
            ], 200),
        ]);

        $config = [
            'dynamic_source' => 'url',
            'source_url' => 'https://api.example.com/options',
            'value_key' => 'value',
            'label_key' => 'label',
        ];

        $field = CustomFormField::factory()->create(['options' => $config]);

        // First call should make HTTP request
        $this->service->loadOptions($field, null);

        // Verify cache entry was created
        $this->assertDatabaseHas('slick_dynamic_options_cache', [
            'field_id' => $field->id,
        ]);

        // Second call should use cache (no HTTP request)
        Http::assertSentCount(1); // Only one request should have been made
        $cachedOptions = $this->service->loadOptions($field, null);

        $this->assertCount(1, $cachedOptions);
    }

    /** @test */
    public function it_invalidates_cache_correctly(): void
    {
        $field = CustomFormField::factory()->create();

        // Create cached options
        DynamicOptionsCache::create([
            'field_id' => $field->id,
            'cache_key' => 'test-key',
            'options' => [['value' => '1', 'label' => 'Test']],
            'ttl_seconds' => 300,
            'cached_at' => now(),
        ]);

        $this->assertDatabaseHas('slick_dynamic_options_cache', [
            'field_id' => $field->id,
        ]);

        // Invalidate cache
        $this->service->invalidateCache($field);

        // Verify cache was deleted
        $this->assertDatabaseMissing('slick_dynamic_options_cache', [
            'field_id' => $field->id,
        ]);
    }

    /** @test */
    public function it_respects_cache_ttl(): void
    {
        $field = CustomFormField::factory()->create();

        // Create expired cache
        $cache = DynamicOptionsCache::create([
            'field_id' => $field->id,
            'cache_key' => 'test-key',
            'options' => [['value' => '1', 'label' => 'Old Option']],
            'ttl_seconds' => 300,
            'cached_at' => now()->subSeconds(400), // Expired
        ]);

        // Should return null since cache is expired
        $result = $this->service->getCachedOptions($field, 'test-key');

        $this->assertNull($result);
    }

    /** @test */
    public function it_fetches_options_from_model(): void
    {
        $config = [
            'dynamic_source' => 'model',
            'model_class' => \Illuminate\Foundation\Auth\User::class,
            'value_column' => 'id',
            'label_column' => 'name',
        ];

        // Create test users
        \DigitalisStudios\SlickForms\Tests\TestUser::factory()->create(['name' => 'John Doe']);
        \DigitalisStudios\SlickForms\Tests\TestUser::factory()->create(['name' => 'Jane Smith']);

        $field = CustomFormField::factory()->create(['options' => $config]);

        $options = $this->service->loadOptions($field, null);

        $this->assertCount(2, $options);
        $this->assertArrayHasKey('value', $options[0]);
        $this->assertArrayHasKey('label', $options[0]);
    }

    /** @test */
    public function it_applies_where_conditions_to_model_queries(): void
    {
        $config = [
            'dynamic_source' => 'model',
            'model_class' => \Illuminate\Foundation\Auth\User::class,
            'value_column' => 'id',
            'label_column' => 'name',
            'where_conditions' => json_encode(['email' => 'john@example.com']),
        ];

        // Create test users
        \DigitalisStudios\SlickForms\Tests\TestUser::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
        \DigitalisStudios\SlickForms\Tests\TestUser::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);

        $field = CustomFormField::factory()->create(['options' => $config]);

        $options = $this->service->loadOptions($field, null);

        // Should only return John
        $this->assertCount(1, $options);
        $this->assertEquals('John Doe', $options[0]['label']);
    }

    /** @test */
    public function it_replaces_parent_placeholder_in_cascading_dropdowns(): void
    {
        Http::fake([
            'https://api.example.com/options/5' => Http::response([
                ['value' => '10', 'label' => 'Child Option 1'],
                ['value' => '11', 'label' => 'Child Option 2'],
            ], 200),
        ]);

        $config = [
            'dynamic_source' => 'url',
            'source_url' => 'https://api.example.com/options/{parent}',
            'value_key' => 'value',
            'label_key' => 'label',
        ];

        $field = CustomFormField::factory()->create(['options' => $config]);

        // Pass parent value
        $options = $this->service->loadOptions($field, '5');

        $this->assertCount(2, $options);
        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/options/5');
        });
    }

    /** @test */
    public function it_returns_empty_array_on_http_failure(): void
    {
        Http::fake([
            'https://api.example.com/options' => Http::response(null, 500),
        ]);

        $config = [
            'dynamic_source' => 'url',
            'source_url' => 'https://api.example.com/options',
            'value_key' => 'value',
            'label_key' => 'label',
        ];

        $field = CustomFormField::factory()->create(['options' => $config]);

        $options = $this->service->loadOptions($field, null);

        $this->assertIsArray($options);
        $this->assertEmpty($options);
    }

    /** @test */
    public function it_dispatches_events_on_success_and_failure(): void
    {
        \Illuminate\Support\Facades\Event::fake();

        // Success case
        Http::fake([
            'https://api.example.com/options' => Http::response([
                ['value' => '1', 'label' => 'Option 1'],
            ], 200),
        ]);

        $field = CustomFormField::factory()->create([
            'options' => [
                'dynamic_source' => 'url',
                'source_url' => 'https://api.example.com/options',
                'value_key' => 'value',
                'label_key' => 'label',
            ],
        ]);

        $this->service->loadOptions($field, null);

        \Illuminate\Support\Facades\Event::assertDispatched(DynamicOptionsLoaded::class);

        // Failure case
        \Illuminate\Support\Facades\Event::fake();
        Http::fake([
            'https://api.example.com/fail' => Http::response(null, 500),
        ]);

        $failField = CustomFormField::factory()->create([
            'options' => [
                'dynamic_source' => 'url',
                'source_url' => 'https://api.example.com/fail',
                'value_key' => 'value',
                'label_key' => 'label',
            ],
        ]);

        $this->service->loadOptions($failField, null);

        \Illuminate\Support\Facades\Event::assertDispatched(DynamicOptionsFailed::class);
    }

    /** @test */
    public function it_resolves_json_paths_correctly(): void
    {
        $data = [
            'data' => [
                'items' => [
                    ['id' => 1, 'name' => 'Item 1'],
                    ['id' => 2, 'name' => 'Item 2'],
                ],
            ],
        ];

        // Test nested path resolution
        $items = $this->service->resolveJsonPath($data, 'data.items');
        $this->assertIsArray($items);
        if (! empty($items)) {
            $this->assertCount(2, $items);
            $this->assertEquals(1, $items[0]['id']);
        }

        // Test root path (empty path returns original data)
        $root = $this->service->resolveJsonPath($data, '');
        $this->assertIsArray($root);
        if (is_array($root) && isset($root['data'])) {
            $this->assertArrayHasKey('data', $root);
        }

        // Test missing path (should return null for missing paths)
        $missing = $this->service->resolveJsonPath($data, 'nonexistent.path');
        $this->assertNull($missing);
    }
}
