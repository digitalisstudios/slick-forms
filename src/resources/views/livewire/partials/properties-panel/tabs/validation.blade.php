{{-- SECTION: Validation --}}
<div class="mb-4">
    {{-- Validation Section (not for content fields) --}}
    @if($selectedField && !in_array($selectedField->field_type, ['header', 'paragraph', 'code', 'image', 'video']))
    <h6 class="text-uppercase text-muted small fw-bold mb-3">
        <i class="bi bi-check-circle me-1"></i> Validation
    </h6>
            
    <div class="mb-3 form-check">
        <input
            type="checkbox"
            class="form-check-input"
            id="fieldIsRequired"
            wire:model="fieldIsRequired"
        >
        <label class="form-check-label" for="fieldIsRequired">
            <strong>Required Field</strong>
        </label>
        <div class="form-text">User must provide a value</div>
    </div>
    @endif
            
    {{-- Field-specific Validation Options --}}
    @if($selectedField)
        @php
            $fieldType = $registry->get($selectedField->field_type);
            $availableValidationOptions = $fieldType->getAvailableValidationOptions();
        @endphp
            
        @if(count($availableValidationOptions) > 0)
            @foreach($availableValidationOptions as $optionKey => $optionConfig)
                <div class="mb-3">
                    <label class="form-label small">{{ $optionConfig['label'] }}</label>
            
                    @if($optionConfig['type'] === 'checkbox')
                        <div class="form-check">
                            <input
                                type="checkbox"
                                class="form-check-input"
                                id="validation_{{ $optionKey }}"
                                wire:model="fieldValidationOptions.{{ $optionKey }}"
                            >
                            <label class="form-check-label small" for="validation_{{ $optionKey }}">
                                Enable
                            </label>
                        </div>
                    @elseif($optionConfig['type'] === 'number')
                        <input
                            type="number"
                            class="form-control form-control-sm"
                            id="validation_{{ $optionKey }}"
                            wire:model="fieldValidationOptions.{{ $optionKey }}"
                            placeholder="{{ $optionConfig['placeholder'] ?? '' }}"
                        >
                    @elseif($optionConfig['type'] === 'date')
                        <input
                            type="date"
                            class="form-control form-control-sm"
                            id="validation_{{ $optionKey }}"
                            wire:model="fieldValidationOptions.{{ $optionKey }}"
                        >
                    @else
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            id="validation_{{ $optionKey }}"
                            wire:model="fieldValidationOptions.{{ $optionKey }}"
                            placeholder="{{ $optionConfig['placeholder'] ?? '' }}"
                        >
                    @endif
            
                    @if(isset($optionConfig['help']))
                        <div class="form-text small">{{ $optionConfig['help'] }}</div>
                    @endif
                </div>
            @endforeach
        @endif
    @endif
            
    {{-- Conditional Validation Section --}}
    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label mb-0">Conditional Validation</label>
            <button
                type="button"
                class="btn btn-sm btn-outline-success"
                wire:click.stop="addConditionalValidation"
                onclick="event.stopPropagation()"
            >
                <i class="bi bi-plus-circle me-1"></i> Add Rule
            </button>
        </div>
        <div class="small text-muted mb-2">Apply validation rules when specific conditions are met</div>
            
        @if(isset($fieldConditionalLogic['conditional_validation']) && count($fieldConditionalLogic['conditional_validation']) > 0)
            @foreach($fieldConditionalLogic['conditional_validation'] as $valIndex => $validationRule)
                <div class="card mb-3 border-success" wire:key="validation-rule-{{ $valIndex }}">
                    <div class="card-header bg-success bg-opacity-10 d-flex justify-content-between align-items-center">
                        <strong class="small">Validation Rule {{ $valIndex + 1 }}</strong>
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-danger"
                            wire:click.stop="removeConditionalValidation({{ $valIndex }})"
                        >
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <div class="card-body p-2">
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Validation Rule</label>
                            <select
                                class="form-select form-select-sm"
                                wire:model="fieldConditionalLogic.conditional_validation.{{ $valIndex }}.rule"
                            >
                                <option value="required">Required</option>
                                <option value="email">Must be valid email</option>
                                <option value="numeric">Must be numeric</option>
                                <option value="min:1">Min value: 1</option>
                                <option value="min:5">Min value: 5</option>
                                <option value="min:10">Min value: 10</option>
                                <option value="min:18">Min value: 18</option>
                                <option value="max:10">Max value: 10</option>
                                <option value="max:100">Max value: 100</option>
                                <option value="max:1000">Max value: 1000</option>
                                <option value="min:3">Min length: 3 characters</option>
                                <option value="min:6">Min length: 6 characters</option>
                                <option value="max:50">Max length: 50 characters</option>
                                <option value="max:255">Max length: 255 characters</option>
                            </select>
                        </div>
            
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Apply when</label>
                            <select
                                class="form-select form-select-sm"
                                wire:model="fieldConditionalLogic.conditional_validation.{{ $valIndex }}.match"
                            >
                                <option value="all">All conditions match (AND)</option>
                                <option value="any">Any condition matches (OR)</option>
                            </select>
                        </div>
            
                        <div class="mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small fw-bold mb-0">Conditions</label>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary btn-xs"
                                    wire:click.stop="addConditionalValidationCondition({{ $valIndex }})"
                                >
                                    <i class="bi bi-plus"></i> Add Condition
                                </button>
                            </div>
            
                            @foreach($validationRule['conditions'] ?? [] as $condIndex => $condition)
                                <div class="card mb-1" wire:key="validation-{{ $valIndex }}-condition-{{ $condIndex }}">
                                    <div class="card-body p-2">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <span class="small text-muted">Condition {{ $condIndex + 1 }}</span>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger btn-xs"
                                                wire:click.stop="removeConditionalValidationCondition({{ $valIndex }}, {{ $condIndex }})"
                                            >
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
            
                                        <div class="mb-1">
                                            <div class="input-group input-group-sm">
                                                <select
                                                    class="form-select form-select-sm"
                                                    wire:model="fieldConditionalLogic.conditional_validation.{{ $valIndex }}.conditions.{{ $condIndex }}.target_field_id"
                                                >
                                                    <option value="">-- Select Field --</option>
                                                    @foreach($fieldsGroupedByContainer as $containerLabel => $fields)
                                                        <optgroup label="{{ $containerLabel }}">
                                                            @foreach($fields as $field)
                                                                @if(!$selectedField || $field->id !== $selectedField->id)
                                                                    <option value="{{ $field->id }}">{{ $field->label }} ({{ $field->element_id }})</option>
                                                                @endif
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                </select>
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-secondary"
                                                    wire:click="activatePicker('validation_{{ $valIndex }}_condition_{{ $condIndex }}')"
                                                    title="Pick field from canvas"
                                                >
                                                    <i class="bi bi-eyedropper"></i>
                                                </button>
                                            </div>
                                        </div>
            
                                        <div class="mb-1">
                                            @php
                                                $targetFieldId = $condition['target_field_id'] ?? null;
                                                $availableOperators = $this->getOperatorsForTargetField($targetFieldId);
                                            @endphp
                                            <select
                                                class="form-select form-select-sm"
                                                wire:model="fieldConditionalLogic.conditional_validation.{{ $valIndex }}.conditions.{{ $condIndex }}.operator"
                                            >
                                                @foreach($availableOperators as $operatorValue => $operatorLabel)
                                                    <option value="{{ $operatorValue }}">{{ $operatorLabel }}</option>
                                                @endforeach
                                            </select>
                                        </div>
            
                                        @if(!in_array($condition['operator'] ?? 'equals', ['is_empty', 'is_not_empty', 'checked', 'unchecked']))
                                            <div>
                                                @php
                                                    $targetFieldId = $condition['target_field_id'] ?? null;
                                                    $targetFieldOptions = $this->getTargetFieldOptions($targetFieldId);
                                                    $currentOperator = $condition['operator'] ?? 'equals';
                                                    $isMultiValueOperator = in_array($currentOperator, ['in', 'not_in']);
                                                @endphp
            
                                                @if($targetFieldOptions)
                                                    @if($isMultiValueOperator)
                                                        {{-- Multi-select for in/not_in operators --}}
                                                        <select
                                                            class="form-select form-select-sm"
                                                            wire:model="fieldConditionalLogic.conditional_validation.{{ $valIndex }}.conditions.{{ $condIndex }}.value"
                                                            multiple
                                                            size="3"
                                                        >
                                                            @foreach($targetFieldOptions as $option)
                                                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="form-text small">Hold Ctrl/Cmd to select multiple</div>
                                                    @else
                                                        {{-- Single select for equals/not_equals --}}
                                                        <select
                                                            class="form-select form-select-sm"
                                                            wire:model="fieldConditionalLogic.conditional_validation.{{ $valIndex }}.conditions.{{ $condIndex }}.value"
                                                        >
                                                            <option value="">-- Select Value --</option>
                                                            @foreach($targetFieldOptions as $option)
                                                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                                            @endforeach
                                                        </select>
                                                    @endif
                                                @else
                                                    {{-- Text input for fields without predefined options --}}
                                                    <input
                                                        type="text"
                                                        class="form-control form-control-sm"
                                                        wire:model="fieldConditionalLogic.conditional_validation.{{ $valIndex }}.conditions.{{ $condIndex }}.value"
                                                        placeholder="{{ $isMultiValueOperator ? 'Enter comma-separated values' : 'Value' }}"
                                                    >
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="alert alert-info small mb-0">
                No conditional validation rules. Base validation rules will apply. Click "Add Rule" to add conditional requirements.
            </div>
        @endif
    </div>
</div>