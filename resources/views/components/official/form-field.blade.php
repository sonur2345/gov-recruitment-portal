@props([
    'name',
    'label',
    'type' => 'text',
    'required' => false,
    'value' => null,
    'placeholder' => '',
    'autocomplete' => null,
    'id' => null,
])

@php
    $errorBag = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $fieldId = $id ?? $name;
    $hasError = $errorBag->has($name);
    $fieldValue = in_array($type, ['password', 'file'], true) ? null : old($name, $value);
    $describedBy = $hasError ? $fieldId . '-error' : null;
@endphp

<div>
    <label for="{{ $fieldId }}" class="mb-1 block text-sm font-semibold text-slate-800">
        {{ $label }}
        @if ($required)
            <span class="text-[var(--gov-danger)]" aria-hidden="true">*</span>
        @endif
    </label>

    <input
        id="{{ $fieldId }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ $fieldValue }}"
        placeholder="{{ $placeholder }}"
        autocomplete="{{ $autocomplete }}"
        @if($required) required @endif
        aria-invalid="{{ $hasError ? 'true' : 'false' }}"
        @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
        {{ $attributes->merge([
            'class' => 'w-full border border-[var(--gov-border)] bg-white px-3 py-2 text-sm text-slate-900 outline-none focus:border-[var(--gov-navy)] focus:ring-1 focus:ring-[var(--gov-navy)]',
        ]) }}
    >

    @if ($hasError)
        <p id="{{ $fieldId }}-error" class="mt-1 text-xs text-[var(--gov-danger)]">
            {{ $errorBag->first($name) }}
        </p>
    @endif
</div>
