@props([
    'title' => 'Official Circular',
    'reference' => null,
    'date' => null,
])

<section {{ $attributes->merge(['class' => 'border border-[var(--gov-border)] bg-white']) }}>
    <div class="border-b border-[var(--gov-border)] bg-slate-100 px-4 py-3">
        <p class="text-xs font-semibold uppercase tracking-wide text-[var(--gov-maroon)]">Government Circular</p>
        <h3 class="mt-1 text-sm font-bold uppercase tracking-wide text-[var(--gov-navy)]">{{ $title }}</h3>
        <div class="mt-1 flex flex-wrap gap-3 text-[11px] text-slate-700">
            @if ($reference)
                <span><span class="font-semibold">Ref:</span> {{ $reference }}</span>
            @endif
            @if ($date)
                <span><span class="font-semibold">Date:</span> {{ $date }}</span>
            @endif
        </div>
    </div>
    <div class="p-4 text-sm leading-6 text-slate-800">
        {{ $slot }}
    </div>
</section>
