@extends('layouts.admin')

@section('title', 'Scrutiny Review')
@section('page_title', 'Scrutiny Review')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.scrutiny.index') }}"><x-official.button variant="outline">Back</x-official.button></a>
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
            @if ($scrutiny)
                <div>
                    <p class="text-xs font-semibold uppercase text-slate-600">Current Scrutiny</p>
                    <x-official.badge :variant="in_array($scrutiny->status, ['eligible', 'not_eligible'], true) ? 'default' : 'warning'">{{ $scrutiny->status }}</x-official.badge>
                    <p class="mt-1 text-xs text-slate-600">By {{ $scrutiny->scrutinyOfficer?->name ?? 'Officer' }}</p>
                </div>
            @endif
        </div>
    </x-official.form>

    <div class="mt-4 grid gap-4 lg:grid-cols-2">
        <x-official.form title="Education">
            <x-official.table :headers="['Exam', 'Board', 'Year', 'Percentage']">
                @forelse ($application->educations as $edu)
                    <tr>
                        <td class="border border-slate-300 px-3 py-2 text-xs">{{ $edu->exam }}</td>
                        <td class="border border-slate-300 px-3 py-2 text-xs">{{ $edu->board_university }}</td>
                        <td class="border border-slate-300 px-3 py-2 text-xs">{{ $edu->year }}</td>
                        <td class="border border-slate-300 px-3 py-2 text-xs">{{ $edu->percentage }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="border border-slate-300 px-3 py-2 text-center text-xs">No education records.</td></tr>
                @endforelse
            </x-official.table>
        </x-official.form>

        <x-official.form title="Experience">
            <x-official.table :headers="['Organization', 'Designation', 'Months']">
                @forelse ($application->experiences as $exp)
                    <tr>
                        <td class="border border-slate-300 px-3 py-2 text-xs">{{ $exp->organization }}</td>
                        <td class="border border-slate-300 px-3 py-2 text-xs">{{ $exp->post }}</td>
                        <td class="border border-slate-300 px-3 py-2 text-xs">{{ $exp->total_months }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="border border-slate-300 px-3 py-2 text-center text-xs">No experience records.</td></tr>
                @endforelse
            </x-official.table>
        </x-official.form>
    </div>

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

    @php
        $isFinal = in_array($application->status, ['eligible', 'rejected'], true)
            || ($scrutiny && in_array($scrutiny->status, ['eligible', 'not_eligible'], true));
    @endphp

    <x-official.form title="Scrutiny Decision" class="mt-4">
        @if ($isFinal)
            <div class="border border-amber-300 bg-amber-50 px-3 py-2 text-sm text-amber-900">
                Final scrutiny decision already made. Re-scrutiny is blocked.
            </div>
        @else
            <form method="POST" action="{{ route('admin.scrutiny.update', $application) }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="decision" class="mb-1 block text-sm font-semibold text-slate-800">Decision <span class="text-red-700">*</span></label>
                        <select id="decision" name="decision" required class="w-full border border-[var(--gov-border)] px-3 py-2 text-sm">
                            <option value="">Select</option>
                            <option value="eligible" @selected(old('decision') === 'eligible')>Eligible</option>
                            <option value="not_eligible" @selected(old('decision') === 'not_eligible')>Not Eligible</option>
                            <option value="pending" @selected(old('decision') === 'pending')>Pending</option>
                        </select>
                    </div>
                    <x-official.input name="remark" label="Remark (Optional)" :value="old('remark')" maxlength="2000" />
                </div>

                @if ($errors->any())
                    <div class="border border-red-300 bg-red-50 px-3 py-2 text-xs text-red-800">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <x-official.button type="submit">Save Scrutiny</x-official.button>
            </form>
        @endif
    </x-official.form>
@endsection
