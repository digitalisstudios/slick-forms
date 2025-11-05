{{-- SECTION: Advanced --}}
<div class="mb-4" wire:key="field-advanced-{{ $selectedField?->id ?? 'none' }}">
    <h6 class="text-uppercase text-muted small fw-bold mb-3">
        <i class="bi bi-gear me-1"></i> Advanced
    </h6>
            
    {{-- Display Utilities --}}
    <div class="mb-3">
        <label class="form-label small fw-bold">Display (Responsive Visibility)</label>
        <div class="row g-2">
            <div class="col-4">
                <label for="fieldDisplay_xs" class="form-label small">XS</label>
                <select class="form-select form-select-sm" id="fieldDisplay_xs" wire:model="properties.display.display">
                    <option value="">Default</option>
                    <option value="none">Hide</option>
                    <option value="block">Block</option>
                    <option value="inline">Inline</option>
                    <option value="inline-block">Inline Block</option>
                    <option value="flex">Flex</option>
                </select>
            </div>
            <div class="col-4">
                <label for="fieldDisplay_sm" class="form-label small">SM</label>
                <select class="form-select form-select-sm" id="fieldDisplay_sm" wire:model="properties.display.display_sm">
                    <option value="">Default</option>
                    <option value="none">Hide</option>
                    <option value="block">Block</option>
                    <option value="inline">Inline</option>
                    <option value="inline-block">Inline Block</option>
                    <option value="flex">Flex</option>
                </select>
            </div>
            <div class="col-4">
                <label for="fieldDisplay_md" class="form-label small">MD</label>
                <select class="form-select form-select-sm" id="fieldDisplay_md" wire:model="properties.display.display_md">
                    <option value="">Default</option>
                    <option value="none">Hide</option>
                    <option value="block">Block</option>
                    <option value="inline">Inline</option>
                    <option value="inline-block">Inline Block</option>
                    <option value="flex">Flex</option>
                </select>
            </div>
        </div>
        <div class="form-text small mt-2">Control visibility at different screen sizes</div>
    </div>
            
    <hr class="my-3">
            
    <h6 class="text-uppercase text-muted small fw-bold mb-3">
        <i class="bi bi-diagram-3 me-1"></i> Conditional Logic
    </h6>
            
        {{-- Visibility Conditions --}}
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="form-label mb-0"><strong>Visibility Conditions</strong></label>
                <div class="btn-group btn-group-sm" role="group">
                    @if(isset($fieldConditionalLogic['rule_groups']))
                        {{-- Advanced Mode: Show Add Group button --}}
                        <button
                            type="button"
                            class="btn btn-outline-primary"
                            wire:click.stop="addRuleGroup"
                        >
                            <i class="bi bi-plus-circle me-1"></i> Add Group
                        </button>
                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            wire:click.stop="switchToSimpleMode"
                            title="Switch to simple mode"
                        >
                            <i class="bi bi-arrow-left-right"></i> Simple
                        </button>
                    @else
                        {{-- Simple Mode: Show Add Condition button --}}
                        <button
                            type="button"
                            class="btn btn-outline-primary"
                            wire:click.stop="addCondition"
                        >
                            <i class="bi bi-plus-circle me-1"></i> Add Condition
                        </button>
                        @if(isset($fieldConditionalLogic['conditions']) && count($fieldConditionalLogic['conditions']) > 1)
                            <button
                                type="button"
                                class="btn btn-outline-secondary"
                                wire:click.stop="switchToAdvancedMode"
                                title="Switch to advanced mode with rule groups"
                            >
                                <i class="bi bi-arrow-left-right"></i> Advanced
                            </button>
                        @endif
                    @endif
                </div>
            </div>
            
            {{-- Advanced Mode: Rule Groups --}}
            @if(isset($fieldConditionalLogic['rule_groups']) && count($fieldConditionalLogic['rule_groups']) > 0)
                <div class="mb-2">
                    <label class="form-label small">Action</label>
                    <select class="form-select form-select-sm" wire:model="fieldConditionalLogic.action">
                        <option value="show">Show this field if...</option>
                        <option value="hide">Hide this field if...</option>
                    </select>
                </div>
            
                @if(count($fieldConditionalLogic['rule_groups']) > 1)
                    <div class="mb-2">
                        <label class="form-label small">Groups Match</label>
                        <select class="form-select form-select-sm" wire:model="fieldConditionalLogic.groups_match">
                            <option value="all">All groups match (AND)</option>
                            <option value="any">Any group matches (OR)</option>
                        </select>
                    </div>
                @endif
            
                @foreach($fieldConditionalLogic['rule_groups'] as $groupIndex => $group)
                    <div class="card border-primary mb-3" wire:key="group-{{ $groupIndex }}">
                        <div class="card-header bg-primary/10 p-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong class="small">Group {{ $groupIndex + 1 }}</strong>
                                <div class="btn-group btn-group-sm">
                                    <button
                                        type="button"
                                        class="btn btn-outline-primary btn-sm"
                                        wire:click.stop="addConditionToGroup({{ $groupIndex }})"
                                    >
                                        <i class="bi bi-plus-circle"></i>
                                    </button>
                                    @if(count($fieldConditionalLogic['rule_groups']) > 1)
                                        <button
                                            type="button"
                                            class="btn btn-outline-danger btn-sm"
                                            wire:click.stop="removeRuleGroup({{ $groupIndex }})"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            @if(count($group['conditions']) > 1)
                                <div class="mb-2">
                                    <label class="form-label small">Conditions Match</label>
                                    <select class="form-select form-select-sm" wire:model="fieldConditionalLogic.rule_groups.{{ $groupIndex }}.match">
                                        <option value="all">All conditions (AND)</option>
                                        <option value="any">Any condition (OR)</option>
                                    </select>
                                </div>
                            @endif
            
                            @foreach($group['conditions'] as $conditionIndex => $condition)
                                <div class="card mb-2 border-secondary" wire:key="group-{{ $groupIndex }}-condition-{{ $conditionIndex }}">
                                    <div class="card-body p-2">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <strong class="small text-muted">Condition {{ $conditionIndex + 1 }}</strong>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                wire:click.stop="removeConditionFromGroup({{ $groupIndex }}, {{ $conditionIndex }})"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
            
                                        <div class="mb-2">
                                            <label class="form-label small">Target Field</label>
                                            <div class="input-group input-group-sm">
                                                <select
                                                    class="form-select form-select-sm"
                                                    wire:model="fieldConditionalLogic.rule_groups.{{ $groupIndex }}.conditions.{{ $conditionIndex }}.target_field_id"
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
                                                    wire:click="activatePicker('group_{{ $groupIndex }}_condition_{{ $conditionIndex }}')"
                                                    title="Pick field from canvas"
                                                >
                                                    <i class="bi bi-eyedropper"></i>
                                                </button>
                                            </div>
                                        </div>
            
                                        <div class="mb-2">
                                            <label class="form-label small">Operator</label>
                                            @php
                                                $targetFieldId = $condition['target_field_id'] ?? null;
                                                $availableOperators = $this->getOperatorsForTargetField($targetFieldId);
                                            @endphp
                                            <select
                                                class="form-select form-select-sm"
                                                wire:model="fieldConditionalLogic.rule_groups.{{ $groupIndex }}.conditions.{{ $conditionIndex }}.operator"
                                            >
                                                @foreach($availableOperators as $operatorValue => $operatorLabel)
                                                    <option value="{{ $operatorValue }}">{{ $operatorLabel }}</option>
                                                @endforeach
                                            </select>
                                        </div>
            
                                        @if(!in_array($condition['operator'] ?? 'equals', ['is_empty', 'is_not_empty', 'checked', 'unchecked']))
                                            <div>
                                                <label class="form-label small">Value</label>
                                                @php
                                                    $targetFieldId = $condition['target_field_id'] ?? null;
                                                    $targetFieldOptions = $this->getTargetFieldOptions($targetFieldId);
                                                    $currentOperator = $condition['operator'] ?? 'equals';
                                                    $isMultiValueOperator = in_array($currentOperator, ['in', 'not_in']);
                                                @endphp
            
                                                @if($targetFieldOptions)
                                                    @if($isMultiValueOperator)
                                                        <select
                                                            class="form-select form-select-sm"
                                                            wire:model="fieldConditionalLogic.rule_groups.{{ $groupIndex }}.conditions.{{ $conditionIndex }}.value"
                                                            multiple
                                                            size="3"
                                                        >
                                                            @foreach($targetFieldOptions as $option)
                                                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="form-text">Hold Ctrl/Cmd to select multiple</div>
                                                    @else
                                                        <select
                                                            class="form-select form-select-sm"
                                                            wire:model="fieldConditionalLogic.rule_groups.{{ $groupIndex }}.conditions.{{ $conditionIndex }}.value"
                                                        >
                                                            <option value="">-- Select Value --</option>
                                                            @foreach($targetFieldOptions as $option)
                                                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                                            @endforeach
                                                        </select>
                                                    @endif
                                                @else
                                                    <input
                                                        type="text"
                                                        class="form-control form-control-sm"
                                                        wire:model="fieldConditionalLogic.rule_groups.{{ $groupIndex }}.conditions.{{ $conditionIndex }}.value"
                                                        placeholder="{{ $isMultiValueOperator ? 'Enter comma-separated values' : 'Enter value' }}"
                                                    >
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            
            {{-- Simple Mode: Flat Conditions --}}
            @elseif(isset($fieldConditionalLogic['conditions']) && count($fieldConditionalLogic['conditions']) > 0)
                <div class="mb-2">
                    <label class="form-label small">Action</label>
                    <select class="form-select form-select-sm" wire:model="fieldConditionalLogic.action">
                        <option value="show">Show this field if...</option>
                        <option value="hide">Hide this field if...</option>
                    </select>
                </div>
            
                <div class="mb-2">
                    <label class="form-label small">Match</label>
                    <select class="form-select form-select-sm" wire:model="fieldConditionalLogic.match">
                        <option value="all">All conditions match (AND)</option>
                        <option value="any">Any condition matches (OR)</option>
                    </select>
                </div>
            
                @foreach($fieldConditionalLogic['conditions'] as $index => $condition)
                    <div class="card mb-2" wire:key="condition-{{ $index }}">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <strong class="small text-muted">Condition {{ $index + 1 }}</strong>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    wire:click.stop="removeCondition({{ $index }})"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
            
                            <div class="mb-2">
                                <label class="form-label small">Target Field</label>
                                <div class="input-group input-group-sm">
                                    <select
                                        class="form-select form-select-sm"
                                        wire:model="fieldConditionalLogic.conditions.{{ $index }}.target_field_id"
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
                                        wire:click="activatePicker('condition_{{ $index }}')"
                                        title="Pick field from canvas"
                                    >
                                        <i class="bi bi-eyedropper"></i>
                                    </button>
                                </div>
                            </div>
            
                            <div class="mb-2">
                                <label class="form-label small">Operator</label>
                                @php
                                    $targetFieldId = $condition['target_field_id'] ?? null;
                                    $availableOperators = $this->getOperatorsForTargetField($targetFieldId);
                                @endphp
                                <select
                                    class="form-select form-select-sm"
                                    wire:model="fieldConditionalLogic.conditions.{{ $index }}.operator"
                                >
                                    @foreach($availableOperators as $operatorValue => $operatorLabel)
                                        <option value="{{ $operatorValue }}">{{ $operatorLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
            
                            @if(!in_array($condition['operator'] ?? 'equals', ['is_empty', 'is_not_empty', 'checked', 'unchecked']))
                                <div>
                                    <label class="form-label small">Value</label>
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
                                                wire:model="fieldConditionalLogic.conditions.{{ $index }}.value"
                                                multiple
                                                size="3"
                                            >
                                                @foreach($targetFieldOptions as $option)
                                                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                                @endforeach
                                            </select>
                                            <div class="form-text">Hold Ctrl/Cmd to select multiple</div>
                                        @else
                                            {{-- Single select for equals/not_equals --}}
                                            <select
                                                class="form-select form-select-sm"
                                                wire:model="fieldConditionalLogic.conditions.{{ $index }}.value"
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
                                            wire:model="fieldConditionalLogic.conditions.{{ $index }}.value"
                                            placeholder="{{ $isMultiValueOperator ? 'Enter comma-separated values' : 'Enter value' }}"
                                        >
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="alert alert-info small mb-0">
                    No conditional logic set. This field will always be visible. Click "Add Condition" to create rules.
                </div>
            @endif
        </div>
    </div>

    {{-- SECTION: Schema-Driven Properties --}}
    {{-- Render any additional properties defined in schema for this tab --}}
    @php
        $schemaRenderer = app(\DigitalisStudios\SlickForms\Services\SchemaRenderer::class);
    @endphp

    @if($schemaRenderer->hasFieldsForTab($schema, 'advanced'))
        <div class="mb-4">
            <h6 class="text-uppercase text-muted small fw-bold mb-3">
                <i class="bi bi-gear me-1"></i> Additional Advanced Properties
            </h6>

            @foreach($schema as $fieldKey => $fieldConfig)
                @if(($fieldConfig['tab'] ?? 'basic') === 'advanced' && ($fieldConfig['type'] ?? 'text') !== 'custom')
                    @php
                        $currentValue = $properties[$fieldKey] ?? ($fieldConfig['default'] ?? null);
                        $fieldType = $fieldConfig['type'] ?? 'text';
                    @endphp

                    @include('slick-forms::livewire.partials.schema-field', [
                        'fieldKey' => $fieldKey,
                        'fieldConfig' => $fieldConfig,
                        'wireModelPrefix' => 'properties',
                        'currentValue' => $currentValue,
                    ])
                @endif
            @endforeach
        </div>
    @endif

