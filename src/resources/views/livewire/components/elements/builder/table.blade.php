{{--
    Table Component - Builder View (Nested Element Structure)
    Shows a TinyMCE-style inline table editor with drag-and-drop support
--}}
@php
    $settings = $element->settings ?? [];
    $label = $settings['label'] ?? 'Table';
    $striped = $settings['striped'] ?? false;
    $bordered = $settings['bordered'] ?? true;
    $borderless = $settings['borderless'] ?? false;
    $hover = $settings['hover'] ?? false;
    $size = $settings['size'] ?? '';

    // Build table class string
    $tableClasses = ['table'];
    if ($striped) $tableClasses[] = 'table-striped';
    if ($borderless) $tableClasses[] = 'table-borderless';
    elseif ($bordered) $tableClasses[] = 'table-bordered';
    if ($hover) $tableClasses[] = 'table-hover';
    if ($size) $tableClasses[] = $size;
    $tableClassString = implode(' ', $tableClasses);

    // Get column count from FormLayoutService
    $layoutService = app(\DigitalisStudios\SlickForms\Services\FormLayoutService::class);
    $columnCount = $layoutService->getTableColumnCount($element);

    // Get body section for Add Row button
    $bodySection = \DigitalisStudios\SlickForms\Models\SlickFormLayoutElement::where('parent_id', $element->id)
        ->where('element_type', 'table_body')
        ->first();
@endphp

<div class="table-builder-wrapper"
     wire:click.stop="editElement({{ $element->id }})"
     style="cursor: pointer;">
    {{-- Table Header with Controls --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <i class="bi bi-table me-2"></i>
            <strong>{{ $label }}</strong>
            @if($columnCount > 0)
                <span class="badge bg-secondary ms-2">{{ $columnCount }} columns</span>
            @endif
        </div>
        <div class="btn-group btn-group-sm" role="group">
            @if($bodySection)
                <button
                    type="button"
                    class="btn btn-outline-success"
                    wire:click.stop="addRowToSection({{ $bodySection->id }})"
                    title="Add Row to Body">
                    <i class="bi bi-plus-circle"></i> Row
                </button>
            @endif
            <button
                type="button"
                class="btn btn-outline-success"
                wire:click.stop="addColumnToTable({{ $element->id }})"
                title="Add Column">
                <i class="bi bi-plus-circle"></i> Column
            </button>
            <button
                type="button"
                class="btn btn-outline-primary"
                wire:click.stop="editElement({{ $element->id }})"
                title="Edit Table">
                <i class="bi bi-gear"></i>
            </button>
        </div>
    </div>

    {{-- Actual Table Structure --}}
    <div class="table-responsive bg-white border rounded" style="padding-top: 2rem;">
        <table class="{{ $tableClassString }}" style="margin-bottom: 0; table-layout: auto; width: 100%;">
            <colgroup>
                <col style="width: 40px;"> {{-- Row controls column --}}
                @for($i = 0; $i < $columnCount; $i++)
                    <col style="width: auto; min-width: 150px;">
                @endfor
            </colgroup>
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
        </table>

        {{-- Help Text --}}
        @if($columnCount === 0)
            <div class="alert alert-info small m-3">
                <i class="bi bi-info-circle me-1"></i>
                This table has no structure yet. Click "Column" to add columns, then add rows to sections.
            </div>
        @endif
    </div>
</div>
