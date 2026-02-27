@extends('layouts.admin')

@section('title', 'Grievances')
@section('page_title', 'Grievance Management')

@section('content')
    <div class="mb-3 rounded border border-slate-200 bg-white p-3">
        <form method="GET" class="grid gap-3 md:grid-cols-3">
            <div>
                <label class="text-[11px] font-semibold uppercase text-slate-500">Status</label>
                <select name="status" class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs">
                    <option value="">All</option>
                    @foreach (['open', 'in_progress', 'resolved', 'closed'] as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[11px] font-semibold uppercase text-slate-500">Priority</label>
                <select name="priority" class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs">
                    <option value="">All</option>
                    @foreach (['low', 'medium', 'high'] as $priority)
                        <option value="{{ $priority }}" @selected(($filters['priority'] ?? '') === $priority)>{{ ucfirst($priority) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button class="border border-[var(--gov-navy)] bg-[var(--gov-navy)] px-3 py-2 text-xs font-semibold text-white">Apply</button>
            </div>
        </form>
    </div>

    <x-admin-table :caption="'Grievance Queue'" :headers="['Ticket', 'Candidate', 'Status', 'Priority', 'Created', 'Action']">
        @forelse ($grievances as $grievance)
            <tr>
                <td class="px-3 py-2 text-[11px]">#{{ $grievance->id }}</td>
                <td class="px-3 py-2 text-xs">{{ $grievance->user?->name ?? '-' }}</td>
                <td class="px-3 py-2 text-[11px]">
                    <x-admin-badge :variant="match ($grievance->status) {
                        'resolved', 'closed' => 'success',
                        'in_progress' => 'info',
                        default => 'warning',
                    }">{{ str_replace('_', ' ', $grievance->status) }}</x-admin-badge>
                </td>
                <td class="px-3 py-2 text-xs">{{ ucfirst($grievance->priority) }}</td>
                <td class="px-3 py-2 text-[11px]">{{ $grievance->created_at?->format('d-m-Y') }}</td>
                <td class="px-3 py-2 text-[11px]">
                    <a href="{{ route('admin.grievances.show', $grievance) }}" class="font-semibold text-[var(--gov-navy)] hover:underline">View</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-3 py-4 text-center text-xs text-slate-500">No grievances found.</td>
            </tr>
        @endforelse
    </x-admin-table>

    <div class="mt-3">
        {{ $grievances->links() }}
    </div>
@endsection
