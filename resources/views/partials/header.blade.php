@php
    $firstActivePost = (isset($activePosts) && $activePosts instanceof \Illuminate\Support\Collection)
        ? $activePosts->first()
        : null;
    $firstNotification = $firstActivePost?->notification;

    $generatedAdvertisementNo = $firstActivePost
        ? ($firstNotification?->advertisement_no
            ?? ('ADV/' . ($firstActivePost->notification_id ?? now()->year) . '/' . str_pad((string) $firstActivePost->id, 3, '0', STR_PAD_LEFT)))
        : 'ADV/2026/001';

    $advertisementNumber = $headerAdvertisementNo ?? $generatedAdvertisementNo;
    $helpdeskEmail = $helpdeskEmail ?? $firstNotification?->helpdesk_email ?? 'helpdesk.recruitment@gov.in';
    $helpdeskPhone = $helpdeskPhone ?? $firstNotification?->helpdesk_phone ?? '1800-120-2026';
@endphp

<header class="border-b-4 border-[var(--gov-maroon)] bg-[var(--gov-navy)] text-white">
    <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
        <div class="grid gap-3 md:grid-cols-3 md:items-center">
            <div class="flex h-16 w-16 items-center justify-center border border-slate-200 bg-slate-800 text-center text-xs font-bold uppercase tracking-wide text-white">
                Emblem
            </div>

            <div class="text-center md:text-center">
                <h1 class="text-sm font-bold uppercase tracking-wider text-white sm:text-base">
                    Government Recruitment Portal
                </h1>
                <p class="mt-1 text-xs text-slate-200">Department of Recruitment and Personnel</p>
                <p class="mt-1 text-xs font-semibold text-amber-300">Advertisement No: {{ $advertisementNumber }}</p>
            </div>

            <div class="space-y-2 text-xs md:text-right">
                <div class="border border-slate-600 bg-slate-800 px-2 py-1">
                    <p class="font-semibold text-amber-300">Helpdesk: {{ $helpdeskPhone }}</p>
                    <p class="text-slate-200">{{ $helpdeskEmail }}</p>
                </div>
                <div class="flex items-center justify-start gap-2 md:justify-end">
                    @guest
                        <a href="{{ route('login') }}" class="border border-slate-300 px-3 py-1 font-semibold text-white hover:bg-slate-700">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="border border-[var(--gov-maroon)] bg-[var(--gov-maroon)] px-3 py-1 font-semibold text-white hover:bg-[#5c0000]">
                            Register
                        </a>
                    @endguest
                    @auth
                        <a href="{{ route(auth()->user()->dashboardRouteName()) }}" class="border border-slate-300 px-3 py-1 font-semibold text-white hover:bg-slate-700">
                            Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="border border-[var(--gov-maroon)] px-3 py-1 font-semibold text-red-100 hover:bg-[#5c0000]">
                                Logout
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <nav x-data="{ open: false }" class="border-t border-slate-300 bg-slate-100 text-slate-900 no-print" aria-label="Primary">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-2 sm:px-6 lg:px-8">
            <button
                type="button"
                class="inline-flex items-center border border-slate-800 px-3 py-1 text-xs font-semibold text-slate-900 md:hidden"
                @click="open = !open"
                aria-label="Toggle primary navigation"
            >
                Menu
            </button>

            <ul class="hidden items-center gap-5 text-sm font-semibold text-slate-900 md:flex">
                <li><a href="{{ route('home') }}" class="hover:text-[var(--gov-maroon)]">Home</a></li>
                <li><a href="{{ route('home') }}#summary" class="hover:text-[var(--gov-maroon)]">Advertisement Summary</a></li>
                <li><a href="{{ route('home') }}#notifications" class="hover:text-[var(--gov-maroon)]">Notifications</a></li>
                <li><a href="{{ route('home') }}#instructions" class="hover:text-[var(--gov-maroon)]">Instructions</a></li>
                @auth
                    <li><a href="{{ route('application.create') }}" class="hover:text-[var(--gov-maroon)]">Apply Online</a></li>
                    <li><a href="{{ route('grievances.index') }}" class="hover:text-[var(--gov-maroon)]">Grievances</a></li>
                @endauth
            </ul>
        </div>

        <div x-show="open" class="border-t border-slate-300 px-4 py-3 md:hidden">
            <ul class="space-y-2 text-sm font-semibold text-slate-900">
                <li><a href="{{ route('home') }}" class="block">Home</a></li>
                <li><a href="{{ route('home') }}#summary" class="block">Advertisement Summary</a></li>
                <li><a href="{{ route('home') }}#notifications" class="block">Notifications</a></li>
                <li><a href="{{ route('home') }}#instructions" class="block">Instructions</a></li>
                @auth
                    <li><a href="{{ route('application.create') }}" class="block">Apply Online</a></li>
                    <li><a href="{{ route('grievances.index') }}" class="block">Grievances</a></li>
                @endauth
            </ul>
        </div>
    </nav>
</header>
