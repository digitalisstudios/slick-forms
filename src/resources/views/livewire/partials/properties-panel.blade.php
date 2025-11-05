{{--
    Properties Panel - Refactored Version

    Dynamic, schema-driven properties panel for fields and layout elements.
    Uses TabRegistry and SchemaRenderer for extensibility.
--}}

{{-- Error Alert --}}
@if($errorMessage)
    <div class="alert alert-danger alert-dismissible mb-3" role="alert">
        <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Error!</strong> {{ $errorMessage }}
        <button type="button" class="btn-close" wire:click="dismissError" aria-label="Close"></button>
    </div>
@endif

{{-- Selection Info for Containers/Rows --}}
@if($selectedElement && !$showElementEditor && !$showFieldEditor && !$showPageEditor)
    <div class="alert alert-success mb-3">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <strong>Selected:</strong>
            <button type="button" class="btn-close btn-sm" wire:click="selectElement(null)"></button>
        </div>
        <div>
            @php
                $elementRegistry = app(\DigitalisStudios\SlickForms\Services\LayoutElementRegistry::class);
                if ($elementRegistry->has($selectedElement->element_type)) {
                    $elementTypeObj = $elementRegistry->get($selectedElement->element_type);
                    echo htmlspecialchars($elementTypeObj->getLabel());

                    $allowedChildren = $elementTypeObj->getAllowedChildren();
                    if ($allowedChildren !== ['*']) {
                        echo ' - Can contain: ' . implode(', ', array_map('ucfirst', $allowedChildren));
                    }
                } else {
                    echo ucwords(str_replace('_', ' ', $selectedElement->element_type));
                }
            @endphp
        </div>
    </div>
@endif

{{-- ============================ --}}
{{-- PAGE EDITOR                  --}}
{{-- ============================ --}}
@if($showPageEditor && $selectedPageId)
    <div class="card">
        <div class="card-header sticky-top d-flex justify-content-between align-items-center" style="background: white; z-index: 10;">
            <h5 class="mb-0">Page Properties</h5>
            <button type="button" class="btn-close" wire:click="closePageEditor"></button>
        </div>

        <div class="card-body">
            <form wire:submit="savePage">
                {{-- Page Title --}}
                <div class="mb-3">
                    <label for="pageTitle" class="form-label">Page Title <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        class="form-control"
                        id="pageTitle"
                        wire:model="pageTitle"
                        required
                    >
                    @error('pageTitle')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Page Description --}}
                <div class="mb-3">
                    <label for="pageDescription" class="form-label">Page Description</label>
                    <textarea
                        class="form-control"
                        id="pageDescription"
                        wire:model="pageDescription"
                        rows="3"
                    ></textarea>
                </div>

                {{-- Page Icon --}}
                <div class="mb-3" wire:ignore>
                    <label for="pageIcon" class="form-label">Page Icon</label>
                    <div class="input-group">
                        <button
                            id="pageIconPicker"
                            class="btn btn-outline-secondary"
                            type="button"
                            data-icon="{{ $pageIcon ?: 'bi-file-text' }}"
                            role="iconpicker"
                            style="min-width: 60px;"
                        >
                            <i class="{{ $pageIcon ?: 'bi-file-text' }}"></i>
                        </button>
                        <input
                            type="text"
                            class="form-control"
                            id="pageIconInput"
                            value="{{ $pageIcon }}"
                            placeholder="Select an icon"
                            readonly
                        >
                    </div>
                    <small class="text-muted">Click the icon button to choose from Bootstrap Icons</small>
                </div>

                {{-- Show in Progress --}}
                <div class="mb-3">
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="pageShowInProgress"
                            wire:model="pageShowInProgress"
                        >
                        <label class="form-check-label" for="pageShowInProgress">
                            Show in Progress Indicator
                        </label>
                    </div>
                    <small class="text-muted">Display this page in the multi-step progress indicator</small>
                </div>

                {{-- Save Button --}}
                <div class="d-grid justify-content-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-floppy me-1"></i> Save Page Properties
                    </button>
                </div>
            </form>
        </div>
    </div>

{{-- ============================ --}}
{{-- FORM SETTINGS EDITOR         --}}
{{-- ============================ --}}
@elseif($showFormEditor)
    @php
        $tabRegistry = app(\DigitalisStudios\SlickForms\Services\TabRegistry::class);
        $tabs = $tabRegistry->getFormTabs();
    @endphp

    <div class="card">
        <div class="card-header sticky-top d-flex justify-content-between align-items-center" style="background: white; z-index: 10;">
            <h5 class="mb-0">Form Settings</h5>
            @if(!$settingsOnly)
                <button type="button" class="btn-close" wire:click="closeFormSettings"></button>
            @endif
        </div>

        <div class="card-body">
            <form wire:submit="saveFormSettings">
                {{-- Form Name Badge --}}
                <div class="alert alert-info py-2 px-3 mb-3">
                    <strong>Form:</strong>
                    <span class="badge bg-primary ms-2">{{ $form->name }}</span>
                </div>

                {{-- Dynamic Tabs Layout: Vertical Nav + Content --}}
                <div class="d-flex gap-3 mb-3">
                    {{-- Vertical Navigation (Dynamic) --}}
                    <ul class="nav nav-underline flex-column" role="tablist" style="min-width: 120px;">
                        @foreach($tabs as $tabKey => $tabConfig)
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link @if($activePropertiesTab === $tabKey) active @endif"
                                    type="button"
                                    wire:click="$set('activePropertiesTab', '{{ $tabKey }}')"
                                >
                                    <i class="{{ $tabConfig['icon'] }} me-1"></i> {{ $tabConfig['label'] }}
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Tab Content (Dynamic with Lazy Loading) --}}
                    <div class="flex-fill" wire:key="form-tab-{{ $activePropertiesTab }}">
                        @php
                            $currentTab = $tabs[$activePropertiesTab] ?? null;
                        @endphp

                        @if($currentTab)
                            @if(isset($currentTab['view']) && $currentTab['view'])
                                {{-- If tab has a custom view, include it --}}
                                <div wire:key="form-custom-view-{{ $activePropertiesTab }}">
                                    @include($currentTab['view'])
                                </div>
                            @else
                                {{-- Default content for tabs without custom views --}}
                                <div wire:key="form-default-tab-{{ $activePropertiesTab }}">
                                    @if($activePropertiesTab === 'basic')
                                        {{-- Form Name --}}
                                        <div class="mb-3">
                                            <label for="formName" class="form-label">
                                                Form Name <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                class="form-control @error('formName') is-invalid @enderror"
                                                id="formName"
                                                wire:model="formName"
                                                required
                                            >
                                            @error('formName')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Description --}}
                                        <div class="mb-3">
                                            <label for="formDescription" class="form-label">Description</label>
                                            <textarea
                                                class="form-control @error('formDescription') is-invalid @enderror"
                                                id="formDescription"
                                                rows="3"
                                                wire:model="formDescription"
                                            ></textarea>
                                            @error('formDescription')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Active & Public Toggles --}}
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        role="switch"
                                                        id="formIsActive"
                                                        wire:model="formIsActive"
                                                    >
                                                    <label class="form-check-label" for="formIsActive">
                                                        Active
                                                    </label>
                                                </div>
                                                <small class="text-muted">Allow submissions</small>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        role="switch"
                                                        id="formIsPublic"
                                                        wire:model="formIsPublic"
                                                    >
                                                    <label class="form-check-label" for="formIsPublic">
                                                        Public
                                                    </label>
                                                </div>
                                                <small class="text-muted">No sign-in required</small>
                                            </div>
                                        </div>

                                        {{-- Expiration Date --}}
                                        <div class="mb-3">
                                            <label for="formExpiresAt" class="form-label">Expiration Date</label>
                                            <input
                                                type="datetime-local"
                                                class="form-control @error('formExpiresAt') is-invalid @enderror"
                                                id="formExpiresAt"
                                                wire:model="formExpiresAt"
                                            >
                                            <small class="text-muted">Form will be disabled after this date</small>
                                            @error('formExpiresAt')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Time Limit --}}
                                        <div class="mb-3">
                                            <label for="formTimeLimit" class="form-label">Time Limit (minutes)</label>
                                            <input
                                                type="number"
                                                class="form-control @error('formTimeLimit') is-invalid @enderror"
                                                id="formTimeLimit"
                                                wire:model="formTimeLimit"
                                                min="0"
                                            >
                                            <small class="text-muted">Max completion time (0 = unlimited)</small>
                                            @error('formTimeLimit')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @elseif($activePropertiesTab === 'advanced')
                                        <div class="alert alert-info small">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Advanced form settings will be available here.
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @else
                            <div class="alert alert-warning small mb-0">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Tab "{{ $activePropertiesTab }}" not found.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Save/Cancel Buttons --}}
                @if($settingsOnly)
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('slick-forms.manage.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-floppy me-1"></i> Save Form Settings
                        </button>
                    </div>
                @else
                    <div class="d-grid justify-content-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-floppy me-1"></i> Save Form Settings
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>

{{-- ============================ --}}
{{-- FIELD EDITOR                 --}}
{{-- ============================ --}}
@elseif($showFieldEditor && $selectedField)
    @php
        $fieldTypeRegistry = app(\DigitalisStudios\SlickForms\Services\FieldTypeRegistry::class);
        $tabRegistry = app(\DigitalisStudios\SlickForms\Services\TabRegistry::class);

        $fieldType = $fieldTypeRegistry->get($selectedField->field_type);
        $tabs = $tabRegistry->getFieldTabs($fieldType);
        $schema = $fieldType->getConfigSchema();
    @endphp

    <div class="card">
        <div class="card-header sticky-top d-flex justify-content-between align-items-center" style="background: white; z-index: 10;">
            <h5 class="mb-0">Field Properties</h5>
            <button type="button" class="btn-close" wire:click="closeFieldEditor"></button>
        </div>

        <div class="card-body">
            <form wire:submit="saveField">
                {{-- Field Type Badge --}}
                <div class="alert alert-info py-2 px-3 mb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Type:</strong>
                        <span class="badge bg-primary ms-2">{{ $fieldType->getLabel() }}</span>
                    </div>
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-info"
                        wire:click="$dispatch('scroll-to-field', { fieldId: {{ $selectedField->id }} })"
                        title="Scroll to field in canvas"
                    >
                        <i class="bi bi-eye"></i>
                    </button>
                </div>

                {{-- Dynamic Tabs Layout: Vertical Nav + Content --}}
                <div class="d-flex gap-3 mb-3">
                    {{-- Vertical Navigation (Dynamic) --}}
                    <ul class="nav nav-underline flex-column" role="tablist" style="min-width: 120px;">
                        @foreach($tabs as $tabKey => $tabConfig)
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link @if($activePropertiesTab === $tabKey) active @endif"
                                    type="button"
                                    wire:click="$set('activePropertiesTab', '{{ $tabKey }}')"
                                >
                                    <i class="{{ $tabConfig['icon'] }} me-1"></i> {{ $tabConfig['label'] }}
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Tab Content (Dynamic with Lazy Loading) --}}
                    <div class="flex-fill" wire:key="field-tab-{{ $selectedField->id }}-{{ $activePropertiesTab }}">
                        @php
                            $currentTab = $tabs[$activePropertiesTab] ?? null;
                        @endphp

                        @if($currentTab)
                            @php
                                $schemaRenderer = app(\DigitalisStudios\SlickForms\Services\SchemaRenderer::class);
                                $hasCustomView = isset($currentTab['view']) && $currentTab['view'];
                                $hasSchemaFields = $schemaRenderer->hasFieldsForTab($schema, $activePropertiesTab);
                            @endphp

                            @if($hasCustomView)
                                {{-- If tab has a custom view, render it first --}}
                                <div wire:key="custom-view-{{ $activePropertiesTab }}">
                                    @include($currentTab['view'], [
                                        'schema' => $schema,
                                        'properties' => $properties ?? [],
                                    ])
                                </div>
                            @endif

                            @if($hasSchemaFields)
                                {{-- Auto-generate schema fields for this tab if any exist --}}
                                <div wire:key="schema-tab-{{ $activePropertiesTab }}">
                                    @include('slick-forms::livewire.partials.schema-tab', [
                                        'schema' => $schema,
                                        'tab' => $activePropertiesTab,
                                        'wireModelPrefix' => 'properties',
                                        'type' => 'field',
                                        'currentValues' => $properties ?? [],
                                    ])
                                </div>
                            @elseif(!$hasCustomView)
                                {{-- Only show "no settings" message if there's no custom view AND no schema fields --}}
                                <div class="alert alert-info small mb-0">
                                    <i class="bi bi-info-circle me-1"></i>
                                    No additional settings available for this tab.
                                </div>
                            @endif
                        @else
                            <div class="alert alert-warning small mb-0">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Tab "{{ $activePropertiesTab }}" not found.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Save Button --}}
                <div class="d-grid justify-content-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-floppy me-1"></i> Save Field Properties
                    </button>
                </div>
            </form>
        </div>
    </div>

{{-- Table cells now use standard element editor (removed dedicated cell editor) --}}

{{-- ============================ --}}
{{-- ELEMENT EDITOR               --}}
{{-- ============================ --}}
@elseif($showElementEditor && $selectedElement)
    @php
        $elementRegistry = app(\DigitalisStudios\SlickForms\Services\LayoutElementRegistry::class);
        $tabRegistry = app(\DigitalisStudios\SlickForms\Services\TabRegistry::class);

        if ($elementRegistry->has($selectedElement->element_type)) {
            $elementType = $elementRegistry->get($selectedElement->element_type);
            $tabs = $tabRegistry->getElementTabs($elementType);
            $schema = $elementType->getConfigSchema();
        } else {
            // Fallback for unregistered element types (shouldn't happen)
            $tabs = $tabRegistry->getDefaultElementTabs();
            $schema = [];
        }
    @endphp

    <div class="card">
        <div class="card-header sticky-top d-flex justify-content-between align-items-center" style="background: white; z-index: 10;">
            <h5 class="mb-0">Element Properties</h5>
            <button type="button" class="btn-close" wire:click="closeElementEditor"></button>
        </div>

        <div class="card-body">
            <form wire:submit="saveElement">
                {{-- Element Type Badge --}}
                <div class="alert alert-info py-2 px-3 mb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Type:</strong>
                        <span class="badge bg-primary ms-2">
                            @if($elementRegistry->has($selectedElement->element_type))
                                {{ $elementRegistry->getLabel($selectedElement->element_type) }}
                            @else
                                {{ ucwords(str_replace('_', ' ', $selectedElement->element_type)) }}
                            @endif
                        </span>
                    </div>
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-info"
                        wire:click="$dispatch('scroll-to-element', { elementId: {{ $selectedElement->id }} })"
                        title="Scroll to element in canvas"
                    >
                        <i class="bi bi-eye"></i>
                    </button>
                </div>

                {{-- Dynamic Tabs Layout: Vertical Nav + Content --}}
                <div class="d-flex gap-3 mb-3">
                    {{-- Vertical Navigation (Dynamic) --}}
                    <ul class="nav nav-underline flex-column" role="tablist" style="min-width: 120px;">
                        @foreach($tabs as $tabKey => $tabConfig)
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link @if($activePropertiesTab === $tabKey) active @endif"
                                    type="button"
                                    wire:click="$set('activePropertiesTab', '{{ $tabKey }}')"
                                >
                                    <i class="{{ $tabConfig['icon'] }} me-1"></i> {{ $tabConfig['label'] }}
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Tab Content (Dynamic with Lazy Loading) --}}
                    <div class="flex-fill" wire:key="element-tab-{{ $selectedElement->id }}-{{ $activePropertiesTab }}">
                        @php
                            $currentTab = $tabs[$activePropertiesTab] ?? null;
                        @endphp

                        @if($currentTab)
                            @php
                                $schemaRenderer = app(\DigitalisStudios\SlickForms\Services\SchemaRenderer::class);
                                $hasCustomView = isset($currentTab['view']) && $currentTab['view'];
                                $hasSchemaFields = $schemaRenderer->hasFieldsForTab($schema, $activePropertiesTab);
                            @endphp

                            @if($hasCustomView)
                                {{-- If tab has a custom view, render it first --}}
                                <div wire:key="element-custom-view-{{ $activePropertiesTab }}">
                                    @include($currentTab['view'])
                                </div>
                            @endif

                            @if($hasSchemaFields)
                                {{-- Auto-generate schema fields for this tab if any exist --}}
                                <div wire:key="element-schema-tab-{{ $activePropertiesTab }}">
                                    @include('slick-forms::livewire.partials.schema-tab', [
                                        'schema' => $schema,
                                        'tab' => $activePropertiesTab,
                                        'wireModelPrefix' => 'elementProperties',
                                        'type' => 'element',
                                        'currentValues' => $elementProperties ?? [],
                                    ])
                                </div>
                            @elseif(!$hasCustomView)
                                {{-- Only show "no settings" message if there's no custom view AND no schema fields --}}
                                <div class="alert alert-info small mb-0">
                                    <i class="bi bi-info-circle me-1"></i>
                                    No additional settings available for this tab.
                                </div>
                            @endif
                        @else
                            <div class="alert alert-warning small mb-0">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Tab "{{ $activePropertiesTab }}" not found.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Save Button --}}
                <div class="d-grid justify-content-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-floppy me-1"></i> Save Element Properties
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
