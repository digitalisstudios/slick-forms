<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;

class SignaturePadField extends BaseFieldType
{
    public function getName(): string
    {
        return 'signature';
    }

    public function getLabel(): string
    {
        return 'Signature Pad';
    }

    public function getIcon(): string
    {
        return 'bi bi-pen';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $attributes = $this->getCommonAttributes($field);
        $canvasId = 'signature_canvas_'.$field->id;
        $wireModel = 'formData.field_'.$field->id;

        // Get configuration options
        $width = $field->options['canvas_width'] ?? 500;
        $height = $field->options['canvas_height'] ?? 200;
        $penColor = $field->options['pen_color'] ?? '#000000';
        $backgroundColor = $field->options['background_color'] ?? '#ffffff';

        // Support CSS units (px, %, rem, em, etc.)
        $widthWithUnit = is_numeric($width) ? $width.'px' : $width;
        $heightWithUnit = is_numeric($height) ? $height.'px' : $height;

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';

        // Render label
        $html .= $this->renderLabel($field, $attributes['id']);

        // Load Signature Pad library (only once per page)
        static $signaturePadLoaded = false;
        if (! $signaturePadLoaded) {
            $html .= '<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>';
            $signaturePadLoaded = true;
        }

        // Signature pad wrapper with Alpine.js
        $html .= '<div x-data="{';
        $html .= '  signaturePad: null,';
        $html .= '  signatureData: $wire.entangle(\''.$wireModel.'\'),';
        $html .= '  isEmpty: true';
        $html .= '}" x-init="';
        $html .= '$nextTick(() => {';
        $html .= '  setTimeout(() => {';
        $html .= '    const canvas = document.getElementById(\''.$canvasId.'\');';
        $html .= '    if (typeof SignaturePad !== \'undefined\' && canvas) {';
        $html .= '      const wrapper = canvas.parentElement;';
        $html .= '      const rect = wrapper.getBoundingClientRect();';
        $html .= '      canvas.width = rect.width;';
        $html .= '      canvas.height = rect.height;';
        $html .= '      signaturePad = new SignaturePad(canvas, {';
        $html .= '        penColor: \''.$penColor.'\',';
        $html .= '        backgroundColor: \''.$backgroundColor.'\'';
        $html .= '      });';
        $html .= '      if (signatureData) {';
        $html .= '        signaturePad.fromDataURL(signatureData);';
        $html .= '        isEmpty = false;';
        $html .= '      }';
        $html .= '      signaturePad.addEventListener(\'endStroke\', () => {';
        $html .= '        signatureData = signaturePad.toDataURL();';
        $html .= '        isEmpty = signaturePad.isEmpty();';
        $html .= '      });';
        $html .= '    }';
        $html .= '  }, 50);';
        $html .= '});';
        $html .= '">';

        // Canvas element (use CSS for sizing to support all units)
        $html .= '<div class="signature-pad-wrapper border rounded" style="background: '.$backgroundColor.'; display: inline-block; width: '.$widthWithUnit.'; height: '.$heightWithUnit.';">';
        $html .= '<canvas id="'.$canvasId.'" class="signature-canvas" style="width: 100%; height: 100%;"></canvas>';
        $html .= '</div>';

        // Control buttons
        $html .= '<div class="mt-2">';
        $html .= '<button type="button" class="btn btn-sm btn-outline-danger" ';
        $html .= 'x-show="!isEmpty" ';
        $html .= '@click="signaturePad.clear(); signatureData = \'\'; isEmpty = true;">';
        $html .= '<i class="bi bi-x-circle me-1"></i> Clear Signature';
        $html .= '</button>';
        $html .= '</div>';

        $html .= '</div>'; // End Alpine wrapper

        // Hidden input for validation
        $html .= '<input type="hidden" '.$this->getWireModelAttribute($field).'="'.$wireModel.'" '.$this->getValidationAttributes($field).'>';

        $html .= $this->renderInvalidFeedback($field);
        $html .= $this->renderValidFeedback($field);
        $html .= $this->renderHelpText($field);

        $html .= '</div>';

        return $html;
    }

    public function renderBuilder(CustomFormField $field): string
    {
        $width = $field->options['canvas_width'] ?? 500;
        $height = $field->options['canvas_height'] ?? 200;
        $backgroundColor = $field->options['background_color'] ?? '#ffffff';

        // Support CSS units (px, %, rem, em, etc.)
        $widthWithUnit = is_numeric($width) ? $width.'px' : $width;
        $heightWithUnit = is_numeric($height) ? $height.'px' : $height;

        // Apply utility classes
        $wrapperClasses = $this->getFieldClasses($field, 'mb-3');

        $html = '<div class="'.$wrapperClasses.'"';
        if ($field->style) {
            $html .= ' style="'.htmlspecialchars($field->style).'"';
        }
        $html .= '>';
        $html .= $this->renderLabel($field, '');

        // Preview placeholder
        $html .= '<div class="signature-pad-preview border rounded p-3" style="background: '.$backgroundColor.'; width: '.$widthWithUnit.'; height: '.$heightWithUnit.'; display: flex; align-items: center; justify-content: center;">';
        $html .= '<div class="text-muted"><i class="bi bi-pen me-1"></i> Signature pad area ('.$width.' × '.$height.')</div>';
        $html .= '</div>';

        $html .= $this->renderHelpText($field);
        $html .= '</div>';

        return $html;
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        $rules = parent::validate($field, $value);
        $rules[] = 'string';
        $rules[] = 'nullable';

        return $rules;
    }

    public function processValue(mixed $value): mixed
    {
        // Store as base64 data URL (can be saved directly to database or extracted)
        return $value;
    }

    public function getConfigSchema(): array
    {
        return array_merge(parent::getConfigSchema(), [
            'canvas_width' => [
                'type' => 'number',
                'label' => 'Canvas Width (px)',
                'tab' => 'options',
                'target' => 'options',
                'default' => 500,
                'help' => 'Width of the signature canvas in pixels',
            ],
            'canvas_height' => [
                'type' => 'number',
                'label' => 'Canvas Height (px)',
                'tab' => 'options',
                'target' => 'options',
                'default' => 200,
                'help' => 'Height of the signature canvas in pixels',
            ],
            'pen_color' => [
                'type' => 'color',
                'label' => 'Pen Color',
                'tab' => 'options',
                'target' => 'options',
                'default' => '#000000',
                'help' => 'Color of the signature pen',
            ],
            'background_color' => [
                'type' => 'color',
                'label' => 'Background Color',
                'tab' => 'options',
                'target' => 'options',
                'default' => '#ffffff',
                'help' => 'Background color of the canvas',
            ],
        ]);
    }

    public function getAvailableValidationOptions(): array
    {
        return [
            // No additional validation options beyond required
            // Signature is either present (base64 data URL) or not
        ];
    }
}
