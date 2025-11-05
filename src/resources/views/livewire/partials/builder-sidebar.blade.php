{{-- Expanded Sidebar Content --}}
<div class="sidebar-expanded" x-show="!sidebarCollapsed" style="width: 290px;">
                {{-- Search Input with Collapse Button --}}
                <div class="p-3 pb-2">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="input-group input-group-sm flex-grow-1">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input
                                type="text"
                                class="form-control border-start-0 ps-0"
                                placeholder="Search fields..."
                                x-model="searchQuery"
                                @input="filterItems($event.target.value)"
                            >
                            <button
                                class="btn btn-outline-secondary"
                                type="button"
                                x-show="searchQuery.length > 0"
                                @click="searchQuery = ''; filterItems('')"
                                style="display: none;"
                            >
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                        <button
                            class="btn btn-sm btn-outline-secondary"
                            type="button"
                            @click="toggleSidebar()"
                            title="Collapse Sidebar"
                        >
                            <i class="bi bi-chevron-left"></i>
                        </button>
                    </div>
                </div>

                <div class="accordion" id="builderSidebar">
                    {{-- Layout Elements --}}
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#layoutCollapse" aria-expanded="true" aria-controls="layoutCollapse">
                                Layout
                            </button>
                        </h2>
                        <div id="layoutCollapse" class="accordion-collapse collapse show" data-bs-parent="#builderSidebar">
                            <div class="accordion-body p-0">
                                <div class="list-group list-group-flush"
                                     id="layout-elements-palette"
                                     x-data="{
                                         initPalette() {
                                             if (typeof Sortable !== 'undefined') {
                                                 new Sortable(this.$el, {
                                                     group: {
                                                         name: 'shared',
                                                         pull: 'clone',
                                                         put: false
                                                     },
                                                     sort: false,
                                                     filter: '.disabled',
                                                     animation: 150
                                                 });
                                             }
                                         }
                                     }"
                                     x-init="initPalette()">
                                    @foreach($availableLayoutTypes as $layoutType)
                                        @php
                                            $isTabButton = $layoutType['name'] === 'tab';
                                            $isColumnButton = $layoutType['name'] === 'column';
                                            $isAccordionItemButton = $layoutType['name'] === 'accordion_item';
                                            $isDisabled = ($isTabButton && (!$selectedElement || $selectedElement->element_type !== 'tabs'))
                                                       || ($isColumnButton && (!$selectedElement || $selectedElement->element_type !== 'row'))
                                                       || ($isAccordionItemButton && (!$selectedElement || $selectedElement->element_type !== 'accordion'));
                                        @endphp
                                        <div
                                            data-type="new-element"
                                            data-element-type="{{ $layoutType['name'] }}"
                                            class="list-group-item list-group-item-action d-flex align-items-center {{ $isDisabled ? 'disabled' : '' }}"
                                            wire:click="addLayoutElement('{{ $layoutType['name'] }}')"
                                            @if($isDisabled) style="opacity: 0.5; cursor: not-allowed;" @else style="cursor: grab;" @endif
                                        >
                                            <i class="{{ $layoutType['icon'] }} me-2"></i>
                                            {{ $layoutType['label'] }}
                                            @if($isTabButton && $isDisabled)
                                                <small class="ms-auto text-muted ps-2">(Select Tabs first)</small>
                                            @elseif($isColumnButton && $isDisabled)
                                                <small class="ms-auto text-muted ps-2">(Select Row first)</small>
                                            @elseif($isAccordionItemButton && $isDisabled)
                                                <small class="ms-auto text-muted ps-2">(Select Accordion first)</small>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#contentCollapse" aria-expanded="false" aria-controls="contentCollapse">
                                Content
                            </button>
                        </h2>
                        <div id="contentCollapse" class="accordion-collapse collapse" data-bs-parent="#builderSidebar">
                            <div class="accordion-body p-0">
                                <div class="list-group list-group-flush"
                                     id="content-types-palette"
                                     x-data="{
                                         initPalette() {
                                             if (typeof Sortable !== 'undefined') {
                                                 new Sortable(this.$el, {
                                                     group: {
                                                         name: 'shared',
                                                         pull: 'clone',
                                                         put: false
                                                     },
                                                     sort: false,
                                                     animation: 150
                                                 });
                                             }
                                         }
                                     }"
                                     x-init="initPalette()">
                                    @foreach($availableContentTypes as $contentType)
                                        <div
                                            data-type="new-field"
                                            data-field-type="{{ $contentType['name'] }}"
                                            class="list-group-item list-group-item-action d-flex align-items-center"
                                            wire:click="addField('{{ $contentType['name'] }}')"
                                            style="cursor: grab;"
                                        >
                                            <i class="{{ $contentType['icon'] }} me-2"></i>
                                            {{ $contentType['label'] }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Fields --}}
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#formFieldsCollapse" aria-expanded="false" aria-controls="formFieldsCollapse">
                                Form Fields
                            </button>
                        </h2>
                        <div id="formFieldsCollapse" class="accordion-collapse collapse" data-bs-parent="#builderSidebar">
                            <div class="accordion-body p-0">
                                <div class="list-group list-group-flush"
                                     id="form-fields-palette"
                                     x-data="{
                                         initPalette() {
                                             if (typeof Sortable !== 'undefined') {
                                                 new Sortable(this.$el, {
                                                     group: {
                                                         name: 'shared',
                                                         pull: 'clone',
                                                         put: false
                                                     },
                                                     sort: false,
                                                     animation: 150
                                                 });
                                             }
                                         }
                                     }"
                                     x-init="initPalette()">
                                    @foreach($availableFormFieldTypes as $fieldType)
                                        <div
                                            data-type="new-field"
                                            data-field-type="{{ $fieldType['name'] }}"
                                            class="list-group-item list-group-item-action d-flex align-items-center"
                                            wire:click="addField('{{ $fieldType['name'] }}')"
                                            style="cursor: grab;"
                                        >
                                            <i class="{{ $fieldType['icon'] }} me-2"></i>
                                            {{ $fieldType['label'] }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Collapsed Sidebar Content (Icons Only) --}}
            <div class="sidebar-collapsed" x-show="sidebarCollapsed" style="width: 50px; padding: 0.5rem 0;">
                {{-- Expand Button --}}
                <button
                    @click="toggleSidebar()"
                    class="btn btn-sm btn-light w-100 mb-2"
                    data-bs-toggle="popover"
                    data-bs-placement="right"
                    data-bs-content="Expand Sidebar"
                    data-bs-trigger="hover"
                >
                    <i class="bi bi-chevron-right"></i>
                </button>

                {{-- Layout Icon --}}
                <button
                    @click="toggleSidebar(); setTimeout(() => {
                        const layoutBtn = document.querySelector('#builderSidebar .accordion-button[data-bs-target=\'#layoutCollapse\']');
                        if (layoutBtn && layoutBtn.classList.contains('collapsed')) {
                            layoutBtn.click();
                        }
                    }, 100)"
                    class="btn btn-sm btn-light w-100 mb-2"
                    data-bs-toggle="popover"
                    data-bs-placement="right"
                    data-bs-content="Layout Elements"
                    data-bs-trigger="hover"
                >
                    <i class="bi bi-grid-3x3-gap"></i>
                </button>

                {{-- Content Icon --}}
                <button
                    @click="toggleSidebar(); setTimeout(() => {
                        const contentBtn = document.querySelector('#builderSidebar .accordion-button[data-bs-target=\'#contentCollapse\']');
                        if (contentBtn && contentBtn.classList.contains('collapsed')) {
                            contentBtn.click();
                        }
                    }, 100)"
                    class="btn btn-sm btn-light w-100 mb-2"
                    data-bs-toggle="popover"
                    data-bs-placement="right"
                    data-bs-content="Content Elements"
                    data-bs-trigger="hover"
                >
                    <i class="bi bi-file-text"></i>
                </button>

                {{-- Form Fields Icon --}}
                <button
                    @click="toggleSidebar(); setTimeout(() => {
                        const fieldsBtn = document.querySelector('#builderSidebar .accordion-button[data-bs-target=\'#formFieldsCollapse\']');
                        if (fieldsBtn && fieldsBtn.classList.contains('collapsed')) {
                            fieldsBtn.click();
                        }
                    }, 100)"
                    class="btn btn-sm btn-light w-100 mb-2"
                    data-bs-toggle="popover"
                    data-bs-placement="right"
                    data-bs-content="Form Fields"
                    data-bs-trigger="hover"
                >
                    <i class="bi bi-input-cursor-text"></i>
                </button>
            </div>
