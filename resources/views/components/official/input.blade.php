@props([
    'name',
    'label',
    'type' => 'text',
    'required' => false,
    'value' => null,
    'id' => null,
    'placeholder' => '',
])

@php
    $inputId = $id ?? $name;
    $hasError = $errors->has($name);
@endphp

<div>
    <label for="{{ $inputId }}" class="mb-1 block text-sm font-semibold text-slate-800">
        {{ $label }}
        @if ($required)
            <span class="text-[var(--gov-danger)]">*</span>
        @endif
    </label>

    <input
        id="{{ $inputId }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ in_array($type, ['password', 'file'], true) ? null : old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        @if ($required) required @endif
        aria-invalid="{{ $hasError ? 'true' : 'false' }}"
        {{ $attributes->merge([
            'class' => 'w-full border border-[var(--gov-border)] bg-white px-3 py-2 text-sm text-slate-900 outline-none focus:border-[var(--gov-navy)] focus:ring-1 focus:ring-[var(--gov-navy)]',
        ]) }}
    >

    @error($name)
        <p class="mt-1 text-xs text-[var(--gov-danger)]">{{ $message }}</p>
    @enderror
</div>
