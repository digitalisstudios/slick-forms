{{-- Table Row Builder Preview - Uses proper HTML <tr> element --}}
@php
    // Check if this row is in a header section
    $parentElement = \DigitalisStudios\SlickForms\Models\SlickFormLayoutElement::find($element->parent_id);
    $isHeaderRow = $parentElement && $parentElement->element_type === 'table_header';

    // Find the table ID by traversing up the parent chain
    $tableId = null;
    $current = $element;
    while ($current && $current->parent_id) {
        $current = \DigitalisStudios\SlickForms\Models\SlickFormLayoutElement::find($current->parent_id);
        if ($current && $current->element_type === 'table') {
            $tableId = $current->id;
            break;
        }
    }
@endphp

<tr class="table-row-builder" data-element-id="{{ $element->id }}">
    {{-- Row Controls Cell --}}
    <td class="row-controls-cell position-relative" style="width: 40px; background: #f8f9fa; border-right: 2px solid #dee2e6; vertical-align: middle; padding: 0.25rem; @if(!$isHeaderRow) cursor: move; @endif">
        <div class="d-flex flex-column align-items-center gap-1">
            @if(!$isHeaderRow)
                {{-- Drag Handle (only for non-header rows) --}}
                <div class="text-muted" style="cursor: move; font-size: 0.8rem;" title="Drag to reorder">
                    <i class="bi bi-grip-vertical"></i>
                </div>
            @endif

            {{-- TR Badge --}}
            <span class="badge bg-info" style="font-size: 0.5rem;">
                TR
            </span>

            {{-- Control Buttons --}}
            <button
                type="button"
                class="btn btn-xs btn-outline-primary p-1"
                wire:click.stop="editElement({{ $element->id }})"
                title="Edit Row"
                style="font-size: 0.6rem;">
                <i class="bi bi-pencil"></i>
            </button>

            @if(!$isHeaderRow)
                {{-- Delete button (only for non-header rows) --}}
                <button
                    type="button"
                    class="btn btn-xs btn-outline-danger p-1"
                    wire:click.stop="deleteTableRow({{ $element->id }})"
                    title="Delete Row"
                    style="font-size: 0.6rem;">
                    <i class="bi bi-trash"></i>
                </button>
            @endif
        </div>
    </td>

    {{-- Row Cells --}}
    @foreach($node['children'] as $columnIndex => $child)
        @include('slick-forms::livewire.partials.builder-element', [
            'node' => $child,
            'registry' => $registry,
            'selectedField' => $selectedField,
            'selectedElement' => $selectedElement,
            'previewMode' => $previewMode,
            'pickerMode' => $pickerMode,
            'columnIndex' => $columnIndex,
            'isHeaderRow' => $isHeaderRow,
            'tableId' => $tableId
        ])
    @endforeach

    @if(empty($node['children']))
        <td colspan="100" class="text-muted small p-3 text-center fst-italic">
            <i class="bi bi-info-circle me-1"></i> Empty row
        </td>
    @endif
</tr>
