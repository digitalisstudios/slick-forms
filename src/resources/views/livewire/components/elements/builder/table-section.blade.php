{{-- Table Section Builder Preview (Header/Body/Footer) - Uses proper HTML table elements --}}
@php
    $sectionTag = match($element->element_type) {
        'table_header' => 'thead',
        'table_body' => 'tbody',
        'table_footer' => 'tfoot',
        default => 'tbody'
    };

    $badgeColor = match($element->element_type) {
        'table_header' => 'primary',
        'table_body' => 'success',
        'table_footer' => 'secondary',
        default => 'dark'
    };

    $label = match($element->element_type) {
        'table_header' => 'Header',
        'table_body' => 'Body',
        'table_footer' => 'Footer',
        default => 'Section'
    };
@endphp

<{{ $sectionTag }} class="table-section-builder sortable-rows-container"
    data-parent-element-id="{{ $element->id }}"
    @if($element->element_type !== 'table_header')
    x-data="{
        init() {
            if (typeof Sortable !== 'undefined') {
                const componentWire = $wire;
                new Sortable(this.$el, {
                    animation: 150,
                    handle: '.row-controls-cell',
                    filter: '.placeholder-text',
                    draggable: '.table-row-builder',
                    onEnd: (evt) => {
                        let parentId = parseInt(evt.to.dataset.parentElementId);
                        let items = Array.from(evt.to.querySelectorAll('.table-row-builder')).filter(el => el.dataset.elementId);
                        let orderedItems = items.map(el => ({
                            type: 'element',
                            id: parseInt(el.dataset.elementId)
                        }));

                        if (orderedItems.length > 0) {
                            componentWire.call('updateChildrenOrderInParent', parentId, orderedItems);
                        }
                    }
                });
            }
        }
    }"
    @endif

    {{-- Section Rows --}}
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
        <tr>
            <td colspan="100" class="text-center text-muted fst-italic p-3 placeholder-text" style="background: #f8f9fa;">
                <i class="bi bi-info-circle me-1"></i> Click "Add Row" to add rows to this section
            </td>
        </tr>
    @endif
</{{ $sectionTag }}>
