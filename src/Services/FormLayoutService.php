<?php

namespace DigitalisStudios\SlickForms\Services;

use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\CustomFormField;
use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;

class FormLayoutService
{
    /**
     * Initialize a new form with default layout structure:
     * Container → Row → Column → Single Text Field
     */
    public function initializeDefaultLayout(CustomForm $form): void
    {
        // Create container
        $container = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'parent_id' => null,
            'element_type' => 'container',
            'order' => 0,
            'settings' => [],
        ]);

        // Create row inside container
        $row = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'parent_id' => $container->id,
            'element_type' => 'row',
            'order' => 0,
            'settings' => [],
        ]);

        // Create column inside row
        $column = SlickFormLayoutElement::create([
            'slick_form_id' => $form->id,
            'parent_id' => $row->id,
            'element_type' => 'column',
            'order' => 0,
            'settings' => [
                'width' => [
                    'xs' => '12',
                ],
            ],
        ]);

        // Create default text field inside column
        CustomFormField::create([
            'slick_form_id' => $form->id,
            'slick_form_layout_element_id' => $column->id,
            'field_type' => 'text',
            'label' => 'Text Input',
            'order' => 0,
            'is_required' => false,
        ]);
    }

    /**
     * Get the form structure as a nested tree
     * Added $pageId parameter to filter by page in multi-page forms
     */
    public function getFormStructure(CustomForm $form, ?int $pageId = null): array
    {
        // Get top-level elements (no parent element or parent field)
        $elementsQuery = SlickFormLayoutElement::where('slick_form_id', $form->id)
            ->whereNull('parent_id')
            ->whereNull('parent_field_id')
            ->orderBy('order');

        if ($pageId !== null) {
            $elementsQuery->where('slick_form_page_id', $pageId);
        } else {
            $elementsQuery->whereNull('slick_form_page_id');
        }

        $topLevelElements = $elementsQuery->get();

        // Get standalone fields (not in any layout element or repeater)
        $fieldsQuery = CustomFormField::where('slick_form_id', $form->id)
            ->whereNull('slick_form_layout_element_id')
            ->whereNull('parent_field_id')
            ->orderBy('order');

        if ($pageId !== null) {
            $fieldsQuery->where('slick_form_page_id', $pageId);
        } else {
            $fieldsQuery->whereNull('slick_form_page_id');
        }

        $standaloneFields = $fieldsQuery->get();

        // Merge elements and fields together, sorted by order
        $items = [];

        foreach ($topLevelElements as $element) {
            $items[] = [
                'order' => $element->order,
                'type' => 'element',
                'data' => $element,
            ];
        }

        foreach ($standaloneFields as $field) {
            $items[] = [
                'order' => $field->order,
                'type' => 'field',
                'data' => $field,
            ];
        }

        // Sort by order
        usort($items, fn ($a, $b) => $a['order'] <=> $b['order']);

        // Build the structure
        $structure = [];
        foreach ($items as $item) {
            if ($item['type'] === 'element') {
                $structure[] = $this->buildElementTree($item['data']);
            } else {
                $structure[] = [
                    'type' => 'field',
                    'data' => $item['data'],
                    'children' => [],
                ];
            }
        }

        return $structure;
    }

    /**
     * Recursively build the element tree
     */
    protected function buildElementTree(SlickFormLayoutElement $element): array
    {
        // Merge child elements and fields together, sorted by order
        $items = [];

        // Add child layout elements
        foreach ($element->children as $childElement) {
            $items[] = [
                'order' => $childElement->order,
                'type' => 'element',
                'data' => $childElement,
            ];
        }

        // Add fields directly in this element
        foreach ($element->fields as $field) {
            $items[] = [
                'order' => $field->order,
                'type' => 'field',
                'data' => $field,
            ];
        }

        // Sort by order
        usort($items, fn ($a, $b) => $a['order'] <=> $b['order']);

        // Build children array
        $children = [];
        foreach ($items as $item) {
            if ($item['type'] === 'element') {
                $children[] = $this->buildElementTree($item['data']);
            } else {
                $children[] = [
                    'type' => 'field',
                    'data' => $item['data'],
                    'children' => [],
                ];
            }
        }

        return [
            'type' => 'element',
            'element_type' => $element->element_type,
            'data' => $element,
            'children' => $children,
        ];
    }

    /**
     * Public wrapper to build a single element node for external callers (e.g., views).
     */
    public function buildElementNode(SlickFormLayoutElement $element): array
    {
        return $this->buildElementTree($element);
    }

    /**
     * Create default table structure: header (1 row x 3 cells) + body (3 rows x 3 cells)
     */
    public function createDefaultTableStructure(SlickFormLayoutElement $table): void
    {
        // Create header section
        $header = SlickFormLayoutElement::create([
            'slick_form_id' => $table->slick_form_id,
            'parent_id' => $table->id,
            'element_type' => 'table_header',
            'order' => 0,
            'settings' => [],
        ]);

        // Create header row with 3 cells
        $this->createTableRow($header, 0, 3, 'th');

        // Create body section
        $body = SlickFormLayoutElement::create([
            'slick_form_id' => $table->slick_form_id,
            'parent_id' => $table->id,
            'element_type' => 'table_body',
            'order' => 1,
            'settings' => [],
        ]);

        // Create 3 body rows with 3 cells each
        for ($i = 0; $i < 3; $i++) {
            $this->createTableRow($body, $i, 3, 'td');
        }
    }

    /**
     * Create a table row with specified number of cells
     */
    public function createTableRow(SlickFormLayoutElement $section, int $order, int $cellCount = 3, string $cellType = 'td'): SlickFormLayoutElement
    {
        $row = SlickFormLayoutElement::create([
            'slick_form_id' => $section->slick_form_id,
            'parent_id' => $section->id,
            'element_type' => 'table_row',
            'order' => $order,
            'settings' => [],
        ]);

        // Create cells
        for ($i = 0; $i < $cellCount; $i++) {
            SlickFormLayoutElement::create([
                'slick_form_id' => $section->slick_form_id,
                'parent_id' => $row->id,
                'element_type' => 'table_cell',
                'order' => $i,
                'settings' => [
                    'cell_type' => $cellType,
                    'colspan' => 1,
                    'rowspan' => 1,
                ],
            ]);
        }

        return $row;
    }

    /**
     * Get column count for a table (based on first row in first section)
     */
    public function getTableColumnCount(SlickFormLayoutElement $table): int
    {
        $section = SlickFormLayoutElement::where('parent_id', $table->id)
            ->whereIn('element_type', ['table_header', 'table_body', 'table_footer'])
            ->orderBy('order')
            ->first();

        if (! $section) {
            return 0;
        }

        $row = SlickFormLayoutElement::where('parent_id', $section->id)
            ->where('element_type', 'table_row')
            ->orderBy('order')
            ->first();

        if (! $row) {
            return 0;
        }

        return SlickFormLayoutElement::where('parent_id', $row->id)
            ->where('element_type', 'table_cell')
            ->count();
    }

    /**
     * Add a column to all rows in all sections of a table
     */
    public function addColumnToTable(SlickFormLayoutElement $table): void
    {
        $sections = SlickFormLayoutElement::where('parent_id', $table->id)
            ->whereIn('element_type', ['table_header', 'table_body', 'table_footer'])
            ->get();

        foreach ($sections as $section) {
            $rows = SlickFormLayoutElement::where('parent_id', $section->id)
                ->where('element_type', 'table_row')
                ->get();

            foreach ($rows as $row) {
                $maxOrder = SlickFormLayoutElement::where('parent_id', $row->id)
                    ->where('element_type', 'table_cell')
                    ->max('order') ?? -1;

                $cellType = $section->element_type === 'table_header' ? 'th' : 'td';

                SlickFormLayoutElement::create([
                    'slick_form_id' => $table->slick_form_id,
                    'parent_id' => $row->id,
                    'element_type' => 'table_cell',
                    'order' => $maxOrder + 1,
                    'settings' => [
                        'cell_type' => $cellType,
                        'colspan' => 1,
                        'rowspan' => 1,
                    ],
                ]);
            }
        }
    }

    /**
     * Delete a column from all rows in all sections of a table
     */
    public function deleteColumnFromTable(SlickFormLayoutElement $table, int $columnIndex): void
    {
        $sections = SlickFormLayoutElement::where('parent_id', $table->id)
            ->whereIn('element_type', ['table_header', 'table_body', 'table_footer'])
            ->get();

        foreach ($sections as $section) {
            $rows = SlickFormLayoutElement::where('parent_id', $section->id)
                ->where('element_type', 'table_row')
                ->get();

            foreach ($rows as $row) {
                $cells = SlickFormLayoutElement::where('parent_id', $row->id)
                    ->where('element_type', 'table_cell')
                    ->orderBy('order')
                    ->get();

                if (isset($cells[$columnIndex])) {
                    $cells[$columnIndex]->delete();

                    // Reorder remaining cells
                    $cells = SlickFormLayoutElement::where('parent_id', $row->id)
                        ->where('element_type', 'table_cell')
                        ->orderBy('order')
                        ->get();

                    foreach ($cells as $index => $cell) {
                        $cell->order = $index;
                        $cell->save();
                    }
                }
            }
        }
    }

    /**
     * Validate that an element can be added to a parent
     */
    public function canAddChild(?SlickFormLayoutElement $parent, string $childType): bool
    {
        // Columns must have a row parent
        if ($childType === 'column' && (! $parent || $parent->element_type !== 'row')) {
            return false;
        }

        // Tabs must have a tabs parent
        if ($childType === 'tab' && (! $parent || $parent->element_type !== 'tabs')) {
            return false;
        }

        // Accordion items must have an accordion parent
        if ($childType === 'accordion_item' && (! $parent || $parent->element_type !== 'accordion')) {
            return false;
        }

        // Table sections (header, body, footer) must have a table parent
        if (in_array($childType, ['table_header', 'table_body', 'table_footer']) && (! $parent || $parent->element_type !== 'table')) {
            return false;
        }

        // Table rows must have a table section parent
        if ($childType === 'table_row' && (! $parent || ! in_array($parent->element_type, ['table_header', 'table_body', 'table_footer']))) {
            return false;
        }

        // Table cells must have a table row parent
        if ($childType === 'table_cell' && (! $parent || $parent->element_type !== 'table_row')) {
            return false;
        }

        // Check if parent can accept this child type
        if ($parent) {
            return $parent->canBeParentOf($childType);
        }

        // If no parent, these elements can be standalone
        return in_array($childType, ['container', 'row', 'field', 'card', 'accordion', 'tabs', 'carousel', 'table']);
    }

    /**
     * Validate that an element can be added under a repeater (parent_field_id).
     * We treat a repeater as a container-like surface that can contain the same
     * types that a column/container could contain (except another container).
     */
    public function canAddChildToField(string $childType): bool
    {
        return in_array($childType, ['row', 'field', 'card', 'accordion', 'tabs', 'carousel', 'table']);
    }
}
