<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') | {{ config('app.name', 'Recruitment Portal') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --gov-primary: #0B3D91;
            --gov-secondary: #800000;
            --gov-border: #cbd5e1;
            --gov-success: #166534;
            --gov-danger: #991b1b;
            --gov-warning: #92400e;
        }

        @media print {
            .no-print,
            aside,
            header {
                display: none !important;
            }
        }
    </style>

    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-900 antialiased">
    <div x-data="{ open: false }" class="min-h-screen">
        <header class="sticky top-0 z-40 border-b border-[var(--gov-primary)] bg-white no-print">
            <div class="mx-auto flex max-w-[1700px] items-center justify-between px-4 py-3 sm:px-6">
                <div>
                    <h1 class="text-sm font-bold uppercase tracking-wide text-[var(--gov-primary)] sm:text-base">
                        Government Recruitment Administration
                    </h1>
                    <p class="text-[11px] text-slate-600">Official back office panel</p>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="border border-[var(--gov-primary)] px-3 py-1 text-xs font-semibold text-[var(--gov-primary)] md:hidden"
                        @click="open = !open"
                    >
                        Menu
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="border border-[var(--gov-secondary)] px-3 py-1 text-xs font-semibold text-[var(--gov-secondary)] hover:bg-rose-50">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <div class="mx-auto flex max-w-[1700px]">
            <aside
                class="fixed inset-y-0 left-0 z-30 w-72 overflow-y-auto bg-[var(--gov-primary)] p-4 text-white transition md:static md:translate-x-0 no-print"
                :class="open ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
                aria-label="Admin navigation"
            >
                <p class="mb-3 border-b border-blue-300/40 pb-2 text-xs font-semibold uppercase tracking-wide text-blue-100">
                    Modules
                </p>

                <nav class="space-y-1 text-sm">
                    <a href="{{ route('admin.dashboard') }}" class="block rounded-sm px-3 py-2 hover:bg-blue-800">Dashboard</a>

                    @can('manage_posts')
                        <a href="{{ route('admin.notifications.index') }}" class="block rounded-sm px-3 py-2 hover:bg-blue-800">Advertisement Management</a>
                        <a href="{{ route('admin.posts.index') }}" class="block rounded-sm px-3 py-2 hover:bg-blue-800">Post Management</a>
                    @endcan

                    @can('verify_dd')
                        <a href="{{ route('admin.demand-drafts.index') }}" class="block rounded-sm px-3 py-2 hover:bg-blue-800">Demand Draft Verification</a>
                    @endcan

                    @can('shortlist_candidates')
                        <a href="{{ route('admin.scrutiny.index') }}" class="block rounded-sm px-3 py-2 hover:bg-blue-800">Application Scrutiny</a>
                    @endcan

                    @can('generate_merit')
                        <a href="{{ route('admin.shortlists.index') }}" class="block rounded-sm px-3 py-2 hover:bg-blue-800">Shortlisting Engine</a>
                        <a href="{{ route('admin.merit.index') }}" class="block rounded-sm px-3 py-2 hover:bg-blue-800">Merit Generation</a>
                    @endcan

                    @can('evaluate_skill_test')
                        <a href="{{ route('admin.skill-tests.index') }}" class="block rounded-sm px-3 py-2 hover:bg-blue-800">Skill Test Marks Entry</a>
                    @endcan

                    @can('verify_documents')
                        <a href="{{ route('admin.document-verifications.index') }}" class="block rounded-sm px-3 py-2 hover:bg-blue-800">Document Verification</a>
                    @endcan

                    @can('generate_appointment')
                        <a href="{{ route('admin.appointment-orders.index') }}" class="block rounded-sm px-3 py-2 hover:bg-blue-800">Appointment Orders</a>
                    @endcan

                    @can('view_reports')
                        <a href="{{ route('admin.reports.index') }}" class="block rounded-sm px-3 py-2 hover:bg-blue-800">Reports & Analytics</a>
                    @endcan

                    @can('view_audit_logs')
                        <a href="{{ route('admin.audit-logs.index') }}" class="block rounded-sm px-3 py-2 hover:bg-blue-800">Audit Logs</a>
                    @endcan
                </nav>
            </aside>

            <main class="min-h-screen flex-1 p-4 md:p-6">
                @if (session('success') || session('error') || session('status'))
                    <div class="mb-4 space-y-2">
                        @if (session('success'))
                            <div class="border border-[var(--gov-success)] bg-white px-3 py-2 text-sm text-[var(--gov-success)]">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="border border-[var(--gov-danger)] bg-white px-3 py-2 text-sm text-[var(--gov-danger)]">
                                {{ session('error') }}
                            </div>
                        @endif
                        @if (session('status'))
                            <div class="border border-[var(--gov-primary)] bg-white px-3 py-2 text-sm text-[var(--gov-primary)]">
                                {{ session('status') }}
                            </div>
                        @endif
                    </div>
                @endif

                <div class="mb-4 border border-[var(--gov-border)] bg-white px-4 py-3">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-[var(--gov-primary)]">
                        @yield('page_title', 'Admin')
                    </h2>
                </div>

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
