@extends('layouts.admin')

@section('title', 'Merit Generation')
@section('page_title', 'Merit Generation')

@section('content')
    <x-official.form title="Merit Formula" description="Total = Education + Skill + Experience | Tie breaker: older DOB first">
        <form method="POST" action="{{ route('admin.merit.generate') }}" class="grid gap-4 md:grid-cols-4">
            @csrf
            <div class="md:col-span-3">
                <label for="post_id" class="mb-1 block text-sm font-semibold text-slate-800">Post <span class="text-red-700">*</span></label>
                <select id="post_id" name="post_id" required class="w-full border border-[var(--gov-border)] px-3 py-2 text-sm">
                    <option value="">Select Post</option>
                    @foreach ($posts as $post)
                        @php $label = $post->name ?? $post->title ?? $post->post_name ?? $post->code ?? ('Post #' . $post->id); @endphp
                        <option value="{{ $post->id }}" @selected(($filters['post_id'] ?? null) == $post->id)>
                            {{ $label }} (Vacancies: {{ $post->total_vacancies }}, Qualified: {{ $post->qualified_count }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <x-official.button type="submit" class="w-full">Generate Merit</x-official.button>
            </div>
        </form>
    </x-official.form>

    <x-official.form title="Merit Readiness" class="mt-4">
        <x-official.table :headers="['Post', 'Vacancies', 'Qualified', 'Selected', 'Waiting', 'Merit Generated']">
            @foreach ($posts as $post)
                <tr>
                    <td class="border border-slate-300 px-3 py-2 text-xs">{{ $post->name ?? $post->title ?? $post->post_name ?? $post->code ?? ('Post #' . $post->id) }}</td>
                    <td class="border border-slate-300 px-3 py-2 text-xs">{{ $post->total_vacancies }}</td>
                    <td class="border border-slate-300 px-3 py-2 text-xs">{{ $post->qualified_count }}</td>
                    <td class="border border-slate-300 px-3 py-2 text-xs">{{ $post->selected_count }}</td>
                    <td class="border border-slate-300 px-3 py-2 text-xs">{{ $post->waiting_count }}</td>
                    <td class="border border-slate-300 px-3 py-2 text-xs">
                        @if ($post->meritGeneration)
                            <x-official.badge variant="success">Yes</x-official.badge>
                        @else
                            <x-official.badge variant="warning">No</x-official.badge>
                        @endif
                    </td>
                </tr>
            @endforeach
        </x-official.table>
    </x-official.form>

    @if (!empty($filters['post_id']))
        <x-official.form title="Merit List" class="mt-4">
            <x-official.table :headers="['Rank', 'Application No', 'Candidate', 'Total Marks', 'DOB', 'Status']">
                @forelse ($meritRows as $row)
                    <tr>
                        <td class="border border-slate-300 px-3 py-2 text-xs">{{ $row->rank ?? '-' }}</td>
                        <td class="border border-slate-300 px-3 py-2 text-xs">{{ $row->application_no }}</td>
                        <td class="border border-slate-300 px-3 py-2 text-sm">{{ $row->user?->name ?? '-' }}</td>
                        <td class="border border-slate-300 px-3 py-2 text-xs">{{ $row->total_marks ?? 0 }}</td>
                        <td class="border border-slate-300 px-3 py-2 text-xs">{{ $row->dob?->format('d-m-Y') ?? '-' }}</td>
                        <td class="border border-slate-300 px-3 py-2 text-xs">
                            <x-official.badge :variant="in_array($row->status, ['selected', 'final_selected'], true) ? 'success' : 'warning'">{{ $row->status }}</x-official.badge>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="border border-slate-300 px-3 py-4 text-center text-sm text-slate-600">No merit rows for selected post.</td></tr>
                @endforelse
            </x-official.table>
        </x-official.form>
    @endif
@endsection
