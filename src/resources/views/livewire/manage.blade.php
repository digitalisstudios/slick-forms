<div>
    {{-- Search and Filter Bar --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        class="form-control"
                        placeholder="Search forms by name or description..."
                    >
                </div>
                <div class="col-md-4 text-end">
                    @if($search)
                        <button wire:click="$set('search', '')" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Clear
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Forms Table --}}
    @if($forms->isEmpty())
        <div class="alert alert-info">
            @if($search)
                No forms found matching your search.
            @else
                No forms have been created yet.
            @endif
        </div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>
                                <a href="#" wire:click.prevent="sortBy('name')" class="text-decoration-none text-dark">
                                    Name
                                    @if($sortBy === 'name')
                                        <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Description</th>
                            <th>
                                <a href="#" wire:click.prevent="sortBy('is_active')" class="text-decoration-none text-dark">
                                    Status
                                    @if($sortBy === 'is_active')
                                        <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center">Submissions</th>
                            <th>
                                <a href="#" wire:click.prevent="sortBy('created_at')" class="text-decoration-none text-dark">
                                    Created
                                    @if($sortBy === 'created_at')
                                        <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($forms as $form)
                            <tr>
                                <td>
                                    <a href="{{ route('slick-forms.builder.show', $form) }}" class="text-decoration-none fw-semibold text-primary">
                                        {{ $form->name }}
                                    </a>
                                </td>
                                <td>{{ Str::limit($form->description, 50) }}</td>
                                <td>
                                    @if($form->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('slick-forms.submissions.show', $form) }}" class="text-decoration-none">
                                        <span class="badge bg-info">{{ number_format($form->submissions_count) }}</span>
                                    </a>
                                </td>
                                <td>{{ $form->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="d-flex gap-1 align-items-center">
                                        <a href="{{ route('slick-forms.form.show.hash', ['hash' => app(\DigitalisStudios\SlickForms\Services\UrlObfuscationService::class)->encodeId($form->id)]) }}" class="btn btn-sm btn-outline-success" title="View Form">
                                            <i class="bi bi-eye"></i> View
                                        </a>

                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="More Actions">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('slick-forms.submissions.show', $form) }}">
                                                        <i class="bi bi-list-check me-2"></i>Submissions
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('slick-forms.analytics.show', $form) }}">
                                                        <i class="bi bi-graph-up me-2"></i>Analytics
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('slick-forms.manage.edit', $form) }}">
                                                        <i class="bi bi-gear me-2"></i>Settings
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('slick-forms.forms.duplicate', $form) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="bi bi-files me-2"></i>Duplicate
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#templateModal{{ $form->id }}">
                                                        <i class="bi bi-star me-2"></i>Make Template
                                                    </button>
                                                </li>
                                                <li>
                                                    <form action="{{ route('slick-forms.forms.toggle-active', $form) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            @if($form->is_active)
                                                                <i class="bi bi-pause-circle me-2"></i>Disable
                                                            @else
                                                                <i class="bi bi-play-circle me-2"></i>Enable
                                                            @endif
                                                        </button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $form->id }}">
                                                        <i class="bi bi-trash me-2"></i>Delete
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($forms->hasPages())
                <div class="card-body">
                    {{ $forms->links() }}
                </div>
            @endif
        </div>
    @endif

    {{-- Delete Confirmation Modals --}}
    @foreach($forms as $form)
        <div class="modal fade" id="deleteModal{{ $form->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $form->id }}" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel{{ $form->id }}">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete the form <strong>{{ $form->name }}</strong>?</p>
                        <p class="text-danger mb-0">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            This action cannot be undone. All submissions and form data will be permanently deleted.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('slick-forms.manage.destroy', $form) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash me-1"></i>Delete Form
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Make Template Modals --}}
    @foreach($forms as $form)
        <div class="modal fade" id="templateModal{{ $form->id }}" tabindex="-1" aria-labelledby="templateModalLabel{{ $form->id }}" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('slick-forms.forms.save-as-template', $form) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="templateModalLabel{{ $form->id }}">Save as Template</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="template_name{{ $form->id }}" class="form-label">Template Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="template_name{{ $form->id }}" name="template_name" value="{{ $form->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="template_category{{ $form->id }}" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="template_category{{ $form->id }}" name="template_category" required>
                                    <option value="">Select a category...</option>
                                    <option value="contact">Contact</option>
                                    <option value="survey">Survey</option>
                                    <option value="registration">Registration</option>
                                    <option value="order">Order</option>
                                    <option value="application">Application</option>
                                    <option value="quiz">Quiz</option>
                                    <option value="lead">Lead</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="template_description{{ $form->id }}" class="form-label">Description</label>
                                <textarea class="form-control" id="template_description{{ $form->id }}" name="template_description" rows="3">{{ $form->description }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-star me-1"></i>Save as Template
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
