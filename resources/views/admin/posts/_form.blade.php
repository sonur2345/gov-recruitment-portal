@csrf
@if (isset($method) && $method === 'PUT')
    @method('PUT')
@endif

<div>
    <label for="notification_id" class="mb-1 block text-sm font-semibold text-slate-800">Advertisement <span class="text-red-700">*</span></label>
    <select id="notification_id" name="notification_id" required class="w-full border border-[var(--gov-border)] px-3 py-2 text-sm">
        <option value="">Select Advertisement</option>
        @foreach ($notifications as $notificationItem)
            <option value="{{ $notificationItem->id }}" @selected(old('notification_id', $post->notification_id ?? '') == $notificationItem->id)>
                {{ $notificationItem->advertisement_no ?? ('ADV/' . $notificationItem->id) }} - {{ $notificationItem->title }}
            </option>
        @endforeach
    </select>
    @error('notification_id') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
</div>

<div class="grid gap-4 md:grid-cols-3">
    <x-official.input name="name" label="Post Name" :required="true" :value="old('name', $post->name ?? '')" />
    <x-official.input name="code" label="Post Code" :required="true" :value="old('code', $post->code ?? '')" />
    <x-official.input name="total_vacancies" label="Total Vacancies" type="number" :required="true" min="1" :value="old('total_vacancies', $post->total_vacancies ?? '')" />
</div>

<div class="grid gap-4 md:grid-cols-3">
    <x-official.input name="age_min" label="Age Min" type="number" :required="true" min="0" :value="old('age_min', $post->age_min ?? '')" />
    <x-official.input name="age_max" label="Age Max" type="number" :required="true" min="0" :value="old('age_max', $post->age_max ?? '')" />
    <x-official.input name="pay_level" label="Pay Level" :value="old('pay_level', $post->pay_level ?? 'Pay Level-6')" />
</div>

<div class="grid gap-4 md:grid-cols-3">
    <x-official.input name="application_fee_general" label="Fee (GEN/OBC/EWS)" type="number" step="0.01" min="0" :required="true" :value="old('application_fee_general', $post->application_fee_general ?? 500)" />
    <x-official.input name="application_fee_reserved" label="Fee (SC/ST/PwBD/Women)" type="number" step="0.01" min="0" :required="true" :value="old('application_fee_reserved', $post->application_fee_reserved ?? 0)" />
    <x-official.input name="exam_date" label="Exam Date" type="date" :value="old('exam_date', isset($post) && $post->exam_date ? $post->exam_date->format('Y-m-d') : '')" />
</div>

<div>
    <label for="qualification_text" class="mb-1 block text-sm font-semibold text-slate-800">Qualification <span class="text-red-700">*</span></label>
    <textarea id="qualification_text" name="qualification_text" rows="3" required class="w-full border border-[var(--gov-border)] px-3 py-2 text-sm">{{ old('qualification_text', $post->qualification_text ?? '') }}</textarea>
    @error('qualification_text') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
</div>

<div>
    <label for="category_breakup" class="mb-1 block text-sm font-semibold text-slate-800">Category Breakup (JSON) <span class="text-red-700">*</span></label>
    <textarea id="category_breakup" name="category_breakup" rows="4" required class="w-full border border-[var(--gov-border)] px-3 py-2 text-sm">{{ old('category_breakup', isset($post) ? json_encode($post->category_breakup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '{"GEN":0,"OBC":0,"SC":0,"ST":0,"EWS":0}') }}</textarea>
    @error('category_breakup') <p class="mt-1 text-xs text-red-700">{{ $message }}</p> @enderror
</div>

<div class="flex flex-wrap gap-6">
    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-800">
        <input type="checkbox" name="experience_required" value="1" @checked((bool) old('experience_required', $post->experience_required ?? false)) class="border-[var(--gov-border)]">
        Experience Required
    </label>

    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-800">
        <input type="checkbox" name="skill_test_required" value="1" @checked((bool) old('skill_test_required', $post->skill_test_required ?? false)) class="border-[var(--gov-border)]">
        Skill Test Required
    </label>
</div>

<div class="grid gap-4 md:grid-cols-3">
    <x-official.input name="weight_education" label="Weight: Education %" type="number" step="0.01" min="0" :value="old('weight_education', $post->weight_education ?? 1)" />
    <x-official.input name="weight_skill" label="Weight: Skill Test" type="number" step="0.01" min="0" :value="old('weight_skill', $post->weight_skill ?? 1)" />
    <x-official.input name="weight_experience" label="Weight: Experience" type="number" step="0.01" min="0" :value="old('weight_experience', $post->weight_experience ?? 1)" />
</div>

@if ($errors->any())
    <div class="border border-red-300 bg-red-50 px-3 py-2 text-xs text-red-800">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<div class="flex flex-wrap gap-2">
    <x-official.button type="submit">Save Post</x-official.button>
    <a href="{{ route('admin.posts.index') }}"><x-official.button variant="outline">Cancel</x-official.button></a>
</div>
