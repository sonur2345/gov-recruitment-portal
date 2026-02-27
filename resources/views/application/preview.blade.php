@extends('layouts.gov')

@section('title', 'APPLICATION PREVIEW | GOVERNMENT RECRUITMENT PORTAL')
@section('meta_description', 'Preview the complete recruitment application before final submission.')

@section('content')
    @php
        $preview = [
            'Post Name' => request('post_name', 'As selected in application form'),
            'Qualification Name' => request('qualification_name', 'As entered in Section 2'),
            'Board/University' => request('board_university', 'As entered in Section 2'),
            'Year of Passing' => request('year_of_passing', 'As entered in Section 2'),
            'Percentage' => request('percentage', 'As entered in Section 2'),
            'Organization Name' => request('organization_name', 'As entered in Section 3'),
            'Designation' => request('designation', 'As entered in Section 3'),
            'Total Experience' => request('total_experience', 'As calculated in Section 3'),
            'Category' => request('category', 'As selected in Section 4'),
            'PwBD Status' => request('pwbd_status', 'As selected in Section 4'),
            'Ex-Serviceman' => request('ex_serviceman', 'As selected in Section 4'),
            'Fee Amount' => request('fee_amount', 'As calculated in Section 5'),
            'Demand Draft Number' => request('dd_number', 'As entered in Section 5'),
            'Bank Name' => request('bank_name', 'As entered in Section 5'),
            'DD Date' => request('dd_date', 'As entered in Section 5'),
        ];
    @endphp

    <section class="mb-4 border border-[var(--gov-navy)] bg-white px-4 py-3">
        <h2 class="text-base font-bold uppercase tracking-wide text-[var(--gov-navy)]">Final Application Preview</h2>
        <p class="mt-1 text-xs text-slate-700">Verify all details and return to edit if required before final submission.</p>
    </section>

    <x-official.form title="Complete Form Summary">
        <x-official.table :headers="['Field', 'Preview Value']">
            @foreach ($preview as $label => $value)
                <tr>
                    <td class="border border-[var(--gov-border)] px-3 py-2 text-xs font-semibold text-slate-700">{{ $label }}</td>
                    <td class="border border-[var(--gov-border)] px-3 py-2 text-xs text-slate-900">{{ $value }}</td>
                </tr>
            @endforeach
        </x-official.table>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('application.create') }}" class="border border-[var(--gov-navy)] px-4 py-2 text-xs font-semibold uppercase tracking-wide text-[var(--gov-navy)] hover:bg-slate-100">
                Edit Application
            </a>
            <a href="{{ route('application.create') }}#preview-panel" class="border border-[var(--gov-navy)] bg-[var(--gov-navy)] px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white hover:bg-[#0d243f]">
                Final Submit
            </a>
            <button type="button" onclick="window.print()" class="border border-[var(--gov-border)] px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-700 hover:bg-slate-100">
                Print Preview
            </button>
        </div>
    </x-official.form>
@endsection
