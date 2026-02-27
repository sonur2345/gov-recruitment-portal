@props([
    'headers' => [],
    'caption' => null,
])

<div class="overflow-x-auto border border-[var(--gov-border)] bg-white">
    <table class="min-w-full border-collapse text-left text-sm">
        @if ($caption)
            <caption class="border-b border-[var(--gov-border)] bg-slate-100 px-3 py-2 text-left text-sm font-semibold uppercase tracking-wide text-[var(--gov-navy)]">
                {{ $caption }}
            </caption>
        @endif

        <thead class="bg-slate-100 text-[var(--gov-navy)]">
            <tr>
                @foreach ($headers as $header)
                    <th scope="col" class="border border-[var(--gov-border)] px-3 py-2 text-xs font-bold uppercase tracking-wide">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>
