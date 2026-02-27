@extends('layouts.admin')

@section('title', 'Post Details')
@section('page_title', 'Post Details')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.posts.index') }}"><x-official.button variant="outline">Back</x-official.button></a>
    </div>

    <x-official.form :title="$post->name . ' (' . $post->code . ')'">
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Advertisement</p>
                <p class="text-sm">{{ $post->notification?->advertisement_no ?? $post->notification?->title ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Total Vacancies</p>
                <p class="text-sm">{{ $post->total_vacancies }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Age Limit</p>
                <p class="text-sm">{{ $post->age_min }} - {{ $post->age_max }} years</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Pay Level</p>
                <p class="text-sm">{{ $post->pay_level ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Application Fee (GEN/OBC/EWS)</p>
                <p class="text-sm">{{ number_format((float) ($post->application_fee_general ?? 0), 2) }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Application Fee (SC/ST/PwBD/Women)</p>
                <p class="text-sm">{{ number_format((float) ($post->application_fee_reserved ?? 0), 2) }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Exam Date</p>
                <p class="text-sm">{{ $post->exam_date?->format('d-m-Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Selection Flags</p>
                <div class="flex flex-wrap gap-1">
                    <x-official.badge :variant="$post->experience_required ? 'success' : 'default'">Experience Required</x-official.badge>
                    <x-official.badge :variant="$post->skill_test_required ? 'success' : 'default'">Skill Test Required</x-official.badge>
                </div>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Weight: Education</p>
                <p class="text-sm">{{ number_format((float) ($post->weight_education ?? 1), 2) }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Weight: Skill Test</p>
                <p class="text-sm">{{ number_format((float) ($post->weight_skill ?? 1), 2) }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-600">Weight: Experience</p>
                <p class="text-sm">{{ number_format((float) ($post->weight_experience ?? 1), 2) }}</p>
            </div>
        </div>

        <div class="border-t border-slate-200 pt-3">
            <p class="text-xs font-semibold uppercase text-slate-600">Qualification</p>
            <p class="mt-1 text-sm whitespace-pre-line">{{ $post->qualification_text }}</p>
        </div>

        <div class="border-t border-slate-200 pt-3">
            <p class="text-xs font-semibold uppercase text-slate-600">Category Breakup (JSON)</p>
            <pre class="mt-1 overflow-auto border border-slate-300 bg-slate-50 p-2 text-xs">{{ json_encode($post->category_breakup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
        </div>
    </x-official.form>
@endsection
