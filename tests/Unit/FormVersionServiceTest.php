<?php

namespace DigitalisStudios\SlickForms\Tests\Unit;

use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Models\FormVersion;
use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;
use DigitalisStudios\SlickForms\Services\FormVersionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase;

class FormVersionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FormVersionService $versionService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->versionService = new FormVersionService;

        // Run migrations
        $this->loadMigrationsFrom(__DIR__.'/../../src/database/migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            'Livewire\LivewireServiceProvider',
            'DigitalisStudios\SlickForms\SlickFormsServiceProvider',
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup in-memory SQLite database
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /** @test */
    public function it_creates_a_version_snapshot()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'is_active' => true,
        ]);

        $version = $this->versionService->createVersion($form);

        $this->assertInstanceOf(FormVersion::class, $version);
        $this->assertEquals(1, $version->version_number);
        $this->assertEquals($form->id, $version->form_id);
        $this->assertNotNull($version->form_snapshot);
    }

    /** @test */
    public function it_increments_version_numbers_correctly()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'is_active' => true,
        ]);

        $version1 = $this->versionService->createVersion($form);
        $version2 = $this->versionService->createVersion($form);
        $version3 = $this->versionService->createVersion($form);

        $this->assertEquals(1, $version1->version_number);
        $this->assertEquals(2, $version2->version_number);
        $this->assertEquals(3, $version3->version_number);
    }

    /** @test */
    public function it_builds_form_snapshot_with_all_data()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'is_active' => true,
        ]);

        $field = CustomFormField::create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'test_field',
            'label' => 'Test Field',
            'order' => 0,
        ]);

        $snapshot = $this->versionService->buildFormSnapshot($form);

        $this->assertArrayHasKey('form', $snapshot);
        $this->assertArrayHasKey('fields', $snapshot);
        $this->assertArrayHasKey('layout_elements', $snapshot);
        $this->assertArrayHasKey('pages', $snapshot);
        $this->assertEquals('Test Form', $snapshot['form']['name']);
        $this->assertCount(1, $snapshot['fields']);
    }

    /** @test */
    public function it_captures_field_data_in_snapshot()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'is_active' => true,
        ]);

        $field = CustomFormField::create([
            'slick_form_id' => $form->id,
            'field_type' => 'email',
            'name' => 'email_field',
            'label' => 'Email Address',
            'placeholder' => 'Enter email',
            'help_text' => 'Your email',
            'validation_rules' => ['required', 'email'],
            'order' => 0,
        ]);

        $snapshot = $this->versionService->buildFormSnapshot($form);

        $this->assertCount(1, $snapshot['fields']);
        $this->assertEquals('email', $snapshot['fields'][0]['field_type']);
        $this->assertEquals('email_field', $snapshot['fields'][0]['name']);
        $this->assertEquals('Email Address', $snapshot['fields'][0]['label']);
    }

    /** @test */
    public function it_captures_layout_elements_in_snapshot()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'is_active' => true,
        ]);

        $container = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'container',
            'settings' => ['fluid' => false],
            'order' => 0,
        ]);

        $snapshot = $this->versionService->buildFormSnapshot($form);

        $this->assertCount(1, $snapshot['layout_elements']);
        $this->assertEquals('container', $snapshot['layout_elements'][0]['element_type']);
    }

    /** @test */
    public function it_restores_form_to_previous_version()
    {
        $form = CustomForm::create([
            'name' => 'Original Form',
            'description' => 'Original Description',
            'is_active' => true,
        ]);

        $field = CustomFormField::create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'original_field',
            'label' => 'Original Field',
            'order' => 0,
        ]);

        $version = $this->versionService->createVersion($form);

        // Modify form
        $form->update(['name' => 'Modified Form']);
        $field->delete();

        // Restore
        $this->versionService->restoreVersion($form, $version);

        $form->refresh();
        $this->assertEquals('Original Form', $form->name);
        $this->assertCount(1, $form->fields);
    }

    /** @test */
    public function it_gets_version_history_for_form()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'is_active' => true,
        ]);

        $this->versionService->createVersion($form);
        $this->versionService->createVersion($form);
        $this->versionService->createVersion($form);

        $history = $this->versionService->getVersionHistory($form);

        $this->assertCount(3, $history);
        $this->assertEquals(3, $history[0]->version_number); // Newest first
        $this->assertEquals(2, $history[1]->version_number);
        $this->assertEquals(1, $history[2]->version_number);
    }

    /** @test */
    public function it_compares_two_versions()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'is_active' => true,
        ]);

        $version1 = $this->versionService->createVersion($form);

        // Add a field
        CustomFormField::create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'new_field',
            'label' => 'New Field',
            'order' => 0,
        ]);

        $version2 = $this->versionService->createVersion($form);

        $differences = $this->versionService->compareVersions($version1, $version2);

        $this->assertEquals(0, $differences['field_count']['from']);
        $this->assertEquals(1, $differences['field_count']['to']);
    }

    /** @test */
    public function it_deletes_version_without_submissions()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'is_active' => true,
        ]);

        $version = $this->versionService->createVersion($form);

        $result = $this->versionService->deleteVersion($version);

        $this->assertTrue($result);
        $this->assertNull(FormVersion::find($version->id));
    }

    /** @test */
    public function it_gets_latest_version()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'is_active' => true,
        ]);

        $version1 = $this->versionService->createVersion($form);
        $version2 = $this->versionService->createVersion($form);
        $version3 = $this->versionService->createVersion($form);

        $latest = $this->versionService->getLatestVersion($form);

        $this->assertEquals($version3->id, $latest->id);
        $this->assertEquals(3, $latest->version_number);
    }

    /** @test */
    public function it_detects_form_changes()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'is_active' => true,
        ]);

        $this->versionService->createVersion($form);

        // No changes yet
        $this->assertFalse($this->versionService->hasChanges($form));

        // Make a change
        $form->update(['name' => 'Modified Form']);

        // Should detect changes
        $this->assertTrue($this->versionService->hasChanges($form));
    }

    /** @test */
    public function it_generates_auto_change_summary()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'is_active' => true,
        ]);

        CustomFormField::create([
            'slick_form_id' => $form->id,
            'field_type' => 'text',
            'name' => 'field1',
            'label' => 'Field 1',
            'order' => 0,
        ]);

        $version = $this->versionService->createVersion($form);

        $this->assertStringContainsString('Version 1', $version->change_summary);
        $this->assertStringContainsString('1 fields', $version->change_summary);
    }

    /** @test */
    public function it_accepts_custom_version_name_and_summary()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'is_active' => true,
        ]);

        $version = $this->versionService->createVersion(
            $form,
            null,
            'Initial Release',
            'First public version of the form'
        );

        $this->assertEquals('Initial Release', $version->version_name);
        $this->assertEquals('First public version of the form', $version->change_summary);
    }

    /** @test */
    public function it_restores_nested_layout_elements()
    {
        $form = CustomForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'is_active' => true,
        ]);

        $container = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'container',
            'order' => 0,
        ]);

        $row = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'parent_id' => $container->id,
            'element_type' => 'row',
            'order' => 0,
        ]);

        $version = $this->versionService->createVersion($form);

        // Verify snapshot contains both elements
        $this->assertCount(2, $version->form_snapshot['layout_elements']);

        // Delete all elements
        $form->layoutElements()->delete();

        // Restore
        $this->versionService->restoreVersion($form, $version);

        $form->refresh();
        // Note: $form->layoutElements only returns top-level elements (those with parent_id = null)
        // We need to query all elements directly to test nested restoration
        $this->assertCount(2, SlickFormLayoutElement::where('slick_form_id', $form->id)->get());

        $restoredContainer = SlickFormLayoutElement::where('slick_form_id', $form->id)->where('element_type', 'container')->first();
        $restoredRow = SlickFormLayoutElement::where('slick_form_id', $form->id)->where('element_type', 'row')->first();

        $this->assertNotNull($restoredContainer);
        $this->assertNotNull($restoredRow);
        $this->assertEquals($restoredContainer->id, $restoredRow->parent_id);
    }
}
