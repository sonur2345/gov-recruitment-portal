@extends('layouts.admin')

@section('title', 'Evaluate Skill Test')
@section('page_title', 'Evaluate Skill Test')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.skill-tests.index') }}"><x-official.button variant="outline">Back</x-official.button></a>
    </div>

    <x-official.form :title="$application->application_no">
        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Candidate</p>
                <p class="text-sm">{{ $application->user?->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Post</p>
                <p class="text-sm">{{ $application->post?->name ?? $application->post?->title ?? $application->post?->post_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Minimum Qualifying</p>
                <p class="text-sm">40%</p>
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
                <p class="text-sm text-slate-600">No documents uploaded.</p>
            @endforelse
        </div>
    </x-official.form>

    <x-official.form title="Evaluation" class="mt-4">
        <form method="POST" action="{{ route('admin.skill-tests.store', $application) }}" class="space-y-4">
            @csrf
            <div class="grid gap-4 md:grid-cols-2">
                <x-official.input id="marks-input" name="marks" label="Marks (0-100)" type="number" step="0.01" :value="old('marks')" />
                <div class="flex items-center pt-6">
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-800">
                        <input id="is_absent" type="checkbox" name="is_absent" value="1" @checked(old('is_absent')) class="border-[var(--gov-border)]">
                        Mark Absent
                    </label>
                </div>
            </div>

            <div>
                <label for="remark" class="mb-1 block text-sm font-semibold text-slate-800">Remark (Optional)</label>
                <textarea id="remark" name="remark" rows="2" class="w-full border border-[var(--gov-border)] px-3 py-2 text-sm">{{ old('remark') }}</textarea>
            </div>

            @if ($errors->any())
                <div class="border border-red-300 bg-red-50 px-3 py-2 text-xs text-red-800">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <x-official.button type="submit">Save Evaluation</x-official.button>
        </form>
    </x-official.form>
@endsection

@push('scripts')
    <script>
        (function () {
            const absentCheckbox = document.getElementById('is_absent');
            const marksInput = document.getElementById('marks-input');

            function toggleMarks() {
                if (!absentCheckbox || !marksInput) {
                    return;
                }

                if (absentCheckbox.checked) {
                    marksInput.value = '';
                    marksInput.setAttribute('disabled', 'disabled');
                } else {
                    marksInput.removeAttribute('disabled');
                }
            }

            absentCheckbox?.addEventListener('change', toggleMarks);
            toggleMarks();
        })();
    </script>
@endpush
