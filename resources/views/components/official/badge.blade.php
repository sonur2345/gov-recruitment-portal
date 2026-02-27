@props([
    'variant' => 'default',
])

@php
    $variants = [
        'default' => 'border-slate-300 bg-slate-100 text-slate-700',
        'info' => 'border-blue-300 bg-blue-50 text-blue-800',
        'warning' => 'border-amber-300 bg-amber-50 text-amber-800',
        'success' => 'border-emerald-300 bg-emerald-50 text-emerald-800',
        'danger' => 'border-red-300 bg-red-50 text-red-800',
    ];
@endphp

<span {{ $attributes->merge([
    'class' => 'inline-flex border px-2 py-1 text-[11px] font-semibold uppercase tracking-wide ' . ($variants[$variant] ?? $variants['default']),
]) }}>
    {{ $slot }}
</span>
