@props([
    'variant' => 'neutral',
])

@php
    $variants = [
        'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'warning' => 'bg-amber-50 text-amber-700 border-amber-200',
        'danger' => 'bg-rose-50 text-rose-700 border-rose-200',
        'info' => 'bg-blue-50 text-blue-700 border-blue-200',
        'neutral' => 'bg-slate-50 text-slate-600 border-slate-200',
    ];
    $classes = $variants[$variant] ?? $variants['neutral'];
@endphp

<span class="inline-flex items-center rounded border px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide {{ $classes }}">
    {{ $slot }}
</span>
