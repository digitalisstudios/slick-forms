{{--
    Schema Tab Renderer

    Auto-generates property panel content from schema configuration.

    Props:
    - $schema: Full schema array
    - $tab: Current tab name
    - $wireModelPrefix: Wire model prefix (e.g., 'properties', 'fieldOptions', 'elementSettings')
    - $type: Type of object being edited ('field' or 'element')
    - $currentValues: Current values array (optional)
--}}

@php
    $schemaRenderer = app(\DigitalisStudios\SlickForms\Services\SchemaRenderer::class);
    $currentValues = $currentValues ?? [];
@endphp

{{-- Check if there are any fields for this tab --}}
@if($schemaRenderer->hasFieldsForTab($schema, $tab))
    {{-- Render all fields for this tab --}}
    @foreach($schema as $fieldKey => $fieldConfig)
        @if(($fieldConfig['tab'] ?? 'basic') === $tab)
            @php
                $currentValue = $currentValues[$fieldKey] ?? ($fieldConfig['default'] ?? null);
                $fieldType = $fieldConfig['type'] ?? 'text';
                $componentName = $fieldConfig['component'] ?? null;
            @endphp

            {{-- Check if this is a custom component --}}
            @if($fieldType === 'custom' && $componentName)
                @include('slick-forms::livewire.partials.custom-properties.' . $componentName, [
                    'fieldKey' => $fieldKey,
                    'fieldConfig' => $fieldConfig,
                    'wireModelPrefix' => $wireModelPrefix,
                    'currentValue' => $currentValue,
                ])
            @else
                @include('slick-forms::livewire.partials.schema-field', [
                    'fieldKey' => $fieldKey,
                    'fieldConfig' => $fieldConfig,
                    'wireModelPrefix' => $wireModelPrefix,
                    'currentValue' => $currentValue,
                ])
            @endif
        @endif
    @endforeach
@else
    {{-- No fields for this tab --}}
    <div class="alert alert-info small mb-0">
        <i class="bi bi-info-circle me-1"></i>
        No additional settings available for this tab.
    </div>
@endif
