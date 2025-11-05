<?php

namespace DigitalisStudios\SlickForms\Services;

use DigitalisStudios\SlickForms\Models\CustomForm;
use Illuminate\Support\Collection;

class FormTemplateService
{
    /**
     * Get all available templates grouped by category
     */
    public function getTemplates(): Collection
    {
        return CustomForm::where('is_template', true)
            ->orderBy('template_category')
            ->orderBy('name')
            ->get()
            ->groupBy('template_category');
    }

    /**
     * Get templates by category
     */
    public function getTemplatesByCategory(string $category): Collection
    {
        return CustomForm::where('is_template', true)
            ->where('template_category', $category)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get all template categories
     */
    public function getCategories(): array
    {
        return [
            'contact' => 'Contact Forms',
            'survey' => 'Surveys & Feedback',
            'registration' => 'Registration Forms',
            'order' => 'Order & Booking Forms',
            'application' => 'Application Forms',
            'quiz' => 'Quizzes & Tests',
            'lead' => 'Lead Generation',
            'other' => 'Other',
        ];
    }

    /**
     * Save a form as a template
     */
    public function saveAsTemplate(
        CustomForm $form,
        string $templateName,
        string $category,
        ?string $description = null,
        ?string $previewImage = null
    ): CustomForm {
        // Create a copy of the form as a template
        $template = $form->replicate();
        $template->name = $templateName;
        $template->is_template = true;
        $template->is_active = false; // Templates are not active forms
        $template->template_category = $category;
        $template->template_description = $description ?? $form->description;
        $template->preview_image = $previewImage;
        $template->save();

        // Create page ID mapping if multi-page form
        $pageIdMap = [];
        if ($form->pages()->exists()) {
            foreach ($form->pages as $page) {
                $oldPageId = $page->id;
                $newPage = $page->replicate();
                $newPage->slick_form_id = $template->id;
                $newPage->save();
                $pageIdMap[$oldPageId] = $newPage->id;
            }
        }

        // Copy all layout elements recursively with page ID mapping
        $layoutElementIdMap = [];
        $this->copyLayoutElements($form->layoutElements, $template->id, null, [], $layoutElementIdMap, $pageIdMap);

        // Copy all fields with updated page and layout references
        foreach ($form->fields as $field) {
            $newField = $field->replicate();
            $newField->slick_form_id = $template->id;

            // Update page reference if it exists
            if ($field->slick_form_page_id && isset($pageIdMap[$field->slick_form_page_id])) {
                $newField->slick_form_page_id = $pageIdMap[$field->slick_form_page_id];
            }

            // Update layout element reference if it exists
            if ($field->slick_form_layout_element_id && isset($layoutElementIdMap[$field->slick_form_layout_element_id])) {
                $newField->slick_form_layout_element_id = $layoutElementIdMap[$field->slick_form_layout_element_id];
            }

            $newField->save();
        }

        return $template;
    }

    /**
     * Create a new form from a template
     */
    public function createFromTemplate(CustomForm $template, string $formName): CustomForm
    {
        if (! $template->is_template) {
            throw new \InvalidArgumentException('The provided form is not a template.');
        }

        // Create new form from template
        $form = $template->replicate();
        $form->name = $formName;
        $form->is_template = false;
        $form->is_active = true;
        $form->template_category = null;
        $form->template_description = null;
        $form->preview_image = null;
        $form->save();

        // Create mappings
        $fieldIdMap = [];
        $layoutElementIdMap = [];
        $pageIdMap = [];

        // Copy pages FIRST if multi-page form, so we have page ID mappings
        if ($template->pages()->exists()) {
            foreach ($template->pages as $page) {
                $oldPageId = $page->id;
                $newPage = $page->replicate();
                $newPage->slick_form_id = $form->id;
                $newPage->save();
                $pageIdMap[$oldPageId] = $newPage->id;
            }
        }

        // Copy top-level layout elements (those without parent_field_id or parent_id)
        $topLevelElements = \DigitalisStudios\SlickForms\Models\SlickFormLayoutElement::where('slick_form_id', $template->id)
            ->whereNull('parent_id')
            ->whereNull('parent_field_id')
            ->orderBy('order')
            ->get();
        $this->copyLayoutElements($topLevelElements, $form->id, null, [], $layoutElementIdMap, $pageIdMap);

        // 1) Copy all fields and update their layout element and page references
        $oldParentByNewId = [];
        $oldLayoutElementByNewFieldId = []; // Track which old layout element each field belonged to
        foreach ($template->fields as $field) {
            $oldFieldId = $field->id;
            $oldParentId = $field->parent_field_id;
            $oldLayoutElementId = $field->slick_form_layout_element_id;
            $newField = $field->replicate();
            $newField->slick_form_id = $form->id;

            // Update layout element reference if it exists AND is already in the map
            // (top-level layout elements were copied in previous step)
            if ($oldLayoutElementId && isset($layoutElementIdMap[$oldLayoutElementId])) {
                $newField->slick_form_layout_element_id = $layoutElementIdMap[$oldLayoutElementId];
            } elseif ($oldLayoutElementId) {
                // Remember this for later (after repeater elements are copied)
                $oldLayoutElementByNewFieldId[$oldFieldId] = $oldLayoutElementId;
            }

            // Update page reference if it exists
            if ($field->slick_form_page_id && isset($pageIdMap[$field->slick_form_page_id])) {
                $newField->slick_form_page_id = $pageIdMap[$field->slick_form_page_id];
            }

            $newField->save();
            $fieldIdMap[$oldFieldId] = $newField->id;
            if ($oldParentId) {
                $oldParentByNewId[$newField->id] = $oldParentId;
            }
        }

        // 2) Fix parent_field_id to point to the corresponding new field IDs
        foreach ($form->fields()->get() as $newField) {
            $currentParent = $newField->parent_field_id;
            if ($currentParent && isset($fieldIdMap[$currentParent])) {
                $newField->parent_field_id = $fieldIdMap[$currentParent];
                $newField->save();
            }
        }

        // 2.5) Copy layout elements that belong to repeater fields (parent_field_id)
        // Must be done AFTER fields are copied and their IDs are mapped
        $this->copyRepeaterLayoutElements($template, $form->id, $fieldIdMap, $layoutElementIdMap, $pageIdMap);

        // 2.6) Update field layout_element_id references for fields that were copied
        // BEFORE the repeater layout elements existed
        foreach ($oldLayoutElementByNewFieldId as $oldFieldId => $oldLayoutElementId) {
            if (isset($fieldIdMap[$oldFieldId]) && isset($layoutElementIdMap[$oldLayoutElementId])) {
                $newFieldId = $fieldIdMap[$oldFieldId];
                $newLayoutElementId = $layoutElementIdMap[$oldLayoutElementId];

                $field = $form->fields()->find($newFieldId);
                if ($field) {
                    $field->slick_form_layout_element_id = $newLayoutElementId;
                    $field->save();
                }
            }
        }

        // 3) Remap conditional logic target_field_id references on copied fields
        foreach ($form->fields()->get() as $newField) {
            $logic = $newField->conditional_logic;
            if (empty($logic) || ! is_array($logic)) {
                continue;
            }

            $remapped = $this->remapConditionalLogic($logic, $fieldIdMap);
            if ($remapped !== $logic) {
                $newField->conditional_logic = $remapped;
                $newField->save();
            }
        }

        return $form;
    }

    /**
     * Remap conditional logic arrays from old field IDs to new IDs using the provided map.
     */
    protected function remapConditionalLogic(array $logic, array $fieldIdMap): array
    {
        // Helper closure to remap a single condition
        $remapCondition = function (array $condition) use ($fieldIdMap) {
            if (isset($condition['target_field_id'])) {
                $old = $condition['target_field_id'];
                if (isset($fieldIdMap[$old])) {
                    $condition['target_field_id'] = $fieldIdMap[$old];
                }
            }

            return $condition;
        };

        // New rule_groups format
        if (isset($logic['rule_groups']) && is_array($logic['rule_groups'])) {
            foreach ($logic['rule_groups'] as $gIndex => $group) {
                if (isset($group['conditions']) && is_array($group['conditions'])) {
                    foreach ($group['conditions'] as $cIndex => $condition) {
                        $logic['rule_groups'][$gIndex]['conditions'][$cIndex] = $remapCondition($condition);
                    }
                }
            }

            return $logic;
        }

        // Legacy flat conditions format
        if (isset($logic['conditions']) && is_array($logic['conditions'])) {
            foreach ($logic['conditions'] as $cIndex => $condition) {
                $logic['conditions'][$cIndex] = $remapCondition($condition);
            }
        }

        return $logic;
    }

    /**
     * Copy layout elements that belong to repeater fields (have parent_field_id set)
     */
    protected function copyRepeaterLayoutElements(
        CustomForm $template,
        int $newFormId,
        array $fieldIdMap,
        array &$layoutElementIdMap,
        array $pageIdMap
    ): void {
        // Get all layout elements from the template that have parent_field_id set
        $repeaterElements = \DigitalisStudios\SlickForms\Models\SlickFormLayoutElement::where('slick_form_id', $template->id)
            ->whereNotNull('parent_field_id')
            ->whereNull('parent_id') // Get top-level elements in repeaters
            ->orderBy('order')
            ->get();

        foreach ($repeaterElements as $element) {
            $oldParentFieldId = $element->parent_field_id;

            // Map the old parent field ID to the new one
            if (! isset($fieldIdMap[$oldParentFieldId])) {
                continue; // Skip if parent field wasn't copied
            }

            $newParentFieldId = $fieldIdMap[$oldParentFieldId];

            // Copy the element and its children recursively
            $this->copyLayoutElementsWithParentField(
                collect([$element]),
                $newFormId,
                null, // No parent_id (top-level in repeater)
                $newParentFieldId,
                $fieldIdMap,
                $layoutElementIdMap,
                $pageIdMap
            );
        }
    }

    /**
     * Copy layout elements recursively with parent_field_id support
     */
    protected function copyLayoutElementsWithParentField(
        $elements,
        int $formId,
        ?int $parentId,
        ?int $parentFieldId,
        array $fieldIdMap,
        array &$layoutElementIdMap,
        array $pageIdMap
    ): void {
        foreach ($elements as $element) {
            $oldElementId = $element->id;
            $newElement = $element->replicate();
            $newElement->slick_form_id = $formId;
            $newElement->parent_id = $parentId;
            $newElement->parent_field_id = $parentFieldId;

            // Update page reference if it exists
            if ($newElement->slick_form_page_id && isset($pageIdMap[$newElement->slick_form_page_id])) {
                $newElement->slick_form_page_id = $pageIdMap[$newElement->slick_form_page_id];
            }

            // Update field_id if this element references a field
            if ($newElement->field_id && isset($fieldIdMap[$newElement->field_id])) {
                $newElement->field_id = $fieldIdMap[$newElement->field_id];
            }

            // Remap element-level conditional logic if present
            if (is_array($newElement->conditional_logic) && ! empty($newElement->conditional_logic)) {
                $newElement->conditional_logic = $this->remapConditionalLogic($newElement->conditional_logic, $fieldIdMap);
            }

            $newElement->save();

            // Map old layout element ID to new ID
            $layoutElementIdMap[$oldElementId] = $newElement->id;

            // Recursively copy children (with parent_id set to this element)
            if ($element->children()->exists()) {
                $this->copyLayoutElementsWithParentField(
                    $element->children,
                    $formId,
                    $newElement->id, // Children have this element as parent
                    null, // Children don't have parent_field_id (only top-level elements in repeaters do)
                    $fieldIdMap,
                    $layoutElementIdMap,
                    $pageIdMap
                );
            }
        }
    }

    /**
     * Copy layout elements recursively
     */
    protected function copyLayoutElements(
        $elements,
        int $formId,
        ?int $parentId = null,
        array $fieldIdMap = [],
        array &$layoutElementIdMap = [],
        array $pageIdMap = []
    ): void {
        foreach ($elements as $element) {
            $oldElementId = $element->id;
            $newElement = $element->replicate();
            $newElement->slick_form_id = $formId;
            $newElement->parent_id = $parentId;

            // Update page reference if it exists
            if ($newElement->slick_form_page_id && isset($pageIdMap[$newElement->slick_form_page_id])) {
                $newElement->slick_form_page_id = $pageIdMap[$newElement->slick_form_page_id];
            }

            // Update field_id if this element references a field
            if ($newElement->field_id && isset($fieldIdMap[$newElement->field_id])) {
                $newElement->field_id = $fieldIdMap[$newElement->field_id];
            }

            // Remap element-level conditional logic if present
            if (is_array($newElement->conditional_logic) && ! empty($newElement->conditional_logic)) {
                $newElement->conditional_logic = $this->remapConditionalLogic($newElement->conditional_logic, $fieldIdMap);
            }

            $newElement->save();

            // Map old layout element ID to new ID
            $layoutElementIdMap[$oldElementId] = $newElement->id;

            // Recursively copy children
            if ($element->children()->exists()) {
                $this->copyLayoutElements($element->children, $formId, $newElement->id, $fieldIdMap, $layoutElementIdMap, $pageIdMap);
            }
        }
    }

    /**
     * Delete a template
     */
    public function deleteTemplate(CustomForm $template): bool
    {
        if (! $template->is_template) {
            throw new \InvalidArgumentException('The provided form is not a template.');
        }

        return $template->delete();
    }
}
