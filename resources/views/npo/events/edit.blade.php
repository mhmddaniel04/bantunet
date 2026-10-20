@extends('layouts.npo')

@section('title', 'Edit initiative')

@section('content')
    <a href="{{ route('npo.events.index') }}" class="back-link">← Back to my initiatives</a>

    <x-page-header eyebrow="Edit" :title="$event->title">
        <x-event-status :event="$event" />
        @if ($event->status !== \App\Models\Event::STATUS_DRAFT)
            <a href="{{ route('events.show', $event) }}" class="btn btn--secondary btn--sm" target="_blank">View on site ↗</a>
        @endif
        <a href="{{ route('npo.applications.index', ['event' => $event->event_id]) }}" class="btn btn--secondary btn--sm">Applications</a>
    </x-page-header>

    <form method="POST" action="{{ route('npo.events.update', $event) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('npo.events._form', ['submitLabel' => 'Save changes'])
    </form>
@endsection