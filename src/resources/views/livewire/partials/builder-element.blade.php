{{-- Recursive partial for rendering layout elements and fields --}}

@if($node['type'] === 'field')
    {{-- Render a field --}}
    <div
        class="field-item"
        data-field-id="{{ $node['data']->id }}"
        data-type="field"
        wire:key="field-{{ $node['data']->id }}"
    >
        <div class="field-wrapper {{ $selectedField && $selectedField->id === $node['data']->id ? 'border-primary' : 'border-light' }}"
             style="position: relative; @if(!$previewMode) border: 1px dashed; padding: 0.75rem; @endif transition: border-color 0.2s; cursor: pointer;"
             wire:click.stop="{{ $pickerMode ? 'pickField(' . $node['data']->id . ')' : 'editField(' . $node['data']->id . ')' }}">

            @if(!$previewMode)
            {{-- Hover controls for field --}}
            <div class="field-controls" style="position: absolute; top: -25px; left: 8px; z-index: 10;">
                <div class="drag-handle" style="cursor: move; background: white; border: 1px solid #dee2e6; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem;">
                    <i class="bi bi-grip-vertical text-muted"></i>
                    <span class="text-uppercase small text-muted ms-1">FLD</span>
                </div>
            </div>
            <button
                type="button"
                class="btn btn-sm btn-outline-danger field-controls"
                wire:click.stop="deleteField({{ $node['data']->id }})"
                style="position: absolute; top: -25px; right: 8px; z-index: 10; border-radius: 0.25rem; padding: 0.25rem 0.5rem;"
            >
                <i class="bi bi-trash"></i>
            </button>
            @endif

            {!! $registry->get($node['data']->field_type)->renderBuilder($node['data']) !!}
        </div>
    </div>

@elseif($node['type'] === 'element')
    {{-- Render a layout element --}}
    @if(!isset($skipWrapper) || !$skipWrapper)
    {{-- Table cells, rows, and sections skip the outer wrapper --}}
    @if(!in_array($node['element_type'], ['table_cell', 'table_row', 'table_header', 'table_body', 'table_footer']))
    <div
        @if($node['data']->element_id) id="{{ $node['data']->element_id }}" @endif
        class="mb-3 layout-element layout-{{ $node['element_type'] }}"
        data-element-id="{{ $node['data']->id }}"
        data-type="element"
        data-element-type="{{ $node['element_type'] }}"
        wire:key="element-{{ $node['data']->id }}"
    >
    @endif
    @endif
        {{-- Table cells, rows, and sections don't need the wrapper styling or controls --}}
        @if(!in_array($node['element_type'], ['table_cell', 'table_row', 'table_header', 'table_body', 'table_footer']))
        <div class="element-wrapper {{ $selectedElement && $selectedElement->id === $node['data']->id ? 'selected' : '' }}"
             style="position: relative;
                    @if(!$previewMode || $node['element_type'] === 'card')
                    border: 1px {{ $node['element_type'] === 'card' ? 'solid' : ($node['element_type'] === 'accordion' ? 'dotted' : 'dashed') }} {{ $selectedElement && $selectedElement->id === $node['data']->id ? '#198754' : '#dee2e6' }};
                    @endif
                    @if(!$previewMode)
                    min-height: 80px;
                    @endif
                    @if(!$previewMode || $node['element_type'] === 'card')
                    padding: 0.5rem;
                    @endif
                    {{ $node['element_type'] === 'card' ? 'background-color: #f8f9fa;' : '' }}
                    transition: border-color 0.2s;
                    cursor: pointer;"
             wire:click.stop="editElement({{ $node['data']->id }})">

            @if(!$previewMode)
            {{-- Hover controls --}}
            <div class="element-controls" style="position: absolute; top: -25px; left: 8px; z-index: 10;">
                <div class="drag-handle" style="cursor: move; background: white; border: 1px solid #dee2e6; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem;">
                    <i class="bi bi-grip-vertical text-muted"></i>
                    <span class="text-uppercase small text-muted ms-1">
                        @if($node['element_type'] === 'container')
                            CON
                        @elseif($node['element_type'] === 'row')
                            ROW
                        @elseif($node['element_type'] === 'column')
                            COL
                        @elseif($node['element_type'] === 'card')
                            CARD
                        @elseif($node['element_type'] === 'accordion')
                            ACC
                        @elseif($node['element_type'] === 'accordion_item')
                            ITEM
                        @elseif($node['element_type'] === 'tabs')
                            TABS
                        @elseif($node['element_type'] === 'tab')
                            TAB
                        @elseif($node['element_type'] === 'carousel')
                            CAROUSEL
                        @elseif($node['element_type'] === 'table')
                            TABLE
                        @endif
                    </span>
                </div>
            </div>
            <button
                type="button"
                class="btn btn-sm btn-outline-danger element-controls"
                wire:click.stop="deleteLayoutElement({{ $node['data']->id }})"
                onclick="return confirm('Are you sure? This will delete all nested elements and fields.');"
                style="position: absolute; top: -25px; right: 8px; z-index: 10; border-radius: 0.25rem; padding: 0.25rem 0.5rem;"
            >
                <i class="bi bi-trash"></i>
            </button>
            @endif
        @endif

            @if($node['element_type'] === 'row')
                    <div class="row sortable-container"
                         data-parent-element-id="{{ $node['data']->id }}"
                         x-data="{
                             initSortable() {
                                 if (typeof Sortable !== 'undefined') {
                                     new Sortable(this.$el, {
                                         animation: 150,
                                         handle: '.drag-handle',
                                         group: 'shared',
                                         filter: '.placeholder-text, .btn, button',
                                         onStart: (evt) => {
                                             document.getElementById('form-canvas').classList.add('drag-active');
                                         },
                                         onAdd: (evt) => {
                                             let parentId = parseInt(evt.to.dataset.parentElementId);
                                             const index = evt.newIndex;

                                             // Mark item as handled to prevent parent containers from also processing it
                                             evt.item.dataset.handledBy = 'row';

                                             // Check if this is a new item from the palette
                                             if (evt.item.dataset.type === 'new-field') {
                                                 const fieldType = evt.item.dataset.fieldType;
                                                 evt.item.remove();
                                                 @this.call('addField', fieldType, parentId, false, index);
                                                 return;
                                             }
                                             if (evt.item.dataset.type === 'new-element') {
                                                 const elementType = evt.item.dataset.elementType;
                                                 evt.item.remove();
                                                 @this.call('addLayoutElement', elementType, parentId, {}, index);
                                                 return;
                                             }

                                             // Collect all children (fields and elements) in their current order
                                             let items = Array.from(evt.to.children).filter(el => el.dataset.fieldId || el.dataset.elementId);
                                             let orderedItems = items.map(el => {
                                                 if (el.dataset.fieldId) {
                                                     return { type: 'field', id: parseInt(el.dataset.fieldId) };
                                                 } else if (el.dataset.elementId) {
                                                     return { type: 'element', id: parseInt(el.dataset.elementId) };
                                                 }
                                             }).filter(item => item !== undefined);

                                             if (orderedItems.length > 0) {
                                                 @this.call('updateChildrenOrderInParent', parentId, orderedItems);
                                             }
                                         },
                                         onEnd: (evt) => {
                                             document.getElementById('form-canvas').classList.remove('drag-active');

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
                                                 @this.call('updateChildrenOrderInParent', parentId, orderedItems);
                                             }
                                         }
                                     });
                                 }
                             }
                         }"
                         x-init="initSortable()">
                        @foreach($node['children'] as $child)
                            @if($child['type'] === 'element' && $child['element_type'] === 'column')
                                <div class="{{ $child['data']->getColumnClass() }}"
                                     data-element-id="{{ $child['data']->id }}"
                                     data-type="element"
                                     data-element-type="column"
                                     wire:key="element-{{ $child['data']->id }}">
                                    @include('slick-forms::livewire.partials.builder-element', ['node' => $child, 'skipWrapper' => true, 'registry' => $registry, 'selectedField' => $selectedField, 'selectedElement' => $selectedElement, 'previewMode' => $previewMode, 'pickerMode' => $pickerMode])
                                </div>
                            @else
                                @include('slick-forms::livewire.partials.builder-element', ['node' => $child, 'registry' => $registry, 'selectedField' => $selectedField, 'selectedElement' => $selectedElement, 'previewMode' => $previewMode, 'pickerMode' => $pickerMode])
                            @endif
                        @endforeach
                        @if(empty($node['children']))
                            <div class="placeholder-text text-muted text-center py-3 small">
                                Drop content here
                            </div>
                        @endif
                    </div>

                @elseif($node['element_type'] === 'accordion')
                    {{-- Accordion with Bootstrap accordion UI --}}
                    <div class="accordion" id="accordion-{{ $node['data']->id }}">
                        @foreach($node['children'] as $index => $accordionChild)
                            @if($accordionChild['type'] === 'element' && $accordionChild['element_type'] === 'accordion_item')
                                <div class="accordion-item position-relative" data-accordion-item-id="{{ $accordionChild['data']->id }}" style="cursor: move;">
                                    <h2 class="accordion-header" id="heading-{{ $node['data']->id }}-{{ $index }}">
                                        <button
                                            class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#collapse-{{ $node['data']->id }}-{{ $index }}"
                                            aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                            aria-controls="collapse-{{ $node['data']->id }}-{{ $index }}"
                                            wire:click.stop="editElement({{ $accordionChild['data']->id }})"
                                        >
                                            <i class="bi bi-grip-vertical text-muted me-2" style="font-size: 0.75rem;"></i>
                                            {{ $accordionChild['data']->getAccordionItemLabel() }}
                                        </button>
                                    </h2>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-danger position-absolute tab-delete-btn"
                                        style="top: 8px; right: 8px; padding: 0.125rem 0.25rem; font-size: 0.7rem; line-height: 1; z-index: 1000;"
                                        wire:click.stop="deleteLayoutElement({{ $accordionChild['data']->id }})"
                                        onclick="return confirm('Are you sure you want to delete this accordion item and all its content?')"
                                    >
                                        <i class="bi bi-x"></i>
                                    </button>
                                    <div
                                        id="collapse-{{ $node['data']->id }}-{{ $index }}"
                                        class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                        aria-labelledby="heading-{{ $node['data']->id }}-{{ $index }}"
                                        data-bs-parent="#accordion-{{ $node['data']->id }}"
                                    >
                                        <div class="accordion-body">
                                            @include('slick-forms::livewire.partials.builder-element', ['node' => $accordionChild, 'skipWrapper' => true, 'registry' => $registry, 'selectedField' => $selectedField, 'selectedElement' => $selectedElement, 'previewMode' => $previewMode, 'pickerMode' => $pickerMode])
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        @if(empty($node['children']))
                            <div class="alert alert-info mt-3">
                                <i class="bi bi-info-circle me-2"></i>Add accordion items using the "Accordion Item" button, then click on an item to add content to it.
                            </div>
                        @endif
                    </div>

                @elseif($node['element_type'] === 'tabs')
                    {{-- Tabs with Bootstrap tab UI --}}
                    <div x-data="{ activeTab: 0 }">
                        {{-- Tab navigation --}}
                        <ul class="nav nav-tabs"
                            role="tablist"
                            data-parent-element-id="{{ $node['data']->id }}"
                            x-data="{
                                initTabSortable() {
                                    if (typeof Sortable !== 'undefined') {
                                        const componentWire = $wire;
                                        new Sortable(this.$el, {
                                            animation: 150,
                                            filter: 'button',
                                            onEnd: (evt) => {
                                                let parentId = parseInt(evt.to.dataset.parentElementId);
                                                let items = Array.from(evt.to.children).filter(el => el.dataset.tabId);
                                                let orderedItems = items.map(el => ({
                                                    type: 'element',
                                                    id: parseInt(el.dataset.tabId)
                                                }));

                                                if (orderedItems.length > 0) {
                                                    componentWire.call('updateChildrenOrderInParent', parentId, orderedItems);
                                                }
                                            }
                                        });
                                    }
                                }
                            }"
                            x-init="initTabSortable()">
                            @foreach($node['children'] as $index => $tabChild)
                                @if($tabChild['type'] === 'element' && $tabChild['element_type'] === 'tab')
                                    <li class="nav-item position-relative" role="presentation" data-tab-id="{{ $tabChild['data']->id }}" style="cursor: move;">
                                        <button
                                            class="nav-link"
                                            :class="{ 'active': activeTab === {{ $index }} }"
                                            @click.prevent="activeTab = {{ $index }}"
                                            wire:click.stop="editElement({{ $tabChild['data']->id }})"
                                            type="button"
                                        >
                                            <i class="bi bi-grip-vertical text-muted me-1" style="font-size: 0.75rem;"></i>
                                            {{ $tabChild['data']->getTabLabel() }}
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-danger position-absolute tab-delete-btn"
                                            style="top: -8px; right: -8px; padding: 0.125rem 0.25rem; font-size: 0.7rem; line-height: 1; z-index: 1000;"
                                            wire:click.stop="deleteLayoutElement({{ $tabChild['data']->id }})"
                                            onclick="return confirm('Are you sure you want to delete this tab and all its content?')"
                                        >
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </li>
                                @endif
                            @endforeach
                        </ul>

                        {{-- Tab content --}}
                        <div class="tab-content pt-3">
                            @foreach($node['children'] as $index => $tabChild)
                                @if($tabChild['type'] === 'element' && $tabChild['element_type'] === 'tab')
                                    <div
                                        x-show="activeTab === {{ $index }}"
                                        class="tab-pane"
                                        :class="{ 'active': activeTab === {{ $index }} }"
                                        role="tabpanel"
                                    >
                                        @include('slick-forms::livewire.partials.builder-element', ['node' => $tabChild, 'skipWrapper' => true, 'registry' => $registry, 'selectedField' => $selectedField, 'selectedElement' => $selectedElement, 'previewMode' => $previewMode, 'pickerMode' => $pickerMode])
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        @if(empty($node['children']))
                            <div class="alert alert-info mt-3">
                                <i class="bi bi-info-circle me-2"></i>Add tabs using the "Tab" button, then click on a tab to add content to it.
                            </div>
                        @endif
                    </div>

                @elseif($node['element_type'] === 'carousel')
                    {{-- Carousel with Swiper.js --}}
                    @include('slick-forms::livewire.components.elements.builder.carousel', [
                        'element' => $node['data'],
                        'node' => $node,
                        'registry' => $registry,
                        'selectedField' => $selectedField,
                        'selectedElement' => $selectedElement,
                        'previewMode' => $previewMode,
                        'pickerMode' => $pickerMode,
                    ])

                @elseif(in_array($node['element_type'], ['table_header', 'table_body', 'table_footer']))
                    {{-- Table sections: keep wrapper but use display: table-row-group --}}
                    @include('slick-forms::livewire.components.elements.builder.table-section', [
                        'element' => $node['data'],
                        'node' => $node,
                        'registry' => $registry,
                        'selectedField' => $selectedField,
                        'selectedElement' => $selectedElement,
                        'previewMode' => $previewMode,
                        'pickerMode' => $pickerMode,
                        'label' => match($node['element_type']) {
                            'table_header' => 'Header',
                            'table_footer' => 'Footer',
                            default => 'Body'
                        },
                        'badgeColor' => match($node['element_type']) {
                            'table_header' => 'primary',
                            'table_footer' => 'secondary',
                            default => 'success'
                        }
                    ])

                @elseif($node['element_type'] === 'table_row')
                    {{-- Table rows: keep wrapper but use display: table-row --}}
                    @include('slick-forms::livewire.components.elements.builder.table-row', [
                        'element' => $node['data'],
                        'node' => $node,
                        'registry' => $registry,
                        'selectedField' => $selectedField,
                        'selectedElement' => $selectedElement,
                        'previewMode' => $previewMode,
                        'pickerMode' => $pickerMode
                    ])

                @elseif($node['element_type'] === 'table_cell')
                    {{-- Table cells: keep wrapper but use display: table-cell --}}
                    @include('slick-forms::livewire.components.elements.builder.table-cell', [
                        'element' => $node['data'],
                        'node' => $node,
                        'registry' => $registry,
                        'selectedField' => $selectedField,
                        'selectedElement' => $selectedElement,
                        'previewMode' => $previewMode,
                        'pickerMode' => $pickerMode
                    ])

                @elseif($node['element_type'] === 'table')
                    {{-- Table with nested structure --}}
                    @include('slick-forms::livewire.components.elements.builder.table', [
                        'element' => $node['data'],
                        'node' => $node,
                        'registry' => $registry,
                        'selectedField' => $selectedField,
                        'selectedElement' => $selectedElement,
                        'previewMode' => $previewMode,
                        'pickerMode' => $pickerMode
                    ])

                @else
                    <div class="sortable-container sortable-{{ $node['data']->id }} {{ $node['element_type'] === 'container' ? 'container' : '' }}"
                        
                         data-parent-element-id="{{ $node['data']->id }}"
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
                                             document.getElementById('form-canvas').classList.add('drag-active');
                                         },
                                         onAdd: (evt) => {
                                             let parentId = parseInt(evt.to.dataset.parentElementId);
                                             const index = evt.newIndex;

                                             // Mark item as handled to prevent parent containers from also processing it
                                             evt.item.dataset.handledBy = 'container';

                                             // Check if this is a new item from the palette
                                             if (evt.item.dataset.type === 'new-field') {
                                                 const fieldType = evt.item.dataset.fieldType;
                                                 evt.item.remove();
                                                 componentWire.call('addField', fieldType, parentId, false, index);
                                                 return;
                                             }
                                             if (evt.item.dataset.type === 'new-element') {
                                                 const elementType = evt.item.dataset.elementType;
                                                 evt.item.remove();
                                                 componentWire.call('addLayoutElement', elementType, parentId, {}, index);
                                                 return;
                                             }

                                             // Collect all children (fields and elements) in their current order
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
                            @include('slick-forms::livewire.partials.builder-element', ['node' => $child, 'registry' => $registry, 'selectedField' => $selectedField, 'selectedElement' => $selectedElement, 'previewMode' => $previewMode, 'pickerMode' => $pickerMode])
                        @endforeach
                        @if(empty($node['children']))
                        <div class="placeholder-text text-muted text-center py-3 small">
                            Drop content here
                        </div>
                        @endif
                    </div>
                @endif
        {{-- Close element-wrapper if we opened it --}}
        @if(!in_array($node['element_type'], ['table_cell', 'table_row', 'table_header', 'table_body', 'table_footer']))
        </div>
        @endif
    @if(!isset($skipWrapper) || !$skipWrapper)
    {{-- Close outer wrapper if we opened it --}}
    @if(!in_array($node['element_type'], ['table_cell', 'table_row', 'table_header', 'table_body', 'table_footer']))
    </div>
    @endif
    @endif
@endif
