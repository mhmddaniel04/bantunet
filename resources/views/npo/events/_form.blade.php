{{--
    Shared initiative form (create & edit).
    Needs: $event, $categories, $skills, $states, $statuses, $submitLabel
--}}
@php
    $hhmm = fn ($time) => $time ? substr($time, 0, 5) : null;   // "09:00:00" -> "09:00"
@endphp

<div class="card form-card">

    <section class="form-section">
        <h2 class="form-section__title">Basic information</h2>
        <p class="form-section__text">What volunteers see first.</p>

        <div class="form">
            <x-form.input name="title" label="Title" :value="$event->title" maxlength="255" required
                          placeholder="e.g. Weekend tutoring for refugee children" />

            <div class="form-row form-row--2">
                <x-form.select name="category_id" label="Category" :options="$categories"
                               :value="$event->category_id" placeholder="Select category" required />

                <x-form.select name="status" label="Status" :options="$statuses"
                               :value="$event->status" required />
            </div>

            <x-form.textarea name="description" label="Description" rows="7" :value="$event->description" required
                             hint="Describe the activities, who it helps and what volunteers will do. A clear description gives better AI matches." />

            <x-form.textarea name="requirements" label="Requirements (optional)" rows="3" :value="$event->requirements"
                             placeholder="e.g. Wear covered shoes. Bring a water bottle." />
        </div>
    </section>

    <section class="form-section">
        <h2 class="form-section__title">When & where</h2>
        <p class="form-section__text">Applications close automatically after the deadline (or the event date).</p>

        <div class="form">
            <div class="form-row form-row--3">
                <x-form.input name="event_date" label="Event date" type="date"
                              :value="$event->event_date?->format('Y-m-d')" required />
                <x-form.input name="start_time" label="Start time" type="time"
                              :value="$hhmm($event->start_time)" required />
                <x-form.input name="end_time" label="End time (optional)" type="time"
                              :value="$hhmm($event->end_time)" />
            </div>

            <div class="form-row form-row--2">
                <x-form.input name="location" label="Venue / address" :value="$event->location" required
                              placeholder="e.g. Taman Rimba Kiara, TTDI" />
                <x-form.select name="state" label="State" :options="$states"
                               :value="$event->state" placeholder="Select state" required />
            </div>

            <div class="form-row form-row--2">
                <x-form.input name="application_deadline" label="Application deadline (optional)" type="date"
                              :value="$event->application_deadline?->format('Y-m-d')"
                              hint="Leave empty to accept applications until the event date." />
                <x-form.input name="capacity" label="Volunteers needed (optional)" type="number" min="1"
                              :value="$event->capacity"
                              hint="Leave empty for no limit." />
            </div>
        </div>
    </section>

    <section class="form-section">
        <h2 class="form-section__title">Skills needed</h2>
        <p class="form-section__text">Pick 1–6 skills. These are used for filtering and Smart AI Matching.</p>

        <div class="form">
            <x-form.checkbox-group name="skills" label="Skills" :options="$skills"
                                   :selected="$event->skills->pluck('skill_id')->all()" />
        </div>
    </section>

    <section class="form-section">
        <h2 class="form-section__title">Photo & visibility</h2>

        <div class="form">
            <x-form.image name="image" label="Cover photo (optional)" :current-url="$event->imageUrl()"
                          hint="JPG, PNG or WEBP, up to 2 MB. Landscape photos look best." />

            <x-form.checkbox name="is_featured" :checked="$event->is_featured">
                Feature this initiative on the home page
            </x-form.checkbox>
        </div>
    </section>

    <div class="form-actions">
        <a href="{{ route('npo.events.index') }}" class="btn btn--ghost">Cancel</a>
        <button type="submit" class="btn btn--primary">{{ $submitLabel }}</button>
    </div>
</div>