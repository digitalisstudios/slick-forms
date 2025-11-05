{{-- Repeater Field Renderer (User-facing form) --}}
@php
    $repeaterId = $field->id;
    // Default to new compact list layout
    $layoutStyle = $field->options['layout_style'] ?? 'list';
    $showInstanceNumber = $field->options['show_instance_number'] ?? true;
    $addButtonText = $field->options['add_button_text'] ?? 'Add Another';
    $removeButtonText = $field->options['remove_button_text'] ?? 'Remove';
    $allowReorder = $field->options['allow_reorder'] ?? true;
    $minInstances = $field->options['min_instances'] ?? 1;
    $maxInstances = $field->options['max_instances'] ?? 10;

    $registry = app(\DigitalisStudios\SlickForms\Services\FieldTypeRegistry::class);
    $layoutService = app(\DigitalisStudios\SlickForms\Services\FormLayoutService::class);

    // Load children (both fields and elements) for this repeater
    // Only load direct field children (not nested in layout elements)
    $fieldChildren = $field->children()
        ->whereNull('slick_form_layout_element_id')
        ->orderBy('order')
        ->get();
    $elementChildren = \DigitalisStudios\SlickForms\Models\SlickFormLayoutElement::where('parent_field_id', $field->id)->orderBy('order')->get();

    // Merge and sort by order to build structure
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
@endphp

<div class="mb-4" x-data="{
    repeaterId: {{ $repeaterId }},
    minInstances: {{ $minInstances }},
    maxInstances: {{ $maxInstances }},
    get instanceCount() {
        return @this.get('formData.field_{{ $repeaterId }}')?.length || 0;
    }
}">
    {{-- Repeater Label --}}
    @if($field->show_label)
        <label class="form-label fw-bold">
            {{ $field->label }}
            @if($field->is_required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    {{-- Help Text --}}
    @if($field->help_text)
        <div class="form-text mb-3">{{ $field->help_text }}</div>
    @endif

    {{-- Instances Container --}}
    <div class="repeater-instances {{ $layoutStyle === 'list' ? 'list-group' : '' }}"
         @if($allowReorder)
         x-init="
             const sortable = new Sortable($el, {
                 animation: 150,
                 handle: '.repeater-drag-handle',
                 onEnd: function(evt) {
                     const newOrder = Array.from($el.children).map((el, idx) => parseInt(el.dataset.instance));
                     @this.call('reorderInstances', {{ $repeaterId }}, newOrder);
                 }
             });
         "
         @endif
    >
        @foreach($formData['field_' . $repeaterId] ?? [] as $instanceIndex => $instanceData)
            <div class="repeater-instance {{ $layoutStyle === 'list' ? 'list-group-item' : 'mb-3' }}" data-instance="{{ $instanceIndex }}" wire:key="repeater-{{ $repeaterId }}-instance-{{ $instanceIndex }}">
                @if($layoutStyle === 'list')
                    {{-- List Item Style: handle | fields | trash --}}
                    <div class="d-flex align-items-start gap-3">
                        @if($allowReorder)
                            <i class="bi bi-grip-vertical repeater-drag-handle mt-2" title="Drag to reorder" style="cursor: move;"></i>
                        @endif

                        <div class="flex-grow-1 w-100">
                            @foreach($children as $child)
                                @include('slick-forms::livewire.components.elements.render-element-in-repeater', [
                                    'node' => $child,
                                    'registry' => $registry,
                                    'formData' => $formData,
                                    'repeaterId' => $repeaterId,
                                    'instanceIndex' => $instanceIndex,
                                ])
                            @endforeach
                        </div>

                        <div class="ms-2 d-flex align-items-start">
                            <button
                                type="button"
                                class="btn btn-outline-danger btn-sm"
                                wire:click.prevent="removeInstance({{ $repeaterId }}, {{ $instanceIndex }})"
                                x-bind:disabled="instanceCount <= minInstances"
                                title="Remove item"
                            >
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                @elseif($layoutStyle === 'card')
                    {{-- Card Style --}}
                    <div class="card">
                        <div class="card-header d-flex align-items-center bg-light">
                            @if($allowReorder)
                                <i class="bi bi-grip-vertical repeater-drag-handle me-2" style="cursor: move;"></i>
                            @endif

                            @if($showInstanceNumber)
                                <strong>{{ $field->label }} #{{ $instanceIndex + 1 }}</strong>
                            @else
                                <strong>{{ $field->label }}</strong>
                            @endif

                            <button
                                type="button"
                                class="btn btn-sm btn-danger ms-auto"
                                wire:click.prevent="removeInstance({{ $repeaterId }}, {{ $instanceIndex }})"
                                x-bind:disabled="instanceCount <= minInstances"
                            >
                                <i class="bi bi-trash me-1"></i>
                                {{ $removeButtonText }}
                            </button>
                        </div>
                        <div class="card-body">
                            @foreach($children as $child)
                                @include('slick-forms::livewire.components.elements.render-element-in-repeater', [
                                    'node' => $child,
                                    'registry' => $registry,
                                    'formData' => $formData,
                                    'repeaterId' => $repeaterId,
                                    'instanceIndex' => $instanceIndex,
                                ])
                            @endforeach
                        </div>
                    </div>

                @elseif($layoutStyle === 'accordion')
                    {{-- Accordion Style --}}
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-{{ $repeaterId }}-{{ $instanceIndex }}">
                            <button
                                class="accordion-button"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapse-{{ $repeaterId }}-{{ $instanceIndex }}"
                                aria-expanded="{{ $instanceIndex === 0 ? 'true' : 'false' }}"
                            >
                                @if($allowReorder)
                                    <i class="bi bi-grip-vertical repeater-drag-handle me-2" style="cursor: move;"></i>
                                @endif

                                @if($showInstanceNumber)
                                    {{ $field->label }} #{{ $instanceIndex + 1 }}
                                @else
                                    {{ $field->label }}
                                @endif
                            </button>
                        </h2>
                        <div
                            id="collapse-{{ $repeaterId }}-{{ $instanceIndex }}"
                            class="accordion-collapse collapse {{ $instanceIndex === 0 ? 'show' : '' }}"
                            aria-labelledby="heading-{{ $repeaterId }}-{{ $instanceIndex }}"
                        >
                            <div class="accordion-body">
                                @foreach($children as $child)
                                    @include('slick-forms::livewire.components.elements.render-element-in-repeater', [
                                        'node' => $child,
                                        'registry' => $registry,
                                        'formData' => $formData,
                                        'repeaterId' => $repeaterId,
                                        'instanceIndex' => $instanceIndex,
                                    ])
                                @endforeach

                                <button
                                    type="button"
                                    class="btn btn-sm btn-danger mt-3"
                                    wire:click.prevent="removeInstance({{ $repeaterId }}, {{ $instanceIndex }})"
                                    x-bind:disabled="instanceCount <= minInstances"
                                >
                                    <i class="bi bi-trash me-1"></i>
                                    {{ $removeButtonText }}
                                </button>
                            </div>
                        </div>
                    </div>

                @else
                    {{-- Plain Style --}}
                    <div class="border rounded p-3 bg-light">
                        <div class="d-flex align-items-center mb-3">
                            @if($allowReorder)
                                <i class="bi bi-grip-vertical repeater-drag-handle me-2" style="cursor: move;"></i>
                            @endif

                            @if($showInstanceNumber)
                                <strong>{{ $field->label }} #{{ $instanceIndex + 1 }}</strong>
                            @else
                                <strong>{{ $field->label }}</strong>
                            @endif

                            <button
                                type="button"
                                class="btn btn-sm btn-danger ms-auto"
                                wire:click.prevent="removeInstance({{ $repeaterId }}, {{ $instanceIndex }})"
                                x-bind:disabled="instanceCount <= minInstances"
                            >
                                <i class="bi bi-trash me-1"></i>
                                {{ $removeButtonText }}
                            </button>
                        </div>

                        @foreach($children as $child)
                            @include('slick-forms::livewire.components.elements.render-element-in-repeater', [
                                'node' => $child,
                                'registry' => $registry,
                                'formData' => $formData,
                                'repeaterId' => $repeaterId,
                                'instanceIndex' => $instanceIndex,
                            ])
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Add Button --}}
    <a
        href="#"
        class="btn btn-primary"
        wire:click.prevent="addInstance({{ $repeaterId }})"
        x-bind:class="{ 'disabled': instanceCount >= maxInstances }"
        wire:loading.attr="disabled"
        wire:loading.class="opacity-50"
        role="button"
    >
        <span wire:loading.remove wire:target="addInstance({{ $repeaterId }})">
            <i class="bi bi-plus-circle me-1"></i>
            {{ $addButtonText }}
        </span>
        <span wire:loading wire:target="addInstance({{ $repeaterId }})">
            <span class="spinner-border spinner-border-sm me-1"></span>
            Adding...
        </span>
    </a>

    {{-- Instance Count Info --}}
    <div class="form-text mt-2">
        <span x-text="instanceCount"></span> of {{ $maxInstances }} instances
        @if($minInstances > 0)
            (minimum: {{ $minInstances }})
        @endif
    </div>

    {{-- Validation Errors --}}
    @error('formData.field_' . $repeaterId)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
