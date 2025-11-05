{{--
    Schema Field Component

    Auto-renders a single field based on schema configuration.

    Props:
    - $fieldKey: The property key (string)
    - $fieldConfig: Schema configuration array
    - $wireModelPrefix: Wire model prefix (string, e.g., 'fieldOptions')
    - $currentValue: Current value (optional)
--}}

@php
    $schemaRenderer = app(\DigitalisStudios\SlickForms\Services\SchemaRenderer::class);
    echo $schemaRenderer->renderField(
        $fieldKey,
        $fieldConfig,
        $wireModelPrefix,
        $currentValue ?? null
    );
@endphp
