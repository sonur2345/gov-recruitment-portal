<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGrievanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['open', 'in_progress', 'resolved', 'closed'])],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high'])],
            'response' => ['nullable', 'string'],
        ];
    }
}
