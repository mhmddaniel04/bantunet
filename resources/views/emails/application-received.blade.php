<x-mail::message>
# Thank you, {{ $submission->volunteer_name }}!

Your application for **{{ $submission->event->title }}** has been sent to **{{ $submission->event->organization->org_name }}**.

<x-mail::panel>
Your tracking code: **{{ $submission->tracking_code }}**
</x-mail::panel>

**Date:** {{ $submission->event->event_date->format('l, j F Y') }}
**Time:** {{ $submission->event->timeRange() }}
**Location:** {{ $submission->event->location }}, {{ $submission->event->state }}

The organisation will review your application soon. You can check your status anytime with your tracking code and this email address.

<x-mail::button :url="$trackUrl">
Track my application
</x-mail::button>

Thank you for volunteering,<br>
{{ config('app.name') }}
</x-mail::message>