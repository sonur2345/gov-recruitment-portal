<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') | {{ config('app.name', 'Recruitment Portal') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            --gov-navy: #0B3D91;
            --gov-navy-dark: #092f6f;
            --gov-primary: #0B3D91;
            --gov-secondary: #800000;
            --gov-border: #cbd5f5;
        }

        [x-cloak] { display: none !important; }

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
    <div x-data="{ sidebarOpen: false }" class="min-h-screen">
        <x-admin-topbar />

        <div class="flex">
            <div
                x-show="sidebarOpen"
                x-cloak
                class="fixed inset-0 z-30 bg-slate-900/40 md:hidden"
                @click="sidebarOpen = false"
            ></div>

            <x-admin-sidebar />

            <main class="min-h-screen flex-1 p-3 sm:p-4">
                <x-admin-flash />

                <div class="mb-3 border border-[var(--gov-border)] bg-white px-3 py-2">
                    <h2 class="text-[11px] font-semibold uppercase tracking-wide text-[var(--gov-navy)]">
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
