<?php

namespace DigitalisStudios\SlickForms\Tests\Unit;

use DigitalisStudios\SlickForms\LayoutElementTypes\CarouselType;
use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;
use DigitalisStudios\SlickForms\Services\LayoutElementRegistry;
use DigitalisStudios\SlickForms\Tests\TestCase;

/**
 * Test carousel layout element functionality
 */
class CarouselTypeTest extends TestCase
{
    protected CarouselType $carouselType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->carouselType = new CarouselType;
    }

    /** @test */
    public function carousel_type_has_correct_metadata()
    {
        $this->assertEquals('carousel', $this->carouselType->getType());
        $this->assertEquals('Carousel', $this->carouselType->getLabel());
        $this->assertEquals('bi-collection-play', $this->carouselType->getIcon());
    }

    /** @test */
    public function carousel_allows_all_child_types()
    {
        $allowedChildren = $this->carouselType->getAllowedChildren();

        $this->assertIsArray($allowedChildren);
        $this->assertContains('*', $allowedChildren);
    }

    /** @test */
    public function carousel_defines_custom_property_tabs()
    {
        $tabs = $this->carouselType->getPropertyTabs();

        $this->assertIsArray($tabs);
        $this->assertArrayHasKey('navigation', $tabs);
        $this->assertArrayHasKey('pagination', $tabs);
        $this->assertArrayHasKey('effects', $tabs);
        $this->assertArrayHasKey('autoplay', $tabs);
        $this->assertArrayHasKey('interaction', $tabs);
        $this->assertArrayHasKey('scrollbar', $tabs);
        $this->assertArrayHasKey('accessibility', $tabs);

        // Check tab structure
        $this->assertEquals('Navigation', $tabs['navigation']['label']);
        $this->assertEquals('bi-arrow-left-right', $tabs['navigation']['icon']);
        $this->assertEquals(36, $tabs['navigation']['order']);

        // Check interaction tab
        $this->assertEquals('Interaction', $tabs['interaction']['label']);
        $this->assertEquals('bi-hand-index', $tabs['interaction']['icon']);
        $this->assertEquals(40, $tabs['interaction']['order']);
    }

    /** @test */
    public function carousel_config_schema_includes_core_settings()
    {
        $schema = $this->carouselType->getConfigSchema();

        // Core settings
        $this->assertArrayHasKey('direction', $schema);
        $this->assertArrayHasKey('speed', $schema);
        $this->assertArrayHasKey('loop', $schema);
        $this->assertArrayHasKey('rewind', $schema);
        $this->assertArrayHasKey('initialSlide', $schema);
        $this->assertArrayHasKey('slidesPerView', $schema);
        $this->assertArrayHasKey('slidesPerGroup', $schema);
        $this->assertArrayHasKey('spaceBetween', $schema);
        $this->assertArrayHasKey('centeredSlides', $schema);
        $this->assertArrayHasKey('autoHeight', $schema);
        $this->assertArrayHasKey('grabCursor', $schema);

        // Verify direction options
        $this->assertEquals('select', $schema['direction']['type']);
        $this->assertArrayHasKey('horizontal', $schema['direction']['options']);
        $this->assertArrayHasKey('vertical', $schema['direction']['options']);
        $this->assertEquals('horizontal', $schema['direction']['default']);

        // Verify new parameters
        $this->assertEquals(0, $schema['initialSlide']['default']);
        $this->assertEquals(1, $schema['slidesPerGroup']['default']);
        $this->assertFalse($schema['rewind']['default']);
    }

    /** @test */
    public function carousel_config_schema_includes_navigation_settings()
    {
        $schema = $this->carouselType->getConfigSchema();

        $this->assertArrayHasKey('navigation_enabled', $schema);
        $this->assertArrayHasKey('navigation_hideOnClick', $schema);

        // Verify navigation enabled setting
        $this->assertEquals('switch', $schema['navigation_enabled']['type']);
        $this->assertEquals('navigation', $schema['navigation_enabled']['tab']);
        $this->assertEquals('settings.navigation.enabled', $schema['navigation_enabled']['target']);
        $this->assertFalse($schema['navigation_enabled']['default']);
    }

    /** @test */
    public function carousel_config_schema_includes_pagination_settings()
    {
        $schema = $this->carouselType->getConfigSchema();

        $this->assertArrayHasKey('pagination_enabled', $schema);
        $this->assertArrayHasKey('pagination_type', $schema);
        $this->assertArrayHasKey('pagination_clickable', $schema);
        $this->assertArrayHasKey('pagination_dynamicBullets', $schema);

        // Verify pagination type options
        $this->assertEquals('select', $schema['pagination_type']['type']);
        $this->assertArrayHasKey('bullets', $schema['pagination_type']['options']);
        $this->assertArrayHasKey('fraction', $schema['pagination_type']['options']);
        $this->assertArrayHasKey('progressbar', $schema['pagination_type']['options']);
        $this->assertEquals('bullets', $schema['pagination_type']['default']);
    }

    /** @test */
    public function carousel_config_schema_includes_effect_settings()
    {
        $schema = $this->carouselType->getConfigSchema();

        $this->assertArrayHasKey('effect', $schema);

        // Verify effect options
        $options = $schema['effect']['options'];
        $this->assertArrayHasKey('slide', $options);
        $this->assertArrayHasKey('fade', $options);
        $this->assertArrayHasKey('cube', $options);
        $this->assertArrayHasKey('coverflow', $options);
        $this->assertArrayHasKey('flip', $options);
        $this->assertArrayHasKey('cards', $options);
        $this->assertArrayHasKey('creative', $options);

        // Effect-specific settings
        $this->assertArrayHasKey('fadeEffect_crossFade', $schema);
        $this->assertArrayHasKey('cubeEffect_shadow', $schema);
        $this->assertArrayHasKey('coverflowEffect_rotate', $schema);
    }

    /** @test */
    public function carousel_config_schema_includes_autoplay_settings()
    {
        $schema = $this->carouselType->getConfigSchema();

        $this->assertArrayHasKey('autoplay_enabled', $schema);
        $this->assertArrayHasKey('autoplay_delay', $schema);
        $this->assertArrayHasKey('autoplay_pauseOnMouseEnter', $schema);
        $this->assertArrayHasKey('autoplay_disableOnInteraction', $schema);

        // Verify autoplay delay constraints
        $this->assertEquals('number', $schema['autoplay_delay']['type']);
        $this->assertEquals(3000, $schema['autoplay_delay']['default']);
        $this->assertEquals(500, $schema['autoplay_delay']['min']);
        $this->assertEquals(10000, $schema['autoplay_delay']['max']);
    }

    /** @test */
    public function carousel_config_schema_includes_accessibility_settings()
    {
        $schema = $this->carouselType->getConfigSchema();

        $this->assertArrayHasKey('a11y_enabled', $schema);
        $this->assertArrayHasKey('a11y_prevSlideMessage', $schema);
        $this->assertArrayHasKey('a11y_nextSlideMessage', $schema);
        $this->assertArrayHasKey('a11y_firstSlideMessage', $schema);
        $this->assertArrayHasKey('a11y_lastSlideMessage', $schema);

        // Verify accessibility is enabled by default
        $this->assertTrue($schema['a11y_enabled']['default']);
        $this->assertEquals('Previous slide', $schema['a11y_prevSlideMessage']['default']);
    }

    /** @test */
    public function carousel_config_schema_includes_advanced_settings()
    {
        $schema = $this->carouselType->getConfigSchema();

        // Keyboard control
        $this->assertArrayHasKey('keyboard_enabled', $schema);
        $this->assertArrayHasKey('keyboard_onlyInViewport', $schema);

        // Mousewheel control
        $this->assertArrayHasKey('mousewheel_enabled', $schema);
        $this->assertArrayHasKey('mousewheel_invert', $schema);
        $this->assertArrayHasKey('mousewheel_sensitivity', $schema);

        // Zoom module
        $this->assertArrayHasKey('zoom_enabled', $schema);
        $this->assertArrayHasKey('zoom_maxRatio', $schema);
        $this->assertArrayHasKey('zoom_minRatio', $schema);
        $this->assertArrayHasKey('zoom_toggle', $schema);

        // Parallax
        $this->assertArrayHasKey('parallax_enabled', $schema);

        // Free mode
        $this->assertArrayHasKey('freeMode_enabled', $schema);
        $this->assertArrayHasKey('freeMode_momentum', $schema);
        $this->assertArrayHasKey('freeMode_sticky', $schema);

        // Grid layout
        $this->assertArrayHasKey('grid_rows', $schema);
        $this->assertArrayHasKey('grid_fill', $schema);

        // Lazy loading
        $this->assertArrayHasKey('lazy', $schema);
        $this->assertFalse($schema['lazy']['default']);
    }

    /** @test */
    public function carousel_config_schema_includes_interaction_controls()
    {
        $schema = $this->carouselType->getConfigSchema();

        $this->assertArrayHasKey('allowSlideNext', $schema);
        $this->assertArrayHasKey('allowSlidePrev', $schema);
        $this->assertArrayHasKey('allowTouchMove', $schema);
        $this->assertArrayHasKey('slideToClickedSlide', $schema);

        // Verify defaults
        $this->assertTrue($schema['allowSlideNext']['default']);
        $this->assertTrue($schema['allowSlidePrev']['default']);
        $this->assertTrue($schema['allowTouchMove']['default']);
        $this->assertFalse($schema['slideToClickedSlide']['default']);

        // Verify tab assignment
        $this->assertEquals('interaction', $schema['allowSlideNext']['tab']);
        $this->assertEquals('interaction', $schema['allowSlidePrev']['tab']);
        $this->assertEquals('interaction', $schema['allowTouchMove']['tab']);
        $this->assertEquals('interaction', $schema['slideToClickedSlide']['tab']);
    }

    /** @test */
    public function carousel_config_schema_includes_responsive_breakpoints()
    {
        $schema = $this->carouselType->getConfigSchema();

        $this->assertArrayHasKey('breakpoints_enabled', $schema);

        // 640px (mobile)
        $this->assertArrayHasKey('breakpoint_640_slidesPerView', $schema);
        $this->assertArrayHasKey('breakpoint_640_spaceBetween', $schema);

        // 768px (tablet)
        $this->assertArrayHasKey('breakpoint_768_slidesPerView', $schema);
        $this->assertArrayHasKey('breakpoint_768_spaceBetween', $schema);

        // 1024px (desktop)
        $this->assertArrayHasKey('breakpoint_1024_slidesPerView', $schema);
        $this->assertArrayHasKey('breakpoint_1024_spaceBetween', $schema);

        // 1280px (large desktop)
        $this->assertArrayHasKey('breakpoint_1280_slidesPerView', $schema);
        $this->assertArrayHasKey('breakpoint_1280_spaceBetween', $schema);
    }

    /** @test */
    public function carousel_config_schema_includes_builder_mode_setting()
    {
        $schema = $this->carouselType->getConfigSchema();

        $this->assertArrayHasKey('builderMode', $schema);
        $this->assertEquals('select', $schema['builderMode']['type']);
        $this->assertEquals('preview', $schema['builderMode']['default']);
        $this->assertArrayHasKey('list', $schema['builderMode']['options']);
        $this->assertArrayHasKey('preview', $schema['builderMode']['options']);
    }

    /** @test */
    public function carousel_element_can_be_created_with_default_settings()
    {
        $form = CustomForm::factory()->create();

        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'carousel',
            'order' => 1,
            'settings' => [
                'direction' => 'horizontal',
                'speed' => 300,
                'loop' => false,
                'slidesPerView' => '1',
                'spaceBetween' => 0,
            ],
        ]);

        $this->assertEquals('carousel', $carousel->element_type);
        $this->assertEquals('horizontal', $carousel->settings['direction']);
        $this->assertEquals(300, $carousel->settings['speed']);
        $this->assertFalse($carousel->settings['loop']);
    }

    /** @test */
    public function carousel_element_can_have_navigation_enabled()
    {
        $form = CustomForm::factory()->create();

        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'carousel',
            'order' => 1,
            'settings' => [
                'navigation' => [
                    'enabled' => true,
                    'hideOnClick' => false,
                ],
            ],
        ]);

        $this->assertTrue($carousel->settings['navigation']['enabled']);
        $this->assertFalse($carousel->settings['navigation']['hideOnClick']);
    }

    /** @test */
    public function carousel_element_can_have_pagination_configured()
    {
        $form = CustomForm::factory()->create();

        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'carousel',
            'order' => 1,
            'settings' => [
                'pagination' => [
                    'enabled' => true,
                    'type' => 'bullets',
                    'clickable' => true,
                    'dynamicBullets' => false,
                ],
            ],
        ]);

        $pagination = $carousel->settings['pagination'];
        $this->assertTrue($pagination['enabled']);
        $this->assertEquals('bullets', $pagination['type']);
        $this->assertTrue($pagination['clickable']);
        $this->assertFalse($pagination['dynamicBullets']);
    }

    /** @test */
    public function carousel_element_can_have_autoplay_configured()
    {
        $form = CustomForm::factory()->create();

        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'carousel',
            'order' => 1,
            'settings' => [
                'autoplay' => [
                    'enabled' => true,
                    'delay' => 5000,
                    'pauseOnMouseEnter' => true,
                    'disableOnInteraction' => false,
                ],
            ],
        ]);

        $autoplay = $carousel->settings['autoplay'];
        $this->assertTrue($autoplay['enabled']);
        $this->assertEquals(5000, $autoplay['delay']);
        $this->assertTrue($autoplay['pauseOnMouseEnter']);
        $this->assertFalse($autoplay['disableOnInteraction']);
    }

    /** @test */
    public function carousel_element_can_have_effects_configured()
    {
        $form = CustomForm::factory()->create();

        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'carousel',
            'order' => 1,
            'settings' => [
                'effect' => 'fade',
                'fadeEffect' => [
                    'crossFade' => true,
                ],
            ],
        ]);

        $this->assertEquals('fade', $carousel->settings['effect']);
        $this->assertTrue($carousel->settings['fadeEffect']['crossFade']);
    }

    /** @test */
    public function carousel_element_can_have_responsive_breakpoints()
    {
        $form = CustomForm::factory()->create();

        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'carousel',
            'order' => 1,
            'settings' => [
                'breakpoints' => [
                    'enabled' => true,
                    '640' => ['slidesPerView' => '1', 'spaceBetween' => 10],
                    '768' => ['slidesPerView' => '2', 'spaceBetween' => 20],
                    '1024' => ['slidesPerView' => '3', 'spaceBetween' => 30],
                ],
            ],
        ]);

        $breakpoints = $carousel->settings['breakpoints'];
        $this->assertTrue($breakpoints['enabled']);
        $this->assertEquals('2', $breakpoints['768']['slidesPerView']);
        $this->assertEquals(20, $breakpoints['768']['spaceBetween']);
    }

    /** @test */
    public function carousel_can_have_slide_children()
    {
        $form = CustomForm::factory()->create();

        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'carousel',
            'order' => 1,
        ]);

        // Create slide containers (regular containers as direct children)
        $slide1 = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'parent_id' => $carousel->id,
            'element_type' => 'container',
            'order' => 0,
        ]);

        $slide2 = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'parent_id' => $carousel->id,
            'element_type' => 'container',
            'order' => 1,
        ]);

        $carousel->refresh();
        $slides = $carousel->children()->orderBy('order')->get();

        $this->assertCount(2, $slides);
        $this->assertEquals('container', $slides[0]->element_type);
        $this->assertEquals('container', $slides[1]->element_type);
    }

    /** @test */
    public function carousel_type_is_registered_in_element_registry()
    {
        $registry = app(LayoutElementRegistry::class);

        $this->assertTrue($registry->has('carousel'));

        $carouselType = $registry->get('carousel');
        $this->assertInstanceOf(CarouselType::class, $carouselType);
    }

    /** @test */
    public function carousel_renders_with_correct_view()
    {
        $form = CustomForm::factory()->create();

        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'carousel',
            'order' => 1,
        ]);

        $html = $this->carouselType->render($carousel, '');

        // Should contain Swiper carousel markup
        $this->assertStringContainsString('swiper', $html);
        $this->assertStringContainsString('carousel-'.$carousel->id, $html);
    }

    /** @test */
    public function carousel_builder_view_uses_correct_template()
    {
        $form = CustomForm::factory()->create();

        $carousel = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'element_type' => 'carousel',
            'order' => 1,
        ]);

        // Builder view requires Livewire context, so we just verify the method exists
        // and returns a view reference. Full rendering is tested in feature tests.
        $this->assertTrue(method_exists($this->carouselType, 'renderBuilder'));

        // Verify the view file exists
        $viewPath = resource_path('views/vendor/slick-forms/livewire/components/elements/builder/carousel.blade.php');
        if (! file_exists($viewPath)) {
            // Check package view path
            $packageViewPath = __DIR__.'/../../src/resources/views/livewire/components/elements/builder/carousel.blade.php';
            $this->assertFileExists($packageViewPath);
        }
    }
}
