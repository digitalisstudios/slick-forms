{{--
    Container Component - Builder View
    Variables: $element, $children (slot content)
--}}
<div class="{{ $element->getContainerClass() }}" style="min-height: 60px;">
    <div class="text-muted small mb-2">
        <i class="bi bi-box me-1"></i>
        {{ $element->getContainerLabel() }}
        @if($element->isContainerFluid())
            <span class="badge bg-info ms-1">Fluid</span>
        @elseif(!empty($element->settings['container_breakpoint']))
            <span class="badge bg-info ms-1">{{ strtoupper($element->settings['container_breakpoint']) }}</span>
        @endif
    </div>
    {{ $children }}
</div>
