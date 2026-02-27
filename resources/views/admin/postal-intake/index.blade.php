@extends('layouts.admin')

@section('title', 'Postal Intake')
@section('page_title', 'Postal Intake')

@section('content')
    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
        <div>
            <h2 class="text-sm font-semibold text-[var(--gov-navy)]">Postal Applications</h2>
            <p class="text-xs text-slate-500">Logged by DEO with inward details.</p>
        </div>
        <a href="{{ route('admin.postal-intake.create') }}" class="border border-[var(--gov-navy)] bg-[var(--gov-navy)] px-3 py-2 text-xs font-semibold text-white">
            New Postal Intake
        </a>
    </div>

    <div class="mb-3 rounded border border-slate-200 bg-white p-3">
        <form method="GET" class="grid gap-3 md:grid-cols-3">
            <div>
                <label class="text-[11px] font-semibold uppercase text-slate-500">Post</label>
                <select name="post_id" class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs">
                    <option value="">All</option>
                    @foreach ($posts as $post)
                        <option value="{{ $post->id }}" @selected(($filters['post_id'] ?? '') == $post->id)>
                            {{ $post->name ?? $post->title ?? $post->post_name ?? ('Post #' . $post->id) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[11px] font-semibold uppercase text-slate-500">Inward No</label>
                <input type="text" name="inward_no" value="{{ $filters['inward_no'] ?? '' }}" class="mt-1 w-full border border-slate-200 px-2 py-1 text-xs" placeholder="INW/2026/001">
            </div>
            <div class="flex items-end">
                <button class="border border-[var(--gov-navy)] bg-[var(--gov-navy)] px-3 py-2 text-xs font-semibold text-white">Apply</button>
            </div>
        </form>
    </div>

    <x-admin-table :caption="'Postal Applications'" :headers="['App No', 'Candidate', 'Post', 'Inward No', 'DD No', 'DD Status', 'Status']">
        @forelse ($applications as $application)
            <tr>
                <td class="px-3 py-2 text-[11px]">{{ $application->application_no }}</td>
                <td class="px-3 py-2 text-xs">{{ $application->user?->name ?? '-' }}</td>
                <td class="px-3 py-2 text-xs">{{ $application->post?->name ?? $application->post?->code ?? '-' }}</td>
                <td class="px-3 py-2 text-[11px]">{{ $application->inward_no ?? '-' }}</td>
                <td class="px-3 py-2 text-[11px]">{{ $application->demandDraft?->dd_number ?? '-' }}</td>
                <td class="px-3 py-2 text-[11px]">{{ $application->demandDraft?->status ?? 'pending' }}</td>
                <td class="px-3 py-2 text-[11px]">{{ $application->status }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-3 py-4 text-center text-xs text-slate-500">No postal intake records.</td>
            </tr>
        @endforelse
    </x-admin-table>

    <div class="mt-3">
        {{ $applications->links() }}
    </div>
@endsection
