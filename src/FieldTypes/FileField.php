<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;

class FileField extends BaseFieldType
{
    public function getName(): string
    {
        return 'file';
    }

    public function getLabel(): string
    {
        return 'File Upload';
    }

    public function getIcon(): string
    {
        return 'bi bi-file-earmark-arrow-up';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $acceptedTypes = $field->options['accepted_types'] ?? '';
        $maxSize = $field->options['max_size'] ?? '10240'; // 10MB default
        $multiple = $field->options['multiple'] ?? false;
        $enableDragDrop = $field->options['enable_drag_drop'] ?? true;
        $showPreview = $field->options['show_preview'] ?? true;
        $fieldId = 'field_'.$field->id;

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';
        // Note: File upload uses custom drag-drop interface, floating labels won't work
        $html .= $this->renderLabelWithFloating($field, $fieldId);

        if ($enableDragDrop) {
            // Drag-and-drop zone with Alpine.js
            $html .= '<div x-data="{
                isDragging: false,
                files: [],
                handleFiles(files) {
                    this.files = Array.from(files);
                }
            }">';

            $html .= '<div
                @dragover.prevent="isDragging = true"
                @dragleave.prevent="isDragging = false"
                @drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; handleFiles($event.dataTransfer.files)"
                :class="isDragging ? \'border-primary bg-primary bg-opacity-10\' : \'border-secondary\'"
                class="border border-2 border-dashed rounded p-4 text-center"
                style="transition: all 0.2s; cursor: pointer;"
                @click="$refs.fileInput.click()"
            >';

            $html .= '<i class="bi bi-cloud-upload fs-1 text-muted d-block mb-2"></i>';
            $html .= '<p class="mb-2"><strong>Drag & drop '.($multiple ? 'files' : 'a file').' here</strong></p>';
            $html .= '<p class="text-muted small mb-0">or click to browse</p>';

            $html .= '</div>';

            // Hidden file input
            $html .= '<input type="file" ';
            $html .= 'class="d-none" ';
            $html .= 'id="'.$fieldId.'" ';
            $html .= 'x-ref="fileInput" ';
            $html .= 'wire:model="formData.field_'.$field->id.'" ';
            $html .= '@change="handleFiles($event.target.files)" ';

            if ($acceptedTypes) {
                $html .= 'accept="'.htmlspecialchars($acceptedTypes).'" ';
            }
            if ($multiple) {
                $html .= 'multiple ';
            }
            if ($field->is_required) {
                $html .= 'required ';
            }
            $html .= '>';

            // File preview area
            if ($showPreview) {
                $html .= '<div x-show="files.length > 0" class="mt-3">';
                $html .= '<div class="d-flex flex-wrap gap-2">';
                $html .= '<template x-for="(file, index) in files" :key="index">';
                $html .= '<div class="card position-relative" style="width: 120px;">';
                $html .= '<button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" style="padding: 0.15rem 0.4rem; z-index: 10;" @click.stop="files.splice(index, 1)" title="Remove file">';
                $html .= '<i class="bi bi-x-lg"></i>';
                $html .= '</button>';
                $html .= '<div class="card-body p-2 text-center">';
                $html .= '<template x-if="file.type.startsWith(\'image/\')">';
                $html .= '<img :src="URL.createObjectURL(file)" class="img-fluid rounded mb-2" style="max-height: 60px; object-fit: cover;">';
                $html .= '</template>';
                $html .= '<template x-if="!file.type.startsWith(\'image/\')">';
                $html .= '<i class="bi bi-file-earmark fs-1 text-muted d-block mb-2"></i>';
                $html .= '</template>';
                $html .= '<p class="small mb-0 text-truncate" :title="file.name" x-text="file.name"></p>';
                $html .= '<p class="small text-muted mb-0" x-text="(file.size / 1024).toFixed(1) + \' KB\'"></p>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</template>';
                $html .= '</div>';
                $html .= '</div>';
            }

            $html .= '</div>';
        } else {
            // Standard file input
            $html .= '<input type="file" ';
            $html .= 'class="form-control" ';
            $html .= 'id="'.$fieldId.'" ';
            $html .= 'wire:model="formData.field_'.$field->id.'" ';

            if ($acceptedTypes) {
                $html .= 'accept="'.htmlspecialchars($acceptedTypes).'" ';
            }
            if ($multiple) {
                $html .= 'multiple ';
            }
            if ($field->is_required) {
                $html .= 'required ';
            }
            $html .= '>';
        }

        $html .= $this->renderInvalidFeedback($field);
        $html .= $this->renderValidFeedback($field);
        $html .= $this->renderHelpText($field);

        // Show file size limit
        $maxSizeMB = round($maxSize / 1024, 2);
        $html .= '<div class="form-text">Maximum file size: '.$maxSizeMB.' MB';
        if ($acceptedTypes) {
            $html .= ' | Accepted types: '.htmlspecialchars($acceptedTypes);
        }
        $html .= '</div>';

        // Loading indicator for Livewire file uploads
        $html .= '<div wire:loading wire:target="formData.field_'.$field->id.'" class="text-muted mt-2">';
        $html .= '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>';
        $html .= 'Uploading...';
        $html .= '</div>';

        $html .= '</div>';

        return $html;
    }

    public function renderBuilder(CustomFormField $field): string
    {
        $acceptedTypes = $field->options['accepted_types'] ?? '';
        $maxSize = $field->options['max_size'] ?? '10240';
        $multiple = $field->options['multiple'] ?? false;
        $enableDragDrop = $field->options['enable_drag_drop'] ?? true;
        $showPreview = $field->options['show_preview'] ?? true;

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';
        $html .= $this->renderLabel($field, $elementId ?? '');

        if ($enableDragDrop) {
            // Show drag-and-drop preview
            $html .= '<div class="border border-2 border-dashed border-secondary rounded p-4 text-center">';
            $html .= '<i class="bi bi-cloud-upload fs-1 text-muted d-block mb-2"></i>';
            $html .= '<p class="mb-2"><strong>Drag & drop '.($multiple ? 'files' : 'a file').' here</strong></p>';
            $html .= '<p class="text-muted small mb-0">or click to browse</p>';
            $html .= '</div>';
        } else {
            $html .= '<input type="file" class="form-control" disabled>';
        }

        $html .= $this->renderInvalidFeedback($field);
        $html .= $this->renderValidFeedback($field);
        $html .= $this->renderHelpText($field);

        $maxSizeMB = round($maxSize / 1024, 2);

        // Show info about enabled features
        $features = [];
        $features[] = 'Max size: '.$maxSizeMB.' MB';
        if ($acceptedTypes) {
            $features[] = 'Types: '.htmlspecialchars($acceptedTypes);
        }
        if ($multiple) {
            $features[] = 'Multiple files';
        }
        if ($enableDragDrop) {
            $features[] = 'Drag & drop';
        }
        if ($showPreview) {
            $features[] = 'Preview enabled';
        }

        $html .= '<div class="form-text small"><i class="bi bi-info-circle me-1"></i>'.implode(' | ', $features).'</div>';

        $html .= '</div>';

        return $html;
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        $rules = parent::validate($field, $value);
        $rules[] = 'file';

        $maxSize = $field->options['max_size'] ?? '10240';
        $rules[] = 'max:'.$maxSize;

        $acceptedTypes = $field->options['accepted_types'] ?? '';
        if ($acceptedTypes) {
            // Convert MIME types or extensions to Laravel validation format
            $mimes = str_replace(['.', ','], ['', ','], $acceptedTypes);
            $rules[] = 'mimes:'.$mimes;
        }

        return $rules;
    }

    public function processValue(mixed $value): mixed
    {
        // If it's a Livewire TemporaryUploadedFile, store it and return the path
        if (is_object($value) && method_exists($value, 'store')) {
            return $value->store('form-uploads', 'public');
        }

        // If it's already a string path, return as-is
        return $value;
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'accepted_types' => [
                'type' => 'text',
                'label' => 'Accepted File Types',
                'tab' => 'options',
                'target' => 'options',
                'placeholder' => '.pdf,.doc,.docx or image/*',
                'required' => false,
                'help' => 'File extensions or MIME types (comma-separated)',
            ],
            'max_size' => [
                'type' => 'number',
                'label' => 'Max File Size (KB)',
                'tab' => 'options',
                'target' => 'options',
                'default' => '10240',
                'required' => false,
                'help' => 'Maximum file size in kilobytes (default: 10MB)',
            ],
            'multiple' => [
                'type' => 'switch',
                'label' => 'Allow Multiple Files',
                'tab' => 'options',
                'target' => 'options',
                'default' => false,
                'required' => false,
                'help' => 'Allow users to upload multiple files at once',
            ],
            'enable_drag_drop' => [
                'type' => 'switch',
                'label' => 'Enable Drag & Drop',
                'tab' => 'options',
                'target' => 'options',
                'default' => true,
                'required' => false,
                'help' => 'Allow drag and drop file uploads',
            ],
            'show_preview' => [
                'type' => 'switch',
                'label' => 'Show File Preview',
                'tab' => 'options',
                'target' => 'options',
                'default' => true,
                'required' => false,
                'help' => 'Display preview of uploaded files',
            ],
        ]);
    }

    public function getAvailableValidationOptions(): array
    {
        return [
            'mimes' => [
                'type' => 'text',
                'label' => 'Allowed MIME Types',
                'help' => 'Comma-separated list of allowed file extensions (e.g., pdf,jpg,png)',
                'rule_format' => 'mimes:{value}',
                'placeholder' => 'pdf,doc,docx',
            ],
            'max' => [
                'type' => 'number',
                'label' => 'Maximum File Size (KB)',
                'help' => 'Maximum file size in kilobytes',
                'rule_format' => 'max:{value}',
                'placeholder' => '10240',
            ],
            'dimensions' => [
                'type' => 'text',
                'label' => 'Image Dimensions',
                'help' => 'For images only. Format: min_width=100,max_width=1000,min_height=100,max_height=1000',
                'rule_format' => 'dimensions:{value}',
                'placeholder' => 'min_width=100,max_width=1000',
            ],
        ];
    }
}
