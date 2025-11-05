{{--
    Model Binding Settings Tab

    Form-level model binding configuration for auto-population and saving.
    Shown when form (not field/element) is selected in builder.
--}}

<div class="model-settings">
    {{-- Enable Model Binding --}}
    <div class="mb-4">
        <div class="form-check form-switch">
            <input
                class="form-check-input"
                type="checkbox"
                id="modelBindingEnabled"
                wire:model.live="modelBindingEnabled"
            >
            <label class="form-check-label fw-bold" for="modelBindingEnabled">
                Enable Model Binding
            </label>
        </div>
        <small class="text-muted d-block mt-1">
            Bind this form to an Eloquent model for auto-population and saving
        </small>
    </div>

    @if($modelBindingEnabled)
        {{-- Basic Configuration Card --}}
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="bi bi-gear me-2"></i>Basic Configuration
                </h6>
            </div>
            <div class="card-body">
                {{-- Model Class --}}
                <div class="mb-3">
                    <label for="modelClass" class="form-label">
                        Model Class <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        class="form-control"
                        id="modelClass"
                        wire:model.live="modelClass"
                        placeholder="App\Models\User"
                    >
                    <small class="text-muted">
                        Fully qualified model class name (e.g., App\Models\User)
                    </small>
                    @error('modelClass')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Model Class (Searchable) UI --}}
                <div class="mb-3">
                    <label for="modelClassSelect" class="form-label">
                        Model Class (Searchable)
                    </label>
                    <div wire:ignore>
                        <select id="modelClassSelect" class="form-select" x-data x-init="
                            $nextTick(() => {
                              setTimeout(() => {
                                const el = document.getElementById('modelClassSelect');
                                const legacy = document.getElementById('modelClass');
                                if (legacy) { legacy.style.display = 'none'; }
                                if (typeof TomSelect !== 'undefined' && el) {
                                  const ts = new TomSelect(el, {
                                    allowEmptyOption: true,
                                    placeholder: 'Search models...',
                                    maxOptions: 5000,
                                    create: false,
                                    sortField: { field: 'text', direction: 'asc' }
                                  });
                                  ts.on('change', (val) => { $wire.call('onModelSelected', val); });
                                  ts.setValue(@js($modelClass ?? ''));
                                }
                              }, 0);
                            })
                        ">
                            <option value="">Select a model...</option>
                            @foreach($this->getAvailableModels() as $class)
                                @php
                                    $display = \Illuminate\Support\Str::startsWith($class, 'App\\Models\\')
                                        ? \Illuminate\Support\Str::after($class, 'App\\Models\\')
                                        : $class;
                                @endphp
                                <option value="{{ $class }}">{{ $display }}</option>
                            @endforeach
                        </select>
                    </div>
                    <small class="text-muted">
                        Fully qualified model class name (e.g., App\\Models\\User)
                    </small>
                </div>

                {{-- Route Parameter --}}
                <div class="mb-3">
                    <label for="routeParameter" class="form-label">
                        Route Parameter
                    </label>
                    <input
                        type="text"
                        class="form-control"
                        id="routeParameter"
                        wire:model="routeParameter"
                        placeholder="model"
                    >
                    <small class="text-muted">
                        Name of the route parameter (default: "model"). Example: /form/{{'{'}}form{{'}'}}/{{'{'}}model{{'}'}}
                    </small>
                </div>

                {{-- Route Key --}}
                <div class="mb-3">
                    <label for="routeKey" class="form-label">
                        Route Key
                    </label>
                    <select
                        class="form-select"
                        id="routeKey"
                        wire:model="routeKey"
                    >
                        <option value="id">ID</option>
                        <option value="uuid">UUID</option>
                        <option value="slug">Slug</option>
                        <option value="email">Email</option>
                    </select>
                    <small class="text-muted">
                        Model attribute to use for route binding (e.g., "id", "uuid", "slug")
                    </small>
                </div>

                {{-- Allow Create / Update --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="allowCreate"
                                wire:model="allowCreate"
                            >
                            <label class="form-check-label" for="allowCreate">
                                Allow Create
                            </label>
                        </div>
                        <small class="text-muted d-block">
                            Form can create new model instances
                        </small>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="allowUpdate"
                                wire:model="allowUpdate"
                            >
                            <label class="form-check-label" for="allowUpdate">
                                Allow Update
                            </label>
                        </div>
                        <small class="text-muted d-block">
                            Form can update existing models
                        </small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Field Mappings Card --}}
        <div class="card mb-4">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bi bi-arrow-left-right me-2"></i>Field Mappings
                    </h6>
                    <button
                        type="button"
                        class="btn btn-sm btn-primary"
                        wire:click="addFieldMapping"
                    >
                        <i class="bi bi-plus-circle me-1"></i>Add Mapping
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if(empty($fieldMappings))
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        No field mappings configured. Click "Add Mapping" to map form fields to model attributes.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Form Field</th>
                                    <th>Model Attribute</th>
                                    <th style="width: 80px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fieldMappings as $index => $mapping)
                                    <tr wire:key="mapping-{{ $index }}">
                                        <td>
                                            <select
                                                class="form-select form-select-sm"
                                                wire:model="fieldMappings.{{ $index }}.form_field"
                                            >
                                                <option value="">Select field...</option>
                                                @foreach($this->getFormFields() as $field)
                                                    <option value="{{ $field->name }}">
                                                        {{ $field->label ?: $field->name }} ({{ $field->name }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input
                                                type="text"
                                                class="form-control form-control-sm"
                                                wire:model="fieldMappings.{{ $index }}.model_attribute"
                                                placeholder="e.g., name or address.city"
                                            >
                                        </td>
                                        <td>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                wire:click="removeFieldMapping({{ $index }})"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted">
                        <i class="bi bi-lightbulb me-1"></i>
                        <strong>Tip:</strong> Use dot notation for nested attributes (e.g., "address.city", "profile.bio")
                    </small>
                @endif
            </div>
        </div>

        {{-- Relationship Mappings Card --}}
        <div class="card mb-4">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bi bi-diagram-3 me-2"></i>Relationship Mappings
                    </h6>
                    <button
                        type="button"
                        class="btn btn-sm btn-primary"
                        wire:click="addRelationshipMapping"
                    >
                        <i class="bi bi-plus-circle me-1"></i>Add Mapping
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if(empty($relationshipMappings))
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        No relationship mappings configured. For many-to-many relationships (e.g., roles, tags).
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Form Field</th>
                                    <th>Relationship Name</th>
                                    <th style="width: 80px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($relationshipMappings as $index => $mapping)
                                    <tr wire:key="rel-mapping-{{ $index }}">
                                        <td>
                                            <select
                                                class="form-select form-select-sm"
                                                wire:model="relationshipMappings.{{ $index }}.form_field"
                                            >
                                                <option value="">Select field...</option>
                                                @foreach($this->getFormFields() as $field)
                                                    <option value="{{ $field->name }}">
                                                        {{ $field->label ?: $field->name }} ({{ $field->name }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input
                                                type="text"
                                                class="form-control form-control-sm"
                                                wire:model="relationshipMappings.{{ $index }}.relationship_name"
                                                placeholder="e.g., roles, tags"
                                            >
                                        </td>
                                        <td>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                wire:click="removeRelationshipMapping({{ $index }})"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted">
                        <i class="bi bi-lightbulb me-1"></i>
                        <strong>Tip:</strong> For BelongsToMany relationships, the system will use sync() automatically
                    </small>
                @endif
            </div>
        </div>

        {{-- Save Button --}}
        <div class="d-grid">
            <button
                type="button"
                class="btn btn-primary"
                wire:click="saveModelBinding"
            >
                <i class="bi bi-save me-1"></i>Save Model Binding Configuration
            </button>
        </div>
    @endif
</div>
