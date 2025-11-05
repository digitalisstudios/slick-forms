<?php

namespace DigitalisStudios\SlickForms\Tests\Unit;

use DigitalisStudios\SlickForms\Services\CarouselPresetService;
use Orchestra\Testbench\TestCase;

class CarouselPresetServiceTest extends TestCase
{
    protected CarouselPresetService $presetService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->presetService = new CarouselPresetService;
    }

    /** @test */
    public function it_returns_all_presets()
    {
        $presets = $this->presetService->getPresets();

        $this->assertIsArray($presets);
        $this->assertCount(10, $presets);
    }

    /** @test */
    public function it_has_required_preset_keys()
    {
        $presets = $this->presetService->getPresets();
        $expectedKeys = [
            'image_gallery',
            'testimonials',
            'product_showcase',
            'hero_slider',
            'album_gallery',
            'portfolio_showcase',
            'thumbnail_gallery',
            'content_cards',
            'timeline',
            'before_after',
        ];

        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $presets);
        }
    }

    /** @test */
    public function each_preset_has_required_structure()
    {
        $presets = $this->presetService->getPresets();

        foreach ($presets as $key => $preset) {
            $this->assertArrayHasKey('label', $preset, "Preset '{$key}' missing 'label'");
            $this->assertArrayHasKey('description', $preset, "Preset '{$key}' missing 'description'");
            $this->assertArrayHasKey('icon', $preset, "Preset '{$key}' missing 'icon'");
            $this->assertArrayHasKey('category', $preset, "Preset '{$key}' missing 'category'");
            $this->assertArrayHasKey('slideCount', $preset, "Preset '{$key}' missing 'slideCount'");
            $this->assertArrayHasKey('slideTemplate', $preset, "Preset '{$key}' missing 'slideTemplate'");
            $this->assertArrayHasKey('settings', $preset, "Preset '{$key}' missing 'settings'");
        }
    }

    /** @test */
    public function each_preset_has_valid_slide_count()
    {
        $presets = $this->presetService->getPresets();

        foreach ($presets as $key => $preset) {
            $this->assertIsInt($preset['slideCount'], "Preset '{$key}' slideCount must be integer");
            $this->assertGreaterThan(0, $preset['slideCount'], "Preset '{$key}' slideCount must be > 0");
        }
    }

    /** @test */
    public function each_preset_has_valid_effect_type()
    {
        $presets = $this->presetService->getPresets();
        $validEffects = ['slide', 'fade', 'cube', 'coverflow', 'flip', 'cards', 'creative'];

        foreach ($presets as $key => $preset) {
            $effect = $preset['settings']['effect'] ?? null;
            $this->assertNotNull($effect, "Preset '{$key}' missing effect setting");
            $this->assertContains($effect, $validEffects, "Preset '{$key}' has invalid effect '{$effect}'");
        }
    }

    /** @test */
    public function coverflow_preset_has_minimum_slides()
    {
        $albumGallery = $this->presetService->getPreset('album_gallery');

        $this->assertEquals('coverflow', $albumGallery['settings']['effect']);
        $this->assertGreaterThanOrEqual(5, $albumGallery['slideCount'], 'Coverflow needs at least 5 slides');
    }

    /** @test */
    public function fade_effect_presets_have_slides_per_view_one()
    {
        $presets = $this->presetService->getPresets();

        foreach ($presets as $key => $preset) {
            if ($preset['settings']['effect'] === 'fade') {
                $this->assertEquals(1, $preset['settings']['slidesPerView'], "Fade preset '{$key}' must have slidesPerView = 1");
            }
        }
    }

    /** @test */
    public function cube_effect_presets_have_slides_per_view_one()
    {
        $presets = $this->presetService->getPresets();

        foreach ($presets as $key => $preset) {
            if ($preset['settings']['effect'] === 'cube') {
                $this->assertEquals(1, $preset['settings']['slidesPerView'], "Cube preset '{$key}' must have slidesPerView = 1");
            }
        }
    }

    /** @test */
    public function it_returns_all_slide_templates()
    {
        $templates = $this->presetService->getSlideTemplates();

        $this->assertIsArray($templates);
        $this->assertNotEmpty($templates);

        $expectedTemplates = [
            'image',
            'thumbnail',
            'testimonial',
            'product',
            'hero',
            'portfolio',
            'card',
            'timeline',
        ];

        foreach ($expectedTemplates as $template) {
            $this->assertArrayHasKey($template, $templates);
        }
    }

    /** @test */
    public function each_slide_template_has_valid_structure()
    {
        $templates = $this->presetService->getSlideTemplates();

        foreach ($templates as $name => $template) {
            $this->assertIsArray($template, "Template '{$name}' must be an array");

            // Some templates (like 'card') have configuration keys and a nested 'fields' array
            $fields = $template;
            if (isset($template['fields']) && is_array($template['fields'])) {
                $fields = $template['fields'];
            }

            foreach ($fields as $index => $field) {
                // Skip non-field configuration keys (like 'min_height', 'padding', etc.)
                if (! is_array($field)) {
                    continue;
                }

                $this->assertArrayHasKey('field_type', $field, "Template '{$name}' field {$index} missing 'field_type'");
                $this->assertArrayHasKey('label', $field, "Template '{$name}' field {$index} missing 'label'");
                $this->assertArrayHasKey('required', $field, "Template '{$name}' field {$index} missing 'required'");
            }
        }
    }

    /** @test */
    public function image_fields_in_templates_have_placeholder_urls()
    {
        $templates = $this->presetService->getSlideTemplates();

        foreach ($templates as $name => $template) {
            // Some templates (like 'card') have configuration keys and a nested 'fields' array
            $fields = $template;
            if (isset($template['fields']) && is_array($template['fields'])) {
                $fields = $template['fields'];
            }

            foreach ($fields as $index => $field) {
                // Skip non-field configuration keys
                if (! is_array($field)) {
                    continue;
                }

                if ($field['field_type'] === 'image') {
                    $this->assertArrayHasKey('placeholder_url', $field, "Template '{$name}' image field {$index} missing 'placeholder_url'");
                    $this->assertStringContainsString('picsum.photos', $field['placeholder_url'], "Template '{$name}' should use picsum.photos URLs");
                }
            }
        }
    }

    /** @test */
    public function it_returns_preset_options_for_select_dropdown()
    {
        $options = $this->presetService->getPresetOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('', $options); // Default "Custom" option
        $this->assertEquals('Custom (No Preset)', $options['']);

        // Check all presets are in options
        $presets = $this->presetService->getPresets();
        foreach ($presets as $key => $preset) {
            $this->assertArrayHasKey($key, $options);
            $this->assertEquals($preset['label'], $options[$key]);
        }
    }

    /** @test */
    public function it_can_get_specific_preset_by_key()
    {
        $imageGallery = $this->presetService->getPreset('image_gallery');

        $this->assertIsArray($imageGallery);
        $this->assertEquals('Image Gallery', $imageGallery['label']);
        $this->assertEquals('slide', $imageGallery['settings']['effect']);
    }

    /** @test */
    public function it_returns_null_for_invalid_preset_key()
    {
        $invalid = $this->presetService->getPreset('nonexistent_preset');

        $this->assertNull($invalid);
    }

    /** @test */
    public function product_showcase_preset_has_breakpoints()
    {
        $productShowcase = $this->presetService->getPreset('product_showcase');

        $this->assertArrayHasKey('breakpoints', $productShowcase['settings']);
        $this->assertTrue($productShowcase['settings']['breakpoints']['enabled']);
        $this->assertArrayHasKey('640_slidesPerView', $productShowcase['settings']['breakpoints']);
        $this->assertArrayHasKey('768_slidesPerView', $productShowcase['settings']['breakpoints']);
        $this->assertArrayHasKey('1024_slidesPerView', $productShowcase['settings']['breakpoints']);
    }

    /** @test */
    public function hero_slider_preset_has_autoplay_enabled()
    {
        $heroSlider = $this->presetService->getPreset('hero_slider');

        $this->assertArrayHasKey('autoplay', $heroSlider['settings']);
        $this->assertTrue($heroSlider['settings']['autoplay']['enabled']);
        $this->assertArrayHasKey('delay', $heroSlider['settings']['autoplay']);
    }

    /** @test */
    public function testimonials_preset_uses_testimonial_template()
    {
        $testimonials = $this->presetService->getPreset('testimonials');
        $templates = $this->presetService->getSlideTemplates();

        $this->assertEquals('testimonial', $testimonials['slideTemplate']);
        $this->assertArrayHasKey('testimonial', $templates);

        // Verify testimonial template has quote, name, and photo fields
        $testimonialFields = $templates['testimonial'];
        $fieldTypes = array_column($testimonialFields, 'field_type');

        $this->assertContains('textarea', $fieldTypes, 'Testimonial should have textarea for quote');
        $this->assertContains('text', $fieldTypes, 'Testimonial should have text for name');
        $this->assertContains('image', $fieldTypes, 'Testimonial should have image for photo');
    }

    /** @test */
    public function timeline_preset_has_free_mode_enabled()
    {
        $timeline = $this->presetService->getPreset('timeline');

        $this->assertArrayHasKey('freeMode', $timeline['settings']);
        $this->assertTrue($timeline['settings']['freeMode']['enabled']);
        $this->assertTrue($timeline['settings']['freeMode']['sticky']);
    }

    /** @test */
    public function before_after_preset_has_rewind_enabled()
    {
        $beforeAfter = $this->presetService->getPreset('before_after');

        $this->assertTrue($beforeAfter['settings']['rewind']);
        $this->assertFalse($beforeAfter['settings']['loop']);
        $this->assertEquals(2, $beforeAfter['slideCount']);
    }

    /** @test */
    public function thumbnail_gallery_has_grid_configuration()
    {
        $thumbnailGallery = $this->presetService->getPreset('thumbnail_gallery');

        $this->assertArrayHasKey('grid', $thumbnailGallery['settings']);
        $this->assertEquals(2, $thumbnailGallery['settings']['grid']['rows']);
        $this->assertEquals('row', $thumbnailGallery['settings']['grid']['fill']);
        $this->assertEquals(12, $thumbnailGallery['slideCount'], 'Thumbnail gallery should have 12 slides for 2x4 grid');
    }

    /** @test */
    public function each_preset_references_valid_slide_template()
    {
        $presets = $this->presetService->getPresets();
        $templates = $this->presetService->getSlideTemplates();

        foreach ($presets as $key => $preset) {
            $templateKey = $preset['slideTemplate'];
            $this->assertArrayHasKey($templateKey, $templates, "Preset '{$key}' references invalid template '{$templateKey}'");
        }
    }
}
