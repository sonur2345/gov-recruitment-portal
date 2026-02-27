<header class="sticky top-0 z-40 border-b border-[var(--gov-border)] bg-white no-print">
    <div class="mx-auto flex max-w-[1600px] items-center justify-between px-3 py-2 sm:px-4">
        <div class="flex items-center gap-2">
            <button
                type="button"
                class="inline-flex h-8 w-8 items-center justify-center rounded border border-[var(--gov-navy)] text-[var(--gov-navy)] md:hidden"
                @click="sidebarOpen = !sidebarOpen"
                aria-label="Toggle sidebar"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-[var(--gov-navy)]">Government Recruitment</p>
                <p class="text-[10px] text-slate-500">Admin Control Panel</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span class="hidden text-[10px] text-slate-500 sm:inline">{{ now()->format('d M Y') }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-admin-button variant="outline" size="xs" type="submit">
                    Logout
                </x-admin-button>
            </form>
        </div>
    </div>
</header>
