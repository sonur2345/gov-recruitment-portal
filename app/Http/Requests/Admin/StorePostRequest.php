<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
            'experience_required' => $this->boolean('experience_required'),
            'skill_test_required' => $this->boolean('skill_test_required'),
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
            'notification_id' => ['required', 'exists:notifications,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:100', 'unique:posts,code'],
            'total_vacancies' => ['required', 'integer', 'min:1'],
            'category_breakup' => ['required', 'json'],
            'age_min' => ['required', 'integer', 'min:0'],
            'age_max' => ['required', 'integer', 'gte:age_min'],
            'qualification_text' => ['required', 'string'],
            'pay_level' => ['nullable', 'string', 'max:100'],
            'application_fee_general' => ['required', 'numeric', 'min:0'],
            'application_fee_reserved' => ['required', 'numeric', 'min:0'],
            'exam_date' => ['nullable', 'date'],
            'experience_required' => ['required', 'boolean'],
            'skill_test_required' => ['required', 'boolean'],
            'weight_education' => ['nullable', 'numeric', 'min:0'],
            'weight_skill' => ['nullable', 'numeric', 'min:0'],
            'weight_experience' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
