<?php

namespace DigitalisStudios\SlickForms\Tests\Feature;

use DigitalisStudios\SlickForms\Livewire\FormBuilder;
use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;
use DigitalisStudios\SlickForms\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

/**
 * Test carousel integration with FormBuilder component
 */
class CarouselBuilderIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected CustomForm $form;

    protected function setUp(): void
    {
        parent::setUp();

        $this->form = CustomForm::create([
            'name' => 'Test Form with Carousel',
            'description' => 'Test form for carousel functionality',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function carousel_can_be_added_to_form()
    {
        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('addLayoutElement', 'carousel', null, [], 0)
            ->assertSuccessful();

        $carousel = SlickFormLayoutElement::where('slick_form_id', $this->form->id)
            ->where('element_type', 'carousel')
            ->first();

        $this->assertNotNull($carousel);
        $this->assertEquals('carousel', $carousel->element_type);
    }

    /** @test */
    public function carousel_has_default_slides_on_creation()
    {
        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('addLayoutElement', 'carousel', null, [], 0);

        $carousel = SlickFormLayoutElement::where('slick_form_id', $this->form->id)
            ->where('element_type', 'carousel')
            ->first();

        // Should have 7 default slides created (Album Gallery preset)
        $slides = $carousel->children()->orderBy('order')->get();
        $this->assertCount(7, $slides);

        // Each slide should be a carousel_slide
        foreach ($slides as $slide) {
            $this->assertEquals('carousel_slide', $slide->element_type);
        }

        // Each slide should have an image field
        foreach ($slides as $slide) {
            $imageField = $slide->fields()->where('field_type', 'image')->first();
            $this->assertNotNull($imageField);
        }
    }

    /** @test */
    public function slides_can_be_added_to_carousel()
    {
        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $this->form->id,
            'element_type' => 'carousel',
            'order' => 1,
        ]);

        // Add slide directly using the layout element creation
        SlickFormLayoutElement::create([
            'slick_form_id' => $this->form->id,
            'parent_id' => $carousel->id,
            'element_type' => 'carousel_slide',
            'order' => 0,
            'settings' => [
                'slide_title' => 'Slide 1',
                'slide_icon' => '',
                'background_color' => '',
                'text_alignment' => '',
            ],
        ]);

        $carousel->refresh();
        $slides = $carousel->children;

        $this->assertGreaterThan(0, $slides->count(), 'Carousel should have at least one slide');

        // Latest slide should be a carousel_slide
        $latestSlide = $slides->sortByDesc('order')->first();
        $this->assertNotNull($latestSlide, 'Latest slide should not be null');
        $this->assertEquals('carousel_slide', $latestSlide->element_type);
        $this->assertEquals($carousel->id, $latestSlide->parent_id);
    }

    /** @test */
    public function slide_can_be_deleted_from_carousel()
    {
        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $this->form->id,
            'element_type' => 'carousel',
            'order' => 1,
        ]);

        $slide = SlickFormLayoutElement::create([
            'slick_form_id' => $this->form->id,
            'parent_id' => $carousel->id,
            'element_type' => 'container',
            'order' => 0,
        ]);

        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('deleteLayoutElement', $slide->id)
            ->assertSuccessful();

        $this->assertDatabaseMissing('slick_form_layout_elements', [
            'id' => $slide->id,
        ]);
    }

    /** @test */
    public function fields_can_be_added_to_carousel_slides()
    {
        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $this->form->id,
            'element_type' => 'carousel',
            'order' => 1,
        ]);

        $slide = SlickFormLayoutElement::create([
            'slick_form_id' => $this->form->id,
            'parent_id' => $carousel->id,
            'element_type' => 'container',
            'order' => 0,
        ]);

        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('addField', 'text', $slide->id, false, 0)
            ->assertSuccessful();

        $field = CustomFormField::where('slick_form_layout_element_id', $slide->id)
            ->where('field_type', 'text')
            ->first();

        $this->assertNotNull($field);
        $this->assertEquals('text', $field->field_type);
        $this->assertEquals($slide->id, $field->slick_form_layout_element_id);
    }

    /** @test */
    public function carousel_can_be_selected_for_editing()
    {
        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $this->form->id,
            'element_type' => 'carousel',
            'order' => 1,
            'settings' => [
                'navigation' => [
                    'enabled' => false,
                ],
            ],
        ]);

        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('selectElement', $carousel->id)
            ->assertSet('selectedElement.id', $carousel->id)
            ->assertSuccessful();
    }

    /** @test */
    public function carousel_settings_are_stored_in_json()
    {
        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $this->form->id,
            'element_type' => 'carousel',
            'order' => 1,
            'settings' => [
                'direction' => 'vertical',
                'speed' => 500,
                'loop' => true,
                'pagination' => [
                    'enabled' => true,
                    'type' => 'bullets',
                ],
                'autoplay' => [
                    'enabled' => true,
                    'delay' => 5000,
                ],
            ],
        ]);

        $carousel->refresh();

        // Verify settings are stored correctly
        $this->assertEquals('vertical', $carousel->settings['direction']);
        $this->assertEquals(500, $carousel->settings['speed']);
        $this->assertTrue($carousel->settings['loop']);
        $this->assertTrue($carousel->settings['pagination']['enabled']);
        $this->assertEquals('bullets', $carousel->settings['pagination']['type']);
        $this->assertTrue($carousel->settings['autoplay']['enabled']);
        $this->assertEquals(5000, $carousel->settings['autoplay']['delay']);
    }

    /** @test */
    public function carousel_builder_mode_can_be_switched()
    {
        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $this->form->id,
            'element_type' => 'carousel',
            'order' => 1,
            'settings' => [
                'builderMode' => 'list',
            ],
        ]);

        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('updateElementSetting', $carousel->id, 'builderMode', 'preview')
            ->assertSuccessful();

        $carousel->refresh();
        $this->assertEquals('preview', $carousel->settings['builderMode']);
    }

    /** @test */
    public function carousel_with_slides_can_be_selected_in_builder()
    {
        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $this->form->id,
            'element_type' => 'carousel',
            'order' => 1,
        ]);

        $slide = SlickFormLayoutElement::create([
            'slick_form_id' => $this->form->id,
            'parent_id' => $carousel->id,
            'element_type' => 'container',
            'order' => 0,
        ]);

        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('selectElement', $carousel->id)
            ->assertSet('selectedElement.id', $carousel->id)
            ->assertSuccessful();
    }

    /** @test */
    public function carousel_slide_can_be_selected_in_builder()
    {
        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $this->form->id,
            'element_type' => 'carousel',
            'order' => 1,
        ]);

        $slide = SlickFormLayoutElement::create([
            'slick_form_id' => $this->form->id,
            'parent_id' => $carousel->id,
            'element_type' => 'container',
            'order' => 0,
        ]);

        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('selectElement', $slide->id)
            ->assertSet('selectedElement.id', $slide->id)
            ->assertSuccessful();
    }

    /** @test */
    public function carousel_slides_can_be_reordered()
    {
        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $this->form->id,
            'element_type' => 'carousel',
            'order' => 1,
        ]);

        $slide1 = SlickFormLayoutElement::create([
            'slick_form_id' => $this->form->id,
            'parent_id' => $carousel->id,
            'element_type' => 'container',
            'order' => 0,
        ]);

        $slide2 = SlickFormLayoutElement::create([
            'slick_form_id' => $this->form->id,
            'parent_id' => $carousel->id,
            'element_type' => 'container',
            'order' => 1,
        ]);

        $slide3 = SlickFormLayoutElement::create([
            'slick_form_id' => $this->form->id,
            'parent_id' => $carousel->id,
            'element_type' => 'container',
            'order' => 2,
        ]);

        // Reorder: slide3, slide1, slide2
        $newOrder = [
            ['type' => 'element', 'id' => $slide3->id],
            ['type' => 'element', 'id' => $slide1->id],
            ['type' => 'element', 'id' => $slide2->id],
        ];

        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('updateChildrenOrderInParent', $carousel->id, $newOrder)
            ->assertSuccessful();

        $slide1->refresh();
        $slide2->refresh();
        $slide3->refresh();

        $this->assertEquals(1, $slide1->order);
        $this->assertEquals(2, $slide2->order);
        $this->assertEquals(0, $slide3->order);
    }

    /** @test */
    public function carousel_can_be_deleted()
    {
        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $this->form->id,
            'element_type' => 'carousel',
            'order' => 1,
        ]);

        Livewire::test(FormBuilder::class, ['formId' => $this->form->id])
            ->call('deleteLayoutElement', $carousel->id)
            ->assertSuccessful();

        $this->assertDatabaseMissing('slick_form_layout_elements', ['id' => $carousel->id]);
    }

    /** @test */
    public function carousel_type_provides_custom_property_tabs()
    {
        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $this->form->id,
            'element_type' => 'carousel',
            'order' => 1,
        ]);

        // Verify that carousel type defines custom tabs
        $carouselType = app(\DigitalisStudios\SlickForms\Services\LayoutElementRegistry::class)->get('carousel');
        $tabs = $carouselType->getPropertyTabs();

        $this->assertArrayHasKey('navigation', $tabs);
        $this->assertArrayHasKey('pagination', $tabs);
        $this->assertArrayHasKey('effects', $tabs);
        $this->assertArrayHasKey('autoplay', $tabs);
        $this->assertArrayHasKey('scrollbar', $tabs);
        $this->assertArrayHasKey('accessibility', $tabs);
    }
}
