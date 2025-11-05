{{-- Table Component - Render View (Nested Element Structure) --}}
@php
    $element = $node['data'];
    $settings = $element->settings ?? [];
    $striped = $settings['striped'] ?? false;
    $bordered = $settings['bordered'] ?? true;
    $borderless = $settings['borderless'] ?? false;
    $hover = $settings['hover'] ?? false;
    $size = $settings['size'] ?? '';
    $responsive = $settings['responsive'] ?? true;

    // Get element attributes (custom classes, styles, conditional visibility)
    $attrs = $getElementAttributes($element);

    // Build table class string
    $tableClasses = ['table'];
    if ($striped) $tableClasses[] = 'table-striped';
    if ($borderless) $tableClasses[] = 'table-borderless';
    elseif ($bordered) $tableClasses[] = 'table-bordered';
    if ($hover) $tableClasses[] = 'table-hover';
    if ($size) $tableClasses[] = $size;

    $tableClassString = implode(' ', $tableClasses);
    if ($attrs['class']) {
        $tableClassString .= ' ' . $attrs['class'];
    }
@endphp

@if($responsive)
    <div class="table-responsive">
@endif

<table class="{{ $tableClassString }}" @if($attrs['style']) style="{{ $attrs['style'] }}" @endif>
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
</table>

@if($responsive)
    </div>
@endif
