{{--
    Container Component - Render View
    Variables: $node, $registry, $formData, $visibleFieldIds, $visibleElementIds, $getElementAttributes
--}}
@php
    $attrs = $getElementAttributes($node['data']);
@endphp

<div class="{{ $node['data']->getContainerClass() }} mb-3 {{ $attrs['class'] }}"
     @if($attrs['style']) style="{{ $attrs['style'] }}" @endif>
    @foreach($node['children'] as $child)
        @include('slick-forms::livewire.partials.render-element', [
            'node' => $child,
            'registry' => $registry,
            'formData' => $formData,
            'visibleFieldIds' => $visibleFieldIds ?? [],
            'visibleElementIds' => $visibleElementIds ?? [],
            'getElementAttributes' => $getElementAttributes
        ])
    @endforeach
</div>
