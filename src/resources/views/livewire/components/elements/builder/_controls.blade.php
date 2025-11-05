{{--
    Shared Builder Controls (Drag Handle + Delete Button)
    Variables: $elementType, $elementId, $deleteConfirm (optional)
--}}
@if(!$previewMode)
    {{-- Drag Handle --}}
    <div class="element-controls" style="position: absolute; top: -25px; left: 8px; z-index: 10;">
        <div class="drag-handle" style="cursor: move; background: white; border: 1px solid #dee2e6; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem;">
            <i class="bi bi-grip-vertical text-muted"></i>
            <span class="text-uppercase small text-muted ms-1">
                @if($elementType === 'container')
                    CON
                @elseif($elementType === 'row')
                    ROW
                @elseif($elementType === 'column')
                    COL
                @elseif($elementType === 'card')
                    CARD
                @elseif($elementType === 'accordion')
                    ACC
                @elseif($elementType === 'accordion_item')
                    ITEM
                @elseif($elementType === 'tabs')
                    TABS
                @elseif($elementType === 'tab')
                    TAB
                @endif
            </span>
        </div>
    </div>

    {{-- Delete Button --}}
    <button
        type="button"
        class="btn btn-sm btn-outline-danger element-controls"
        wire:click.stop="deleteLayoutElement({{ $elementId }})"
        @if(isset($deleteConfirm) && $deleteConfirm)
            onclick="return confirm('Are you sure? This will delete all nested elements and fields.');"
        @endif
        style="position: absolute; top: -25px; right: 8px; z-index: 10; border-radius: 0.25rem; padding: 0.25rem 0.5rem;"
    >
        <i class="bi bi-trash"></i>
    </button>
@endif
