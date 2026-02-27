<?php

namespace App\Http\Requests\Candidate;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
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
            'post_id' => ['required', 'exists:posts,id'],
            'category' => ['required', 'string', 'max:100'],
            'sub_reservation' => ['nullable', 'string', 'max:100'],
            'pwbd_status' => ['nullable', 'in:yes,no'],
            'ex_serviceman' => ['nullable', 'in:yes,no'],
            'dob' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female,other'],
            'father_name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'regex:/^[0-9]{10}$/'],
            'address' => ['required', 'string', 'max:1000'],
            'aadhar_number' => ['nullable', 'regex:/^[0-9]{12}$/'],
            'bank_account_number' => ['nullable', 'regex:/^[0-9]{9,18}$/'],
            'ifsc_code' => ['nullable', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/i'],

            'education' => ['required', 'array', 'min:1'],
            'education.*.exam' => ['required', 'string', 'max:255'],
            'education.*.board_university' => ['required', 'string', 'max:255'],
            'education.*.subject' => ['required', 'string', 'max:255'],
            'education.*.year' => ['required', 'integer', 'min:1900', 'max:2100'],
            'education.*.percentage' => ['required', 'numeric', 'between:0,100'],

            'experience' => ['nullable', 'array'],
            'experience.*.organization' => ['required_with:experience.*.post,experience.*.from_date,experience.*.to_date', 'nullable', 'string', 'max:255'],
            'experience.*.post' => ['required_with:experience.*.organization,experience.*.from_date,experience.*.to_date', 'nullable', 'string', 'max:255'],
            'experience.*.from_date' => ['required_with:experience.*.organization,experience.*.post,experience.*.to_date', 'nullable', 'date'],
            'experience.*.to_date' => ['required_with:experience.*.organization,experience.*.post,experience.*.from_date', 'nullable', 'date'],
            'experience.*.total_months' => ['required_with:experience.*.organization,experience.*.post', 'nullable', 'integer', 'min:0'],

            'dd_number' => ['required', 'string', 'max:50'],
            'bank_name' => ['required', 'string', 'max:255'],
            'bank_branch' => ['required', 'string', 'max:255'],
            'dd_date' => ['required', 'date', 'before_or_equal:today'],
            'amount' => ['required', 'numeric', 'min:0'],

            'education_certificate' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'mimetypes:application/pdf,image/jpeg,image/png', 'max:5120'],
            'experience_certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'mimetypes:application/pdf,image/jpeg,image/png', 'max:5120'],
            'reservation_certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'mimetypes:application/pdf,image/jpeg,image/png', 'max:5120'],
            'dd_copy' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'mimetypes:application/pdf,image/jpeg,image/png', 'max:5120'],
            'documents.photo' => ['required', 'file', 'mimes:jpg,jpeg,png', 'mimetypes:image/jpeg,image/png', 'max:2048'],
            'documents.signature' => ['required', 'file', 'mimes:jpg,jpeg,png', 'mimetypes:image/jpeg,image/png', 'max:1024'],
            'documents.id_proof' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'mimetypes:application/pdf,image/jpeg,image/png', 'max:5120'],
            'caste_certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'mimetypes:application/pdf,image/jpeg,image/png', 'max:5120'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $postId = $this->input('post_id');
            if (!$postId) {
                return;
            }

            $post = Post::query()->with('notification')->find($postId);
            if (!$post || !$post->notification) {
                $validator->errors()->add('post_id', 'Selected post does not have a valid notification.');
                return;
            }

            if (now()->toDateString() > $post->notification->end_date?->toDateString()) {
                $validator->errors()->add('post_id', 'Application last date has passed for this notification.');
            }

            if (!empty($post->notification->fee_last_date) && $this->filled('dd_date')) {
                if ($this->date('dd_date')?->toDateString() > $post->notification->fee_last_date->toDateString()) {
                    $validator->errors()->add('dd_date', 'Demand draft date cannot be after fee last date.');
                }
            }

            $experienceRows = $this->input('experience', []);
            foreach ($experienceRows as $index => $row) {
                $from = $row['from_date'] ?? null;
                $to = $row['to_date'] ?? null;
                if ($from && $to && $to < $from) {
                    $validator->errors()->add("experience.$index.to_date", 'To date must be after or equal to from date.');
                }
            }
        });
    }
}
