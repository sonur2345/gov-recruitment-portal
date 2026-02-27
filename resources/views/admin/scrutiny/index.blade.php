@extends('layouts.admin')

@section('title', 'Application Scrutiny')
@section('page_title', 'Application Scrutiny')

@section('content')
    <x-official.table :headers="['Application No', 'Candidate', 'Post', 'Status', 'Action']">
        @forelse ($applications as $application)
            @php
                $postName = $application->post?->name ?? $application->post?->title ?? $application->post?->post_name ?? '-';
            @endphp
            <tr>
                <td class="border border-slate-300 px-3 py-2 text-xs">{{ $application->application_no }}</td>
                <td class="border border-slate-300 px-3 py-2 text-sm">{{ $application->user?->name ?? '-' }}</td>
                <td class="border border-slate-300 px-3 py-2 text-sm">{{ $postName }}</td>
                <td class="border border-slate-300 px-3 py-2 text-xs"><x-official.badge variant="info">{{ $application->status }}</x-official.badge></td>
                <td class="border border-slate-300 px-3 py-2 text-xs">
                    <a href="{{ route('admin.scrutiny.show', $application) }}"><x-official.button variant="outline">Review</x-official.button></a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="border border-slate-300 px-3 py-4 text-center text-sm text-slate-600">
                    No `dd_verified` applications available for scrutiny.
                </td>
            </tr>
        @endforelse
    </x-official.table>

    <div class="mt-4">{{ $applications->links() }}</div>
@endsection
