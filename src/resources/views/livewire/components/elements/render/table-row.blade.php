{{-- Table Row Render - Uses proper HTML <tr> element --}}
@php
    $element = $node['data'];
    $attrs = $getElementAttributes($element);
@endphp

<tr @if($attrs['class']) class="{{ $attrs['class'] }}" @endif @if($attrs['style']) style="{{ $attrs['style'] }}" @endif>
    @foreach($node['children'] as $child)
        @include('slick-forms::livewire.partials.render-element', [
            'node' => $child,
            'registry' => $registry,
            'formData' => $formData,
            'visibleFieldIds' => $visibleFieldIds,
            'visibleElementIds' => $visibleElementIds,
            'getElementAttributes' => $getElementAttributes,
            'repeaterInstances' => $repeaterInstances ?? []
        ])
    @endforeach
</tr>
