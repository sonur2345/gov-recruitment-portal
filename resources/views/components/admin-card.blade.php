@props([
    'title' => null,
])

<div {{ $attributes->merge(['class' => 'rounded border border-slate-200 bg-white p-3 shadow-sm']) }}>
    @if ($title)
        <div class="mb-2 text-[11px] font-semibold uppercase tracking-wide text-slate-500">
            {{ $title }}
        </div>
    @endif
    {{ $slot }}
</div>
