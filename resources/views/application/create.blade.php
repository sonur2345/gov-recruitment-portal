@extends('layouts.gov')

@section('title', 'APPLICATION FORM | GOVERNMENT RECRUITMENT PORTAL')
@section('meta_description', 'Online recruitment application form as per official SRS structure.')

@section('content')
    <section class="mb-4 border border-[var(--gov-navy)] bg-white px-4 py-3">
        <h2 class="text-base font-bold uppercase tracking-wide text-[var(--gov-navy)]">Recruitment Application Form</h2>
        <p class="mt-1 text-xs text-slate-700">Complete all sections carefully and verify details before final submission.</p>
    </section>

    @if ($errors->any())
        <section class="mb-4 border border-[var(--gov-danger)] bg-red-50 px-4 py-3 text-xs text-[var(--gov-danger)]" role="alert">
            <p class="font-semibold">Please correct the following errors:</p>
            <ul class="mt-1 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    <form id="application-form" method="POST" action="{{ route('application.store') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <x-official.form title="Section 1: Post Selection">
            <div>
                <label for="post_id" class="mb-1 block text-sm font-semibold text-slate-800">
                    Post Name <span class="text-[var(--gov-danger)]">*</span>
                </label>
                <select
                    id="post_id"
                    name="post_id"
                    required
                    class="w-full border border-[var(--gov-border)] bg-white px-3 py-2 text-sm outline-none focus:border-[var(--gov-navy)] focus:ring-1 focus:ring-[var(--gov-navy)]"
                >
                    <option value="">Select Post</option>
                    @foreach ($posts as $post)
                        @php
                            $postLabel = $post->name ?? $post->title ?? $post->post_name ?? ('Post #' . $post->id);
                            $isSelected = (string) old('post_id', $selectedPostId) === (string) $post->id;
                        @endphp
                        <option value="{{ $post->id }}" @selected($isSelected)>
                            {{ $postLabel }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-slate-600">Post name is auto-populated when selected from a recruitment notification.</p>
            </div>
        </x-official.form>

        <x-official.form title="Candidate Profile (Auto Filled from Registration)">
            <x-official.table :headers="['Field', 'Value']">
                <tr>
                    <td class="border border-[var(--gov-border)] px-3 py-2 text-xs font-semibold">Full Name</td>
                    <td class="border border-[var(--gov-border)] px-3 py-2 text-xs">{{ $candidateProfile?->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="border border-[var(--gov-border)] px-3 py-2 text-xs font-semibold">Father Name</td>
                    <td class="border border-[var(--gov-border)] px-3 py-2 text-xs">{{ $candidateProfile?->father_name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="border border-[var(--gov-border)] px-3 py-2 text-xs font-semibold">Date of Birth</td>
                    <td class="border border-[var(--gov-border)] px-3 py-2 text-xs">{{ $candidateProfile?->dob?->format('d-m-Y') ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="border border-[var(--gov-border)] px-3 py-2 text-xs font-semibold">Mobile</td>
                    <td class="border border-[var(--gov-border)] px-3 py-2 text-xs">{{ $candidateProfile?->mobile ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="border border-[var(--gov-border)] px-3 py-2 text-xs font-semibold">Address</td>
                    <td class="border border-[var(--gov-border)] px-3 py-2 text-xs">{{ $candidateProfile?->correspondence_address ?? $candidateProfile?->permanent_address ?? '-' }}</td>
                </tr>
            </x-official.table>
        </x-official.form>

        <x-official.form title="Section 2: Educational Qualification">
            <div class="grid gap-4 md:grid-cols-2">
                <x-official.form-field name="education[0][exam]" label="Qualification Name" :required="true" :value="old('education.0.exam')" />
                <x-official.form-field name="education[0][board_university]" label="Board/University" :required="true" :value="old('education.0.board_university')" />
                <x-official.form-field name="education[0][year]" label="Year of Passing" type="number" :required="true" min="1900" max="{{ now()->year }}" :value="old('education.0.year')" />
                <x-official.form-field name="education[0][percentage]" label="Percentage" type="number" step="0.01" min="0" max="100" :required="true" :value="old('education.0.percentage')" />

                <div class="md:col-span-2">
                    <label for="education_certificate" class="mb-1 block text-sm font-semibold text-slate-800">
                        Upload Certificate <span class="text-[var(--gov-danger)]">*</span>
                    </label>
                    <input id="education_certificate" name="education_certificate" type="file" required accept=".pdf,.jpg,.jpeg,.png" class="w-full border border-[var(--gov-border)] bg-white px-3 py-2 text-sm">
                </div>
            </div>

            <input type="hidden" name="education[0][subject]" value="{{ old('education.0.subject', 'General') }}">
        </x-official.form>

        <x-official.form title="Section 3: Experience Details">
            <div class="grid gap-4 md:grid-cols-2">
                <x-official.form-field name="experience[0][organization]" label="Organization Name" :value="old('experience.0.organization')" />
                <x-official.form-field name="experience[0][post]" label="Designation" :value="old('experience.0.post')" />
                <x-official.form-field name="experience[0][from_date]" label="From Date" type="date" :value="old('experience.0.from_date')" />
                <x-official.form-field name="experience[0][to_date]" label="To Date" type="date" :value="old('experience.0.to_date')" />
                <x-official.form-field id="experience_months" name="experience[0][total_months]" label="Total Experience (Months)" type="number" :value="old('experience.0.total_months')" readonly />

                <div class="md:col-span-2">
                    <label for="experience_certificate" class="mb-1 block text-sm font-semibold text-slate-800">Upload Experience Certificate</label>
                    <input id="experience_certificate" name="experience_certificate" type="file" accept=".pdf,.jpg,.jpeg,.png" class="w-full border border-[var(--gov-border)] bg-white px-3 py-2 text-sm">
                </div>
            </div>
        </x-official.form>

        <x-official.form title="Section 4: Category & Reservation Details">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label for="category" class="mb-1 block text-sm font-semibold text-slate-800">
                        Category <span class="text-[var(--gov-danger)]">*</span>
                    </label>
                    <select id="category" name="category" required class="w-full border border-[var(--gov-border)] bg-white px-3 py-2 text-sm outline-none focus:border-[var(--gov-navy)] focus:ring-1 focus:ring-[var(--gov-navy)]">
                        <option value="">Select Category</option>
                        <option value="GEN" @selected(old('category') === 'GEN')>GEN</option>
                        <option value="OBC" @selected(old('category') === 'OBC')>OBC</option>
                        <option value="SC" @selected(old('category') === 'SC')>SC</option>
                        <option value="ST" @selected(old('category') === 'ST')>ST</option>
                        <option value="EWS" @selected(old('category') === 'EWS')>EWS</option>
                    </select>
                </div>

                <div>
                    <label for="pwbd_status" class="mb-1 block text-sm font-semibold text-slate-800">PwBD Status</label>
                    <select id="pwbd_status" name="pwbd_status" class="w-full border border-[var(--gov-border)] bg-white px-3 py-2 text-sm outline-none focus:border-[var(--gov-navy)] focus:ring-1 focus:ring-[var(--gov-navy)]">
                        <option value="no" @selected(old('pwbd_status', 'no') === 'no')>No</option>
                        <option value="yes" @selected(old('pwbd_status') === 'yes')>Yes</option>
                    </select>
                </div>

                <div>
                    <label for="ex_serviceman" class="mb-1 block text-sm font-semibold text-slate-800">Ex-Serviceman</label>
                    <select id="ex_serviceman" name="ex_serviceman" class="w-full border border-[var(--gov-border)] bg-white px-3 py-2 text-sm outline-none focus:border-[var(--gov-navy)] focus:ring-1 focus:ring-[var(--gov-navy)]">
                        <option value="no" @selected(old('ex_serviceman', 'no') === 'no')>No</option>
                        <option value="yes" @selected(old('ex_serviceman') === 'yes')>Yes</option>
                    </select>
                </div>

                <x-official.form-field name="sub_reservation" label="Reservation Details (if applicable)" :value="old('sub_reservation')" />

                <div class="md:col-span-2">
                    <label for="reservation_certificate" class="mb-1 block text-sm font-semibold text-slate-800">Upload Relevant Certificate</label>
                    <input id="reservation_certificate" name="reservation_certificate" type="file" accept=".pdf,.jpg,.jpeg,.png" class="w-full border border-[var(--gov-border)] bg-white px-3 py-2 text-sm">
                </div>
            </div>
        </x-official.form>

        <x-official.form title="Section 5: Payment Details">
            <div class="grid gap-4 md:grid-cols-2">
                <x-official.form-field id="fee_amount" name="amount" label="Fee Amount (Auto Calculated)" type="number" step="0.01" :required="true" :value="old('amount', 500)" readonly />
                <x-official.form-field name="dd_number" label="Demand Draft Number" :required="true" :value="old('dd_number')" />
                <x-official.form-field name="bank_name" label="Bank Name" :required="true" :value="old('bank_name')" />
                <x-official.form-field name="bank_branch" label="Branch" :required="true" :value="old('bank_branch')" />
                <x-official.form-field name="dd_date" label="DD Date" type="date" :required="true" :value="old('dd_date')" />

                <div class="md:col-span-2">
                    <label for="dd_copy" class="mb-1 block text-sm font-semibold text-slate-800">
                        Upload DD Copy <span class="text-[var(--gov-danger)]">*</span>
                    </label>
                    <input id="dd_copy" name="dd_copy" type="file" required accept=".pdf,.jpg,.jpeg,.png" class="w-full border border-[var(--gov-border)] bg-white px-3 py-2 text-sm">
                </div>
            </div>
        </x-official.form>

        <x-official.form title="Section 6: Upload Documents">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label for="photo" class="mb-1 block text-sm font-semibold text-slate-800">Photograph (Passport Size) <span class="text-[var(--gov-danger)]">*</span></label>
                    <input id="photo" name="documents[photo]" type="file" required accept=".jpg,.jpeg,.png" class="w-full border border-[var(--gov-border)] bg-white px-3 py-2 text-sm">
                </div>
                <div>
                    <label for="signature" class="mb-1 block text-sm font-semibold text-slate-800">Signature <span class="text-[var(--gov-danger)]">*</span></label>
                    <input id="signature" name="documents[signature]" type="file" required accept=".jpg,.jpeg,.png" class="w-full border border-[var(--gov-border)] bg-white px-3 py-2 text-sm">
                </div>
                <div>
                    <label for="id_proof" class="mb-1 block text-sm font-semibold text-slate-800">ID Proof <span class="text-[var(--gov-danger)]">*</span></label>
                    <input id="id_proof" name="documents[id_proof]" type="file" required accept=".pdf,.jpg,.jpeg,.png" class="w-full border border-[var(--gov-border)] bg-white px-3 py-2 text-sm">
                </div>
                <div>
                    <label for="caste_certificate" class="mb-1 block text-sm font-semibold text-slate-800">Caste Certificate (If Applicable)</label>
                    <input id="caste_certificate" name="caste_certificate" type="file" accept=".pdf,.jpg,.jpeg,.png" class="w-full border border-[var(--gov-border)] bg-white px-3 py-2 text-sm">
                </div>
            </div>
        </x-official.form>

        <x-official.form id="preview-panel" title="Section 7: Final Preview">
            <div id="preview-content" class="hidden space-y-3">
                <x-official.table :headers="['Field', 'Provided Value']">
                    <tr>
                        <td class="border border-[var(--gov-border)] px-3 py-2 text-xs font-semibold text-slate-700">Post Name</td>
                        <td id="preview_post_name" class="border border-[var(--gov-border)] px-3 py-2 text-xs text-slate-900">-</td>
                    </tr>
                    <tr>
                        <td class="border border-[var(--gov-border)] px-3 py-2 text-xs font-semibold text-slate-700">Qualification Name</td>
                        <td id="preview_qualification_name" class="border border-[var(--gov-border)] px-3 py-2 text-xs text-slate-900">-</td>
                    </tr>
                    <tr>
                        <td class="border border-[var(--gov-border)] px-3 py-2 text-xs font-semibold text-slate-700">Organization Name</td>
                        <td id="preview_organization_name" class="border border-[var(--gov-border)] px-3 py-2 text-xs text-slate-900">-</td>
                    </tr>
                    <tr>
                        <td class="border border-[var(--gov-border)] px-3 py-2 text-xs font-semibold text-slate-700">Category</td>
                        <td id="preview_category" class="border border-[var(--gov-border)] px-3 py-2 text-xs text-slate-900">-</td>
                    </tr>
                    <tr>
                        <td class="border border-[var(--gov-border)] px-3 py-2 text-xs font-semibold text-slate-700">Fee Amount</td>
                        <td id="preview_fee_amount" class="border border-[var(--gov-border)] px-3 py-2 text-xs text-slate-900">-</td>
                    </tr>
                    <tr>
                        <td class="border border-[var(--gov-border)] px-3 py-2 text-xs font-semibold text-slate-700">DD Number</td>
                        <td id="preview_dd_number" class="border border-[var(--gov-border)] px-3 py-2 text-xs text-slate-900">-</td>
                    </tr>
                </x-official.table>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button type="button" id="show-preview" class="border border-[var(--gov-maroon)] bg-[var(--gov-maroon)] px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white hover:bg-[#651924]">
                    Show Preview
                </button>
                <button type="button" id="edit-form" class="hidden border border-[var(--gov-navy)] px-4 py-2 text-xs font-semibold uppercase tracking-wide text-[var(--gov-navy)] hover:bg-slate-100">
                    Edit Form
                </button>
                <button type="submit" id="final-submit" class="hidden border border-[var(--gov-navy)] bg-[var(--gov-navy)] px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white hover:bg-[#0d243f]">
                    Final Submit
                </button>
                <a href="{{ route('application.preview') }}" class="border border-[var(--gov-border)] px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-700 hover:bg-slate-100">
                    Open Preview Page
                </a>
            </div>
        </x-official.form>

        <input type="hidden" name="dob" value="{{ old('dob', $candidateProfile?->dob?->toDateString() ?? '') }}">
        <input type="hidden" name="gender" value="{{ old('gender', strtolower((string) ($candidateProfile?->gender ?? 'other'))) }}">
        <input type="hidden" name="father_name" value="{{ old('father_name', $candidateProfile?->father_name ?? '') }}">
        <input type="hidden" name="mobile" value="{{ old('mobile', $candidateProfile?->mobile ?? '') }}">
        <input type="hidden" name="address" value="{{ old('address', $candidateProfile?->correspondence_address ?? $candidateProfile?->permanent_address ?? '') }}">
        <input type="hidden" name="aadhar_number" value="{{ old('aadhar_number', $candidateProfile?->aadhaar_number ?? '') }}">
        <input type="hidden" name="bank_account_number" value="{{ old('bank_account_number') }}">
        <input type="hidden" name="ifsc_code" value="{{ old('ifsc_code') }}">
    </form>
@endsection

@push('scripts')
    <script>
        (function () {
            const categoryEl = document.getElementById('category');
            const pwbdEl = document.getElementById('pwbd_status');
            const feeAmountEl = document.getElementById('fee_amount');
            const fromDateEl = document.querySelector('input[name="experience[0][from_date]"]');
            const toDateEl = document.querySelector('input[name="experience[0][to_date]"]');
            const experienceMonthsEl = document.getElementById('experience_months');
            const showPreviewButton = document.getElementById('show-preview');
            const editFormButton = document.getElementById('edit-form');
            const finalSubmitButton = document.getElementById('final-submit');
            const previewContent = document.getElementById('preview-content');

            function calculateFee() {
                const category = (categoryEl?.value || '').toUpperCase();
                const isPwbd = pwbdEl?.value === 'yes';
                let fee = 500;

                if (category === 'SC' || category === 'ST' || isPwbd) {
                    fee = 0;
                }

                if (feeAmountEl) {
                    feeAmountEl.value = fee.toFixed(2);
                }
            }

            function calculateExperienceMonths() {
                if (!fromDateEl || !toDateEl || !experienceMonthsEl) {
                    return;
                }

                if (!fromDateEl.value || !toDateEl.value) {
                    experienceMonthsEl.value = '';
                    return;
                }

                const fromDate = new Date(fromDateEl.value);
                const toDate = new Date(toDateEl.value);

                if (Number.isNaN(fromDate.getTime()) || Number.isNaN(toDate.getTime()) || toDate < fromDate) {
                    experienceMonthsEl.value = '';
                    return;
                }

                const months = Math.max(0, (toDate.getFullYear() - fromDate.getFullYear()) * 12 + (toDate.getMonth() - fromDate.getMonth()));
                experienceMonthsEl.value = String(months);
            }

            function setPreviewValue(id, value) {
                const target = document.getElementById(id);
                if (target) {
                    target.textContent = value && value.trim() !== '' ? value : '-';
                }
            }

            function openPreview() {
                const postSelect = document.getElementById('post_id');
                const selectedPost = postSelect ? postSelect.options[postSelect.selectedIndex]?.text || '' : '';

                setPreviewValue('preview_post_name', selectedPost);
                setPreviewValue('preview_qualification_name', document.querySelector('input[name="education[0][exam]"]')?.value || '');
                setPreviewValue('preview_organization_name', document.querySelector('input[name="experience[0][organization]"]')?.value || '');
                setPreviewValue('preview_category', categoryEl?.value || '');
                setPreviewValue('preview_fee_amount', feeAmountEl?.value || '');
                setPreviewValue('preview_dd_number', document.querySelector('input[name="dd_number"]')?.value || '');

                previewContent?.classList.remove('hidden');
                editFormButton?.classList.remove('hidden');
                finalSubmitButton?.classList.remove('hidden');
            }

            function closePreview() {
                previewContent?.classList.add('hidden');
                editFormButton?.classList.add('hidden');
                finalSubmitButton?.classList.add('hidden');
            }

            categoryEl?.addEventListener('change', calculateFee);
            pwbdEl?.addEventListener('change', calculateFee);
            fromDateEl?.addEventListener('change', calculateExperienceMonths);
            toDateEl?.addEventListener('change', calculateExperienceMonths);
            showPreviewButton?.addEventListener('click', openPreview);
            editFormButton?.addEventListener('click', closePreview);

            calculateFee();
            calculateExperienceMonths();
        })();
    </script>
@endpush
