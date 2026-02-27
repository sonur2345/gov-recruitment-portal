@extends('layouts.admin')

@section('title', 'Advertisement Details')
@section('page_title', 'Advertisement Details')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.notifications.index') }}"><x-official.button variant="outline">Back</x-official.button></a>
    </div>

    <x-official.form :title="$notification->title" :description="'Advertisement No: ' . ($notification->advertisement_no ?? ('ADV/' . $notification->id))">
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Start Date</p>
                <p class="text-sm">{{ $notification->start_date?->format('d-m-Y') }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Last Date</p>
                <p class="text-sm">{{ $notification->end_date?->format('d-m-Y') }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Last Date & Time</p>
                <p class="text-sm">{{ $notification->last_date_time?->format('d-m-Y H:i') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Fee Last Date</p>
                <p class="text-sm">{{ $notification->fee_last_date?->format('d-m-Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Exam Date</p>
                <p class="text-sm">{{ $notification->exam_date?->format('d-m-Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">DD Payee Text</p>
                <p class="text-sm">{{ $notification->dd_payee_text ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Helpdesk Phone</p>
                <p class="text-sm">{{ $notification->helpdesk_phone ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Helpdesk Email</p>
                <p class="text-sm">{{ $notification->helpdesk_email ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Status</p>
                <x-official.badge :variant="match ($notification->status) {
                    'published' => 'success',
                    'closed' => 'default',
                    default => 'warning',
                }">{{ $notification->status }}</x-official.badge>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Version</p>
                <p class="text-sm">v{{ $notification->version ?? 1 }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">PDF</p>
                @if ($notification->pdf_path)
                    <a href="{{ $notification->signedPdfUrl() }}" target="_blank" class="text-sm font-semibold text-[var(--gov-primary)] hover:underline">Download / View</a>
                @else
                    <p class="text-sm">Not uploaded</p>
                @endif
            </div>
            <div class="md:col-span-2">
                <p class="text-xs font-semibold uppercase text-slate-600">Postal Address</p>
                <p class="text-sm whitespace-pre-line">{{ $notification->postal_address ?? '-' }}</p>
            </div>
        </div>

        <div class="border-t border-slate-200 pt-3">
            <p class="text-xs font-semibold uppercase text-slate-600">Description</p>
            <p class="mt-1 text-sm text-slate-800 whitespace-pre-line">{{ $notification->description }}</p>
        </div>
    </x-official.form>
@endsection
