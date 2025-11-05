<div>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-graph-up me-2"></i>Analytics Overview (Last {{ $days }} Days)
            </h5>
        </div>
        <div class="card-body">
            {{-- Key Metrics Row --}}
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card text-center h-100 border-primary">
                        <div class="card-body">
                            <div class="text-primary mb-2">
                                <i class="bi bi-eye fs-1"></i>
                            </div>
                            <h3 class="mb-0">{{ number_format($analytics['total_views']) }}</h3>
                            <p class="text-muted mb-0 small">Total Views</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center h-100 border-info">
                        <div class="card-body">
                            <div class="text-info mb-2">
                                <i class="bi bi-play-circle fs-1"></i>
                            </div>
                            <h3 class="mb-0">{{ number_format($analytics['total_starts']) }}</h3>
                            <p class="text-muted mb-0 small">Form Starts</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center h-100 border-success">
                        <div class="card-body">
                            <div class="text-success mb-2">
                                <i class="bi bi-check-circle fs-1"></i>
                            </div>
                            <h3 class="mb-0">{{ number_format($analytics['total_submissions']) }}</h3>
                            <p class="text-muted mb-0 small">Submissions</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center h-100 border-danger">
                        <div class="card-body">
                            <div class="text-danger mb-2">
                                <i class="bi bi-x-circle fs-1"></i>
                            </div>
                            <h3 class="mb-0">{{ number_format($analytics['total_abandoned']) }}</h3>
                            <p class="text-muted mb-0 small">Abandoned</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Performance Metrics Row --}}
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h4 class="mb-1">{{ $analytics['completion_rate'] }}%</h4>
                            <p class="text-muted mb-0 small">Completion Rate</p>
                            <div class="progress mt-2" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                     style="width: {{ $analytics['completion_rate'] }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h4 class="mb-1">{{ $analytics['abandonment_rate'] }}%</h4>
                            <p class="text-muted mb-0 small">Abandonment Rate</p>
                            <div class="progress mt-2" style="height: 8px;">
                                <div class="progress-bar bg-danger" role="progressbar"
                                     style="width: {{ $analytics['abandonment_rate'] }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h4 class="mb-1">
                                @if($analytics['average_time_seconds'])
                                    {{ gmdate('i:s', $analytics['average_time_seconds']) }}
                                @else
                                    N/A
                                @endif
                            </h4>
                            <p class="text-muted mb-0 small">Avg. Completion Time</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts Row --}}
            <div class="row g-3">
                {{-- Device Breakdown --}}
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title">Device Breakdown</h6>
                            @if(count($deviceBreakdown) > 0)
                                @php
                                    $totalDevices = array_sum(array_column($deviceBreakdown, 'count'));
                                @endphp
                                <div class="list-group list-group-flush">
                                    @foreach($deviceBreakdown as $device)
                                        @php
                                            $percentage = $totalDevices > 0 ? round(($device['count'] / $totalDevices) * 100) : 0;
                                            $icon = match($device['device_type']) {
                                                'desktop' => 'bi-laptop',
                                                'mobile' => 'bi-phone',
                                                'tablet' => 'bi-tablet',
                                                default => 'bi-device',
                                            };
                                        @endphp
                                        <div class="list-group-item border-0 px-0">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span>
                                                    <i class="bi {{ $icon }} me-1"></i>
                                                    {{ ucfirst($device['device_type']) }}
                                                </span>
                                                <span class="badge bg-secondary">{{ $device['count'] }}</span>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar" role="progressbar"
                                                     style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted small mb-0">No device data available</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Submissions Over Time --}}
                <div class="col-md-8">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title">Submissions Over Time ({{ count($submissionsOverTime) }} days)</h6>
                            @if(count($submissionsOverTime) > 0)
                                @php
                                    $maxCount = max(array_column($submissionsOverTime, 'count'));
                                @endphp
                                <div class="d-flex align-items-end gap-1" style="height: 150px;">
                                    @foreach($submissionsOverTime as $day)
                                        @php
                                            $height = $maxCount > 0 ? round(($day['count'] / $maxCount) * 150) : 2;
                                        @endphp
                                        <div class="flex-fill d-flex align-items-end"
                                             data-bs-toggle="tooltip"
                                             data-bs-title="{{ \Carbon\Carbon::parse($day['date'])->format('M j') }}: {{ $day['count'] }} submissions">
                                            <div class="bg-primary rounded-top w-100"
                                                 style="height: {{ $height }}px; min-height: 2px;"></div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($submissionsOverTime[0]['date'])->format('M j') }}
                                    </small>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($submissionsOverTime[count($submissionsOverTime)-1]['date'])->format('M j') }}
                                    </small>
                                </div>
                            @else
                                <p class="text-muted small mb-0">No submission data available</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
