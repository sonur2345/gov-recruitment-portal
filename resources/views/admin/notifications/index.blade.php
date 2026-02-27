@extends('layouts.admin')

@section('title', 'Advertisement Management')
@section('page_title', 'Advertisement Management')

@section('content')
    <div class="mb-4 flex justify-end">
        <a href="{{ route('admin.notifications.create') }}">
            <x-official.button>Create Advertisement</x-official.button>
        </a>
    </div>

    <x-official.table :headers="['Advertisement No', 'Title', 'Start Date', 'Last Date', 'Status', 'PDF', 'Actions']">
        @forelse ($notifications as $notification)
            @php
                $status = strtolower((string) $notification->status);
                $statusVariant = match ($status) {
                    'published' => 'success',
                    'closed' => 'default',
                    default => 'warning',
                };
            @endphp
            <tr>
                <td class="border border-slate-300 px-3 py-2 text-xs">{{ $notification->advertisement_no ?? ('ADV/' . $notification->id) }}</td>
                <td class="border border-slate-300 px-3 py-2 text-sm">{{ $notification->title }}</td>
                <td class="border border-slate-300 px-3 py-2 text-xs">{{ $notification->start_date?->format('d-m-Y') }}</td>
                <td class="border border-slate-300 px-3 py-2 text-xs">{{ $notification->end_date?->format('d-m-Y') }}</td>
                <td class="border border-slate-300 px-3 py-2 text-xs">
                    <x-official.badge :variant="$statusVariant">{{ $status }}</x-official.badge>
                </td>
                <td class="border border-slate-300 px-3 py-2 text-xs">
                    @if ($notification->pdf_path)
                        <a href="{{ $notification->signedPdfUrl() }}" target="_blank" class="font-semibold text-[var(--gov-primary)] hover:underline">View</a>
                    @else
                        -
                    @endif
                </td>
                <td class="border border-slate-300 px-3 py-2 text-xs">
                    <div class="flex flex-wrap gap-1">
                        <a href="{{ route('admin.notifications.show', $notification) }}"><x-official.button variant="outline">View</x-official.button></a>
                        <a href="{{ route('admin.notifications.edit', $notification) }}"><x-official.button variant="outline">Edit</x-official.button></a>
                        <form action="{{ route('admin.notifications.destroy', $notification) }}" method="POST" onsubmit="return confirm('Delete this advertisement?');">
                            @csrf
                            @method('DELETE')
                            <x-official.button variant="danger" type="submit">Delete</x-official.button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="border border-slate-300 px-3 py-4 text-center text-sm text-slate-600">No advertisements found.</td>
            </tr>
        @endforelse
    </x-official.table>

    <div class="mt-4">{{ $notifications->links() }}</div>
@endsection
