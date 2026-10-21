<?php

namespace App\Http\Requests\Public;

use App\Models\Submission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Volunteer application form.
 */
class ApplyRequest extends FormRequest
{
    private const PHONE = 'regex:/^\+?[0-9\- ]{9,15}$/';

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'volunteer_name'  => trim((string) $this->input('volunteer_name')),
            'volunteer_email' => mb_strtolower(trim((string) $this->input('volunteer_email'))),
        ]);
    }

    public function rules(): array
    {
        $eventId = $this->route('event')->event_id;

        return [
            'volunteer_name'  => ['required', 'string', 'max:255'],
            'volunteer_email' => [
                'required', 'string', 'email', 'max:100',
                Rule::unique('submissions', 'volunteer_email')->where('event_id', $eventId),
            ],
            'volunteer_phone' => ['required', 'string', 'max:20', self::PHONE],
            'age'             => ['required', 'integer', 'between:13,100'],
            'gender'          => ['required', Rule::in(config('bantunet.genders'))],
            'tshirt_size'     => ['required', Rule::in(config('bantunet.tshirt_sizes'))],
            'availability'    => ['required', Rule::in(array_keys(config('bantunet.availability')))],
            'emergency_name'  => ['required', 'string', 'max:255'],
            'emergency_phone' => ['required', 'string', 'max:20', self::PHONE, 'different:volunteer_phone'],
            'skills_summary'  => ['nullable', 'string', 'max:1500'],
            'resume'          => [
                'nullable', 'file',
                'mimes:'.implode(',', config('bantunet.resume.mimes')),
                'max:'.config('bantunet.resume.max_kb'),
            ],
            'source'          => ['required', Rule::in([Submission::SOURCE_BROWSE, Submission::SOURCE_SMART_MATCH])],
            'match_score'     => ['nullable', 'numeric', 'between:0,100'],
            'consent'         => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'volunteer_email.unique'    => 'You have already applied to this initiative with this email. Use "Track application" to check your status.',
            'volunteer_phone.regex'     => 'Invalid phone number, e.g. 012-345 6789',
            'emergency_phone.regex'     => 'Invalid phone number, e.g. 012-345 6789',
            'emergency_phone.different' => 'The emergency contact must have a different number from yours.',
            'age.between'               => 'Volunteers must be at least 13 years old.',
            'resume.mimes'              => 'Upload your résumé as a PDF or Word document.',
            'resume.max'                => 'The résumé must be 2 MB or smaller.',
            'consent.accepted'          => 'Please agree so the organisation can use your details to process your application.',
        ];
    }

    public function attributes(): array
    {
        return [
            'volunteer_name'  => 'full name',
            'volunteer_email' => 'email',
            'volunteer_phone' => 'phone number',
            'tshirt_size'     => 'T-shirt size',
            'emergency_name'  => 'emergency contact name',
            'emergency_phone' => 'emergency contact phone',
        ];
    }
}