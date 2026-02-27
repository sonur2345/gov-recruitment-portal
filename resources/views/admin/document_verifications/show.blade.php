@extends('layouts.admin')

@section('title', 'Document Verification')
@section('page_title', 'Document Verification')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.document-verifications.index') }}"><x-official.button variant="outline">Back</x-official.button></a>
    </div>

    <x-official.form :title="'Application ' . $application->application_no">
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Candidate</p>
                <p class="text-sm">{{ $application->user?->name ?? '-' }} ({{ $application->user?->email ?? '-' }})</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Post</p>
                <p class="text-sm">{{ $application->post?->name ?? $application->post?->title ?? $application->post?->post_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Current Status</p>
                <x-official.badge variant="info">{{ $application->status }}</x-official.badge>
            </div>
        </div>
    </x-official.form>

    <x-official.form title="Uploaded Documents" class="mt-4">
        <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($application->documents as $document)
                <a href="{{ $document->signedDownloadUrl() }}" target="_blank">
                    <x-official.button variant="outline" class="w-full">{{ ucfirst(str_replace('_', ' ', $document->document_type)) }}</x-official.button>
                </a>
            @empty
                <p class="text-sm text-slate-600">No uploaded documents found.</p>
            @endforelse
        </div>
    </x-official.form>

    <x-official.form title="DV Committee Decision" class="mt-4">
        <form method="POST" action="{{ route('admin.document-verifications.store', $application) }}" class="space-y-4">
            @csrf

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label for="status" class="mb-1 block text-sm font-semibold text-slate-800">Status <span class="text-red-700">*</span></label>
                    <select id="status" name="status" required class="w-full border border-[var(--gov-border)] px-3 py-2 text-sm">
                        <option value="">Select</option>
                        <option value="verified" @selected(old('status') === 'verified')>Verified (Final Selected)</option>
                        <option value="provisional" @selected(old('status') === 'provisional')>Provisional</option>
                        <option value="rejected" @selected(old('status') === 'rejected')>Rejected</option>
                    </select>
                </div>
                <x-official.input name="remark" label="Remark (Optional)" :value="old('remark')" maxlength="1500" />
            </div>

            @if ($errors->any())
                <div class="border border-red-300 bg-red-50 px-3 py-2 text-xs text-red-800">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <x-official.button type="submit">Save DV Decision</x-official.button>
        </form>
    </x-official.form>
@endsection
