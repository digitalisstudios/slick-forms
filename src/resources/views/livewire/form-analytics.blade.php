<div class="container-fluid">
    {{-- Chart.js CDN --}}
    @once
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @endonce

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="bi bi-graph-up me-2"></i>
            Analytics: {{ $form->name }}
        </h2>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('slick-forms.manage.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Forms
            </a>
            <a href="{{ route('slick-forms.builder.show', $form) }}" class="btn btn-primary">
                <i class="bi bi-pencil-square me-1"></i>Edit Form
            </a>
        </div>
    </div>

    {{-- Time Range Selector --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Time Range</label>
                    <select class="form-select" wire:model.live="days">
                        <option value="7">Last 7 days</option>
                        <option value="30">Last 30 days</option>
                        <option value="90">Last 90 days</option>
                        <option value="365">Last year</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Views</h6>
                            <h3 class="mb-0">{{ number_format($summary['total_views'] ?? 0) }}</h3>
                        </div>
                        <i class="bi bi-eye text-primary" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Form Starts</h6>
                            <h3 class="mb-0">{{ number_format($summary['total_starts'] ?? 0) }}</h3>
                        </div>
                        <i class="bi bi-play-circle text-info" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Submissions</h6>
                            <h3 class="mb-0">{{ number_format($summary['total_submissions'] ?? 0) }}</h3>
                        </div>
                        <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Completion Rate</h6>
                            <h3 class="mb-0">{{ number_format($summary['completion_rate'] ?? 0, 1) }}%</h3>
                        </div>
                        <i class="bi bi-graph-up-arrow text-warning" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Additional Stats Row --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Abandoned</h6>
                    <h4 class="mb-0">{{ number_format($summary['total_abandoned'] ?? 0) }}</h4>
                    <small class="text-danger">{{ number_format($summary['abandonment_rate'] ?? 0, 1) }}% abandonment rate</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Avg. Completion Time</h6>
                    <h4 class="mb-0">
                        @php
                            $seconds = $summary['average_time_seconds'] ?? 0;
                            $minutes = floor($seconds / 60);
                            $secs = $seconds % 60;
                        @endphp
                        {{ $minutes }}m {{ $secs }}s
                    </h4>
                    <small class="text-muted">Time from start to submit</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Start Rate</h6>
                    <h4 class="mb-0">
                        @php
                            $views = $summary['total_views'] ?? 0;
                            $starts = $summary['total_starts'] ?? 0;
                            $startRate = $views > 0 ? ($starts / $views) * 100 : 0;
                        @endphp
                        {{ number_format($startRate, 1) }}%
                    </h4>
                    <small class="text-muted">Views that started form</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Submissions Over Time Chart --}}
    @if(count($submissionsOverTime) > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-bar-chart-line me-2"></i>
                    Submissions Over Time
                </h5>
            </div>
            <div class="card-body">
                <canvas id="submissionsChart" height="80"></canvas>
            </div>
        </div>

        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('submissionsChart');
                if (ctx) {
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: @json(array_column($submissionsOverTime, 'date')),
                            datasets: [{
                                label: 'Submissions',
                                data: @json(array_column($submissionsOverTime, 'count')),
                                borderColor: 'rgb(75, 192, 192)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                tension: 0.1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }
            });
        </script>
        @endpush
    @endif

    {{-- Device & Browser Breakdown --}}
    <div class="row g-3 mb-4">
        @if(count($deviceBreakdown) > 0)
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-phone me-2"></i>
                            Device Breakdown
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="deviceChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('deviceChart');
                    if (ctx) {
                        new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: @json(array_column($deviceBreakdown, 'device_type')),
                                datasets: [{
                                    data: @json(array_column($deviceBreakdown, 'count')),
                                    backgroundColor: [
                                        'rgba(54, 162, 235, 0.8)',
                                        'rgba(255, 99, 132, 0.8)',
                                        'rgba(255, 206, 86, 0.8)'
                                    ]
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false
                            }
                        });
                    }
                });
            </script>
            @endpush
        @endif

        @if(count($browserBreakdown) > 0)
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-browser-chrome me-2"></i>
                            Top Browsers
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Browser</th>
                                        <th class="text-end">Count</th>
                                        <th class="text-end">%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalBrowser = array_sum(array_column($browserBreakdown, 'count'));
                                    @endphp
                                    @foreach($browserBreakdown as $browser)
                                        <tr>
                                            <td>{{ $browser['browser'] ?? 'Unknown' }}</td>
                                            <td class="text-end">{{ number_format($browser['count']) }}</td>
                                            <td class="text-end">
                                                {{ number_format(($browser['count'] / $totalBrowser) * 100, 1) }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Field Completion Rates --}}
    @if(count($fieldCompletionRates) > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-list-check me-2"></i>
                    Field Completion Rates
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Type</th>
                                <th class="text-end">Interactions</th>
                                <th>Completion Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fieldCompletionRates as $field)
                                <tr>
                                    <td>{{ $field['field_label'] }}</td>
                                    <td><span class="badge bg-secondary">{{ $field['field_type'] }}</span></td>
                                    <td class="text-end">{{ number_format($field['interactions']) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar"
                                                     style="width: {{ $field['completion_rate'] }}%"
                                                     aria-valuenow="{{ $field['completion_rate'] }}"
                                                     aria-valuemin="0"
                                                     aria-valuemax="100">
                                                    {{ number_format($field['completion_rate'], 1) }}%
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- Drop-off Points (Multi-page forms only) --}}
    @if(count($dropOffPoints) > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-sign-stop me-2"></i>
                    Drop-off Points by Page
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Page</th>
                                <th class="text-end">Views</th>
                                <th class="text-end">Abandoned</th>
                                <th>Drop-off Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dropOffPoints as $point)
                                <tr>
                                    <td>{{ $point['page_title'] }}</td>
                                    <td class="text-end">{{ number_format($point['views']) }}</td>
                                    <td class="text-end">{{ number_format($point['abandoned']) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                                <div class="progress-bar bg-danger" role="progressbar"
                                                     style="width: {{ $point['drop_off_rate'] }}%">
                                                    {{ number_format($point['drop_off_rate'], 1) }}%
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if(count($validationErrors) > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Most Common Validation Errors
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Type</th>
                                <th class="text-end">Error Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($validationErrors as $error)
                                <tr>
                                    <td>{{ $error->label }}</td>
                                    <td><span class="badge bg-secondary">{{ $error->field_type }}</span></td>
                                    <td class="text-end">
                                        <span class="badge bg-danger">{{ number_format($error->error_count) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- No Data State --}}
    @if(($summary['total_views'] ?? 0) === 0)
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-graph-up text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3">No Analytics Data Yet</h4>
                <p class="text-muted">
                    Analytics will appear here once users start viewing and submitting your form.
                </p>
                <a href="{{ route('slick-forms.form.show.hash', ['hash' => app(\DigitalisStudios\SlickForms\Services\UrlObfuscationService::class)->encodeId($form->id)]) }}" class="btn btn-primary" target="_blank">
                    <i class="bi bi-box-arrow-up-right me-1"></i>
                    View Form
                </a>
            </div>
        </div>
    @endif
</div>
