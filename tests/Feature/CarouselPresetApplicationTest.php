<?php

namespace DigitalisStudios\SlickForms\Tests\Feature;

use DigitalisStudios\SlickForms\Livewire\FormBuilder;
use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;
use DigitalisStudios\SlickForms\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class CarouselPresetApplicationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_apply_image_gallery_preset_to_empty_carousel()
    {
        $form = CustomForm::create(['name' => 'Test Form']);

        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'carousel',
            'settings' => [],
        ]);

        Livewire::test(FormBuilder::class, ['formId' => $form->id])
            ->call('applyCarouselPreset', $carousel->id, 'image_gallery')
            ->assertHasNoErrors();

        $carousel->refresh();

        // Verify settings were applied
        $this->assertEquals('slide', $carousel->settings['effect']);
        $this->assertEquals(1, $carousel->settings['slidesPerView']);
        $this->assertTrue($carousel->settings['loop']);
        $this->assertTrue($carousel->settings['navigation']['enabled']);

        // Verify 5 slides were created
        $this->assertCount(5, $carousel->children);

        // Verify each slide has an image field
        foreach ($carousel->children as $slide) {
            $this->assertEquals('carousel_slide', $slide->element_type);
            $fields = CustomFormField::where('slick_form_layout_element_id', $slide->id)->get();
            $this->assertCount(1, $fields);
            $this->assertEquals('image', $fields->first()->field_type);
        }
    }

    /** @test */
    public function it_can_apply_testimonials_preset()
    {
        $form = CustomForm::create(['name' => 'Test Form']);

        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'carousel',
            'settings' => [],
        ]);

        Livewire::test(FormBuilder::class, ['formId' => $form->id])
            ->call('applyCarouselPreset', $carousel->id, 'testimonials');

        $carousel->refresh();

        // Verify fade effect
        $this->assertEquals('fade', $carousel->settings['effect']);
        $this->assertTrue($carousel->settings['fadeEffect']['crossFade']);

        // Verify autoplay
        $this->assertTrue($carousel->settings['autoplay']['enabled']);
        $this->assertEquals(5000, $carousel->settings['autoplay']['delay']);

        // Verify 3 slides
        $this->assertCount(3, $carousel->children);

        // Verify each slide has 3 fields (quote, name, photo)
        foreach ($carousel->children as $slide) {
            $fields = CustomFormField::where('slick_form_layout_element_id', $slide->id)->get();
            $this->assertCount(3, $fields);

            $fieldTypes = $fields->pluck('field_type')->toArray();
            $this->assertContains('textarea', $fieldTypes);
            $this->assertContains('text', $fieldTypes);
            $this->assertContains('image', $fieldTypes);
        }
    }

    /** @test */
    public function it_can_apply_product_showcase_preset_with_breakpoints()
    {
        $form = CustomForm::create(['name' => 'Test Form']);

        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'carousel',
            'settings' => [],
        ]);

        Livewire::test(FormBuilder::class, ['formId' => $form->id])
            ->call('applyCarouselPreset', $carousel->id, 'product_showcase');

        $carousel->refresh();

        // Verify breakpoints
        $this->assertTrue($carousel->settings['breakpoints']['enabled']);
        $this->assertEquals(1, $carousel->settings['breakpoints']['640_slidesPerView']);
        $this->assertEquals(2, $carousel->settings['breakpoints']['768_slidesPerView']);
        $this->assertEquals(3, $carousel->settings['breakpoints']['1024_slidesPerView']);

        // Verify 6 slides
        $this->assertCount(6, $carousel->children);

        // Verify product fields (image, name, description, price)
        $slide = $carousel->children->first();
        $fields = CustomFormField::where('slick_form_layout_element_id', $slide->id)->get();
        $this->assertCount(4, $fields);

        $fieldTypes = $fields->pluck('field_type')->toArray();
        $this->assertContains('image', $fieldTypes);
        $this->assertContains('text', $fieldTypes);
        $this->assertContains('textarea', $fieldTypes);
        $this->assertContains('number', $fieldTypes);
    }

    /** @test */
    public function it_replaces_existing_slides_when_applying_preset()
    {
        $form = CustomForm::create(['name' => 'Test Form']);

        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'carousel',
            'settings' => [],
        ]);

        // Create 3 existing slides manually
        for ($i = 0; $i < 3; $i++) {
            $slide = SlickFormLayoutElement::create([
                'slick_form_id' => $form->id,
                'parent_id' => $carousel->id,
                'element_type' => 'carousel_slide',
                'order' => $i,
            ]);

            CustomFormField::create([
                'slick_form_id' => $form->id,
                'slick_form_layout_element_id' => $slide->id,
                'field_type' => 'text',
                'name' => 'old_field_'.$i,
                'label' => 'Old Field',
            ]);
        }

        $this->assertCount(3, $carousel->children);

        // Apply preset
        Livewire::test(FormBuilder::class, ['formId' => $form->id])
            ->call('applyCarouselPreset', $carousel->id, 'image_gallery');

        $carousel->refresh();

        // Verify old slides were deleted and new ones created
        $this->assertCount(5, $carousel->children);

        // Verify new slides have image fields, not text fields
        foreach ($carousel->children as $slide) {
            $fields = CustomFormField::where('slick_form_layout_element_id', $slide->id)->get();
            $this->assertCount(1, $fields);
            $this->assertEquals('image', $fields->first()->field_type);

            // Verify this is a new field, not an old one
            $this->assertStringNotContainsString('old_field', $fields->first()->name);
        }

        // Verify carousel has exactly 5 image fields associated with new slides
        $imageFields = CustomFormField::whereIn('slick_form_layout_element_id', $carousel->children->pluck('id'))
            ->where('field_type', 'image')
            ->count();
        $this->assertEquals(5, $imageFields);
    }

    /** @test */
    public function it_applies_coverflow_preset_with_correct_settings()
    {
        $form = CustomForm::create(['name' => 'Test Form']);

        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'carousel',
            'settings' => [],
        ]);

        Livewire::test(FormBuilder::class, ['formId' => $form->id])
            ->call('applyCarouselPreset', $carousel->id, 'album_gallery');

        $carousel->refresh();

        // Verify coverflow effect
        $this->assertEquals('coverflow', $carousel->settings['effect']);
        $this->assertEquals(20, $carousel->settings['coverflowEffect']['rotate']);
        $this->assertEquals(200, $carousel->settings['coverflowEffect']['depth']);

        // Verify centered slides
        $this->assertTrue($carousel->settings['centeredSlides']);
        $this->assertEquals(3, $carousel->settings['slidesPerView']);

        // Verify 7 slides (minimum for coverflow)
        $this->assertCount(7, $carousel->children);
    }

    /** @test */
    public function it_applies_cube_preset_with_correct_settings()
    {
        $form = CustomForm::create(['name' => 'Test Form']);

        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'carousel',
            'settings' => [],
        ]);

        Livewire::test(FormBuilder::class, ['formId' => $form->id])
            ->call('applyCarouselPreset', $carousel->id, 'portfolio_showcase');

        $carousel->refresh();

        // Verify cube effect
        $this->assertEquals('cube', $carousel->settings['effect']);
        $this->assertTrue($carousel->settings['cubeEffect']['shadow']);
        $this->assertEquals(20, $carousel->settings['cubeEffect']['shadowOffset']);

        // Verify slides per view is 1 (required for cube)
        $this->assertEquals(1, $carousel->settings['slidesPerView']);

        // Verify 4 slides
        $this->assertCount(4, $carousel->children);
    }

    /** @test */
    public function it_applies_timeline_preset_with_free_mode()
    {
        $form = CustomForm::create(['name' => 'Test Form']);

        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'carousel',
            'settings' => [],
        ]);

        Livewire::test(FormBuilder::class, ['formId' => $form->id])
            ->call('applyCarouselPreset', $carousel->id, 'timeline');

        $carousel->refresh();

        // Verify free mode
        $this->assertTrue($carousel->settings['freeMode']['enabled']);
        $this->assertTrue($carousel->settings['freeMode']['sticky']);

        // Verify scrollbar
        $this->assertTrue($carousel->settings['scrollbar']['enabled']);
        $this->assertTrue($carousel->settings['scrollbar']['draggable']);

        // Verify 8 slides
        $this->assertCount(8, $carousel->children);
    }

    /** @test */
    public function it_clears_preset_selection_after_applying()
    {
        $form = CustomForm::create(['name' => 'Test Form']);

        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'carousel',
            'settings' => ['preset' => 'image_gallery'],
        ]);

        Livewire::test(FormBuilder::class, ['formId' => $form->id])
            ->call('applyCarouselPreset', $carousel->id, 'image_gallery');

        $carousel->refresh();

        // Verify preset was cleared (so dropdown shows "Custom")
        $this->assertEquals('', $carousel->settings['preset'] ?? '');
    }

    /** @test */
    public function it_does_nothing_when_applying_empty_preset_key()
    {
        $form = CustomForm::create(['name' => 'Test Form']);

        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'carousel',
            'settings' => ['some_setting' => 'value'],
        ]);

        $originalSettings = $carousel->settings;

        Livewire::test(FormBuilder::class, ['formId' => $form->id])
            ->call('applyCarouselPreset', $carousel->id, '');

        $carousel->refresh();

        // Verify settings unchanged
        $this->assertEquals($originalSettings, $carousel->settings);

        // Verify no slides created
        $this->assertCount(0, $carousel->children);
    }

    /** @test */
    public function it_shows_error_for_invalid_preset_key()
    {
        $form = CustomForm::create(['name' => 'Test Form']);

        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'carousel',
            'settings' => [],
        ]);

        Livewire::test(FormBuilder::class, ['formId' => $form->id])
            ->call('applyCarouselPreset', $carousel->id, 'nonexistent_preset');

        $carousel->refresh();

        // Verify carousel was NOT modified by invalid preset
        $this->assertEmpty($carousel->settings);
        $this->assertCount(0, $carousel->children);
    }

    /** @test */
    public function it_creates_unique_field_names_for_each_slide()
    {
        $form = CustomForm::create(['name' => 'Test Form']);

        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'carousel',
            'settings' => [],
        ]);

        Livewire::test(FormBuilder::class, ['formId' => $form->id])
            ->call('applyCarouselPreset', $carousel->id, 'image_gallery');

        $carousel->refresh();

        // Get all field names
        $fieldNames = [];
        foreach ($carousel->children as $slide) {
            $fields = CustomFormField::where('slick_form_layout_element_id', $slide->id)->get();
            foreach ($fields as $field) {
                $fieldNames[] = $field->name;
            }
        }

        // Verify all names are unique
        $this->assertCount(5, $fieldNames);
        $this->assertEquals(5, count(array_unique($fieldNames)));

        // Verify naming pattern (slide_0_image_XXXXXX)
        foreach ($fieldNames as $name) {
            $this->assertMatchesRegularExpression('/^slide_\d+_[a-z_]+_[a-zA-Z0-9]{6}$/', $name);
        }
    }
}
