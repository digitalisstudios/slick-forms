{{-- Table Cell Render - Uses proper HTML <td> or <th> element --}}
@php
    $element = $node['data'];
    $settings = $element->settings ?? [];
    $cellType = $settings['cell_type'] ?? 'td';
    $colspan = $settings['colspan'] ?? 1;
    $rowspan = $settings['rowspan'] ?? 1;

    // Get element attributes (custom classes, styles, conditional visibility)
    $attrs = $getElementAttributes($element);
@endphp

<{{ $cellType }}
    @if($attrs['class']) class="{{ $attrs['class'] }}" @endif
    @if($attrs['style']) style="{{ $attrs['style'] }}" @endif
    @if($colspan > 1) colspan="{{ $colspan }}" @endif
    @if($rowspan > 1) rowspan="{{ $rowspan }}" @endif
>
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
</{{ $cellType }}>
