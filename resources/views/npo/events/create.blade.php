@extends('layouts.npo')

@section('title', 'New initiative')

@section('content')
    <a href="{{ route('npo.events.index') }}" class="back-link">← Back to my initiatives</a>

    <x-page-header eyebrow="New" title="Create an initiative"
                   lead="Published initiatives appear on the public website straight away. Save as Draft to finish later." />

    <form method="POST" action="{{ route('npo.events.store') }}" enctype="multipart/form-data">
        @csrf
        @include('npo.events._form', ['submitLabel' => 'Save initiative'])
    </form>
@endsection