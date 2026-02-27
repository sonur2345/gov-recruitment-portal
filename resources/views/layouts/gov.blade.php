<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GOVERNMENT RECRUITMENT PORTAL')</title>
    <meta name="description" content="@yield('meta_description', 'Official Government Recruitment Portal')">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --gov-navy: #0B3D91;
            --gov-maroon: #800000;
            --gov-bg: #f8fafc;
            --gov-border: #cbd5e1;
            --gov-success: #166534;
            --gov-danger: #991b1b;
            --gov-warning: #92400e;
            --gov-primary: var(--gov-navy);
            --gov-secondary: var(--gov-maroon);
            --gov-accent: #f1f5f9;
        }

        body {
            font-family: "Noto Serif", "Times New Roman", Georgia, serif;
            background: #eef2f7;
            color: #0f172a;
        }

        .gov-watermark {
            position: relative;
            overflow: hidden;
        }

        .gov-watermark::before {
            content: "OFFICIAL";
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(3rem, 12vw, 9rem);
            font-weight: 700;
            letter-spacing: 0.2em;
            color: rgba(11, 61, 145, 0.04);
            pointer-events: none;
            user-select: none;
            z-index: 0;
            transform: rotate(-18deg);
        }

        .gov-watermark > * {
            position: relative;
            z-index: 1;
        }

        /* Fallbacks for Tailwind arbitrary value classes on older builds */
        .bg-\[var\(--gov-navy\)\] { background-color: var(--gov-navy) !important; }
        .bg-\[var\(--gov-maroon\)\] { background-color: var(--gov-maroon) !important; }
        .bg-\[var\(--gov-primary\)\] { background-color: var(--gov-primary) !important; }
        .bg-\[var\(--gov-secondary\)\] { background-color: var(--gov-secondary) !important; }

        .text-\[var\(--gov-navy\)\] { color: var(--gov-navy) !important; }
        .text-\[var\(--gov-maroon\)\] { color: var(--gov-maroon) !important; }
        .text-\[var\(--gov-primary\)\] { color: var(--gov-primary) !important; }
        .text-\[var\(--gov-secondary\)\] { color: var(--gov-secondary) !important; }
        .text-\[var\(--gov-success\)\] { color: var(--gov-success) !important; }
        .text-\[var\(--gov-danger\)\] { color: var(--gov-danger) !important; }
        .text-\[var\(--gov-warning\)\] { color: var(--gov-warning) !important; }

        .border-\[var\(--gov-navy\)\] { border-color: var(--gov-navy) !important; }
        .border-\[var\(--gov-maroon\)\] { border-color: var(--gov-maroon) !important; }
        .border-\[var\(--gov-primary\)\] { border-color: var(--gov-primary) !important; }
        .border-\[var\(--gov-secondary\)\] { border-color: var(--gov-secondary) !important; }
        .border-\[var\(--gov-border\)\] { border-color: var(--gov-border) !important; }
        .border-\[var\(--gov-success\)\] { border-color: var(--gov-success) !important; }
        .border-\[var\(--gov-danger\)\] { border-color: var(--gov-danger) !important; }

        .focus\:border-\[var\(--gov-navy\)\]:focus { border-color: var(--gov-navy) !important; }
        .focus\:border-\[var\(--gov-primary\)\]:focus { border-color: var(--gov-primary) !important; }
        .focus\:ring-\[var\(--gov-navy\)\]:focus { --tw-ring-color: var(--gov-navy) !important; }
        .focus\:ring-\[var\(--gov-primary\)\]:focus { --tw-ring-color: var(--gov-primary) !important; }

        .hover\:text-\[var\(--gov-maroon\)\]:hover { color: var(--gov-maroon) !important; }
        .hover\:text-\[var\(--gov-secondary\)\]:hover { color: var(--gov-secondary) !important; }
        .hover\:bg-\[\#651924\]:hover { background-color: #651924 !important; }
        .hover\:bg-\[\#0d243f\]:hover { background-color: #0d243f !important; }

        .text-\[10px\] { font-size: 10px; line-height: 1rem; }
        .tracking-\[0\.12em\] { letter-spacing: 0.12em; }
        @media (min-width: 768px) {
            .md\:grid-cols-\[auto_1fr_auto\] { grid-template-columns: auto 1fr auto; }
        }

        @media print {
            .no-print,
            header,
            nav,
            footer {
                display: none !important;
            }

            body {
                background: #fff !important;
                color: #000 !important;
            }

            .print-area,
            table,
            th,
            td {
                border-color: #000 !important;
            }
        }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen antialiased">
    <div class="flex min-h-screen flex-col">
        @include('partials.header')

        <main class="print-area gov-watermark mx-auto w-full max-w-7xl flex-1 px-4 py-6 sm:px-6 lg:px-8">
            @if (session('success') || session('error') || session('status'))
                <div class="mb-4 space-y-2">
                    @if (session('success'))
                        <div class="border border-[var(--gov-success)] bg-green-50 px-3 py-2 text-sm text-[var(--gov-success)]" role="status" aria-live="polite">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="border border-[var(--gov-danger)] bg-red-50 px-3 py-2 text-sm text-[var(--gov-danger)]" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if (session('status'))
                        <div class="border border-[var(--gov-navy)] bg-blue-50 px-3 py-2 text-sm text-[var(--gov-navy)]" role="status" aria-live="polite">
                            {{ session('status') }}
                        </div>
                    @endif
                </div>
            @endif

            @yield('content')
        </main>

        @include('partials.footer')
    </div>

    @stack('scripts')
</body>
</html>
