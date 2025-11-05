{{--
    Column Component - Builder View
    Variables: $element, $children (slot content)
--}}
<div class="{{ $element->getColumnClass() }}" style="min-height: 50px;">
    <div class="text-muted small mb-2">
        <i class="bi bi-layout-sidebar me-1"></i>
        Column
        @php
            $widths = $element->settings['column_width'] ?? [];
            $hasWidths = !empty(array_filter($widths));
        @endphp
        @if($hasWidths)
            <span class="badge bg-secondary ms-1">
                @foreach(['xs', 'sm', 'md', 'lg', 'xl', 'xxl'] as $bp)
                    @if(!empty($widths[$bp]))
                        {{ strtoupper($bp) }}:{{ $widths[$bp] }}
                    @endif
                @endforeach
            </span>
        @endif
    </div>
    {{ $children }}
</div>
