@extends('layouts.admin')

@section('title', 'Document Verification')
@section('page_title', 'Document Verification')

@section('content')
    <x-official.table :headers="['Application No', 'Candidate', 'Post', 'Rank', 'Action']">
        @forelse ($applications as $application)
            <tr>
                <td class="border border-slate-300 px-3 py-2 text-xs">{{ $application->application_no }}</td>
                <td class="border border-slate-300 px-3 py-2 text-sm">{{ $application->user?->name ?? '-' }}</td>
                <td class="border border-slate-300 px-3 py-2 text-sm">{{ $application->post?->name ?? $application->post?->title ?? $application->post?->post_name ?? '-' }}</td>
                <td class="border border-slate-300 px-3 py-2 text-xs">{{ $application->rank ?? '-' }}</td>
                <td class="border border-slate-300 px-3 py-2 text-xs">
                    <a href="{{ route('admin.document-verifications.show', $application) }}"><x-official.button variant="outline">Verify</x-official.button></a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="border border-slate-300 px-3 py-4 text-center text-sm text-slate-600">No selected candidates found for document verification.</td>
            </tr>
        @endforelse
    </x-official.table>

    <div class="mt-4">{{ $applications->links() }}</div>
@endsection
