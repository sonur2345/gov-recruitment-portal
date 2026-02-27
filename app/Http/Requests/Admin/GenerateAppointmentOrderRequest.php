<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GenerateAppointmentOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'office_address' => ['required', 'string', 'max:1200'],
            'signature_name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
