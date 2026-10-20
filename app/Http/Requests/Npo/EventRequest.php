<?php

namespace App\Http\Requests\Npo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Create / edit initiative form.
 */
class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        $event = $this->route('event');

        // Creating: any logged-in NPO. Editing: only the owner (EventPolicy).
        return $event ? $this->user()->can('update', $event) : true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured'  => $this->boolean('is_featured'),
            'remove_image' => $this->boolean('remove_image'),
        ]);
    }

    public function rules(): array
    {
        $isCreating = $this->route('event') === null;

        return [
            'title'                => ['required', 'string', 'max:255'],
            'category_id'          => ['required', 'integer', 'exists:categories,category_id'],
            'status'               => ['required', Rule::in(array_keys(config('bantunet.event_statuses')))],
            'description'          => ['required', 'string', 'min:30', 'max:5000'],
            'requirements'         => ['nullable', 'string', 'max:2000'],

            'event_date'           => array_filter(['required', 'date', $isCreating ? 'after_or_equal:today' : null]),
            'start_time'           => ['required', 'date_format:H:i'],
            'end_time'             => ['nullable', 'date_format:H:i', 'after:start_time'],
            'location'             => ['required', 'string', 'max:255'],
            'state'                => ['required', Rule::in(config('bantunet.states'))],
            'application_deadline' => ['nullable', 'date', 'before_or_equal:event_date'],
            'capacity'             => ['nullable', 'integer', 'min:1', 'max:10000'],

            'skills'               => ['required', 'array', 'min:1', 'max:6'],
            'skills.*'             => ['integer', 'exists:skills,skill_id'],

            'image'                => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_image'         => ['boolean'],
            'is_featured'          => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'event_date.after_or_equal'            => 'The event date cannot be in the past.',
            'end_time.after'                       => 'The end time must be after the start time.',
            'application_deadline.before_or_equal' => 'The deadline must be on or before the event date.',
            'skills.required'                      => 'Choose at least one skill — it helps AI matching.',
            'skills.max'                           => 'Choose up to 6 skills.',
            'description.min'                      => 'Please write at least 30 characters so volunteers (and the AI) understand the activity.',
            'image.max'                            => 'The photo must be 2 MB or smaller.',
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id'          => 'category',
            'event_date'           => 'event date',
            'start_time'           => 'start time',
            'end_time'             => 'end time',
            'application_deadline' => 'application deadline',
        ];
    }
}