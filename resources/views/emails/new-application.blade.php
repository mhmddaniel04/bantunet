<x-mail::message>
# New volunteer application

**{{ $submission->volunteer_name }}** applied to **{{ $submission->event->title }}**.

**Email:** {{ $submission->volunteer_email }}
**Phone:** {{ $submission->volunteer_phone }}
**Availability:** {{ config('bantunet.availability')[$submission->availability] ?? $submission->availability }}
@if ($submission->source === \App\Models\Submission::SOURCE_SMART_MATCH)
**Found via:** Smart AI Match ({{ number_format($submission->match_score) }}% match)
@endif

<x-mail::button :url="$reviewUrl">
Review application
</x-mail::button>

{{ config('app.name') }} NPO Portal
</x-mail::message>