@extends('layouts.admin')

@section('title', 'Audit Logs')
@section('page_title', 'Audit Logs')

@section('content')
    <x-official.form title="Filter Logs" class="mb-4">
        <form method="GET" class="grid gap-4 md:grid-cols-5">
            <x-official.input name="action" label="Action" :value="$filters['action'] ?? ''" />
            <x-official.input name="user_id" label="User ID" type="number" :value="$filters['user_id'] ?? ''" />
            <x-official.input name="date_from" label="From" type="date" :value="$filters['date_from'] ?? ''" />
            <x-official.input name="date_to" label="To" type="date" :value="$filters['date_to'] ?? ''" />
            <div class="flex items-end">
                <x-official.button type="submit" class="w-full">Filter</x-official.button>
            </div>
        </form>
    </x-official.form>

    <x-official.table :headers="['ID', 'Time', 'User', 'Action', 'Model', 'IP', 'User Agent']">
        @forelse ($logs as $log)
            <tr>
                <td class="border border-slate-300 px-3 py-2 text-xs">{{ $log->id }}</td>
                <td class="border border-slate-300 px-3 py-2 text-xs">{{ optional($log->created_at)->format('d M Y H:i:s') }}</td>
                <td class="border border-slate-300 px-3 py-2 text-xs">
                    {{ $log->user?->name ?? '-' }}
                    @if ($log->user?->email)
                        <p class="text-[11px] text-slate-500">{{ $log->user->email }}</p>
                    @endif
                </td>
                <td class="border border-slate-300 px-3 py-2 text-xs">{{ $log->action }}</td>
                <td class="border border-slate-300 px-3 py-2 text-xs">{{ class_basename($log->model_type) }}#{{ $log->model_id }}</td>
                <td class="border border-slate-300 px-3 py-2 text-xs">{{ $log->ip_address }}</td>
                <td class="border border-slate-300 px-3 py-2 text-xs">{{ \Illuminate\Support\Str::limit($log->user_agent, 90) }}</td>
            </tr>
        @empty
            <tr><td colspan="7" class="border border-slate-300 px-3 py-4 text-center text-sm text-slate-600">No audit logs found.</td></tr>
        @endforelse
    </x-official.table>

    <div class="mt-4">{{ $logs->links() }}</div>
@endsection
