@extends('layouts.gov')

@section('title', 'REGISTRATION | GOVERNMENT RECRUITMENT PORTAL')
@section('meta_description', 'Candidate registration form as per official application requirements.')

@section('content')
    <section class="mx-auto max-w-5xl">
        <x-official.form title="Candidate Registration" description="All fields marked with * are mandatory">
            @if ($errors->any())
                <div class="border border-[var(--gov-danger)] bg-red-50 px-3 py-2 text-xs text-[var(--gov-danger)]" role="alert">
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-5" novalidate>
                @csrf

                <x-official.form title="Personal Details">
                    <div class="grid gap-4 md:grid-cols-2">
                        <x-official.form-field name="name" label="Full Name" :required="true" autocomplete="name" />
                        <x-official.form-field name="father_name" label="Father Name" :required="true" />
                        <x-official.form-field name="mother_name" label="Mother Name" :required="true" />
                        <x-official.form-field name="dob" label="Date of Birth" type="date" :required="true" />

                        <div>
                            <label for="gender" class="mb-1 block text-sm font-semibold text-slate-800">Gender <span class="text-[var(--gov-danger)]">*</span></label>
                            <select id="gender" name="gender" required class="w-full border border-[var(--gov-border)] px-3 py-2 text-sm outline-none focus:border-[var(--gov-navy)] focus:ring-1 focus:ring-[var(--gov-navy)]">
                                <option value="">Select Gender</option>
                                <option value="male" @selected(old('gender') === 'male')>Male</option>
                                <option value="female" @selected(old('gender') === 'female')>Female</option>
                                <option value="other" @selected(old('gender') === 'other')>Other</option>
                            </select>
                        </div>

                        <div>
                            <label for="category" class="mb-1 block text-sm font-semibold text-slate-800">Category <span class="text-[var(--gov-danger)]">*</span></label>
                            <select id="category" name="category" required class="w-full border border-[var(--gov-border)] px-3 py-2 text-sm outline-none focus:border-[var(--gov-navy)] focus:ring-1 focus:ring-[var(--gov-navy)]">
                                <option value="">Select Category</option>
                                <option value="GEN" @selected(old('category') === 'GEN')>GEN</option>
                                <option value="OBC" @selected(old('category') === 'OBC')>OBC</option>
                                <option value="SC" @selected(old('category') === 'SC')>SC</option>
                                <option value="ST" @selected(old('category') === 'ST')>ST</option>
                                <option value="EWS" @selected(old('category') === 'EWS')>EWS</option>
                            </select>
                        </div>

                        <div>
                            <label for="marital_status" class="mb-1 block text-sm font-semibold text-slate-800">Marital Status <span class="text-[var(--gov-danger)]">*</span></label>
                            <select id="marital_status" name="marital_status" required class="w-full border border-[var(--gov-border)] px-3 py-2 text-sm outline-none focus:border-[var(--gov-navy)] focus:ring-1 focus:ring-[var(--gov-navy)]">
                                <option value="">Select Status</option>
                                <option value="single" @selected(old('marital_status') === 'single')>Single</option>
                                <option value="married" @selected(old('marital_status') === 'married')>Married</option>
                                <option value="widowed" @selected(old('marital_status') === 'widowed')>Widowed</option>
                            </select>
                        </div>

                        <x-official.form-field name="nationality" label="Nationality" :required="true" value="Indian" />
                    </div>
                </x-official.form>

                <x-official.form title="Contact Details">
                    <div class="grid gap-4 md:grid-cols-2">
                        <x-official.form-field name="mobile" label="Mobile Number" type="tel" :required="true" autocomplete="tel" />
                        <x-official.form-field name="email" label="Email Address" type="email" :required="true" autocomplete="username" />

                        <div class="md:col-span-2">
                            <label for="correspondence_address" class="mb-1 block text-sm font-semibold text-slate-800">
                                Correspondence Address <span class="text-[var(--gov-danger)]">*</span>
                            </label>
                            <textarea id="correspondence_address" name="correspondence_address" rows="2" required class="w-full border border-[var(--gov-border)] px-3 py-2 text-sm outline-none focus:border-[var(--gov-navy)] focus:ring-1 focus:ring-[var(--gov-navy)]">{{ old('correspondence_address') }}</textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label for="permanent_address" class="mb-1 block text-sm font-semibold text-slate-800">
                                Permanent Address <span class="text-[var(--gov-danger)]">*</span>
                            </label>
                            <textarea id="permanent_address" name="permanent_address" rows="2" required class="w-full border border-[var(--gov-border)] px-3 py-2 text-sm outline-none focus:border-[var(--gov-navy)] focus:ring-1 focus:ring-[var(--gov-navy)]">{{ old('permanent_address') }}</textarea>
                        </div>
                    </div>
                </x-official.form>

                <x-official.form title="Identity Details">
                    <div class="grid gap-4 md:grid-cols-2">
                        <x-official.form-field name="aadhaar_number" label="Aadhaar Number" :required="true" maxlength="12" />
                        <div>
                            <label for="id_proof_upload" class="mb-1 block text-sm font-semibold text-slate-800">ID Proof Upload <span class="text-[var(--gov-danger)]">*</span></label>
                            <input id="id_proof_upload" name="id_proof_upload" type="file" required accept=".pdf,.jpg,.jpeg,.png" class="w-full border border-[var(--gov-border)] bg-white px-3 py-2 text-sm">
                        </div>
                    </div>
                </x-official.form>

                <x-official.form title="Password Setup">
                    <div class="grid gap-4 md:grid-cols-2">
                        <x-official.form-field name="password" label="Password" type="password" :required="true" autocomplete="new-password" />
                        <x-official.form-field name="password_confirmation" label="Confirm Password" type="password" :required="true" autocomplete="new-password" />
                    </div>
                </x-official.form>

                <div class="border border-[var(--gov-border)] bg-slate-50 px-4 py-3">
                    <label for="declaration" class="inline-flex items-start gap-2 text-sm text-slate-800">
                        <input
                            id="declaration"
                            name="declaration"
                            type="checkbox"
                            value="1"
                            required
                            class="mt-0.5 border-[var(--gov-border)] text-[var(--gov-navy)] focus:ring-[var(--gov-navy)]"
                        >
                        <span>
                            I hereby declare that all information furnished by me is true, complete, and correct to the best of my knowledge.
                        </span>
                    </label>
                </div>

                <button type="submit" class="w-full border border-[var(--gov-navy)] bg-[var(--gov-navy)] px-4 py-2 text-sm font-semibold uppercase tracking-wide text-white hover:bg-[#0d243f]">
                    Submit Registration
                </button>
            </form>

            <p class="text-center text-xs text-slate-700">
                Already registered?
                <a href="{{ route('login') }}" class="font-semibold text-[var(--gov-navy)] hover:underline">Login Here</a>
            </p>
        </x-official.form>
    </section>
@endsection
