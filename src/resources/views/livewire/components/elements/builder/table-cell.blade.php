{{-- Table Cell Builder Preview - Uses proper HTML <td> or <th> element --}}
@php
    $settings = $element->settings ?? [];
    $cellType = $settings['cell_type'] ?? 'td';
    $colspan = $settings['colspan'] ?? 1;
    $rowspan = $settings['rowspan'] ?? 1;
@endphp

<{{ $cellType }}
    class="table-cell-builder position-relative"
    style="min-height: 60px; padding: 0.5rem; cursor: pointer; background-color: {{ $cellType === 'th' ? '#e9ecef' : 'white' }}; vertical-align: top;"
    wire:click.stop="editElement({{ $element->id }})"
    data-cell-id="{{ $element->id }}"
    @if($colspan > 1) colspan="{{ $colspan }}" @endif
    @if($rowspan > 1) rowspan="{{ $rowspan }}" @endif
>
    {{-- Cell Type Badge and Delete Column Button (for TH in header) --}}
    <div class="position-absolute top-0 end-0 m-1 d-flex align-items-center gap-1" style="z-index: 5;">
        @if($cellType === 'th' && isset($isHeaderRow) && $isHeaderRow && isset($tableId) && isset($columnIndex))
            <button
                type="button"
                class="btn btn-xs btn-outline-danger p-1"
                wire:click.stop="deleteColumnFromTable({{ $tableId }}, {{ $columnIndex }})"
                title="Delete Column {{ $columnIndex + 1 }}"
                style="font-size: 0.5rem; padding: 0.1rem 0.3rem !important;">
                <i class="bi bi-trash"></i>
            </button>
        @endif
        <span class="badge bg-secondary" style="font-size: 0.6rem;">
            {{ strtoupper($cellType) }}
            @if($colspan > 1) <span class="ms-1">colspan:{{ $colspan }}</span> @endif
            @if($rowspan > 1) <span class="ms-1">rowspan:{{ $rowspan }}</span> @endif
        </span>
    </div>

    {{-- Cell Content (fields/elements via drag-and-drop) --}}
    <div class="cell-content-wrapper sortable-container"
         style="min-height: 40px;"
         data-parent-element-id="{{ $element->id }}"
         x-data="{
             init() {
                 if (typeof Sortable !== 'undefined') {
                     const componentWire = $wire;
                     new Sortable(this.$el, {
                         animation: 150,
                         handle: '.drag-handle',
                         group: 'shared',
                         filter: '.placeholder-text, .btn, button',
                         onStart: (evt) => {
                             document.getElementById('form-canvas')?.classList.add('drag-active');
                         },
                         onAdd: (evt) => {
                             let parentId = parseInt(evt.to.dataset.parentElementId);

                             // Check if this is a new item from the palette
                             if (evt.item.dataset.type === 'new-field') {
                                 const fieldType = evt.item.dataset.fieldType;
                                 evt.item.remove();
                                 componentWire.call('addField', fieldType, parentId);
                                 return;
                             }
                             if (evt.item.dataset.type === 'new-element') {
                                 const elementType = evt.item.dataset.elementType;
                                 evt.item.remove();
                                 componentWire.call('addLayoutElement', elementType, parentId);
                                 return;
                             }

                             // Collect all children in their current order
                             let items = Array.from(evt.to.children).filter(el => el.dataset.fieldId || el.dataset.elementId);
                             let orderedItems = items.map(el => {
                                 if (el.dataset.fieldId) {
                                     return { type: 'field', id: parseInt(el.dataset.fieldId) };
                                 } else if (el.dataset.elementId) {
                                     return { type: 'element', id: parseInt(el.dataset.elementId) };
                                 }
                             }).filter(item => item !== undefined);

                             if (orderedItems.length > 0) {
                                 componentWire.call('updateChildrenOrderInParent', parentId, orderedItems);
                             }
                         },
                         onEnd: (evt) => {
                             document.getElementById('form-canvas')?.classList.remove('drag-active');

                             let parentId = parseInt(evt.to.dataset.parentElementId);
                             let items = Array.from(evt.to.children).filter(el => el.dataset.fieldId || el.dataset.elementId);
                             let orderedItems = items.map(el => {
                                 if (el.dataset.fieldId) {
                                     return { type: 'field', id: parseInt(el.dataset.fieldId) };
                                 } else if (el.dataset.elementId) {
                                     return { type: 'element', id: parseInt(el.dataset.elementId) };
                                 }
                             }).filter(item => item !== undefined);

                             if (orderedItems.length > 0) {
                                 componentWire.call('updateChildrenOrderInParent', parentId, orderedItems);
                             }
                         }
                     });
                 }
             }
         }">
        @foreach($node['children'] as $child)
            @include('slick-forms::livewire.partials.builder-element', [
                'node' => $child,
                'registry' => $registry,
                'selectedField' => $selectedField,
                'selectedElement' => $selectedElement,
                'previewMode' => $previewMode,
                'pickerMode' => $pickerMode
            ])
        @endforeach

        @if(empty($node['children']))
            <div class="text-center text-muted fst-italic small p-2 placeholder-text">
                <i class="bi bi-cursor"></i><br>
                Drop fields or elements here
            </div>
        @endif
    </div>
</{{ $cellType }}>
