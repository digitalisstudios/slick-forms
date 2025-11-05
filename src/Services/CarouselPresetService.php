<?php

namespace DigitalisStudios\SlickForms\Services;

class CarouselPresetService
{
    /**
     * Get all available carousel presets.
     */
    public function getPresets(): array
    {
        return [
            'image_gallery' => [
                'label' => 'Image Gallery',
                'description' => 'Classic photo carousel with navigation and pagination',
                'icon' => 'bi-images',
                'category' => 'media',
                'slideCount' => 5,
                'slideTemplate' => 'image',
                'settings' => [
                    'effect' => 'slide',
                    'slidesPerView' => 1,
                    'spaceBetween' => 0,
                    'loop' => true,
                    'speed' => 300,
                    'grabCursor' => true,
                    'navigation' => ['enabled' => true],
                    'pagination' => [
                        'enabled' => true,
                        'type' => 'bullets',
                        'clickable' => true,
                    ],
                ],
            ],

            'testimonials' => [
                'label' => 'Testimonials',
                'description' => 'Customer quotes with elegant fade transitions',
                'icon' => 'bi-chat-quote',
                'category' => 'content',
                'slideCount' => 3,
                'slideTemplate' => 'testimonial',
                'settings' => [
                    'effect' => 'fade',
                    'fadeEffect' => ['crossFade' => true],
                    'slidesPerView' => 1,
                    'speed' => 600,
                    'autoplay' => [
                        'enabled' => true,
                        'delay' => 5000,
                        'pauseOnMouseEnter' => true,
                    ],
                    'pagination' => [
                        'enabled' => true,
                        'type' => 'bullets',
                        'clickable' => true,
                    ],
                ],
            ],

            'product_showcase' => [
                'label' => 'Product Showcase',
                'description' => 'E-commerce product grid with responsive breakpoints',
                'icon' => 'bi-bag',
                'category' => 'ecommerce',
                'slideCount' => 6,
                'slideTemplate' => 'product',
                'settings' => [
                    'effect' => 'slide',
                    'slidesPerView' => 3,
                    'spaceBetween' => 30,
                    'loop' => true,
                    'grabCursor' => true,
                    'navigation' => ['enabled' => true],
                    'pagination' => [
                        'enabled' => true,
                        'type' => 'progressbar',
                    ],
                    'breakpoints' => [
                        'enabled' => true,
                        '640_slidesPerView' => 1,
                        '640_spaceBetween' => 10,
                        '768_slidesPerView' => 2,
                        '768_spaceBetween' => 20,
                        '1024_slidesPerView' => 3,
                        '1024_spaceBetween' => 30,
                    ],
                ],
            ],

            'hero_slider' => [
                'label' => 'Hero Slider',
                'description' => 'Full-width homepage banners with autoplay',
                'icon' => 'bi-aspect-ratio',
                'category' => 'hero',
                'slideCount' => 3,
                'slideTemplate' => 'hero',
                'settings' => [
                    'effect' => 'fade',
                    'fadeEffect' => ['crossFade' => true],
                    'slidesPerView' => 1,
                    'speed' => 800,
                    'loop' => true,
                    'autoplay' => [
                        'enabled' => true,
                        'delay' => 4000,
                        'pauseOnMouseEnter' => true,
                    ],
                    'pagination' => [
                        'enabled' => true,
                        'type' => 'bullets',
                        'clickable' => true,
                        'dynamicBullets' => true,
                    ],
                    'keyboard' => [
                        'enabled' => true,
                        'onlyInViewport' => true,
                    ],
                ],
            ],

            'album_gallery' => [
                'label' => 'Album Gallery (Coverflow)',
                'description' => '3D photo album with tilted perspective',
                'icon' => 'bi-collection',
                'category' => 'media',
                'slideCount' => 7,
                'slideTemplate' => 'image',
                'settings' => [
                    // Settings Tab
                    'direction' => 'horizontal',
                    'speed' => 400,
                    'loop' => true,
                    'slidesPerView' => 3,
                    'spaceBetween' => 0,
                    'centeredSlides' => true,
                    'grabCursor' => true,
                    'initialSlide' => 0,
                    'slidesPerGroup' => 1,

                    // Effects Tab
                    'effect' => 'coverflow',
                    'coverflowEffect' => [
                        'rotate' => 20,
                        'stretch' => 0,
                        'depth' => 200,
                        'modifier' => 1,
                        'scale' => 0.9,
                        'slideShadows' => true,
                    ],

                    // Navigation Tab
                    'navigation' => [
                        'enabled' => true,
                        'hideOnClick' => false,
                    ],

                    // Pagination Tab
                    'pagination' => [
                        'enabled' => false,
                        'type' => 'bullets',
                        'clickable' => true,
                        'dynamicBullets' => false,
                    ],

                    // Autoplay Tab
                    'autoplay' => [
                        'enabled' => true,
                        'delay' => 5000,
                        'pauseOnMouseEnter' => true,
                        'disableOnInteraction' => true,
                    ],

                    // Interaction Tab
                    'allowSlideNext' => true,
                    'allowSlidePrev' => true,
                    'allowTouchMove' => true,
                    'slideToClickedSlide' => true,

                    // Accessibility Tab
                    'a11y' => [
                        'enabled' => true,
                        'prevSlideMessage' => 'Previous slide',
                        'nextSlideMessage' => 'Next slide',
                        'firstSlideMessage' => 'This is the first slide',
                        'lastSlideMessage' => 'This is the last slide',
                    ],
                ],
            ],

            'portfolio_showcase' => [
                'label' => 'Portfolio Showcase (Cube)',
                'description' => '3D rotating cube for creative work',
                'icon' => 'bi-box',
                'category' => 'portfolio',
                'slideCount' => 4,
                'slideTemplate' => 'portfolio',
                'settings' => [
                    'effect' => 'cube',
                    'cubeEffect' => [
                        'shadow' => true,
                        'shadowOffset' => 20,
                        'shadowScale' => 0.94,
                        'slideShadows' => true,
                    ],
                    'slidesPerView' => 1,
                    'speed' => 500,
                    'loop' => true,
                    'grabCursor' => true,
                    'navigation' => ['enabled' => true],
                    'pagination' => [
                        'enabled' => true,
                        'type' => 'fraction',
                    ],
                ],
            ],

            'thumbnail_gallery' => [
                'label' => 'Thumbnail Grid',
                'description' => 'Compact multi-row image grid',
                'icon' => 'bi-grid-3x3',
                'category' => 'media',
                'slideCount' => 12,
                'slideTemplate' => 'thumbnail',
                'settings' => [
                    'effect' => 'slide',
                    'slidesPerView' => 4,
                    'spaceBetween' => 15,
                    'loop' => false,
                    'slidesPerGroup' => 4,
                    'grid' => [
                        'rows' => 2,
                        'fill' => 'row',
                    ],
                    'navigation' => ['enabled' => true],
                    'pagination' => [
                        'enabled' => true,
                        'type' => 'bullets',
                        'clickable' => true,
                    ],
                ],
            ],

            'content_cards' => [
                'label' => 'Content Cards',
                'description' => 'Centered cards with parallax background',
                'icon' => 'bi-card-text',
                'category' => 'content',
                'slideCount' => 5,
                'slideTemplate' => 'card',
                'settings' => [
                    'effect' => 'slide',
                    'slidesPerView' => 1,
                    'centeredSlides' => true,
                    'spaceBetween' => 20,
                    'loop' => false,
                    'rewind' => true,
                    'grabCursor' => true,
                    'slideToClickedSlide' => true,
                    'breakpoints' => [
                        640 => ['slidesPerView' => 1.5, 'spaceBetween' => 20],
                        768 => ['slidesPerView' => 2, 'spaceBetween' => 20],
                        1024 => ['slidesPerView' => 2.5, 'spaceBetween' => 30],
                        1280 => ['slidesPerView' => 3, 'spaceBetween' => 30],
                    ],
                    'pagination' => [
                        'enabled' => true,
                        'type' => 'bullets',
                        'clickable' => true,
                        'dynamicBullets' => true,
                    ],
                    'parallax' => [
                        'enabled' => true,
                        'background_mode' => 'url',
                        'background_url' => 'https://picsum.photos/seed/carousel-parallax/1920/1080',
                    ],
                ],
            ],

            'timeline' => [
                'label' => 'Timeline',
                'description' => 'Horizontal scrolling timeline with free mode',
                'icon' => 'bi-clock-history',
                'category' => 'content',
                'slideCount' => 8,
                'slideTemplate' => 'timeline',
                'settings' => [
                    'effect' => 'slide',
                    'slidesPerView' => 4,
                    'spaceBetween' => 25,
                    'loop' => false,
                    'grabCursor' => true,
                    'freeMode' => [
                        'enabled' => true,
                        'sticky' => true,
                    ],
                    'scrollbar' => [
                        'enabled' => true,
                        'draggable' => true,
                        'hide' => false,
                    ],
                    'mousewheel' => [
                        'enabled' => true,
                        'invert' => false,
                    ],
                ],
            ],

            'before_after' => [
                'label' => 'Before/After Comparison',
                'description' => 'Simple 2-slide comparison with rewind',
                'icon' => 'bi-arrow-left-right',
                'category' => 'comparison',
                'slideCount' => 2,
                'slideTemplate' => 'image',
                'settings' => [
                    'effect' => 'slide',
                    'slidesPerView' => 1,
                    'spaceBetween' => 0,
                    'loop' => false,
                    'rewind' => true,
                    'speed' => 400,
                    'navigation' => ['enabled' => true],
                    'pagination' => [
                        'enabled' => true,
                        'type' => 'fraction',
                    ],
                    'allowSlideNext' => true,
                    'allowSlidePrev' => true,
                ],
            ],
        ];
    }

    /**
     * Get slide templates for auto-generating fields.
     */
    public function getSlideTemplates(): array
    {
        return [
            'image' => [
                [
                    'field_type' => 'image',
                    'label' => 'Image',
                    'placeholder_url' => 'https://picsum.photos/seed/carousel-image/800/600',
                    'required' => true,
                ],
            ],

            'thumbnail' => [
                [
                    'field_type' => 'image',
                    'label' => 'Thumbnail',
                    'placeholder_url' => 'https://picsum.photos/seed/carousel-thumbnail/300',
                    'required' => true,
                ],
            ],

            'testimonial' => [
                [
                    'field_type' => 'textarea',
                    'label' => 'Quote',
                    'placeholder' => 'Customer testimonial text...',
                    'required' => true,
                ],
                [
                    'field_type' => 'text',
                    'label' => 'Customer Name',
                    'placeholder' => 'John Doe',
                    'required' => true,
                ],
                [
                    'field_type' => 'image',
                    'label' => 'Photo',
                    'placeholder_url' => 'https://picsum.photos/seed/carousel-avatar/150',
                    'required' => false,
                ],
            ],

            'product' => [
                [
                    'field_type' => 'image',
                    'label' => 'Product Image',
                    'placeholder_url' => 'https://picsum.photos/seed/carousel-product/600',
                    'required' => true,
                ],
                [
                    'field_type' => 'text',
                    'label' => 'Product Name',
                    'placeholder' => 'Product Name',
                    'required' => true,
                ],
                [
                    'field_type' => 'textarea',
                    'label' => 'Description',
                    'placeholder' => 'Product description...',
                    'required' => false,
                ],
                [
                    'field_type' => 'number',
                    'label' => 'Price',
                    'placeholder' => '99.99',
                    'required' => false,
                ],
            ],

            'hero' => [
                [
                    'field_type' => 'image',
                    'label' => 'Background Image',
                    'placeholder_url' => 'https://picsum.photos/seed/carousel-hero/1920/1080',
                    'required' => true,
                ],
                [
                    'field_type' => 'header',
                    'label' => 'Headline',
                    'placeholder' => 'Welcome to Our Site',
                    'required' => true,
                ],
                [
                    'field_type' => 'paragraph',
                    'label' => 'Subheading',
                    'placeholder' => 'Discover amazing features',
                    'required' => false,
                ],
            ],

            'portfolio' => [
                [
                    'field_type' => 'image',
                    'label' => 'Project Image',
                    'placeholder_url' => 'https://picsum.photos/seed/carousel-portfolio/800/600',
                    'required' => true,
                ],
                [
                    'field_type' => 'text',
                    'label' => 'Project Title',
                    'placeholder' => 'Project Name',
                    'required' => true,
                ],
                [
                    'field_type' => 'textarea',
                    'label' => 'Description',
                    'placeholder' => 'Project description...',
                    'required' => false,
                ],
            ],

            'card' => [
                'min_height' => '400px',
                'padding' => 'p-4',
                'text_alignment' => 'center',
                'vertical_alignment' => 'end',
                'fields' => [
                    [
                        'field_type' => 'header',
                        'label' => 'Title',
                        'placeholder' => 'Card Title',
                        'required' => true,
                        'show_label' => false,
                    ],
                    [
                        'field_type' => 'paragraph',
                        'label' => 'Content',
                        'placeholder' => 'Card content text...',
                        'default_content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                        'required' => false,
                        'show_label' => false,
                    ],
                ],
            ],

            'timeline' => [
                [
                    'field_type' => 'text',
                    'label' => 'Date',
                    'placeholder' => '2024',
                    'required' => true,
                ],
                [
                    'field_type' => 'text',
                    'label' => 'Event Title',
                    'placeholder' => 'Milestone Title',
                    'required' => true,
                ],
                [
                    'field_type' => 'textarea',
                    'label' => 'Description',
                    'placeholder' => 'Event description...',
                    'required' => false,
                ],
            ],
        ];
    }

    /**
     * Get preset options formatted for select dropdown.
     */
    public function getPresetOptions(): array
    {
        $options = ['' => 'Custom (No Preset)'];

        foreach ($this->getPresets() as $key => $preset) {
            $options[$key] = $preset['label'];
        }

        return $options;
    }

    /**
     * Get a specific preset by key.
     */
    public function getPreset(string $key): ?array
    {
        $presets = $this->getPresets();

        return $presets[$key] ?? null;
    }
}
