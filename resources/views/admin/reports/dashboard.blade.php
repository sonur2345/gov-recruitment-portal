@extends('layouts.admin')

@section('title', 'Reports & Analytics')
@section('page_title', 'Reports & Analytics')

@section('content')
    <div class="mb-4 flex flex-wrap gap-2">
        <a href="{{ route('admin.reports.export.csv', request()->query()) }}"><x-official.button variant="outline">Export CSV</x-official.button></a>
        <a href="{{ route('admin.reports.export.excel', request()->query()) }}"><x-official.button variant="outline">Export Excel</x-official.button></a>
        <a href="{{ route('admin.reports.export.pdf', request()->query()) }}"><x-official.button variant="outline">Export PDF</x-official.button></a>
    </div>

    <x-official.form title="Filter Reports" class="mb-4">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="grid gap-4 md:grid-cols-5">
            <div>
                <label for="post_id" class="mb-1 block text-sm font-semibold text-slate-800">Post</label>
                <select id="post_id" name="post_id" class="w-full border border-[var(--gov-border)] px-3 py-2 text-sm">
                    <option value="">All Posts</option>
                    @foreach ($posts as $post)
                        <option value="{{ $post->id }}" @selected(($filters['post_id'] ?? null) == $post->id)>
                            {{ $post->name ?? $post->title ?? $post->post_name ?? $post->code ?? ('Post #' . $post->id) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="category" class="mb-1 block text-sm font-semibold text-slate-800">Category</label>
                <select id="category" name="category" class="w-full border border-[var(--gov-border)] px-3 py-2 text-sm">
                    <option value="">All</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" @selected(($filters['category'] ?? null) === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </div>

            <x-official.input name="date_from" label="Date From" type="date" :value="$filters['date_from'] ?? ''" />
            <x-official.input name="date_to" label="Date To" type="date" :value="$filters['date_to'] ?? ''" />

            <div class="flex items-end gap-2">
                <x-official.button type="submit" class="w-full">Apply</x-official.button>
                <a href="{{ route('admin.reports.index') }}" class="w-full"><x-official.button variant="outline" class="w-full">Reset</x-official.button></a>
            </div>
        </form>
    </x-official.form>

    @php
        $cards = [
            'Total Posts' => $summary['total_posts'],
            'Total Applications' => $summary['total_applications'],
            'Eligible' => $summary['eligible_candidates'],
            'Shortlisted' => $summary['shortlisted'],
            'Qualified' => $summary['qualified'],
            'Selected' => $summary['selected'],
            'Final Selected' => $summary['final_selected'],
            'Rejected' => $summary['rejected'],
            'Waiting List' => $summary['waiting_list_count'],
        ];
    @endphp

    <section class="mb-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($cards as $label => $value)
            <x-official.card>
                <p class="text-xs font-semibold uppercase text-slate-600">{{ $label }}</p>
                <p class="mt-1 text-2xl font-bold text-[var(--gov-primary)]">{{ $value }}</p>
            </x-official.card>
        @endforeach
    </section>

    <section class="mb-4 grid gap-4 lg:grid-cols-2">
        <x-official.card title="Monthly Application Trend">
            <canvas id="monthlyTrendChart"></canvas>
        </x-official.card>
        <x-official.card title="Status Distribution">
            <canvas id="statusDistributionChart"></canvas>
        </x-official.card>
    </section>

    <x-official.card title="Post-wise Selection Comparison" class="mb-4">
        <canvas id="postSelectionChart"></canvas>
    </x-official.card>

    <section class="grid gap-4 lg:grid-cols-3">
        <x-official.card title="Post-wise Statistics" class="lg:col-span-2">
            <x-official.table :headers="['Post', 'Applications', 'Selected', 'Waiting', 'Rejected', 'Vacancies', 'Filled']">
                @forelse ($postWiseStats as $row)
                    <tr>
                        <td class="border border-slate-300 px-3 py-2 text-xs">{{ $row->post_label }}</td>
                        <td class="border border-slate-300 px-3 py-2 text-xs">{{ (int) $row->applications_count }}</td>
                        <td class="border border-slate-300 px-3 py-2 text-xs">{{ (int) $row->selected_count }}</td>
                        <td class="border border-slate-300 px-3 py-2 text-xs">{{ (int) $row->waiting_count }}</td>
                        <td class="border border-slate-300 px-3 py-2 text-xs">{{ (int) $row->rejected_count }}</td>
                        <td class="border border-slate-300 px-3 py-2 text-xs">{{ (int) $row->vacancies }}</td>
                        <td class="border border-slate-300 px-3 py-2 text-xs">{{ (int) $row->filled_seats }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="border border-slate-300 px-3 py-4 text-center text-sm text-slate-600">No data.</td></tr>
                @endforelse
            </x-official.table>
        </x-official.card>

        <x-official.card title="Category-wise Statistics">
            <canvas id="categoryChart" class="mb-3"></canvas>
            <x-official.table :headers="['Category', 'Total', 'Selected']">
                @foreach ($categoryStats['table'] as $row)
                    <tr>
                        <td class="border border-slate-300 px-3 py-2 text-xs">{{ $row['category'] }}</td>
                        <td class="border border-slate-300 px-3 py-2 text-xs">{{ $row['total'] }}</td>
                        <td class="border border-slate-300 px-3 py-2 text-xs">{{ $row['selected'] }}</td>
                    </tr>
                @endforeach
            </x-official.table>
        </x-official.card>
    </section>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        new Chart(document.getElementById('monthlyTrendChart'), {
            type: 'line',
            data: {
                labels: @json($monthlyTrend['labels']),
                datasets: [{
                    label: 'Applications',
                    data: @json($monthlyTrend['values']),
                    borderColor: '#0B3D91',
                    backgroundColor: 'rgba(11,61,145,0.15)',
                    fill: true,
                    tension: 0.25
                }]
            }
        });

        new Chart(document.getElementById('statusDistributionChart'), {
            type: 'pie',
            data: {
                labels: @json($statusDistribution['labels']),
                datasets: [{
                    data: @json($statusDistribution['values']),
                    backgroundColor: ['#0B3D91', '#800000', '#16a34a', '#f59e0b', '#ef4444', '#6366f1', '#0ea5e9', '#64748b']
                }]
            }
        });

        new Chart(document.getElementById('postSelectionChart'), {
            type: 'bar',
            data: {
                labels: @json($postSelectionChart['labels']),
                datasets: [
                    { label: 'Selected', data: @json($postSelectionChart['selected']), backgroundColor: '#166534' },
                    { label: 'Waiting', data: @json($postSelectionChart['waiting']), backgroundColor: '#f59e0b' }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        new Chart(document.getElementById('categoryChart'), {
            type: 'bar',
            data: {
                labels: @json($categoryStats['labels']),
                datasets: [
                    { label: 'Total', data: @json($categoryStats['distribution']), backgroundColor: '#0B3D91' },
                    { label: 'Selected', data: @json($categoryStats['selected_distribution']), backgroundColor: '#166534' }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    </script>
@endpush
