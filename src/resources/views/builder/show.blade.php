@extends('slick-forms::layouts.builder')

@section('title', 'Form Builder - ' . $form->name)

@section('content')
    <livewire:slick-forms::form-builder :form-id="$form->id" />
@endsection

@section('modals')
    {{-- Make Template Modal --}}
    <div class="modal fade" id="templateModal{{ $form->id }}" tabindex="-1" aria-labelledby="templateModalLabel{{ $form->id }}" aria-hidden="true">
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
@endsection
