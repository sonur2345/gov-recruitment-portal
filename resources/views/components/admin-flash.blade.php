@if (session('success') || session('error') || session('status'))
    <div class="mb-3 space-y-2">
        @if (session('success'))
            <div class="rounded border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-700">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="rounded border border-rose-200 bg-rose-50 px-3 py-2 text-xs text-rose-700">
                {{ session('error') }}
            </div>
        @endif
        @if (session('status'))
            <div class="rounded border border-blue-200 bg-blue-50 px-3 py-2 text-xs text-blue-700">
                {{ session('status') }}
            </div>
        @endif
    </div>
@endif
