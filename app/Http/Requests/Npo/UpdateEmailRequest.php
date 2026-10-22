<?php

namespace App\Http\Requests\Npo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Change the login email. Requires the current password.
 */
class UpdateEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'org_email' => mb_strtolower(trim((string) $this->input('org_email'))),
        ]);
    }

    public function rules(): array
    {
        return [
            'org_email' => [
                'required', 'string', 'email', 'max:100',
                Rule::unique('organizations', 'org_email')->ignore($this->user()->org_id, 'org_id'),
            ],
            // "current_password" checks the value against the logged-in NPO's password
            'email_password' => ['required', 'current_password'],
        ];
    }

    public function messages(): array
    {
        return [
            'org_email.unique'                => 'This email is already used by another organisation.',
            'email_password.current_password' => 'Your password is incorrect.',
        ];
    }

    public function attributes(): array
    {
        return [
            'org_email'      => 'new email',
            'email_password' => 'password',
        ];
    }
}