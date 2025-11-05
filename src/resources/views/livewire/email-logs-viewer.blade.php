{{--
    Email Logs Viewer

    Modal displaying email delivery logs with filtering and detail view.
--}}

<div>
    {{-- Filters Row --}}
    <div class="row g-3 mb-4">
        {{-- Status Filter --}}
        <div class="col-md-4">
            <label for="statusFilter" class="form-label small">Status</label>
            <select class="form-select form-select-sm" id="statusFilter" wire:model.live="statusFilter">
                <option value="all">All Statuses</option>
                <option value="sent">✓ Sent</option>
                <option value="failed">✗ Failed</option>
                <option value="queued">⏱ Queued</option>
            </select>
        </div>

        {{-- Date Filter --}}
        <div class="col-md-4">
            <label for="dateFilter" class="form-label small">Date Range</label>
            <select class="form-select form-select-sm" id="dateFilter" wire:model.live="dateFilter">
                <option value="all">All Time</option>
                <option value="today">Today</option>
                <option value="last_7_days">Last 7 Days</option>
                <option value="last_30_days">Last 30 Days</option>
            </select>
        </div>

        {{-- Search --}}
        <div class="col-md-4">
            <label for="searchQuery" class="form-label small">Search Recipient</label>
            <input
                type="text"
                class="form-control form-control-sm"
                id="searchQuery"
                wire:model.live.debounce.300ms="searchQuery"
                placeholder="user@example.com"
            >
        </div>
    </div>

    {{-- Logs Table --}}
    @if($logs->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 20%;">Date/Time</th>
                        <th style="width: 30%;">Recipient</th>
                        <th style="width: 35%;">Subject</th>
                        <th style="width: 15%;" class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr
                            wire:key="log-{{ $log->id }}"
                            style="cursor: pointer;"
                            wire:click="viewLogDetails({{ $log->id }})"
                        >
                            <td>
                                <small>{{ $log->sent_at?->format('M d, Y H:i') ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <small>{{ $log->recipient_email }}</small>
                            </td>
                            <td>
                                <small>{{ Str::limit($log->subject, 50) }}</small>
                            </td>
                            <td class="text-center">
                                @if($log->status === 'sent')
                                    <span class="badge bg-success">✓ Sent</span>
                                @elseif($log->status === 'failed')
                                    <span class="badge bg-danger">✗ Failed</span>
                                @elseif($log->status === 'queued')
                                    <span class="badge bg-warning text-dark">⏱ Queued</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($log->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="small text-muted">
                Showing {{ $logs->firstItem() }}-{{ $logs->lastItem() }} of {{ $logs->total() }} logs
            </div>
            <div>
                {{ $logs->links() }}
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            No email logs found for the selected filters.
        </div>
    @endif

    {{-- Log Detail Modal --}}
    @if($selectedLog)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" wire:key="log-detail-{{ $selectedLog->id }}">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header @if($selectedLog->status === 'sent') bg-success @elseif($selectedLog->status === 'failed') bg-danger @else bg-warning @endif bg-opacity-10">
                        <h5 class="modal-title">
                            <i class="bi bi-envelope-open me-2"></i>
                            Email Log Details
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeLogDetails"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Status Badge --}}
                        <div class="mb-3">
                            <strong>Status:</strong>
                            @if($selectedLog->status === 'sent')
                                <span class="badge bg-success">✓ Sent</span>
                            @elseif($selectedLog->status === 'failed')
                                <span class="badge bg-danger">✗ Failed</span>
                            @elseif($selectedLog->status === 'queued')
                                <span class="badge bg-warning text-dark">⏱ Queued</span>
                            @endif
                        </div>

                        {{-- Recipient --}}
                        <div class="mb-3">
                            <strong>Recipient:</strong>
                            <code>{{ $selectedLog->recipient_email }}</code>
                        </div>

                        {{-- Subject --}}
                        <div class="mb-3">
                            <strong>Subject:</strong>
                            <div class="text-muted">{{ $selectedLog->subject }}</div>
                        </div>

                        {{-- Sent At --}}
                        <div class="mb-3">
                            <strong>Sent At:</strong>
                            {{ $selectedLog->sent_at?->format('F j, Y g:i A') ?? 'Not sent yet' }}
                        </div>

                        {{-- Template --}}
                        @if($selectedLog->template)
                            <div class="mb-3">
                                <strong>Template:</strong>
                                Priority {{ $selectedLog->template->priority ?? 'N/A' }}
                                @if($selectedLog->template->attach_pdf)
                                    <span class="badge bg-secondary ms-2">
                                        <i class="bi bi-paperclip"></i> PDF Attached
                                    </span>
                                @endif
                            </div>
                        @endif

                        {{-- Submission --}}
                        @if($selectedLog->submission)
                            <div class="mb-3">
                                <strong>Submission:</strong>
                                <a href="#" class="text-decoration-none">
                                    #{{ $selectedLog->submission->id }}
                                </a>
                                <small class="text-muted">
                                    ({{ $selectedLog->submission->created_at->format('M d, Y') }})
                                </small>
                            </div>
                        @endif

                        {{-- Error Message --}}
                        @if($selectedLog->status === 'failed' && $selectedLog->error_message)
                            <div class="alert alert-danger">
                                <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Error Message:</strong>
                                <pre class="mb-0 mt-2 small">{{ $selectedLog->error_message }}</pre>
                            </div>
                        @endif

                        {{-- Attempts --}}
                        @if($selectedLog->attempts > 1)
                            <div class="mb-3">
                                <strong>Attempts:</strong>
                                <span class="badge bg-info">{{ $selectedLog->attempts }} attempt(s)</span>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeLogDetails">
                            <i class="bi bi-x-circle me-1"></i> Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
