{{--
    Accordion Component - Render View
    Enhanced accordion with flush style, always-open mode, default open item, and icons
    Variables: $node, $registry, $formData, $visibleFieldIds, $visibleElementIds, $getElementAttributes
--}}
@php
    $attrs = $getElementAttributes($node['data']);
@endphp

<div class="{{ $node['data']->getAccordionClass() }} mb-3 {{ $attrs['class'] }}"
     id="accordion-{{ $node['data']->id }}"
     @if($attrs['style']) style="{{ $attrs['style'] }}" @endif>

    @foreach($node['children'] as $index => $child)
        @if($child['type'] === 'element' && $child['element_type'] === 'accordion_item')
            @php
                $isDefaultOpen = ($index === $node['data']->getDefaultOpenItem()) || $child['data']->isInitiallyOpen();
            @endphp
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading-{{ $node['data']->id }}-{{ $index }}">
                    <button class="accordion-button {{ !$isDefaultOpen ? 'collapsed' : '' }}"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapse-{{ $node['data']->id }}-{{ $index }}"
                            aria-expanded="{{ $isDefaultOpen ? 'true' : 'false' }}"
                            aria-controls="collapse-{{ $node['data']->id }}-{{ $index }}"
                            @unless($node['data']->isAccordionAlwaysOpen())
                                data-bs-parent="#accordion-{{ $node['data']->id }}"
                            @endunless>
                        @if($child['data']->getAccordionItemIcon())
                            <i class="{{ $child['data']->getAccordionItemIcon() }} me-2"></i>
                        @endif
                        {{ $child['data']->getAccordionItemLabel() }}
                    </button>
                </h2>
                <div id="collapse-{{ $node['data']->id }}-{{ $index }}"
                     class="accordion-collapse collapse {{ $isDefaultOpen ? 'show' : '' }}"
                     aria-labelledby="heading-{{ $node['data']->id }}-{{ $index }}"
                     @unless($node['data']->isAccordionAlwaysOpen())
                         data-bs-parent="#accordion-{{ $node['data']->id }}"
                     @endunless>
                    <div class="accordion-body">
                        @foreach($child['children'] as $accordionContent)
                            @include('slick-forms::livewire.partials.render-element', [
                                'node' => $accordionContent,
                                'registry' => $registry,
                                'formData' => $formData,
                                'visibleFieldIds' => $visibleFieldIds ?? [],
                                'visibleElementIds' => $visibleElementIds ?? [],
                                'getElementAttributes' => $getElementAttributes
                            ])
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>
