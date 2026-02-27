<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSkillTestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_absent' => $this->boolean('is_absent'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'is_absent' => ['required', 'boolean'],
            'marks' => [
                'nullable',
                'numeric',
                'between:0,100',
                Rule::requiredIf(fn (): bool => !$this->boolean('is_absent')),
            ],
            'remark' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
