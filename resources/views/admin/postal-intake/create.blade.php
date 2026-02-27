@extends('layouts.admin')

@section('title', 'New Postal Intake')
@section('page_title', 'New Postal Intake')

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.postal-intake.index') }}" class="text-xs font-semibold text-[var(--gov-navy)] hover:underline">Back to list</a>
    </div>

    <form action="{{ route('admin.postal-intake.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 rounded border border-slate-200 bg-white p-4">
        @csrf

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="text-[11px] font-semibold uppercase text-slate-500">Post <span class="text-red-600">*</span></label>
                <select name="post_id" required class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs">
                    <option value="">Select post</option>
                    @foreach ($posts as $post)
                        <option value="{{ $post->id }}" @selected(old('post_id') == $post->id)>
                            {{ $post->name ?? $post->title ?? $post->post_name ?? ('Post #' . $post->id) }}
                        </option>
                    @endforeach
                </select>
                @error('post_id') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-[11px] font-semibold uppercase text-slate-500">Inward No <span class="text-red-600">*</span></label>
                <input type="text" name="inward_no" value="{{ old('inward_no') }}" required class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs">
                @error('inward_no') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-[11px] font-semibold uppercase text-slate-500">Inward Date <span class="text-red-600">*</span></label>
                <input type="date" name="inward_date" value="{{ old('inward_date') }}" required class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs">
                @error('inward_date') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-[11px] font-semibold uppercase text-slate-500">Received At</label>
                <input type="datetime-local" name="postal_received_at" value="{{ old('postal_received_at') }}" class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs">
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="text-[11px] font-semibold uppercase text-slate-500">Candidate Name <span class="text-red-600">*</span></label>
                <input type="text" name="candidate_name" value="{{ old('candidate_name') }}" required class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs">
                @error('candidate_name') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-[11px] font-semibold uppercase text-slate-500">Father Name</label>
                <input type="text" name="father_name" value="{{ old('father_name') }}" class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs">
            </div>
            <div>
                <label class="text-[11px] font-semibold uppercase text-slate-500">Email</label>
                <input type="email" name="candidate_email" value="{{ old('candidate_email') }}" class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs">
            </div>
            <div>
                <label class="text-[11px] font-semibold uppercase text-slate-500">Mobile</label>
                <input type="text" name="candidate_mobile" value="{{ old('candidate_mobile') }}" class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs">
            </div>
            <div>
                <label class="text-[11px] font-semibold uppercase text-slate-500">Date of Birth <span class="text-red-600">*</span></label>
                <input type="date" name="dob" value="{{ old('dob') }}" required class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs">
            </div>
            <div>
                <label class="text-[11px] font-semibold uppercase text-slate-500">Gender <span class="text-red-600">*</span></label>
                <select name="gender" required class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs">
                    <option value="">Select</option>
                    @foreach (['Male', 'Female', 'Other'] as $gender)
                        <option value="{{ $gender }}" @selected(old('gender') === $gender)>{{ $gender }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[11px] font-semibold uppercase text-slate-500">Category <span class="text-red-600">*</span></label>
                <input type="text" name="category" value="{{ old('category') }}" required class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs">
            </div>
            <div>
                <label class="text-[11px] font-semibold uppercase text-slate-500">Sub Reservation</label>
                <input type="text" name="sub_reservation" value="{{ old('sub_reservation') }}" class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs">
            </div>
        </div>

        <div>
            <label class="text-[11px] font-semibold uppercase text-slate-500">Address</label>
            <textarea name="address" rows="2" class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs">{{ old('address') }}</textarea>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <label class="text-[11px] font-semibold uppercase text-slate-500">DD Number <span class="text-red-600">*</span></label>
                <input type="text" name="dd_number" value="{{ old('dd_number') }}" required class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs">
                @error('dd_number') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-[11px] font-semibold uppercase text-slate-500">Bank Name <span class="text-red-600">*</span></label>
                <input type="text" name="bank_name" value="{{ old('bank_name') }}" required class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs">
                @error('bank_name') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-[11px] font-semibold uppercase text-slate-500">Bank Branch</label>
                <input type="text" name="bank_branch" value="{{ old('bank_branch') }}" class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs">
            </div>
            <div>
                <label class="text-[11px] font-semibold uppercase text-slate-500">DD Date <span class="text-red-600">*</span></label>
                <input type="date" name="dd_date" value="{{ old('dd_date') }}" required class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs">
                @error('dd_date') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-[11px] font-semibold uppercase text-slate-500">Amount <span class="text-red-600">*</span></label>
                <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" required class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs">
                @error('amount') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="text-[11px] font-semibold uppercase text-slate-500">Envelope Scan</label>
                <input type="file" name="envelope_scan" class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs">
            </div>
            <div>
                <label class="text-[11px] font-semibold uppercase text-slate-500">DD Scan</label>
                <input type="file" name="dd_scan" class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs">
            </div>
        </div>

        @if ($errors->any())
            <div class="border border-red-300 bg-red-50 px-3 py-2 text-xs text-red-800">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="flex flex-wrap gap-2">
            <button type="submit" class="border border-[var(--gov-navy)] bg-[var(--gov-navy)] px-3 py-2 text-xs font-semibold text-white">
                Save Postal Intake
            </button>
            <a href="{{ route('admin.postal-intake.index') }}" class="border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700">Cancel</a>
        </div>
    </form>
@endsection
