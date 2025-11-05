@extends('slick-forms::layouts.app')

@section('title', 'Submissions - ' . $form->name)

@section('content')
<div class="py-4">
    <livewire:slick-forms::submission-viewer :form-id="$form->id" />
</div>
@endsection
