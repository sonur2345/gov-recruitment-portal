<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Recruitment Portal') }} | Home</title>
    <meta name="description" content="Official recruitment portal for notifications, available posts, online applications, and candidate instructions.">
    <meta name="keywords" content="recruitment portal, government jobs, online application, vacancies, notifications">
    <meta name="robots" content="index,follow">
    <link rel="canonical" href="{{ route('home') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Manrope', ui-sans-serif, system-ui, sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    @php
        $dashboardRoute = auth()->check() ? auth()->user()->dashboardRouteName() : 'dashboard';
    @endphp
    <header class="relative overflow-hidden bg-slate-950 text-white">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(16,185,129,0.22),transparent_35%),radial-gradient(circle_at_top_left,rgba(59,130,246,0.22),transparent_45%)]"></div>

        <nav x-data="{ open: false }" class="relative z-10 border-b border-white/10">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="text-lg font-bold tracking-wide">
                    Recruitment Portal
                </a>

                <button type="button" @click="open = !open" class="inline-flex items-center rounded-md border border-white/20 p-2 text-white md:hidden" aria-label="Toggle navigation menu">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                </button>

                <div class="hidden items-center gap-8 md:flex">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-white/90 hover:text-white">Home</a>
                    <a href="#about" class="text-sm font-medium text-white/90 hover:text-white">About</a>
                    <a href="#available-posts" class="text-sm font-medium text-white/90 hover:text-white">Available Posts</a>
                    <a href="#instructions" class="text-sm font-medium text-white/90 hover:text-white">Instructions</a>

                    @auth
                        <a href="{{ route($dashboardRoute) }}" class="rounded-md bg-emerald-500 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-400">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="rounded-md border border-white/20 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">
                                Logout
                            </button>
                        </form>
                    @else
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="rounded-md border border-white/20 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Login</a>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="rounded-md bg-white px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-slate-100">Register</a>
                        @endif
                    @endauth
                </div>
            </div>

            <div x-show="open" x-transition class="border-t border-white/10 bg-slate-900/95 px-4 py-4 md:hidden">
                <div class="space-y-3">
                    <a href="{{ route('home') }}" class="block text-sm font-medium text-white/90 hover:text-white">Home</a>
                    <a href="#about" class="block text-sm font-medium text-white/90 hover:text-white">About</a>
                    <a href="#available-posts" class="block text-sm font-medium text-white/90 hover:text-white">Available Posts</a>
                    <a href="#instructions" class="block text-sm font-medium text-white/90 hover:text-white">Instructions</a>

                    @auth
                        <a href="{{ route($dashboardRoute) }}" class="block rounded-md bg-emerald-500 px-4 py-2 text-center text-sm font-semibold text-white">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full rounded-md border border-white/20 px-4 py-2 text-sm font-semibold text-white">
                                Logout
                            </button>
                        </form>
                    @else
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="block rounded-md border border-white/20 px-4 py-2 text-center text-sm font-semibold text-white">Login</a>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="block rounded-md bg-white px-4 py-2 text-center text-sm font-semibold text-slate-900">Register</a>
                        @endif
                    @endauth
                </div>
            </div>
        </nav>

        <div class="relative z-10 mx-auto max-w-7xl px-4 pb-20 pt-14 sm:px-6 lg:px-8 lg:pt-20">
            <div class="grid gap-10 lg:grid-cols-12 lg:items-center">
                <div class="lg:col-span-8">
                    <p class="mb-3 inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-emerald-300">
                        Official Recruitment System
                    </p>
                    <h1 class="text-3xl font-extrabold leading-tight sm:text-4xl lg:text-5xl">
                        Government Recruitment Portal
                    </h1>
                    <p class="mt-5 max-w-2xl text-base text-slate-200 sm:text-lg">
                        Apply online for current vacancies, track applications, and access official instructions from one secure and transparent portal.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        @auth
                            <a href="{{ route('application.create') }}" class="rounded-md bg-emerald-500 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-400">
                                Apply Now
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-md bg-emerald-500 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-400">
                                Apply Now
                            </a>
                        @endauth
                        <a href="#available-posts" class="rounded-md border border-white/20 px-6 py-3 text-sm font-semibold text-white hover:bg-white/10">
                            View Available Posts
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-4">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-300">Live Openings</p>
                        <p class="mt-3 text-4xl font-extrabold text-white">{{ $activePosts->count() }}</p>
                        <p class="mt-1 text-sm text-slate-300">Active posts currently available</p>
                        <p class="mt-6 text-xs text-slate-400">Apply before the last date shown for each post.</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section id="about" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">About Portal</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        This portal streamlines recruitment by publishing notifications, listing vacancies, and enabling secure digital applications for candidates.
                    </p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Transparent Process</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        Every stage from application submission to document verification and final selection is tracked with clear status updates.
                    </p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Secure Access</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        User authentication and role-based access ensure secure handling of candidate and administrative recruitment operations.
                    </p>
                </div>
            </div>
        </section>

        <section id="available-posts" class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Available Posts</h2>
                    <p class="mt-1 text-sm text-slate-600">Active posts with vacancy details and application deadlines.</p>
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                @forelse ($activePosts as $post)
                    @php
                        $postTitle = $post->name ?? $post->title ?? $post->post_name ?? ('Post #'.$post->id);
                        $vacancies = $post->total_vacancies ?? $post->total_posts ?? 0;
                        $lastDate = $post->notification?->end_date?->format('d M Y') ?? 'Not announced';
                    @endphp

                    <article class="flex h-full flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <h3 class="text-lg font-semibold text-slate-900">{{ $postTitle }}</h3>
                        <p class="mt-4 text-sm text-slate-600">
                            <span class="font-semibold text-slate-900">Vacancies:</span> {{ $vacancies }}
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            <span class="font-semibold text-slate-900">Last Date:</span> {{ $lastDate }}
                        </p>

                        <div class="mt-6">
                            @auth
                                <a href="{{ route('application.create.post', $post) }}" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700">
                                    Apply
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center rounded-md bg-emerald-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-400">
                                    Login to Apply
                                </a>
                            @endauth
                        </div>
                    </article>
                @empty
                    <div class="sm:col-span-2 xl:col-span-3">
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center">
                            <p class="text-base font-semibold text-slate-900">No active posts available right now.</p>
                            <p class="mt-2 text-sm text-slate-600">Please check back later for new recruitment notifications.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>

        <section id="instructions" class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <h2 class="text-2xl font-bold text-slate-900">Important Instructions</h2>
                <ul class="mt-5 space-y-3 text-sm leading-6 text-slate-700">
                    <li>Read eligibility criteria and post-specific conditions before applying.</li>
                    <li>Keep your photo, signature, and supporting documents ready in required formats.</li>
                    <li>Fill accurate personal and educational information; incorrect data may lead to rejection.</li>
                    <li>Submit the application before the last date and save acknowledgement for reference.</li>
                    <li>Use a valid email and mobile number for recruitment updates and alerts.</li>
                </ul>
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 py-10 text-sm text-slate-600 sm:grid-cols-2 sm:px-6 lg:px-8">
            <div>
                <h3 class="text-sm font-semibold uppercase tracking-[0.12em] text-slate-900">Contact</h3>
                <p class="mt-3">Recruitment Cell, Administrative Building</p>
                <p>Raipur, Chhattisgarh, India</p>
                <p class="mt-2">Phone: +91-771-123-4567</p>
            </div>
            <div class="sm:text-right">
                <h3 class="text-sm font-semibold uppercase tracking-[0.12em] text-slate-900">Email</h3>
                <p class="mt-3">support@recruitmentportal.gov.in</p>
                <p class="mt-6 text-xs text-slate-500">
                    &copy; {{ date('Y') }} Recruitment Portal. All rights reserved.
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
