@extends('layouts.admin')

@section('title', 'Demand Draft Verification')
@section('page_title', 'Demand Draft Verification')

@section('content')
    <x-official.table :headers="['Application No', 'Candidate', 'Post', 'DD Number', 'Bank', 'Branch', 'Amount', 'DD Date', 'Action']">
        @forelse ($pendingDDs as $dd)
            <tr>
                <td class="border border-slate-300 px-3 py-2 text-xs">{{ $dd->application?->application_no ?? '-' }}</td>
                <td class="border border-slate-300 px-3 py-2 text-sm">{{ $dd->application?->user?->name ?? '-' }}</td>
                <td class="border border-slate-300 px-3 py-2 text-sm">{{ $dd->application?->post?->name ?? $dd->application?->post?->title ?? '-' }}</td>
                <td class="border border-slate-300 px-3 py-2 text-xs">{{ $dd->dd_number }}</td>
                <td class="border border-slate-300 px-3 py-2 text-xs">{{ $dd->bank_name }}</td>
                <td class="border border-slate-300 px-3 py-2 text-xs">{{ $dd->bank_branch ?? '-' }}</td>
                <td class="border border-slate-300 px-3 py-2 text-xs">{{ number_format((float) $dd->amount, 2) }}</td>
                <td class="border border-slate-300 px-3 py-2 text-xs">{{ $dd->dd_date?->format('d-m-Y') }}</td>
                <td class="border border-slate-300 px-3 py-2 text-xs">
                    <div class="space-y-2">
                        <form method="POST" action="{{ route('admin.demand-drafts.valid', $dd) }}">
                            @csrf
                            @method('PATCH')
                            <x-official.button variant="success" type="submit">Mark Valid</x-official.button>
                        </form>

                        <form method="POST" action="{{ route('admin.demand-drafts.invalid', $dd) }}" class="space-y-1">
                            @csrf
                            @method('PATCH')
                            <input type="text" name="remark" placeholder="Invalid remark" required class="w-full border border-[var(--gov-border)] px-2 py-1 text-xs">
                            <x-official.button variant="danger" type="submit">Mark Invalid</x-official.button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="border border-slate-300 px-3 py-4 text-center text-sm text-slate-600">No pending demand drafts.</td>
            </tr>
        @endforelse
    </x-official.table>

    <div class="mt-4">{{ $pendingDDs->links() }}</div>
@endsection
