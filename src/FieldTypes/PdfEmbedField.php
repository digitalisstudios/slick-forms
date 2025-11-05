<?php

namespace DigitalisStudios\SlickForms\FieldTypes;

use DigitalisStudios\SlickForms\Models\CustomFormField;

class PdfEmbedField extends BaseFieldType
{
    public function getName(): string
    {
        return 'pdf_embed';
    }

    public function getLabel(): string
    {
        return 'PDF Embed';
    }

    public function getIcon(): string
    {
        return 'bi bi-filetype-pdf';
    }

    public function render(CustomFormField $field, mixed $value = null): string
    {
        $pdfUrl = $field->options['pdf_url'] ?? '';
        $width = $field->options['width'] ?? '100%';
        $height = $field->options['height'] ?? '600px';
        $className = $field->class ?? '';
        $style = $field->style ?? '';

        if (! $pdfUrl) {
            return '';
        }

        $viewerId = 'pdf_viewer_'.$field->id;

        $html = '<div';
        $classes = [];
        if ($className) {
            $classes[] = $className;
        }
        if (! empty($classes)) {
            $html .= ' class="'.htmlspecialchars(implode(' ', $classes)).'"';
        }

        // Build style attribute
        $styles = [];
        if ($width) {
            $styles[] = 'width: '.htmlspecialchars($width);
        }
        if ($height) {
            $styles[] = 'height: '.htmlspecialchars($height);
        }
        if ($style) {
            $styles[] = $style;
        }
        if (! empty($styles)) {
            $html .= ' style="'.htmlspecialchars(implode('; ', $styles)).'"';
        }
        $html .= '>';

        // Container for enhanced viewer
        $html .= '<div id="'.htmlspecialchars($viewerId).'" data-src="'.htmlspecialchars($pdfUrl).'"></div>';

        // Fallback iframe (hidden once viewer loads)
        $html .= '<iframe src="'.htmlspecialchars($pdfUrl).'" style="width:100%; height:100%; border:0;" title="Embedded PDF"></iframe>';

        // Dynamically load SlickPdfViewer assets only when needed
        $js = "(function(){\n".
              "  var viewerId = '".addslashes($viewerId)."';\n".
              "  function init(){\n".
              "    var el = document.getElementById('".addslashes($viewerId)."');\n".
              "    if (!el) return;\n".
              "    var src = el.getAttribute('data-src');\n".
              "    if (window.SlickPdfViewer && typeof window.SlickPdfViewer === 'function'){\n".
              "      try {\n".
              "        var viewer = new window.SlickPdfViewer({ container: '#".addslashes($viewerId)."', src: src });\n".
              "        var iframe = el.nextElementSibling; if (iframe && iframe.tagName === 'IFRAME') { iframe.style.display = 'none'; }\n".
              "      } catch(e) {}\n".
              "    }\n".
              "  }\n".
              "  function ensureCss(){\n".
              "    var links = document.getElementsByTagName('link');\n".
              "    var found = false;\n".
              "    for (var i=0;i<links.length;i++){ if ((links[i].href||'').indexOf('slick-pdf-viewer')!==-1) { found=true; break; } }\n".
              "    if (!found){ var l=document.createElement('link'); l.rel='stylesheet'; l.href='https://unpkg.com/slick-pdf-viewer@latest/dist/slick-pdf-viewer.min.css'; document.head.appendChild(l);}\n".
              "  }\n".
              "  function ensureLib(){\n".
              "    if (window.SlickPdfViewer){ init(); return; }\n".
              "    ensureCss();\n".
              "    var s=document.createElement('script'); s.src='https://unpkg.com/slick-pdf-viewer@latest/dist/slick-pdf-viewer.min.js'; s.onload=init; document.head.appendChild(s);\n".
              "  }\n".
              "  if (document.readyState==='loading'){ document.addEventListener('DOMContentLoaded', ensureLib); } else { ensureLib(); }\n".
              '})();';
        $html .= '<script>'.$js.'</script>';

        if ($field->help_text) {
            $html .= '<p class="text-muted small mt-2">'.htmlspecialchars($field->help_text).'</p>';
        }

        $html .= '</div>';

        return $html;
    }

    public function renderBuilder(CustomFormField $field): string
    {
        $pdfUrl = $field->options['pdf_url'] ?? '';

        if (! $pdfUrl) {
            // Placeholder when not configured
            $html = '<div class="border rounded p-3 bg-light text-center" style="min-height: 200px; display:flex; align-items:center; justify-content:center;">';
            $html .= '<div>';
            $html .= '<i class="bi bi-filetype-pdf" style="font-size:3rem; color:#6c757d;"></i>';
            $html .= '<div class="mt-2 text-muted small">Configure PDF in Field Properties</div>';
            $html .= '</div>';
            $html .= '</div>';

            return $html;
        }

        return $this->render($field);
    }

    public function validate(CustomFormField $field, mixed $value): array
    {
        // Display only; no validation
        return [];
    }

    public function getConfigSchema(): array
    {
        $schema = parent::getConfigSchema();

        // Remove input-related options
        unset($schema['floating_label'], $schema['field_size'], $schema['placeholder']);

        // Extract and re-add help text fields later
        $helpTextAsPopover = $schema['help_text_as_popover'];
        $helpText = $schema['help_text'];
        unset($schema['help_text_as_popover'], $schema['help_text']);

        $schema = array_merge($schema, [
            'input_mode' => [
                'type' => 'select',
                'label' => 'Source',
                'tab' => 'basic',
                'target' => 'options',
                'options' => [
                    'url' => 'PDF URL',
                    'upload' => 'File Upload',
                ],
                'default' => 'url',
            ],
            'pdf_url' => [
                'type' => 'text',
                'label' => 'PDF URL',
                'tab' => 'basic',
                'target' => 'options',
                'placeholder' => 'https://example.com/file.pdf',
                'show_if' => ['input_mode' => 'url'],
            ],
            'pdf_upload' => [
                'type' => 'file',
                'label' => 'Upload PDF',
                'tab' => 'basic',
                'target' => 'options',
                'accept' => 'application/pdf',
                'show_if' => ['input_mode' => 'upload'],
            ],
            'pdf_preview' => [
                'type' => 'html',
                'label' => 'Preview',
                'tab' => 'basic',
                'target' => 'options',
                'content' => '
                    <div x-show="$wire.properties.pdf_url" class="border rounded p-2 bg-light">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-filetype-pdf text-danger"></i>
                            <a :href="$wire.properties.pdf_url" target="_blank" class="small">Open uploaded PDF</a>
                        </div>
                        <div class="small text-success mt-2">
                            <i class="bi bi-check-circle me-1"></i> PDF uploaded successfully
                        </div>
                    </div>
                ',
            ],
            // Re-add help and sizing
            'help_text_as_popover' => $helpTextAsPopover,
            'help_text' => $helpText,
            'width' => [
                'type' => 'text',
                'label' => 'Width',
                'tab' => 'options',
                'target' => 'options',
                'placeholder' => 'e.g., 100%, 800px',
                'default' => '100%',
            ],
            'height' => [
                'type' => 'text',
                'label' => 'Height',
                'tab' => 'options',
                'target' => 'options',
                'placeholder' => 'e.g., 600px',
                'default' => '600px',
            ],
        ]);

        return $schema;
    }

    public function getAvailableValidationOptions(): array
    {
        return [];
    }

    public function getPropertyTabs(): array
    {
        return [
            'validation' => false,
        ];
    }
}
