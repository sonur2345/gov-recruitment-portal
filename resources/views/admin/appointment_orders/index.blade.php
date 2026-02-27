@extends('layouts.admin')

@section('title', 'Appointment Orders')
@section('page_title', 'Appointment Orders')

@section('content')
    <x-official.table :headers="['Application No', 'Candidate', 'Post', 'Category', 'Merit Rank', 'Appointment Order']">
        @forelse ($applications as $application)
            <tr>
                <td class="border border-slate-300 px-3 py-2 text-xs">{{ $application->application_no }}</td>
                <td class="border border-slate-300 px-3 py-2 text-sm">{{ $application->user?->name ?? '-' }}</td>
                <td class="border border-slate-300 px-3 py-2 text-sm">{{ $application->post?->name ?? $application->post?->title ?? $application->post?->post_name ?? '-' }}</td>
                <td class="border border-slate-300 px-3 py-2 text-xs">{{ $application->category }}</td>
                <td class="border border-slate-300 px-3 py-2 text-xs">{{ $application->rank ?? '-' }}</td>
                <td class="border border-slate-300 px-3 py-2 text-xs">
                    @if ($application->appointmentOrder)
                        <p class="mb-1 text-xs font-semibold text-slate-700">{{ $application->appointmentOrder->order_number }}</p>
                        <a href="{{ $application->appointmentOrder->signedDownloadUrl() }}">
                            <x-official.button variant="outline">Download PDF</x-official.button>
                        </a>
                    @else
                        <form method="POST" action="{{ route('admin.appointment-orders.generate', $application) }}" class="space-y-1">
                            @csrf
                            <input type="text" name="office_address" required value="Department of Recruitment, Chhattisgarh Secretariat, Naya Raipur" class="w-full border border-[var(--gov-border)] px-2 py-1 text-xs" placeholder="Office Address">
                            <input type="text" name="signature_name" class="w-full border border-[var(--gov-border)] px-2 py-1 text-xs" placeholder="Signature Name (optional)">
                            <x-official.button variant="success" type="submit">Generate</x-official.button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="border border-slate-300 px-3 py-4 text-center text-sm text-slate-600">No final selected applications available.</td></tr>
        @endforelse
    </x-official.table>

    <div class="mt-4">{{ $applications->links() }}</div>
@endsection
