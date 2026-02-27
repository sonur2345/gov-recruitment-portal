<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNotificationRequest extends FormRequest
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
        $notificationId = $this->route('notification')?->id;

        return [
            'advertisement_no' => ['nullable', 'string', 'max:120', Rule::unique('notifications', 'advertisement_no')->ignore($notificationId)],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'postal_address' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'last_date_time' => ['nullable', 'date', 'after_or_equal:start_date'],
            'dd_payee_text' => ['nullable', 'string', 'max:255'],
            'fee_last_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'exam_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'helpdesk_phone' => ['nullable', 'string', 'max:50'],
            'helpdesk_email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', Rule::in(['draft', 'published', 'closed'])],
            'pdf' => ['nullable', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:5120'],
        ];
    }
}
