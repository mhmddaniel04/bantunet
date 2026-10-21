<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Smart Match questions.
 */
class MatchRequest extends FormRequest
{
    /** On error, go back to the question page (not the results URL). */
    protected $redirectRoute = 'match.index';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'about'        => ['required', 'string', 'min:15', 'max:600'],
            'skills'       => ['nullable', 'array', 'max:6'],
            'skills.*'     => ['integer', 'exists:skills,skill_id'],
            'category'     => ['nullable', 'integer', 'exists:categories,category_id'],
            'availability' => ['required', Rule::in(array_keys(config('bantunet.availability')))],
            'state'        => ['nullable', Rule::in(config('bantunet.states'))],
        ];
    }

    public function messages(): array
    {
        return [
            'about.required' => 'Tell us a little about yourself — this is what the AI reads.',
            'about.min'      => 'Please write at least a sentence (15+ characters) so the AI can understand you.',
            'skills.max'     => 'Choose up to 6 skills.',
        ];
    }

    /** Clean answers in the shape MatchingService expects. */
    public function profile(): array
    {
        return [
            'skill_ids'    => array_map('intval', $this->validated('skills', [])),
            'category_id'  => $this->validated('category') ? (int) $this->validated('category') : null,
            'availability' => $this->validated('availability'),
            'state'        => $this->validated('state'),
            'about'        => trim($this->validated('about')),
        ];
    }
}