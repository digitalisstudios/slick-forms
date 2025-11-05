{{--
    Children Tab - Shows hierarchical structure of child elements and fields
    Variables: $selectedElement (the currently selected layout element)
--}}

@php
    use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;
    use DigitalisStudios\SlickForms\Models\CustomFormField;

    /**
     * Recursively render children with indentation
     */
    function renderChildren($parent, $depth = 0, $selectedField = null, $selectedElement = null) {
        $indent = $depth * 20; // 20px per level
        $output = '';

        // Get child elements
        $childElements = SlickFormLayoutElement::where('parent_id', $parent->id)
            ->orderBy('order')
            ->get();

        // Get child fields
        $childFields = CustomFormField::where('slick_form_layout_element_id', $parent->id)
            ->orderBy('order')
            ->get();

        // Merge and sort by order
        $children = collect([])
            ->merge($childElements->map(fn($e) => ['type' => 'element', 'data' => $e, 'order' => $e->order]))
            ->merge($childFields->map(fn($f) => ['type' => 'field', 'data' => $f, 'order' => $f->order]))
            ->sortBy('order');

        foreach ($children as $child) {
            if ($child['type'] === 'element') {
                $element = $child['data'];
                $isSelected = $selectedElement && $selectedElement->id === $element->id;

                $output .= '<div class="child-item child-element ' . ($isSelected ? 'selected' : '') . '"
                                 style="padding-left: ' . $indent . 'px; cursor: pointer;"
                                 wire:click.stop="editElement(' . $element->id . ')">';
                $output .= '<div class="d-flex align-items-center py-1 px-2 rounded hover-bg">';
                $output .= '<i class="bi bi-box text-success me-2"></i>';
                $output .= '<span class="fw-medium">' . ucfirst($element->element_type) . '</span>';

                // Show element ID if set
                if ($element->element_id) {
                    $output .= '<span class="text-muted ms-2 small">#' . htmlspecialchars($element->element_id) . '</span>';
                }

                $output .= '</div>';
                $output .= '</div>';

                // Recursively render this element's children
                $output .= renderChildren($element, $depth + 1, $selectedField, $selectedElement);

            } else {
                $field = $child['data'];
                $isSelected = $selectedField && $selectedField->id === $field->id;

                $output .= '<div class="child-item child-field ' . ($isSelected ? 'selected' : '') . '"
                                 style="padding-left: ' . $indent . 'px; cursor: pointer;"
                                 wire:click.stop="editField(' . $field->id . ')">';
                $output .= '<div class="d-flex align-items-center py-1 px-2 rounded hover-bg">';
                $output .= '<i class="bi bi-input-cursor-text text-primary me-2"></i>';
                $output .= '<span>' . htmlspecialchars($field->label ?: $field->name) . '</span>';
                $output .= '<span class="text-muted ms-2 small">(' . $field->field_type . ')</span>';
                $output .= '</div>';
                $output .= '</div>';
            }
        }

        return $output;
    }
@endphp

@if($selectedElement)
    <div class="children-tree">
        @if($selectedElement->element_type === 'table')
            {{-- Special handling for nested table structure --}}
            @php
                // Get table sections (header, body, footer)
                $sections = SlickFormLayoutElement::where('parent_id', $selectedElement->id)
                    ->whereIn('element_type', ['table_header', 'table_body', 'table_footer'])
                    ->orderBy('order')
                    ->get();

                $hasSections = $sections->count() > 0;
            @endphp

            @if($hasSections)
                <div class="mb-2">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Table structure with sections, rows, and cells
                    </small>
                </div>

                @foreach($sections as $section)
                    @php
                        $sectionLabel = match($section->element_type) {
                            'table_header' => 'Header',
                            'table_body' => 'Body',
                            'table_footer' => 'Footer',
                            default => ucfirst($section->element_type)
                        };
                        $sectionColor = match($section->element_type) {
                            'table_header' => 'primary',
                            'table_body' => 'success',
                            'table_footer' => 'secondary',
                            default => 'dark'
                        };
                    @endphp

                    {{-- Section --}}
                    <div class="child-item child-section" style="cursor: pointer;" wire:click.stop="editElement({{ $section->id }})">
                        <div class="d-flex align-items-center py-1 px-2 rounded hover-bg">
                            <i class="bi bi-layer-forward text-{{ $sectionColor }} me-2"></i>
                            <span class="fw-medium">{{ $sectionLabel }}</span>
                        </div>
                    </div>

                    {{-- Rows in section --}}
                    @php
                        $rows = SlickFormLayoutElement::where('parent_id', $section->id)
                            ->where('element_type', 'table_row')
                            ->orderBy('order')
                            ->get();
                    @endphp

                    @foreach($rows as $rowIndex => $row)
                        <div class="child-item child-row" style="padding-left: 20px; cursor: pointer;" wire:click.stop="editElement({{ $row->id }})">
                            <div class="d-flex align-items-center py-1 px-2 rounded hover-bg">
                                <i class="bi bi-list text-warning me-2"></i>
                                <span class="fw-medium">Row {{ $rowIndex + 1 }}</span>
                            </div>
                        </div>

                        {{-- Cells in row --}}
                        @php
                            $cells = SlickFormLayoutElement::where('parent_id', $row->id)
                                ->where('element_type', 'table_cell')
                                ->orderBy('order')
                                ->get();
                        @endphp

                        @foreach($cells as $cellIndex => $cell)
                            @php
                                $isSelected = $selectedElement && $selectedElement->id === $cell->id;
                                $cellType = $cell->settings['cell_type'] ?? 'td';
                            @endphp
                            <div class="child-item child-cell {{ $isSelected ? 'selected' : '' }}"
                                 style="padding-left: 40px; cursor: pointer;"
                                 wire:click.stop="editElement({{ $cell->id }})">
                                <div class="d-flex align-items-center py-1 px-2 rounded hover-bg">
                                    <i class="bi bi-square text-info me-2"></i>
                                    <span>Cell {{ $cellIndex + 1 }}</span>
                                    <span class="badge bg-light text-dark ms-2">{{ strtoupper($cellType) }}</span>
                                </div>
                            </div>

                            {{-- Show fields and elements inside cell --}}
                            <div style="padding-left: 60px;">
                                {!! renderChildren($cell, 3, $selectedField ?? null, $selectedElement) !!}
                            </div>
                        @endforeach
                    @endforeach
                @endforeach
            @else
                <div class="text-center text-muted py-4">
                    <i class="bi bi-table" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">Empty table</p>
                    <small>Table structure will appear here</small>
                </div>
            @endif
        @else
            {{-- Standard hierarchical structure for other elements --}}
            @php
                // Get direct children
                $childElements = SlickFormLayoutElement::where('parent_id', $selectedElement->id)
                    ->orderBy('order')
                    ->get();

                $childFields = CustomFormField::where('slick_form_layout_element_id', $selectedElement->id)
                    ->orderBy('order')
                    ->get();

                $hasChildren = $childElements->count() > 0 || $childFields->count() > 0;
            @endphp

            @if($hasChildren)
                <div class="mb-2">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Click any item to edit its properties
                    </small>
                </div>

                {!! renderChildren($selectedElement, 0, $selectedField ?? null, $selectedElement) !!}
            @else
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">No children</p>
                    <small>Drag fields or elements into this container</small>
                </div>
            @endif
        @endif
    </div>

    <style>
        .children-tree {
            font-size: 0.9rem;
        }

        .child-item .hover-bg {
            transition: background-color 0.15s;
        }

        .child-item .hover-bg:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .child-item.selected .hover-bg {
            background-color: rgba(13, 110, 253, 0.1);
            border-left: 3px solid #0d6efd;
        }

        .child-element i.bi-box {
            font-size: 0.9rem;
        }

        .child-field i.bi-input-cursor-text {
            font-size: 0.85rem;
        }
    </style>
@else
    <div class="text-center text-muted py-4">
        <i class="bi bi-info-circle" style="font-size: 2rem;"></i>
        <p class="mt-2 mb-0">Select an element to view its children</p>
    </div>
@endif
