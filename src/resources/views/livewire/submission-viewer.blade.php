<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Submissions for {{ $this->form->name }}</h2>
            <p class="text-muted">Total submissions: {{ $submissions->total() }}</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('slick-forms.manage.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to All Forms
                </a>
                @if($this->excelAvailable || $this->pdfAvailable)
                    <div class="btn-group" role="group">
                        @if($this->excelAvailable)
                            <button type="button" class="btn btn-success" wire:click="exportCsv">
                                <i class="bi bi-filetype-csv"></i> CSV
                            </button>
                            <button type="button" class="btn btn-success" wire:click="exportExcel">
                                <i class="bi bi-file-earmark-excel"></i> Excel
                            </button>
                        @endif
                        @if($this->pdfAvailable)
                            <button type="button" class="btn btn-danger" wire:click="exportPdf">
                                <i class="bi bi-filetype-pdf"></i> PDF
                            </button>
                        @endif
                    </div>
                @else
                    <div class="alert alert-info mb-0 py-2 px-3">
                        <small>
                            <i class="bi bi-info-circle"></i>
                            Export functionality requires optional packages. Install:
                            <code>composer require maatwebsite/excel barryvdh/laravel-dompdf</code>
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label">Search</label>
            <input
                type="text"
                class="form-control"
                wire:model.live="search"
                placeholder="Search submissions..."
            >
        </div>
        <div class="col-md-3">
            <label class="form-label">Start Date</label>
            <input
                type="date"
                class="form-control"
                wire:model.live="startDate"
            >
        </div>
        <div class="col-md-3">
            <label class="form-label">End Date</label>
            <input
                type="date"
                class="form-control"
                wire:model.live="endDate"
            >
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button
                type="button"
                class="btn btn-outline-secondary w-100"
                wire:click="$set('search', ''); $set('startDate', null); $set('endDate', null)"
            >
                Clear Filters
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @if($submissions->isEmpty())
                <div class="alert alert-info">
                    No submissions yet for this form.
                </div>
            @else
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Submitted By</th>
                                        <th>Submitted At</th>
                                        <th>IP Address</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($submissions as $submission)
                                        <tr>
                                            <td>{{ $submission->id }}</td>
                                            <td>
                                                @if($submission->user)
                                                    {{ $submission->user->name }}
                                                @else
                                                    <span class="text-muted">Guest</span>
                                                @endif
                                            </td>
                                            <td>{{ $submission->submitted_at->format('M d, Y g:i A') }}</td>
                                            <td>{{ $submission->ip_address ?? 'N/A' }}</td>
                                            <td>
                                                <button
                                                    class="btn btn-sm btn-primary"
                                                    wire:click="viewSubmission({{ $submission->id }})"
                                                >
                                                    <i class="bi bi-eye"></i> View
                                                </button>
                                                <button
                                                    class="btn btn-sm btn-danger"
                                                    wire:click="deleteSubmission({{ $submission->id }})"
                                                    onclick="return confirm('Are you sure you want to delete this submission?')"
                                                >
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $submissions->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($showSubmissionModal && $this->selectedSubmission)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Submission #{{ $this->selectedSubmission->id }}</h5>
                        <button type="button" class="btn-close" wire:click="closeSubmissionModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <strong>Submitted By:</strong>
                            @if($this->selectedSubmission->user)
                                {{ $this->selectedSubmission->user->name }} ({{ $this->selectedSubmission->user->email }})
                            @else
                                <span class="text-muted">Guest</span>
                            @endif
                        </div>
                        <div class="mb-3">
                            <strong>Submitted At:</strong> {{ $this->selectedSubmission->submitted_at->format('M d, Y g:i A') }}
                        </div>
                        <div class="mb-3">
                            <strong>IP Address:</strong> {{ $this->selectedSubmission->ip_address ?? 'N/A' }}
                        </div>

                        <hr>

                        <h6 class="mb-3">Form Data:</h6>

                        @foreach($this->selectedSubmission->fieldValues as $fieldValue)
                            <div class="mb-3">
                                <strong>{{ $fieldValue->field->label }}:</strong>
                                <div class="ps-3">
                                    @if($fieldValue->value)
                                        {{ $fieldValue->value }}
                                    @else
                                        <span class="text-muted">No response</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeSubmissionModal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
