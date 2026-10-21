<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

/**
 * "Track application" form: tracking code + email.
 */
class TrackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** Accepts "bn-7k2qx9", "BN-7K2QX9" or just "7K2QX9". */
    protected function prepareForValidation(): void
    {
        $code = strtoupper(trim((string) $this->input('tracking_code')));

        if ($code !== '' && ! str_starts_with($code, 'BN-')) {
            $code = 'BN-'.$code;
        }

        $this->merge([
            'tracking_code'   => $code,
            'volunteer_email' => mb_strtolower(trim((string) $this->input('volunteer_email'))),
        ]);
    }

    public function rules(): array
    {
        return [
            'tracking_code'   => ['required', 'string', 'max:12'],
            'volunteer_email' => ['required', 'string', 'email'],
        ];
    }
}