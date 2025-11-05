{{--
    Repeater Children Tab (hierarchical)
    Mirrors the Container Children tab, but starts from a repeater field.
--}}

@php
    use DigitalisStudios\SlickForms\Models\CustomFormField;
    use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;

    /**
     * Render tree for a layout element (recursively)
     */
    function renderElementTree($element, $depth = 0, $selectedField = null, $selectedElement = null) {
        $indent = $depth * 20;
        $html = '';

        $isSelected = $selectedElement && $selectedElement->id === $element->id;
        $label = ucfirst(str_replace('_', ' ', $element->element_type));
        if ($element->element_type === 'column') {
            $label .= ' ('.$element->getColumnWidthLabel().')';
        }

        $html .= '<div class="child-item child-element '.($isSelected ? 'selected' : '').'" '
               . 'style="padding-left: '.$indent.'px; cursor: pointer;" '
               . 'wire:click.stop="editElement('.$element->id.')">';
        $html .= '<div class="d-flex align-items-center py-1 px-2 rounded hover-bg">';
        $html .= '<i class="bi bi-box text-success me-2"></i>';
        $html .= '<span class="fw-medium">'.$label.'</span>';
        $html .= '</div></div>';

        // Build mixed children: element->children (by parent_id) + element->fields
        $children = collect([])
            ->merge(SlickFormLayoutElement::where('parent_id', $element->id)->orderBy('order')->get()->map(fn($e) => ['type' => 'element', 'order' => $e->order, 'data' => $e]))
            ->merge(CustomFormField::where('slick_form_layout_element_id', $element->id)->orderBy('order')->get()->map(fn($f) => ['type' => 'field', 'order' => $f->order, 'data' => $f]))
            ->sortBy('order');

        foreach ($children as $child) {
            if ($child['type'] === 'element') {
                $html .= renderElementTree($child['data'], $depth + 1, $selectedField, $selectedElement);
            } else {
                $f = $child['data'];
                $sel = $selectedField && $selectedField->id === $f->id;
                $html .= '<div class="child-item child-field '.($sel ? 'selected' : '').'" '
                    .'style="padding-left: '.(($depth + 1) * 20).'px; cursor: pointer;" '
                    .'wire:click.stop="editField('.$f->id.')">';
                $html .= '<div class="d-flex align-items-center py-1 px-2 rounded hover-bg">';
                $html .= '<i class="bi bi-input-cursor-text text-primary me-2"></i>';
                $html .= '<span>'.htmlspecialchars($f->label ?: $f->name).'</span>';
                $html .= '<span class="text-muted ms-2 small">('.$f->field_type.')</span>';
                $html .= '</div></div>';
            }
        }

        return $html;
    }

    /**
     * Render tree from a repeater field (mixed elements+fields at root)
     */
    function renderRepeaterTree($repeater, $selectedField = null, $selectedElement = null) {
        $items = collect([])
            ->merge(SlickFormLayoutElement::where('parent_field_id', $repeater->id)->orderBy('order')->get()->map(fn($e) => ['type' => 'element', 'order' => $e->order, 'data' => $e]))
            ->merge(CustomFormField::where('parent_field_id', $repeater->id)->orderBy('order')->get()->map(fn($f) => ['type' => 'field', 'order' => $f->order, 'data' => $f]))
            ->sortBy('order');

        $html = '';
        foreach ($items as $item) {
            if ($item['type'] === 'element') {
                $html .= renderElementTree($item['data'], 0, $selectedField, $selectedElement);
            } else {
                $f = $item['data'];
                $sel = $selectedField && $selectedField->id === $f->id;
                $html .= '<div class="child-item child-field '.($sel ? 'selected' : '').'" '
                    .'style="padding-left: 0px; cursor: pointer;" '
                    .'wire:click.stop="editField('.$f->id.')">';
                $html .= '<div class="d-flex align-items-center py-1 px-2 rounded hover-bg">';
                $html .= '<i class="bi bi-input-cursor-text text-primary me-2"></i>';
                $html .= '<span>'.htmlspecialchars($f->label ?: $f->name).'</span>';
                $html .= '<span class="text-muted ms-2 small">('.$f->field_type.')</span>';
                $html .= '</div></div>';
            }
        }

        if ($html === '') {
            $html = '<div class="text-center text-muted py-4">'
                  . '<i class="bi bi-inbox" style="font-size: 2rem;"></i>'
                  . '<p class="mt-2 mb-0">No children</p>'
                  . '<small>Drag fields or elements into this repeater in the canvas</small>'
                  . '</div>';
        }

        return $html;
    }

    $repeater = $selectedField ?? null;
@endphp

@if(!$repeater)
    <div class="text-center text-muted py-4">
        <i class="bi bi-info-circle" style="font-size: 2rem;"></i>
        <p class="mt-2 mb-0">Select a repeater field to view its children</p>
    </div>
@else
    <div class="mb-2">
        <small class="text-muted">
            <i class="bi bi-info-circle me-1"></i>
            Click any item to edit its properties
        </small>
    </div>

    <div class="children-tree">
        {!! renderRepeaterTree($repeater, $selectedField ?? null, $selectedElement ?? null) !!}
    </div>

    <style>
        .children-tree { font-size: 0.9rem; }
        .child-item .hover-bg { transition: background-color 0.15s; }
        .child-item .hover-bg:hover { background-color: rgba(0,0,0,0.05); }
        .child-item.selected .hover-bg { background-color: rgba(13,110,253,0.1); border-left: 3px solid #0d6efd; }
        .child-element i.bi-box { font-size: 0.9rem; }
        .child-field i.bi-input-cursor-text { font-size: 0.85rem; }
    </style>
@endif
