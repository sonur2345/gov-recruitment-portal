@csrf
@if (isset($method) && $method === 'PUT')
    @method('PUT')
@endif

<div class="grid gap-4 md:grid-cols-2">
    <x-official.input name="advertisement_no" label="Advertisement Number" :value="old('advertisement_no', $notification->advertisement_no ?? '')" placeholder="ADV/2026/001" />
    <x-official.input name="title" label="Title" :required="true" :value="old('title', $notification->title ?? '')" />
</div>

<div>
    <label for="description" class="mb-1 block text-sm font-semibold text-slate-800">Description <span class="text-red-700">*</span></label>
    <textarea id="description" name="description" rows="4" required class="w-full border border-[var(--gov-border)] px-3 py-2 text-sm">{{ old('description', $notification->description ?? '') }}</textarea>
    @error('description') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
</div>

<div>
    <label for="postal_address" class="mb-1 block text-sm font-semibold text-slate-800">Postal Address</label>
    <textarea id="postal_address" name="postal_address" rows="3" class="w-full border border-[var(--gov-border)] px-3 py-2 text-sm">{{ old('postal_address', $notification->postal_address ?? '') }}</textarea>
    @error('postal_address') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
</div>

<div class="grid gap-4 md:grid-cols-3">
    <x-official.input name="start_date" label="Start Date" type="date" :required="true" :value="old('start_date', isset($notification) && $notification->start_date ? $notification->start_date->format('Y-m-d') : '')" />
    <x-official.input name="end_date" label="Last Date" type="date" :required="true" :value="old('end_date', isset($notification) && $notification->end_date ? $notification->end_date->format('Y-m-d') : '')" />
    <x-official.input name="fee_last_date" label="Fee Last Date" type="date" :value="old('fee_last_date', isset($notification) && $notification->fee_last_date ? $notification->fee_last_date->format('Y-m-d') : '')" />
</div>

<div class="grid gap-4 md:grid-cols-3">
    <x-official.input name="last_date_time" label="Last Date & Time" type="datetime-local" :value="old('last_date_time', isset($notification) && $notification->last_date_time ? $notification->last_date_time->format('Y-m-d\\TH:i') : '')" />
    <x-official.input name="exam_date" label="Exam Date" type="date" :value="old('exam_date', isset($notification) && $notification->exam_date ? $notification->exam_date->format('Y-m-d') : '')" />
    <x-official.input name="helpdesk_phone" label="Helpdesk Phone" :value="old('helpdesk_phone', $notification->helpdesk_phone ?? '')" />
    <x-official.input name="helpdesk_email" label="Helpdesk Email" type="email" :value="old('helpdesk_email', $notification->helpdesk_email ?? '')" />
</div>

<div class="grid gap-4 md:grid-cols-2">
    <x-official.input name="dd_payee_text" label="DD Payee Text" :value="old('dd_payee_text', $notification->dd_payee_text ?? '')" placeholder="Payable at: District Health Society" />
</div>

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label for="status" class="mb-1 block text-sm font-semibold text-slate-800">Status <span class="text-red-700">*</span></label>
        @php $selectedStatus = old('status', $notification->status ?? 'draft'); @endphp
        <select id="status" name="status" required class="w-full border border-[var(--gov-border)] px-3 py-2 text-sm">
            <option value="draft" @selected($selectedStatus === 'draft')>Draft</option>
            <option value="published" @selected($selectedStatus === 'published')>Published</option>
            <option value="closed" @selected($selectedStatus === 'closed')>Closed</option>
        </select>
    </div>

    <div>
        <label for="pdf" class="mb-1 block text-sm font-semibold text-slate-800">Advertisement PDF</label>
        <input id="pdf" type="file" name="pdf" accept="application/pdf" class="w-full border border-[var(--gov-border)] px-3 py-2 text-sm">
        @if (isset($notification) && $notification->pdf_path)
            <p class="mt-1 text-xs text-slate-600">Current:
                <a href="{{ $notification->signedPdfUrl() }}" target="_blank" class="font-semibold text-[var(--gov-primary)] hover:underline">View PDF</a>
            </p>
        @endif
    </div>
</div>

@if ($errors->any())
    <div class="border border-red-300 bg-red-50 px-3 py-2 text-xs text-red-800">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<div class="flex flex-wrap gap-2">
    <x-official.button type="submit">Save Advertisement</x-official.button>
    <a href="{{ route('admin.notifications.index') }}"><x-official.button variant="outline">Cancel</x-official.button></a>
</div>
