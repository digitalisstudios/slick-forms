{{--
    Spam Logs Viewer

    Modal displaying spam detection logs with filtering and detail view.
--}}

<div>
    {{-- Filters Row --}}
    <div class="row g-3 mb-4">
        {{-- Detection Method Filter --}}
        <div class="col-md-4">
            <label for="methodFilter" class="form-label small">Detection Method</label>
            <select class="form-select form-select-sm" id="methodFilter" wire:model.live="methodFilter">
                <option value="all">All Methods</option>
                <option value="honeypot">🐝 Honeypot</option>
                <option value="rate_limit">⏱ Rate Limit</option>
                <option value="recaptcha">🤖 reCAPTCHA</option>
                <option value="hcaptcha">✋ hCaptcha</option>
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
            <label for="searchQuery" class="form-label small">Search IP Address</label>
            <input
                type="text"
                class="form-control form-control-sm"
                id="searchQuery"
                wire:model.live.debounce.300ms="searchQuery"
                placeholder="192.168.1.1"
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
                        <th style="width: 25%;">IP Address</th>
                        <th style="width: 20%;" class="text-center">Method</th>
                        <th style="width: 35%;">Details</th>
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
                                <small>{{ $log->created_at?->format('M d, Y H:i') ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <code class="small">{{ $log->ip_address }}</code>
                            </td>
                            <td class="text-center">
                                @if($log->detection_method === 'honeypot')
                                    <span class="badge bg-warning text-dark">🐝 Honeypot</span>
                                @elseif($log->detection_method === 'rate_limit')
                                    <span class="badge bg-danger">⏱ Rate Limit</span>
                                @elseif($log->detection_method === 'recaptcha')
                                    <span class="badge bg-primary">🤖 reCAPTCHA</span>
                                @elseif($log->detection_method === 'hcaptcha')
                                    <span class="badge bg-success">✋ hCaptcha</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($log->detection_method) }}</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    @php
                                        $details = $log->details;
                                        $summary = '';

                                        if ($log->detection_method === 'honeypot') {
                                            if (isset($details['field_filled'])) {
                                                $summary = 'Field filled: ' . ($details['field_filled'] ? 'Yes' : 'No');
                                            }
                                        } elseif ($log->detection_method === 'rate_limit') {
                                            if (isset($details['attempts'])) {
                                                $summary = 'Attempts: ' . $details['attempts'];
                                            }
                                        } elseif ($log->detection_method === 'recaptcha') {
                                            if (isset($details['score'])) {
                                                $summary = 'Score: ' . number_format($details['score'], 2);
                                            }
                                        } elseif ($log->detection_method === 'hcaptcha') {
                                            $summary = 'Challenge failed';
                                        }
                                    @endphp
                                    {{ $summary ?: 'Click for details' }}
                                </small>
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
            No spam logs found for the selected filters.
        </div>
    @endif

    {{-- Log Detail Modal --}}
    @if($selectedLog)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" wire:key="log-detail-{{ $selectedLog->id }}">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-danger bg-opacity-10">
                        <h5 class="modal-title">
                            <i class="bi bi-shield-exclamation me-2"></i>
                            Spam Detection Details
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeLogDetails"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Detection Method Badge --}}
                        <div class="mb-3">
                            <strong>Detection Method:</strong>
                            @if($selectedLog->detection_method === 'honeypot')
                                <span class="badge bg-warning text-dark">🐝 Honeypot</span>
                            @elseif($selectedLog->detection_method === 'rate_limit')
                                <span class="badge bg-danger">⏱ Rate Limit</span>
                            @elseif($selectedLog->detection_method === 'recaptcha')
                                <span class="badge bg-primary">🤖 reCAPTCHA</span>
                            @elseif($selectedLog->detection_method === 'hcaptcha')
                                <span class="badge bg-success">✋ hCaptcha</span>
                            @endif
                        </div>

                        {{-- IP Address --}}
                        <div class="mb-3">
                            <strong>IP Address:</strong>
                            <code>{{ $selectedLog->ip_address }}</code>
                        </div>

                        {{-- Detected At --}}
                        <div class="mb-3">
                            <strong>Detected At:</strong>
                            {{ $selectedLog->created_at?->format('F j, Y g:i A') ?? 'N/A' }}
                        </div>

                        {{-- Detection Details --}}
                        <div class="mb-3">
                            <strong>Detection Details:</strong>
                            <div class="border rounded p-3 bg-light mt-2">
                                @if($selectedLog->detection_method === 'honeypot')
                                    <ul class="mb-0">
                                        @if(isset($selectedLog->details['field_filled']))
                                            <li>
                                                Field Filled:
                                                <strong class="{{ $selectedLog->details['field_filled'] ? 'text-danger' : 'text-success' }}">
                                                    {{ $selectedLog->details['field_filled'] ? 'Yes' : 'No' }}
                                                </strong>
                                            </li>
                                        @endif
                                        @if(isset($selectedLog->details['field_name']))
                                            <li>Field Name: <code>{{ $selectedLog->details['field_name'] }}</code></li>
                                        @endif
                                        @if(isset($selectedLog->details['time_taken']))
                                            <li>Submission Time: {{ $selectedLog->details['time_taken'] }} seconds</li>
                                        @endif
                                        @if(isset($selectedLog->details['threshold']))
                                            <li>Time Threshold: {{ $selectedLog->details['threshold'] }} seconds</li>
                                        @endif
                                    </ul>
                                @elseif($selectedLog->detection_method === 'rate_limit')
                                    <ul class="mb-0">
                                        @if(isset($selectedLog->details['attempts']))
                                            <li>Total Attempts: <strong class="text-danger">{{ $selectedLog->details['attempts'] }}</strong></li>
                                        @endif
                                        @if(isset($selectedLog->details['max_attempts']))
                                            <li>Max Allowed: {{ $selectedLog->details['max_attempts'] }}</li>
                                        @endif
                                        @if(isset($selectedLog->details['time_window']))
                                            <li>Time Window: {{ $selectedLog->details['time_window'] }} minutes</li>
                                        @endif
                                    </ul>
                                @elseif($selectedLog->detection_method === 'recaptcha')
                                    <ul class="mb-0">
                                        @if(isset($selectedLog->details['score']))
                                            <li>
                                                Bot Score:
                                                <strong class="text-danger">{{ number_format($selectedLog->details['score'], 2) }}</strong>
                                                <small class="text-muted">(0.0 = bot, 1.0 = human)</small>
                                            </li>
                                        @endif
                                        @if(isset($selectedLog->details['threshold']))
                                            <li>Required Threshold: {{ number_format($selectedLog->details['threshold'], 2) }}</li>
                                        @endif
                                        @if(isset($selectedLog->details['action']))
                                            <li>Action: <code>{{ $selectedLog->details['action'] }}</code></li>
                                        @endif
                                    </ul>
                                @elseif($selectedLog->detection_method === 'hcaptcha')
                                    <ul class="mb-0">
                                        <li>Challenge: <strong class="text-danger">Failed</strong></li>
                                        @if(isset($selectedLog->details['error_codes']))
                                            <li>Error Codes:
                                                @foreach($selectedLog->details['error_codes'] as $code)
                                                    <code>{{ $code }}</code>{{ !$loop->last ? ', ' : '' }}
                                                @endforeach
                                            </li>
                                        @endif
                                    </ul>
                                @endif
                            </div>
                        </div>

                        {{-- Raw Details (JSON) --}}
                        @if(!empty($selectedLog->details))
                            <div class="mb-3">
                                <strong>Raw Data:</strong>
                                <details class="mt-2">
                                    <summary class="text-muted small" style="cursor: pointer;">View JSON</summary>
                                    <pre class="bg-dark text-light p-3 rounded mt-2 small">{{ json_encode($selectedLog->details, JSON_PRETTY_PRINT) }}</pre>
                                </details>
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
