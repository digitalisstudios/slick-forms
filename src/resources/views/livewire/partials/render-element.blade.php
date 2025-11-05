@php
    // Helper function to build element class and style attributes
    $getElementAttributes = function($element) {
        $classes = [];
        $styles = [];

        // Add custom CSS classes if set
        if (!empty($element->class)) {
            $classes[] = $element->class;
        }

        // Add inline styles if set
        if (!empty($element->style)) {
            $styles[] = $element->style;
        }

        // Check conditional visibility
        $isElementVisible = true;
        if (isset($visibleElementIds) && is_array($visibleElementIds)) {
            $isElementVisible = in_array($element->id, $visibleElementIds);
        }

        // Add display:none if not visible
        if (!$isElementVisible) {
            $styles[] = 'display: none';
        }

        return [
            'class' => !empty($classes) ? implode(' ', $classes) : '',
            'style' => !empty($styles) ? implode('; ', $styles) : '',
        ];
    };
@endphp

{{-- Render a field --}}
@if($node['type'] === 'field')
    @php
        $isVisible = in_array($node['data']->id, $visibleFieldIds ?? []);
    @endphp

    <div wire:key="field-{{ $node['data']->id }}" @if(!$isVisible) style="display: none;" @endif>
        @if($node['data']->field_type === 'repeater')
            {{-- Special handling for repeater fields - needs access to Livewire component variables --}}
            @include('slick-forms::partials.repeater-render', [
                'field' => $node['data'],
                'value' => $formData['field_' . $node['data']->id] ?? null,
            ])
        @else
            {!! $registry->get($node['data']->field_type)->render($node['data'], $formData['field_' . $node['data']->id] ?? null) !!}
        @endif
    </div>

{{-- Render a layout element --}}
@elseif($node['type'] === 'element')
    @if($node['element_type'] === 'container')
        @include('slick-forms::livewire.components.elements.render.container', [
            'node' => $node,
            'registry' => $registry,
            'formData' => $formData,
            'visibleFieldIds' => $visibleFieldIds ?? [],
            'visibleElementIds' => $visibleElementIds ?? [],
            'getElementAttributes' => $getElementAttributes,
            'repeaterInstances' => $repeaterInstances ?? []
        ])

    @elseif($node['element_type'] === 'row')
        @include('slick-forms::livewire.components.elements.render.row', [
            'node' => $node,
            'registry' => $registry,
            'formData' => $formData,
            'visibleFieldIds' => $visibleFieldIds ?? [],
            'visibleElementIds' => $visibleElementIds ?? [],
            'getElementAttributes' => $getElementAttributes,
            'repeaterInstances' => $repeaterInstances ?? []
        ])

    @elseif($node['element_type'] === 'column')
        @include('slick-forms::livewire.components.elements.render.column', [
            'node' => $node,
            'registry' => $registry,
            'formData' => $formData,
            'visibleFieldIds' => $visibleFieldIds ?? [],
            'visibleElementIds' => $visibleElementIds ?? [],
            'getElementAttributes' => $getElementAttributes,
            'repeaterInstances' => $repeaterInstances ?? []
        ])

    @elseif($node['element_type'] === 'card')
        @include('slick-forms::livewire.components.elements.render.card', [
            'node' => $node,
            'registry' => $registry,
            'formData' => $formData,
            'visibleFieldIds' => $visibleFieldIds ?? [],
            'visibleElementIds' => $visibleElementIds ?? [],
            'getElementAttributes' => $getElementAttributes,
            'repeaterInstances' => $repeaterInstances ?? []
        ])

    @elseif($node['element_type'] === 'accordion')
        @include('slick-forms::livewire.components.elements.render.accordion', [
            'node' => $node,
            'registry' => $registry,
            'formData' => $formData,
            'visibleFieldIds' => $visibleFieldIds ?? [],
            'visibleElementIds' => $visibleElementIds ?? [],
            'getElementAttributes' => $getElementAttributes,
            'repeaterInstances' => $repeaterInstances ?? []
        ])

    @elseif($node['element_type'] === 'accordion_item')
        {{-- Individual accordion item element - render its children --}}
        @foreach($node['children'] as $child)
            @include('slick-forms::livewire.partials.render-element', ['node' => $child, 'registry' => $registry, 'formData' => $formData, 'visibleFieldIds' => $visibleFieldIds ?? [], 'visibleElementIds' => $visibleElementIds ?? [], 'getElementAttributes' => $getElementAttributes, 'repeaterInstances' => $repeaterInstances ?? []])
        @endforeach

    @elseif($node['element_type'] === 'tabs')
        @include('slick-forms::livewire.components.elements.render.tabs', [
            'node' => $node,
            'registry' => $registry,
            'formData' => $formData,
            'visibleFieldIds' => $visibleFieldIds ?? [],
            'visibleElementIds' => $visibleElementIds ?? [],
            'getElementAttributes' => $getElementAttributes,
            'repeaterInstances' => $repeaterInstances ?? []
        ])

    @elseif($node['element_type'] === 'tab')
        {{-- Individual tab element - render its children --}}
        @foreach($node['children'] as $child)
            @include('slick-forms::livewire.partials.render-element', ['node' => $child, 'registry' => $registry, 'formData' => $formData, 'visibleFieldIds' => $visibleFieldIds ?? [], 'visibleElementIds' => $visibleElementIds ?? [], 'getElementAttributes' => $getElementAttributes, 'repeaterInstances' => $repeaterInstances ?? []])
        @endforeach

    @elseif($node['element_type'] === 'carousel')
        @include('slick-forms::livewire.components.elements.render.carousel', [
            'element' => $node['data'],
            'node' => $node,
            'registry' => $registry,
            'formData' => $formData,
            'visibleFieldIds' => $visibleFieldIds ?? [],
            'visibleElementIds' => $visibleElementIds ?? [],
            'getElementAttributes' => $getElementAttributes,
            'repeaterInstances' => $repeaterInstances ?? []
        ])

    @elseif(in_array($node['element_type'], ['table_header', 'table_body', 'table_footer']))
        @include('slick-forms::livewire.components.elements.render.table-section', [
            'node' => $node,
            'registry' => $registry,
            'formData' => $formData,
            'visibleFieldIds' => $visibleFieldIds ?? [],
            'visibleElementIds' => $visibleElementIds ?? [],
            'getElementAttributes' => $getElementAttributes,
            'repeaterInstances' => $repeaterInstances ?? []
        ])

    @elseif($node['element_type'] === 'table_row')
        @include('slick-forms::livewire.components.elements.render.table-row', [
            'node' => $node,
            'registry' => $registry,
            'formData' => $formData,
            'visibleFieldIds' => $visibleFieldIds ?? [],
            'visibleElementIds' => $visibleElementIds ?? [],
            'getElementAttributes' => $getElementAttributes,
            'repeaterInstances' => $repeaterInstances ?? []
        ])

    @elseif($node['element_type'] === 'table_cell')
        @include('slick-forms::livewire.components.elements.render.table-cell', [
            'node' => $node,
            'registry' => $registry,
            'formData' => $formData,
            'visibleFieldIds' => $visibleFieldIds ?? [],
            'visibleElementIds' => $visibleElementIds ?? [],
            'getElementAttributes' => $getElementAttributes,
            'repeaterInstances' => $repeaterInstances ?? []
        ])

    @elseif($node['element_type'] === 'table')
        @include('slick-forms::livewire.components.elements.render.table', [
            'node' => $node,
            'registry' => $registry,
            'formData' => $formData,
            'visibleFieldIds' => $visibleFieldIds ?? [],
            'visibleElementIds' => $visibleElementIds ?? [],
            'getElementAttributes' => $getElementAttributes,
            'repeaterInstances' => $repeaterInstances ?? []
        ])
    @endif
@endif
