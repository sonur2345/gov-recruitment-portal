<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['verified', 'provisional', 'rejected'])],
            'remark' => ['nullable', 'string', 'max:1500'],
            'checklist' => ['nullable', 'array'],
            'checklist.*.name' => ['nullable', 'string', 'max:255'],
            'checklist.*.status' => ['nullable', Rule::in(['verified', 'missing', 'mismatch'])],
            'checklist.*.remark' => ['nullable', 'string', 'max:500'],
        ];
    }
}
