@extends('slick-forms::layouts.app')

@section('title', $form->name)

@section('content')
<div class="py-5">
    <livewire:slick-forms::form-renderer
        :form-id="$form->id"
        :prefill-data="$prefillData ?? null"
    />
</div>
@endsection
