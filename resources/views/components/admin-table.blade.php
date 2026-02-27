@props([
    'caption' => null,
    'headers' => [],
])

<div class="rounded border border-slate-200 bg-white shadow-sm">
    @if ($caption)
        <div class="border-b border-slate-200 px-3 py-2 text-[11px] font-semibold uppercase tracking-wide text-slate-500">
            {{ $caption }}
        </div>
    @endif
    <div class="max-h-[360px] overflow-x-auto">
        <table class="min-w-full text-left text-xs">
            <thead class="sticky top-0 z-10 bg-slate-50 text-[10px] uppercase tracking-wide text-slate-600">
                <tr>
                    @foreach ($headers as $header)
                        <th class="whitespace-nowrap border-b border-slate-200 px-3 py-2 font-semibold">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
