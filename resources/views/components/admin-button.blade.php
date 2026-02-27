@props([
    'variant' => 'primary',
    'size' => 'sm',
    'type' => 'button',
])

@php
    $variants = [
        'primary' => 'bg-[var(--gov-navy)] text-white border border-[var(--gov-navy)] hover:bg-[var(--gov-navy-dark)]',
        'outline' => 'bg-white text-[var(--gov-navy)] border border-[var(--gov-navy)] hover:bg-slate-50',
        'ghost' => 'bg-transparent text-[var(--gov-navy)] hover:bg-slate-100',
    ];

    $sizes = [
        'xs' => 'text-[10px] px-2 py-1',
        'sm' => 'text-xs px-3 py-1.5',
        'md' => 'text-sm px-4 py-2',
    ];
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded font-semibold transition ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['sm'])]) }}>
    {{ $slot }}
</button>
