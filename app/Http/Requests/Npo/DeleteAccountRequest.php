<?php

namespace App\Http\Requests\Npo;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Permanently delete the organisation. Requires the password and typing DELETE.
 */
class DeleteAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'delete_password' => ['required', 'current_password'],
            'confirm_text'    => ['required', 'in:DELETE'],
        ];
    }

    public function messages(): array
    {
        return [
            'delete_password.current_password' => 'Your password is incorrect.',
            'confirm_text.in'                  => 'Type DELETE in capital letters to confirm.',
        ];
    }

    public function attributes(): array
    {
        return [
            'delete_password' => 'password',
            'confirm_text'    => 'confirmation',
        ];
    }
}