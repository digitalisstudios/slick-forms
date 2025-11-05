{{--
    Carousel Builder View

    Dual-mode carousel editor for FormBuilder:
    1. List Mode: Structured list of slides with drag-and-drop reordering
    2. Preview Mode: Live preview of carousel with current settings

    @var SlickFormLayoutElement $element
--}}

@php
    $settings = $element->settings ?? [];
    $builderMode = $settings['builderMode'] ?? 'list';
    $slides = $element->children()->orderBy('order')->get();
@endphp

<div class="carousel-builder-wrapper"
     data-element-id="{{ $element->id }}"
     x-data="{
         elementId: {{ $element->id }},
         swiperInstance: null,
         lastInitTime: 0,
         init() {
             this.setupAlpineListener();
             this.setupLivewireListeners();
             @if($builderMode === 'preview')
             setTimeout(() => this.initCarousel(), 100);
             @endif
         },
         setupAlpineListener() {
             window.addEventListener('carousel-mode-changed-{{ $element->id }}', (event) => {
                 if (event.detail.mode === 'preview') {
                     setTimeout(() => {
                         const carouselEl = document.getElementById('carousel-{{ $element->id }}');
                         if (carouselEl) {
                             this.initCarousel();
                         } else {
                             setTimeout(() => {
                                 const carouselEl = document.getElementById('carousel-{{ $element->id }}');
                                 if (carouselEl) {
                                     this.initCarousel();
                                 }
                             }, 300);
                         }
                     }, 300);
                 } else {
                     if (this.swiperInstance) {
                         try {
                             this.swiperInstance.destroy(true, true);
                             this.swiperInstance = null;
                         } catch (e) {}
                     }
                 }
             });
         },
         setupLivewireListeners() {
             if (typeof Livewire === 'undefined') return;

             Livewire.on('element-saved', (event) => {
                 if (event.elementId === {{ $element->id }} && event.elementType === 'carousel') {
                     setTimeout(() => {
                         const carouselEl = document.getElementById('carousel-{{ $element->id }}');
                         if (carouselEl) {
                             this.initCarousel();
                         }
                     }, 150);
                 }
             });

             // Throttled morph handler - only reinitialize once per 200ms
             let morphTimeout = null;
             Livewire.hook('morph.updated', ({ el, component }) => {
                 const carouselEl = document.getElementById('carousel-{{ $element->id }}');
                 if (carouselEl && (el === carouselEl || el.contains(carouselEl) || carouselEl.contains(el))) {
                     if (morphTimeout) clearTimeout(morphTimeout);
                     morphTimeout = setTimeout(() => {
                         this.initCarousel();
                         morphTimeout = null;
                     }, 200);
                 }
             });
         },
         waitForSwiper(callback) {
             if (typeof Swiper !== 'undefined') {
                 callback();
             } else if (window.swiperLoaded) {
                 setTimeout(() => {
                     if (typeof Swiper !== 'undefined') {
                         callback();
                     }
                 }, 100);
             } else {
                 if (!window.swiperLoading) {
                     window.swiperLoading = true;
                     const script = document.createElement('script');
                     script.src = 'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js';
                     script.onload = function() {
                         window.swiperLoaded = true;
                         window.dispatchEvent(new Event('swiper-loaded'));
                     };
                     script.onerror = function() {
                         window.swiperLoading = false;
                     };
                     document.head.appendChild(script);
                 }
                 window.addEventListener('swiper-loaded', callback, { once: true });
                 setTimeout(() => {
                     if (typeof Swiper !== 'undefined') {
                         callback();
                     }
                 }, 3000);
             }
         },
         initCarousel() {
             const carouselId = 'carousel-{{ $element->id }}';
             const carouselEl = document.getElementById(carouselId);
             if (!carouselEl) return;

             this.waitForSwiper(() => {
                 if (this.swiperInstance) {
                     try {
                         this.swiperInstance.destroy(true, true);
                     } catch (e) {}
                 }
                 @php
                     $direction = $settings['direction'] ?? 'horizontal';
                     $speed = $settings['speed'] ?? 300;
                     $loopEnabled = filter_var($settings['loop'] ?? false, FILTER_VALIDATE_BOOLEAN);
                     $slidesPerView = $settings['slidesPerView'] ?? 1;
                     $spaceBetween = $settings['spaceBetween'] ?? 0;
                     $effect = $settings['effect'] ?? 'slide';
                     $grabCursor = filter_var($settings['grabCursor'] ?? false, FILTER_VALIDATE_BOOLEAN);
                     $centeredSlides = filter_var($settings['centeredSlides'] ?? false, FILTER_VALIDATE_BOOLEAN);
                     $navigation = $settings['navigation'] ?? ['enabled' => false];
                     $pagination = $settings['pagination'] ?? ['enabled' => false];
                     $autoplay = $settings['autoplay'] ?? ['enabled' => false];
                     $parallax = $settings['parallax'] ?? ['enabled' => false];
                     $hasParallax = $parallax['enabled'] ?? false;
                 @endphp
                 const swiper = new Swiper('#' + carouselId, {
                     direction: '{{ $direction }}',
                     speed: {{ $speed }},
                     loop: {{ $loopEnabled ? 'true' : 'false' }},
                     slidesPerView: {{ is_numeric($slidesPerView) ? $slidesPerView : "'" . $slidesPerView . "'" }},
                     spaceBetween: {{ $spaceBetween }},
                     effect: '{{ $effect }}',
                     grabCursor: {{ $grabCursor ? 'true' : 'false' }},
                     centeredSlides: {{ $centeredSlides ? 'true' : 'false' }},
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
                     @if($navigation['enabled'] ?? false)
                     navigation: {
                         nextEl: '#' + carouselId + ' .swiper-button-next',
                         prevEl: '#' + carouselId + ' .swiper-button-prev',
                     },
                     @endif
                     @if($pagination['enabled'] ?? false)
                     pagination: {
                         el: '#' + carouselId + ' .swiper-pagination',
                         clickable: true,
                     },
                     @endif
                     @if($autoplay['enabled'] ?? false)
                     autoplay: {
                         delay: {{ $autoplay['delay'] ?? 3000 }},
                     },
                     @endif
                     @if($hasParallax)
                     // Parallax Module
                     parallax: {
                         enabled: true,
                     },
                     @endif
                 });
                 this.swiperInstance = swiper;
             });
         }
     }">
    {{-- Mode Toggle Buttons --}}
    <div class="carousel-mode-toggle mb-3 d-flex justify-content-between align-items-center">
        <div class="btn-group btn-group-sm" role="group" aria-label="Carousel view mode">
            <button type="button"
                    class="btn @if($builderMode === 'list') btn-primary @else btn-outline-secondary @endif"
                    wire:click="updateElementSetting({{ $element->id }}, 'builderMode', 'list')"
                    x-on:click="$dispatch('carousel-mode-changed-{{ $element->id }}', { mode: 'list' })"
                    title="List view for editing slides">
                <i class="bi-list-ul me-1"></i> List View
            </button>
            <button type="button"
                    class="btn @if($builderMode === 'preview') btn-primary @else btn-outline-secondary @endif"
                    wire:click="updateElementSetting({{ $element->id }}, 'builderMode', 'preview')"
                    x-on:click="$dispatch('carousel-mode-changed-{{ $element->id }}', { mode: 'preview' })"
                    title="Preview carousel as it will appear">
                <i class="bi-eye me-1"></i> Preview
            </button>
        </div>

        <span class="badge bg-secondary">
            {{ $slides->count() }} {{ Str::plural('slide', $slides->count()) }}
        </span>
    </div>

    @if($builderMode === 'list')
        {{-- ================================ --}}
        {{-- LIST MODE: Structured Editing   --}}
        {{-- ================================ --}}

        <div class="carousel-slides-list" @click.stop>
            {{-- Slides List --}}
            <div class="list-group carousel-sortable-slides" data-carousel-id="{{ $element->id }}">
                @forelse($slides as $slide)
                    <div class="list-group-item carousel-slide-item"
                         data-slide-id="{{ $slide->id }}"
                         wire:key="carousel-slide-{{ $slide->id }}">

                        {{-- Drag Handle --}}
                        <div class="d-flex align-items-start gap-2">
                            <span class="drag-handle text-secondary"
                                  style="cursor: move; user-select: none;"
                                  title="Drag to reorder">
                                <i class="bi-grip-vertical"></i>
                            </span>

                            {{-- Slide Info --}}
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="text-primary">
                                        @php
                                            $slideSettings = $slide->settings ?? [];
                                        @endphp
                                        @if(!empty($slideSettings['slide_icon']))
                                            <i class="{{ $slideSettings['slide_icon'] }} me-1"></i>
                                        @else
                                            <i class="bi-collection-play me-1"></i>
                                        @endif
                                        {{ $slideSettings['slide_title'] ?? 'Slide ' . $loop->iteration }}
                                    </strong>

                                    {{-- Slide Element ID Badge --}}
                                    @if($slide->element_id)
                                        <code class="badge bg-light text-dark small">#{{ $slide->element_id }}</code>
                                    @endif
                                </div>

                                {{-- Slide Drop Zone (render actual contents) --}}
                                <div class="sortable-container p-3 bg-light border border-dashed rounded"
                                     data-element-id="{{ $slide->id }}"
                                     data-type="element"
                                     style="min-height: {{ $slideSettings['min_height'] ?? '100px' }};
                                            @if(!empty($slideSettings['background_image_url']))
                                                background-image: url('{{ $slideSettings['background_image_url'] }}');
                                                background-size: cover;
                                                background-position: center;
                                                position: relative;
                                            @endif
                                            @if(!empty($slideSettings['background_color']))
                                                background-color: {{ $slideSettings['background_color'] }};
                                            @endif">

                                    @php
                                        // Build the node structure for this slide
                                        $slideNode = [
                                            'type' => 'element',
                                            'element_type' => 'container',
                                            'data' => $slide,
                                            'children' => []
                                        ];

                                        // Add child elements
                                        foreach ($slide->children as $child) {
                                            $slideNode['children'][] = [
                                                'type' => 'element',
                                                'element_type' => $child->element_type,
                                                'data' => $child,
                                                'children' => []
                                            ];
                                        }

                                        // Add fields
                                        foreach ($slide->fields as $field) {
                                            $slideNode['children'][] = [
                                                'type' => 'field',
                                                'data' => $field,
                                                'children' => []
                                            ];
                                        }
                                    @endphp

                                    @if(empty($slideNode['children']))
                                        <div class="text-center text-muted py-4">
                                            <i class="bi-download fs-3 d-block mb-2"></i>
                                            <small>Drag fields or elements here</small>
                                        </div>
                                    @else
                                        {{-- Render slide contents --}}
                                        @foreach($slideNode['children'] as $child)
                                            @include('slick-forms::livewire.partials.builder-element', [
                                                'node' => $child,
                                                'registry' => $registry ?? app(\DigitalisStudios\SlickForms\Services\FieldTypeRegistry::class),
                                                'selectedField' => $selectedField ?? null,
                                                'selectedElement' => $selectedElement ?? null,
                                                'previewMode' => $previewMode ?? false,
                                                'pickerMode' => $pickerMode ?? false,
                                            ])
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="btn-group-vertical btn-group-sm" role="group">
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        wire:click.stop="editElement({{ $slide->id }})"
                                        title="Edit slide properties">
                                    <i class="bi-pencil"></i>
                                </button>

                                <button type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        wire:click.stop="deleteLayoutElement({{ $slide->id }})"
                                        wire:confirm="Delete this slide? All contents (fields and elements) will also be deleted."
                                        title="Delete slide">
                                    <i class="bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- Empty State --}}
                    <div class="alert alert-info mb-0">
                        <i class="bi-info-circle me-1"></i>
                        <strong>No slides yet.</strong>
                        <p class="mb-0 small mt-1">
                            Click "Add Slide" below to create your first slide, then drag fields or layout elements into it.
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- Add Slide Button --}}
            <div class="d-grid mt-3" @click.stop>
                <button type="button"
                        class="btn btn-success"
                        wire:click="addSlideToCarousel({{ $element->id }})"
                        title="Add new slide to carousel">
                    <i class="bi-plus-circle me-1"></i> Add Slide
                </button>
            </div>

            {{-- Help Text --}}
            <div class="alert alert-light border mt-3 mb-0 small">
                <strong><i class="bi-lightbulb me-1"></i> Tips:</strong>
                <ul class="mb-0 ps-3">
                    <li>Drag slides to reorder them</li>
                    <li>Drag fields or elements from the left palette into slides</li>
                    <li>Click <i class="bi-pencil"></i> to edit a slide's properties</li>
                    <li>Switch to Preview mode to see your carousel in action</li>
                </ul>
            </div>
        </div>

    @else
        {{-- ================================ --}}
        {{-- PREVIEW MODE: Live Carousel     --}}
        {{-- ================================ --}}

        <div class="carousel-preview-mode" wire:key="carousel-preview-{{ $element->id }}-{{ $builderMode }}" @click.stop>
            {{-- Preview Header --}}
            <div class="alert alert-info mb-3">
                <i class="bi-eye me-1"></i>
                <strong>Preview Mode</strong>
                <p class="mb-0 small">
                    This is a preview. List View allows all editing options.
                </p>
            </div>

            {{-- Render Actual Carousel --}}
            @if($slides->count() > 0)
                {{-- Inline carousel render to avoid closure serialization issues --}}
                @php
                    $carouselId = 'carousel-' . $element->id;
                    $settings = $element->settings ?? [];
                    $parallax = $settings['parallax'] ?? [];
                    $hasParallax = !empty($parallax['enabled']);
                @endphp

                <div class="swiper" id="{{ $carouselId }}">
                    {{-- Parallax Background (single image behind all slides) --}}
                    @if($hasParallax && !empty($parallax['background_url']))
                        <div class="parallax-bg"
                             style="background-image: url('{{ $parallax['background_url'] }}');"
                             data-swiper-parallax="-23%"></div>
                    @endif

                    <div class="swiper-wrapper">
                        @foreach($slides as $slide)
                            @php
                                $slideSettings = $slide->settings ?? [];
                            @endphp
                            <div class="swiper-slide"
                                 data-slide-id="{{ $slide->id }}"
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
                                 ">
                                {{-- Render slide contents using builder preview --}}
                                @php
                                    $slideNode = [
                                        'type' => 'element',
                                        'element_type' => 'container',
                                        'data' => $slide,
                                        'children' => []
                                    ];

                                    // Add child elements
                                    foreach ($slide->children as $child) {
                                        $slideNode['children'][] = [
                                            'type' => 'element',
                                            'element_type' => $child->element_type,
                                            'data' => $child,
                                            'children' => []
                                        ];
                                    }

                                    // Add fields
                                    foreach ($slide->fields as $field) {
                                        $slideNode['children'][] = [
                                            'type' => 'field',
                                            'data' => $field,
                                            'children' => []
                                        ];
                                    }
                                @endphp

                                @if($hasParallax)
                                    {{-- Parallax wrapper for slide content --}}
                                    <div data-swiper-parallax="-300" data-swiper-parallax-opacity="0">
                                @endif

                                @if(empty($slideNode['children']))
                                    <div class="text-center text-muted py-4">
                                        <i class="bi-inbox fs-3 d-block mb-2"></i>
                                        <small>Empty slide</small>
                                    </div>
                                @else
                                    {{-- Render slide contents using builder-element partial --}}
                                    @foreach($slideNode['children'] as $child)
                                        @include('slick-forms::livewire.partials.builder-element', [
                                            'node' => $child,
                                            'registry' => $registry ?? app(\DigitalisStudios\SlickForms\Services\FieldTypeRegistry::class),
                                            'selectedField' => null,
                                            'selectedElement' => null,
                                            'previewMode' => true,
                                            'pickerMode' => false,
                                        ])
                                    @endforeach
                                @endif

                                @if($hasParallax)
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if(($settings['navigation']['enabled'] ?? false))
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>
                    @endif

                    @if(($settings['pagination']['enabled'] ?? false))
                        <div class="swiper-pagination"></div>
                    @endif
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="bi-exclamation-triangle me-1"></i>
                    No slides to preview. Add slides in List View first.
                </div>
            @endif
        </div>
    @endif
</div>

{{-- Load Swiper CSS/JS (always load for carousel builder) --}}
@once
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css" />
@endonce

<script>
(function() {
    // Load Swiper JS if not already loaded
    if (typeof Swiper === 'undefined' && !window.swiperLoading) {
        window.swiperLoading = true;
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js';
        script.onload = function() {
            window.swiperLoaded = true;
            window.dispatchEvent(new Event('swiper-loaded'));
        };
        document.head.appendChild(script);
    }
})();
</script>

{{-- SortableJS for Slide Reordering and Content Dropping (List Mode Only) --}}
@if($builderMode === 'list')
<script>
(function() {
    // Initialize sortable for slide reordering and content dropping
    document.addEventListener('livewire:init', function() {
        initCarouselSortable{{ $element->id }}();

        // Re-initialize after Livewire updates
        window.Livewire.hook('morph.updated', ({el, component}) => {
            setTimeout(() => initCarouselSortable{{ $element->id }}(), 100);
        });
    });

    function initCarouselSortable{{ $element->id }}() {
        // 1. Initialize sortable for slide reordering
        const slidesList = document.querySelector('[data-carousel-id="{{ $element->id }}"].carousel-sortable-slides');

        if (slidesList && typeof Sortable !== 'undefined') {
            new Sortable(slidesList, {
                animation: 150,
                handle: '.drag-handle',
                draggable: '.carousel-slide-item',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',

                onEnd: function(evt) {
                    const orderedItems = Array.from(slidesList.querySelectorAll('.carousel-slide-item'))
                        .map(item => ({
                            type: 'element',
                            id: parseInt(item.dataset.slideId)
                        }));

                    @this.call('updateChildrenOrderInParent', {{ $element->id }}, orderedItems);
                },
            });
        }

        // 2. Initialize sortable for each slide's content area
        const slideContainers = document.querySelectorAll('[data-carousel-id="{{ $element->id }}"] .sortable-container');

        slideContainers.forEach(container => {
            if (typeof Sortable !== 'undefined') {
                new Sortable(container, {
                    group: 'shared',
                    animation: 150,
                    handle: '.drag-handle',
                    filter: '.placeholder-text, .btn, button',
                    ghostClass: 'sortable-ghost',

                    onAdd: function(evt) {
                        const elementId = parseInt(container.dataset.elementId);
                        const index = evt.newIndex;

                        // Check if this is a new item from the palette
                        if (evt.item.dataset.type === 'new-field') {
                            const fieldType = evt.item.dataset.fieldType;
                            evt.item.remove(); // Remove the cloned placeholder
                            @this.call('addField', fieldType, elementId, false, index);
                            return;
                        }

                        if (evt.item.dataset.type === 'new-element') {
                            const elementType = evt.item.dataset.elementType;
                            evt.item.remove(); // Remove the cloned placeholder
                            @this.call('addLayoutElement', elementType, elementId, {}, index);
                            return;
                        }

                        // Get all existing items in this container (moved from elsewhere)
                        const items = Array.from(container.children)
                            .filter(el => el.dataset.fieldId || el.dataset.elementId)
                            .map((el, index) => ({
                                type: el.dataset.type,
                                id: parseInt(el.dataset.fieldId || el.dataset.elementId),
                            }));

                        if (items.length > 0) {
                            @this.call('updateChildrenOrderInParent', elementId, items);
                        }
                    },

                    onEnd: function(evt) {
                        const elementId = parseInt(container.dataset.elementId);

                        const items = Array.from(container.children)
                            .filter(el => el.dataset.fieldId || el.dataset.elementId)
                            .map((el, index) => ({
                                type: el.dataset.type,
                                id: parseInt(el.dataset.fieldId || el.dataset.elementId),
                            }));

                        if (items.length > 0) {
                            @this.call('updateChildrenOrderInParent', elementId, items);
                        }
                    }
                });
            }
        });
    }
})();
</script>
@endif

{{-- Carousel Builder Styles --}}
<style>
    .carousel-builder-wrapper {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
    }

    .carousel-slide-item {
        transition: all 0.2s ease;
    }

    .carousel-slide-item:hover {
        background-color: #f0f0f0;
    }

    .carousel-slide-item .drag-handle {
        padding: 0.5rem 0.25rem;
        opacity: 0.5;
        transition: opacity 0.2s;
    }

    .carousel-slide-item:hover .drag-handle {
        opacity: 1;
    }

    .sortable-ghost {
        opacity: 0.4;
        background: #e9ecef;
    }

    .sortable-chosen {
        background: #e7f5ff;
        border-color: #0d6efd;
    }

    .sortable-drag {
        opacity: 0.8;
    }

    /* Preview mode styling */
    .carousel-preview-mode {
        background: white;
        border-radius: 0.375rem;
        padding: 1rem;
    }

    /* Ensure Swiper navigation is visible in preview mode */
    .carousel-preview-mode .swiper {
        position: relative;
        /* No padding - let arrows position themselves inside the container */
    }

    .carousel-preview-mode .swiper-button-prev,
    .carousel-preview-mode .swiper-button-next {
        color: var(--bs-primary, #0d6efd);
        z-index: 10;
        /* Arrows positioned by Swiper - just ensure visibility */
    }

    /* Make arrows smaller and more compact in preview mode */
    .carousel-preview-mode .swiper-button-prev::after,
    .carousel-preview-mode .swiper-button-next::after {
        font-size: 20px;
    }

    /* Parallax background styling */
    .carousel-preview-mode .parallax-bg {
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
    .carousel-preview-mode .swiper-slide {
        position: relative;
        z-index: 1;
    }
</style>
