@extends('layouts.gov')

@section('title', 'Grievance Details')
@section('page_title', 'Grievance Details')

@section('content')
    <div class="mb-4">
        <a href="{{ route('grievances.index') }}" class="text-sm font-semibold text-[var(--gov-primary)] hover:underline">Back to list</a>
    </div>

    <div class="space-y-4 rounded border border-slate-200 bg-white p-4">
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Subject</p>
                <p class="text-sm">{{ $grievance->subject }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Status</p>
                <p class="text-sm">{{ ucfirst($grievance->status) }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Priority</p>
                <p class="text-sm">{{ ucfirst($grievance->priority) }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Submitted On</p>
                <p class="text-sm">{{ $grievance->created_at?->format('d-m-Y H:i') }}</p>
            </div>
        </div>

        <div>
            <p class="text-xs font-semibold uppercase text-slate-600">Description</p>
            <p class="text-sm whitespace-pre-line">{{ $grievance->description }}</p>
        </div>

        @if ($grievance->response)
            <div class="border-t border-slate-200 pt-3">
                <p class="text-xs font-semibold uppercase text-slate-600">Admin Response</p>
                <p class="text-sm whitespace-pre-line">{{ $grievance->response }}</p>
            </div>
        @endif

        @if ($grievance->documents->isNotEmpty())
            <div class="border-t border-slate-200 pt-3">
                <p class="text-xs font-semibold uppercase text-slate-600">Attachments</p>
                <ul class="mt-2 space-y-1 text-sm">
                    @foreach ($grievance->documents as $doc)
                        <li>
                            <a href="{{ URL::temporarySignedRoute('files.grievances.download', now()->addMinutes(20), ['document' => $doc->id]) }}"
                               class="text-[var(--gov-primary)] hover:underline">
                                {{ $doc->original_name ?? 'Attachment' }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endsection
