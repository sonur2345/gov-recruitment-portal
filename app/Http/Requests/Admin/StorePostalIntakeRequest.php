<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePostalIntakeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'post_id' => ['required', 'integer', 'exists:posts,id'],
            'candidate_name' => ['required', 'string', 'max:255'],
            'candidate_email' => ['nullable', 'email', 'max:255'],
            'candidate_mobile' => ['nullable', 'string', 'max:20'],
            'dob' => ['required', 'date'],
            'gender' => ['required', 'string', 'max:20'],
            'category' => ['required', 'string', 'max:50'],
            'sub_reservation' => ['nullable', 'string', 'max:120'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'inward_no' => ['required', 'string', 'max:120'],
            'inward_date' => ['required', 'date'],
            'postal_received_at' => ['nullable', 'date'],
            'dd_number' => ['required', 'string', 'max:120'],
            'bank_name' => ['required', 'string', 'max:255'],
            'bank_branch' => ['nullable', 'string', 'max:255'],
            'dd_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'envelope_scan' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'dd_scan' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }
}
