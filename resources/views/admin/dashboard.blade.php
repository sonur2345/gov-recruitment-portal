@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page_title', 'Admin Dashboard')

@section('content')
    @php
        $cards = [
            ['label' => 'Total Applications', 'value' => $adminStats['applications'] ?? 0],
            ['label' => 'Under Scrutiny', 'value' => $adminStats['under_scrutiny'] ?? 0],
            ['label' => 'Shortlisted', 'value' => $adminStats['shortlisted'] ?? 0],
            ['label' => 'Qualified', 'value' => $adminStats['qualified'] ?? 0],
            ['label' => 'Selected', 'value' => $adminStats['selected'] ?? 0],
            ['label' => 'Final Selected', 'value' => $adminStats['final_selected'] ?? 0],
            ['label' => 'Pending Grievances', 'value' => $adminStats['pending_grievances'] ?? 0],
            ['label' => 'Demand Draft Pending', 'value' => $adminStats['dd_pending'] ?? 0],
        ];
    @endphp

    <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($cards as $card)
            <x-admin-card>
                <p class="text-[11px] font-semibold uppercase text-slate-500">{{ $card['label'] }}</p>
                <p class="mt-1 text-lg font-semibold text-[var(--gov-navy)]">{{ $card['value'] }}</p>
            </x-admin-card>
        @endforeach
    </section>

    <section class="mt-3 grid gap-3 lg:grid-cols-2">
        <x-admin-card title="Status Distribution">
            <canvas id="statusChart"></canvas>
        </x-admin-card>
        <x-admin-card title="Monthly Applications">
            <canvas id="monthlyChart"></canvas>
        </x-admin-card>
    </section>

    <section class="mt-3">
        <x-admin-table :caption="'Recent Applications'" :headers="['Application No', 'Candidate', 'Post', 'Status', 'Applied On']">
            @forelse ($recentApplications as $application)
                @php
                    $postName = $application->post?->name ?? $application->post?->title ?? $application->post?->post_name ?? ('Post #' . $application->post_id);
                    $statusValue = strtolower((string) $application->status);
                    $badgeVariant = match ($statusValue) {
                        'rejected' => 'danger',
                        'final_selected', 'selected' => 'success',
                        'shortlisted', 'qualified' => 'info',
                        default => 'warning',
                    };
                @endphp
                <tr>
                    <td class="whitespace-nowrap border-b border-slate-100 px-3 py-2 text-[11px]">{{ $application->application_no }}</td>
                    <td class="border-b border-slate-100 px-3 py-2 text-xs">{{ $application->user?->name ?? '-' }}</td>
                    <td class="border-b border-slate-100 px-3 py-2 text-xs">{{ $postName }}</td>
                    <td class="border-b border-slate-100 px-3 py-2 text-[11px]">
                        <x-admin-badge :variant="$badgeVariant">{{ str_replace('_', ' ', $statusValue) }}</x-admin-badge>
                    </td>
                    <td class="border-b border-slate-100 px-3 py-2 text-[11px]">{{ $application->created_at?->format('d-m-Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-3 py-4 text-center text-xs text-slate-500">
                        No application records available.
                    </td>
                </tr>
            @endforelse
        </x-admin-table>
    </section>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            new Chart(statusCtx, {
                type: 'pie',
                data: {
                    labels: @json($statusDistribution['labels']),
                    datasets: [{
                        data: @json($statusDistribution['values']),
                        backgroundColor: ['#0B3D91', '#800000', '#166534', '#c2410c', '#4338ca', '#0f766e', '#be123c', '#64748b']
                    }]
                }
            });
        }

        const monthlyCtx = document.getElementById('monthlyChart');
        if (monthlyCtx) {
            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: @json($monthlyTrend['labels']),
                    datasets: [{
                        label: 'Applications',
                        data: @json($monthlyTrend['values']),
                        borderColor: '#0B3D91',
                        backgroundColor: 'rgba(11,61,145,0.15)',
                        fill: true,
                        tension: 0.2
                    }]
                }
            });
        }
    </script>
@endpush
