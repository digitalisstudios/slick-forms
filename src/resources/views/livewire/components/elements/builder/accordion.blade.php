{{--
    Accordion Component - Builder View
    Variables: $element, $children (array of child nodes), $registry
--}}
<div class="{{ $element->getAccordionClass() }}" id="accordion-builder-{{ $element->id }}">
    <div class="text-muted small mb-2">
        <i class="bi bi-list-nested me-1"></i>
        Accordion
        @if(!empty($element->settings['accordion_flush']))
            <span class="badge bg-secondary ms-1">Flush</span>
        @endif
        @if($element->isAccordionAlwaysOpen())
            <span class="badge bg-secondary ms-1">Always Open</span>
        @endif
    </div>

    @foreach($children as $index => $child)
        @if($child['type'] === 'element' && $child['element_type'] === 'accordion_item')
            @php
                $isDefaultOpen = ($index === $element->getDefaultOpenItem()) || $child['data']->isInitiallyOpen();
            @endphp
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button {{ !$isDefaultOpen ? 'collapsed' : '' }}"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapse-builder-{{ $element->id }}-{{ $index }}">
                        @if($child['data']->getAccordionItemIcon())
                            <i class="{{ $child['data']->getAccordionItemIcon() }} me-2"></i>
                        @endif
                        {{ $child['data']->getAccordionItemLabel() }}
                    </button>
                </h2>
                <div id="collapse-builder-{{ $element->id }}-{{ $index }}"
                     class="accordion-collapse collapse {{ $isDefaultOpen ? 'show' : '' }}">
                    <div class="accordion-body" style="min-height: 40px;">
                        {{-- Children will be rendered by parent --}}
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>
