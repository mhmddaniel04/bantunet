<?php

namespace App\Http\Requests\Npo;

use Illuminate\Foundation\Http\FormRequest;

/**
 * "✨ Suggest category & skills with AI" on the initiative form.
 */
class SuggestTagsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;   // route is already behind the "auth" middleware
    }

    public function rules(): array
    {
        return [
            'title'       => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:30', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'description.required' => 'Write a description first so the AI knows what the initiative is about.',
            'description.min'      => 'Write at least 30 characters in the description first.',
        ];
    }
}