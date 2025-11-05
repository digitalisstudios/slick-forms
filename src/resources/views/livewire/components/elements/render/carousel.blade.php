{{--
    Carousel Render View

    Renders a Swiper.js carousel for user-facing forms.
    Supports all Swiper features including navigation, pagination,
    autoplay, effects, and responsive breakpoints.

    @var SlickFormLayoutElement $element
--}}

@php
    $settings = $element->settings ?? [];
    $carouselId = 'carousel-' . $element->id;

    // Extract settings with defaults (cast booleans explicitly)
    $direction = $settings['direction'] ?? 'horizontal';
    $speed = $settings['speed'] ?? 300;
    $loopEnabled = filter_var($settings['loop'] ?? false, FILTER_VALIDATE_BOOLEAN);
    $slidesPerView = $settings['slidesPerView'] ?? 1;
    $spaceBetween = $settings['spaceBetween'] ?? 0;
    $effect = $settings['effect'] ?? 'slide';
    $grabCursor = filter_var($settings['grabCursor'] ?? false, FILTER_VALIDATE_BOOLEAN);
    $centeredSlides = filter_var($settings['centeredSlides'] ?? false, FILTER_VALIDATE_BOOLEAN);
    $autoHeight = filter_var($settings['autoHeight'] ?? false, FILTER_VALIDATE_BOOLEAN);

    // Module settings
    $navigation = $settings['navigation'] ?? ['enabled' => false];
    $pagination = $settings['pagination'] ?? ['enabled' => false];
    $scrollbar = $settings['scrollbar'] ?? ['enabled' => false];
    $autoplay = $settings['autoplay'] ?? ['enabled' => false];
    $a11y = $settings['a11y'] ?? ['enabled' => true];
    $keyboard = $settings['keyboard'] ?? ['enabled' => false];
    $mousewheel = $settings['mousewheel'] ?? ['enabled' => false];
    $zoom = $settings['zoom'] ?? ['enabled' => false];
    $parallax = $settings['parallax'] ?? ['enabled' => false];
    $freeMode = $settings['freeMode'] ?? ['enabled' => false];
    $grid = $settings['grid'] ?? [];
    $breakpoints = $settings['breakpoints'] ?? [];

    // Determine which modules are needed
    $hasNavigation = $navigation['enabled'] ?? false;
    $hasPagination = $pagination['enabled'] ?? false;
    $hasScrollbar = $scrollbar['enabled'] ?? false;
    $hasAutoplay = $autoplay['enabled'] ?? false;
    $hasKeyboard = $keyboard['enabled'] ?? false;
    $hasMousewheel = $mousewheel['enabled'] ?? false;
    $hasZoom = $zoom['enabled'] ?? false;
    $hasParallax = $parallax['enabled'] ?? false;
    $hasFreeMode = $freeMode['enabled'] ?? false;
    $hasGrid = ($grid['rows'] ?? 1) > 1;
    $hasBreakpoints = ($breakpoints['enabled'] ?? false) && !empty($breakpoints);

    // Build responsive breakpoints object
    $responsiveBreakpoints = [];
    if ($hasBreakpoints) {
        foreach ([640, 768, 1024, 1280] as $width) {
            $breakpoint = [];
            if (isset($breakpoints[$width]['slidesPerView']) && $breakpoints[$width]['slidesPerView'] !== '') {
                $breakpoint['slidesPerView'] = is_numeric($breakpoints[$width]['slidesPerView'])
                    ? (int) $breakpoints[$width]['slidesPerView']
                    : $breakpoints[$width]['slidesPerView'];
            }
            if (isset($breakpoints[$width]['spaceBetween']) && $breakpoints[$width]['spaceBetween'] !== null) {
                $breakpoint['spaceBetween'] = (int) $breakpoints[$width]['spaceBetween'];
            }
            if (!empty($breakpoint)) {
                $responsiveBreakpoints[$width] = $breakpoint;
            }
        }
    }

    // Get slides (child elements)
    $slides = $element->children()->orderBy('order')->get();
@endphp

{{-- Load Swiper CSS (once per page) --}}
@once
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css" />
@endonce

{{-- Carousel Container --}}
<div class="swiper" id="{{ $carouselId }}"
     @if($element->class) class="{{ $element->class }}" @endif
     @if($element->style) style="{{ $element->style }}" @endif>

    {{-- Parallax Background (single image behind all slides) --}}
    @if($hasParallax && !empty($parallax['background_url']))
        <div class="parallax-bg"
             style="background-image: url('{{ $parallax['background_url'] }}');"
             data-swiper-parallax="-23%"></div>
    @endif

    {{-- Slides Wrapper --}}
    <div class="swiper-wrapper">
        @forelse($slides as $slide)
            @php
                $slideSettings = $slide->settings ?? [];
            @endphp
            <div class="swiper-slide"
                 data-slide-id="{{ $slide->id }}"
                 @if(!empty($slideSettings['autoplay_delay']))
                     data-swiper-autoplay="{{ $slideSettings['autoplay_delay'] }}"
                 @endif
                 @if(!empty($slideSettings['hash']))
                     data-hash="{{ $slideSettings['hash'] }}"
                 @endif
                 @if(!empty($slideSettings['history']))
                     data-history="{{ $slideSettings['history'] }}"
                 @endif
                 @if(!empty($slideSettings['zoom_max_ratio']))
                     data-swiper-zoom="{{ $slideSettings['zoom_max_ratio'] }}"
                 @endif
                 style="
                     @if(!empty($slideSettings['background_color']))
                         background-color: {{ $slideSettings['background_color'] }};
                     @endif
                     @if(!empty($slideSettings['background_image_url']))
                         background-image: url('{{ $slideSettings['background_image_url'] }}');
                         background-size: cover;
                         background-position: center;
                     @endif
                     @if(!empty($slideSettings['min_height']))
                         min-height: {{ $slideSettings['min_height'] }};
                     @endif
                     @if(!empty($slideSettings['custom_style']))
                         {{ $slideSettings['custom_style'] }}
                     @endif
                 "
                 class="
                     @if(!empty($slideSettings['padding'])) {{ $slideSettings['padding'] }} @endif
                     @if(!empty($slideSettings['text_alignment'])) text-{{ $slideSettings['text_alignment'] }} @endif
                     @if(!empty($slideSettings['vertical_alignment'])) d-flex align-items-{{ $slideSettings['vertical_alignment'] }} @endif
                     @if(!empty($slideSettings['custom_class'])) {{ $slideSettings['custom_class'] }} @endif
                 ">
                @if($hasZoom)
                    {{-- Zoom container required for zoom module --}}
                    <div class="swiper-zoom-container">
                @endif

                @if($hasParallax)
                    {{-- Parallax wrapper for slide content --}}
                    <div data-swiper-parallax="-300" data-swiper-parallax-opacity="0">
                @endif

                {{-- Recursively render slide contents (nested elements and fields) --}}
                @foreach($slide->children()->orderBy('order')->get() as $child)
                    @include('slick-forms::livewire.partials.render-element', [
                        'node' => $child,
                        'registry' => $registry ?? app(\DigitalisStudios\SlickForms\Services\LayoutElementRegistry::class),
                        'formData' => $formData ?? [],
                        'visibleFieldIds' => $visibleFieldIds ?? [],
                        'visibleElementIds' => $visibleElementIds ?? [],
                    ])
                @endforeach

                @foreach($slide->fields()->orderBy('order')->get() as $field)
                    @if(in_array($field->id, $visibleFieldIds ?? []))
                        @php
                            $fieldRegistry = app(\DigitalisStudios\SlickForms\Services\FieldTypeRegistry::class);
                        @endphp
                        @if($fieldRegistry->has($field->field_type))
                            {!! $fieldRegistry->get($field->field_type)->render($field, $formData['field_' . $field->id] ?? null) !!}
                        @endif
                    @endif
                @endforeach

                @if($hasParallax)
                    </div>
                @endif

                @if($hasZoom)
                    </div>
                @endif
            </div>
        @empty
            {{-- Empty state (no slides) --}}
            <div class="swiper-slide">
                <div class="alert alert-info mb-0">
                    <i class="bi-info-circle me-1"></i>
                    No slides configured for this carousel.
                </div>
            </div>
        @endforelse
    </div>

    {{-- Navigation Arrows --}}
    @if($hasNavigation)
        <div class="swiper-button-prev" @click.stop></div>
        <div class="swiper-button-next" @click.stop></div>
    @endif

    {{-- Pagination --}}
    @if($hasPagination)
        <div class="swiper-pagination" @click.stop></div>
    @endif

    {{-- Scrollbar --}}
    @if($hasScrollbar)
        <div class="swiper-scrollbar" @click.stop></div>
    @endif
</div>

{{-- Load Swiper JS (check if not already loaded) --}}
<script>
(function() {
    if (typeof Swiper === 'undefined' && !window.swiperLoading) {
        window.swiperLoading = true;
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js';
        script.onload = function() {
            window.swiperLoaded = true;
            // Trigger any waiting initializations
            window.dispatchEvent(new Event('swiper-loaded'));
        };
        document.head.appendChild(script);
    }
})();
</script>

{{-- Initialize Swiper Instance --}}
<script>
document.addEventListener('livewire:init', () => {
    // Define init function first
    function initCarousel{{ $element->id }}() {
        const carouselEl = document.getElementById('{{ $carouselId }}');
        if (!carouselEl) {
            console.error('Carousel element not found: {{ $carouselId }}');
            return;
        }

        // Destroy existing instance if it exists
        if (window['swiper_{{ $element->id }}']) {
            try {
                window['swiper_{{ $element->id }}'].destroy(true, true);
            } catch (e) {
                console.warn('Error destroying previous Swiper instance:', e);
            }
        }

        // Initialize Swiper
        const swiper{{ $element->id }} = new Swiper('#{{ $carouselId }}', {
            // Core Parameters
            direction: '{{ $direction }}',
            speed: {{ $speed }},
            loop: {!! json_encode($loopEnabled) !!},
            rewind: {!! json_encode($settings['rewind'] ?? false) !!},
            initialSlide: {{ $settings['initialSlide'] ?? 0 }},
            slidesPerView: {!! is_numeric($slidesPerView) ? $slidesPerView : json_encode($slidesPerView) !!},
            slidesPerGroup: {{ $settings['slidesPerGroup'] ?? 1 }},
            spaceBetween: {{ $spaceBetween }},
            effect: '{{ $effect }}',
            grabCursor: {!! json_encode($grabCursor) !!},
            centeredSlides: {!! json_encode($centeredSlides) !!},
            autoHeight: {!! json_encode($autoHeight) !!},

            // Interaction Controls
            allowSlideNext: {!! json_encode($settings['allowSlideNext'] ?? true) !!},
            allowSlidePrev: {!! json_encode($settings['allowSlidePrev'] ?? true) !!},
            allowTouchMove: {!! json_encode($settings['allowTouchMove'] ?? true) !!},
            slideToClickedSlide: {!! json_encode($settings['slideToClickedSlide'] ?? false) !!},

            @if($hasNavigation)
            // Navigation
            navigation: {
                nextEl: '#{{ $carouselId }} .swiper-button-next',
                prevEl: '#{{ $carouselId }} .swiper-button-prev',
                hideOnClick: {!! json_encode($navigation['hideOnClick'] ?? false) !!},
            },
            @endif

            @if($hasPagination)
            // Pagination
            pagination: {
                el: '#{{ $carouselId }} .swiper-pagination',
                type: '{{ $pagination['type'] ?? 'bullets' }}',
                clickable: {!! json_encode($pagination['clickable'] ?? true) !!},
                dynamicBullets: {!! json_encode($pagination['dynamicBullets'] ?? false) !!},
            },
            @endif

            @if($hasScrollbar)
            // Scrollbar
            scrollbar: {
                el: '#{{ $carouselId }} .swiper-scrollbar',
                draggable: {!! json_encode($scrollbar['draggable'] ?? false) !!},
                hide: {!! json_encode($scrollbar['hide'] ?? true) !!},
            },
            @endif

            @if($hasAutoplay)
            // Autoplay
            autoplay: {
                delay: {{ $autoplay['delay'] ?? 3000 }},
                pauseOnMouseEnter: {!! json_encode($autoplay['pauseOnMouseEnter'] ?? false) !!},
                disableOnInteraction: {!! json_encode($autoplay['disableOnInteraction'] ?? true) !!},
            },
            @endif

            // Effect-specific settings
            @if($effect === 'fade')
            fadeEffect: {
                crossFade: {!! json_encode($settings['fadeEffect']['crossFade'] ?? false) !!},
            },
            @endif

            @if($effect === 'cube')
            cubeEffect: {
                shadow: {!! json_encode($settings['cubeEffect']['shadow'] ?? true) !!},
                shadowOffset: {{ $settings['cubeEffect']['shadowOffset'] ?? 20 }},
                shadowScale: {{ $settings['cubeEffect']['shadowScale'] ?? 0.94 }},
                slideShadows: {!! json_encode($settings['cubeEffect']['slideShadows'] ?? true) !!},
            },
            @endif

            @if($effect === 'coverflow')
            coverflowEffect: {
                rotate: {{ $settings['coverflowEffect']['rotate'] ?? 50 }},
                stretch: {{ $settings['coverflowEffect']['stretch'] ?? 0 }},
                depth: {{ $settings['coverflowEffect']['depth'] ?? 100 }},
                modifier: {{ $settings['coverflowEffect']['modifier'] ?? 1 }},
                scale: {{ $settings['coverflowEffect']['scale'] ?? 1 }},
                slideShadows: {!! json_encode($settings['coverflowEffect']['slideShadows'] ?? true) !!},
            },
            @endif

            // Accessibility
            a11y: {
                enabled: {!! json_encode($a11y['enabled'] ?? true) !!},
                prevSlideMessage: '{{ $a11y['prevSlideMessage'] ?? 'Previous slide' }}',
                nextSlideMessage: '{{ $a11y['nextSlideMessage'] ?? 'Next slide' }}',
                firstSlideMessage: '{{ $a11y['firstSlideMessage'] ?? 'This is the first slide' }}',
                lastSlideMessage: '{{ $a11y['lastSlideMessage'] ?? 'This is the last slide' }}',
            },

            @if($hasKeyboard)
            // Keyboard Control
            keyboard: {
                enabled: true,
                onlyInViewport: {!! json_encode($keyboard['onlyInViewport'] ?? true) !!},
            },
            @endif

            @if($hasMousewheel)
            // Mousewheel Control
            mousewheel: {
                enabled: true,
                invert: {!! json_encode($mousewheel['invert'] ?? false) !!},
                sensitivity: {{ $mousewheel['sensitivity'] ?? 1 }},
            },
            @endif

            @if($hasZoom)
            // Zoom Module
            zoom: {
                enabled: true,
                maxRatio: {{ $zoom['maxRatio'] ?? 3 }},
                minRatio: {{ $zoom['minRatio'] ?? 1 }},
                toggle: {!! json_encode($zoom['toggle'] ?? true) !!},
            },
            @endif

            @if($hasParallax)
            // Parallax Module
            parallax: {
                enabled: true,
            },
            @endif

            @if($hasFreeMode)
            // Free Mode
            freeMode: {
                enabled: true,
                momentum: {!! json_encode($freeMode['momentum'] ?? true) !!},
                sticky: {!! json_encode($freeMode['sticky'] ?? false) !!},
            },
            @endif

            @if($hasGrid)
            // Grid Layout
            grid: {
                rows: {{ $grid['rows'] ?? 1 }},
                fill: '{{ $grid['fill'] ?? 'row' }}',
            },
            @endif

            @if($hasBreakpoints && !empty($responsiveBreakpoints))
            // Responsive Breakpoints
            breakpoints: {!! json_encode($responsiveBreakpoints, JSON_UNESCAPED_SLASHES) !!},
            @endif

            @if($settings['lazy'] ?? false)
            // Lazy Loading
            lazy: {
                loadPrevNext: true,
                loadPrevNextAmount: 1,
            },
            @endif

        });

        // Store instance for external access
        window['swiper_{{ $element->id }}'] = swiper{{ $element->id }};
    }

    // Expose init function globally for builder preview mode
    window['initCarousel{{ $element->id }}'] = initCarousel{{ $element->id }};

    // Helper to wait for Swiper to be loaded
    function waitForSwiper{{ $element->id }}(callback) {
        if (typeof Swiper !== 'undefined') {
            callback();
        } else {
            window.addEventListener('swiper-loaded', callback, { once: true });
            // Fallback timeout
            setTimeout(() => {
                if (typeof Swiper !== 'undefined') {
                    callback();
                }
            }, 1000);
        }
    }

    // Initialize after DOM is ready (use setTimeout to wait for Livewire morphing to complete)
    setTimeout(() => {
        const carouselEl{{ $element->id }} = document.getElementById('{{ $carouselId }}');
        if (carouselEl{{ $element->id }}) {
            waitForSwiper{{ $element->id }}(initCarousel{{ $element->id }});
        }
    }, 100);

    // Re-initialize on Livewire updates (when form is re-rendered)
    Livewire.hook('morph.updated', ({ el, component }) => {
        const carouselEl = document.getElementById('{{ $carouselId }}');
        if (carouselEl && !carouselEl.swiper) {
            waitForSwiper{{ $element->id }}(initCarousel{{ $element->id }});
        }
    });
});
</script>

{{-- Custom Carousel Styles --}}
<style>
    #{{ $carouselId }} {
        width: 100%;
        height: auto;
    }

    #{{ $carouselId }} .swiper-slide {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: stretch;
    }

    /* Parallax background styling */
    #{{ $carouselId }} .parallax-bg {
        position: absolute;
        left: 0;
        top: 0;
        width: 130%;
        height: 100%;
        background-size: cover;
        background-position: center;
        z-index: 0;
    }

    /* Ensure slide content appears above parallax background */
    #{{ $carouselId }} .swiper-slide {
        position: relative;
        z-index: 1;
    }

    /* Navigation button styling */
    #{{ $carouselId }} .swiper-button-prev,
    #{{ $carouselId }} .swiper-button-next {
        color: var(--bs-primary, #0d6efd);
    }

    /* Pagination bullet styling */
    #{{ $carouselId }} .swiper-pagination-bullet-active {
        background: var(--bs-primary, #0d6efd);
    }
</style>
