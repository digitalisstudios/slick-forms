<?php

namespace DigitalisStudios\SlickForms\Services;

use DigitalisStudios\SlickForms\Models\CustomForm;
use DigitalisStudios\SlickForms\Models\FormVersion;
use DigitalisStudios\SlickForms\Models\SlickFormLayoutElement;
use Illuminate\Support\Facades\DB;

class FormVersionService
{
    /**
     * Create a new version snapshot of the form.
     */
    public function createVersion(CustomForm $form, ?int $userId = null, ?string $versionName = null, ?string $changeSummary = null): FormVersion
    {
        $snapshot = $this->buildFormSnapshot($form);

        // Get next version number for this form
        $versionNumber = FormVersion::where('form_id', $form->id)->max('version_number') + 1;

        // Auto-generate change summary if not provided
        if (! $changeSummary) {
            $changeSummary = $this->generateChangeSummary($form, $versionNumber);
        }

        return FormVersion::create([
            'form_id' => $form->id,
            'version_number' => $versionNumber,
            'version_name' => $versionName,
            'form_snapshot' => $snapshot,
            'published_by' => $userId,
            'change_summary' => $changeSummary,
            'published_at' => now(),
        ]);
    }

    /**
     * Build complete snapshot of current form state.
     */
    public function buildFormSnapshot(CustomForm $form): array
    {
        return [
            'form' => $form->only([
                'name',
                'description',
                'is_active',
                'settings',
            ]),
            'fields' => $form->fields()->orderBy('order')->get()->map(function ($field) {
                return $field->only([
                    'id',
                    'field_type',
                    'name',
                    'label',
                    'placeholder',
                    'help_text',
                    'validation_rules',
                    'conditional_logic',
                    'options',
                    'order',
                    'slick_form_layout_element_id',
                    'class',
                    'style',
                    'element_id',
                    'show_label',
                    'help_text_as_popover',
                ]);
            })->toArray(),
            'layout_elements' => SlickFormLayoutElement::where('slick_form_id', $form->id)
                ->orderBy('order')
                ->get()
                ->map(function ($element) {
                    return $element->only([
                        'id',
                        'parent_id',
                        'element_type',
                        'settings',
                        'order',
                    ]);
                })->toArray(),
            'pages' => $form->pages()->orderBy('order')->get()->map(function ($page) {
                return $page->only([
                    'id',
                    'title',
                    'description',
                    'order',
                    'settings',
                ]);
            })->toArray(),
        ];
    }

    /**
     * Restore form to a specific version.
     */
    public function restoreVersion(CustomForm $form, FormVersion $version): bool
    {
        DB::beginTransaction();

        try {
            $snapshot = $version->form_snapshot;

            // Update form settings
            $form->update($snapshot['form']);

            // Delete existing fields and layout elements (must delete ALL layout elements, not just top-level)
            $form->fields()->delete();
            SlickFormLayoutElement::where('slick_form_id', $form->id)->delete();
            $form->pages()->delete();

            // Restore pages
            $pageIdMap = [];
            foreach ($snapshot['pages'] ?? [] as $pageData) {
                $oldId = $pageData['id'];
                unset($pageData['id']);

                $page = $form->pages()->create([
                    ...$pageData,
                    'slick_form_id' => $form->id,
                ]);

                $pageIdMap[$oldId] = $page->id;
            }

            // Restore layout elements (need to handle parent_id relationships)
            $elementIdMap = [];
            $elementsToCreate = collect($snapshot['layout_elements'] ?? []);

            // First pass: Create elements without parents
            $elementsToCreate->where('parent_id', null)->each(function ($elementData) use ($form, &$elementIdMap) {
                $oldId = $elementData['id'];
                unset($elementData['id']);

                $element = SlickFormLayoutElement::create([
                    ...$elementData,
                    'slick_form_id' => $form->id,
                    'parent_id' => null,
                ]);

                $elementIdMap[$oldId] = $element->id;
            });

            // Second pass: Create child elements (up to 10 levels deep)
            for ($depth = 0; $depth < 10; $depth++) {
                $created = 0;

                $elementsToCreate->whereNotNull('parent_id')->each(function ($elementData) use ($form, &$elementIdMap, &$created) {
                    // Skip if already created
                    if (isset($elementIdMap[$elementData['id']])) {
                        return;
                    }

                    // Skip if parent not yet created
                    if (! isset($elementIdMap[$elementData['parent_id']])) {
                        return;
                    }

                    $oldId = $elementData['id'];
                    $oldParentId = $elementData['parent_id'];
                    unset($elementData['id']);

                    $element = SlickFormLayoutElement::create([
                        ...$elementData,
                        'slick_form_id' => $form->id,
                        'parent_id' => $elementIdMap[$oldParentId],
                    ]);

                    $elementIdMap[$oldId] = $element->id;
                    $created++;
                });

                if ($created === 0) {
                    break;
                }
            }

            // Restore fields
            foreach ($snapshot['fields'] ?? [] as $fieldData) {
                $oldElementId = $fieldData['slick_form_layout_element_id'];
                unset($fieldData['id']);

                $form->fields()->create([
                    ...$fieldData,
                    'slick_form_id' => $form->id,
                    'slick_form_layout_element_id' => $oldElementId ? ($elementIdMap[$oldElementId] ?? null) : null,
                ]);
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get version history for a form.
     */
    public function getVersionHistory(CustomForm $form)
    {
        return FormVersion::where('form_id', $form->id)
            ->orderBy('version_number', 'desc')
            ->with('publisher')
            ->get();
    }

    /**
     * Compare two versions and return differences.
     */
    public function compareVersions(FormVersion $version1, FormVersion $version2): array
    {
        $snap1 = $version1->form_snapshot;
        $snap2 = $version2->form_snapshot;

        $differences = [];

        // Compare form settings
        foreach ($snap1['form'] as $key => $value) {
            if (($snap2['form'][$key] ?? null) !== $value) {
                $differences['form'][$key] = [
                    'from' => $value,
                    'to' => $snap2['form'][$key] ?? null,
                ];
            }
        }

        // Compare field counts
        $differences['field_count'] = [
            'from' => count($snap1['fields'] ?? []),
            'to' => count($snap2['fields'] ?? []),
        ];

        // Compare layout element counts
        $differences['element_count'] = [
            'from' => count($snap1['layout_elements'] ?? []),
            'to' => count($snap2['layout_elements'] ?? []),
        ];

        // Compare page counts
        $differences['page_count'] = [
            'from' => count($snap1['pages'] ?? []),
            'to' => count($snap2['pages'] ?? []),
        ];

        return $differences;
    }

    /**
     * Auto-generate change summary based on form state.
     */
    protected function generateChangeSummary(CustomForm $form, int $versionNumber): string
    {
        $fieldCount = $form->fields()->count();
        $elementCount = $form->layoutElements()->count();
        $pageCount = $form->pages()->count();

        return "Version {$versionNumber}: Form snapshot with {$fieldCount} fields, {$elementCount} layout elements, and {$pageCount} pages.";
    }

    /**
     * Delete a specific version (admin only).
     */
    public function deleteVersion(FormVersion $version): bool
    {
        // Don't allow deleting if this version has submissions
        if ($version->submissions()->count() > 0) {
            throw new \RuntimeException('Cannot delete version with existing submissions');
        }

        return $version->delete();
    }

    /**
     * Get the latest version for a form.
     */
    public function getLatestVersion(CustomForm $form): ?FormVersion
    {
        return FormVersion::where('form_id', $form->id)
            ->orderBy('version_number', 'desc')
            ->first();
    }

    /**
     * Check if form has changed since last version.
     */
    public function hasChanges(CustomForm $form): bool
    {
        $latestVersion = $this->getLatestVersion($form);

        if (! $latestVersion) {
            return true; // No versions yet, so yes there are changes
        }

        $currentSnapshot = $this->buildFormSnapshot($form);
        $latestSnapshot = $latestVersion->form_snapshot;

        // Simple comparison - could be more sophisticated
        return json_encode($currentSnapshot) !== json_encode($latestSnapshot);
    }
}
