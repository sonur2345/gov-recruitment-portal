@extends('layouts.admin')

@section('title', 'Shortlisting Engine')
@section('page_title', 'Shortlisting Engine')

@section('content')
    <x-official.form title="Shortlisting Rule" description="1-10 vacancies: 10x | 11-50: 5x | above 50: 3x">
        <form method="POST" action="{{ route('admin.shortlists.generate') }}" class="grid gap-4 md:grid-cols-4">
            @csrf
            <div class="md:col-span-3">
                <label for="post_id" class="mb-1 block text-sm font-semibold text-slate-800">Post <span class="text-red-700">*</span></label>
                <select id="post_id" name="post_id" required class="w-full border border-[var(--gov-border)] px-3 py-2 text-sm">
                    <option value="">Select Post</option>
                    @foreach ($posts as $post)
                        @php $label = $post->name ?? $post->title ?? $post->post_name ?? $post->code ?? ('Post #' . $post->id); @endphp
                        <option value="{{ $post->id }}" @selected(($filters['post_id'] ?? null) == $post->id)>
                            {{ $label }} (Vacancies: {{ $post->total_vacancies }}, Eligible: {{ $post->eligible_count }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <x-official.button type="submit" class="w-full">Generate Shortlist</x-official.button>
            </div>
        </form>
    </x-official.form>

    <x-official.form title="Post Summary" class="mt-4">
        <x-official.table :headers="['Post', 'Vacancies', 'Total Applications', 'Eligible', 'Already Shortlisted']">
            @foreach ($posts as $post)
                <tr>
                    <td class="border border-slate-300 px-3 py-2 text-xs">{{ $post->name ?? $post->title ?? $post->post_name ?? $post->code ?? ('Post #' . $post->id) }}</td>
                    <td class="border border-slate-300 px-3 py-2 text-xs">{{ $post->total_vacancies }}</td>
                    <td class="border border-slate-300 px-3 py-2 text-xs">{{ $post->total_applications_count }}</td>
                    <td class="border border-slate-300 px-3 py-2 text-xs">{{ $post->eligible_count }}</td>
                    <td class="border border-slate-300 px-3 py-2 text-xs">{{ $post->shortlisted_count }}</td>
                </tr>
            @endforeach
        </x-official.table>
    </x-official.form>

    @if (!empty($filters['post_id']))
        <x-official.form title="Generated Shortlist" class="mt-4">
            <x-official.table :headers="['Rank', 'Application No', 'Candidate', 'Post', 'Status']">
                @forelse ($shortlistRows as $row)
                    <tr>
                        <td class="border border-slate-300 px-3 py-2 text-xs">{{ $row->rank }}</td>
                        <td class="border border-slate-300 px-3 py-2 text-xs">{{ $row->application?->application_no }}</td>
                        <td class="border border-slate-300 px-3 py-2 text-sm">{{ $row->application?->user?->name ?? '-' }}</td>
                        <td class="border border-slate-300 px-3 py-2 text-sm">{{ $row->application?->post?->name ?? $row->application?->post?->title ?? $row->application?->post?->post_name ?? '-' }}</td>
                        <td class="border border-slate-300 px-3 py-2 text-xs">{{ $row->application?->status ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="border border-slate-300 px-3 py-4 text-center text-sm text-slate-600">No shortlist rows generated for selected post.</td></tr>
                @endforelse
            </x-official.table>
        </x-official.form>
    @endif
@endsection
