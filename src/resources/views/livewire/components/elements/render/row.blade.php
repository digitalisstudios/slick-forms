{{--
    Row Component - Render View
    Uses getRowClass() for gutter and alignment
    Variables: $node, $registry, $formData, $visibleFieldIds, $visibleElementIds, $getElementAttributes
--}}
@php
    $attrs = $getElementAttributes($node['data']);
@endphp

<div class="{{ $node['data']->getRowClass() }} mb-3 {{ $attrs['class'] }}"
     @if($attrs['style']) style="{{ $attrs['style'] }}" @endif>
    @foreach($node['children'] as $child)
        @if($child['type'] === 'element' && $child['element_type'] === 'column')
            @php
                $colAttrs = $getElementAttributes($child['data']);
            @endphp
            <div class="{{ $child['data']->getColumnClass() }} {{ $colAttrs['class'] }}"
                 @if($colAttrs['style']) style="{{ $colAttrs['style'] }}" @endif>
                @foreach($child['children'] as $grandchild)
                    @include('slick-forms::livewire.partials.render-element', [
                        'node' => $grandchild,
                        'registry' => $registry,
                        'formData' => $formData,
                        'visibleFieldIds' => $visibleFieldIds ?? [],
                        'visibleElementIds' => $visibleElementIds ?? [],
                        'getElementAttributes' => $getElementAttributes
                    ])
                @endforeach
            </div>
        @else
            @include('slick-forms::livewire.partials.render-element', [
                'node' => $child,
                'registry' => $registry,
                'formData' => $formData,
                'visibleFieldIds' => $visibleFieldIds ?? [],
                'visibleElementIds' => $visibleElementIds ?? [],
                'getElementAttributes' => $getElementAttributes
            ])
        @endif
    @endforeach
</div>
