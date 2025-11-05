{{--
    Email Settings Tab

    Form-level email notification configuration for admins and users.
    Shown when form (not field/element) is selected in builder.
--}}

<div class="email-settings">
    {{-- Enable Email Notifications --}}
    <div class="mb-4">
        <div class="form-check form-switch">
            <input
                class="form-check-input"
                type="checkbox"
                id="emailEnabled"
                wire:model.live="emailEnabled"
            >
            <label class="form-check-label fw-bold" for="emailEnabled">
                Enable Email Notifications
            </label>
        </div>
        <small class="text-muted d-block mt-1">
            Send automated emails when this form is submitted
        </small>
    </div>

    @if($emailEnabled)
        {{-- Admin Notifications Section --}}
        <div class="card mb-4">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bi bi-person-badge me-2"></i>Admin Notifications
                    </h6>
                    <button
                        type="button"
                        class="btn btn-sm btn-primary"
                        wire:click="addAdminTemplate"
                    >
                        <i class="bi bi-plus-circle me-1"></i>Add Template
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if(empty($adminEmailTemplates))
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        No admin email templates configured. Click "Add Template" to create one.
                    </div>
                @else
                    {{-- List of Admin Templates --}}
                    @foreach($adminEmailTemplates as $index => $template)
                        <div class="border rounded p-3 mb-3" wire:key="admin-template-{{ $index }}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="badge bg-primary me-2">Priority: {{ $template['priority'] ?? 1 }}</span>
                                    @if(!($template['enabled'] ?? true))
                                        <span class="badge bg-secondary">Disabled</span>
                                    @endif
                                </div>
                                <div>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary me-1"
                                        wire:click="editAdminTemplate({{ $index }})"
                                    >
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        wire:click="deleteAdminTemplate({{ $index }})"
                                        wire:confirm="Are you sure you want to delete this email template?"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="small">
                                <strong>Recipients:</strong>
                                @if(is_array($template['recipients'] ?? null))
                                    {{ implode(', ', $template['recipients']) }}
                                @else
                                    {{ $template['recipients'] ?? 'Not set' }}
                                @endif
                            </div>

                            <div class="small">
                                <strong>Subject:</strong> {{ $template['subject'] ?? 'Not set' }}
                            </div>

                            @if($template['attach_pdf'] ?? false)
                                <div class="small">
                                    <i class="bi bi-paperclip text-primary"></i> PDF attachment enabled
                                </div>
                            @endif

                            @if(!empty($template['conditional_rules']))
                                <div class="small text-muted">
                                    <i class="bi bi-filter"></i> Conditional rules: {{ count($template['conditional_rules']) }} rule(s)
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- User Confirmation Section --}}
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="bi bi-person-check me-2"></i>User Confirmation
                </h6>
            </div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="userConfirmationEnabled"
                        wire:model.live="userConfirmationEnabled"
                    >
                    <label class="form-check-label" for="userConfirmationEnabled">
                        Send confirmation email to user
                    </label>
                </div>

                @if($userConfirmationEnabled)
                    {{-- Email Field Selector --}}
                    <div class="mb-3">
                        <label for="userEmailFieldId" class="form-label">
                            Email Field <span class="text-danger">*</span>
                        </label>
                        <select
                            class="form-select"
                            id="userEmailFieldId"
                            wire:model="userEmailFieldId"
                        >
                            <option value="">Select email field...</option>
                            @foreach($this->getEmailFields() as $field)
                                <option value="{{ $field->id }}">{{ $field->label ?: $field->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            Select which field contains the user's email address
                        </small>
                    </div>

                    {{-- Subject Line --}}
                    <div class="mb-3">
                        <label for="userConfirmationSubject" class="form-label">
                            Email Subject
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="userConfirmationSubject"
                            wire:model="userConfirmationSubject"
                            placeholder="Thank you for your submission"
                        >
                        <small class="text-muted">
                            Available variables: @{{form.name}}, @{{submission.id}}
                        </small>
                    </div>

                    {{-- Template Editor Button --}}
                    <button
                        type="button"
                        class="btn btn-outline-primary btn-sm"
                        wire:click="editUserConfirmationTemplate"
                    >
                        <i class="bi bi-pencil me-1"></i>Edit Email Template
                    </button>
                @endif
            </div>
        </div>

        {{-- Email Logs Button --}}
        <div class="d-grid">
            <button
                type="button"
                class="btn btn-outline-secondary"
                wire:click="showEmailLogs"
            >
                <i class="bi bi-list-ul me-1"></i>View Email Logs
            </button>
        </div>
    @endif
</div>
