@props([
    'title' => null,
    'description' => null,
])

<section {{ $attributes->merge(['class' => 'border border-[var(--gov-border)] bg-white']) }}>
    @if ($title || $description)
        <div class="border-b border-[var(--gov-border)] bg-slate-100 px-4 py-3">
            @if ($title)
                <h3 class="text-sm font-bold uppercase tracking-wide text-[var(--gov-navy)]">{{ $title }}</h3>
            @endif
            @if ($description)
                <p class="mt-1 text-xs text-slate-700">{{ $description }}</p>
            @endif
        </div>
    @endif

    <div class="p-4">
        {{ $slot }}
    </div>
</section>
