{{--
    Row Component - Builder View
    Variables: $element, $children (slot content)
--}}
<div class="{{ $element->getRowClass() }}" style="min-height: 60px;">
    <div class="text-muted small mb-2">
        <i class="bi bi-layout-three-columns me-1"></i>
        Row
        @if(!empty($element->settings['gutter']))
            <span class="badge bg-secondary ms-1">{{ $element->settings['gutter'] }}</span>
        @endif
        @if(!empty($element->settings['horizontal_alignment']))
            <span class="badge bg-secondary ms-1">{{ str_replace('justify-content-', '', $element->settings['horizontal_alignment']) }}</span>
        @endif
    </div>
    {{ $children }}
</div>
