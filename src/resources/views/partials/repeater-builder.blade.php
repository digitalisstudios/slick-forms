{{-- Repeater Field Builder Preview --}}
@php
    $registry = app(\DigitalisStudios\SlickForms\Services\FieldTypeRegistry::class);
    $layoutService = app(\DigitalisStudios\SlickForms\Services\FormLayoutService::class);

    // Get Livewire component variables if not already set
    if (!isset($selectedElement)) {
        $selectedElement = $this->selectedElement ?? null;
    }
    if (!isset($selectedField)) {
        $selectedField = $this->selectedField ?? null;
    }
    if (!isset($previewMode)) {
        $previewMode = $this->previewMode ?? false;
    }
    if (!isset($pickerMode)) {
        $pickerMode = $this->pickerMode ?? false;
    }

    // Load children (both fields and elements)
    // Only load direct field children (not nested in layout elements)
    $fieldChildren = $field->children()
        ->whereNull('slick_form_layout_element_id')
        ->orderBy('order')
        ->get();
    $elementChildren = \DigitalisStudios\SlickForms\Models\SlickFormLayoutElement::where('parent_field_id', $field->id)->orderBy('order')->get();

    // Merge and sort by order
    $items = collect();
    foreach ($fieldChildren as $childField) {
        $items->push(['type' => 'field', 'order' => $childField->order, 'data' => $childField]);
    }
    foreach ($elementChildren as $element) {
        $items->push(['type' => 'element', 'order' => $element->order, 'data' => $element]);
    }
    $items = $items->sortBy('order');

    // Build tree structure for rendering
    $children = [];
    foreach ($items as $item) {
        if ($item['type'] === 'field') {
            $children[] = [
                'type' => 'field',
                'data' => $item['data'],
                'children' => [],
            ];
        } else {
            $children[] = $layoutService->buildElementNode($item['data']);
        }
    }

    $isSelected = isset($selectedFieldId) && $selectedFieldId === $field->id;
@endphp

{{-- Outer wrapper with proper data attributes --}}
<div class="field-wrapper mb-3 {{ $isSelected ? 'border-primary' : '' }}"
     data-field-id="{{ $field->id }}"
     data-type="field"
     data-field-type="repeater"
     wire:key="field-{{ $field->id }}"
     wire:click.stop="editField({{ $field->id }})"
     style="position: relative; border: 2px dashed #dee2e6; padding: 1rem; cursor: pointer; background-color: #f8f9fa; border-radius: 0.375rem;">

    {{-- Hover Controls: Drag Handle --}}
    <div class="field-controls drag-handle"
         style="position: absolute; top: -25px; left: 0; z-index: 10;">
        <div class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-grip-vertical"></i> REP
        </div>
    </div>

    {{-- Hover Controls: Delete Button --}}
    <button class="btn btn-sm btn-outline-danger field-controls"
            style="position: absolute; top: -25px; right: 0; z-index: 10;"
            wire:click.stop="deleteField({{ $field->id }})"
            title="Delete Repeater">
        <i class="bi bi-trash"></i>
    </button>

    {{-- Repeater Header --}}
    <div class="mb-3 pb-2 border-bottom">
        <div class="d-flex align-items-center">
            <i class="bi bi-collection me-2 text-primary"></i>
            <div class="flex-grow-1">
                <strong>{{ $field->label ?: $field->name }}</strong>
                <div class="small text-muted">Repeater Template (users can add/remove instances)</div>
            </div>
        </div>
    </div>

    {{-- Sortable Drop Zone for Children --}}
    <div class="sortable-container border-2 border-dashed rounded p-3"
         data-parent-field-id="{{ $field->id }}"
         style="min-height: 100px; background-color: white;"
         x-data="{
            initSortable() {
                const element = this.$el;
                if (typeof Sortable !== 'undefined') {
                    new Sortable(element, {
                        animation: 150,
                        handle: '.drag-handle',
                        group: 'shared',
                        filter: '.placeholder-text, .btn, button',
                        onStart: () => {
                            document.getElementById('form-canvas')?.classList.add('drag-active');
                        },
                        onAdd: function(evt) {
                            const itemType = evt.item.dataset.type;
                            const parentFieldId = element.dataset.parentFieldId;
                            const newIndex = evt.newIndex;

                            // Mark item as handled to prevent parent containers from also processing it
                            evt.item.dataset.handledBy = 'repeater';

                            if (itemType === 'new-field') {
                                const fieldType = evt.item.dataset.fieldType;
                                evt.item.remove();
                                @this.call('addField', fieldType, null, true, newIndex, parseInt(parentFieldId));
                                return;
                            } else if (itemType === 'field') {
                                const fieldId = parseInt(evt.item.dataset.fieldId);
                                @this.call('moveFieldToRepeater', fieldId, parseInt(parentFieldId));
                                return;
                            } else if (itemType === 'new-element') {
                                const elementType = evt.item.dataset.elementType;
                                evt.item.remove();
                                @this.call('addLayoutElementToField', elementType, parseInt(parentFieldId), {}, newIndex);
                                return;
                            } else if (itemType === 'element') {
                                const elementId = parseInt(evt.item.dataset.elementId);
                                @this.call('moveElementToRepeater', elementId, parseInt(parentFieldId));
                                return;
                            }
                        },
                        onEnd: function(evt) {
                            document.getElementById('form-canvas')?.classList.remove('drag-active');
                            if (evt.from === evt.to) {
                                const parentFieldId = element.dataset.parentFieldId;
                                const items = Array.from(element.children)
                                    .filter(el => !el.classList.contains('placeholder-text'))
                                    .map((el, index) => {
                                        if (el.dataset.fieldId) {
                                            return {id: parseInt(el.dataset.fieldId), type: 'field', order: index};
                                        } else if (el.dataset.elementId) {
                                            return {id: parseInt(el.dataset.elementId), type: 'element', order: index};
                                        }
                                    })
                                    .filter(Boolean);
                                if (items.length > 0) {
                                    @this.call('updateChildrenOrderInRepeater', parseInt(parentFieldId), items);
                                }
                            }
                        }
                    });
                }
            }
         }"
         x-init="initSortable()">

        @if(count($children) === 0)
            {{-- Placeholder when empty --}}
            <div class="text-center text-muted py-5 placeholder-text">
                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                <p class="mt-2 mb-0">Drag fields or layout elements here to build the repeater template</p>
                <small>Users will be able to add multiple instances of this template</small>
            </div>
        @else
            {{-- Render child fields and elements recursively --}}
            @foreach($children as $child)
                @include('slick-forms::livewire.partials.builder-element', [
                    'node' => $child,
                    'registry' => $registry,
                    'selectedField' => $selectedField ?? null,
                    'selectedElement' => $selectedElement ?? null,
                    'previewMode' => $previewMode ?? false,
                    'pickerMode' => $pickerMode ?? false,
                ])
            @endforeach
        @endif
    </div>
</div>
