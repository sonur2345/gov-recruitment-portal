@extends('layouts.gov')

@section('title', 'GOVERNMENT RECRUITMENT PORTAL')
@section('meta_description', 'Official recruitment advertisement, vacancies, timelines, and application access.')

@section('content')
    @php
        $primaryPost = $activePosts->first();
        $notification = $primaryPost?->notification;

        $postName = $primaryPost?->name ?? $primaryPost?->title ?? $primaryPost?->post_name ?? 'Multi-Tasking Staff';
        $totalVacancies = $primaryPost?->total_vacancies ?? $primaryPost?->total_posts ?? 0;

        $rawBreakup = (array) ($primaryPost?->category_breakup ?? []);
        $normalizedBreakup = [];
        foreach ($rawBreakup as $key => $value) {
            $normalizedBreakup[strtoupper((string) $key)] = (int) $value;
        }

        $categoryBreakup = [
            'GEN' => $normalizedBreakup['GEN'] ?? 0,
            'OBC' => $normalizedBreakup['OBC'] ?? 0,
            'SC' => $normalizedBreakup['SC'] ?? 0,
            'ST' => $normalizedBreakup['ST'] ?? 0,
            'EWS' => $normalizedBreakup['EWS'] ?? 0,
        ];

        $advertisementNo = $notification?->advertisement_no ?? ('ADV/' . ($primaryPost?->notification_id ?? now()->year) . '/' . str_pad((string) ($primaryPost?->id ?? 1), 3, '0', STR_PAD_LEFT));
        $payLevel = $primaryPost?->pay_level ?? 'Pay Level-6 (As per applicable recruitment rules)';
        $generalFee = number_format((float) ($primaryPost?->application_fee_general ?? 500), 2);
        $reservedFee = number_format((float) ($primaryPost?->application_fee_reserved ?? 0), 2);

        $startDate = $notification?->start_date?->format('d-m-Y') ?? 'To be notified';
        $lastDate = $notification?->end_date?->format('d-m-Y') ?? 'To be notified';
        $lastDateTime = $notification?->last_date_time?->format('d-m-Y H:i') ?? $lastDate;
        $feeLastDate = $notification?->fee_last_date?->format('d-m-Y') ?? $lastDate;
        $examDate = $notification?->exam_date?->format('d-m-Y') ?? ($primaryPost?->exam_date ? \Illuminate\Support\Carbon::parse($primaryPost->exam_date)->format('d-m-Y') : 'Will be notified separately');
        $postalAddress = $notification?->postal_address ?? 'Refer to the official notification for postal address.';
        $ddPayeeText = $notification?->dd_payee_text ?? 'As per official notification';

        $applicationSteps = [
            'Register using valid email and mobile number.',
            'Login and complete personal, educational, and reservation details.',
            'Upload prescribed documents in required format.',
            'Enter demand draft and payment details carefully.',
            'Preview the complete application and submit finally before closing date.',
        ];

        $requiredDocuments = [
            'Recent passport size photograph',
            'Signature (clear and legible)',
            'Valid photo ID proof (Aadhaar/Passport/Voter ID)',
            'Educational qualification certificates',
            'Category/PwBD/Ex-Serviceman certificate, if applicable',
            'Demand Draft copy as per fee category',
        ];

        $eligibilitySummary = [
            'Applicant must satisfy educational qualification for the post.',
            'Category and age relaxations will be as per government norms.',
            'Only complete applications with all documents and fee details are considered.',
        ];
    @endphp

    <x-official.circular class="mb-6" title="Important Notice" :reference="$advertisementNo" :date="now()->format('d-m-Y')">
        <p class="font-semibold text-[var(--gov-maroon)]">{{ $importantNotice }}</p>
    </x-official.circular>

    <section id="summary" class="mb-6">
        <h2 class="mb-3 border-b border-[var(--gov-border)] pb-2 text-base font-bold uppercase tracking-wide text-[var(--gov-navy)]">
            Recruitment Advertisement Summary
        </h2>

        <div class="grid gap-4 lg:grid-cols-3">
            <x-official.form title="Advertisement Details" :description="'Reference No: ' . $advertisementNo">
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-600">Post Name</p>
                        <p class="text-sm text-slate-900">{{ $postName }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-600">Total Vacancies</p>
                        <p class="text-sm text-slate-900">{{ $totalVacancies }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs font-semibold uppercase text-slate-600">Category-wise Breakup</p>
                        <p class="text-sm text-slate-900">
                            GEN: {{ $categoryBreakup['GEN'] }},
                            OBC: {{ $categoryBreakup['OBC'] }},
                            SC: {{ $categoryBreakup['SC'] }},
                            ST: {{ $categoryBreakup['ST'] }},
                            EWS: {{ $categoryBreakup['EWS'] }}
                        </p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs font-semibold uppercase text-slate-600">Pay Level</p>
                        <p class="text-sm text-slate-900">{{ $payLevel }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs font-semibold uppercase text-slate-600">Application Fee Details</p>
                        <p class="text-sm text-slate-900">GEN/OBC/EWS: INR {{ $generalFee }} | SC/ST/PwBD/Women: INR {{ $reservedFee }}</p>
                    </div>
                </div>
            </x-official.form>

            <x-official.form title="Important Dates">
                <div class="space-y-2 text-sm">
                    <p><span class="font-semibold text-slate-700">Start Date:</span> {{ $startDate }}</p>
                    <p><span class="font-semibold text-slate-700">Last Date:</span> {{ $lastDate }}</p>
                    <p><span class="font-semibold text-slate-700">Last Date & Time:</span> {{ $lastDateTime }}</p>
                    <p><span class="font-semibold text-slate-700">Fee Last Date:</span> {{ $feeLastDate }}</p>
                    <p><span class="font-semibold text-slate-700">Exam Date:</span> {{ $examDate }}</p>
                </div>
            </x-official.form>

            <x-official.form title="Postal Submission Details">
                <div class="space-y-2 text-sm">
                    <p><span class="font-semibold text-slate-700">Postal Address:</span></p>
                    <p class="whitespace-pre-line text-slate-800">{{ $postalAddress }}</p>
                    <p><span class="font-semibold text-slate-700">DD Payee Text:</span> {{ $ddPayeeText }}</p>
                </div>
            </x-official.form>
        </div>
    </section>

    <section id="notifications" class="mb-6">
        <h2 class="mb-3 border-b border-[var(--gov-border)] pb-2 text-base font-bold uppercase tracking-wide text-[var(--gov-navy)]">
            Recruitment Notifications
        </h2>

        <x-official.table :headers="['Advertisement No', 'Post Name', 'Vacancies', 'Last Date', 'Apply Link']">
            @forelse ($activePosts as $post)
                @php
                    $tablePostName = $post->name ?? $post->title ?? $post->post_name ?? ('Post #' . $post->id);
                    $tableVacancies = $post->total_vacancies ?? $post->total_posts ?? 0;
                    $tableLastDate = $post->notification?->end_date?->format('d-m-Y') ?? 'N/A';
                    $tableAdNo = $post->notification?->advertisement_no ?? ('ADV/' . $post->notification_id . '/' . str_pad((string) $post->id, 3, '0', STR_PAD_LEFT));
                @endphp
                <tr>
                    <td class="border border-[var(--gov-border)] px-3 py-2 text-xs">{{ $tableAdNo }}</td>
                    <td class="border border-[var(--gov-border)] px-3 py-2 text-sm font-medium">{{ $tablePostName }}</td>
                    <td class="border border-[var(--gov-border)] px-3 py-2 text-xs">{{ $tableVacancies }}</td>
                    <td class="border border-[var(--gov-border)] px-3 py-2 text-xs">{{ $tableLastDate }}</td>
                    <td class="border border-[var(--gov-border)] px-3 py-2 text-xs">
                        @auth
                            <a href="{{ route('application.create.post', $post) }}" class="inline-flex border border-[var(--gov-navy)] bg-[var(--gov-navy)] px-3 py-1 font-semibold text-white hover:bg-[#0d243f]">
                                Apply
                            </a>
                        @endauth
                        @guest
                            <a href="{{ route('login') }}" class="inline-flex border border-[var(--gov-navy)] px-3 py-1 font-semibold text-[var(--gov-navy)] hover:bg-slate-100">
                                Login to Apply
                            </a>
                        @endguest
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="border border-[var(--gov-border)] px-3 py-4 text-center text-sm text-slate-600">
                        No active recruitment notifications are available at this time.
                    </td>
                </tr>
            @endforelse
        </x-official.table>
    </section>

    <section id="instructions" class="grid gap-4 lg:grid-cols-3">
        <x-official.form title="Application Steps">
            <ol class="list-decimal space-y-2 pl-5 text-sm text-slate-800">
                @foreach ($applicationSteps as $step)
                    <li>{{ $step }}</li>
                @endforeach
            </ol>
        </x-official.form>

        <x-official.form title="Required Documents">
            <ul class="list-disc space-y-2 pl-5 text-sm text-slate-800">
                @foreach ($requiredDocuments as $document)
                    <li>{{ $document }}</li>
                @endforeach
            </ul>
        </x-official.form>

        <x-official.form title="Eligibility Criteria Summary">
            <ul class="list-disc space-y-2 pl-5 text-sm text-slate-800">
                @foreach ($eligibilitySummary as $criterion)
                    <li>{{ $criterion }}</li>
                @endforeach
            </ul>
        </x-official.form>
    </section>
@endsection
