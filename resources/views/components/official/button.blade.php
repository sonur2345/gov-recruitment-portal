@props([
    'variant' => 'primary',
    'type' => 'button',
])

@php
    $base = 'inline-flex items-center justify-center border px-3 py-2 text-xs font-semibold uppercase tracking-wide transition focus:outline-none focus:ring-1';
    $variants = [
        'primary' => 'border-[var(--gov-navy)] bg-[var(--gov-navy)] text-white hover:bg-[#0d243f] focus:ring-[var(--gov-navy)]',
        'secondary' => 'border-[var(--gov-maroon)] bg-[var(--gov-maroon)] text-white hover:bg-[#651924] focus:ring-[var(--gov-maroon)]',
        'outline' => 'border-[var(--gov-border)] bg-white text-slate-700 hover:bg-slate-100 focus:ring-[var(--gov-navy)]',
        'danger' => 'border-[var(--gov-danger)] bg-[var(--gov-danger)] text-white hover:bg-[#7f1d1d] focus:ring-[var(--gov-danger)]',
        'success' => 'border-[var(--gov-success)] bg-[var(--gov-success)] text-white hover:bg-[#14532d] focus:ring-[var(--gov-success)]',
    ];
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => $base . ' ' . ($variants[$variant] ?? $variants['primary']),
    ]) }}
>
    {{ $slot }}
</button>
