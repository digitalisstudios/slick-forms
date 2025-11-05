<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;

class TextareaField extends BaseFieldType
{
    public function getName(): string
    {
        return 'textarea';
    }

    public function getLabel(): string
    {
        return 'Textarea';
    }

    public function getIcon(): string
    {
        return 'bi bi-textarea-t';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $attributes = $this->getCommonAttributes($field);
        $attributes['rows'] = $field->options['rows'] ?? 4;
        $attributes[$this->getWireModelAttribute($field)] = 'formData.field_'.$field->id;
        $attributes['@change'] = '$wire.refreshVisibility()';
        // Bootstrap floating labels require explicit height on textarea
        if (! ($field->options['use_wysiwyg'] ?? false) && ($field->options['floating_label'] ?? false)) {
            $rows = (int) ($field->options['rows'] ?? 4);
            $rows = max(1, min(20, $rows));
            // Approximate a sensible height based on rows (fallback to 120px)
            $approxPx = max(100, min(400, ($rows * 24) + 24));
            if (empty($attributes['style'])) {
                $attributes['style'] = 'height: '.$approxPx.'px;';
            } else {
                $attributes['style'] .= ' height: '.$approxPx.'px;';
            }
        }

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';

        $useWysiwyg = (bool) ($field->options['use_wysiwyg'] ?? false);

        if ($useWysiwyg) {
            // Standard label (non-floating) for editors
            $html .= $this->renderLabel($field, $attributes['id']);

            // Quill editor container
            $editorId = 'quill_field_'.$field->id;
            $wireModel = 'formData.field_'.$field->id;
            $html .= '<div id="'.$editorId.'" style="height: 200px; background: white;"></div>';

            // Alpine + Quill init, sync with Livewire
            $html .= '<div x-data="{ editorContent: \''.htmlspecialchars($value ?? '', ENT_QUOTES).'\', quillInstance: null }" x-init="';
            $html .= '$nextTick(() => {';
            $html .= '  setTimeout(() => {';
            $html .= '    const el = document.getElementById(\''.$editorId.'\');';
            $html .= '    if (typeof Quill !== \'undefined\' && el && el.offsetParent !== null) {';
            $html .= '      const quill = new Quill(\'#'.$editorId.'\', {';
            $html .= '        theme: \'snow\',';
            $html .= '        modules: { toolbar: [[\'bold\', \'italic\', \'underline\'], [\'link\'], [{\'list\': \'ordered\'}, {\'list\': \'bullet\'}]] }';
            $html .= '      });';
            $html .= '      quillInstance = quill;';
            $html .= '      const initialContent = $wire.get(\''.$wireModel.'\') || editorContent || \'\';';
            $html .= '      if (initialContent) { quill.root.innerHTML = initialContent; }';
            $html .= '      editorContent = quill.root.innerHTML;';
            $html .= '      quill.on(\'text-change\', () => { editorContent = quill.root.innerHTML; });';
            $html .= '      $watch(\'editorContent\', value => { $wire.set(\''.$wireModel.'\', value, false); });';
            $html .= '    }';
            $html .= '  }, 50);';
            $html .= '});';
            $html .= '"></div>';
        } else {
            $html .= $this->renderLabelWithFloating($field, $attributes['id']);

            $textareaHtml = '<textarea '.$this->renderAttributes($attributes).$this->getValidationAttributes($field).'>';
            $textareaHtml .= htmlspecialchars($value ?? '');
            $textareaHtml .= '</textarea>';
            $html .= $this->wrapFloatingLabel($field, $textareaHtml);
        }

        $html .= $this->renderInvalidFeedback($field);
        $html .= $this->renderValidFeedback($field);
        $html .= $this->renderHelpText($field);

        $html .= '</div>';

        return $html;
    }

    public function renderBuilder(CustomFormField $field): string
    {
        $rows = $field->options['rows'] ?? 4;

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';
        $html .= $this->renderLabel($field, $elementId ?? '');
        $html .= '<textarea class="form-control" rows="'.$rows.'" placeholder="'.htmlspecialchars($field->placeholder ?? '').'" disabled></textarea>';
        $html .= $this->renderHelpText($field);

        $html .= '</div>';

        return $html;
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        $rules = parent::validate($field, $value);
        $rules[] = 'string';

        return $rules;
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'use_wysiwyg' => [
                'type' => 'switch',
                'label' => 'Enable WYSIWYG (Quill)',
                'tab' => 'options',
                'target' => 'options',
                'default' => false,
                'help' => 'Render this textarea as a rich text editor',
            ],
            'rows' => [
                'type' => 'number',
                'label' => 'Rows',
                'tab' => 'options',
                'target' => 'options',
                'default' => 4,
                'min' => 1,
                'max' => 20,
                'required' => false,
                'help' => 'Number of visible text rows',
            ],
        ]);
    }

    public function getAvailableValidationOptions(): array
    {
        return [
            'min' => [
                'type' => 'number',
                'label' => 'Minimum Length',
                'help' => 'Minimum number of characters required',
                'rule_format' => 'min:{value}',
            ],
            'max' => [
                'type' => 'number',
                'label' => 'Maximum Length',
                'help' => 'Maximum number of characters allowed',
                'rule_format' => 'max:{value}',
            ],
        ];
    }
}
