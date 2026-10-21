<x-mail::message>
@if ($approved)
# Great news, {{ $submission->volunteer_name }}!

Your application for **{{ $submission->event->title }}** has been **approved** by {{ $submission->event->organization->org_name }}.

**Date:** {{ $submission->event->event_date->format('l, j F Y') }}
**Time:** {{ $submission->event->timeRange() }}
**Location:** {{ $submission->event->location }}, {{ $submission->event->state }}
@else
# Hi {{ $submission->volunteer_name }},

Thank you for applying to **{{ $submission->event->title }}**. Unfortunately, {{ $submission->event->organization->org_name }} could not offer you a place this time.

Please don't be discouraged — there are many other initiatives looking for volunteers like you.
@endif

@if ($submission->reviewer_note)
<x-mail::panel>
**Note from the organisation:**
{{ $submission->reviewer_note }}
</x-mail::panel>
@endif

<x-mail::button :url="$trackUrl">
View my application
</x-mail::button>

Thank you,<br>
{{ config('app.name') }}
</x-mail::message>