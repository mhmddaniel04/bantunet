<?php

namespace App\Http\Requests\Npo;

use App\Models\Submission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Approve / reject an application.
 */
class ReviewApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('review', $this->route('submission'));
    }

    public function rules(): array
    {
        return [
            'decision'      => ['required', Rule::in([Submission::STATUS_APPROVED, Submission::STATUS_REJECTED])],
            'reviewer_note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'decision.required' => 'Choose Approve or Reject.',
        ];
    }
}