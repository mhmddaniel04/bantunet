<?php

namespace App\Http\Requests\Npo;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Mark which approved volunteers attended and how many hours they gave.
 * Expected input: attendance[submission_id][attended] = 0|1, attendance[submission_id][hours] = 3.5
 */
class AttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewSubmissions', $this->route('event'));
    }

    public function rules(): array
    {
        return [
            'attendance'            => ['required', 'array'],
            'attendance.*.attended' => ['required', 'boolean'],
            'attendance.*.hours'    => ['nullable', 'numeric', 'min:0', 'max:24'],
        ];
    }

    public function messages(): array
    {
        return [
            'attendance.*.hours.max'     => 'Hours must be 24 or less.',
            'attendance.*.hours.numeric' => 'Hours must be a number, e.g. 3.5',
        ];
    }
}