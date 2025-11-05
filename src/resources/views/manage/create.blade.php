@extends('slick-forms::layouts.app')

@section('title', 'Create Form')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Create New Form</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('slick-forms.manage.store') }}" method="POST">
                        @csrf

                        <div class="form-floating mb-3">
                            <input
                                type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                id="name"
                                name="name"
                                placeholder="Form Name"
                                value="{{ old('name') }}"
                                required
                            >
                            <label for="name">Form Name <span class="text-danger">*</span></label>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <textarea
                                class="form-control @error('description') is-invalid @enderror"
                                id="description"
                                name="description"
                                placeholder="Description"
                                style="height: 100px"
                            >{{ old('description') }}</textarea>
                            <label for="description">Description</label>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Optional description for your form</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3 form-check form-switch">
                                    <input
                                        type="checkbox"
                                        class="form-check-input"
                                        role="switch"
                                        id="is_active"
                                        name="is_active"
                                        value="1"
                                        {{ old('is_active', true) ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="is_active">
                                        Active (Allow Submissions)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 form-check form-switch">
                                    <input
                                        type="checkbox"
                                        class="form-check-input"
                                        role="switch"
                                        id="is_public"
                                        name="is_public"
                                        value="1"
                                        {{ old('is_public', true) ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="is_public">
                                        Public (Users don't have to sign-in)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expires_at" class="form-label">Expiration Date</label>
                                    <input
                                        type="datetime-local"
                                        class="form-control @error('expires_at') is-invalid @enderror"
                                        id="expires_at"
                                        name="expires_at"
                                        value="{{ old('expires_at') }}"
                                    >
                                    <div class="form-text">Form will be disabled after this date</div>
                                    @error('expires_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="time_limited" class="form-label">Time Limit (minutes)</label>
                                    <input
                                        type="number"
                                        class="form-control @error('time_limited') is-invalid @enderror"
                                        id="time_limited"
                                        name="time_limited"
                                        value="{{ old('time_limited', 0) }}"
                                        min="0"
                                    >
                                    <div class="form-text">Max completion time (0 = unlimited)</div>
                                    @error('time_limited')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Create Form
                            </button>
                            <a href="{{ route('slick-forms.manage.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
