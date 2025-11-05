<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;

class TagsField extends BaseFieldType
{
    public function getName(): string
    {
        return 'tags';
    }

    public function getLabel(): string
    {
        return 'Tags';
    }

    public function getIcon(): string
    {
        return 'bi bi-tags';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $maxTags = $field->options['max_tags'] ?? null;
        $suggestions = $field->options['suggestions'] ?? [];
        $existingTags = is_array($value) ? $value : ($value ? json_decode($value, true) : []);
        $existingTagsJson = json_encode($existingTags ?? []);

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';
        // Pass true for $includeAsterisk since CSS can't reach the nested input in input-group
        $fieldId = 'field_'.$field->id;
        $html .= $this->renderLabel($field, $fieldId, true);

        $html .= '<div x-data="{
            tags: '.$existingTagsJson.',
            newTag: \'\',
            addTag() {
                const tag = this.newTag.trim();
                if (tag && !this.tags.includes(tag)'.($maxTags ? ' && this.tags.length < '.$maxTags : '').') {
                    this.tags.push(tag);
                    this.newTag = \'\';
                    $wire.set(\'formData.field_'.$field->id.'\', this.tags);
                    $wire.refreshVisibility();
                }
            },
            removeTag(index) {
                this.tags.splice(index, 1);
                $wire.set(\'formData.field_'.$field->id.'\', this.tags);
                $wire.refreshVisibility();
            }
        }">';

        // Tags display
        $html .= '<div class="d-flex flex-wrap gap-2 mb-2">';
        $html .= '<template x-for="(tag, index) in tags" :key="index">';
        $html .= '<span class="badge bg-primary d-flex align-items-center gap-1">';
        $html .= '<span x-text="tag"></span>';
        $html .= '<button type="button" class="btn-close btn-close-white" style="font-size: 0.6rem;" @click="removeTag(index)"></button>';
        $html .= '</span>';
        $html .= '</template>';
        $html .= '</div>';

        // Input for new tag
        $html .= '<div class="input-group">';
        $html .= '<input type="text" class="form-control" x-model="newTag" @keydown.enter.prevent="addTag()" placeholder="Type and press Enter to add tag">';
        $html .= '<button type="button" class="btn btn-outline-secondary" @click="addTag()">Add</button>';
        $html .= '</div>';

        // Hidden input for Livewire
        $html .= '<input type="hidden" '.$this->getWireModelAttribute($field).'="formData.field_'.$field->id.'" '.$this->getValidationAttributes($field).'>';

        if ($maxTags) {
            $html .= '<div class="form-text">Maximum '.$maxTags.' tags allowed. <span x-text="tags.length"></span> / '.$maxTags.' used</div>';
        }
        $html .= $this->renderInvalidFeedback($field);
        $html .= $this->renderValidFeedback($field);
        $html .= $this->renderHelpText($field);

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    public function renderBuilder(CustomFormField $field): string
    {
        $maxTags = $field->options['max_tags'] ?? null;

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';
        // Pass true for $includeAsterisk since CSS can't reach the nested input in input-group
        $html .= $this->renderLabel($field, $elementId ?? '', true);

        $html .= '<div class="d-flex flex-wrap gap-2 mb-2">';
        $html .= '<span class="badge bg-primary">Example Tag</span>';
        $html .= '<span class="badge bg-primary">Another Tag</span>';
        $html .= '</div>';

        $html .= '<div class="input-group">';
        $html .= '<input type="text" class="form-control" placeholder="Type and press Enter to add tag" disabled>';
        $html .= '<button type="button" class="btn btn-outline-secondary" disabled>Add</button>';
        $html .= '</div>';

        if ($maxTags) {
            $html .= '<div class="form-text">Maximum '.$maxTags.' tags allowed</div>';
        }        $html .= $this->renderHelpText($field);

        $html .= '</div>';

        return $html;
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        $rules = parent::validate($field, $value);
        $rules[] = 'array';

        $maxTags = $field->options['max_tags'] ?? null;
        if ($maxTags) {
            $rules[] = 'max:'.$maxTags;
        }

        return $rules;
    }

    public function processValue(mixed $value): mixed
    {
        return is_array($value) ? json_encode($value) : $value;
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'max_tags' => [
                'type' => 'number',
                'label' => 'Maximum Tags (optional)',
                'tab' => 'options',
                'target' => 'options',
                'placeholder' => 'Leave empty for unlimited',
                'required' => false,
                'help' => 'Maximum number of tags allowed (leave empty for unlimited)',
            ],
        ]);
    }

    public function getAvailableValidationOptions(): array
    {
        return [
            'min' => [
                'type' => 'number',
                'label' => 'Minimum Tags',
                'help' => 'Minimum number of tags required',
                'rule_format' => 'min:{value}',
            ],
            'max' => [
                'type' => 'number',
                'label' => 'Maximum Tags',
                'help' => 'Maximum number of tags allowed',
                'rule_format' => 'max:{value}',
            ],
        ];
    }
}
