{{--
    Tabs Component - Render View
    Enhanced tabs with styles, alignment, icons, and fade animation
    Variables: $node, $registry, $formData, $visibleFieldIds, $visibleElementIds, $getElementAttributes
--}}
@php
    $attrs = $getElementAttributes($node['data']);
@endphp

<div class="mb-3 {{ $attrs['class'] }}"
     @if($attrs['style']) style="{{ $attrs['style'] }}" @endif>

    {{-- Tab Navigation --}}
    <ul class="{{ $node['data']->getTabsClass() }}"
        id="tabs-{{ $node['data']->id }}"
        role="tablist">
        @foreach($node['children'] as $index => $child)
            @if($child['type'] === 'element' && $child['element_type'] === 'tab')
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $index === $node['data']->getDefaultActiveTab() ? 'active' : '' }}"
                            id="tab-{{ $node['data']->id }}-{{ $index }}-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#tab-{{ $node['data']->id }}-{{ $index }}"
                            type="button"
                            role="tab"
                            aria-controls="tab-{{ $node['data']->id }}-{{ $index }}"
                            aria-selected="{{ $index === $node['data']->getDefaultActiveTab() ? 'true' : 'false' }}">
                        @if($child['data']->getTabIcon())
                            <i class="{{ $child['data']->getTabIcon() }} me-1"></i>
                        @endif
                        {{ $child['data']->getTabLabel() }}
                    </button>
                </li>
            @endif
        @endforeach
    </ul>

    {{-- Tab Content --}}
    <div class="tab-content pt-3" id="tabs-{{ $node['data']->id }}-content">
        @foreach($node['children'] as $index => $child)
            @if($child['type'] === 'element' && $child['element_type'] === 'tab')
                <div class="tab-pane {{ $node['data']->hasFadeAnimation() ? 'fade' : '' }} {{ $index === $node['data']->getDefaultActiveTab() ? 'show active' : '' }}"
                     id="tab-{{ $node['data']->id }}-{{ $index }}"
                     role="tabpanel"
                     aria-labelledby="tab-{{ $node['data']->id }}-{{ $index }}-tab">
                    @foreach($child['children'] as $tabContent)
                        @include('slick-forms::livewire.partials.render-element', [
                            'node' => $tabContent,
                            'registry' => $registry,
                            'formData' => $formData,
                            'visibleFieldIds' => $visibleFieldIds ?? [],
                            'visibleElementIds' => $visibleElementIds ?? [],
                            'getElementAttributes' => $getElementAttributes
                        ])
                    @endforeach
                </div>
            @endif
        @endforeach
    </div>
</div>
