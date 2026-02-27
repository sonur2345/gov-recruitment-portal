@extends('layouts.gov')

@section('title', 'CANDIDATE DASHBOARD | GOVERNMENT RECRUITMENT PORTAL')
@section('meta_description', 'Track application status, payment status, admit card, and print application details.')

@section('content')
    @php
        $statusLabelMap = [
            'submitted' => 'Submitted',
            'dd_verified' => 'Under Scrutiny',
            'under_scrutiny' => 'Under Scrutiny',
            'eligible' => 'Shortlisted',
            'shortlisted' => 'Shortlisted',
            'appeared' => 'Qualified',
            'qualified' => 'Qualified',
            'dv_pending' => 'Under Scrutiny',
            'waiting' => 'Under Scrutiny',
            'selected' => 'Selected',
            'final_selected' => 'Selected',
            'rejected' => 'Rejected',
        ];

        $statusClassMap = [
            'Submitted' => 'bg-slate-100 text-slate-800',
            'Under Scrutiny' => 'bg-amber-100 text-amber-800',
            'Shortlisted' => 'bg-indigo-100 text-indigo-800',
            'Qualified' => 'bg-blue-100 text-blue-800',
            'Selected' => 'bg-green-100 text-green-800',
            'Rejected' => 'bg-red-100 text-red-800',
        ];
    @endphp

    <section class="mb-4 border border-[var(--gov-navy)] bg-white px-4 py-3">
        <h2 class="text-base font-bold uppercase tracking-wide text-[var(--gov-navy)]">Candidate Dashboard</h2>
        <p class="mt-1 text-xs text-slate-700">
            Application statuses: Submitted, Under Scrutiny, Shortlisted, Qualified, Selected, Rejected
        </p>
    </section>

    <section class="mb-6 grid gap-4 md:grid-cols-4">
        <div class="border border-[var(--gov-border)] bg-white px-4 py-3">
            <p class="text-xs font-semibold uppercase text-slate-600">Total Applications</p>
            <p class="mt-1 text-xl font-bold text-[var(--gov-navy)]">{{ $summary['total'] }}</p>
        </div>
        <div class="border border-[var(--gov-border)] bg-white px-4 py-3">
            <p class="text-xs font-semibold uppercase text-slate-600">Under Scrutiny</p>
            <p class="mt-1 text-xl font-bold text-[var(--gov-warning)]">{{ $summary['under_scrutiny'] }}</p>
        </div>
        <div class="border border-[var(--gov-border)] bg-white px-4 py-3">
            <p class="text-xs font-semibold uppercase text-slate-600">Shortlisted</p>
            <p class="mt-1 text-xl font-bold text-indigo-700">{{ $summary['shortlisted'] }}</p>
        </div>
        <div class="border border-[var(--gov-border)] bg-white px-4 py-3">
            <p class="text-xs font-semibold uppercase text-slate-600">Selected</p>
            <p class="mt-1 text-xl font-bold text-[var(--gov-success)]">{{ $summary['selected'] }}</p>
        </div>
    </section>

    <x-official.table
        :caption="'Application Tracking Status'"
        :headers="['Application ID', 'Post Applied', 'Application Status', 'Payment Status', 'Download Admit Card', 'Print Application']"
    >
        @forelse ($applications as $application)
            @php
                $statusKey = strtolower((string) $application->status);
                $statusLabel = $statusLabelMap[$statusKey] ?? ucfirst(str_replace('_', ' ', $statusKey));
                $statusClass = $statusClassMap[$statusLabel] ?? 'bg-slate-100 text-slate-700';
                $postName = $application->post?->name ?? $application->post?->title ?? ('Post #' . $application->post_id);
                $paymentStatusKey = strtolower((string) ($application->demandDraft?->status ?? 'pending'));
                $paymentStatus = ucfirst($paymentStatusKey);
                $canDownloadAdmit = in_array($statusKey, ['shortlisted', 'qualified', 'selected', 'final_selected'], true);
                $admitCardUrl = route('applications.admit-card.download', [
                    'application' => $application,
                    'exam_date' => now()->addDays(7)->toDateString(),
                    'venue' => 'Main Examination Centre',
                ]);
            @endphp

            <tr>
                <td class="border border-[var(--gov-border)] px-3 py-2 text-xs">{{ $application->application_no }}</td>
                <td class="border border-[var(--gov-border)] px-3 py-2 text-sm">{{ $postName }}</td>
                <td class="border border-[var(--gov-border)] px-3 py-2 text-xs">
                    <span class="inline-flex px-2 py-1 font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
                </td>
                <td class="border border-[var(--gov-border)] px-3 py-2 text-xs">{{ $paymentStatus }}</td>
                <td class="border border-[var(--gov-border)] px-3 py-2 text-xs">
                    @if ($canDownloadAdmit)
                        <a href="{{ $admitCardUrl }}" class="inline-flex border border-[var(--gov-navy)] bg-[var(--gov-navy)] px-2 py-1 font-semibold text-white hover:bg-[#0d243f]">
                            Download
                        </a>
                    @else
                        <span class="inline-flex border border-[var(--gov-border)] bg-slate-100 px-2 py-1 text-slate-600">Not Eligible</span>
                    @endif
                </td>
                <td class="border border-[var(--gov-border)] px-3 py-2 text-xs">
                    <button type="button" onclick="window.print()" class="border border-[var(--gov-border)] px-2 py-1 font-semibold text-slate-700 hover:bg-slate-100">
                        Print
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="border border-[var(--gov-border)] px-3 py-4 text-center text-sm text-slate-600">
                    No applications found.
                </td>
            </tr>
        @endforelse
    </x-official.table>

    @if ($applications->hasPages())
        <div class="mt-4 flex justify-center">
            {{ $applications->onEachSide(1)->links() }}
        </div>
    @endif
@endsection
