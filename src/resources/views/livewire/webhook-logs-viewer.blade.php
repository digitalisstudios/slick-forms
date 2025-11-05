<div class="webhook-logs-viewer">
    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="row mb-4">
            <div class="col">
                <h4>
                    <i class="bi bi-webhook me-2"></i>
                    Webhook Logs - {{ $form->name }}
                </h4>
                <p class="text-muted">
                    View webhook delivery logs, inspect requests/responses, and retry failed deliveries.
                </p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <label for="webhookFilter" class="form-label">Filter by Webhook</label>
                <select
                    id="webhookFilter"
                    class="form-select"
                    wire:model.live="selectedWebhookId"
                >
                    <option value="">All Webhooks</option>
                    @foreach($webhooks as $webhook)
                        <option value="{{ $webhook->id }}">{{ $webhook->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label for="statusFilter" class="form-label">Filter by Status</label>
                <select
                    id="statusFilter"
                    class="form-select"
                    wire:model.live="statusFilter"
                >
                    <option value="all">All Statuses</option>
                    <option value="sent">Sent</option>
                    <option value="failed">Failed</option>
                    <option value="pending">Pending</option>
                </select>
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    wire:click="clearFilters"
                >
                    <i class="bi bi-x-circle me-1"></i>Clear Filters
                </button>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-1"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Logs Table --}}
        <div class="card">
            <div class="card-body">
                @if($logs->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <p class="mt-3 text-muted">No webhook logs found.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Webhook</th>
                                    <th>URL</th>
                                    <th>Status</th>
                                    <th>Response</th>
                                    <th>Retries</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                    <tr wire:key="log-{{ $log->id }}">
                                        <td>
                                            <small class="text-muted">
                                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                                            </small>
                                        </td>
                                        <td>
                                            <strong>{{ $log->webhook->name }}</strong>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <i class="bi bi-link-45deg"></i>
                                                {{ Str::limit($log->request_url, 40) }}
                                            </small>
                                        </td>
                                        <td>
                                            @if($log->status === 'sent')
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> Sent
                                                </span>
                                            @elseif($log->status === 'failed')
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-x-circle"></i> Failed
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="bi bi-clock"></i> Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->response_status)
                                                <span class="badge bg-light text-dark border">
                                                    {{ $log->response_status }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->retry_count > 0)
                                                <span class="badge bg-warning">{{ $log->retry_count }}</span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-primary"
                                                    wire:click="viewLogDetails({{ $log->id }})"
                                                    title="View details"
                                                >
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                @if($log->status === 'failed')
                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-success"
                                                        wire:click="retryWebhook({{ $log->id }})"
                                                        title="Retry webhook"
                                                    >
                                                        <i class="bi bi-arrow-repeat"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-3">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Log Details Modal --}}
    @if($showLogDetails && $selectedLog)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-file-earmark-text me-2"></i>
                            Webhook Log Details
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeLogDetails"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Summary --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>Webhook Information</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="text-muted" style="width: 40%;">Name:</td>
                                        <td><strong>{{ $selectedLog->webhook->name }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">URL:</td>
                                        <td><small>{{ $selectedLog->request_url }}</small></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Event:</td>
                                        <td>{{ $selectedLog->event_type }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Timestamp:</td>
                                        <td>{{ $selectedLog->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Status Information</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="text-muted" style="width: 40%;">Status:</td>
                                        <td>
                                            @if($selectedLog->status === 'sent')
                                                <span class="badge bg-success">Sent</span>
                                            @elseif($selectedLog->status === 'failed')
                                                <span class="badge bg-danger">Failed</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Response Status:</td>
                                        <td>
                                            @if($selectedLog->response_status)
                                                <span class="badge bg-light text-dark border">
                                                    {{ $selectedLog->response_status }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Retry Count:</td>
                                        <td>{{ $selectedLog->retry_count }}</td>
                                    </tr>
                                    @if($selectedLog->sent_at)
                                        <tr>
                                            <td class="text-muted">Sent At:</td>
                                            <td>{{ $selectedLog->sent_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        @if($selectedLog->error_message)
                            <div class="alert alert-danger">
                                <h6><i class="bi bi-exclamation-triangle me-1"></i> Error Message</h6>
                                <pre class="mb-0"><code>{{ $selectedLog->error_message }}</code></pre>
                            </div>
                        @endif

                        {{-- Request Details --}}
                        <div class="mb-4">
                            <h6>Request Details</h6>

                            @if($selectedLog->request_headers)
                                <div class="mb-3">
                                    <strong>Headers:</strong>
                                    <pre class="bg-light p-3 rounded mt-2"><code>{{ json_encode($selectedLog->request_headers, JSON_PRETTY_PRINT) }}</code></pre>
                                </div>
                            @endif

                            @if($selectedLog->request_body)
                                <div class="mb-3">
                                    <strong>Body:</strong>
                                    <pre class="bg-light p-3 rounded mt-2" style="max-height: 300px; overflow-y: auto;"><code>{{ is_string($selectedLog->request_body) ? $selectedLog->request_body : json_encode(json_decode($selectedLog->request_body), JSON_PRETTY_PRINT) }}</code></pre>
                                </div>
                            @endif
                        </div>

                        {{-- Response Details --}}
                        @if($selectedLog->response_status)
                            <div class="mb-4">
                                <h6>Response Details</h6>

                                @if($selectedLog->response_headers)
                                    <div class="mb-3">
                                        <strong>Headers:</strong>
                                        <pre class="bg-light p-3 rounded mt-2"><code>{{ json_encode($selectedLog->response_headers, JSON_PRETTY_PRINT) }}</code></pre>
                                    </div>
                                @endif

                                @if($selectedLog->response_body)
                                    <div class="mb-3">
                                        <strong>Body:</strong>
                                        <pre class="bg-light p-3 rounded mt-2" style="max-height: 300px; overflow-y: auto;"><code>{{ is_string($selectedLog->response_body) ? $selectedLog->response_body : json_encode(json_decode($selectedLog->response_body), JSON_PRETTY_PRINT) }}</code></pre>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        @if($selectedLog->status === 'failed')
                            <button
                                type="button"
                                class="btn btn-success"
                                wire:click="retryWebhook({{ $selectedLog->id }})"
                            >
                                <i class="bi bi-arrow-repeat me-1"></i>Retry Webhook
                            </button>
                        @endif
                        <button type="button" class="btn btn-secondary" wire:click="closeLogDetails">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
