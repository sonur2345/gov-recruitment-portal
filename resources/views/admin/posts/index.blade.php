@extends('layouts.admin')

@section('title', 'Post Management')
@section('page_title', 'Post Management')

@section('content')
    <div class="mb-4 flex justify-end">
        <a href="{{ route('admin.posts.create') }}"><x-official.button>Create Post</x-official.button></a>
    </div>

    <x-official.table :headers="['Post', 'Code', 'Advertisement', 'Vacancies', 'Pay Level', 'Age', 'Flags', 'Actions']">
        @forelse ($posts as $post)
            <tr>
                <td class="border border-slate-300 px-3 py-2 text-sm">{{ $post->name }}</td>
                <td class="border border-slate-300 px-3 py-2 text-xs">{{ $post->code }}</td>
                <td class="border border-slate-300 px-3 py-2 text-xs">{{ $post->notification?->advertisement_no ?? $post->notification?->title ?? '-' }}</td>
                <td class="border border-slate-300 px-3 py-2 text-xs">{{ $post->total_vacancies }}</td>
                <td class="border border-slate-300 px-3 py-2 text-xs">{{ $post->pay_level ?? '-' }}</td>
                <td class="border border-slate-300 px-3 py-2 text-xs">{{ $post->age_min }} - {{ $post->age_max }}</td>
                <td class="border border-slate-300 px-3 py-2 text-xs">
                    <div class="flex flex-wrap gap-1">
                        <x-official.badge :variant="$post->experience_required ? 'success' : 'default'">Experience</x-official.badge>
                        <x-official.badge :variant="$post->skill_test_required ? 'success' : 'default'">Skill Test</x-official.badge>
                    </div>
                </td>
                <td class="border border-slate-300 px-3 py-2 text-xs">
                    <div class="flex flex-wrap gap-1">
                        <a href="{{ route('admin.posts.show', $post) }}"><x-official.button variant="outline">View</x-official.button></a>
                        <a href="{{ route('admin.posts.edit', $post) }}"><x-official.button variant="outline">Edit</x-official.button></a>
                        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Delete this post?');">
                            @csrf
                            @method('DELETE')
                            <x-official.button variant="danger" type="submit">Delete</x-official.button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="border border-slate-300 px-3 py-4 text-center text-sm text-slate-600">No posts found.</td>
            </tr>
        @endforelse
    </x-official.table>

    <div class="mt-4">{{ $posts->links() }}</div>
@endsection
