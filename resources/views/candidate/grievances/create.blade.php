@extends('layouts.gov')

@section('title', 'Raise Grievance')
@section('page_title', 'Raise Grievance')

@section('content')
    <div class="mb-4">
        <a href="{{ route('grievances.index') }}" class="text-sm font-semibold text-[var(--gov-primary)] hover:underline">Back to list</a>
    </div>

    <form action="{{ route('grievances.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 rounded border border-slate-200 bg-white p-4">
        @csrf

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-800">Application (optional)</label>
                <select name="application_id" class="w-full border border-[var(--gov-border)] px-3 py-2 text-sm">
                    <option value="">General / Not specific</option>
                    @foreach ($applications as $application)
                        <option value="{{ $application->id }}" @selected(old('application_id') == $application->id)>
                            {{ $application->application_no }} - {{ $application->post?->name ?? $application->post?->code ?? 'Post' }}
                        </option>
                    @endforeach
                </select>
                @error('application_id') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-800">Priority</label>
                <select name="priority" class="w-full border border-[var(--gov-border)] px-3 py-2 text-sm">
                    @foreach (['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'] as $key => $label)
                        <option value="{{ $key }}" @selected(old('priority', 'medium') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('priority') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-800">Subject <span class="text-red-700">*</span></label>
            <input type="text" name="subject" value="{{ old('subject') }}" required class="w-full border border-[var(--gov-border)] px-3 py-2 text-sm">
            @error('subject') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-800">Description <span class="text-red-700">*</span></label>
            <textarea name="description" rows="4" required class="w-full border border-[var(--gov-border)] px-3 py-2 text-sm">{{ old('description') }}</textarea>
            @error('description') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-800">Attachments (optional)</label>
            <input type="file" name="attachments[]" multiple class="w-full border border-[var(--gov-border)] px-3 py-2 text-sm">
            @error('attachments.*') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
        </div>

        @if ($errors->any())
            <div class="border border-red-300 bg-red-50 px-3 py-2 text-xs text-red-800">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="flex flex-wrap gap-2">
            <button type="submit" class="border border-[var(--gov-primary)] bg-[var(--gov-primary)] px-3 py-2 text-sm font-semibold text-white hover:bg-[#0d243f]">
                Submit Grievance
            </button>
            <a href="{{ route('grievances.index') }}" class="border border-[var(--gov-border)] px-3 py-2 text-sm font-semibold text-slate-700">Cancel</a>
        </div>
    </form>
@endsection
