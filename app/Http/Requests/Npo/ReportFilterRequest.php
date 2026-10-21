<?php

namespace App\Http\Requests\Npo;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Report period (from/to) and optional category. Defaults to the current year.
 */
class ReportFilterRequest extends FormRequest
{
    protected $redirectRoute = 'npo.reports.index';

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'from' => $this->input('from') ?: now()->startOfYear()->toDateString(),
            'to'   => $this->input('to') ?: now()->endOfYear()->toDateString(),
        ]);
    }

    public function rules(): array
    {
        return [
            'from'     => ['required', 'date'],
            'to'       => ['required', 'date', 'after_or_equal:from'],
            'category' => ['nullable', 'integer', 'exists:categories,category_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'to.after_or_equal' => 'The end date must be on or after the start date.',
        ];
    }

    public function from(): CarbonImmutable
    {
        return CarbonImmutable::parse($this->validated('from'))->startOfDay();
    }

    public function to(): CarbonImmutable
    {
        return CarbonImmutable::parse($this->validated('to'))->endOfDay();
    }

    public function categoryId(): ?int
    {
        return $this->validated('category') ? (int) $this->validated('category') : null;
    }

    /** Current filter values, for form fields and export links. */
    public function filters(): array
    {
        return array_filter([
            'from'     => $this->from()->toDateString(),
            'to'       => $this->to()->toDateString(),
            'category' => $this->categoryId(),
        ]);
    }
}