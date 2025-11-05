{{-- Render a layout element or field inside a repeater instance --}}
@php
    $nodeType = $node['type'];
    $nodeData = $node['data'];
@endphp

@if($nodeType === 'field')
    {{-- Render field with wire:model replacement for repeater nesting --}}
    @php
        $childFieldType = $registry->get($nodeData->field_type);
        $childValue = $formData['field_' . $repeaterId][$instanceIndex]['field_' . $nodeData->id] ?? null;
        $renderedHtml = $childFieldType->render($nodeData, $childValue);

        // Replace wire:model to nest under repeater array
        $renderedHtml = preg_replace(
            '/wire:model(\.live|\.defer)?="formData\.field_' . $nodeData->id . '"/',
            'wire:model$1="formData.field_' . $repeaterId . '.' . $instanceIndex . '.field_' . $nodeData->id . '"',
            $renderedHtml
        );
    @endphp
    {!! $renderedHtml !!}

@elseif($nodeType === 'element')
    {{-- Render layout element recursively --}}
    @php
        $element = $nodeData;
        $elementType = $element->element_type;
        $children = $node['children'] ?? [];
    @endphp

    @if($elementType === 'container')
        @include('slick-forms::livewire.components.elements.render.container', [
            'node' => $node,
            'registry' => $registry,
            'formData' => $formData,
            'repeaterId' => $repeaterId,
            'instanceIndex' => $instanceIndex,
        ])
    @elseif($elementType === 'row')
        <div class="row {{ $element->settings['gutter'] ?? 'g-3' }} {{ $element->settings['vertical_align'] ?? '' }} {{ $element->settings['horizontal_align'] ?? '' }}"
             @if($element->element_id) id="{{ $element->element_id }}" @endif
             @if($element->class) class="{{ $element->class }}" @endif
             @if($element->style) style="{{ $element->style }}" @endif>
            @foreach($children as $child)
                @include('slick-forms::livewire.components.elements.render-element-in-repeater', [
                    'node' => $child,
                    'registry' => $registry,
                    'formData' => $formData,
                    'repeaterId' => $repeaterId,
                    'instanceIndex' => $instanceIndex,
                ])
            @endforeach
        </div>
    @elseif($elementType === 'column')
        @php
            $colWidth = $element->settings['column_width'] ?? 'equal';
            $colClasses = [];

            if ($colWidth === 'equal') {
                $colClasses[] = 'col';
            } elseif ($colWidth === 'auto') {
                $colClasses[] = 'col-auto';
            } elseif (is_array($colWidth)) {
                // Responsive widths: ['xs' => '12', 'md' => '6', 'lg' => '4']
                foreach ($colWidth as $breakpoint => $width) {
                    if ($breakpoint === 'xs') {
                        $colClasses[] = 'col-' . $width;
                    } else {
                        $colClasses[] = 'col-' . $breakpoint . '-' . $width;
                    }
                }
            } else {
                $colClasses[] = 'col-md-' . $colWidth;
            }
        @endphp
        <div class="{{ implode(' ', $colClasses) }}"
             @if($element->element_id) id="{{ $element->element_id }}" @endif
             @if($element->class) class="{{ $element->class }}" @endif
             @if($element->style) style="{{ $element->style }}" @endif>
            @foreach($children as $child)
                @include('slick-forms::livewire.components.elements.render-element-in-repeater', [
                    'node' => $child,
                    'registry' => $registry,
                    'formData' => $formData,
                    'repeaterId' => $repeaterId,
                    'instanceIndex' => $instanceIndex,
                ])
            @endforeach
        </div>
    @elseif($elementType === 'card')
        @include('slick-forms::livewire.components.elements.render.card', [
            'node' => $node,
            'registry' => $registry,
            'formData' => $formData,
            'repeaterId' => $repeaterId,
            'instanceIndex' => $instanceIndex,
        ])
    @elseif($elementType === 'accordion')
        @include('slick-forms::livewire.components.elements.render.accordion', [
            'node' => $node,
            'registry' => $registry,
            'formData' => $formData,
            'repeaterId' => $repeaterId,
            'instanceIndex' => $instanceIndex,
        ])
    @elseif($elementType === 'tabs')
        @include('slick-forms::livewire.components.elements.render.tabs', [
            'node' => $node,
            'registry' => $registry,
            'formData' => $formData,
            'repeaterId' => $repeaterId,
            'instanceIndex' => $instanceIndex,
        ])
    @elseif($elementType === 'table')
        @include('slick-forms::livewire.components.elements.render.table', [
            'node' => $node,
            'registry' => $registry,
            'formData' => $formData,
            'repeaterId' => $repeaterId,
            'instanceIndex' => $instanceIndex,
        ])
    @endif
@endif
