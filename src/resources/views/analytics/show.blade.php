@extends('slick-forms::layouts.app')

@section('title', 'Analytics - ' . $form->name)

@section('content')
<div class="py-4">
    <livewire:slick-forms::form-analytics :form-id="$form->id" />
</div>
@endsection
