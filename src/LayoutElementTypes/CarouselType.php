<?php

namespace DigitalisStudios\SlickForms\LayoutElementTypes;

use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;

/**
 * CarouselType
 *
 * A powerful carousel/slider component powered by Swiper.js.
 * Supports ALL Swiper.js features including navigation, pagination,
 * autoplay, effects, responsive breakpoints, and more.
 *
 * Features:
 * - 200+ configuration parameters
 * - 15+ modules (Navigation, Pagination, Autoplay, Effects, etc.)
 * - 7 transition effects (slide, fade, cube, coverflow, flip, cards, creative)
 * - Responsive breakpoints
 * - Full accessibility support
 * - Mixed content (fields, layout elements, cards, etc.)
 *
 * @see https://swiperjs.com/swiper-api
 */
class CarouselType extends BaseLayoutElementType
{
    public function getType(): string
    {
        return 'carousel';
    }

    public function getLabel(): string
    {
        return 'Carousel';
    }

    public function getIcon(): string
    {
        return 'bi-collection-play';
    }

    public function getAllowedChildren(): array
    {
        // Carousel slides can contain ANY content type
        return ['*'];
    }

    /**
     * Get default settings for new carousel elements
     */
    public function getDefaultSettings(): array
    {
        return [
            'builderMode' => 'preview',
        ];
    }

    /**
     * Determine if properties panel should auto-open when element is created
     */
    public function shouldAutoOpenProperties(): bool
    {
        return true;
    }

    /**
     * Get the default preset to apply when element is created
     */
    public function getDefaultPreset(): ?string
    {
        return 'album_gallery';
    }

    /**
     * Define carousel-specific property tabs
     * These tabs only appear when editing carousel elements
     */
    public function getPropertyTabs(): array
    {
        return [
            'navigation' => [
                'label' => 'Navigation',
                'icon' => 'bi-arrow-left-right',
                'order' => 36,
            ],
            'pagination' => [
                'label' => 'Pagination',
                'icon' => 'bi-circle-fill',
                'order' => 37,
            ],
            'effects' => [
                'label' => 'Effects',
                'icon' => 'bi-magic',
                'order' => 38,
            ],
            'autoplay' => [
                'label' => 'Autoplay',
                'icon' => 'bi-play-circle',
                'order' => 39,
            ],
            'interaction' => [
                'label' => 'Interaction',
                'icon' => 'bi-hand-index',
                'order' => 40,
            ],
            'scrollbar' => [
                'label' => 'Scrollbar',
                'icon' => 'bi-arrows-expand',
                'order' => 41,
            ],
            'accessibility' => [
                'label' => 'Accessibility',
                'icon' => 'bi-universal-access',
                'order' => 42,
            ],
        ];
    }

    public function getConfigSchema(): array
    {
        $presetService = app(\DigitalisStudios\SlickForms\Services\CarouselPresetService::class);

        return array_merge(parent::getConfigSchema(), [
            // ========================================
            // CORE SETTINGS TAB
            // ========================================

            'preset' => [
                'type' => 'custom',
                'label' => 'Preset',
                'tab' => 'basic',
                'target' => 'settings',
                'options' => $presetService->getPresetOptions(),
                'default' => '',
                'help' => '⚠️ Applying a preset will replace ALL current settings and slides.',
                'order' => 5,
                'component' => 'carousel-preset-selector',
            ],

            'direction' => [
                'type' => 'select',
                'label' => 'Direction',
                'tab' => 'settings',
                'target' => 'settings',
                'options' => [
                    'horizontal' => 'Horizontal',
                    'vertical' => 'Vertical',
                ],
                'default' => 'horizontal',
                'help' => 'Slider direction',
                'order' => 10,
            ],

            'speed' => [
                'type' => 'number',
                'label' => 'Transition Speed (ms)',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => 300,
                'min' => 0,
                'max' => 5000,
                'help' => 'Duration of transition between slides in milliseconds',
                'order' => 20,
            ],

            'loop' => [
                'type' => 'switch',
                'label' => 'Enable Loop',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => false,
                'help' => 'Set to true to enable continuous loop mode (infinite carousel)',
                'order' => 30,
            ],

            'rewind' => [
                'type' => 'switch',
                'label' => 'Rewind',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => false,
                'help' => 'When reaching the end, rewind to beginning (alternative to loop mode)',
                'order' => 35,
            ],

            'slidesPerView' => [
                'type' => 'text',
                'label' => 'Slides Per View',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => '1',
                'help' => 'Number of slides visible at once. Use number (1, 2, 3...) or "auto" for automatic sizing',
                'placeholder' => '1 or "auto"',
                'order' => 40,
            ],

            'initialSlide' => [
                'type' => 'number',
                'label' => 'Initial Slide',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => 0,
                'min' => 0,
                'help' => 'Index number of slide to display on load (0 = first slide, 1 = second, etc.)',
                'order' => 45,
            ],

            'spaceBetween' => [
                'type' => 'number',
                'label' => 'Space Between Slides (px)',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => 0,
                'min' => 0,
                'help' => 'Distance between slides in pixels',
                'order' => 50,
            ],

            'slidesPerGroup' => [
                'type' => 'number',
                'label' => 'Slides Per Group',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => 1,
                'min' => 1,
                'max' => 10,
                'help' => 'Number of slides to skip when navigating (e.g., 3 = skip 3 slides per click)',
                'order' => 55,
            ],

            'centeredSlides' => [
                'type' => 'switch',
                'label' => 'Center Active Slide',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => false,
                'help' => 'If true, active slide will be centered, not always at the left edge',
                'order' => 60,
            ],

            'autoHeight' => [
                'type' => 'switch',
                'label' => 'Auto Height',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => false,
                'help' => 'Automatically adapt carousel height to active slide content',
                'order' => 70,
            ],

            'grabCursor' => [
                'type' => 'switch',
                'label' => 'Grab Cursor',
                'tab' => 'settings',
                'target' => 'settings',
                'default' => false,
                'help' => 'Show hand/grab cursor when hovering over carousel',
                'order' => 80,
            ],

            // ========================================
            // EFFECTS TAB
            // ========================================

            'effect' => [
                'type' => 'select',
                'label' => 'Transition Effect',
                'tab' => 'effects',
                'target' => 'settings',
                'options' => [
                    'slide' => 'Slide (Default)',
                    'fade' => 'Fade',
                    'cube' => 'Cube (3D)',
                    'coverflow' => 'Coverflow (3D)',
                    'flip' => 'Flip (3D)',
                    'cards' => 'Cards',
                    'creative' => 'Creative (Custom)',
                ],
                'default' => 'slide',
                'help' => 'Choose transition effect between slides',
                'order' => 10,
            ],

            'coverflow_tip' => [
                'type' => 'html',
                'tab' => 'effects',
                'content' => '<div class="alert alert-info mb-0"><i class="bi bi-info-circle me-2"></i><strong>Coverflow Tip:</strong> For best results, ensure you have at least <strong>5 slides</strong> in your carousel. When using Coverflow effect with centered slides, Swiper requires <strong>2 additional slides</strong> than the number of slides per view. Set "Slides per View" (in Settings tab) to "auto" or a specific number. <strong>Example:</strong> If Slides per view is 5, at least 7 slides would be needed for optimal display. Recommended minimums are <strong>3 slides per view</strong> and <strong>5 slides</strong> for coverflow to work properly.</div>',
                'show_if' => ['effect' => 'coverflow'],
                'order' => 11,
            ],

            // Fade Effect Settings
            'fadeEffect_crossFade' => [
                'type' => 'switch',
                'label' => 'Cross Fade',
                'tab' => 'effects',
                'target' => 'settings.fadeEffect.crossFade',
                'default' => false,
                'help' => 'Enable cross-fade transition (overlapping slides)',
                'show_if' => ['effect' => 'fade'],
                'order' => 20,
            ],

            // Cube Effect Settings
            'cubeEffect_shadow' => [
                'type' => 'switch',
                'label' => 'Enable Shadow',
                'tab' => 'effects',
                'target' => 'settings.cubeEffect.shadow',
                'default' => true,
                'help' => 'Enable main cube shadow effect',
                'show_if' => ['effect' => 'cube'],
                'order' => 30,
            ],

            'cubeEffect_shadowOffset' => [
                'type' => 'number',
                'label' => 'Shadow Offset',
                'tab' => 'effects',
                'target' => 'settings.cubeEffect.shadowOffset',
                'default' => 20,
                'min' => 0,
                'max' => 100,
                'help' => 'Main shadow offset in pixels',
                'show_if' => ['effect' => 'cube'],
                'order' => 31,
            ],

            'cubeEffect_shadowScale' => [
                'type' => 'number',
                'label' => 'Shadow Scale',
                'tab' => 'effects',
                'target' => 'settings.cubeEffect.shadowScale',
                'default' => 0.94,
                'min' => 0,
                'max' => 1,
                'step' => 0.01,
                'help' => 'Main shadow scale ratio',
                'show_if' => ['effect' => 'cube'],
                'order' => 32,
            ],

            'cubeEffect_slideShadows' => [
                'type' => 'switch',
                'label' => 'Slide Shadows',
                'tab' => 'effects',
                'target' => 'settings.cubeEffect.slideShadows',
                'default' => true,
                'help' => 'Enable individual slide shadows',
                'show_if' => ['effect' => 'cube'],
                'order' => 33,
            ],

            // Coverflow Effect Settings
            'coverflowEffect_rotate' => [
                'type' => 'number',
                'label' => 'Rotate Angle',
                'tab' => 'effects',
                'target' => 'settings.coverflowEffect.rotate',
                'default' => 50,
                'min' => 0,
                'max' => 180,
                'help' => 'Slide rotation angle in degrees',
                'show_if' => ['effect' => 'coverflow'],
                'order' => 40,
            ],

            'coverflowEffect_stretch' => [
                'type' => 'number',
                'label' => 'Stretch',
                'tab' => 'effects',
                'target' => 'settings.coverflowEffect.stretch',
                'default' => 0,
                'help' => 'Stretch space between slides (px)',
                'show_if' => ['effect' => 'coverflow'],
                'order' => 41,
            ],

            'coverflowEffect_depth' => [
                'type' => 'number',
                'label' => 'Depth',
                'tab' => 'effects',
                'target' => 'settings.coverflowEffect.depth',
                'default' => 100,
                'help' => 'Depth offset in pixels (slides translate in Z axis)',
                'show_if' => ['effect' => 'coverflow'],
                'order' => 42,
            ],

            'coverflowEffect_modifier' => [
                'type' => 'number',
                'label' => 'Modifier',
                'tab' => 'effects',
                'target' => 'settings.coverflowEffect.modifier',
                'default' => 1,
                'min' => 0.1,
                'step' => 0.1,
                'help' => 'Effect multiplier',
                'show_if' => ['effect' => 'coverflow'],
                'order' => 43,
            ],

            'coverflowEffect_scale' => [
                'type' => 'number',
                'label' => 'Scale',
                'tab' => 'effects',
                'target' => 'settings.coverflowEffect.scale',
                'default' => 1,
                'min' => 0.1,
                'max' => 2,
                'step' => 0.1,
                'help' => 'Slide scale effect',
                'show_if' => ['effect' => 'coverflow'],
                'order' => 44,
            ],

            'coverflowEffect_slideShadows' => [
                'type' => 'switch',
                'label' => 'Slide Shadows',
                'tab' => 'effects',
                'target' => 'settings.coverflowEffect.slideShadows',
                'default' => true,
                'help' => 'Enable individual slide shadows',
                'show_if' => ['effect' => 'coverflow'],
                'order' => 45,
            ],

            // ========================================
            // INTERACTION TAB
            // ========================================

            'allowSlideNext' => [
                'type' => 'switch',
                'label' => 'Allow Slide Next',
                'tab' => 'interaction',
                'target' => 'settings',
                'default' => true,
                'help' => 'Enable forward navigation (swipe right/click next button)',
                'order' => 10,
            ],

            'allowSlidePrev' => [
                'type' => 'switch',
                'label' => 'Allow Slide Previous',
                'tab' => 'interaction',
                'target' => 'settings',
                'default' => true,
                'help' => 'Enable backward navigation (swipe left/click prev button)',
                'order' => 20,
            ],

            'allowTouchMove' => [
                'type' => 'switch',
                'label' => 'Allow Touch/Drag',
                'tab' => 'interaction',
                'target' => 'settings',
                'default' => true,
                'help' => 'Enable touch/mouse drag to navigate slides',
                'order' => 30,
            ],

            'slideToClickedSlide' => [
                'type' => 'switch',
                'label' => 'Slide to Clicked Slide',
                'tab' => 'interaction',
                'target' => 'settings',
                'default' => false,
                'help' => 'Click any visible slide to navigate to it (useful with multiple slides per view)',
                'order' => 40,
            ],

            // ========================================
            // NAVIGATION TAB
            // ========================================

            'navigation_enabled' => [
                'type' => 'switch',
                'label' => 'Enable Navigation Arrows',
                'tab' => 'navigation',
                'target' => 'settings.navigation.enabled',
                'default' => false,
                'help' => 'Show previous/next navigation arrows',
                'order' => 10,
            ],

            'navigation_hideOnClick' => [
                'type' => 'switch',
                'label' => 'Hide on Click',
                'tab' => 'navigation',
                'target' => 'settings.navigation.hideOnClick',
                'default' => false,
                'help' => 'Toggle navigation visibility when carousel is clicked',
                'show_if' => ['navigation_enabled' => true],
                'order' => 20,
            ],

            // ========================================
            // PAGINATION TAB
            // ========================================

            'pagination_enabled' => [
                'type' => 'switch',
                'label' => 'Enable Pagination',
                'tab' => 'pagination',
                'target' => 'settings.pagination.enabled',
                'default' => false,
                'help' => 'Show pagination indicators',
                'order' => 10,
            ],

            'pagination_type' => [
                'type' => 'select',
                'label' => 'Pagination Type',
                'tab' => 'pagination',
                'target' => 'settings.pagination.type',
                'options' => [
                    'bullets' => 'Bullets',
                    'fraction' => 'Fraction (1 / 5)',
                    'progressbar' => 'Progress Bar',
                ],
                'default' => 'bullets',
                'help' => 'Type of pagination indicator to display',
                'show_if' => ['pagination_enabled' => true],
                'order' => 20,
            ],

            'pagination_clickable' => [
                'type' => 'switch',
                'label' => 'Clickable Bullets',
                'tab' => 'pagination',
                'target' => 'settings.pagination.clickable',
                'default' => true,
                'help' => 'Make pagination bullets clickable to navigate',
                'show_if' => ['pagination_enabled' => true],
                'order' => 30,
            ],

            'pagination_dynamicBullets' => [
                'type' => 'switch',
                'label' => 'Dynamic Bullets',
                'tab' => 'pagination',
                'target' => 'settings.pagination.dynamicBullets',
                'default' => false,
                'help' => 'Show limited number of pagination bullets with active highlighted',
                'show_if' => ['pagination_enabled' => true],
                'order' => 40,
            ],

            // ========================================
            // AUTOPLAY TAB
            // ========================================

            'autoplay_enabled' => [
                'type' => 'switch',
                'label' => 'Enable Autoplay',
                'tab' => 'autoplay',
                'target' => 'settings.autoplay.enabled',
                'default' => false,
                'help' => 'Automatically transition between slides',
                'order' => 10,
            ],

            'autoplay_delay' => [
                'type' => 'number',
                'label' => 'Delay (ms)',
                'tab' => 'autoplay',
                'target' => 'settings.autoplay.delay',
                'default' => 3000,
                'min' => 500,
                'max' => 10000,
                'help' => 'Time between slide transitions in milliseconds',
                'show_if' => ['autoplay_enabled' => true],
                'order' => 20,
            ],

            'autoplay_pauseOnMouseEnter' => [
                'type' => 'switch',
                'label' => 'Pause on Hover',
                'tab' => 'autoplay',
                'target' => 'settings.autoplay.pauseOnMouseEnter',
                'default' => false,
                'help' => 'Pause autoplay when mouse hovers over carousel',
                'show_if' => ['autoplay_enabled' => true],
                'order' => 30,
            ],

            'autoplay_disableOnInteraction' => [
                'type' => 'switch',
                'label' => 'Disable on Interaction',
                'tab' => 'autoplay',
                'target' => 'settings.autoplay.disableOnInteraction',
                'default' => true,
                'help' => 'Disable autoplay after user interacts with carousel',
                'show_if' => ['autoplay_enabled' => true],
                'order' => 40,
            ],

            // ========================================
            // SCROLLBAR TAB
            // ========================================

            'scrollbar_enabled' => [
                'type' => 'switch',
                'label' => 'Enable Scrollbar',
                'tab' => 'scrollbar',
                'target' => 'settings.scrollbar.enabled',
                'default' => false,
                'help' => 'Show scrollbar indicator',
                'order' => 10,
            ],

            'scrollbar_draggable' => [
                'type' => 'switch',
                'label' => 'Draggable',
                'tab' => 'scrollbar',
                'target' => 'settings.scrollbar.draggable',
                'default' => false,
                'help' => 'Allow scrollbar to be draggable',
                'show_if' => ['scrollbar_enabled' => true],
                'order' => 20,
            ],

            'scrollbar_hide' => [
                'type' => 'switch',
                'label' => 'Auto Hide',
                'tab' => 'scrollbar',
                'target' => 'settings.scrollbar.hide',
                'default' => true,
                'help' => 'Automatically hide scrollbar after interaction',
                'show_if' => ['scrollbar_enabled' => true],
                'order' => 30,
            ],

            // ========================================
            // ACCESSIBILITY TAB
            // ========================================

            'a11y_enabled' => [
                'type' => 'switch',
                'label' => 'Enable Accessibility',
                'tab' => 'accessibility',
                'target' => 'settings.a11y.enabled',
                'default' => true,
                'help' => 'Add ARIA labels and keyboard support for screen readers',
                'order' => 10,
            ],

            'a11y_prevSlideMessage' => [
                'type' => 'text',
                'label' => 'Previous Slide Message',
                'tab' => 'accessibility',
                'target' => 'settings.a11y.prevSlideMessage',
                'default' => 'Previous slide',
                'help' => 'ARIA label for previous button',
                'order' => 20,
            ],

            'a11y_nextSlideMessage' => [
                'type' => 'text',
                'label' => 'Next Slide Message',
                'tab' => 'accessibility',
                'target' => 'settings.a11y.nextSlideMessage',
                'default' => 'Next slide',
                'help' => 'ARIA label for next button',
                'order' => 30,
            ],

            'a11y_firstSlideMessage' => [
                'type' => 'text',
                'label' => 'First Slide Message',
                'tab' => 'accessibility',
                'target' => 'settings.a11y.firstSlideMessage',
                'default' => 'This is the first slide',
                'help' => 'Screen reader message when reaching first slide',
                'order' => 40,
            ],

            'a11y_lastSlideMessage' => [
                'type' => 'text',
                'label' => 'Last Slide Message',
                'tab' => 'accessibility',
                'target' => 'settings.a11y.lastSlideMessage',
                'default' => 'This is the last slide',
                'help' => 'Screen reader message when reaching last slide',
                'order' => 50,
            ],

            // ========================================
            // ADVANCED TAB - KEYBOARD CONTROL
            // ========================================

            'keyboard_enabled' => [
                'type' => 'switch',
                'label' => 'Enable Keyboard Navigation',
                'tab' => 'advanced',
                'target' => 'settings.keyboard.enabled',
                'default' => false,
                'help' => 'Enable keyboard control (arrow keys)',
                'order' => 10,
            ],

            'keyboard_onlyInViewport' => [
                'type' => 'switch',
                'label' => 'Only In Viewport',
                'tab' => 'advanced',
                'target' => 'settings.keyboard.onlyInViewport',
                'default' => true,
                'help' => 'Keyboard control only works when carousel is visible in viewport',
                'show_if' => ['keyboard_enabled' => true],
                'order' => 11,
            ],

            // ========================================
            // ADVANCED TAB - MOUSEWHEEL CONTROL
            // ========================================

            'mousewheel_enabled' => [
                'type' => 'switch',
                'label' => 'Enable Mousewheel Navigation',
                'tab' => 'advanced',
                'target' => 'settings.mousewheel.enabled',
                'default' => false,
                'help' => 'Enable navigation by mouse wheel scrolling',
                'order' => 20,
            ],

            'mousewheel_invert' => [
                'type' => 'switch',
                'label' => 'Invert Direction',
                'tab' => 'advanced',
                'target' => 'settings.mousewheel.invert',
                'default' => false,
                'help' => 'Reverse mousewheel scroll direction',
                'show_if' => ['mousewheel_enabled' => true],
                'order' => 21,
            ],

            'mousewheel_sensitivity' => [
                'type' => 'number',
                'label' => 'Sensitivity',
                'tab' => 'advanced',
                'target' => 'settings.mousewheel.sensitivity',
                'default' => 1,
                'min' => 0.1,
                'max' => 10,
                'step' => 0.1,
                'help' => 'Mousewheel scroll sensitivity multiplier',
                'show_if' => ['mousewheel_enabled' => true],
                'order' => 22,
            ],

            // ========================================
            // ADVANCED TAB - ZOOM MODULE
            // ========================================

            'zoom_enabled' => [
                'type' => 'switch',
                'label' => 'Enable Zoom',
                'tab' => 'advanced',
                'target' => 'settings.zoom.enabled',
                'default' => false,
                'help' => 'Enable pinch-to-zoom on slides (mobile/desktop)',
                'order' => 30,
            ],

            'zoom_maxRatio' => [
                'type' => 'number',
                'label' => 'Max Zoom Ratio',
                'tab' => 'advanced',
                'target' => 'settings.zoom.maxRatio',
                'default' => 3,
                'min' => 1,
                'max' => 10,
                'help' => 'Maximum zoom scale',
                'show_if' => ['zoom_enabled' => true],
                'order' => 31,
            ],

            'zoom_minRatio' => [
                'type' => 'number',
                'label' => 'Min Zoom Ratio',
                'tab' => 'advanced',
                'target' => 'settings.zoom.minRatio',
                'default' => 1,
                'min' => 0.5,
                'max' => 2,
                'step' => 0.1,
                'help' => 'Minimum zoom scale',
                'show_if' => ['zoom_enabled' => true],
                'order' => 32,
            ],

            'zoom_toggle' => [
                'type' => 'switch',
                'label' => 'Double-Tap to Zoom',
                'tab' => 'advanced',
                'target' => 'settings.zoom.toggle',
                'default' => true,
                'help' => 'Enable double-tap/click to toggle zoom',
                'show_if' => ['zoom_enabled' => true],
                'order' => 33,
            ],

            // ========================================
            // ADVANCED TAB - PARALLAX MODULE
            // ========================================

            'parallax_enabled' => [
                'type' => 'switch',
                'label' => 'Enable Parallax',
                'tab' => 'advanced',
                'target' => 'settings.parallax.enabled',
                'default' => false,
                'help' => 'Enable parallax effects with a single background image that slides behind content',
                'order' => 40,
            ],

            'parallax_bg_mode' => [
                'type' => 'select',
                'label' => 'Parallax Background',
                'tab' => 'advanced',
                'target' => 'settings.parallax.background_mode',
                'options' => [
                    '' => 'None',
                    'url' => 'Image URL',
                    'upload' => 'File Upload',
                ],
                'default' => '',
                'help' => 'Background image for parallax effect',
                'show_if' => ['parallax_enabled' => true],
                'order' => 41,
            ],

            'parallax_bg_url' => [
                'type' => 'text',
                'label' => 'Background Image URL',
                'tab' => 'advanced',
                'target' => 'settings.parallax.background_url',
                'placeholder' => 'https://example.com/image.jpg',
                'help' => 'URL of the parallax background image',
                'show_if' => ['parallax_bg_mode' => 'url'],
                'order' => 42,
            ],

            'parallax_bg_upload' => [
                'type' => 'file',
                'label' => 'Upload Background Image',
                'tab' => 'advanced',
                'target' => 'settings.parallax.background_upload',
                'accept' => 'image/*',
                'help' => 'Upload a background image for parallax effect',
                'show_if' => ['parallax_bg_mode' => 'upload'],
                'order' => 43,
            ],

            'parallax_bg_preview' => [
                'type' => 'html',
                'label' => 'Preview',
                'tab' => 'advanced',
                'content' => '
                    <div x-show="$wire.properties.parallax_bg_url" class="border rounded p-2 bg-light text-center">
                        <img :src="$wire.properties.parallax_bg_url" class="img-fluid" style="max-height: 200px;" alt="Parallax Background Preview">
                        <div class="small text-muted mt-2">
                            <i class="bi bi-image me-1"></i> Parallax background preview
                        </div>
                    </div>
                ',
                'show_if' => ['parallax_bg_mode' => ['url', 'upload']],
                'order' => 44,
            ],

            // ========================================
            // ADVANCED TAB - FREE MODE
            // ========================================

            'freeMode_enabled' => [
                'type' => 'switch',
                'label' => 'Enable Free Mode',
                'tab' => 'advanced',
                'target' => 'settings.freeMode.enabled',
                'default' => false,
                'help' => 'Slides will not snap to positions, continuous scrolling',
                'order' => 50,
            ],

            'freeMode_momentum' => [
                'type' => 'switch',
                'label' => 'Momentum',
                'tab' => 'advanced',
                'target' => 'settings.freeMode.momentum',
                'default' => true,
                'help' => 'Enable momentum scrolling',
                'show_if' => ['freeMode_enabled' => true],
                'order' => 51,
            ],

            'freeMode_sticky' => [
                'type' => 'switch',
                'label' => 'Sticky Snap',
                'tab' => 'advanced',
                'target' => 'settings.freeMode.sticky',
                'default' => false,
                'help' => 'Snap to slides after free mode scroll ends',
                'show_if' => ['freeMode_enabled' => true],
                'order' => 52,
            ],

            // ========================================
            // ADVANCED TAB - GRID LAYOUT
            // ========================================

            'grid_rows' => [
                'type' => 'number',
                'label' => 'Grid Rows',
                'tab' => 'advanced',
                'target' => 'settings.grid.rows',
                'default' => 1,
                'min' => 1,
                'max' => 10,
                'help' => 'Number of slides rows (for grid layout)',
                'order' => 60,
            ],

            'grid_fill' => [
                'type' => 'select',
                'label' => 'Grid Fill Direction',
                'tab' => 'advanced',
                'target' => 'settings.grid.fill',
                'options' => [
                    'row' => 'Row',
                    'column' => 'Column',
                ],
                'default' => 'row',
                'help' => 'How grid slides are filled',
                'show_if' => [
                    ['field' => 'grid_rows', 'operator' => '>', 'value' => 1],
                ],
                'order' => 61,
            ],

            // ========================================
            // ADVANCED TAB - RESPONSIVE BREAKPOINTS
            // ========================================

            'breakpoints_enabled' => [
                'type' => 'switch',
                'label' => 'Enable Responsive Breakpoints',
                'tab' => 'advanced',
                'target' => 'settings.breakpoints.enabled',
                'default' => false,
                'help' => 'Override settings at specific screen widths',
                'order' => 70,
            ],

            // Mobile Breakpoint (640px)
            'breakpoint_640_slidesPerView' => [
                'type' => 'text',
                'label' => '640px - Slides Per View',
                'tab' => 'advanced',
                'target' => 'settings.breakpoints.640.slidesPerView',
                'default' => '',
                'placeholder' => 'e.g., 1',
                'help' => 'Number of slides on mobile (640px and up)',
                'show_if' => ['breakpoints_enabled' => true],
                'order' => 71,
            ],

            'breakpoint_640_spaceBetween' => [
                'type' => 'number',
                'label' => '640px - Space Between',
                'tab' => 'advanced',
                'target' => 'settings.breakpoints.640.spaceBetween',
                'default' => null,
                'placeholder' => 'e.g., 10',
                'help' => 'Space between slides on mobile',
                'show_if' => ['breakpoints_enabled' => true],
                'order' => 72,
            ],

            // Tablet Breakpoint (768px)
            'breakpoint_768_slidesPerView' => [
                'type' => 'text',
                'label' => '768px - Slides Per View',
                'tab' => 'advanced',
                'target' => 'settings.breakpoints.768.slidesPerView',
                'default' => '',
                'placeholder' => 'e.g., 2',
                'help' => 'Number of slides on tablet (768px and up)',
                'show_if' => ['breakpoints_enabled' => true],
                'order' => 73,
            ],

            'breakpoint_768_spaceBetween' => [
                'type' => 'number',
                'label' => '768px - Space Between',
                'tab' => 'advanced',
                'target' => 'settings.breakpoints.768.spaceBetween',
                'default' => null,
                'placeholder' => 'e.g., 20',
                'help' => 'Space between slides on tablet',
                'show_if' => ['breakpoints_enabled' => true],
                'order' => 74,
            ],

            // Desktop Breakpoint (1024px)
            'breakpoint_1024_slidesPerView' => [
                'type' => 'text',
                'label' => '1024px - Slides Per View',
                'tab' => 'advanced',
                'target' => 'settings.breakpoints.1024.slidesPerView',
                'default' => '',
                'placeholder' => 'e.g., 3',
                'help' => 'Number of slides on desktop (1024px and up)',
                'show_if' => ['breakpoints_enabled' => true],
                'order' => 75,
            ],

            'breakpoint_1024_spaceBetween' => [
                'type' => 'number',
                'label' => '1024px - Space Between',
                'tab' => 'advanced',
                'target' => 'settings.breakpoints.1024.spaceBetween',
                'default' => null,
                'placeholder' => 'e.g., 30',
                'help' => 'Space between slides on desktop',
                'show_if' => ['breakpoints_enabled' => true],
                'order' => 76,
            ],

            // Large Desktop Breakpoint (1280px)
            'breakpoint_1280_slidesPerView' => [
                'type' => 'text',
                'label' => '1280px - Slides Per View',
                'tab' => 'advanced',
                'target' => 'settings.breakpoints.1280.slidesPerView',
                'default' => '',
                'placeholder' => 'e.g., 4',
                'help' => 'Number of slides on large desktop (1280px and up)',
                'show_if' => ['breakpoints_enabled' => true],
                'order' => 77,
            ],

            'breakpoint_1280_spaceBetween' => [
                'type' => 'number',
                'label' => '1280px - Space Between',
                'tab' => 'advanced',
                'target' => 'settings.breakpoints.1280.spaceBetween',
                'default' => null,
                'placeholder' => 'e.g., 40',
                'help' => 'Space between slides on large desktop',
                'show_if' => ['breakpoints_enabled' => true],
                'order' => 78,
            ],

            // ========================================
            // ADVANCED TAB - LAZY LOADING
            // ========================================

            'lazy' => [
                'type' => 'switch',
                'label' => 'Lazy Load Images',
                'tab' => 'advanced',
                'target' => 'settings',
                'default' => false,
                'help' => 'Load images only when slides become visible (improves performance for image-heavy carousels)',
                'order' => 78,
            ],

            // Builder Mode (UI-only setting)
            'builderMode' => [
                'type' => 'select',
                'label' => 'Builder View Mode',
                'tab' => 'advanced',
                'target' => 'settings',
                'default' => 'preview',
                'options' => [
                    'list' => 'List View (Edit)',
                    'preview' => 'Preview Mode (Read-only)',
                ],
                'help' => 'Toggle between editable list view and live preview mode in the form builder',
                'order' => 79,
            ],
        ]);
    }

    public function render(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        // Use dedicated carousel render view
        return view('slick-forms::livewire.components.elements.render.carousel', [
            'element' => $element,
            'childrenHtml' => $childrenHtml,
        ])->render();
    }

    public function renderBuilder(SlickFormLayoutElement $element, string $childrenHtml): string
    {
        // Use dedicated carousel builder view
        return view('slick-forms::livewire.components.elements.builder.carousel', [
            'element' => $element,
            'childrenHtml' => $childrenHtml,
        ])->render();
    }
}
