@extends('slick-forms::layouts.app')

@section('title', 'Manage Forms')

@section('content')
<div class="container py-5">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1>Manage Forms</h1>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Analytics Dashboard --}}
    <div class="mb-4">
        <livewire:slick-forms::manage-stats />
    </div>

    {{-- Template Gallery --}}
    <livewire:slick-forms::form-templates />

    {{-- Forms Table with Search/Sort --}}
    <livewire:slick-forms::manage />
</div>
@endsection
