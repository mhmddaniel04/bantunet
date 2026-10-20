<?php

namespace App\Http\Requests\Public;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

/**
 * Checks the search/filter values in the browse page URL.
 * Invalid values (e.g. ?category=abc) simply reset the filters.
 */
class EventFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q'        => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'exists:categories,slug'],
            'state'    => ['nullable', Rule::in(config('bantunet.states'))],
            'skill'    => ['nullable', 'string', 'exists:skills,slug'],
            'when'     => ['nullable', Rule::in(array_keys(config('bantunet.date_ranges')))],
            'sort'     => ['nullable', Rule::in(array_keys(config('bantunet.sort_options')))],
        ];
    }

    /** Only the filters that were actually filled in. */
    public function filters(): array
    {
        return array_filter($this->validated(), fn ($value) => filled($value));
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(redirect()->route('events.index'));
    }
}