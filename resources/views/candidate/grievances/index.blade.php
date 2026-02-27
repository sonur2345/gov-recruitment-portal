@extends('layouts.gov')

@section('title', 'Grievances')
@section('page_title', 'Grievances')

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
        <div>
            <h2 class="text-lg font-semibold text-[var(--gov-primary)]">Your Grievances</h2>
            <p class="text-sm text-slate-600">Track status and responses.</p>
        </div>
        <a href="{{ route('grievances.create') }}" class="inline-flex border border-[var(--gov-primary)] bg-[var(--gov-primary)] px-3 py-2 text-sm font-semibold text-white hover:bg-[#0d243f]">
            New Grievance
        </a>
    </div>

    <div class="overflow-x-auto rounded border border-slate-200 bg-white">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-600">
                <tr>
                    <th class="px-3 py-2">Subject</th>
                    <th class="px-3 py-2">Status</th>
                    <th class="px-3 py-2">Priority</th>
                    <th class="px-3 py-2">Created</th>
                    <th class="px-3 py-2">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                @forelse ($grievances as $grievance)
                    <tr>
                        <td class="px-3 py-2">{{ $grievance->subject }}</td>
                        <td class="px-3 py-2">
                            <span class="rounded border px-2 py-1 text-xs uppercase">{{ $grievance->status }}</span>
                        </td>
                        <td class="px-3 py-2">{{ ucfirst($grievance->priority) }}</td>
                        <td class="px-3 py-2">{{ $grievance->created_at?->format('d-m-Y') }}</td>
                        <td class="px-3 py-2">
                            <a href="{{ route('grievances.show', $grievance) }}" class="text-sm font-semibold text-[var(--gov-primary)] hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-4 text-center text-sm text-slate-500">No grievances submitted yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $grievances->links() }}
    </div>
@endsection
