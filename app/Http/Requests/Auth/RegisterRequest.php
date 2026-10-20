<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Clean the input before validating, so "unique" checks are reliable.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'org_name'   => trim((string) $this->input('org_name')),
            'org_email'  => mb_strtolower(trim((string) $this->input('org_email'))),
            'ros_number' => mb_strtoupper(trim((string) $this->input('ros_number'))),
        ]);
    }

    public function rules(): array
    {
        return [
            'org_name'    => ['required', 'string', 'max:255'],
            'ros_number'  => ['required', 'string', 'max:50', 'regex:/^[A-Z0-9\-\/ ]+$/', 'unique:organizations,ros_number'],
            'org_email'   => ['required', 'string', 'email', 'max:100', 'unique:organizations,org_email'],
            'org_phone'   => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\- ]{9,15}$/'],
            'state'       => ['required', Rule::in(config('bantunet.states'))],
            'password'    => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'declaration' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'ros_number.unique'    => 'This ROS number is already registered on BantuNet.',
            'ros_number.regex'     => 'Only letters, numbers, dashes and slashes allowed.',
            'org_email.unique'     => 'This email is already registered. Try signing in.',
            'org_phone.regex'      => 'Invalid phone number, e.g. 03-1234 5678',
            'declaration.accepted' => 'Please confirm your organisation is registered with ROS.',
        ];
    }

    public function attributes(): array
    {
        return [
            'org_name'  => 'organisation name',
            'org_email' => 'email',
            'org_phone' => 'phone number',
        ];
    }
}