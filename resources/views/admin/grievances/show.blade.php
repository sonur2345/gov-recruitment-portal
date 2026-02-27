@extends('layouts.admin')

@section('title', 'Grievance Details')
@section('page_title', 'Grievance Details')

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.grievances.index') }}" class="text-xs font-semibold text-[var(--gov-navy)] hover:underline">Back to list</a>
    </div>

    <div class="grid gap-3 lg:grid-cols-[2fr_1fr]">
        <x-admin-card>
            <div class="grid gap-3 md:grid-cols-2">
                <div>
                    <p class="text-[11px] font-semibold uppercase text-slate-500">Ticket</p>
                    <p class="text-sm">#{{ $grievance->id }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase text-slate-500">Candidate</p>
                    <p class="text-sm">{{ $grievance->user?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase text-slate-500">Status</p>
                    <p class="text-sm">{{ ucfirst(str_replace('_', ' ', $grievance->status)) }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase text-slate-500">Priority</p>
                    <p class="text-sm">{{ ucfirst($grievance->priority) }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase text-slate-500">Submitted</p>
                    <p class="text-sm">{{ $grievance->created_at?->format('d-m-Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase text-slate-500">Application</p>
                    <p class="text-sm">{{ $grievance->application?->application_no ?? '-' }}</p>
                </div>
            </div>

            <div class="mt-3 border-t border-slate-200 pt-3">
                <p class="text-[11px] font-semibold uppercase text-slate-500">Subject</p>
                <p class="text-sm">{{ $grievance->subject }}</p>
            </div>
            <div class="mt-3">
                <p class="text-[11px] font-semibold uppercase text-slate-500">Description</p>
                <p class="text-sm whitespace-pre-line">{{ $grievance->description }}</p>
            </div>

            @if ($grievance->documents->isNotEmpty())
                <div class="mt-3 border-t border-slate-200 pt-3">
                    <p class="text-[11px] font-semibold uppercase text-slate-500">Attachments</p>
                    <ul class="mt-2 space-y-1 text-sm">
                        @foreach ($grievance->documents as $doc)
                            <li>
                                <a href="{{ URL::temporarySignedRoute('files.grievances.download', now()->addMinutes(20), ['document' => $doc->id]) }}"
                                   class="text-[var(--gov-navy)] hover:underline">
                                    {{ $doc->original_name ?? 'Attachment' }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </x-admin-card>

        <x-admin-card>
            <form method="POST" action="{{ route('admin.grievances.update', $grievance) }}" class="space-y-3">
                @csrf
                @method('PATCH')

                <div>
                    <label class="text-[11px] font-semibold uppercase text-slate-500">Status</label>
                    <select name="status" class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs">
                        @foreach (['open', 'in_progress', 'resolved', 'closed'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $grievance->status) === $status)>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('status') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-[11px] font-semibold uppercase text-slate-500">Priority</label>
                    <select name="priority" class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs">
                        @foreach (['low', 'medium', 'high'] as $priority)
                            <option value="{{ $priority }}" @selected(old('priority', $grievance->priority) === $priority)>
                                {{ ucfirst($priority) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-[11px] font-semibold uppercase text-slate-500">Response</label>
                    <textarea name="response" rows="4" class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs">{{ old('response', $grievance->response) }}</textarea>
                    @error('response') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
                </div>

                <button class="w-full border border-[var(--gov-navy)] bg-[var(--gov-navy)] px-3 py-2 text-xs font-semibold text-white">Update</button>
            </form>
        </x-admin-card>
    </div>
@endsection
