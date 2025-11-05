{{-- Table Section Render (Header/Body/Footer) - Uses proper HTML table elements --}}
@php
    $element = $node['data'];
    $sectionTag = match($element->element_type) {
        'table_header' => 'thead',
        'table_body' => 'tbody',
        'table_footer' => 'tfoot',
        default => 'tbody'
    };
@endphp

<{{ $sectionTag }}>
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
</{{ $sectionTag }}>
