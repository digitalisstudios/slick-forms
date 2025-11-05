<div>
    @if($templates->count() > 0)
        <div class="row mb-4 align-items-start">
            <div class="col">
                {{-- Template Gallery --}}
                <div class="card">
                    <div class="card-header" style="cursor: pointer;" wire:click="toggleCollapse">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-files me-2"></i>Start from a Template
                            </h5>
                            <i class="bi bi-chevron-{{ $collapsed ? 'down' : 'up' }}"></i>
                        </div>
                    </div>
                    <div class="collapse {{ $collapsed ? '' : 'show' }}" id="templateGallery">
                        <div class="card-body">
                            @foreach($templates as $category => $categoryTemplates)
                                <h6 class="text-muted mb-3">{{ ucwords(str_replace('_', ' ', $category)) }}</h6>
                                <div class="row g-3 mb-4">
                                    @foreach($categoryTemplates as $template)
                                        <div class="col-md-4">
                                            <div class="card h-100 border-primary">
                                                <div class="card-body">
                                                    <h6 class="card-title">{{ $template->name }}</h6>
                                                    <p class="card-text small text-muted">
                                                        {{ $template->template_description ?? $template->description }}
                                                    </p>
                                                </div>
                                                <div class="card-footer bg-white border-top-0">
                                                    <form action="{{ route('slick-forms.templates.use', $template) }}" method="POST">
                                                        @csrf
                                                        <div class="input-group input-group-sm">
                                                            <input
                                                                type="text"
                                                                name="form_name"
                                                                class="form-control"
                                                                placeholder="New form name..."
                                                                required
                                                            >
                                                            <button type="submit" class="btn btn-primary">
                                                                <i class="bi bi-plus-circle"></i> Use
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('slick-forms.manage.create') }}" class="btn btn-primary py-3">
                    <i class="bi bi-plus-circle"></i> Create New Form
                </a>
            </div>
        </div>
    @else
        <div class="row mb-4">
            <div class="col text-center">
                <a href="{{ route('slick-forms.manage.create') }}" class="btn btn-primary py-3">
                    <i class="bi bi-plus-circle"></i> Create New Form
                </a>
            </div>
        </div>
    @endif
</div>
