<?php

namespace App\Http\Requests\Npo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Organisation profile: details + profile picture.
 */
class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;   // route is behind "auth"; an NPO can only edit itself
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'org_name'    => trim((string) $this->input('org_name')),
            'remove_logo' => $this->boolean('remove_logo'),
        ]);
    }

    public function rules(): array
    {
        return [
            'org_name'        => ['required', 'string', 'max:255'],
            'org_phone'       => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\- ]{9,15}$/'],
            'state'           => ['required', Rule::in(config('bantunet.states'))],
            'org_hq_location' => ['nullable', 'string', 'max:500'],
            'website'         => ['nullable', 'url', 'max:255'],
            'description'     => ['nullable', 'string', 'max:2000'],
            'logo'            => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024', 'dimensions:min_width=100,min_height=100'],
            'remove_logo'     => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'org_phone.regex'  => 'Invalid phone number, e.g. 03-1234 5678',
            'website.url'      => 'Enter a full web address starting with https://',
            'logo.max'         => 'The profile picture must be 1 MB or smaller.',
            'logo.dimensions'  => 'The profile picture must be at least 100 × 100 pixels.',
        ];
    }

    public function attributes(): array
    {
        return [
            'org_name'        => 'organisation name',
            'org_phone'       => 'phone number',
            'org_hq_location' => 'address',
            'logo'            => 'profile picture',
        ];
    }
}