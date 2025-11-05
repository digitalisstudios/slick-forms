{{--
    Webhook Settings Tab

    Form-level webhook configuration for external integrations.
    Shown when form (not field/element) is selected in builder.
--}}

<div class="webhook-settings">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h6 class="mb-1">Webhook Integrations</h6>
            <small class="text-muted">
                Send form submissions to external APIs automatically
            </small>
        </div>
        <button
            type="button"
            class="btn btn-sm btn-primary"
            wire:click="addWebhook"
        >
            <i class="bi bi-plus-circle me-1"></i>Add Webhook
        </button>
    </div>

    {{-- Webhooks List --}}
    @if(empty($webhooks))
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-1"></i>
            No webhooks configured. Click "Add Webhook" to create your first integration.
        </div>
    @else
        <div class="webhooks-list">
            @foreach($webhooks as $webhook)
                <div class="card mb-3" wire:key="webhook-{{ $webhook['id'] }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    {{ $webhook['name'] }}
                                    @if(!$webhook['enabled'])
                                        <span class="badge bg-secondary ms-2">Disabled</span>
                                    @else
                                        <span class="badge bg-success ms-2">Enabled</span>
                                    @endif
                                </h6>
                                <small class="text-muted">
                                    <i class="bi bi-link-45deg"></i>
                                    {{ $webhook['url'] }}
                                </small>
                            </div>
                            <div class="btn-group" role="group">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    wire:click="testWebhook({{ $webhook['id'] }})"
                                    title="Test webhook"
                                >
                                    <i class="bi bi-play-circle"></i>
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-secondary"
                                    wire:click="editWebhook({{ $webhook['id'] }})"
                                    title="Edit webhook"
                                >
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    wire:click="deleteWebhook({{ $webhook['id'] }})"
                                    wire:confirm="Are you sure you want to delete this webhook?"
                                    title="Delete webhook"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="row g-2 mt-2">
                            <div class="col-auto">
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-arrow-up-right"></i> {{ $webhook['method'] }}
                                </span>
                            </div>
                            <div class="col-auto">
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-file-earmark-code"></i> {{ strtoupper($webhook['format']) }}
                                </span>
                            </div>
                            <div class="col-auto">
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-arrow-repeat"></i> Max Retries: {{ $webhook['max_retries'] }}
                                </span>
                            </div>
                            @if(!empty($webhook['headers']))
                                <div class="col-auto">
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-list-ul"></i> {{ count($webhook['headers']) }} Header(s)
                                    </span>
                                </div>
                            @endif
                            @if(!empty($webhook['trigger_conditions']))
                                <div class="col-auto">
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-filter"></i> Conditional
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Test Result --}}
                        @if($webhookTestResult && isset($webhookTestResult['webhook_id']) && $webhookTestResult['webhook_id'] == $webhook['id'])
                            <div class="mt-3 alert {{ $webhookTestResult['success'] ? 'alert-success' : 'alert-danger' }} mb-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>
                                            <i class="bi bi-{{ $webhookTestResult['success'] ? 'check-circle' : 'x-circle' }}"></i>
                                            Test {{ $webhookTestResult['success'] ? 'Successful' : 'Failed' }}
                                        </strong>
                                        <div class="small mt-1">
                                            @if($webhookTestResult['success'])
                                                Status: {{ $webhookTestResult['status'] }} | Duration: {{ $webhookTestResult['duration'] }}s
                                            @else
                                                Error: {{ $webhookTestResult['error'] }}
                                            @endif
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        class="btn-close"
                                        wire:click="$set('webhookTestResult', null)"
                                    ></button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Webhook Editor Modal --}}
    @if($showWebhookEditor)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-webhook me-2"></i>
                            {{ $editingWebhookId ? 'Edit Webhook' : 'Add Webhook' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeWebhookEditor"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Webhook Name --}}
                        <div class="mb-3">
                            <label for="webhookName" class="form-label">
                                Name <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control @error('webhookName') is-invalid @enderror"
                                id="webhookName"
                                wire:model="webhookName"
                                placeholder="e.g., Slack Notification, CRM Integration"
                            >
                            @error('webhookName')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">A descriptive name for this webhook</small>
                        </div>

                        {{-- Webhook URL --}}
                        <div class="mb-3">
                            <label for="webhookUrl" class="form-label">
                                URL <span class="text-danger">*</span>
                            </label>
                            <input
                                type="url"
                                class="form-control @error('webhookUrl') is-invalid @enderror"
                                id="webhookUrl"
                                wire:model="webhookUrl"
                                placeholder="https://api.example.com/webhook"
                            >
                            @error('webhookUrl')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">The endpoint URL to send data to</small>
                        </div>

                        <div class="row">
                            {{-- HTTP Method --}}
                            <div class="col-md-4 mb-3">
                                <label for="webhookMethod" class="form-label">
                                    Method <span class="text-danger">*</span>
                                </label>
                                <select
                                    class="form-select @error('webhookMethod') is-invalid @enderror"
                                    id="webhookMethod"
                                    wire:model="webhookMethod"
                                >
                                    <option value="GET">GET</option>
                                    <option value="POST">POST</option>
                                    <option value="PUT">PUT</option>
                                    <option value="PATCH">PATCH</option>
                                    <option value="DELETE">DELETE</option>
                                </select>
                                @error('webhookMethod')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Format --}}
                            <div class="col-md-4 mb-3">
                                <label for="webhookFormat" class="form-label">
                                    Format <span class="text-danger">*</span>
                                </label>
                                <select
                                    class="form-select @error('webhookFormat') is-invalid @enderror"
                                    id="webhookFormat"
                                    wire:model="webhookFormat"
                                >
                                    <option value="json">JSON</option>
                                    <option value="form_data">Form Data</option>
                                    <option value="xml">XML</option>
                                </select>
                                @error('webhookFormat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Enabled --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label d-block">Status</label>
                                <div class="form-check form-switch">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="webhookEnabled"
                                        wire:model="webhookEnabled"
                                    >
                                    <label class="form-check-label" for="webhookEnabled">
                                        {{ $webhookEnabled ? 'Enabled' : 'Disabled' }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- HTTP Headers --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">HTTP Headers</label>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-secondary"
                                    wire:click="addWebhookHeader"
                                >
                                    <i class="bi bi-plus"></i> Add Header
                                </button>
                            </div>

                            @if(empty($webhookHeaders))
                                <div class="alert alert-sm alert-light mb-0">
                                    No custom headers. Click "Add Header" to add authentication or custom headers.
                                </div>
                            @else
                                @foreach($webhookHeaders as $index => $header)
                                    <div class="row g-2 mb-2" wire:key="header-{{ $index }}">
                                        <div class="col-5">
                                            <input
                                                type="text"
                                                class="form-control form-control-sm"
                                                wire:model="webhookHeaders.{{ $index }}.key"
                                                placeholder="Header name (e.g., Authorization)"
                                            >
                                        </div>
                                        <div class="col-6">
                                            <input
                                                type="text"
                                                class="form-control form-control-sm"
                                                wire:model="webhookHeaders.{{ $index }}.value"
                                                placeholder="Header value"
                                            >
                                        </div>
                                        <div class="col-1">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger w-100"
                                                wire:click="removeWebhookHeader({{ $index }})"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="row">
                            {{-- Max Retries --}}
                            <div class="col-md-6 mb-3">
                                <label for="webhookMaxRetries" class="form-label">
                                    Max Retries <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="number"
                                    class="form-control @error('webhookMaxRetries') is-invalid @enderror"
                                    id="webhookMaxRetries"
                                    wire:model="webhookMaxRetries"
                                    min="0"
                                    max="10"
                                >
                                @error('webhookMaxRetries')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Number of retry attempts on failure (0-10)</small>
                            </div>

                            {{-- Retry Delay --}}
                            <div class="col-md-6 mb-3">
                                <label for="webhookRetryDelay" class="form-label">
                                    Retry Delay (seconds) <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="number"
                                    class="form-control @error('webhookRetryDelay') is-invalid @enderror"
                                    id="webhookRetryDelay"
                                    wire:model="webhookRetryDelay"
                                    min="1"
                                    max="3600"
                                >
                                @error('webhookRetryDelay')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Initial delay before retry (uses exponential backoff)</small>
                            </div>
                        </div>

                        {{-- Trigger Conditions (Optional - Phase 5 feature) --}}
                        <div class="alert alert-light">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Note:</strong> Conditional triggers can be configured in a future update.
                            Currently, webhooks fire on every form submission.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeWebhookEditor">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="saveWebhook">
                            <i class="bi bi-check-circle me-1"></i>
                            {{ $editingWebhookId ? 'Update Webhook' : 'Create Webhook' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
